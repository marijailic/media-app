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

    public function testStoreMediaAlbum(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $mediaAlbumId = $this->faker->uuid;

        $requestData = [
            'id' => $mediaAlbumId,
            'files' => [
                UploadedFile::fake()->image('image1.jpeg'),
                UploadedFile::fake()->image('image2.png'),
                UploadedFile::fake()->image('image3.jpg'),
                UploadedFile::fake()->create('document.pdf'),
                UploadedFile::fake()->create('document.doc'),
                UploadedFile::fake()->create('document.docx'),
                UploadedFile::fake()->create('spreadsheet.xls'),
            ]
        ];

        $this->post(route('media-album.store'), $requestData)
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
    }

    public function testStoreMediaAlbumRequiredFileValidationViolation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $requestData = [
            'id' => $this->faker->uuid,
            'files' => [null]
        ];

        $this->post(route('media-album.store'), $requestData)
            ->assertStatus(302);
    }

    public function testStoreMediaAlbumFileValidationViolation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $requestData = [
            'id' => $this->faker->uuid,
            'files' => [$this->faker->word]
        ];

        $this->post(route('media-album.store'), $requestData)
            ->assertStatus(302);
    }

    public function testStoreMediaAlbumFileMimeValidationViolation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $requestData = [
            'id' => $this->faker->uuid,
            'files' => [
                UploadedFile::fake()->create('image.webp')
            ]
        ];

        $this->post(route('media-album.store'), $requestData)
            ->assertStatus(302);
    }

    public function testStoreMediaAlbumMaxFileSizeValidationViolation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $requestData = [
            UploadedFile::fake()->create('large-file.pdf', 3072)
        ];

        $this->post(route('media-album.store'), $requestData)
            ->assertStatus(302);
    }

    public function testStoreMediaAlbumWhenAlbumExists(): void
    {
        Storage::fake('public');

        $mediaAlbumId = $this->faker->uuid;
        MediaAlbum::factory()->for(User::factory())->create(
            ['id' => $mediaAlbumId]
        );

        $file = UploadedFile::fake()->image($this->faker->word . '.jpeg');

        $requestData = [
            'id' => $mediaAlbumId,
            'files' => [$file]
        ];

        $response = $this->post(route('media-album.store'), $requestData)
            ->assertOk();

        $this->assertStringEndsWith($file->getClientOriginalName(), $response->json('data.0.full_url'));

        $this->assertDatabaseHas('media', [
            'model_id' => $mediaAlbumId,
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

        $page2_response = $this->getJson(route('media-album.show', [$mediaAlbum, 'page' => 2]));
        $this->assertCount(5, $page2_response['data']);
    }
}
