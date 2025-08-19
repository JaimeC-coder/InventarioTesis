<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = Category::orderBy('id', 'desc')->get();
        return view('admin.categories.index', ['categories' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $category = Category::create($request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ]));
        session()->flash('swal', [
            'title' => 'Exitoso',
            'text' => 'La categoría se ha creado correctamente.',
            'icon' => 'success',
        ]);

        return redirect()->route('admin.categories.edit', $category);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): void
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', ['category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): \Illuminate\Http\RedirectResponse
    {
        $category->update($request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]));
        session()->flash('swal', [
            'title' => 'Exitoso',
            'text' => 'La categoría se ha actualizado correctamente.',
            'icon' => 'success',
        ]);

        return redirect()->route('admin.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): \Illuminate\Http\RedirectResponse
    {
        if ($category->products()->exists()) {
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'No se puede eliminar la categoría ,tiene productos asociados.',
                'icon' => 'error',
            ]);
            return redirect()->route('admin.categories.index');
        }

        $category->delete();
        session()->flash('swal', [
            'title' => 'Exitoso',
            'text' => 'La categoría se ha eliminado correctamente.',
            'icon' => 'success',
        ]);

        return redirect()->route('admin.categories.index');
    }
}
