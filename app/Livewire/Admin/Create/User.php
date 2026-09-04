<?php

namespace App\Livewire\Admin\Create;

use App\Http\Requests\UserRequest;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class User extends Component
{
    public string $name;

    public string $lastname;

    public string $email;

    public string $password;

    public string $role_id;

    public string $document;

    public string $phone;

    public string $address;

    public string $fechaNacimiento;

    public function save()
    {
        $userRequest = new UserRequest();
        $this->validate($userRequest->rulesForAction('POST'), $userRequest->messages());
        DB::beginTransaction();
        try {
            $nameComplete = $this->name . ' ' . $this->lastname;
            $user = ModelsUser::create([
                'name' => $nameComplete,
                'email' => $this->email,
                'password' => bcrypt($this->password),
            ]);
            $user->roles()->attach($this->role_id);
            $user->employee()->create([
                'document' => $this->document,
                'phone' => $this->phone,
                'address' => $this->address,
                'fechaNacimiento' => $this->fechaNacimiento,
            ]);
            DB::commit();
            $this->dispatch('swal', [
                'title' => 'Exitoso',
                'text' => 'La creación del Empleado fue exitosa.',
                'icon' => 'success',
            ]);

            return redirect()->route('admin.customers.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Error al crear el cliente: ' . $exception->getMessage(), [
                'stack' => $exception->getTraceAsString(),
            ]);
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al crear el cliente.',
                'icon' => 'error',
            ]);
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error('Error al crear el cliente: ' . $exception->getMessage(), [
                'stack' => $exception->getTraceAsString(),
            ]);
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al crear el cliente.',
                'icon' => 'error',
            ]);
        }

        return null;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.create.user');
    }
}
