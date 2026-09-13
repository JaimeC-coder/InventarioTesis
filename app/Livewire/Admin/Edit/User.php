<?php

namespace App\Livewire\Admin\Edit;

use App\Models\Employee;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class User extends Component
{
    public ModelsUser $user;

    public Employee $employee;

    public string $name;

    public string $lastname;

    public string $email;

    public string $password;

    public string $document;

    public string $phone;

    public string $address;

    public string $fechaNacimiento;

    public string $user_id;

    public int $role_id;

    public function mount(ModelsUser $modelsUser): void
    {
        $this->user = $modelsUser;
        $this->employee = $modelsUser->employee ?? new Employee();
        $this->email = $modelsUser->email;
        $this->name = $modelsUser->name;
        $this->role_id = $modelsUser->roles->first()->id ?? 0;
        $this->document = $modelsUser->employee->document ?? '';
        $this->phone = $modelsUser->employee->identity ?? '';
        $this->address = $modelsUser->employee->address ?? '';
        $this->phone = $modelsUser->employee->phone ?? '';
        $this->fechaNacimiento = $modelsUser->employee->fechaNacimiento ?? '';
    }

    public function limpiar(): void
    {
        $this->reset(['name', 'email', 'document', 'phone', 'address', 'fechaNacimiento']);
        $this->name = $this->user->name;
        // $this->lastname = $this->user->lastname;
        $this->email = $this->user->email;
        $this->role_id = $this->user->roles->first()->id ?? 0;
        $this->document = $this->employee->document ?? '';
        $this->phone = $this->employee->phone ?? '';
        $this->address = $this->employee->address ?? '';
        $this->fechaNacimiento = $this->employee->fechaNacimiento ?? '';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function updated(string $property): void
    {
        $this->resetErrorBag($property);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'password' => 'required|string|min:8',
            'document' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'fechaNacimiento' => 'required|date',
            'role_id' => 'required|exists:roles,id',
        ]);
        DB::beginTransaction();
        try {
            $this->user->update([
                'name' => $this->name . ' ' . $this->lastname,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
            //REMOVEMOS LOS ROLES ANTERIORES Y ASIGNAMOS EL NUEVO ROL
            $this->user->roles()->detach();
            $this->user->syncRoles([$this->role_id]);
            $this->user->employee()->updateOrCreate(
                [],
                [
                    'document' => $this->document,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'fechaNacimiento' => $this->fechaNacimiento,
                ]
            );
            DB::commit();
            session()->flash('message', 'Unidad actualizada exitosamente.');
            // Reset the form fields
            return redirect()->route('admin.users.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error al crear unidad: ' . $exception->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al actualizar la unidad.',
            ]);
        }
        return null;
    }

    // $this->category_uuid = $product->category->uuid ?? null;
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.edit.user');
    }
}
