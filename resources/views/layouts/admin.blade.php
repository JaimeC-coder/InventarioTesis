@props(['title' => config('app.name', 'Laravel')])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Aplica el tema antes de pintar la página para evitar el parpadeo de tema incorrecto --}}
    <script>
        (function () {
            const theme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', theme === 'dark' || (!theme && prefersDark));
        })();
    </script>

    <title>
        {{ $title }}
    </title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- sweet Alert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Scripts -->
    <wireui:scripts />

    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 dark:text-white"
    x-data="{ sidebarOpen: true, darkMode: document.documentElement.classList.contains('dark') }"
    x-init="$watch('darkMode', value => {
        document.documentElement.classList.toggle('dark', value);
        localStorage.setItem('theme', value ? 'dark' : 'light');
    })">


    <x-banner />

    @include('layouts.includes.admin.siderbar')

    @livewire('components.navigation-menu')

    @include('components.admin.structurebody')



    @stack('modals')

    @livewireScripts

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>

        Livewire.on('swal', (data) => {
        console.log('Evento swal recibido:', data);
            const options = data[0];

            Swal.fire(options).then((result) => {
                if (result.isConfirmed && options.onConfirm) {
                    eval(options.onConfirm);
                }
            });
        });
    </script>


    @if (session()->has('swal'))
        <script>
            const swalData = @json(session('swal'));

            Swal.fire({
                title: swalData.title,
                text: swalData.text,
                icon: swalData.icon,
            });

            console.log('1. Evento swal recibido:', data);
            const options = data[0];
            console.log('2. options.onConfirm es:', options.onConfirm);
            Swal.fire(options).then((result) => {
                if (result.isConfirmed && options.onConfirm) {
                    eval(options.onConfirm);
                }
            });
        </script>
    @endif

    @stack('scripts')

</body>

</html>
