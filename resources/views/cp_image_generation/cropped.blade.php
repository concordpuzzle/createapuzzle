@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1 class="page-title">Your Cropped Image</h1>
    <div class="card mb-4 shadow-sm mx-auto" style="max-width: 600px;">
        <img src="{{ Storage::url($image->generated_image) }}" class="card-img-top rounded-image" alt="Cropped Image">
        <div class="card-body">
            <form method="POST" action="{{ route('cp_image_generation.create_product') }}">
                @csrf
                <div class="form-group">
                    <input type="text" class="form-control sleek-input" id="title" name="title" placeholder="Title" value="{{ old('title') }}" required>
                </div>
                <div class="form-group">
                    <textarea class="form-control sleek-input" id="description" name="description" rows="3" placeholder="Description" required>{{ old('description') }}</textarea>
                </div>
                <input type="hidden" name="image_id" value="{{ $image->id }}">
                <button type="submit" class="btn create-puzzle-btn">Create Puzzle</button>
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

    .sleek-input {
        margin-top: 15px;
        margin-bottom: 15px;
        border: none;
        border-radius: 3px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
