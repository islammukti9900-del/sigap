<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::withCount('incidents')->orderBy('nama')->get();

            return response()->json([
                'success' => true,
                'Message' => 'Berhasil mengambil data kategori',
                'data' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal mengambil data kategori',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:categories,nama'],
            'icon' => ['nullable', 'string', 'max:30'],
            'warna' => ['nullable', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        try {
            $category = Category::create($data);

            return response()->json([
                'success' => true,
                'Message' => 'Kategori berhasil dibuat',
                'data' => $category,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal membuat kategori',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama' => ['sometimes', 'required', 'string', 'max:100', "unique:categories,nama,{$id}"],
            'icon' => ['nullable', 'string', 'max:30'],
            'warna' => ['nullable', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        try {
            $category = Category::findOrFail($id);
            $category->update($data);

            return response()->json([
                'success' => true,
                'Message' => 'Kategori berhasil diperbarui',
                'data' => $category->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal memperbarui kategori',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            if ($category->incidents()->exists()) {
                return response()->json([
                    'success' => false,
                    'Message' => 'Kategori tidak bisa dihapus karena masih dipakai insiden',
                ], 422);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'Message' => 'Kategori berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal menghapus kategori',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }
}
