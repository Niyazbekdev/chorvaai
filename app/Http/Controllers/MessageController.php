<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Conversation $conversation, Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless(
            in_array($user->id, [$conversation->buyer_id, $conversation->seller_id]),
            403
        );

        $request->validate(['message' => 'required|string|max:2000']);

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'message'   => $request->message,
        ]);

        $conversation->update(['last_message_at' => now()]);

        $message->load('sender');
        broadcast(new MessageSent($message));

        return redirect()->route('conversations.show', $conversation)
            ->withFragment('bottom');
    }
}
