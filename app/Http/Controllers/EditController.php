<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\InstagramSession;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class EditController extends Controller
{
    protected $instagrapiUrl;

    public function __construct()
    {
        $this->instagrapiUrl = env('INSTAGRAPI_URL');
    }

    public function index()
    {
        $userId = Auth::id();
        $images = Image::where('user_id', $userId)->get();
        $videos = Video::where('user_id', $userId)->get();
        

        return view('edit', compact('images', 'videos'));
    }

// method for editing meida
    public function editMedia(Request $request, $id)
    {
 
        $instagramSession = InstagramSession::where('user_id', Auth::id())->latest('created_at')->firstOrFail();


        $response = Http::asForm()->post(env('INSTAGRAPI_URL') . '/media/edit', [
            'sessionid' => $instagramSession->session_id,
            'media_id' => $id,
            'caption' => $request->caption,
        ]);


        if ($response->successful()) {

            $image = Image::where('media_id', $id)->first();
            if ($image) {
                $image->caption = $request->caption;
                $image->save();
            }

            return back()->with('success', 'Caption updated successfully.');
        } else {

            return back()->with('error', 'Error updating caption: ' . $response->body());
        }
    }
}

