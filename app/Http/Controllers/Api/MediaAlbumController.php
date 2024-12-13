<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMediaAlbumRequest;
use App\Http\Resources\MediaAlbumResource;
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
        $mediaAlbum = MediaAlbum::firstOrCreate(
            ['id' => $request->validated()['id']],
            ['user_id' => auth()->id()]
        );

        $media = $request->safe()->collect('files')->map(fn($file) =>
            $mediaAlbum->addMedia($file)->toMediaCollection()
        );

        return MediaAlbumResource::collection($media);
    }

    /**
     * Display the specified resource.
     */
    public function show(MediaAlbum $mediaAlbum)
    {
        return MediaAlbumResource::collection(
            $mediaAlbum->media()->paginate()
        );
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
