<?php

namespace App\Http\Controllers;

use App\Models\InstagramSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InsightsController extends Controller
{
    protected $username;
    protected $sessionId;
    protected $instagrapiUrl;


    public function __construct()
    {
        $this->instagrapiUrl = env('INSTAGRAPI_URL');
        $this->username = env('INSTAGRAM_USERNAME');
    }

//methods for fetching account and media insights
    public function fetchInsights(Request $request)
    {
        $user = Auth::user(); 
        $instagramSession = InstagramSession::where('user_id', $user->id)->latest()->first();

        if (!$instagramSession) {
            return back()->with('error', 'No Instagram session found.');
        }

        $response = Http::asForm()->post("{$this->instagrapiUrl}/insights/account", [
            'sessionid' => $instagramSession->session_id,
        ]);

        if ($response->successful()) {
            $insightsData = $response->json();
            $importantMetrics = $this->extractMetrics($insightsData);

            return view('insights', ['metrics' => $importantMetrics]);
        } else {
            return back()->with('error', 'Failed to fetch account insights.');
        }
    }

 
    protected function extractMetrics($data)
    {
        $metrics = [
            'Impressions' => $data['account_insights_unit']['impressions_metric_count'] ?? 'N/A',
            'Reach' => $data['account_insights_unit']['reach_metric_count'] ?? 'N/A',
            'Profile Visits' => $data['account_insights_unit']['profile_visits_metric_count'] ?? 'N/A',
            'Follower Growth' => $data['followers_unit']['followers_delta_from_last_week'] ?? 'N/A',
            'Total Posts' => $data['account_summary_unit']['posts_count'] ?? 'N/A',
            'Story Engagement' => $data['stories_unit']['last_week_stories_count'] ?? 'N/A',

        ];

        return $metrics;
    }



    public function fetchMediaInsights(Request $request)
    {
        if ($request->has('fetch_insights')) {
            $instagramSession = InstagramSession::where('username', $this->username)->first();

            if (!$instagramSession) {
                Log::error('No Instagram session found for username: ' . $this->username);
                return back()->with('error', 'No Instagram session found.');
            }

            $queryParams = http_build_query([
                'post_type' => $request->input('post_type', 'ALL'),
                'time_frame' => $request->input('time_frame', 'TWO_YEARS'),
                'data_ordering' => $request->input('data_ordering', 'REACH_COUNT'),
                'count' => $request->input('count', 0),
            ]);

            $url = "{$this->instagrapiUrl}/insights/media_feed_all?{$queryParams}";

            $response = Http::asForm()->post($url, [
                'sessionid' => $instagramSession->session_id,
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $insightsData = [];

                foreach ($responseData as $item) {
                    if (isset($item['node'])) {
                        $node = $item['node'];
                        $carouselChildren = [];

                        if ($node['instagram_media_type'] === 'CAROUSEL_V2' && isset($node['carousel_children'])) {
                            foreach ($node['carousel_children'] as $child) {
                                $carouselChildren[] = [
                                    'image_url' => $child['image']['uri'] ?? null,
                              
                                ];
                            }
                        }

                        $insightsData[] = [
                            'media_id' => $node['instagram_media_id'] ?? null,
                            'media_type' => $node['instagram_media_type'] ?? null,
                            'image_url' => $node['image']['uri'] ?? null,
                            'like_count' => $node['like_count'] ?? 0,
                            'engagement' => $node['engagement'] ?? 0,
                            'reach_count' => $node['inline_insights_node']['metrics']['reach_count'] ?? 0,
                            'carousel_children' => $carouselChildren,
                            'video_url' => $node['video_url'] ?? null,
                        ];
                    }
                }

                return view('insights', compact('insightsData'));
            } else {
                $error = 'Failed to fetch insights: ' . $response->body();
                Log::error($error);
                return back()->with('error', $error);
            }
        }

        $insightsData = [];
        return view('insights', compact('insightsData'));
    }
    public function showInsights()
    {

        $insightsData = [];


        return view('insights', compact('insightsData'));
    }

}




