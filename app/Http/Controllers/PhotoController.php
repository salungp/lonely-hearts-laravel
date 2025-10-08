<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Photo;
use Illuminate\Support\Str;

class PhotoController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'photos.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $files = $request->file('photos');
        $uploaded = [];

        foreach ($files as $index => $file) {
            // --- Create directory if not exists ---
            $uploadPath = public_path('uploads/ad_photos');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // --- Generate safe random file name ---
            $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();

            // --- Move file to /public/uploads/ad_photos ---
            $file->move($uploadPath, $fileName);

            $photoData = [
                'id'          => Str::uuid(),
                'file_path'   => 'uploads/ad_photos/' . $fileName, // accessible via asset()
                'sort_order'  => $index,
                'is_primary'  => $index === 0,
            ];

            $sessionPhotos = session('uploaded_photos', []);
            $sessionPhotos[] = $photoData;
            session(['uploaded_photos' => $sessionPhotos]);

            $uploaded[] = $photoData;
        }

        return redirect()->back()->with('success', 'The photo has been set!');
    }

    public function remove(Request $request)
    {
        $request->validate(['file_path' => 'required|string']);

        $filePath = public_path($request->file_path);

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // --- Remove from session if exists ---
        $sessionPhotos = session('uploaded_photos', []);
        $filtered = array_filter($sessionPhotos, fn($p) => $p['file_path'] !== $request->file_path);
        session(['uploaded_photos' => array_values($filtered)]);

        return response()->json(['success' => true]);
    }
}