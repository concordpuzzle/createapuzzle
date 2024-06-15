<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Concord Puzzle - Make a Puzzle with AI</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Styles -->
    <style>
        /* ! tailwindcss v3.2.4 | MIT License | https://tailwindcss.com */
        *,::after,::before {
            box-sizing: border-box;
            border-width: 0;
            border-style: solid;
            border-color: #e5e7eb;
        }
        ::after,::before {
            --tw-content: '';
        }
        html {
            line-height: 1.5;
            -webkit-text-size-adjust: 100%;
            -moz-tab-size: 4;
            tab-size: 4;
            font-family: Figtree, sans-serif;
            font-feature-settings: normal;
        }
        body {
            margin: 0;
            line-height: inherit;
        }
        .carousel-container {
            overflow: hidden;
            position: relative;
            margin-top: 50px;
        }
        .carousel {
            display: flex;
            animation: scroll 30s linear infinite;
        }
        .carousel img {
            border-radius: 4px;
            margin: 0 10px;
        }
        .carousel:hover {
            animation-play-state: paused;
        }
        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(calc(-250px * 7)); }
        }
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
    <script src="https://kit.fontawesome.com/21428d3739.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Arvo:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
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
                                <i class="fa-solid fa-magic" style="color:#fff;font-size:22px;"></i>
                            </div>
                            <h2 class="mt-6 text-xl font-semibold text-gray-900">Make a Puzzle with <u>Generative AI</u></h2>
                            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Generate a custom puzzle using AI. Simply provide a prompt and let the AI create a unique image for your puzzle.
                            </p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="self-center shrink-0 stroke-black-500 w-6 h-6 mx-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                        </svg>
                    </a>

                    <a href="https://make.concordpuzzle.com/puzzle-feed" class="scale-100 p-6 bg-white rounded-lg shadow-none shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div style="background-color:#0c2461" class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full">
                                <i class="fa-solid fa-users" style="color:#fff;font-size:22px;"></i>
                            </div>
                            <h2 class="mt-6 text-xl font-semibold text-gray-900">Browse <u>Community Made</u> Puzzles</h2>
                            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Explore puzzles created by the community. Find inspiration and discover unique designs made by fellow puzzle enthusiasts.
                            </p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="self-center shrink-0 stroke-black-500 w-6 h-6 mx-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                        </svg>
                    </a>

                    <a href="#" class="scale-100 p-6 bg-white rounded-lg shadow-none shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div style="background-color:#0c2461" class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full">
                                <i class="fa-solid fa-camera-retro" style="color:#fff;font-size:22px;"></i>
                            </div>
                            <h2 class="mt-6 text-xl font-semibold text-gray-900">Make a Puzzle with a <u>Photo</u></h2>
                            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Upload your own photo to create a personalized jigsaw puzzle. Perfect for capturing memories in a fun way.
                            </p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="self-center shrink-0 stroke-black-500 w-6 h-6 mx-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                        </svg>
                    </a>

                    <div class="scale-100 p-6 bg-white rounded-lg shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <a href="https://concordpuzzle.com"><img src="https://concordpuzzle.com/wp-content/uploads/2024/06/Create-a-Puzzle-1.png" width="95px;"></a><br />
                            <h2 class="text-xl font-semibold text-gray-900">By Concord Puzzle</h2>
                            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                We offer customizable jigsaw puzzles in various piece counts and styles, including 12-piece and 500-piece options. Based in Massachusetts, our focus is on customer satisfaction, aiming to be a one-stop shop for puzzle enthusiasts by allowing personalization of puzzle designs. You can shop with us on Etsy and Instagram, with flat rate shipping available.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mt-5">
                <div class="carousel-container">
                    <div class="carousel">
                        @foreach($publishedProducts as $product)
                            <a href="{{ $product->product_url }}">
                                <img src="{{ Storage::url($product->cropped_image) }}" alt="{{ $product->title }}" width="250px">
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex justify-center mt-16 px-0 sm:items-center">
                <div class="text-center text-sm sm:text-left">
                    <center>&copy; Concord Puzzle<br>A Massachusetts Puzzle Company e.2023</center>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
