<?php

namespace App\Http\Controllers;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Ad;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function show(): View
    {
        $userId = Auth::id();

        $messages = Message::query()
        ->where('sender_id', '!=', $userId) // only messages sent to me
        ->whereIn('id', function ($sub) use ($userId) {
            $sub->selectRaw('MAX(id)')
                ->from('messages')
                ->where('sender_id', '!=', $userId) // only last received messages
                ->groupBy('sender_id', 'conversation_id');
        })
        ->latest()
        ->with(['sender', 'conversation.replier']) // just eager load
        ->get();


        return view('message.show', compact('messages'));
    }

    public function sent(): View
    {
        $userId = Auth::id();  
        
        $sub = DB::table('messages as m1')
            ->select('m1.conversation_id', DB::raw('MAX(m1.id) as last_message_id'))
            ->where('m1.sender_id', $userId)   // 👈 berarti pesan keluar
            ->groupBy('m1.conversation_id');

        $sent = DB::table('messages as m')
            ->joinSub($sub, 'last', function ($join) {
                $join->on('m.id', '=', 'last.last_message_id');
            })
            ->join('conversations as c', 'c.id', '=', 'm.conversation_id')
            ->join('ads as a', 'a.id', '=', 'c.ad_id')
            ->join('users as adOwner', 'adOwner.id', '=', 'a.user_id')
            ->join('users as replier', 'replier.id', '=', 'c.replier_id')
            ->select(
                'm.id as message_id',
                'm.content',
                'm.is_read',
                'm.created_at',
                'c.id as conversation_id',
                'a.slug as ad_slug',
                'a.description as ad_description',
                'adOwner.name as ad_owner_name',
                'replier.name as replier_name'
            )
            ->orderBy('m.created_at', 'desc')
            ->get();

        return view('message.sent', ['messages' => $sent]);
    }

    public function conversationMessages($replier_id)
    {
        $userId = Auth::id();

        // 1) make sure conversation exists and user is a participant
        $conversation = DB::table('conversations')->where('replier_id', $replier_id)->first();

        // if (! $conversation || ! in_array($userId, [(int)$conversation->author_id, (int)$conversation->replier_id])) {
        //     return response()->json(['message' => 'Forbidden'], 403);
        // }

        // 2) mark as read all messages from the other person in this conversation
        DB::table('messages')
            ->where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', 0)
            ->update(['is_read' => 1, 'read_at' => now()]);

        // 3) fetch messages with the sender name
        $messages = DB::table('messages as m')
            ->join('users as u', 'm.sender_id', '=', 'u.id')
            ->where('m.conversation_id', $conversation->id)
            ->select(
                'm.id',
                'm.conversation_id',
                'm.sender_id',
                'u.name as sender_name',
                'm.content',
                'm.is_read',
                'm.read_at',
                'm.created_at'
            )
            ->orderBy('m.created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function show_by_sender($sender_id)
    {
        $messages = DB::table('messages')
            ->join('users', 'messages.sender_id', '=', 'users.id')
            ->where('messages.sender_id', $sender_id)
            ->where('messages.receiver_id', Auth::id())
            ->select('messages.*', 'users.name as sender_name')
            ->orderBy('messages.created_at', 'asc')
            ->get();

        // Mark them as read
        DB::table('messages')
            ->where('sender_id', $sender_id)
            ->where('receiver_id', Auth::id())
            ->update(['is_read' => 1]);

        return response()->json($messages);
    }

    public function store(Request $request, $conversationId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => Auth::id(),
            'content' => $request->content,
            'is_read' => false,
        ]);

        // return as JSON so frontend can append it
        return response()->json([
            'id' => $message->id,
            'sender_name' => $message->sender->name,
            'content' => $message->content,
            'created_at' => $message->created_at,
        ]);
    }

    public function sent_messages($receiverId)
    {
        $userId = Auth::id();

        $messages = Message::with('conversation.replier')
            ->where('conversation_id', $receiverId)
            ->where('sender_id', $userId)
            ->get();


        return response()->json($messages);
    }


}
