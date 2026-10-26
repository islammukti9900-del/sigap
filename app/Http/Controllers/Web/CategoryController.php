<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('incidents')->orderBy('nama')->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:categories,nama'],
            'icon' => ['nullable', 'string', 'max:30'],
            'warna' => ['nullable', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Category::create($data);

        return back()->with('success', 'Kategori "' . $data['nama'] . '" dibuat.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100', "unique:categories,nama,{$id}"],
            'icon' => ['nullable', 'string', 'max:30'],
            'warna' => ['nullable', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Category::findOrFail($id)->update($data);

        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->incidents()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih dipakai insiden.');
        }

        $category->delete();

        return back()->with('success', 'Kategori dihapus.');
    }
}
