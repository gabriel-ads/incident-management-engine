<?php

namespace Database\Seeders;

use App\Models\Incident;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IncidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Incident::create([
            'name' => 'LGPD CPF',
            'evidence' => 'Supostamente após o login, o cpf aparenta estar indo para diferentes endpoints',
            'criticality' => '1',
            'host' => 'serasa.com.br',
            'user_id' => 5
        ]);
    }
}
