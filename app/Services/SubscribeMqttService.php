<?php

namespace App\Services;

use App\Services\VendDataService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use PhpMqtt\Client\Facades\MQTT;

class SubscribeMqttService
{
  const SUBSCRIBED_TOPIC = '#';

  protected $vendDataService;

  public function __construct()
  {
    $this->vendDataService = new VendDataService();
  }

  public function subscribe()
  {
    $mqtt = MQTT::connection();
    $mqtt->subscribe(self::SUBSCRIBED_TOPIC, function (string $topic, string $message) {
        $this->vendDataService->store($topic, $message);
    }, 1);
    $mqtt->loop(true);
  }
}