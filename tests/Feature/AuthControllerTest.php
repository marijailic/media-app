<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testLogin(): void
    {
        $password = 'password';
        $user = User::factory()->create([
            'password' => $password
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ])
            ->assertJson([
                'token_type' => 'Bearer'
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    public function testLogout(): void
    {
        $user = User::factory()->create();
        $user->createToken('auth_token')->plainTextToken;

        $this->actingAs($user)->post(route('logout'))
            ->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id
        ]);
    }
}
