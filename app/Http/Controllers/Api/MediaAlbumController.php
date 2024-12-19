<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetThumbnailsMediaAlbumRequest;
use App\Http\Requests\Api\UploadMediaAlbumRequest;
use App\Http\Resources\MediaAlbumResource;
use App\Http\Resources\MediaAlbumsResource;
use App\Models\Media;
use App\Models\MediaAlbum;
use Illuminate\Support\Facades\Gate;

class MediaAlbumController extends Controller
{
    public function getThumbnails(GetThumbnailsMediaAlbumRequest $request)
    {
        $mediaAlbums = MediaAlbum::query()
            ->with(['media' => fn ($query) => $query->limit(1)])
            ->whereIn('id', $request->validated('album_ids'))->get();

        $mediaAlbums->each(fn($album) => Gate::authorize('ownsAlbum', $album));

        return MediaAlbumsResource::collection($mediaAlbums);
    }

    public function upload(UploadMediaAlbumRequest $request)
    {
        $mediaAlbum = MediaAlbum::firstOrCreate(
            ['id' => $request->validated('id')],
            ['user_id' => auth()->id()]
        );

        Gate::authorize('ownsAlbum', $mediaAlbum);

        $media = $request->safe()->collect('files')->map(fn($file) =>
        $mediaAlbum->addMedia($file)->toMediaCollection()
        );

        return MediaAlbumResource::collection($media);
    }

    public function getAlbumMedia(MediaAlbum $mediaAlbum)
    {
        Gate::authorize('ownsAlbum', $mediaAlbum);

        return MediaAlbumResource::collection(
            $mediaAlbum->media()->paginate()
        );
    }

    public function delete(MediaAlbum $mediaAlbum)
    {
        Gate::authorize('ownsAlbum', $mediaAlbum);

        $mediaAlbum->media()->delete();
        $mediaAlbum->delete();

        return response()->noContent(200);
    }

    public function deleteMedia(MediaAlbum $mediaAlbum, Media $media)
    {
        Gate::authorize('ownsAlbum', $mediaAlbum);

        $media->delete();

        return response()->noContent(200);
    }
}
