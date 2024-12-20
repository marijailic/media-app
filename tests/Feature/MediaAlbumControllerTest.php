<?php

namespace Tests\Feature;

use App\Models\MediaAlbum;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaAlbumControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public function testGetThumbnails(): void
    {
        Storage::fake(env('MEDIA_DISK'));

        $user = User::factory()->create();
        $this->actingAs($user);

        $firstMediaAlbum = MediaAlbum::factory()->create([
            'id' => $this->faker->uuid,
            'user_id' => $user->id
        ]);

        $secondMediaAlbum = MediaAlbum::factory()->create([
            'id' => $this->faker->uuid,
            'user_id' => $user->id
        ]);

        $firstMediaName = $this->faker->word;
        $firstMediaAlbum->addMedia(
            UploadedFile::fake()->image($firstMediaName . '.jpg')
        )->toMediaCollection();

        $secondMediaName = $this->faker->word;
        $secondMediaAlbum->addMedia(
            UploadedFile::fake()->image($secondMediaName . '.jpg')
        )->toMediaCollection();

        $response = $this->getJson(route('media-album.thumbnails', [
            'album_ids' => [$firstMediaAlbum->id, $secondMediaAlbum->id],
        ]))->assertOk();

        $responseAlbumIds = collect($response->json('data'))->pluck('id')->all();
        $this->assertContains($firstMediaAlbum->id, $responseAlbumIds);
        $this->assertContains($secondMediaAlbum->id, $responseAlbumIds);

        $this->assertStringContainsString($firstMediaName . '-thumb.jpg', $response->json('data.0.thumb_url'));
        $this->assertStringContainsString($secondMediaName . '-thumb.jpg', $response->json('data.1.thumb_url'));
    }

    public function testUpload(): void
    {
        Storage::fake(env('MEDIA_DISK'));

        $user = User::factory()->create();
        $this->actingAs($user);

        $mediaAlbumId = $this->faker->uuid;

        $fileExtensions = collect(['.jpeg', '.png', '.jpg', '.pdf', '.doc', '.docx', '.xls']);

        $files = $fileExtensions->map(function ($extension) use ($fileExtensions) {
            return UploadedFile::fake()->image('document' . $extension);
        });

        $requestData = [
            'id' => $mediaAlbumId,
            'files' => $files->toArray(),
        ];

        $response = $this->post(route('media-album.upload'), $requestData)
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'thumb_url',
                        'full_url',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('media_albums', [
            'id' => $mediaAlbumId,
        ]);

        foreach ($files as $index => $file) {
            $this->assertStringContainsString(
                $file->getClientOriginalName(),
                $response->json("data.$index.full_url")
            );
        }
    }

    public function testUploadShouldFailIfUserIsNotOwner(): void
    {
        Storage::fake(env('MEDIA_DISK'));

        $albumOwner = User::factory()->create();
        $mediaAlbumId = $this->faker->uuid;

        MediaAlbum::factory()->create([
            'id' => $mediaAlbumId,
            'user_id' => $albumOwner->id
        ]);

        $authorizedUser = User::factory()->create();
        $this->actingAs($authorizedUser);

        $requestData = [
            'id' => $mediaAlbumId,
            'files' => [
                UploadedFile::fake()->image($this->faker->word . '.jpg')
            ]
        ];

        $this->post(route('media-album.upload'), $requestData)
            ->assertStatus(403);
    }

    public function testUploadShouldFailIfFileIsNull(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $requestData = [
            'id' => $this->faker->uuid,
            'files' => [null]
        ];

        $this->post(route('media-album.upload'), $requestData)
            ->assertStatus(302);
    }

    public function testUploadShouldFailIfProvidedFileIsNotFileType(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $requestData = [
            'id' => $this->faker->uuid,
            'files' => [$this->faker->word]
        ];

        $this->post(route('media-album.upload'), $requestData)
            ->assertStatus(302);
    }

    public function testUploadShouldFailIfMimeTypeIsInvalid(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $requestData = [
            'id' => $this->faker->uuid,
            'files' => [
                UploadedFile::fake()->create('image.webp')
            ]
        ];

        $this->post(route('media-album.upload'), $requestData)
            ->assertStatus(302);
    }

    public function testUploadShouldFailIfFileExceedsMaxSize(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $requestData = [
            UploadedFile::fake()->create('large-file.pdf', 3072)
        ];

        $this->post(route('media-album.upload'), $requestData)
            ->assertStatus(302);
    }

    public function testUploadShouldStoreFileInGivenAlbumIfItExists(): void
    {
        Storage::fake(env('MEDIA_DISK'));

        $user = User::factory()->create();
        $this->actingAs($user);

        $mediaAlbumId = $this->faker->uuid;
        MediaAlbum::factory()->for(User::factory())->create([
            'id' => $mediaAlbumId,
            'user_id' => $user->id
        ]);

        $file = UploadedFile::fake()->image($this->faker->word . '.jpeg');

        $requestData = [
            'id' => $mediaAlbumId,
            'files' => [$file]
        ];

        $response = $this->post(route('media-album.upload'), $requestData)
            ->assertOk();

        $this->assertStringContainsString($file->getClientOriginalName(), $response->json('data.0.full_url'));

        $this->assertDatabaseHas('media', [
            'model_id' => $mediaAlbumId,
        ]);
    }

    public function testGetAlbumMedia(): void
    {
        Storage::fake(env('MEDIA_DISK'));

        $user = User::factory()->create();
        $this->actingAs($user);

        $mediaAlbum = MediaAlbum::factory()->for($user)->create();

        for ($i = 0; $i < 20; $i++) {
            $mediaAlbum
                ->addMedia(UploadedFile::fake()->image("image{$i}.jpg"))
                ->toMediaCollection();
        }

        $response = $this->getJson(route('media-album.media', $mediaAlbum))
            ->assertOk()
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

        $page2_response = $this->getJson(route('media-album.media', [$mediaAlbum, 'page' => 2]));
        $this->assertCount(5, $page2_response['data']);
    }

    public function testDelete(): void
    {
        Storage::fake(env('MEDIA_DISK'));

        $user = User::factory()->create();
        $this->actingAs($user);

        $mediaAlbum = MediaAlbum::factory()->create([
            'id' => $this->faker->uuid,
            'user_id' => $user->id
        ]);

        $firstMedia = $mediaAlbum->addMedia(
            UploadedFile::fake()->image($this->faker->word . '.jpg')
        )->toMediaCollection();

        $secondMedia = $mediaAlbum->addMedia(
            UploadedFile::fake()->image($this->faker->word . '.jpg')
        )->toMediaCollection();

        $this->assertDatabaseHas('media', ['id' => $firstMedia->id]);
        $this->assertDatabaseHas('media', ['id' => $secondMedia->id]);
        $this->assertDatabaseHas('media_albums', ['id' => $mediaAlbum->id]);

        $this->deleteJson(route('media-album.delete', $mediaAlbum->id))
            ->assertOk();

        $this->assertSoftDeleted('media', ['id' => $firstMedia->id]);
        $this->assertSoftDeleted('media', ['id' => $secondMedia->id]);
        $this->assertSoftDeleted('media_albums', ['id' => $mediaAlbum->id]);
    }

    public function testDeleteMedia(): void
    {
        Storage::fake(env('MEDIA_DISK'));

        $user = User::factory()->create();
        $this->actingAs($user);

        $mediaAlbum = MediaAlbum::factory()->create([
            'id' => $this->faker->uuid,
            'user_id' => $user->id
        ]);

        $firstMedia = $mediaAlbum->addMedia(
            UploadedFile::fake()->image($this->faker->word . '.jpg')
        )->toMediaCollection();

        $secondMedia = $mediaAlbum->addMedia(
            UploadedFile::fake()->image($this->faker->word . '.jpg')
        )->toMediaCollection();

        $this->assertDatabaseHas('media', ['id' => $firstMedia->id]);
        $this->assertDatabaseHas('media', ['id' => $secondMedia->id]);
        $this->assertDatabaseHas('media_albums', ['id' => $mediaAlbum->id]);

        $this->deleteJson(route('media-album.delete-media', [$mediaAlbum->id, $firstMedia->id]))
            ->assertOk();

        $this->assertSoftDeleted('media', ['id' => $firstMedia->id]);
        $this->assertNull($mediaAlbum->media()->find($firstMedia->id));

        $this->assertDatabaseHas('media', ['id' => $secondMedia->id]);
        $this->assertDatabaseHas('media_albums', ['id' => $mediaAlbum->id]);
    }
}
