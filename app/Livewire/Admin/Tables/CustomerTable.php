<?php

namespace App\Livewire\Admin\Tables;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CustomerTable extends DataTableComponent
{
    protected $model = Customer::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('created_at', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make('#')
                ->label(function ($row, Column $column): int {
                    return $this->getRowNumber();
                })
                ->sortable(),
            Column::make('Num Doc', 'document_number')
                ->sortable(),
            Column::make('Identity', 'identity.name')
                ->sortable(),
            Column::make('Razon Social', 'name')
                ->sortable(),
            Column::make('Correo', 'email')
                ->sortable(),
            Column::make('Teléfono', 'phone')
                ->sortable(),
            Column::make('Dirección', 'address')
                ->sortable(),
            Column::make('UUID', 'uuid')
                ->sortable()
                ->hideIf(true),
            Column::make('Fecha de creación', 'created_at')
                ->sortable()
                ->searchable(),
            Column::make('Acciones')
                ->label(function ($row, Column $column): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View {
                    // $row aquí es el modelo completo Customer
                    return view('admin.customers.actions', [
                        'customer' => $row, // Pasa el modelo completo
                    ]);
                }),
        ];
    }

    protected function getRowNumber(): int
    {
        static $position = null;
        if ($position === null) {
            $position = (($this->getPage() - 1) * $this->getPerPage()) + 1;
        } else {
            $position++;
        }

        return $position;
    }

    public function builder(): Builder
    {
        return Customer::query()->with(['identity']);
    }
}
