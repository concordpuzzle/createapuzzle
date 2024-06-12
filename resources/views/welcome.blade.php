<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Concord Puzzle | Make a Jigsaw Puzzle with AI</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-ETLGDQVH5T"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-ETLGDQVH5T');
</script>
    <!-- Styles -->
    <style>
        /* Your existing styles here */
        /* Add the new styles here */
        .radio-canada-big {
            font-family: "Radio Canada Big", sans-serif;
            font-weight: 400;
        }
        .btn-danger {
            background-color: #b71540;
            border-color: #b71540;
        }
        .btn-danger:hover {
            background-color: #a21336;
            border-color: #a21336;
        }
        .like-button {
            background: none;
            border: none;
            color: #b71540;
            font-size: 24px;
            cursor: pointer;
        }
        .like-button.liked {
            color: #ff0000;
        }
        .like-count {
            font-style: italic;
            font-size: 9px;
        }
    </style>
    <script src="https://kit.fontawesome.com/21428d3739.js" crossorigin="anonymous"></script>
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
</head>
<body class="antialiased">
    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-center bg-white selection:bg-red-500 selection:text-white">
        @if (Route::has('login'))
            <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                @auth
                    <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="flex justify-center">
                <div style="color:#b71540;font-size:44px;" class="arvo-bold">Make a Puzzle</div>
            </div>

            <div class="mt-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                    <a href="https://make.concordpuzzle.com/dashboard" class="scale-100 p-6 bg-white rounded-lg shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div style="background-color:#0c2461" class="h-16 w-16 flex items-center justify-center rounded-full">
                                <i class="fa-solid fa-camera-retro" style="color:#fff;font-size:22px;"></i>
                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-gray-900">Make a Puzzle with <u>Generative AI</u></h2>

                            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Upload your own photo to create a unique, custom jigsaw puzzle. Choose the number of pieces and other options.
                            </p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="self-center shrink-0 stroke-black-500 w-6 h-6 mx-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                        </svg>
                    </a>

                    <a href="https://make.concordpuzzle.com/puzzle-feed" class="scale-100 p-6 bg-white rounded-lg shadow-none shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div style="background-color:#0c2461" class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full">
                                <i class="fa-solid fa-pencil" style="color:#fff;font-size:22px;"></i>
                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-gray-900">Browse <u>Community Made</u> Puzzles</h2>

                            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Provide a description or prompt to generate a custom illustration. Our AI will create a unique image based on your input, which you can then turn into a personalized jigsaw puzzle by selecting the number of pieces and other options.
                            </p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="self-center shrink-0 stroke-black-500 w-6 h-6 mx-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                        </svg>
                    </a>

                    <a href="#" class="scale-100 p-6 bg-white rounded-lg shadow-none shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div style="background-color:#0c2461" class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full">
                                <i class="fa-solid fa-palette" style="color:#fff;font-size:22px;"></i>
                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-gray-900">Make a Puzzle with a <u>Photo</u></h2>

                            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Upload your own photo to create a unique, custom jigsaw puzzle. Choose the number of pieces and other options.
                            </p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="self-center shrink-0 stroke-black-500 w-6 h-6 mx-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                        </svg>
                    </a>

                    <div class="scale-100 p-6 bg-white rounded-lg shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <img src="https://concordpuzzle.com/wp-content/uploads/2024/06/Create-a-Puzzle-1.png" width="95px;"><br />

                            <h2 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">By Concord Puzzle</h2>

                            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                We offer customizable jigsaw puzzles in various piece counts and styles, including 12-piece and 500-piece options. Based in Massachusetts, our focus is on customer satisfaction, aiming to be a one-stop shop for puzzle enthusiasts by allowing personalization of puzzle designs. You can shop with us on Etsy and Instagram, with flat rate shipping available.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Community Made Puzzles Section -->
            <div class="mt-16">
                <h2 class="mb-4" style="font-size: 33px; color: #b71540;" class="arvo-bold">Popular Community Made Puzzles</h2>
                <div class="row justify-content-center">
                    @foreach($popularProducts as $product)
                        <div class="col-md-3 mb-4 d-flex justify-content-center">
                            <div class="card h-100 shadow-sm" style="width: 18rem;">
                                <img src="{{ Storage::url($product->cropped_image) }}" class="card-img-top" alt="{{ $product->title }}" style="border-radius: 4px;">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title radio-canada-big">
                                        {{ $product->title }}
                                        <button class="like-button {{ $product->likes->contains('user_id', auth()->id()) ? 'liked' : '' }}" onclick="likeProduct({{ $product->id }}, this)">
                                            <i class="fa fa-heart{{ $product->likes->contains('user_id', auth()->id()) ? '' : '-o' }}"></i>
                                        </button>
                                    </h5>
                                    <div class="like-count">
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

            <div class="flex justify-center mt-16 px-0 sm:items-center">
                <div class="text-center text-sm sm:text-left">
                    <center> &copy; Concord Puzzle<br>A Massachusetts Puzzle Company e.2023 </center>
                </div>
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
                        button.querySelector('i').classList.remove('fa-heart-o');
                        button.querySelector('i').classList.add('fa-heart');
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
</body>
</html>
