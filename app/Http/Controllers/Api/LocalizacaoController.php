<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LocalizacaoController extends Controller
{
    // ==========================================================
    // 🔎 BUSCAR CIDADES (com cache + proteção inteligente)
    // ==========================================================
    public function buscarCidades(Request $request)
    {
        $busca  = $request->query('q');
        $idioma = substr($request->header('Accept-Language', 'pt'), 0, 2);

        // ------------------------------------------------------
        // 🚫 Validação mínima
        // ------------------------------------------------------
        if (empty($busca) || strlen($busca) < 3) {
            return response()->json([]);
        }

        // Normaliza busca para chave de cache
        $buscaNormalizada = Str::lower(trim($busca));

        // ======================================================
        // 🛡️ RATE LIMIT POR IP (leve e seguro)
        // ======================================================
        $ipKey = 'buscar-cidades-ip:' . $request->ip();

        if (RateLimiter::tooManyAttempts($ipKey, 120)) {
            return response()->json([
                'message' => 'Muitas requisições. Aguarde um momento.'
            ], 429);
        }

        RateLimiter::hit($ipKey, 60); // janela de 60 segundos

        // ======================================================
        // 🛡️ RATE LIMIT POR USUÁRIO (extra proteção opcional)
        // ======================================================
        if ($request->user()) {

            $userKey = 'buscar-cidades-user:' . $request->user()->id;

            if (RateLimiter::tooManyAttempts($userKey, 60)) {
                return response()->json([
                    'message' => 'Limite temporário atingido.'
                ], 429);
            }

            RateLimiter::hit($userKey, 60);
        }

        // ======================================================
        // 💾 CACHE (evita consultas repetidas)
        // ======================================================
        $cacheKey = "buscar-cidades:{$buscaNormalizada}:{$idioma}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($buscaNormalizada, $idioma) {

            // =============================
            // 1️⃣ BUSCA LOCAL
            // =============================
            $cidadesLocais = Cidade::with('estado')
                ->where('nome', 'LIKE', $buscaNormalizada . '%')
                ->orderByRaw("CASE WHEN LOWER(nome) LIKE ? THEN 1 ELSE 2 END", [$buscaNormalizada . '%'])
                ->orderBy('nome', 'asc')
                ->limit(5)
                ->get();

            if ($cidadesLocais->count() >= 5) {
                return $this->formatarCidadesLocais($cidadesLocais);
            }

            // =============================
            // 2️⃣ BUSCA GEONAMES
            // =============================
            try {

                $response = Http::timeout(5)->get("http://api.geonames.org/searchJSON", [
                    'name_startsWith' => $buscaNormalizada,
                    'maxRows' => 20,
                    'username' => 'nnluiz',
                    'lang' => $idioma,
                    'featureClass' => 'P',
                    'isNameRequired' => 'true',
                    'orderby' => 'relevance'
                ]);

                if ($response->successful()) {

                    $geonames = $response->json()['geonames'] ?? [];

                    $cidadesExternas = collect($geonames)
                        ->map(function ($item) {

                            $estado = $item['adminName1'] ?? '';
                            $pais   = $item['countryCode'] ?? '';

                            return [
                                'id' => null,
                                'nome' => $item['name'] . ($estado ? " - $estado" : "") . " ($pais)",
                                'geoname_id' => $item['geonameId'],
                                'latitude' => $item['lat'] ?? null,
                                'longitude' => $item['lng'] ?? null,
                                'nome_puro' => mb_strtolower($item['name']),
                                'pais_code' => $pais
                            ];
                        })
                        ->sortBy(function ($cidade) use ($buscaNormalizada) {

                            // prioridade:
                            // 1️⃣ nome exato
                            // 2️⃣ começa com
                            // 3️⃣ ordem alfabética

                            if ($cidade['nome_puro'] === $buscaNormalizada) {
                                return 0;
                            }

                            if (str_starts_with($cidade['nome_puro'], $buscaNormalizada)) {
                                return 1;
                            }

                            return 2;
                        })
                        ->values()
                        ->take(5)
                        ->map(function ($cidade) {
                            unset($cidade['nome_puro']);
                            return $cidade;
                        });

                    return $cidadesExternas->toArray();
                }

                return $this->formatarCidadesLocais($cidadesLocais);

            } catch (\Exception $e) {

                // Se GeoNames falhar, retorna apenas locais
                return $this->formatarCidadesLocais($cidadesLocais);
            }
        });
    }

    // ==========================================================
    // 📌 FORMATA CIDADES LOCAIS
    // ==========================================================
    private function formatarCidadesLocais($cidades)
    {
        return $cidades->map(function ($cidade) {

            return [
                'id' => $cidade->id,
                'nome' => $cidade->nome . " - " .
                    ($cidade->estado->uf ?? '') .
                    " (" . ($cidade->pais_code ?? 'BR') . ")",
                'geoname_id' => $cidade->geoname_id,
                'latitude' => $cidade->latitude,
                'longitude' => $cidade->longitude
            ];
        })->toArray();
    }

    // ==========================================================
    // 📍 ATUALIZAR LOCALIZAÇÃO DO USUÁRIO
    // ==========================================================
    public function atualizarLocalizacao(Request $request)
    {
        $request->validate([
            'nome_completo' => 'required|string',
            'geoname_id'    => 'nullable|numeric',
            'cidade_id'     => 'nullable|numeric',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric'
        ]);

        $usuario = $request->user();
        $cidade  = null;

        // ------------------------------------------------------
        // 1️⃣ Cidade já existente no banco
        // ------------------------------------------------------
        if ($request->cidade_id) {
            $cidade = Cidade::find($request->cidade_id);
        }

        // ------------------------------------------------------
        // 2️⃣ Cidade vinda do GeoNames
        // ------------------------------------------------------
        if (!$cidade && $request->geoname_id) {

            $cidade = Cidade::where('geoname_id', $request->geoname_id)->first();

            if (!$cidade) {

                $partes = explode(' - ', $request->nome_completo);

                $cidade = Cidade::create([
                    'geoname_id'  => $request->geoname_id,
                    'nome'        => $partes[0],
                    'display_name'=> $request->nome_completo,
                    'pais_code'   => $this->extrairPais($request->nome_completo),
                    'latitude'    => $request->latitude,
                    'longitude'   => $request->longitude
                ]);
            }
        }

        // ------------------------------------------------------
        // 3️⃣ Atualiza perfil do usuário
        // ------------------------------------------------------
        if ($cidade && $usuario) {

            $usuario->perfil()->update([
                'cidade_id' => $cidade->id
            ]);

            return response()->json([
                'status'  => 'sucesso',
                'cidade'  => $cidade
            ]);
        }

        return response()->json([
            'status'  => 'erro',
            'message' => 'Não foi possível salvar'
        ], 404);
    }

    // ==========================================================
    // 🏳️ Extrai país do texto (ex: "(BR)")
    // ==========================================================
    private function extrairPais($string)
    {
        if (preg_match('/\((.*?)\)/', $string, $matches)) {
            return $matches[1];
        }

        return 'BR';
    }
}