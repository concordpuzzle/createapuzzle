<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Concord Puzzle') }}</title>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-ETLGDQVH5T"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-ETLGDQVH5T');
</script>
        <!-- Popular Community Made Puzzles Section -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<div class="mt-16">
    <h2 class="mb-4 arvo-bold" style="font-size: 33px; color: #b71540;">Popular Community Made Puzzles</h2>
    <div class="container">
        <div class="row justify-content-center">
            @foreach($popularProducts as $product)
                <div class="col-md-3 mb-4 d-flex align-items-stretch">
                    <div class="card h-100 shadow-sm" style="width: 18rem;">
                        <img src="{{ Storage::url($product->cropped_image) }}" class="card-img-top" alt="{{ $product->title }}" style="border-radius: 4px;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title radio-canada-big">
                                {{ $product->title }}
                                <button class="like-button {{ $product->likes->contains('user_id', auth()->id()) ? 'liked' : '' }}" onclick="likeProduct({{ $product->id }}, this)">
                                    <i class="fa fa-heart{{ $product->likes->contains('user_id', auth()->id()) ? '' : '-o' }}"></i>
                                </button>
                            </h5>
                            <div class="like-count" style="font-size: 9px; font-style: italic;">
                                <span id="like-count-{{ $product->id }}">{{ $product->likes->count() }}</span> people like this
                            </div>
                            <div class="mt-auto">
                                <a href="{{ $product->product_url }}" class="btn btn-danger btn-block mt-2" target="_blank">View Product</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    function likeProduct(productId, button) {
        $.ajax({
            url: '{{ route("cp_image_generation.like") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    const likeCountElement = document.getElementById('like-count-' + productId);
                    likeCountElement.textContent = response.likes_count;
                    button.classList.add('liked');
                    button.setAttribute('disabled', 'true');
                } else {
                    alert(response.message);
                }
            },
            error: function(error) {
                console.error('Error liking the product:', error);
            }
        });
    }
</script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
            @yield('content')
            </main>
        </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>
