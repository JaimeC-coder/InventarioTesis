<div class="flex flex-col h-[calc(100vh-4rem)] border border-gray-200 rounded-xl overflow-hidden bg-white-50">
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="col-span-1">
            <div class=" gap-4 bg-neutral-primary-soft  max-w-full p-6 border border-default rounded-xl shadow-xs">
                <h5 class="mb-3 text-2xl font-semibold tracking-tight text-heading leading-8">Generar Reporte</h5>
                <p class="text-body mb-6">Seleccione el tipo de reporte que desea generar y las fechas de inicio y fin.
                </p>
                <x-forms.select name="report_type" :options="$reports"
                    label="Seleccione el tipo de reporte que desea generar" wire:model="reportType" option-label="name" option-value="id" />
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <x-forms.input name="start_date" label="Fecha de inicio" type="date" wire:model="startDate" />
                    <x-forms.input name="end_date" label="Fecha de fin" type="date" wire:model="endDate" />
                </div>


                <div class="flex items-end justify-between mt-6">
                    <span></span>
                    <x-forms.button black right-icon="clipboard" label="Generar reporte" theme="primary"
                        wire:click.prevent="generateReport()" />
                </div>
            </div>
        </div>
        <div class="col-span-1">
            <div class="bg-neutral-primary-soft block max-w-full p-6 border border-default rounded-xl shadow-xs">
                <h5 class="mb-3 text-2xl font-semibold tracking-tight text-heading leading-8">Lista de Reportes
                    realizados</h5>
                <p class="text-body mb-6">Ultimos Reportes generados.</p>
                <div class="w-full max-w-full p-4 bg-neutral-primary-soft border border-default rounded-xl shadow-xs">

                    <div class="flow-root">
                        <ul role="list" class="divide-y divide-default ">

                            @foreach ($listreports as $listreport)
                                <li class="py-4 sm:py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 min-w-0 ms-2">
                                            <p class="font-medium text-heading truncate">
                                                {{ $listreport['name'] }}
                                            </p>
                                            <p class="text-sm text-body truncate">
                                                {{ $listreport['description'] }}
                                            </p>
                                        </div>
                                        <div class="inline-flex items-center font-medium text-heading">

                                                <button type="button" wire:click="exportReport('{{ $listreport['download_link'] }}')"
                                                    class="flex items-center text-sm text-white bg-black hover:bg-brand-strong border rounded-md px-3 py-2.5 "><svg
                                                        class="w-6 h-6" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        fill="currentColor" viewBox="0 0 24 24">
                                                        <path fill-rule="evenodd"
                                                        d="M13 11.15V4a1 1 0 1 0-2 0v7.15L8.78 8.374a1 1 0 1 0-1.56 1.25l4 5a1 1 0 0 0 1.56 0l4-5a1 1 0 1 0-1.56-1.25L13 11.15Z"
                                                        clip-rule="evenodd" />
                                                    <path fill-rule="evenodd"
                                                        d="M9.657 15.874 7.358 13H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2h-2.358l-2.3 2.874a3 3 0 0 1-4.685 0ZM17 16a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H17Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                DESCARGAR</button>

                                        </div>
                                    </div>
                                </li>
                            @endforeach







                        </ul>
                    </div>
                </div>
            </div>






        </div>

    </div>
</div>
