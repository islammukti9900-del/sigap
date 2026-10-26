<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\LoginThrottle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Registrasi publik — selalu sebagai pelapor.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => User::ROLE_PELAPOR,
            ]);

            return response()->json([
                'success' => true,
                'Message' => 'Register berhasil',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Register gagal',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }

    public function login(Request $request, LoginThrottle $throttle, ActivityLogger $logger)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $ip = $request->ip();

        // Lockout progresif (sama seperti login web).
        if ($throttle->tooManyAttempts($data['email'], $ip)) {
            $target = User::where('email', $data['email'])->first();
            $logger->log('keamanan.lockout', 'Percobaan login API ditolak karena akun dikunci: ' . $data['email'], null, $target?->id);

            return response()->json([
                'success' => false,
                'Message' => $throttle->lockoutMessage(),
            ], 429);
        }

        try {
            $user = User::where('email', $data['email'])->first();

            if (!$user || !Hash::check($data['password'], $user->password)) {
                $attempts = $throttle->recordFailure($data['email'], $ip);
                $logger->log('keamanan.login_gagal', 'Login API gagal untuk email: ' . $data['email'], null, $user?->id);
                if ($attempts >= LoginThrottle::MAX_ATTEMPTS) {
                    $logger->log('keamanan.lockout', 'Akun dikunci ' . LoginThrottle::LOCK_MINUTES . ' menit setelah ' . $attempts . ' login API gagal: ' . $data['email'], null, $user?->id);
                }

                throw new \Exception('Email atau password salah.');
            }

            $throttle->clear($data['email'], $ip);
            $logger->log('keamanan.login', 'Login API berhasil: ' . $user->email, null, $user->id);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'Message' => 'Login berhasil',
                'data' => ['user' => $user, 'token' => $token],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Login gagal',
                'error' => $this->errorMessage($e),
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'Message' => 'Logout berhasil',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Logout gagal',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }
}
