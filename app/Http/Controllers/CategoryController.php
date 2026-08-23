<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Traits\HandlesSwalMessagesTrait;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    use HandlesSwalMessagesTrait;

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
    public function store(CategoryRequest $categoryRequest): \Illuminate\Http\RedirectResponse
    {
        try {
            $category = Category::create($categoryRequest->validated());
            $this->successSwal('La categoría se ha creado correctamente.', type: 'session');
            return redirect()->route('admin.categories.index');
        } catch (\Exception $exception) {
            $this->successSwal('Hubo un problema al crear la categoría.', type: 'session');
            return redirect()->route('admin.categories.index');
        }
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
    public function update(CategoryRequest $categoryRequest, Category $category): \Illuminate\Http\RedirectResponse
    {
        try {
            $category->update($categoryRequest->validated());
            $this->successSwal('La categoría se ha actualizado correctamente.', type: 'session');

            return redirect()->route('admin.categories.index');
        } catch (\Exception $exception) {
            $this->successSwal('Hubo un problema al actualizar la categoría.', type: 'session');

            return redirect()->route('admin.categories.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): \Illuminate\Http\RedirectResponse
    {
        if ($category->products()->exists()) {
            $this->successSwal('No se puede eliminar la categoría ,tiene productos asociados.', type: 'session');
            return redirect()->route('admin.categories.index');
        }

        $category->delete();
        $this->successSwal('La categoría se ha eliminado correctamente.', type: 'session');

        return redirect()->route('admin.categories.index');
    }
}
