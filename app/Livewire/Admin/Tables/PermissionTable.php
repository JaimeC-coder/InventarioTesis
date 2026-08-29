<?php

namespace App\Livewire\Admin\Tables;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use Spatie\Permission\Models\Permission;

final class PermissionTable extends PowerGridComponent
{
    public string $tableName = 'permission-table-wixau9-table';

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
        return Permission::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('description', fn(Permission $permission) => strtolower(e($permission->description)));
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Descripcion', 'description')
                ->sortable()
                ->searchable(),
        ];
    }
}
