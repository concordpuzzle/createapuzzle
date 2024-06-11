<!-- resources/views/cp_image_generation/cropped.blade.php -->
@extends('layouts.app')


@section('content')
<div class="container">
    <h1>Your Cropped Image</h1>
    <div class="card mb-4 shadow-sm">
        <img src="{{ Storage::url($image->generated_image) }}" class="card-img-top" alt="Cropped Image">
        <div class="card-body">
            <form method="POST" action="{{ route('cp_image_generation.create_product') }}">
                @csrf
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $image->title) }}" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $image->description) }}</textarea>
                </div>
                <input type="hidden" name="image_id" value="{{ $image->id }}">
                <button type="submit" class="btn btn-primary">Create Puzzle</button>
            </form>
        </div>
    </div>
</div>
@endsection