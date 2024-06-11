@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1 style="font-size: 33px; margin-top: 30px; margin-bottom: 30px;">Published Puzzles</h1>
    <div class="row justify-content-center">
        @foreach($publishedProducts as $product)
            <div class="col-md-4 d-flex justify-content-center mb-4">
                <div class="card shadow-sm">
                    <img src="{{ Storage::url($product->cropped_image) }}" class="card-img-top" alt="{{ $product->title }}" style="border-radius: 4px;">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $product->title }}</h5>
                        <p class="card-text">{{ $product->description }}</p>
                        <a href="{{ $product->product_url }}" class="btn btn-primary" target="_blank">View Product</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
