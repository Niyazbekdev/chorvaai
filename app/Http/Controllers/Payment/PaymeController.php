<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\PaymeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymeController extends Controller
{
    public function __construct(private PaymeService $paymeService) {}

    public function handle(Request $request): JsonResponse
    {
        if (!$this->paymeService->verifyAuth($request)) {
            return response()->json(
                $this->paymeService->handle(['id' => null, 'method' => '']),
                401
            );
        }

        $rpc    = $request->json()->all();
        $result = $this->paymeService->handle($rpc);

        return response()->json($result);
    }
}
