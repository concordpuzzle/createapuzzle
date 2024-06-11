@extends('layouts.app')

@section('content')
<div class="container mx-auto text-center my-8">
    <h1 class="text-4xl font-bold mb-8 arvo-bold">Puzzle Feed</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($publishedProducts as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="{{ Storage::url($product->cropped_image) }}" class="w-full h-64 object-cover" alt="{{ $product->title }}" style="border-radius: 4px;">
                <div class="p-4">
                    <h5 class="text-lg font-bold mb-2">{{ $product->title }}</h5>
                    <p class="text-gray-600 mb-4">{{ $product->description }}</p>
                    <a href="{{ $product->product_url }}" class="inline-block bg-red-700 text-white py-2 px-4 rounded" target="_blank">View Product</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
