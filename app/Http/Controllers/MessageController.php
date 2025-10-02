<?php

namespace App\Http\Controllers;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Ad;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use League\Uri\IPv4\Converter;

class MessageController extends Controller
{
    public $stylePrompts = [
        'funny',
        'romantic',
        'casual',
        'formal',
        'literature',
        'adventurous',
        'mysterious',
        'rom-com',
        'philosophical',
        'trendy',
        'storytelling',
        'cinematic',
    ];

    public function show(): View
    {
        $user_id = Auth::id();

        // Step 1: get latest message IDs (same as before)
        $latestMessageIds = Message::selectRaw('MAX(id) as id')
            ->where('sender_id', '!=', $user_id)
            ->groupBy('sender_id')
            ->pluck('id');

        // Step 2: fetch messages + conversation, filtered by author_id
        $messages = Message::whereIn('id', $latestMessageIds)
            ->whereHas('conversation', function ($q) use ($user_id) {
                $q->where('author_id', $user_id);
            })
            ->with('conversation') // eager load
            ->orderByDesc('id')
            ->get();

        return view('message.show', [
            'messages' => $messages,
            'prompts' => $this->stylePrompts
        ]);
    }

    public function sent(): View
    {
        $userId = Auth::id();

        $sent = Message::with([
                'conversation.ad',
                'conversation.author',
                'conversation.replier'
            ])
            ->where('sender_id', $userId)
            ->whereHas('conversation', function ($q) use ($userId) {
                $q->where('replier_id', $userId); // only when I am the replier
            })
            ->orderBy('created_at', 'desc')
            ->get()
            // ✅ Keep only the latest message per ad
            ->unique(fn($msg) => $msg->conversation->ad_id);

        return view('message.sent', [
            'messages' => $sent,
            'prompts' => $this->stylePrompts
        ]);
    }

    public function conversationMessages($replier_id)
    {
        $userId = Auth::id();

        // 1) make sure conversation exists and user is a participant
        $conversation = DB::table('conversations')->where('replier_id', $replier_id)->first();

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

        $this->update_conversation_progress($message->conversation_id);

        $conversation = Conversation::with('author')->find($message->conversation_id);

        if ($conversation && $conversation->author && $conversation->author->email) {
            $this->send_email([
                'email'   => $conversation->author->email,
                'name'    => $conversation->author->name,
                'content' => $request->content,
            ]);
        }

        // return as JSON so frontend can append it
        return response()->json([
            'id' => $message->id,
            'sender_name' => $message->sender->name,
            'content' => $message->content,
            'created_at' => $message->created_at,
        ]);
    }

    public function update_conversation_progress($conversation_id)
    {
        $conversation = Conversation::find($conversation_id);

        if (!$conversation) return;

        $messages = Message::where('conversation_id', $conversation_id)->get();

        $progress = '0%';

        if ($messages->count() > 0) {
            $progress = '25%';
        }

        $senders = $messages->pluck('sender_id')->unique();
        if ($senders->count() >= 2) {
            $progress = '50%';
        }

        if ($messages->count() >= 5 || $conversation->unlocked_photo) {
            $progress = '75%';
        }

        // Optional: you can define your own “completed” logic
        if ($conversation->confirmed) {
            $progress = '100%';
        }

        $conversation->progress = $progress;
        $conversation->save();
    }

    public function send_email($data = [])
    {
        $email = $data['email'];
        if ($data['email'] != null) {
            Mail::send('mail.reply', [
                'name' => $data['name'],
                'content' => $data['content']
            ], function ($content) use ($email) {
                $content->to($email)
                        ->subject('You just got a reply!');
            });
        }
    }


    public function sent_messages($receiverId)
    {
        $userId = Auth::id();

        $messages = Message::with('conversation.replier')
            ->where('conversation_id', $receiverId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }


}
