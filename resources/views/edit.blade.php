<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')

    <title>Document</title>
</head>
<body>
<nav class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between">
            <a href="/" class="text-lg font-semibold">Prophet</a>
            <a href="{{ route('instagram.upload') }}" class="hover:bg-blue-700 p-2 rounded">Upload Posts</a>
<a href="{{ route('instagram.edit.index') }}" class="hover:bg-blue-700 p-2 rounded">Edit Posts</a>
<a href="{{ route('instagram.delete.index') }}" class="hover:bg-blue-700 p-2 rounded">Delete Posts</a>
<a href="{{ route('instagram.insights') }}" class="hover:bg-blue-700 p-2 rounded">Insights</a>
        </div>
    </nav>
@foreach($images as $image)
    <div class="mb-6">
        <img src="{{ asset('storage/uploads/images/' . basename($image->file_path)) }}" alt="Image" style="width: 300px; height: 300px; object-fit: cover;">
        <form action="{{ route('media.edit', $image->media_id) }}" method="POST">
            @csrf
            <textarea name="caption" rows="3" class="border rounded  py-2 px-3 text-gray-700 leading-tight">{{ $image->caption }}</textarea>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2">Update</button>
        </form>
    </div>
@endforeach
</body>
</html>