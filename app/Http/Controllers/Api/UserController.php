<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Traits\SanitizesOutput;

class UserController extends Controller
{
    use SanitizesOutput;

    /*
    |--------------------------------------------------------------------------
    | REGISTRATION
    |--------------------------------------------------------------------------
    | - Creates user
    | - Creates initial profile
    | - Sends verification email
    | - DOES NOT auto-login
    */
    public function register(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]).{8,}$/'
            ],
            'birth_date' => 'required|date|before:today',
            'gender'      => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erros de validação.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                // 2. User creation
                $user = User::create([
                    'name'      => $request->name,
                    'email'     => strtolower(trim($request->email)),
                    'password'  => Hash::make($request->password),
                    'ultimo_ip' => $request->ip(),
                    'uuid'      => (string) Str::ulid(),
                    'nivel_acesso' => 0, // Consumer default
                ]);

                // 3. Profile creation
                Profile::create([
                    'user_id' => $user->id,
                    'nickname' => $request->nickname ?? $request->name,
                    'birth_date' => $request->birth_date,
                    'gender' => $request->gender,
                    'gender_identity' => $request->gender_identity,
                    'sexual_orientation' => $request->sexual_orientation,
                    'purpose' => $request->purpose ?? 'all',
                    'visibility' => 'public',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Usuário registrado com sucesso',
                    'user' => $user->load('perfil')
                ], 201);
            });
        } catch (\Exception $e) {
            return $this->safeJsonResponse([
                'success' => false,
                'message' => 'Falha no registro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new user (admin only)
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]).{8,}$/'
            ],
            'nivel_acesso' => 'required|integer|min:10|max:12', // Only Staff (levels 10-12)
        ]);

        if ($validator->fails()) {
            return $this->safeJsonResponse([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => strtolower(trim($request->email)),
                    'password' => Hash::make($request->password),
                    'nivel_acesso' => $request->nivel_acesso,
                    'ativo' => true,
                    'uuid' => (string) Str::ulid(),
                ]);

                // Para Staff, o perfil é simplificado
                Profile::create([
                    'user_id' => $user->id,
                    'nickname' => $request->name,
                    'birth_date' => now()->subYears(20), // Placeholder
                    'gender' => 'other',
                    'gender_identity' => 'Not Specified',
                    'sexual_orientation' => 'Not Specified',
                    'purpose' => 'networking',
                    'visibility' => 'hidden', // Staff não aparece no feed
                ]);

                return $this->safeJsonResponse([
                    'success' => true,
                    'message' => 'Usuário do staff criado com sucesso.',
                    'user' => $user->load('perfil')
                ], 201);
            });
        } catch (\Exception $e) {
            return $this->safeJsonResponse([
                'success' => false,
                'message' => 'Erro ao criar usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | USER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    /**
     * Get all users (admin only)
     */
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', User::class);
            
            $users = User::with('perfil')
                ->withCount(['fotos', 'reportsReceived as reports_count'])
                ->when($request->search, function ($query, $search) {
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->when($request->filter === 'staff', function ($query) {
                    $query->where('nivel_acesso', '>=', 10);
                })
                ->when($request->filter === 'regular', function ($query) {
                    $query->where('nivel_acesso', '<', 10);
                })
                ->when($request->filter === 'reported', function ($query) {
                    $query->has('reportsReceived');
                })
                ->when($request->level, function ($query, $level) {
                    $query->where('nivel_acesso', $level);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return $this->safeJsonResponse($users->toArray());
        } catch (\Exception $e) {
            return $this->safeJsonResponse([
                'success' => false,
                'message' => 'Error fetching users: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get user details
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        
        $user->load(['perfil', 'perfil.photos', 'perfil.hobbies']);
        
        return $this->safeJsonResponse($user->toArray());
    }

    /**
     * Update user (admin only)
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'nivel_acesso' => 'sometimes|integer|min:0|max:12',
            'ativo' => 'sometimes|boolean',
            'profile.nickname' => 'sometimes|string|max:255',
            'profile.gender' => 'sometimes|string|max:20',
            'profile.biography' => 'sometimes|string|nullable'
        ]);

        // Atualiza dados da conta
        $user->update($request->only(['name', 'email', 'nivel_acesso', 'ativo']));

        // Atualiza dados do perfil se enviados
        if ($request->has('profile')) {
            $user->perfil()->update($request->input('profile'));
        }
        
        return $this->safeJsonResponse($user->load('perfil')->toArray());
    }

    /**
     * Reset user password
     */
    public function resetPassword(User $user)
    {
        $this->authorize('update', $user);
        
        $temporaryPassword = Str::random(10);
        
        $user->update([
            'password' => Hash::make($temporaryPassword)
        ]);
        
        return response()->json([
            'message' => 'Password reset successfully',
            'temporary_password' => $temporaryPassword
        ]);
    }

    /**
     * Delete user (admin only)
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        $user->delete();
        
        return response()->json(['message' => 'User deleted successfully']);
    }
}
