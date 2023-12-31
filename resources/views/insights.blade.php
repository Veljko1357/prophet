<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between">
            <a href="/" class="text-lg font-semibold">Prophet</a>
            <a href="{{ route('instagram.upload') }}" class="hover:bg-blue-700 p-2 rounded">Upload Posts</a>
<a href="{{ route('instagram.edit.index') }}" class="hover:bg-blue-700 p-2 rounded">Edit Posts</a>
<a href="{{ route('instagram.delete.index') }}" class="hover:bg-blue-700 p-2 rounded">Delete Posts</a>
<a href="{{ route('instagram.insights') }}" class="hover:bg-blue-700 p-2 rounded">Insights</a>
        </div>
    </nav>
<div class="container mx-auto p-4 flex flex-wrap gap-8">

    <div class="w-full lg:w-1/2 bg-white shadow-md rounded p-6">
        <h1 class="text-xl font-bold mb-4">Instagram Insights</h1>
        
        <!-- Form for fetching insights -->
        <form action="{{ route('instagram.insights') }}" method="POST" class="mb-4">
            @csrf
            <!-- Post Type Select -->
            <div class="mb-4">
                <label for="post_type" class="block text-gray-700 text-sm font-bold mb-2">Post Type:</label>
                <select id="post_type" name="post_type" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="ALL">All</option>
                    <option value="CAROUSEL_V2">Carousel</option>
                    <option value="IMAGE">Image</option>
                    <option value="SHOPPING">Shopping</option>
                    <option value="VIDEO">Video</option>
                </select>
            </div>
            
            <!-- Time Frame Select -->
            <div class="mb-4">
                <label for="time_frame" class="block text-gray-700 text-sm font-bold mb-2">Time Frame:</label>
                <select id="time_frame" name="time_frame" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="ONE_WEEK">One Week</option>
                    <option value="ONE_MONTH">One Month</option>
                    <option value="THREE_MONTHS">Three Months</option>
                    <option value="SIX_MONTHS">Six Months</option>
                    <option value="ONE_YEAR">One Year</option>
                    <option value="TWO_YEARS" selected>Two Years</option>
                </select>
            </div>

            <!-- Data Ordering Select -->
            <div class="mb-4">
                <label for="data_ordering" class="block text-gray-700 text-sm font-bold mb-2">Data Ordering:</label>
                <select id="data_ordering" name="data_ordering" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="REACH_COUNT" selected>Reach Count</option>
                    <option value="LIKE_COUNT">Like Count</option>
                    <option value="FOLLOW">Follow</option>
                    <option value="SHARE_COUNT">Share Count</option>
                    <option value="BIO_LINK_CLICK">Bio Link Click</option>
                    <option value="COMMENT_COUNT">Comment Count</option>
                    <option value="IMPRESSION_COUNT">Impression Count</option>
                    <option value="PROFILE_VIEW">Profile View</option>
                    <option value="VIDEO_VIEW_COUNT">Video View Count</option>
                    <option value="SAVE_COUNT">Save Count</option>
                </select>
            </div>

            <!-- Count Input -->
            <div class="mb-4">
                <label for="count" class="block text-gray-700 text-sm font-bold mb-2">Count:</label>
                <input type="number" id="count" name="count" min="0" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('count', 0) }}">
            </div>

            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Fetch Insights</button>
        </form>
    </div>

    <!-- Column for the media feed insights -->
    <div class="w-full lg:w-1/2 bg-white shadow-md rounded p-6">
        <h1 class="text-xl font-bold mb-4">Instagram Media Feed Insights</h1>
        
        @if (!empty($mediaFeedInsights))
            @foreach ($mediaFeedInsights as $item)
                <div class="mb-4 p-4 border-b">
                    <h2 class="text-lg font-bold">Media ID: {{ $item['media_id'] }}</h2>
                    <p class="text-gray-600">Reach Count: {{ $item['reach_count'] }}</p>
                    <p class="text-gray-600">Like Count: {{ $item['like_count'] }}</p>
                    <p class="text-gray-600">Impressions: {{ $item['impression'] }}</p>

                </div>
            @endforeach
        @else
            <p class="text-gray-600">No media feed insights available.</p>
        @endif
    </div>
</div>

</body>
</html>