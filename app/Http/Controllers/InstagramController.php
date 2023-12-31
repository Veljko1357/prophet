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

class InstagramController extends Controller
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

    public function login(Request $request)
{
    // Attempt to login with Instagrapi
    $response = Http::asForm()->post("{$this->instagrapiUrl}/auth/login", [
        "username" => $this->username,
        "password" => $this->password,
    ]);

    if ($response->successful()) {
        $sessionData = $response->json();
        $encodedSessionId = $sessionData ?? null;

        if ($encodedSessionId) {
            $sessionId = urldecode($encodedSessionId);

            // Check if a Laravel user exists for this Instagram username, otherwise create it
            $user = User::firstOrCreate(
                ['username' => $this->username],
                ['password' => Hash::make($this->password)] // Note: Storing Instagram passwords is not recommended
            );

            // Log the user into Laravel's session
            Auth::login($user, true);

            // Save the Instagram session linked to the Laravel user
            InstagramSession::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'username' => $this->username, 
                    'session_id' => $sessionId // Ensure session_id is included here
                ]
            );

            // Save additional user info if needed
            $this->saveUserInfo($sessionId, $user->id);

            // Redirect to a route after successful login
            return redirect()->route('instagram.upload');
        } else {
            return back()->with('error', 'Failed to obtain session ID.');
        }
    } else {
        return back()->with('error', 'Failed to login with Instagram.');
    }
}
    

protected function saveUserInfo($sessionId, $userId)
{
    $response = Http::asForm()->post("{$this->instagrapiUrl}/user/info", [
        'sessionid' => $sessionId,
    ]);

    if ($response->successful()) {
        $userInfoData = $response->json();

        // Update or create the user info linked to the Laravel user
        UserInfo::updateOrCreate(
            ['user_id' => $userId],
            [
                'username' => $userInfoData['username'],
                'profile_picture_url' => $userInfoData['profile_pic_url'],
                'bio' => $userInfoData['biography'],
                'media_count' => $userInfoData['media_count'],
                'followers_count' => $userInfoData['follower_count'],
                'following_count' => $userInfoData['following_count'],
            ]
        );
    } else {
        Log::error('Failed to fetch user info: ' . $response->body());
    }
}

  

    public function fetchAndAnalyzeAccountInsights(Request $request)
    {
        $instagramSession = InstagramSession::where('username', $this->username)->first();
    
        if (!$instagramSession) {
            return back()->with('error', 'No Instagram session found.');
        }
    
        $response = Http::asForm()->post("{$this->instagrapiUrl}/insights/account", [
            'sessionid' => $instagramSession->session_id,
        ]);
    
        if ($response->successful()) {
            $insightsData = $response->json();
            
            // Analyze the insights data to determine the best time to post
            $bestPostingTime = $this->calculateBestPostingTime($insightsData);
    
            return view('upload', ['bestPostingTime' => $bestPostingTime]);
        } else {
            $error = 'Failed to fetch account insights: ' . $response->body();
            Log::error($error);
            return back()->with('error', $error);
        }
    }
    
    protected function calculateBestPostingTime($insightsData)
    {
        $bestTimesPerDay = [];
    
        if (isset($insightsData['followers_unit']['days_hourly_followers_graphs'])) {
            foreach ($insightsData['followers_unit']['days_hourly_followers_graphs'] as $dayData) {
                $dayName = $dayData['name'];
                $maxValue = 0;
                $bestTimeForDay = '';
    
                foreach ($dayData['data_points'] as $timePoint) {
                    if ($timePoint['value'] > $maxValue) {
                        $maxValue = $timePoint['value'];
                        $bestTimeForDay = $timePoint['label'];
                    }
                }
    
                if ($bestTimeForDay != '') {
                    $bestTimesPerDay[$dayName] = $bestTimeForDay;
                }
            }
        }
    
        return $bestTimesPerDay;
    }
    

}