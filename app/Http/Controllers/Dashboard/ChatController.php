<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Order;
use App\Models\messages;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index($id)
    {
        $conversations = Conversation::query()
            ->where(function ($query) {
                $query->where('customer', Auth()->user()->id)
                    ->orWhere('engineer', Auth()->user()->id);
            })
            ->where('order_id', $id)
            ->get();


        foreach ($conversations as $conversation) {
            $messages = messages::with('conversation.order.services')->where('conversation_id', '=', $conversation->id)->get();
        }
        $order = Order::with('services')->find($id);

        return view('dashboard.chat', compact('messages','order','conversations'));
    }

    public function sendMessage(Request $request){
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body' => 'required|string|max:1000',
        ]);
    
        messages::create([
            'user_id' => Auth::id(),
            'conversation_id' => $request->conversation_id,
            'body' => $request->body,
            'is_seen' => 0,
        ]);
    
        return back()->with('success', 'Message sent successfully!');
    }
}
