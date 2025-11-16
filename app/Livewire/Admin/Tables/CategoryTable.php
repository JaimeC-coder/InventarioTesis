<?php

namespace App\Livewire\Admin\Tables;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class CategoryTable extends PowerGridComponent
{
    public string $tableName = 'category-table-oc8dnv-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Category::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('description', function ($dish): string {
                return '<div title="' . e($dish->description) . '" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">' . e($dish->description) . '</div>';
            })
            ->add('uuid')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Nombre', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Descripción', 'description')
                ->sortable()
                ->searchable(),
            // Column::make('Uuid', 'uuid')
            //     ->sortable()
            //     ->searchable(),
            // Column::make('Created at', 'created_at_formatted', 'created_at')
            //     ->sortable(),
            Column::make('Creado el', 'created_at')
                ->sortable()
                ->searchable(),
            Column::action('Acciónes'),
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit(string $rowId): void
    {
        $this->js("Swal.fire({
                        title: '¿Estás seguro?',
                        text: 'Esta acción no se puede deshacer.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });");
    }

    protected function getListeners()
    {
        return [
            'confirmDelete',
            'deleteConfirmed' => 'delete', // Evento que se lanza desde JS cuando el usuario confirma
        ];
    }

    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete(int $params): void
    {
        //   dd($params);
        //  $categoryId = $params['categoryId'] ?? null;
        // Emitimos un evento de JS (usaremos JS para mostrar Swal)
        $this->dispatch('swal:confirmDelete', [
            'title' => '¿Estás seguro?',
            'text' => 'Esta acción no se puede deshacer.',
            'icon' => 'warning',
            'confirmButtonText' => 'Sí, eliminar',
            'cancelButtonText' => 'Cancelar',
            'categoryId' => $params,
        ]);
    }

    public function delete($categoryId): void
    {
        $category = Category::find($categoryId);
        if ($category) {
            $category->delete();
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
            $this->dispatch('swal:success', [
                'title' => 'Eliminado',
                'text' => 'La categoría se eliminó correctamente.',
                'icon' => 'success',
            ]);
        }
    }

    public function actions(Category $category): array
    {
        return [
            Button::add('edit')
                ->slot('Editar')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('editCategory', ['categoryId' => $category->id]),
            Button::add('delete')
                ->slot('delete')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('confirmDelete', ['params' => $category->id]),
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
