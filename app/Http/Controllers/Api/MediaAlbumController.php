<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMediaAlbumRequest;
use App\Models\MediaAlbum;
use Illuminate\Http\Request;

class MediaAlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMediaAlbumRequest $request)
    {
////        dd("TEST");
//        $validatedData = $request->validated();
//
//        $mediaAlbum = MediaAlbum::create([
////            'user_id' => auth()->id(),
//            'user_id' => '9db4a253-4e06-4219-9aaa-8f1982f91d6c',
//        ]);
//
//        $uploadedMedia = [];
//
//        if (!empty($validatedData->files)) {
//            foreach ($validatedData->files as $file) {
////                dd($file);
//                $media = $mediaAlbum->addMedia($file)->toMediaCollection();
//                $uploadedMedia[] = [
//                    'id' => $media->id,
//                    'name' => $media->name,
//                    'url' => $media->getUrl(),
//                ];
//            }
//        }
//
//        return response()->json([
//            'message' => 'Album and media uploaded successfully!',
//            'album' => [
//                'id' => $mediaAlbum->id,
//                'name' => $mediaAlbum->name,
//            ],
//            'media' => $uploadedMedia,
//        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
