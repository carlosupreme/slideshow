<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\File;
use App\Models\Slideshow;

Route::get('/greet', function (Request $request) {
    return response()->json(['message' => 'Hello World!', 'req' => $request->all()]);
});

Route::post('/slide', function (Request $request){
    $FOLDER = 'media/';

    $files = $request->allFiles();
    $title = $request->title;

    $slideshow = Slideshow::create(['title' => $title]);

    foreach ($files as $file){
        $fileName = $FOLDER . Str::random(30) . '.' . $file->extension();
        Storage::disk('public')->put($fileName, file_get_contents($file));
        $file = File::create([
            'name' => $file->getClientOriginalName(),
            'slideshow_id' => $slideshow->id,
            'path' => $fileName,
            'size' => $file->getSize(),
            'type' => $file->getClientMimeType()
        ]);
    }

    $slide = Slideshow::with('files')->findOrFail($slideshow->id);

    return response()->json(['message' => 'File uploaded successfully', 'slideshow' => $slide]);
});

Route::put('/slide/{id}', function (Request $request, string $id){
    $FOLDER = 'media/';

    $files = $request->allFiles();

    foreach ($files as $file){
   	 $fileName = $FOLDER . Str::random(30) . '.' . $file->extension();
    	 Storage::disk('public')->put($fileName, file_get_contents($file));

   	 $file = File::create([
        	'name' => $file->getClientOriginalName(),
	        'slideshow_id' => $id,
	        'path' => $fileName,
	        'size' => $file->getSize(),
	        'type' => $file->getClientMimeType()
	 ]);
    }
    return response()->json(['message' => 'Files uploaded successfully']);
});

Route::get('/slide', function (){
    $slides = Slideshow::with('files')->orderBy('id', 'desc')->get();
    return response()->json($slides);
});

Route::get('/slide/{id}', function (string $slideId){
    $slide = Slideshow::with('files')->findOrFail($slideId);
    return response()->json($slide);
});


Route::delete('/slide/{id}', function (string $slideId){
    $slide = Slideshow::with('files')->findOrFail($slideId);

    $slide->files->each(function ($file){
        Storage::disk('public')->delete($file->path);
        $file->delete();
    });

    $slide->delete();

    return response()->json(['message' => 'Slide deleted successfully']);
});

Route::delete('/file/{id}', function (string $fileId){
    $file = File::findOrFail($fileId);
    Storage::disk('public')->delete($file->path);
    $file->delete();
    return response()->json(['message' => 'File deleted successfully']);
});
