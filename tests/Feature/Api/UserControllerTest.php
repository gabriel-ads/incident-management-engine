<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserControllerTest extends TestCase
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

    public function test_user_pagination_get(): void
    {
        $token = $this->login();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/users');

        $response
            ->assertStatus(200)
            ->assertJsonPath('users.data.0.id', 5);

        $response->assertJson(
            fn(AssertableJson $json) =>
            $json->where('status', true)
                ->missing('password')
                ->etc()
        );

        $response->assertJsonStructure([
            'status',
            'users' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'active',
                    ]
                ],
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ],
        ]);

        $response->assertJson([
            'users' => [
                'current_page' => 1,
                'next_page_url' => url('/api/users?page=2'),
                'per_page' => 2,
                'total' => 5,
                'last_page' => 3,
            ],
        ]);
    }

    public function test_user_pagination_next_page(): void
    {
        $token = $this->login();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/users?page=2');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'users' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'active',
                    ]
                ],
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ],
        ]);

        $response->assertJson([
            'users' => [
                'current_page' => 2,
                'prev_page_url' => url('/api/users?page=1'),
                'next_page_url' => url('/api/users?page=3'),
                'per_page' => 2,
                'total' => 5,
                'last_page' => 3,
            ],
        ]);
    }

    public function test_user_get_with_id()
    {
        $token = $this->login();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/users/2');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'user' => [
                'id',
                'name',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at',
            ]

        ]);
    }

    public function test_user_post()
    {
        $response = $this->post('api/users', ["name" => "Gabriel", "email" => "gabriel.alves@email.com", "password" => "123456a"]);

        $response->assertStatus(201)->assertJsonStructure([
            'status',
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
            "message"
        ]);

        $withoutName = $this->post('api/users', ["email" => "gabriel@email.com", "password" => "123456a"]);
        $withoutEmail = $this->post('api/users', ["name" => "Gabriel", "password" => "123456a"]);
        $withoutPass = $this->post('api/users', ["name" => "Gabriel", "email" => "alves@email.com"]);
        $wrongEmail = $this->post('api/users', ["name" => "Gabriel", "email" => "gabriel", "password" => "123456a"]);
        $passwordWithoutMinimumValue = $this->post('api/users', ["name" => "Gabriel", "email" => "g.alves@email.com", "password" => "1"]);

        $withoutName->assertStatus(422);
        $withoutEmail->assertStatus(422);
        $withoutPass->assertStatus(422);
        $wrongEmail->assertStatus(422);
        $passwordWithoutMinimumValue->assertStatus(422);
    }

    public function test_user_put()
    {
        $token = $this->login();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put('/api/users/2', ["name" => "Gabriel", "email" => "gabriel.alves@email.com", "password" => "123456a"]);

        $response->assertStatus(200)->assertJsonStructure([
            'status',
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
            "message"
        ]);

        $withoutName = $this->put('api/users/2', ["email" => "gabriel.alves@email.com", "password" => "123456a"]);
        $withoutEmail = $this->put('api/users/2', ["name" => "Gabriel", "password" => "123456a"]);
        $withoutPass = $this->put('api/users/2', ["name" => "Gabriel", "email" => "alves@email.com"]);
        $wrongEmail = $this->put('api/users/2', ["name" => "Gabriel", "email" => "gabriel", "password" => "123456a"]);
        $passwordWithoutMinimumValue = $this->put('api/users/2', ["name" => "Gabriel", "email" => "g.alves@email.com", "password" => "1"]);

        $withoutName->assertStatus(200);
        $withoutEmail->assertStatus(200);
        $withoutPass->assertStatus(200);
        $wrongEmail->assertStatus(422);
        $passwordWithoutMinimumValue->assertStatus(422);
    }

    public function test_user_delete()
    {
        $token = $this->login();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/users/2');

        $response->assertStatus(200)->assertJsonStructure([
            'status',
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
            "message"
        ]);
    }

    public function test_user_private_routes_without_token()
    {
        $getPagination = $this->get('/api/users/page=1');
        $getPagination->assertStatus(401);

        $getWithId = $this->get('/api/users/2');
        $getWithId->assertStatus(401);

        $putUser = $this->put('/api/users/2');
        $putUser->assertStatus(401);

        $deleteUser = $this->put('/api/users/2');
        $deleteUser->assertStatus(401);
    }
}
