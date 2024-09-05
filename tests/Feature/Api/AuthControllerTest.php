<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function login(): string
    {
        $users =  User::factory(5)->create();

        $login = $this->post(
            '/api/auth/login',
            [
                'email' => $users[0]->email,
                'password' => "password"
            ]
        );

        $token = $login->json('access_token');

        return $token;
    }
    /**
     * A basic feature test example.
     */
    public function test_login(): void
    {
        $users =  User::factory(1)->create();

        $response = $this->post(
            '/api/auth/login',
            [
                'email' => $users[0]->email,
                'password' => "password"
            ]
        );

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "status",
            "access_token",
            "token_type",
            "expires_in"
        ]);
    }

    public function test_refresh_token(): void
    {
        $token = $this->login();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->post(
            '/api/auth/refresh'
        );

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "status",
            "access_token",
            "token_type",
            "expires_in"
        ]);
    }

    public function test_logout(): void
    {
        $token = $this->login();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->post(
            '/api/auth/logout'
        );

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "status",
            "message",
        ]);
    }

    public function test_me(): void
    {
        $token = $this->login();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->post(
            '/api/auth/me'
        );

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'email_verified_at',
            'created_at',
            'updated_at',
        ]);
    }

    public function test_auth_private_routes_without_token(): void
    {

        $refresh = $this->post(
            '/api/auth/refresh'
        );
        $logout = $this->post(
            '/api/auth/logout'
        );
        $me = $this->post(
            '/api/auth/me'
        );

        $refresh->assertStatus(401);
        $logout->assertStatus(401);
        $me->assertStatus(401);
    }
}
