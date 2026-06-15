<div class="border-2 p-8 rounded-xl mb-4 border-gray-950">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Gráfica de entradas, salidas y movimientos por mes </h2>
    <x-forms.select label="Seleccione un mes" wire:model.live="mesSeleccionado" dark :options="$arrayMeses" option-label="name"
        option-value="id" :searchable="false" />

    <div>
        <canvas id="ventasChart"></canvas>
    </div>


    @push('scripts')
        <script>
            document.addEventListener("livewire:init", () => {

                const ctx = document.getElementById('ventasChart');

                let chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Entradas , Salidas y Movimientos',
                            data: [],
                            borderWidth: 1,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(201, 203, 207, 0.2)'
                            ],
                            borderColor: [
                                'rgb(255, 99, 132)',
                                'rgb(75, 192, 192)',
                                'rgb(201, 203, 207)'
                            ],
                            borderWidth: 1
                        }]
                    }
                });

                Livewire.on('updateChart', (event) => {

                    chart.data.labels = event.labels;
                    chart.data.datasets[0].data = event.data;
                    chart.data.datasets[0].label = event.text;

                    chart.update();

                });

            });
        </script>
    @endpush


</div>
