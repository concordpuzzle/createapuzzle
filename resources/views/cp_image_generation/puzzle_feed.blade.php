@extends('layouts.public')

@section('content')
<!-- Bootstrap CSS CDN -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Radio+Canada+Big:wght@400&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/21428d3739.js" crossorigin="anonymous"></script>
<style>
    .radio-canada-big {
        font-family: "Radio Canada Big", sans-serif;
        font-weight: 400;
    }
    .like-button {
        background: none;
        border: none;
        color: #fff;
        font-size: 28px;
        cursor: pointer;
    }
    .like-button.liked {
        color: #ff0000;
    }
    .like-count {
        font-weight: bold;
        font-size: 18px;
        margin-right: 10px;
        color: #fff;
    }
    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
    }
    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 0; /* Remove padding to ensure card ends at the bottom of the image */
    }
    .card-img-top {
        transition: transform 0.2s ease;
    }
    .card:hover .card-img-top {
        transform: scale(1.05);
    }
    .card-title {
        font-size: 18px;
        font-weight: bold;
    }
    .card-description {
        font-size: 14px;
        color: #6c757d;
    }
    .overlay {
        position: absolute;
        bottom: 8px;
        left: 10px;
        right: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        pointer-events: none;
    }
    .overlay-content {
        pointer-events: all;
    }
    .make-puzzle-button {
        background-color: #079992;
        color: #fff;
        border: none;
        padding: 10px 20px;
        font-size: 18px;
        border-radius: 5px;
        text-decoration: none;
        margin-top: 20px;
    }
    .make-puzzle-button:hover {
        background-color: #067a6e;
        color: #fff;
        text-decoration: none;
    }
</style>

<div class="container text-center my-5">

<div class="flex justify-center"><br>
                   <div style="color:#0c2461;font-size:33px;" class="arvo-bold">Community Made</div></a>
                </div>   <br> <div class="row">
        @foreach($publishedProducts as $product)
            <div class="col-md-3 mb-4 position-relative">
                <div class="card h-100 shadow-sm">
                    <a href="{{ $product->product_url }}" class="text-decoration-none">
                        <img src="{{ Storage::url($product->cropped_image) }}" class="card-img-top" alt="{{ $product->title }}" style="border-radius: 4px;">
                    </a>
                    <div class="overlay">
                        <div class="like-count" style="pointer-events: all;">
                            <span id="like-count-{{ $product->id }}">{{ $product->likes->count() }}</span> likes
                        </div>
                        <div class="overlay-content">
                            <button class="like-button {{ $product->likes->contains('user_id', auth()->id()) ? 'liked' : '' }}" onclick="toggleLike({{ $product->id }}, this)">
                                <i class="fa fa-heart{{ $product->likes->contains('user_id', auth()->id()) ? '' : '-o' }}"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <br>
    <a href="{{ url('/dashboard') }}" class="make-puzzle-button">Make a Puzzle</a>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleLike(productId, button) {
        @guest
            window.location.href = '{{ route("login") }}';
            return;
        @endguest

        const isLiked = button.classList.contains('liked');
        const url = isLiked ? '{{ route("cp_image_generation.unlike") }}' : '{{ route("cp_image_generation.like") }}';

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    const likeCountElement = document.getElementById('like-count-' + productId);
                    likeCountElement.textContent = response.likes_count;
                    if (isLiked) {
                        button.classList.remove('liked');
                        button.querySelector('i').classList.remove('fa-heart');
                        button.querySelector('i').classList.add('fa-heart-o');
                    } else {
                        button.classList.add('liked');
                        button.querySelector('i').classList.remove('fa-heart-o');
                        button.querySelector('i').classList.add('fa-heart');
                    }
                } else {
                    alert(response.message);
                }
            },
            error: function(error) {
                console.error('Error toggling like:', error);
            }
        });
    }
</script>
@endsection
