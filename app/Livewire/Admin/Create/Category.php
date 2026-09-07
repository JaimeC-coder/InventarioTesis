<?php

namespace App\Livewire\Admin\Create;

use App\Http\Requests\CategoryRequest;
use App\Models\Category as ModelsCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Category extends Component
{
    public string $name ;

    public string $description;

    public string $codigo;

    public function limpiar(): void
    {
        $this->reset(['name', 'description', 'codigo']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function save()
    {
        $categoryRequest = new CategoryRequest();
        $this->validate($categoryRequest->rulesForAction('POST'), $categoryRequest->messages());
        DB::beginTransaction();
        try {
            ModelsCategory::create([
                'name' => $this->name,
                'description' => $this->description,
                'codigo' => $this->codigo,
            ]);
            DB::commit();
            $this->dispatch('swal', [
                'title' => 'Exitoso',
                'text' => 'La creación de la categoría fue exitosa.',
                'icon' => 'success',
            ]);
            $this->limpiar();

            return redirect()->route('admin.categories.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Error al crear la categoría: ' . $exception->getMessage(), [
                'stack' => $exception->getTraceAsString(),
            ]);
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al crear la categoría.',
                'icon' => 'error',
            ]);
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error('Error al crear la categoría: ' . $exception->getMessage(), [
                'stack' => $exception->getTraceAsString(),
            ]);
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al crear la categoría.',
                'icon' => 'error',
            ]);
        }

        return null;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.create.category');
    }
}
