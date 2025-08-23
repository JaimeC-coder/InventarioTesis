<div>
    <form wire:submit='save'>
        <div class="grid grid-cols-4 gap-4">
            <div class="md-4">
                <label for="supplier_id" class="text-gray-700 dark:text-white">Tipo de Comprobante</label>
                <select name="voucher_type" id="voucher_type" wire:model="voucher_type" class="form-select block w-full mt-1">
                    <option value="">Seleccione Tipo de Comprobante</option>
                    <option value="1" @if($voucher_type === 1) selected @endif>Factura</option>
                    <option value="2" @if($voucher_type === 2) selected @endif>Boleta</option>
                </select>
            </div>
            <div class="md-4">
                <label for="serie" class="text-gray-700 dark:text-white">Serie</label>
                <input type="text" name="serie" id="serie" wire:model="serie" class="form-input block w-full mt-1 disabled:opacity-50" placeholder="Serie"  disabled="true">
            </div>
            <div class="md-4">
                <label for="correlativo" class="text-gray-700 dark:text-white">Correlativo</label>
                <input type="text" name="correlativo" id="correlativo" wire:model="correlativo" class="form-input block w-full mt-1" placeholder="Correlativo">
            </div>
            <div class="md-4">
                <label for="date" class="text-gray-700 dark:text-white">Fecha</label>
                <input type="date" name="date" id="date" wire:model="date" class="form-input block w-full mt-1">
            </div>
        </div>
    </form>
</div>
