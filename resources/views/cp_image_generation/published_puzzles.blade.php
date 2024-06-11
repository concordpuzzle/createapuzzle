@extends('layouts.app')

@section('content')
<div class="container mx-auto text-center">
    <h1 class="text-4xl my-8">Published Puzzles</h1>
    <div class="flex flex-wrap justify-center mx-4">
        @foreach($publishedProducts as $product)
            <div class="w-1/4 p-4">
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-4">
                    <img src="{{ Storage::url($product->cropped_image) }}" class="w-full h-64 object-cover" alt="{{ $product->title }}" style="border-radius: 4px;">
                    <div class="p-4">
                        <h5 class="text-lg font-bold mb-2">{{ $product->title }}</h5>
                        <p class="text-gray-600 mb-4">{{ $product->description }}</p>
                        <a href="{{ $product->product_url }}" class="inline-block bg-red-700 text-white py-2 px-4 rounded" target="_blank">View Product</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
