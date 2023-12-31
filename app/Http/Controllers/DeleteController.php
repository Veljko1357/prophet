<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\InstagramSession;
use App\Models\StoryImage;
use App\Models\StoryVideo;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DeleteController extends Controller
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
        $userId = Auth::id();
        $photos = Image::where('user_id', $userId)->get();
        $videos = Video::where('user_id', $userId)->get();
        $storyImages = StoryImage::where('user_id', $userId)->get();
        $storyVideos = StoryVideo::where('user_id', $userId)->get();

    
        return view('delete', compact('photos', 'videos', 'storyImages', 'storyVideos'));
    }

    //method for deleteing media locally, from DB and from instagram

    public function destroy($mediaType, $mediaId)
    {
        $userId = Auth::id();
        $instagramSession = InstagramSession::where('user_id', $userId)->first();

        if (!$instagramSession) {
            return back()->with('error', 'Instagram session not found.');
        }

        $sessionId = $instagramSession->session_id;
        $mediaItem = null;
        $filePath = '';
        

        switch ($mediaType) {
            case 'photo':
                $mediaItem = Image::where('user_id', $userId)->where('id', $mediaId)->first();
                $filePath = 'public/uploads/images/' . basename($mediaItem->file_path);
                break;
            case 'video':
                $mediaItem = Video::where('user_id', $userId)->where('id', $mediaId)->first();
                $filePath = 'public/uploads/videos/' . basename($mediaItem->file_path);
                break;
            case 'story-image':
                $mediaItem = StoryImage::where('user_id', $userId)->where('id', $mediaId)->first();
                $filePath = 'public/uploads/stories/images/' . basename($mediaItem->file_path);
                break;
            case 'story-video':
                $mediaItem = StoryVideo::where('user_id', $userId)->where('id', $mediaId)->first();
                $filePath = 'public/uploads/stories/videos/' . basename($mediaItem->file_path);
                break;
            default:
                return back()->with('error', 'Invalid media type.');
        }

        if ($mediaItem) {
            $response = Http::asForm()->post("{$this->instagrapiUrl}/media/delete", [
                'sessionid' => $sessionId,
                'media_id'  => $mediaItem->media_id,
            ]);

            if ($response->successful()) {
                if(Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }
                
                $mediaItem->delete();
                
                return back()->with('success', 'Media deleted successfully.');
            } else {
                return back()->with('error', 'Failed to delete media from Instagram.');
            }
        }

        return back()->with('error', 'Media not found.');
    }

}

