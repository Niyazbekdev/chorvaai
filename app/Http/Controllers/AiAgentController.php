<?php

namespace App\Http\Controllers;

use App\Services\AnthropicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AiAgentController extends Controller
{
    public function index(): View
    {
        return view('ai-agent.index');
    }

    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'messages'          => ['required', 'array', 'min:1', 'max:30'],
            'messages.*.role'   => ['required', 'in:user,assistant'],
            'messages.*.content'=> ['required', 'string', 'max:1000'],
        ]);

        try {
            $reply = app(AnthropicService::class)->chat($request->messages);
            return response()->json(['reply' => $reply]);
        } catch (\Throwable $e) {
            Log::error('AiAgent chat error: ' . $e->getMessage());
            return response()->json(
                ['reply' => 'Kechirasiz, AI xizmati hozir vaqtincha ishlamaydi. Iltimos, keyinroq urinib ko\'ring.'],
                503,
            );
        }
    }
}
