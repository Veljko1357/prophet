<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>Document</title>
</head>

<body class="bg-gray-100">
    <nav class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between">
            <a href="instagram.in" class="text-lg font-semibold">Post Prophet</a>
            <a href="{{ route('instagram.insights') }}" class="hover:bg-blue-700 p-2 rounded">Upload Posts</a>
            <a href="{{ route('instagram.edit.index') }}" class="hover:bg-blue-700 p-2 rounded">Edit Posts</a>
            <a href="{{ route('instagram.delete.index') }}" class="hover:bg-blue-700 p-2 rounded">Delete Posts</a>
            <a href="{{ route('instagram.insights') }}" class="hover:bg-blue-700 p-2 rounded">Insights</a>
        </div>
    </nav>
    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold mb-8 text-center">Delete Media</h1>

     <!-- success and error messages -->
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

 <!-- Photos -->
        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-4 text-center">Photos</h2>
            <div class="flex flex-wrap justify-center gap-4">
                @foreach($photos as $photo)
                <div style="width: 300px;"> 
                    <div class="bg-white rounded-lg overflow-hidden shadow-lg mb-4">
                        <img src="{{ asset('storage/uploads/images/' . basename($photo->file_path)) }}" alt="Photo"
                            style="width: 300px; height: 300px; object-fit: cover;"> 
                    </div>
                    <form action="{{ route('instagram.delete', ['mediaType' => 'photo', 'mediaId' => $photo->id]) }}"
                        method="POST" style="width: 300px;"> 
                        @csrf
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded w-full">Delete</button>
                    </form>
                </div>
                @endforeach
            </div>
        </section>
 <!-- videos -->
        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-4 text-center">Videos</h2>
            <div class="flex flex-wrap justify-center gap-4">
                @foreach($videos as $video)
                <div style="width: 300px;">
                    <div class="bg-white rounded-lg overflow-hidden shadow-lg mb-4">
                        <video controls class="w-full h-full" style="width: 300px; height: 300px;">
                            <source src="{{ asset('storage/uploads/videos/' . basename($video->file_path)) }}"
                                type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <form action="{{ route('instagram.delete', ['mediaType' => 'video', 'mediaId' => $video->id]) }}"
                        method="POST" style="width: 300px;">
                        @csrf
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded w-full">Delete</button>
                    </form>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Story Images -->
        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-4 text-center">Story Images</h2>
            <div class="flex flex-wrap justify-center gap-4">
                @foreach($storyImages as $storyImage)
                <div style="width: 300px;">
                    <div class="bg-white rounded-lg overflow-hidden shadow-lg mb-4">
                        <img src="{{ asset('storage/uploads/stories/images/' . basename($storyImage->file_path)) }}"
                            alt="Story Image" style="width: 300px; height: 300px; object-fit: cover;">
                    </div>
                    <form
                        action="{{ route('instagram.delete', ['mediaType' => 'story-image', 'mediaId' => $storyImage->id]) }}"
                        method="POST" style="width: 300px;">
                        @csrf
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded w-full">Delete</button>
                    </form>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Story Videos -->
        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-4 text-center">Story Videos</h2>
            <div class="flex flex-wrap justify-center gap-4">
                @foreach($storyVideos as $storyVideo)
                <div style="width: 300px;">
                    <div class="bg-white rounded-lg overflow-hidden shadow-lg mb-4">
                        <video controls class="w-full h-full" style="width: 300px; height: 300px;">
                            <source
                                src="{{ asset('storage/uploads/stories/videos/' . basename($storyVideo->file_path)) }}"
                                type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <form
                        action="{{ route('instagram.delete', ['mediaType' => 'story-video', 'mediaId' => $storyVideo->id]) }}"
                        method="POST" style="width: 300px;">
                        @csrf
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded w-full">Delete</button>
                    </form>
                </div>
                @endforeach
            </div>
        </section>

    </div>
</body>

</html>