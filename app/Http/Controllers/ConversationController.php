<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $conversations = Conversation::with([
                'product',
                'buyer',
                'seller',
                'messages' => fn ($q) => $q->latest(),
            ])
            ->where('buyer_id', $userId)
            ->orWhere('seller_id', $userId)
            ->orderByDesc('last_message_at')
            ->get();

        return view('conversations.index', [
            'conversations' => $conversations,
            'userId'        => $userId,
        ]);
    }

    public function show(Conversation $conversation, Request $request): View
    {
        $user = $request->user();

        abort_unless(
            in_array($user->id, [$conversation->buyer_id, $conversation->seller_id]),
            403
        );

        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $conversation->messages()->with('sender')->oldest()->get();
        $conversation->load(['product', 'buyer', 'seller']);

        return view('conversations.show', compact('conversation', 'messages', 'user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'message'    => 'required|string|max:2000',
        ]);

        $product = Product::findOrFail($request->product_id);
        $buyer   = $request->user();

        abort_if($buyer->id === $product->user_id, 403);

        $conversation = Conversation::firstOrCreate(
            ['product_id' => $product->id, 'buyer_id' => $buyer->id],
            ['seller_id' => $product->user_id, 'last_message_at' => now()]
        );

        $message = $conversation->messages()->create([
            'sender_id' => $buyer->id,
            'message'   => $request->message,
        ]);

        $conversation->update(['last_message_at' => now()]);

        $message->load('sender');
        broadcast(new MessageSent($message));

        return redirect()->route('conversations.show', $conversation);
    }
}
