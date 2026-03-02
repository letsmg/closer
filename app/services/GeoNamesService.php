<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeoNamesService
{
    protected string $username = 'nnluiz';
    //usado no seeder para popular estados e cidades
    public function buscarCidadesPorEstado($uf)
    {
        $response = Http::timeout(10)->get(
            "http://api.geonames.org/searchJSON",
            [
                'adminCode1' => $uf,
                'country' => 'BR',
                'featureClass' => 'P',
                'maxRows' => 30,
                'orderby' => 'population',
                'username' => $this->username
            ]
        );

        if (!$response->successful()) {
            return [];
        }

        return $response->json()['geonames'] ?? [];
    }
}