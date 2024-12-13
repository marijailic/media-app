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

    public function testStoreMediaAlbum(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $files = [
            UploadedFile::fake()->image('image1.jpg'),
            UploadedFile::fake()->image('image2.png'),
        ];

        $response = $this->post(route('media-album.store'), [
            'files' => $files
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('media_albums', [
            'user_id' => $user->id,
        ]);
    }

    public function testShowMediaAlbum(): void
    {
        Storage::fake('public');

        $mediaAlbum = MediaAlbum::factory()->for(User::factory())->create();

        for ($i = 0; $i < 20; $i++) {
            $mediaAlbum
                ->addMedia(UploadedFile::fake()->image("image{$i}.jpg"))
                ->toMediaCollection();
        }

        $response = $this->getJson(route('media-album.show', $mediaAlbum))
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

        $this->assertCount(15, $response['data']);

        $page2_response = $this->getJson(route('media-album.show', [$mediaAlbum, 'page' => 2]));
        $this->assertCount(5, $page2_response['data']);
    }
}
