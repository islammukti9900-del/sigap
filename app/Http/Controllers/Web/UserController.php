<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->role, fn ($q, $role) => $q->where('role', $role))
            ->orderBy('name')
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form', ['user' => new User()]);
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->validated();
            // Akun staff tidak terhubung ke anggota.

            User::create($data);

            return redirect()->route('pengguna.index')
                ->with('success', 'Akun staff berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('users.form', compact('user'));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $data = $request->validated();

            // Password kosong = tidak diubah.
            if (empty($data['password'])) {
                unset($data['password']);
            }

            // Tidak boleh menurunkan role diri sendiri.
            if ($user->id === $request->user()->id && isset($data['role'])) {
                unset($data['role']);
            }

            $user->update($data);

            return redirect()->route('pengguna.index')
                ->with('success', 'Akun staff berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->id === $request->user()->id) {
                throw new \Exception('Tidak dapat menghapus akun sendiri.');
            }

            $user->delete();

            return redirect()->route('pengguna.index')
                ->with('success', 'Akun staff berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
