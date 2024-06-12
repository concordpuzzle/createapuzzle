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
</style>

<div class="container text-center my-5">
    <h1 class="display-4 mb-4 radio-canada-big">Community Made Puzzles</h1>
    <div class="row">
        @foreach($publishedProducts as $product)
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="{{ Storage::url($product->cropped_image) }}" class="card-img-top" alt="{{ $product->title }}" style="border-radius: 4px;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title radio-canada-big">{{ $product->title }}</h5>
                        <div class="mt-auto">
                            <button class="like-button" onclick="likeProduct({{ $product->id }}, this)">
                                <i class="fa fa-heart"></i>
                            </button>
                            <span id="like-count-{{ $product->id }}">{{ $product->likes_count ?? 0 }}</span>
                            <a href="{{ $product->product_url }}" class="btn btn-danger btn-block mt-2" target="_blank">View Product</a>
                        </div>
                    </div>
                </div>
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
