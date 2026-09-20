<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Traits\HandlesSwalMessagesTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    use HandlesSwalMessagesTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Category::class);

        return view('admin.categories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Category::class);

        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $categoryRequest): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('create', Category::class);
        try {
            Category::create($categoryRequest->validated());
            $this->successSwal('Categoría registrada correctamente.', type: 'session');

            return redirect()->route('admin.categories.index');
        } catch (\Exception $exception) {
            $this->errorSwal('Hubo un problema al crear la categoría.', type: 'session');
            Log::error('Error al crear categoría: '.$exception->getMessage());

            return redirect()->back();
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
        $this->authorize('update', $category);

        return view('admin.categories.edit', ['category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $categoryRequest, Category $category): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $category);
        try {
            $category->update($categoryRequest->validated());
            $this->successSwal('Categoría actualizada correctamente.', type: 'session');

            return redirect()->route('admin.categories.index');
        } catch (\Exception $exception) {
            $this->errorSwal('Hubo un problema al actualizar la categoría.', type: 'session');
            Log::error('Error al actualizar categoría: '.$exception->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('delete', $category);
        if ($category->products()->exists()) {
            $this->errorSwal('No se puede eliminar la categoría ,tiene productos asociados.', type: 'session');

            return redirect()->back();
        }

        $category->delete();
        $this->successSwal('Categoría eliminada correctamente.', type: 'session');

        return redirect()->back();
    }
}
