<?php

namespace Tests\Feature\Api;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class IncidentControllerTest extends TestCase
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

    public function test_incident_pagination_get(): void
    {
        $token = $this->login();
        Incident::factory(10)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/incidents');

        $response
            ->assertStatus(200)
            ->assertJsonPath('incidents.data.0.id', 10);

        $response->assertJson(
            fn(AssertableJson $json) =>
            $json->where('status', true)
                ->etc()
        );

        $response->assertJsonStructure([
            'status',
            'incidents' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'evidence',
                        'criticality',
                        'host',
                        'created_at',
                        'updated_at',
                        'user_id'
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
            'incidents' => [
                'current_page' => 1,
                'next_page_url' => url('/api/incidents?page=2'),
                'per_page' => 5,
                'total' => 10,
                'last_page' => 2,
            ],
        ]);
    }

    public function test_incident_pagination_next_page(): void
    {
        $token = $this->login();
        Incident::factory(10)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/incidents?page=2');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'incidents' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'evidence',
                        'criticality',
                        'host',
                        'created_at',
                        'updated_at',
                        'user_id'
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
            'incidents' => [
                'current_page' => 2,
                'prev_page_url' => url('/api/incidents?page=1'),
                'next_page_url' => null,
                'per_page' => 5,
                'total' => 10,
                'last_page' => 2,
            ],
        ]);
    }

    public function test_incident_post()
    {
        $token = $this->login();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->post(
            'api/incidents',
            [
                "name" => "LGPD CPF",
                "evidence" => "Supostamente após o login, o cpf aparenta estar indo para diferentes endpoints",
                "criticality" => 1,
                "host" => 'serasa.com.br',
                "user_id" => 1
            ]
        );

        $response->assertStatus(202)->assertJsonStructure([
            'status',
            "message"
        ]);

        $withoutName = $this->post(
            'api/incidents',
            [
                "evidence" => "gabriel.alves@email.com",
                "criticality" => 1,
                "host" => 'serasa.com.br',
                "user_id" => 1
            ]
        );
        $withoutEvidence = $this->post('api/incidents', [
            "name" => "LGPD CPF",
            "criticality" => 1,
            "host" => 'serasa.com.br',
            "user_id" => 1
        ]);
        $withoutCriticality = $this->post('api/incidents', [
            "name" => "LGPD CPF",
            "evidence" => "Supostamente após o login, o cpf aparenta estar indo para diferentes endpoints",
            "host" => 'serasa.com.br',
            "user_id" => 1
        ]);
        $withoutHost = $this->post('api/incidents', [
            "name" => "LGPD CPF",
            "evidence" => "Supostamente após o login, o cpf aparenta estar indo para diferentes endpoints",
            "criticality" => 1,
            "user_id" => 1
        ]);
        $evidenceWithoutMinimumValue = $this->post('api/incidents', [
            "name" => "LGPD CPF",
            "evidence" => "Supostamente",
            "criticality" => 1,
            "host" => 'serasa.com.br',
            "user_id" => 1
        ]);

        $withoutName->assertStatus(422);
        $withoutEvidence->assertStatus(422);
        $withoutCriticality->assertStatus(422);
        $withoutHost->assertStatus(422);
        $evidenceWithoutMinimumValue->assertStatus(422);
    }

    public function test_incident_put()
    {
        $token = $this->login();
        Incident::factory(3)->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->put(
            'api/incidents/3',
            [
                "name" => "LGPD CPF",
                "evidence" => "Supostamente após o login, o cpf aparenta estar indo para diferentes endpoints",
                "criticality" => 1,
                "host" => 'serasa.com.br',
                "user_id" => 1
            ]
        );

        $response->assertStatus(202)->assertJsonStructure([
            'status',
            "message"
        ]);

        $withoutName = $this->put(
            'api/incidents/3',
            [
                "evidence" => "gabriel.alves@email.com",
                "criticality" => 1,
                "host" => 'serasa.com.br',
                "user_id" => 1
            ]
        );
        $withoutEvidence = $this->put('api/incidents/3', [
            "name" => "LGPD CPF",
            "criticality" => 1,
            "host" => 'serasa.com.br',
            "user_id" => 1
        ]);
        $withoutCriticality = $this->put('api/incidents/3', [
            "name" => "LGPD CPF",
            "evidence" => "Supostamente após o login, o cpf aparenta estar indo para diferentes endpoints",
            "host" => 'serasa.com.br',
            "user_id" => 1
        ]);
        $withoutHost = $this->put('api/incidents/3', [
            "name" => "LGPD CPF",
            "evidence" => "Supostamente após o login, o cpf aparenta estar indo para diferentes endpoints",
            "criticality" => 1,
            "user_id" => 1
        ]);
        $evidenceWithoutMinimumValue = $this->put('api/incidents/3', [
            "name" => "LGPD CPF",
            "evidence" => "Supostamente",
            "criticality" => 1,
            "host" => 'serasa.com.br',
            "user_id" => 1
        ]);

        $withoutName->assertStatus(202);
        $withoutEvidence->assertStatus(202);
        $withoutCriticality->assertStatus(202);
        $withoutHost->assertStatus(202);
        $evidenceWithoutMinimumValue->assertStatus(422);
    }

    public function test_incident_delete()
    {
        $token = $this->login();
        Incident::factory(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/incidents/3');

        $response->assertStatus(202)->assertJsonStructure([
            'status',
            "message"
        ]);
    }

    public function test_incident_private_routes_without_token()
    {

        $getIncidents = $this->get('/api/incidents');
        $getIncidents->assertStatus(401);

        $postIncidents = $this->post('/api/incidents');
        $postIncidents->assertStatus(401);

        $putIncidents = $this->put('/api/incidents/2');
        $putIncidents->assertStatus(401);

        $deleteIncidents = $this->delete('/api/incidents/2');
        $deleteIncidents->assertStatus(401);
    }
}
