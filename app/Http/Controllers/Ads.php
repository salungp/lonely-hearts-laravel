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
use App\Models\Profile;
use App\Models\Like;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Package;

class Ads extends Controller
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

    public $options = [
        "height" => [
            "Tall",
            "Kinda Tall",
            "Perfectly average",
            "Not too tall",
            "Petite",
            "Small"
        ],
        "hair" => [
            "Blue hair",
            "Highlights",
            "Two-Tone",
            "Rainbow Hair"
        ],
        "eyes" => [
            "Red",
            "Blue",
            "Brown",
            "Black"
        ],
        "behavior" => [
            "Bubbly",
            "Calm",
            "Adventurous",
            "Playful",
            "Serious",
            "Confident"
        ],
        "seeking" => [
            "Sugar Daddy",
            "Sugar Baby",
            "Sugar Mommy",
            "Mentor",
            "Sponsor",
            "Companion"
        ],
        "hobby" => [
            "Reading",
            "Traveling",
            "Cooking",
            "Gaming",
            "Music",
            "Sports",
            "Drawing",
            "Art"
        ]
    ];

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
            'funny' => 'Write something funny, witty, and playful.',
            'romantic' => 'Write something sweet, affectionate, and heartwarming.',
            'casual' => 'Write in a relaxed, everyday tone, like chatting with a friend.',
            'formal' => 'Write in a professional, polite, and respectful style.',
            'literature' => 'Write something poetic, elegant, and descriptive, like classic literature.',
            'adventurous' => 'Write with excitement, boldness, and energy. Emphasize discovery and thrill.',
            'mysterious' => 'Write in a cryptic, intriguing, and dramatic style.',
            'rom-com' => 'Write like a playful, lighthearted rom-com scene.',
            'philosophical' => 'Write with deep reflections, thoughtful insights, and metaphorical language.',
            'trendy' => 'Write in a modern, stylish, pop-culture-influenced tone.',
            'storytelling' => 'Write as if narrating a short, engaging story.',
            'cinematic' => 'Write with vivid, movie-like descriptions, dramatic imagery, and action.',
        ];
    
        $instruction = $stylePrompts[$style] ?? "Write something in a clear, natural style.";
    
        if (!empty($text)) {
            $instruction = "Rewrite this text in the chosen style. Keep it under 255 characters.\n\nOriginal text: " . $text;
        } else {
            $instruction = $instruction . " Keep it under 255 characters.";
        }
    
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a text stylist. Always respond under 255 characters.'],
                ['role' => 'user', 'content' => $instruction],
            ],
        ]);
    
        $styledText = $response['choices'][0]['message']['content'] ?? '';
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
        $ad = Ad::where('box_number', $box)->first();
        $package = Package::get();
        $package_id = Package::where('id', 'cff06bcb-339f-427e-865a-7169074b2d0c')->first();
        $stylePrompts = $this->stylePrompts;
        if (!$ad) {
            abort(404);
        }
        return view('ads.reply_first', [
            'ad' => $ad,
            'prompts' => $stylePrompts,
            'package' => $package,
            'package_id' => $package_id
        ]);
    }

    public function reply_second($box): View
    {
        $ad = Ad::where('box_number', $box)->first();
        $package = Package::get();
        $package_id = Package::where('id', 'cff06bcb-339f-427e-865a-7169074b2d0c')->first();
        $stylePrompts = $this->stylePrompts;
        if (!$ad) {
            abort(404);
        }
        return view('ads.reply_second', [
            'ad' => $ad,
            'prompts' => $stylePrompts,
            'package' => $package,
            'package_id' => $package_id
        ]);
    }

    public function confirmation($box): View
    {
        $ad = Ad::where('box_number', $box)->first();
        
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
        $stylePrompts = $this->stylePrompts;
        $package = Package::get();
        $package_id = Package::where('id', 1)->first();
        return view('ads.create_first', [
            'prompts' => $stylePrompts,
            'package' => $package,
            'package_id' => $package_id
        ]);
    }

    public function create_second(): View
    {
        $package = Package::get();
        $package_id = Package::where('id', 'cff06bcb-339f-427e-865a-7169074b2d0c')->first();
        return view('ads.create_second', [
            "options" => $this->options,
            'package' => $package,
            'package_id' => $package_id
        ]);
    }

    public function writing($box)
    {
        if (Auth::check()) {
            return redirect()->route('ad.confirmation', ['box'=>$box]);
        }

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
            'ad_id'   => 'required|uuid|exists:ads,id',
        ]);

        $adId = $validated['ad_id'];

        // --- Case 1: Guest or no profile yet ---
        if (session()->has('profile') || !Auth::check()) {
            session([
                'reply' => [
                    'ad_id'   => $adId,
                    'content' => $validated['content'],
                ],
            ]);

            return response()->json([
                'success'  => true,
                'message'  => 'Ad stored in session, please log in first.',
                'redirect' => route('offer'),
            ]);
        }

        // --- Case 2: Logged-in user ---
        $userId  = Auth::id();
        $user    = Auth::user();
        $ad      = Ad::with('user')->findOrFail($adId);
        $author  = $ad->user;

        if ($ad->user_id === $userId) {
            return back()->withErrors(['You cannot reply to your own ad.']);
        }

        // Find or create conversation
        $conversation = Conversation::firstOrCreate(
            [
                'ad_id'      => $ad->id,
                'author_id'  => $ad->user_id,
                'replier_id' => $userId,
            ],
            [
                'progress'       => '0%',
                'unlocked_photo' => false,
            ]
        );

        // Insert message
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $userId,
            'content'         => $validated['content'],
            'status'          => 'sent',
        ]);

        // Update progress
        $messageCount = $conversation->messages()->count();
        $progress     = match (true) {
            $messageCount >= 8 => '100%',
            $messageCount >= 6 => '75%',
            $messageCount >= 4 => '50%',
            $messageCount >= 2 => '25%',
            default            => '0%',
        };
        $conversation->update([
            'progress'       => $progress,
            'unlocked_photo' => $messageCount >= 3,
        ]);

        // Notify ad author by email
        if ($author?->email) {
            Mail::send('mail.reply', [
                'name'    => $user->name,
                'content' => $validated['content'],
            ], function ($message) use ($author) {
                $message->to($author->email)
                        ->subject('You just got a reply!');
            });
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Ad stored in session, please log in first.',
            'redirect' => route('offer'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'location'    => 'required|string|max:255',
        ]);

        // --- Case 1: User not logged in yet ---
        if (!Auth::check()) {
            session([
                'ads' => [
                    'description' => $validated['description'],
                    'location'    => $validated['location'],
                ],
            ]);

            return response()->json([
                'success'  => true,
                'message'  => 'Ad stored in session, please log in first.',
                'redirect' => route('offer'),
            ]);
        }

        // --- Case 2: Logged in but no profile ---
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        if (!$profile) {
            // Force them to create a profile before posting an ad
            return response()->json([
                'success'  => false,
                'message'  => 'Please create your profile before posting an ad.',
                'redirect' => route('profile.create'),
            ], 403);
        }

        // --- Generate witty title ---
        $prompt = $this->generateAdPrompt($profile, $validated['description']);
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a witty personal ad writer.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $title = trim($response['choices'][0]['message']['content'] ?? 'Seeking someone special');
        $title = preg_replace('/^["“]|["”]$/u', '', $title);
        $title = str_replace('!', '', $title);
        $title = trim($profile->display_name.' '.$validated['location'].' '.$title);

        // Ensure slug is unique
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;
        while (Ad::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        // Generate box number
        $box = rand(100000, 999999);

        // --- Save Ad ---
        $ad = Ad::create([
            'user_id'             => $user->id,
            'description'         => $validated['description'],
            'title'               => $title,
            'slug'                => $slug,
            'snapshot_name'       => $profile->display_name,
            'snapshot_occupation' => $profile->occupation,
            'snapshot_age'        => $profile->age,
            'snapshot_status'     => $profile->status,
            'snapshot_gender'     => $profile->gender,
            'location'            => $validated['location'],
            'views'               => 0,
            'box_number'          => $box,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ad created successfully',
            'data' => [
                'id'         => $ad->id,
                'title'      => $title,
                'slug'       => $slug,
                'box_number' => $box,
            ],
            'redirect' => route('ad.confirmation', ['box' => $box]),
        ]);
    }

    /**
     * Build the GPT prompt for ad title generation
     */
    protected function generateAdPrompt(Profile $profile, string $description): string
    {
        return "
        You are writing humorous and quirky personal ad titles, like in the book 'They Call Me Naughty Lola'.
        Examples:
        - Tonight, female readers to 90, I am the hunter and you are my quarry.
        - If we share a bath together I have to insist on wearing verruca socks.
        - I’ll see you at the singles night.

        Rules:
        - Output only ONE short title (max 8 words).
        - Do not use quotation marks or exclamation marks.
        - No explanations.

        Profile:
        Name: {$profile->display_name}
        Age: {$profile->age}
        Gender: {$profile->gender}
        Location: {$profile->location}
        Status: {$profile->status}
        Occupation: {$profile->occupation}
        Description: {$description}
        ";
    }

    
    public function toggleLike(Ad $ad)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $existing = Like::where('ad_id', $ad->id)->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            Like::create(['ad_id' => $ad->id, 'user_id' => $userId]); // UUID set by model
            $liked = true;
        }

        $count = Like::where('ad_id', $ad->id)->count();

        return response()->json([
            'liked' => $liked,
            'count' => $count,
        ]);
    }
}
