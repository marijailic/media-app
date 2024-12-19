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

        $response = $this->post(route('auth.login'), [
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
    }
}
