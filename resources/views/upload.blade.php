<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>Prophet - Instagram Management</title>
</head>
<body>
<nav class="bg-blue-600 p-4 text-white"> <!-- Blue header -->
        <div class="container mx-auto flex justify-between">
            <a href="/" class="text-lg font-semibold">Prophet</a>
            <a href="{{ route('instagram.upload') }}" class="hover:bg-blue-700 p-2 rounded">Upload Posts</a>
            <a href="{{ route('instagram.edit.index') }}" class="hover:bg-blue-700 p-2 rounded">Edit Posts</a>
<a href="{{ route('instagram.delete.index') }}" class="hover:bg-blue-700 p-2 rounded">Delete Posts</a>
<a href="{{ route('instagram.insights') }}" class="hover:bg-blue-700 p-2 rounded">Insights</a>
        </div>
    </nav>

<div class="container mx-auto p-4">
        <!-- Success and Error Alerts -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif



        <!-- Photo Upload Section -->
        <div class="flex justify-between items-start">
            <div class="bg-white rounded shadow p-6 w-full lg:w-1/2">
                <h2 class="text-xl font-semibold mb-4">Upload Photo</h2>
                <form action="{{ route('instagram.photo.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="block text-gray-700 text-sm font-bold mb-2">Choose a photo:</label>
                        <input type="file" id="file" name="file" required class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label for="caption" class="block text-gray-700 text-sm font-bold mb-2">Caption:</label>
                        <input type="text" id="caption" name="caption" required class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Upload</button>
                </form>
            </div>
            @if(session('imageUrl'))
                <div class="w-full lg:w-1/2 ml-4">
                    <h3 class="text-xl font-semibold mb-4">Last Uploaded Photo:</h3>
                    <img src="{{ session('imageUrl') }}" alt="Uploaded Photo" class="max-w-xs rounded shadow">
                    <p class="mt-2">{{ session('caption') }}</p>
                </div>
            @endif
        </div>
        
        <!-- Video Upload Section -->
        <div class="flex justify-between items-start mt-6">
            <div class="bg-white rounded shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Upload Video</h2>
                <form action="{{ route('instagram.video.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="video" class="block text-gray-700 text-sm font-bold mb-2">Choose a video:</label>
                        <input type="file" id="video" name="video" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label for="video_caption" class="block text-gray-700 text-sm font-bold mb-2">Caption:</label>
                        <input type="text" id="video_caption" name="caption" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Upload Video</button>
                </form>
            </div>
            @if(session('videoUrl'))
                <div class="ml-4">
                    <h3 class="text-xl font-semibold mb-4">Last Uploaded Video:</h3>
                    <video controls class="max-w-xs">
                        <source src="{{ session('videoUrl') }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <p>{{ session('videoCaption') }}</p>
                </div>
            @endif
        </div>

        <!-- Photo Story Upload Section -->
        <div class="flex justify-between items-start mt-6">
            <div class="bg-white rounded shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Upload Photo to Story</h2>
                <form action="{{ route('instagram.photo.upload_to_story') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="story_file" class="block text-gray-700 text-sm font-bold mb-2">Choose a photo for your story:</label>
                        <input type="file" id="story_file" name="file" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Upload to Story</button>
                </form>
            </div>
            @if(session('storyImageUrl'))
                <div class="ml-4">
                    <h3 class="text-xl font-semibold mb-4">Last Uploaded Photo Story:</h3>
                    <img src="{{ session('storyImageUrl') }}" alt="Story Photo" class="max-w-xs">
                </div>
            @endif
        </div>

        <!-- Video Story Upload Section -->
        <div class="flex justify-between items-start mt-6">
            <div class="bg-white rounded shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Upload Video to Story</h2>
                <form action="{{ route('instagram.video.upload_to_story') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="video_story" class="block text-gray-700 text-sm font-bold mb-2">Choose a video for the story:</label>
                        <input type="file" id="video_story" name="video" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Upload Video to Story</button>
                </form>
            </div>
            @if(session('videoStoryUrl'))
                <div class="ml-4">
                    <h3 class="text-xl font-semibold mb-4">Last Uploaded Video Story:</h3>
                    <video controls class="max-w-xs">
                        <source src="{{ session('videoStoryUrl') }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            @endif
        </div>

        <h2 class="text-xl font-semibold mb-4">Best time to post</h2>
            <form action="{{ route('account.insights') }}" method="POST">
                @csrf
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Get Best time to post</button>
            </form>

            @if(!empty($bestPostingTime))
                <h3 class="mt-4 font-semibold">Best Time to Post:</h3>
                <ul class="list-disc pl-5 mt-2">
                    @foreach($bestPostingTime as $day => $time)
                        <li><strong>{{ $day }}:</strong> {{ $time }}m</li>
                    @endforeach
                </ul>
            @endif
    </div>
</body>
</html>
