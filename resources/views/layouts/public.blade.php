<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Arvo:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
        <style> 
.arvo-regular {
  font-family: "Arvo", serif;
  font-weight: 400;
  font-style: normal;
}

.arvo-bold {
  font-family: "Arvo", serif;
  font-weight: 700;
  font-style: normal;
}

.arvo-regular-italic {
  font-family: "Arvo", serif;
  font-weight: 400;
  font-style: italic;
}

.arvo-bold-italic {
  font-family: "Arvo", serif;
  font-weight: 700;
  font-style: italic;
}

</style>
        <title>Make a Puzzle - Puzzle Feed</title>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-ETLGDQVH5T"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-ETLGDQVH5T');
</script>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

<!-- Page Heading -->
@if (isset($header))
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{ $header }}
        </div>
    </header>
@endif
    </head>
    <body>

        <div class="font-sans text-gray-900 antialiased">
            
        @yield('content')
        </div>

        @livewireScripts
    </body>
</html>

