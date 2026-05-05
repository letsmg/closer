<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\Country;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        $country = Country::where('code', 'BR')->first();

        if (!$country) {
            $this->command->error('Brazil not found. Run CountrySeeder first.');
            return;
        }

        $states = [
            ['name' => 'Acre', 'uf' => 'AC'],
            ['name' => 'Alagoas', 'uf' => 'AL'],
            ['name' => 'Amapá', 'uf' => 'AP'],
            ['name' => 'Amazonas', 'uf' => 'AM'],
            ['name' => 'Bahia', 'uf' => 'BA'],
            ['name' => 'Ceará', 'uf' => 'CE'],
            ['name' => 'Distrito Federal', 'uf' => 'DF'],
            ['name' => 'Espírito Santo', 'uf' => 'ES'],
            ['name' => 'Goiás', 'uf' => 'GO'],
            ['name' => 'Maranhão', 'uf' => 'MA'],
            ['name' => 'Mato Grosso', 'uf' => 'MT'],
            ['name' => 'Mato Grosso do Sul', 'uf' => 'MS'],
            ['name' => 'Minas Gerais', 'uf' => 'MG'],
            ['name' => 'Pará', 'uf' => 'PA'],
            ['name' => 'Paraíba', 'uf' => 'PB'],
            ['name' => 'Paraná', 'uf' => 'PR'],
            ['name' => 'Pernambuco', 'uf' => 'PE'],
            ['name' => 'Piauí', 'uf' => 'PI'],
            ['name' => 'Rio de Janeiro', 'uf' => 'RJ'],
            ['name' => 'Rio Grande do Norte', 'uf' => 'RN'],
            ['name' => 'Rio Grande do Sul', 'uf' => 'RS'],
            ['name' => 'Rondônia', 'uf' => 'RO'],
            ['name' => 'Roraima', 'uf' => 'RR'],
            ['name' => 'Santa Catarina', 'uf' => 'SC'],
            ['name' => 'São Paulo', 'uf' => 'SP'],
            ['name' => 'Sergipe', 'uf' => 'SE'],
            ['name' => 'Tocantins', 'uf' => 'TO'],
        ];

        foreach ($states as $state) {
            State::updateOrCreate(
                ['name' => $state['name']],
                [
                    'uf' => $state['uf'],
                    'country_id' => $country->id,
                ]
            );
        }

        $this->command->info('States created successfully.');
    }
}
