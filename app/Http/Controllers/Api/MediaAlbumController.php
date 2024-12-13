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
        $validatedData = $request->validated();

        $mediaAlbum = MediaAlbum::firstOrCreate(
            ['id' => $validatedData['id']],
//            ['user_id' => auth()->id()]
            ['user_id' => '9db6e69d-d62c-49f2-ba27-0020aeac1362']
        );

//        collect($validatedData['files'])->each(fn($file) =>
//            $mediaAlbum->addMedia($file)->toMediaCollection()
//        );
//
//        return MediaAlbumResource::collection($mediaFiles);


        $fileAdders = $mediaAlbum
            ->addAllMediaFromRequest()
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection();
            });

        dd($fileAdders);

        return MediaAlbumResource::collection($fileAdders);
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
