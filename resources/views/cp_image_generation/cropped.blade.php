<!-- resources/views/cp_image_generation/cropped.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Your Cropped Image</h1>
    <div class="card mb-4 shadow-sm">
        <img src="{{ Storage::url($image->generated_image) }}" class="card-img-top" alt="Cropped Image">
        <div class="card-body">
            <button class="btn btn-primary" onclick="alert('Purchase functionality not implemented yet')">Purchase</button>
        </div>
    </div>
</div>
@endsection
