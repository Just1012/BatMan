<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;
use App\Models\Order;
use App\Models\messages;

class ChatController extends Controller
{
    public function getConversation($id)
    {
        // Retrieve the conversations where the user is either the customer or the engineer
        $conversations = Conversation::query()
            ->where(function ($query) {
                $query->where('customer', Auth()->user()->id)
                    ->orWhere('engineer', Auth()->user()->id);
            })
            ->where('order_id', $id)
            ->first();

        // Initialize an array to hold messages
        $allMessages = [];

        // Retrieve messages for each conversation
        // foreach ($conversations as $conversation) {
        $messages = messages::with('conversation')
            ->where('conversation_id', '=', $conversations->id)
            ->get();

        // Append messages to the allMessages array
        foreach ($messages as $message) {
          $message['is_seen'] = 1;
            $allMessages[] = $message;
        }
        // }

        // Retrieve the order and its related services
        $order = Order::with('services')->find($id);

        // Return the data as a JSON response
        return response()->json([
            'conversations' => $conversations,
            'messages' => $allMessages,
            'order' => $order
        ], 200);
    }


    public function sendMessage(Request $request)
    {

        $conversations = Conversation::where('order_id', $request->order_id)
            ->first();



        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        messages::create([
            'user_id' => Auth::id(),
            'conversation_id' => $conversations->id,
            'body' => $request->body,
            'is_seen' => 0,
        ]);

        return response("Message sent Successfully", 200);
    }
}
