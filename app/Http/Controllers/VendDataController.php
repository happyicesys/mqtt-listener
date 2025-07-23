<?php

namespace App\Http\Controllers;

use App\Services\VendDataService;
use Illuminate\Http\Request;

class VendDataController extends Controller
{
    protected $vendDataService;

    public function __construct()
    {
        $this->vendDataService = new VendDataService();
    }

    /**
     * Store vend data.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->vendDataService->store(
            null,
            $request->all(),
            'http',
            $request->ip()
        );

        return response()->json(['status' => 'success'], 200);
    }
}
