@extends('layouts.app')

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
    .card-link {
        text-decoration: none;
        color: inherit;
    }
</style>

<div class="container text-center my-5">
    <h1 style="font-size:22px!important;" class="display-4 mb-4 radio-canada-big">Community Made Puzzles</h1>
    <div class="row">
        @foreach($publishedProducts as $product)
            <div class="col-md-3 mb-4">
                <a href="{{ $product->product_url }}" class="card-link" target="_blank">
                    <div class="card h-100 shadow-sm">
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
                            <div class="like-count">
                                Click to view this puzzle
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
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
@endsection
