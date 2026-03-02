<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FotoPerfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FotoController extends Controller
{
    /**
     * Upload de nova foto com regras de moderação e subpastas
     */
    public function upload(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $user = $request->user();
        $totalFotos = $user->fotos()->count();

        if ($totalFotos >= 6) {
            return response()->json(['error' => 'Você já atingiu o limite de 6 fotos.'], 400);
        }

        $file = $request->file('foto');

        // Lógica de Subpastas
        $nomeArquivo = Str::uuid() . '.jpg';
        $subpasta = "fotos/user_{$user->id}";
        $caminhoRelativo = "{$subpasta}/{$nomeArquivo}";
        $diretorioAbsoluto = storage_path("app/public/{$subpasta}");

        if (!file_exists($diretorioAbsoluto)) {
            mkdir($diretorioAbsoluto, 0755, true);
        }

        // Processamento Intervention Image
        $img = Image::make($file->getRealPath());
        $img->fit(800, 1000, function ($constraint) {
            $constraint->upsize();
        })->encode('jpg', 80);

        $img->save("{$diretorioAbsoluto}/{$nomeArquivo}");

        // Salva no banco. Se for a primeira, já vira a principal.
        $foto = $user->fotos()->create([
            'path' => $caminhoRelativo,
            'is_principal' => $totalFotos === 0,
            'ordem' => $totalFotos + 1
        ]);

        return response()->json([
            'message' => 'Foto enviada com sucesso!',
            'url' => asset('storage/' . $caminhoRelativo),
            'id' => $foto->id
        ]);
    }

    /**
     * Remove uma foto e garante que o usuário não fique "sem rosto"
     */
    public function destroy($id, Request $request)
    {
        $user = $request->user();
        $foto = $user->fotos()->find($id);

        if (!$foto) {
            return response()->json(['error' => 'Foto não encontrada.'], 404);
        }

        if ($user->fotos()->count() <= 1) {
            return response()->json([
                'error' => 'Você precisa manter pelo menos uma foto ativa.',
                'code' => 'MIN_PHOTO_REQUIRED'
            ], 422);
        }

        $foiPrincipal = $foto->is_principal;

        if (Storage::disk('public')->exists($foto->path)) {
            Storage::disk('public')->delete($foto->path);
        }

        $foto->delete();

        // Se deletou a principal, define a primeira restante como nova principal
        if ($foiPrincipal) {
            $novaPrincipal = $user->fotos()->orderBy('ordem', 'asc')->first();
            if ($novaPrincipal) {
                $novaPrincipal->update(['is_principal' => true]);
            }
        }

        return response()->json(['message' => 'Foto removida com sucesso.']);
    }

    /**
     * REORDENAR: Recebe um array do Kotlin e sincroniza a galeria
     */
    public function reordenar(Request $request)
    {
        $request->validate([
            'fotos' => 'required|array',
            'fotos.*.id' => 'required|exists:fotos_perfil,id',
            'fotos.*.ordem' => 'required|integer|between:1,6',
            'fotos.*.is_principal' => 'required|boolean'
        ]);

        $user = $request->user();

        // Usamos Transaction para garantir que se um update falhar, nada muda
        DB::transaction(function () use ($request, $user) {
            
            // Primeiro, resetamos todas as fotos do usuário para não serem principais
            // (Evita ter duas fotos marcadas como principal ao mesmo tempo)
            $user->fotos()->update(['is_principal' => false]);

            foreach ($request->fotos as $dadosFoto) {
                // O find assegura que o usuário só edita fotos que pertencem a ele
                $foto = $user->fotos()->find($dadosFoto['id']);
                
                if ($foto) {
                    $foto->update([
                        'ordem' => $dadosFoto['ordem'],
                        'is_principal' => $dadosFoto['is_principal']
                    ]);
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Galeria reordenada com sucesso!'
        ]);
    }
}