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
//    use DatabaseTransactions;

    public function testStoreMediaAlbum(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $requestData = [
            'id' => '9db69da1-57c4-48a2-a39c-f7cdd61fb07f',
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

        $response = $this->post(route('media-album.store'), $requestData);
        dd($response);

        $response->assertStatus(200);


        $this->assertDatabaseHas('media_albums', [
//            'user_id' => $user->id,
            'user_id' => '9db6e69d-d62c-49f2-ba27-0020aeac1362',
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
