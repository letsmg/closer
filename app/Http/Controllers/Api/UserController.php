<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
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
        // 1. Validation (Add confirmed for security)
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8|confirmed', // Android should send password_confirmation

            // Profile data
            'age'     => 'required|integer',
            'gender'      => 'required|string',
            'country_id'   => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
        ]);

        try {
            return DB::transaction(function () use ($request) {

                // 2. User creation
                $user = User::create([
                    'name'      => $request->name,
                    'email'     => $request->email,
                    'password'  => Hash::make($request->password),
                    'last_ip' => $request->ip(),
                ]);

                // 3. Profile creation
                Profile::create([
                    'user_id' => $user->id,
                    'nickname' => $request->name,
                    'birth_date' => now()->subYears($request->age),
                    'gender' => $request->gender,
                    'gender_identity' => 'cisgender',
                    'sexual_orientation' => 'heterosexual',
                    'purpose' => 'all',
                    'profession' => null,
                    'biography' => null,
                    'smoker' => false,
                    'drinker' => false,
                    'marital_status' => 'single',
                    'country_id' => $request->country_id,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'visibility' => 'public',
                ]);

                return response()->json([
                    'message' => 'User registered successfully',
                    'user' => $user->load('profile')
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Registration failed',
                'message' => $e->getMessage()
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
        $this->authorize('manage-users');
        
        $users = User::with('perfil')
            ->withCount(['fotos'])
            ->leftJoin('reports', 'users.id', '=', 'reports.reported_id')
            ->select('users.*', DB::raw('COUNT(reports.id) as reports_count'))
            ->groupBy('users.id')
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('users.name', 'like', "%{$search}%")
                      ->orWhere('users.email', 'like', "%{$search}%");
                });
            })
            ->when($request->filter === 'staff', function ($query) {
                $query->where('nivel_acesso', '>=', 3);
            })
            ->when($request->filter === 'regular', function ($query) {
                $query->where('nivel_acesso', '<', 3);
            })
            ->when($request->filter === 'reported', function ($query) {
                $query->having('reports_count', '>', 0);
            })
            ->when($request->level, function ($query, $level) {
                $query->where('nivel_acesso', $level);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($users);
    }

    /**
     * Get user details
     */
    public function show(User $user)
    {
        $this->authorize('view-users');
        
        $user->load(['profile', 'profile.photos', 'profile.hobbies']);
        
        return response()->json($user);
    }

    /**
     * Update user (admin only)
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('manage-users');
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'nivel_acesso' => 'sometimes|integer|min:0|max:3',
            'ativo' => 'sometimes|boolean'
        ]);

        $user->update($request->only(['name', 'email', 'nivel_acesso', 'ativo']));
        
        return response()->json($user);
    }

    /**
     * Delete user (admin only)
     */
    public function destroy(User $user)
    {
        $this->authorize('manage-users');
        
        $user->delete();
        
        return response()->json(['message' => 'User deleted successfully']);
    }
}
