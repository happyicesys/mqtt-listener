<?php

namespace App\Services;
use App\Jobs\StoreVendData;
use App\Models\VendData;
use Carbon\Carbon;
use PhpMqtt\Client\Facades\MQTT;

class VendDataService
{

    public function store($topic, $input)
    {
        $standardizedVendData = $this->standardizedVendData($input);
        $decodedData = $this->decodeVendData($standardizedVendData);
        $this->processVendData($topic, $standardizedVendData, $decodedData);
    }

  public function standardizedVendData($input)
  {
    $input = collect($input);

    if(strpos($input, '&') !== false) {
      $input = $input->first();
      foreach(explode('&', $input) as $processInput) {
        list($a, $b) = explode('=', $processInput);
        $finalInput[$a] = $b;
      }
      $finalInput = collect($finalInput);
    }else {
      $finalInput = $input;
    }
    return $finalInput;
  }

  public function decodeVendData($input) {
    $data = [];
    $processedDataArr = [];

    if(isset($input['f']) and isset($input['g']) and isset($input['m']) and isset($input['p']) and isset($input['t'])) {
        foreach($input as $dataIndex => $data) {
            switch($dataIndex) {
                case 'f':
                    break;
                case 't':
                    break;
                case 'm':
                    $processedDataArr['code'] = $data;
                    break;
                case 'g':
                    break;
                case 'p':
                    if(isset($data)) {
                      if(strpos($data, ' ')) {
                          $data = str_replace(' ', '+', $data);
                      }
                      if(substr($data, -1) == '!') {
                          $data = base64_decode(substr_replace($data,"=",-1));
                      }else {
                          $data = base64_decode($data);
                      }
                      $processedDataArr['content'] = $data;
                    }
                    break;
                default:
            }
        }

        if(str_starts_with($processedDataArr['content'], "{\"") or empty($input['p'])) {
          $processedDataArr['data'] = json_decode($processedDataArr['content'], true);
        }else {
          $processedDataArr['data']['Vid'] = json_decode($processedDataArr['code'], true);
          $processedDataArr['data']['Type'] = 'CHANNEL';
          $processedDataArr['data']['channels'] = [];
          $byteData = unpack('C*', $processedDataArr['content']);

          if(!empty($byteData)) {
            switch($byteData[1]) {
              case 65:
                $processedDataArr['data']['label'] = 'A';
                break;
              case 66:
                $processedDataArr['data']['label'] = 'B';
                break;
              case 67:
                $processedDataArr['data']['label'] = 'C';
                break;
              case 83:
                $processedDataArr['data']['label'] = 'S';
                break;
              default:
                $processedDataArr['data']['label'] = 'error';
            }
          }

          // if(!empty($byteData) && $byteData[1] == 83) {
          if(!empty($byteData)) {
            $byteSize = (sizeof($byteData) - 5)/ 11;
            if($byteSize == 60) {
              // INT16U id;
              // INT8U Col_FaultCode;
              // INT8U Col_Capacity;
              // INT8U Col_GoodsCount;
              // INT32U Col_Price;
              // INT16U Col_ProductId;
              $i = 2;
              $i += 4;
              for($j = 0; $j < $byteSize; $j++) {
                $channelArr = [];
                $channelCode = $byteData[$i++];
                $channelCode += $byteData[$i++]*0x100;
                $channelArr['channel_code'] = $channelCode;

                $channelArr['error_code'] = $byteData[$i++];
                $channelArr['capacity'] = $byteData[$i++];
                $channelArr['qty'] = $byteData[$i++];

                $amount = $byteData[$i++];
                $amount += $byteData[$i++]*0x100;
                $amount += $byteData[$i++]*0x10000;
                $amount += $byteData[$i++]*0x1000000;
                $channelArr['amount'] = $amount;
                $i += 2;
                if(is_array($channelArr)) {
                  array_push($processedDataArr['data']['channels'], $channelArr);
                }
              }
            }else {
              // INT16U id;
              // INT8U Col_FaultCode;
              // INT8U Col_Capacity;
              // INT8U Col_GoodsCount;
              // INT32U Col_Price;
              // INT16U Col_ProductId;
              // INT16U discount_grp;
              // INT32U Col_Price2;
              // INT16U lock_cnt;
              $byteSize = (sizeof($byteData) - 5)/ 19;
              $i = 2;
              if($processedDataArr['data']['label'] === 'S') {
                $i += 4;
              }else {
                $i += 2;
              }
              for($j = 0; $j < $byteSize; $j++) {
                $channelArr = [];
                $channelCode = $byteData[$i++];
                $channelCode += $byteData[$i++]*0x100;
                $channelArr['channel_code'] = $channelCode;
                $channelArr['error_code'] = $byteData[$i++];
                $channelArr['capacity'] = $byteData[$i++];
                $channelArr['qty'] = $byteData[$i++];
                $amount = $byteData[$i++];
                $amount += $byteData[$i++]*0x100;
                $amount += $byteData[$i++]*0x10000;
                $amount += $byteData[$i++]*0x1000000;
                $channelArr['amount'] = $amount;
                $productId = $byteData[$i++];
                $productId += $byteData[$i++]*0x100;
                $channelArr['product_id'] = $productId;
                $discountGroup = $byteData[$i++];
                $discountGroup += $byteData[$i++]*0x100;
                $channelArr['discount_group'] = $discountGroup;
                $amount2 = $byteData[$i++];
                $amount2 += $byteData[$i++]*0x100;
                $amount2 += $byteData[$i++]*0x10000;
                $amount2 += $byteData[$i++]*0x1000000;
                $channelArr['amount2'] = $amount2;
                $lockQty = $byteData[$i++];
                $lockQty += $byteData[$i++]*0x100;
                $channelArr['lock_qty'] = $lockQty;
                if(is_array($channelArr)) {
                  array_push($processedDataArr['data']['channels'], $channelArr);
                }
              }
            }
          }
        }
      $data = $processedDataArr['data'];
    }else {
      $data = $input;
    }
    return $data;
  }

  public function processVendData($topic, $originalInput, $processedInput)
  {
    $saveVendData = true;

    if(isset($originalInput['m'])) {
        $vendCode = $originalInput['m'];

        if(isset($processedInput['Type'])) {
            switch($processedInput['Type']) {
            case 'P':
            case 'TEMPERATURECONTROL':
                $saveVendData = false;
                break;
            default:
                $saveVendData = true;
            }

            if($saveVendData) {
                StoreVendData::dispatch($vendCode, $topic, $processedInput);
            }
        }

    }
  }
}