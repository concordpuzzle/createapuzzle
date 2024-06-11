@extends('layouts.app')
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

@section('content')
<div class="container text-center my-5">
    <h1 class="mb-4" style="font-size: 33px;">Published Puzzles</h1>
    <div class="row justify-content-center">
        @foreach($publishedProducts as $product)
            <div class="col-md-4 mb-4 d-flex justify-content-center">
                <div class="card shadow-sm" style="width: 18rem;">
                    <img src="{{ Storage::url($product->cropped_image) }}" class="card-img-top" alt="{{ $product->title }}" style="border-radius: 4px;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->title }}</h5>
                        <p class="card-text">{{ $product->description }}</p>
                        <a href="{{ $product->product_url }}" class="btn btn-danger" style="background-color: #b71540; border-color: #b71540;">View Product</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
