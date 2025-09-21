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

        $messages = Message::whereHas('conversation', function ($q) use ($userId) {
            $q->where('author_id', $userId); // only ads owned by me
        })
            ->where('sender_id', '!=', $userId) // exclude my own sent messages
            ->with(['sender', 'conversation.replier'])
            ->select('messages.*')
            ->whereIn('id', function ($sub) use ($userId) {
                $sub->selectRaw('MAX(id)')
                    ->from('messages')
                    ->where('sender_id', '!=', $userId)
                    ->groupBy('sender_id', 'conversation_id');
            })
            ->latest()
            ->get();

        return view('message.show', compact('messages'));
    }

    public function sent(): View
    {
        $user_id = Auth::id();

        $messages = Message::where('sender_id', $user_id)
            ->with([
                'conversation.author',   // ✅ load ad creator
                'conversation.replier',  // ✅ load replier
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('message.sent', ['messages' => $messages]);
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
}
