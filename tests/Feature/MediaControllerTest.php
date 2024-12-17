<?php

namespace Tests\Feature;

use App\Models\MediaAlbum;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public function testDestroyMedia(): void
    {
        Storage::fake('public');

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

        $this->deleteJson(route('media.destroy', $firstMedia->id))
            ->assertOk();

        $this->assertSoftDeleted('media', ['id' => $firstMedia->id]);
        $this->assertNull($mediaAlbum->media()->find($firstMedia->id));

        $this->assertDatabaseHas('media', ['id' => $secondMedia->id]);
        $this->assertDatabaseHas('media_albums', ['id' => $mediaAlbum->id]);
    }
}
