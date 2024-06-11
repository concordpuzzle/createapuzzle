@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1>Your Cropped Image</h1>
    <div class="card mb-4 shadow-sm mx-auto" style="max-width: 600px;">
        <img src="{{ Storage::url($image->generated_image) }}" class="card-img-top" alt="Cropped Image">
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
                <button type="submit" class="btn btn-primary">Create Puzzle</button>
            </form>
        </div>
    </div>
</div>

<style>
    .sleek-input {
        margin-top: 15px;
        border: none;
        border-radius: 3px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection
