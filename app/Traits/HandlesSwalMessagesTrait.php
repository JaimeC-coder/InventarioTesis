<?php

namespace App\Traits;


/**
 * Trait HandlesSwalMessagesTrait
 * @package App\Traits
 * This trait provides methods to display SweetAlert messages in the application.
 * It includes methods for error, success, warning, and confirmation messages.
 * @property string|null $swal_icon
 * @property string|null $swal_title
 * @property string|null $swal_text
 * @method void errorSwal(string $text, string $title = 'Error al procesar', string $type = 'dispatch')
 * @method void successSwal(string $text, string $title = 'Operación exitosa', string $type = 'dispatch')
 * @method void warningSwal(string $text, string $title = 'Advertencia', string $type = 'dispatch')
 * @method void confirmSwal(string $text, string $onConfirmMethod, string $title = '¿Estás seguro?', array $onConfirmParams = [])
 * @method void fireSwal(string $icon, string $title, string $text, string $type)
 * @method void dispatch(string $event, array $payload)
 */

trait HandlesSwalMessagesTrait
{
    public function errorSwal(string $text, string $title = 'Error al procesar', string $type = 'dispatch'): void
    {
        $this->fireSwal('error', $title, $text, $type);
    }

    public function successSwal(string $text, string $title = 'Operación exitosa', string $type = 'dispatch'): void
    {
        $this->fireSwal('success', $title, $text, $type);
    }

    public function warningSwal(string $text, string $title = 'Advertencia', string $type = 'dispatch'): void
    {
        $this->fireSwal('warning', $title, $text, $type);
    }

    public function confirmSwal(string $text, string $onConfirmMethod, string $title = '¿Estás seguro?', array $onConfirmParams = []): void
    {
        $this->dispatch('swal-confirm', [
            'icon' => 'question',
            'title' => $title,
            'text' => $text,
            'showCancelButton' => true,
            'confirmButtonText' => 'Sí, continuar',
            'cancelButtonText' => 'Cancelar',
            'onConfirmMethod' => $onConfirmMethod,
            'onConfirmParams' => $onConfirmParams,
        ]);
    }

    private function fireSwal(string $icon, string $title, string $text, string $type): void
    {
        $payload = compact('icon', 'title', 'text');

        match ($type) {
            'session' => session()->flash('swal', $payload),
            default => $this->dispatch('swal', $payload),
        };
    }
}
