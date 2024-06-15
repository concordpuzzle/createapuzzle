@extends('layouts.app')

@section('content')
<!-- Include Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Radio+Canada+Big:wght@400&display=swap" rel="stylesheet">

<!-- Include Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<style>
    .radio-canada-big {
        font-family: "Radio Canada Big", sans-serif;
        font-weight: 400;
    }
    .card-title {
        font-size: 20px;
        font-family: "Radio Canada Big", sans-serif;
    }
    .btn-danger {
        font-size: 16px;
        background-color: #b71540;
        border-color: #b71540;
    }
    .btn-danger:hover {
        color: #f5f6fa;
    }
    .empty-message {
        font-size: 18px;
        font-family: "Radio Canada Big", sans-serif;
    }
</style>

<div class="container text-center my-5">
    <h1 class="radio-canada-big mb-4" style="font-size: 33px;">Published Puzzles</h1>
    @if($publishedProducts->isEmpty())
        <p class="empty-message text-muted">No puzzles published yet. <a href="{{ route('cp_image_generation.index') }}">Make your first puzzle here!</a></p>
    @else
        <div class="row justify-content-center">
            @foreach($publishedProducts as $product)
                <div class="col-md-3 mb-4 d-flex justify-content-center">
                    <div class="card shadow-sm" style="width: 18rem;">
                        <img src="{{ Storage::url($product->cropped_image) }}" class="card-img-top" alt="{{ $product->title }}" style="border-radius: 4px;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->title }}</h5>
                            <a href="{{ $product->product_url }}" class="btn btn-danger">View Product</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
