<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\Ad;
use OpenAI\Laravel\Facades\OpenAI;

class Ads extends Controller
{
    public function reply($box): View
    {
        return view('ads.reply', [
            'box' => $box
        ]);
    }

    public function ad_edit($id): View
    {
        $ad = Ad::where('id', $id)->first();

        return view('profile.ads.edit', ['ad' => $ad]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'id'          => 'required|integer',
        ]);

        $id = $validated['id'];
        $description = $validated['description'];

        $update = Ad::where('id', $id)->update([
            'description' => $description
        ]);

        if ($update) {
            return redirect()->back()->with('success', 'Your ad has been updated!');
        } else {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);

        // optional: check ownership
        if ($ad->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $ad->delete();

        return redirect()->back()->with('success', 'Ad deleted successfully');
    }

    public function apply_style(Request $request)
    {
        $text = $request->input('text');
        $style = $request->input('style'); // e.g. "funny"

        $stylePrompts = [
            'funny' => 'Rewrite the text with humor, witty jokes, and light sarcasm.',
            'romantic' => 'Rewrite the text in a sweet, affectionate, and heartwarming way.',
            'casual' => 'Rewrite the text in a relaxed, everyday tone, like chatting with a friend.',
            'formal' => 'Rewrite the text in a professional, polite, and respectful style.',
            'literature' => 'Rewrite the text in a poetic, elegant, and descriptive way, like classic literature.',
            'adventurous' => 'Rewrite with excitement, boldness, and energy. Emphasize discovery and thrill.',
            'mysterious' => 'Rewrite in a cryptic, intriguing, and slightly dramatic style.',
            'rom-com' => 'Rewrite like a lighthearted romantic comedy scene, playful and charming.',
            'philosophical' => 'Rewrite with deep reflections, thoughtful insights, and metaphorical language.',
            'trendy' => 'Rewrite in a modern, stylish, pop-culture-influenced tone.',
            'storytelling' => 'Rewrite as if narrating a short story about the text.',
            'cinematic' => 'Rewrite with vivid, movie-like descriptions, dramatic imagery, and action.',
        ];
    
        $instruction = ($stylePrompts[$style] ?? "Rewrite the text in a clear, natural style.") . " Keep the rewritten version under 255 characters.";
    
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a text stylist. Always rewrite text in the given tone, and never exceed 255 characters.'],
                ['role' => 'user', 'content' => $instruction . "\n\nOriginal text (max 300 chars): " . $text],
            ],
        ]);
    
        $styledText = $response['choices'][0]['message']['content'];

        if (mb_strlen($styledText) > 255) {
            $styledText = mb_substr($styledText, 0, 252) . '...';
        }

        return response()->json([
            'original' => $text,
            'style' => $style,
            'styled_text' => $styledText
        ]);
    }


    public function filter_location(Request $request)
    {
        if (is_null($request->location)) {
            session()->forget('selected_location');
        } else {
            $request->validate([
                'location' => 'required|string|max:100',
            ]);
            session(['selected_location' => $request->location]);
        }
    
        return response()->json(['success' => true]);
    }

    public function reply_first($box): View
    {
        $ad = DB::table('ads')->where('box_number', $box)->first();
        $stylePrompts = [
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
        if (!$ad) {
            abort(404);
        }
        return view('ads.reply_first', [
            'ad' => $ad,
            'prompts' => $stylePrompts,
        ]);
    }

    public function reply_second($box): View
    {
        $ad = DB::table('ads')->where('box_number', $box)->first();
        $stylePrompts = [
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
        if (!$ad) {
            abort(404);
        }
        return view('ads.reply_second', [
            'ad' => $ad,
            'prompts' => $stylePrompts,
        ]);
    }

    public function confirmation($box): View
    {
        $ad = DB::table('ads')->where('box_number', $box)->first();

        return view('ads.create_confirmation', [
            'ad' => $ad
        ]);
    }

    public function reply_confirmation(): View
    {
        return view('ads.reply_confirmation');
    }

    public function create_ad(): View
    {
        return view('ads.create');
    }

    public function create_first(): View
    {
        $stylePrompts = [
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
        return view('ads.create_first', ['prompts' => $stylePrompts]);
    }

    public function create_second(): View
    {
        return view('ads.create_second');
    }

    public function writing($box): View
    {
        return view('ads.writing', [
            'redirectUrl' => route('home'), // or any route you want
            'delay' => 5000,
            'box' => $box
        ]);
    }

    public function reply_store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $adId = $request->input('ad_id');

        if (session()->has('profile')) {
            session([
                'reply' => [
                    'ad_id'   => $adId,
                    'content' => $validated['content'],
                ],
            ]);

            return redirect()->route('offer'); // or login
        }

        // Logged in user
        $userId = Auth::id();
        $ad     = DB::table('ads')->where('id', $adId)->first();
        $author = DB::table('users')->where('id', $ad->user_id)->first();
        $replier = DB::table('users')->where('id', $userId)->first();

        if (!$ad) {
            return back()->withErrors(['Ad not found.']);
        }

        if ($ad->user_id == $userId) {
            return back()->withErrors(['You cannot reply to your own ad.']);
        }

        // Find or create conversation
        $conversation = DB::table('conversations')
            ->where('ad_id', $ad->id)
            ->where('author_id', $ad->user_id)
            ->where('replier_id', $userId)
            ->first();

        if (!$conversation) {
            $conversationId = DB::table('conversations')->insertGetId([
                'ad_id'          => $ad->id,
                'author_id'      => $ad->user_id,
                'replier_id'     => $userId,
                'progress'       => '0%',
                'unlocked_photo' => false,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        } else {
            $conversationId = $conversation->id;
        }

        // ✅ Insert message
        DB::table('messages')->insert([
            'conversation_id' => $conversationId,
            'sender_id'       => $userId,
            'content'         => $validated['content'],
            'status'          => 'sent',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // ✅ Count messages to update progress
        $messageCount = DB::table('messages')
            ->where('conversation_id', $conversationId)
            ->count();

        $progress = '0%';
        if ($messageCount >= 8) {
            $progress = '100%';
        } elseif ($messageCount >= 6) {
            $progress = '75%';
        } elseif ($messageCount >= 4) {
            $progress = '50%';
        } elseif ($messageCount >= 2) {
            $progress = '25%';
        }

        $unlockedPhoto = $messageCount >= 3;

        DB::table('conversations')
            ->where('id', $conversationId)
            ->update([
                'progress'       => $progress,
                'unlocked_photo' => $unlockedPhoto,
                'updated_at'     => now(),
            ]);

        // ✅ (Optional) trigger email notification to ad author
        // Mail::to($ad->email)->send(new NewReplyMail($validated['content']));

        $email = $author->email;
        $content = $validated['content'];

        // Send email using Blade template
        if ($email != null) {
            Mail::send('mail.reply', [
                'name' => $replier->name,
                'content' => $content
            ], function ($content) use ($email) {
                $content->to($email)
                        ->subject('You just got a reply!');
            });
        }

        return redirect()->route('reply_confirmation');
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'location'    => 'required|string|max:255',
        ]);

        if (!Auth::check()) {
            // Save to session
            session([
                'ads' => [
                    'description'  => $validated['description'],
                    'location'     => $request->location
                ],
            ]);

            return redirect()->route('offer');
        } else {
            $user_id = Auth::id();
            $profile = DB::table('table_profiles')->where('user_id', $user_id)->first();
            $box = rand(100000, 999999);
            $slug = Str::slug($profile->location.' '.$profile->display_name.' '.$profile->occupation.' '.$profile->status.' '.$box);

            if ($profile) {
                DB::table('ads')->insert([
                    'user_id'               => $user_id,
                    'description'           => $validated['description'],
                    'slug'                  => $slug,
                    'snapshot_name'         => $profile->display_name,
                    'snapshot_occupation'   => $profile->occupation,
                    'snapshot_age'          => $profile->age,
                    'snapshot_status'       => $profile->status,
                    'snapshot_gender'       => $profile->gender,
                    'location'              => $profile->location,
                    'views'                 => 0,
                    'box_number'            => $box,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);

                return redirect()->route('ad.writing', ['box' => $box]);
            }
        }
    }

    public function toggleLike($adId)
    {
        $userId = Auth::id();

        $liked = DB::table('like')
                   ->where('ad_id', $adId)
                   ->where('user_id', $userId)
                   ->exists();

        if ($liked) {
            DB::table('like')
              ->where('ad_id', $adId)
              ->where('user_id', $userId)
              ->delete();
        } else {
            DB::table('like')->insert([
                'ad_id' => $adId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'liked' => !$liked,
        ]);
    }
}
