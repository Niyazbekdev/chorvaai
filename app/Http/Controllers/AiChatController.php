<?php

namespace App\Http\Controllers;

use App\Models\AiChatSession;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    public function page()
    {
        return view('ai-assistant.index');
    }

    public function newChat(Request $request): JsonResponse
    {
        $request->session()->forget('ai_chat_session_id');
        return response()->json(['ok' => true]);
    }

    public function sendWithFile(Request $request, GeminiService $gemini): JsonResponse
    {
        $request->validate([
            'message' => 'nullable|string|max:2000',
            'file'    => 'required|file|max:10240|mimes:jpeg,jpg,png,gif,webp,pdf',
        ]);

        $session = $this->getOrCreateSession($request);

        $history = $session->messages()
            ->orderBy('created_at')
            ->get(['role', 'content'])
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            ->toArray();

        $file     = $request->file('file');
        $mimeType = $file->getMimeType();
        $fileData = base64_encode(file_get_contents($file->getRealPath()));
        $userText = $request->input('message', '') ?: 'Bu fayl haqida nima deyishingiz mumkin?';

        $session->messages()->create([
            'role'    => 'user',
            'content' => $userText . ' [Fayl: ' . $file->getClientOriginalName() . ']',
        ]);

        $reply = $gemini->chatWithFile($history, $userText, $fileData, $mimeType);

        $session->messages()->create([
            'role'    => 'model',
            'content' => $reply,
        ]);

        $session->touch();

        return response()->json(['reply' => $reply]);
    }

    public function send(Request $request, GeminiService $gemini): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $session = $this->getOrCreateSession($request);

        $history = $session->messages()
            ->orderBy('created_at')
            ->get(['role', 'content'])
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            ->toArray();

        $session->messages()->create([
            'role'    => 'user',
            'content' => $request->message,
        ]);

        $reply = $gemini->chat($history, $request->message);

        $session->messages()->create([
            'role'    => 'model',
            'content' => $reply,
        ]);

        $session->touch();

        return response()->json(['reply' => $reply]);
    }

    public function history(Request $request): JsonResponse
    {
        $sessionId = $request->session()->get('ai_chat_session_id');

        if (! $sessionId) {
            return response()->json(['messages' => []]);
        }

        $session = AiChatSession::find($sessionId);

        if (! $session) {
            return response()->json(['messages' => []]);
        }

        $messages = $session->messages()
            ->orderBy('created_at')
            ->get(['role', 'content', 'created_at']);

        return response()->json(['messages' => $messages]);
    }

    private function getOrCreateSession(Request $request): AiChatSession
    {
        $sessionId = $request->session()->get('ai_chat_session_id');

        if ($sessionId) {
            $session = AiChatSession::find($sessionId);
            if ($session) {
                return $session;
            }
        }

        $session = AiChatSession::create([
            'user_id' => $request->user()?->id,
        ]);

        $request->session()->put('ai_chat_session_id', $session->id);

        return $session;
    }
}
