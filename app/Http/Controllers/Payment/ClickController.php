<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\ClickService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClickController extends Controller
{
    public function __construct(private ClickService $clickService) {}

    public function prepare(Request $request): JsonResponse
    {
        return response()->json(
            $this->clickService->prepare($request->all())
        );
    }

    public function complete(Request $request): JsonResponse
    {
        return response()->json(
            $this->clickService->complete($request->all())
        );
    }
}
