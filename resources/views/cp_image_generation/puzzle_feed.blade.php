@extends('layouts.app')

@section('content')
<!-- Bootstrap CSS CDN -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<div class="container text-center my-5">
    <h1 class="display-4 mb-4 arvo-bold">Puzzle Feed</h1>
    <div class="row">
        @foreach($publishedProducts as $product)
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="{{ Storage::url($product->cropped_image) }}" class="card-img-top" alt="{{ $product->title }}" style="border-radius: 4px;">
                    <div class="card-body">
                        <h5 class="card-title font-weight-bold">{{ $product->title }}</h5>
                        <p class="card-text">{{ $product->description }}</p>
                        <a href="{{ $product->product_url }}" class="btn btn-primary btn-block" target="_blank">View Product</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
