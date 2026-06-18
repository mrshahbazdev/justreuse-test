<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - JustReused</title>
    @include('partials.favicon')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    @livewireStyles
    <style>
        * { font-family: 'Inter', sans-serif; }
        body {
            overflow: hidden;
            height: 100vh;
            background: #f8fafc;
        }
        main {
            height: calc(100vh - 80px);
            overflow: hidden;
            padding: 0;
        }
    </style>
</head>
<body class="bg-gray-50">
    @php
        $settings = App\Models\Setting::get_logos();
    @endphp
    @include('layouts.headernew')
	
    <main>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places" defer></script>
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>
