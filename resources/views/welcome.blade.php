{{--
    REGULAR MODE - Production/Conservative Design
    
    This is the stable, client-ready landing page.
    Clean design that "masa" (general audience) easily understands.
    
    For experimental/creative designs, see landing.blade.php
    Route: http://127.0.0.1:8000/
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('darkMode', false) ? 'dark' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AgwaSuri - Water Quality Monitoring</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body
    class="relative font-sans antialiased transition-colors duration-300 bg-fixed bg-center bg-no-repeat bg-cover dark:bg-gray-900"
    style="background-image: linear-gradient(rgba(27, 101, 27, 0.2), rgba(27, 101, 27, 0.2)), url('{{ asset('assets/hidden_forest_lake.png') }}');">

    <!-- Dark mode overlay -->
    <div class="fixed inset-0 transition-opacity duration-300 bg-black pointer-events-none dark:opacity-50 opacity-0"></div>

    <div class="relative z-10">
        <livewire:welcome.navigation />

        <div class="relative flex items-center justify-center min-h-screen px-4 mx-auto max-w-screen-xl lg:px-8">
            <div class="text-center max-w-4xl mx-auto py-20">

                <h1 class="text-4xl font-normal text-white font-serif sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl drop-shadow-lg">
                    Monitor

                    <strong class="block font-normal">Water Resources. </strong>
                </h1>

                <p class="max-w-2xl mx-auto mt-6 text-white text-lg sm:text-xl/relaxed drop-shadow-md">
                    Monitor and maintain water quality with Agwasuri, your reliable water quality data monitoring system. Track key parameters like
                    temperature, salinity, dissolved oxygen,
                    and pH levels to ensure a healthier environment.
                </p>

                <div class="flex flex-wrap justify-center gap-4 mt-8">
                    <a href="{{ route('login') }}"
                        class="inline-block px-12 py-3 text-sm font-medium text-white transition-all duration-200 rounded shadow bg-cvsu hover:bg-cvsu-700 focus:outline-none focus:ring-2 focus:ring-cvsu-400 active:bg-cvsu-800 dark:bg-cvsu-600 dark:hover:bg-cvsu-700">
                        Login to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts

    <!-- Dark Mode Toggle Script -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Listen for dark mode toggle event
            Livewire.on('dark-mode-toggled', (event) => {
                if (event.darkMode) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            });
        });
    </script>
</body>

</html>
