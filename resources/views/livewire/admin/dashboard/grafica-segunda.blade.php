<div class="border-2 p-8 rounded-xl mb-4 border-gray-950">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Gráfica de Compras, Ventas por año </h2>
    <x-forms.select label="Seleccione un año" wire:model.live="anioSeleccionado" dark :options="$arrayanio" option-label="name"
        option-value="id" :searchable="false" />

    <div wire:ignore style="position: relative; height: 350px; width: 100%;">
        <canvas id="ventasChart2"></canvas>
    </div>


    @push('scripts')
        <script>
            document.addEventListener("livewire:init", () => {

                const ctx = document.getElementById('ventasChart2');

                let chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                                label: 'Ventas',
                                data: [],
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderWidth: 2,
                                tension: 0.3
                            },
                            {
                                label: 'Compras',
                                data: [],
                                borderColor: 'rgb(255, 99, 132)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderWidth: 2,
                                tension: 0.3
                            }
                            // ,
                            // {
                            //     label: 'Diferencia',
                            //     data: [],
                            //     borderColor: 'rgb(201, 203, 207)',
                            //     backgroundColor: 'rgba(201, 203, 207, 0.2)',
                            //     borderWidth: 2,
                            //     borderDash: [5, 5],
                            //     tension: 0.3
                            // }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: ''
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true // o true, según prefieras
                            }
                        }
                    }
                });

                Livewire.on('updateChart2', (event) => {
                    chart.data.labels = event.labels;
                    chart.data.datasets[0].data = event.data.ventas;
                    chart.data.datasets[1].data = event.data.compras;
                    // chart.data.datasets[2].data = event.data.diferencia;

                    chart.options.plugins.title.text = event.text;

                    chart.update();
                });


            });
        </script>
    @endpush


</div>
