<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IndexMediaAlbumRequest;
use App\Http\Requests\Api\StoreMediaAlbumRequest;
use App\Http\Resources\MediaAlbumResource;
use App\Http\Resources\MediaAlbumsResource;
use App\Models\MediaAlbum;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MediaAlbumController extends Controller
{
    use AuthorizesRequests;

    public function index(IndexMediaAlbumRequest $request)
    {
        $mediaAlbums = MediaAlbum::whereIn('id', $request->validated('album_ids'))->get();

        return MediaAlbumsResource::collection($mediaAlbums);
    }

    public function store(StoreMediaAlbumRequest $request)
    {
        $mediaAlbum = MediaAlbum::firstOrCreate(
            ['id' => $request->validated('id')],
            ['user_id' => auth()->id()]
        );

        $this->authorize('store', $mediaAlbum);

        $media = $request->safe()->collect('files')->map(fn($file) =>
        $mediaAlbum->addMedia($file)->toMediaCollection()
        );

        return MediaAlbumResource::collection($media);
    }

    public function show(MediaAlbum $mediaAlbum)
    {
        return MediaAlbumResource::collection(
            $mediaAlbum->media()->paginate()
        );
    }

    public function destroy(MediaAlbum $mediaAlbum)
    {
        $mediaAlbum->media()->delete();
        $mediaAlbum->delete();

        return response()->noContent(200);
    }
}
