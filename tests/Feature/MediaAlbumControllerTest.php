<?php

namespace Tests\Feature;

use App\Models\MediaAlbum;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaAlbumControllerTest extends TestCase
{
    use DatabaseTransactions;
    public function testShowMediaAlbum(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $mediaAlbum = MediaAlbum::factory()->create([
            'user_id' => $user->id
        ]);

        for ($i = 0; $i < 15; $i++) {
            $mediaAlbum
                ->addMedia(UploadedFile::fake()->image("image{$i}.jpg"))
                ->toMediaCollection();
        }

        $response = $this->getJson(route('media-album.show', ['media_album' => $mediaAlbum->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'thumb_url',
                        'full_url',
                    ],
                ],
                'links',
                'meta' => [
                    'per_page',
                    'total'
                ],
            ]);

        $this->assertCount(10, $response['data']);
        self::assertEquals(10, $response->json('meta.per_page'));
    }
}
