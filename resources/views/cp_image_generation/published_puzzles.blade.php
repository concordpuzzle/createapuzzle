@extends('layouts.app')

@section('content')
<div class="container mx-auto text-center">
    <h1 class="text-4xl my-8">Published Puzzles</h1>
    <div class="flex flex-wrap justify-center">
        @foreach($publishedProducts as $product)
            <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 p-4">
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-4">
                    <img src="{{ Storage::url($product->cropped_image) }}" class="w-full h-64 object-cover rounded-t-lg" alt="{{ $product->title }}">
                    <div class="p-4">
                        <h5 class="text-lg font-bold mb-2">{{ $product->title }}</h5>
                        <p class="text-gray-600 mb-4">{{ $product->description }}</p>
                        <a href="{{ $product->product_url }}" class="inline-block bg-red-700 text-white py-2 px-4 rounded mt-2" target="_blank">View Product</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
