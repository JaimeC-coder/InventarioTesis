<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', theme === 'dark' || (!theme && prefersDark));
        })();
    </script>

    <title>403 · Acceso denegado</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 px-4">
        <div class="max-w-lg w-full text-center">
            <div class="text-8xl mb-2 select-none animate-bounce">🚧</div>

            <div class="text-sm font-semibold tracking-widest text-indigo-500 uppercase mb-2">
                Error 403
            </div>

            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                ¡Por aquí no puedes pasar, muchachón!
            </h1>

            <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                No tienes permisos para entrar a esta sección. Si crees que deberías
                tenerlos, dale un toque al administrador del sistema
                <span class="whitespace-nowrap">(con buena cara 😉).</span>
            </p>

            <div class="flex items-center justify-center gap-3">
                <a href="{{ Auth::check() ? route('admin.dashboard') : url('/') }}"
                    class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-md shadow transition">
                    ← Volver por donde viniste
                </a>
            </div>
        </div>
    </div>
</body>

</html>
