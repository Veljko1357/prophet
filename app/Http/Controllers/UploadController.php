<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\InstagramSession;
use App\Models\StoryImage;
use App\Models\StoryVideo;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    protected $username;
    protected $password;
    protected $instagrapiUrl;
    protected $sessionId;


    public function __construct()
    {
        $this->instagrapiUrl = env('INSTAGRAPI_URL');
        $this->username = env('INSTAGRAM_USERNAME');
        $this->password = env('INSTAGRAM_PASSWORD');
    }

    public function index()
    {

        return view('upload');
    }

    public function uploadPhoto(Request $request) //method for uploading photos, storing them locally and in the database under the logged in user with the original name
    {
        if (!Auth::check()) {
            return back()->with('error', 'You must be logged in.');
        }

        $validatedData = $request->validate([
            'file' => 'required|image|max:8000',
            'caption' => 'required|string|max:2200',
        ]);

        $file = $validatedData['file'];
        $caption = $validatedData['caption'];

        $originalFileName = $file->getClientOriginalName();
        $filePath = 'uploads/images/' . $originalFileName;

        if (Storage::disk('public')->exists($filePath)) {
            session([
                'existingImageUrl' => asset('storage/' . $filePath),
                'existingCaption' => $caption,
                'confirmUpload' => true
            ]);
            return back()->with('info', 'This photo already exists. Do you want to upload it again?');
        }


        $file->storeAs('uploads/images', $originalFileName, 'public');

        $instagramSession = InstagramSession::where('user_id', Auth::id())->latest('created_at')->firstOrFail();

        $response = Http::timeout(30)->asMultipart()->post("{$this->instagrapiUrl}/photo/upload", [ //added timeout as for some reason it stopped working 
            [
                'name' => 'sessionid',
                'contents' => $instagramSession->session_id,
            ],
            [
                'name' => 'file',
                'contents' => fopen(storage_path('app/public/' . $filePath), 'r'),
                'filename' => $originalFileName,
            ],
            [
                'name' => 'caption',
                'contents' => $caption,
            ],
        ]);

        if ($response->successful()) {

            $responseData = $response->json();
            $mediaId = $responseData['id'] ?? null;
            $imageUrl = $responseData['image_versions2']['candidates'][0]['url'] ?? null;


            Image::create([
                'user_id' => Auth::id(),
                'media_id' => $mediaId,
                'file_path' => $filePath,
                'caption' => $caption,
                'url' => $imageUrl
            ]);

            session([
                'imageUrl' => asset('storage/' . $filePath),
                'caption' => $caption,
            ]);
            return back()->with('success', 'Photo uploaded successfully.');
        } else {
            Storage::disk('public')->delete($filePath);
            $error = 'Failed to upload photo: ' . $response->body();
            Log::error($error);
            return back()->with('error', $error);
        }
    }




    public function uploadVideo(Request $request) //method for uploading videos, storing them locally and in the database under the logged in user with the original name
    {
        if (!Auth::check()) {
            return back()->with('error', 'You must be logged in.');
        }


        $video = $request->file('video');
        $caption = $request->input('caption');

        $videoPath = $video->store('uploads/videos', 'public');


        $instagramSession = InstagramSession::where('user_id', Auth::id())->latest('created_at')->firstOrFail();


        $postData = [
            [
                'name' => 'sessionid',
                'contents' => $instagramSession->session_id,
            ],
            [
                'name' => 'file',
                'contents' => fopen(storage_path('app/public/' . $videoPath), 'r'),
                'filename' => $video->getClientOriginalName(),
            ],
            [
                'name' => 'caption',
                'contents' => $caption,
            ],
        ];

        $response = Http::timeout(30)->asMultipart()->post("{$this->instagrapiUrl}/video/upload", $postData);

        if ($response->successful()) {
            $responseData = $response->json();
            $mediaId = $responseData['id'] ?? null; 
            $videoUrl = $responseData['video_url'] ?? null; 


            Video::create([
                'user_id' => Auth::id(),
                'media_id' => $mediaId,
                'file_path' => $videoPath,
                'caption' => $caption,
                'url' => $videoUrl
            ]);


            session([
                'videoUrl' => asset('storage/' . $videoPath),
                'videoCaption' => $caption,
            ]);

            return back()->with('success', 'Video uploaded successfully.');
        } else {
            Storage::disk('public')->delete($videoPath);

            $error = 'Failed to upload video: ' . $response->body();
            Log::error($error);
            return back()->with('error', $error);
        }
    }


//methods for uploading photos and videos to stories as well as saving them locally and in the DB

    public function uploadPhotoToStory(Request $request) 
    {
        if (!Auth::check()) {
            return back()->with('error', 'You must be logged in.');
        }


        $file = $request->file('file');


        $filePath = $file->store('uploads/stories/images', 'public');


        $instagramSession = InstagramSession::where('user_id', Auth::id())->latest('created_at')->firstOrFail();


        $postData = [
            [
                'name' => 'sessionid',
                'contents' => $instagramSession->session_id,
            ],
            [
                'name' => 'file',
                'contents' => fopen(storage_path('app/public/' . $filePath), 'r'),
                'filename' => $file->getClientOriginalName(),
            ],

        ];


        $response = Http::asMultipart()->post("{$this->instagrapiUrl}/photo/upload_to_story", $postData);

        if ($response->successful()) {
            $responseData = $response->json();


            $mediaId = $responseData['pk'] ?? null;
            $storyPhotoUrl = $responseData['thumbnail_url'] ?? null; 


            if ($mediaId && $storyPhotoUrl) {

                StoryImage::create([
                    'user_id' => Auth::id(),
                    'media_id' => $mediaId,
                    'file_path' => $filePath,
                    'url' => $storyPhotoUrl
                ]);
            }


            session(['storyImageUrl' => asset('storage/' . $filePath)]);

            return back()->with('success', 'Photo uploaded to story successfully.');
        } else {

            Storage::disk('public')->delete($filePath);

            $error = 'Failed to upload photo to story: ' . $response->body();
            Log::error($error);
            return back()->with('error', $error);
        }
    }

    public function uploadVideoToStory(Request $request)
    {
        if (!Auth::check()) {
            return back()->with('error', 'You must be logged in.');
        }

        $video = $request->file('video');
        if (!$video) {
            return back()->with('error', 'The video file is required.');
        }


        $originalFileName = $video->getClientOriginalName();
        $videoPath = 'uploads/stories/videos/' . $originalFileName;


        $video->storeAs('uploads/stories/videos', $originalFileName, 'public');

        $instagramSession = InstagramSession::where('user_id', Auth::id())->latest('created_at')->firstOrFail();

        $response = Http::asMultipart()->post("{$this->instagrapiUrl}/video/upload_to_story", [
            [
                'name' => 'sessionid',
                'contents' => $instagramSession->session_id,
            ],
            [
                'name' => 'file',
                'contents' => fopen(storage_path('app/public/' . $videoPath), 'r'),
                'filename' => $originalFileName,
            ],

        ]);

        if ($response->successful()) {
            $responseData = $response->json();


            $mediaId = $responseData['pk'] ?? null;
            $storyVideoUrl = $responseData['thumbnail_url'] ?? null; 


            if ($mediaId && $storyVideoUrl) {

                StoryVideo::create([
                    'user_id' => Auth::id(),
                    'media_id' => $mediaId,
                    'file_path' => $videoPath,
                    'url' => $storyVideoUrl
                ]);
            }

            session([
                'videoStoryUrl' => asset('storage/' . $videoPath)
            ]);

            return back()->with('success', 'Video uploaded to story successfully.');
        } else {
            Storage::disk('public')->delete($videoPath);
            $error = 'Failed to upload video to story: ' . $response->body();
            Log::error($error);
            return back()->with('error', $error);
        }
    }



}
