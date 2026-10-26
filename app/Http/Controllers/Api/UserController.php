<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $users = User::query()
                ->when($request->role, fn ($q, $role) => $q->where('role', $role))
                ->orderBy('name')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'Message' => 'Berhasil mengambil data pengguna',
                'data' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal mengambil data pengguna',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->validated();
            

            $user = User::create($data);

            return response()->json([
                'success' => true,
                'Message' => 'Berhasil membuat akun staff',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal membuat akun staff',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $user = User::query()->findOrFail($id);

            return response()->json([
                'success' => true,
                'Message' => 'Berhasil mengambil data pengguna',
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal mengambil data pengguna',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $data = $request->validated();

            if (empty($data['password'])) {
                unset($data['password']);
            }

            if ($user->id === $request->user()->id && isset($data['role'])) {
                unset($data['role']);
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'Message' => 'Berhasil memperbarui akun staff',
                'data' => $user->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal memperbarui akun staff',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->id === $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'Message' => 'Tidak dapat menghapus akun sendiri',
                ], 422);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'Message' => 'Berhasil menghapus akun staff',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal menghapus akun staff',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }
}
