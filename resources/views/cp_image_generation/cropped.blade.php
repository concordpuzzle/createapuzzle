@extends('layouts.app')

<?php use Illuminate\Support\Facades\Storage; ?>

@section('content')
<div class="container text-center">
    <h1 class="page-title">Your New 500 Piece Puzzle!</h1>
    <p>To purchase it, publish to ConcordPuzzle.com with the button below.</p><br>
    <div class="card mb-4 shadow-sm mx-auto" style="max-width: 600px;">
        <img src="{{ Storage::url($image->generated_image) }}" class="card-img-top rounded-image" alt="Cropped Image">
        <div class="card-body">
            <form method="POST" action="{{ route('cp_image_generation.create_product') }}">
                @csrf
                <input type="hidden" name="image_id" value="{{ $image->id }}">
                <button type="submit" class="btn create-puzzle-btn">Publish Puzzle</button>
            </form>
        </div>
    </div>
</div>

<style>
    .page-title {
        font-size: 33px;
        margin-top: 30px;
        margin-bottom: 30px;
    }

    .create-puzzle-btn {
        background-color: #b71540;
        color: white;
        border-radius: 3px;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        margin-top: 15px;
        margin-bottom: 15px;
    }

    .create-puzzle-btn:hover {
        background-color: #a21336;
    }

    .rounded-image {
        border-radius: 4px;
    }
</style>
@endsection
