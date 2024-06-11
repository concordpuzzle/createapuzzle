<div>
    <!-- I begin to speak only when I am certain what I will say is not better left unsaid. - Cato the Younger -->
</div>
<!-- resources/views/staging.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Generate Your Custom Image</h1>
    <form action="{{ route('staging.generate') }}" method="POST">
        @csrf
        <div class="mb-3">
            <input type="text" name="prompt" class="form-control" placeholder="Enter your image prompt" required>
        </div>
        <button type="submit" class="btn btn-primary">Generate Image</button>
    </form>
    <hr>
    <h2>Generated Images</h2>
    <div class="row">
        @foreach($images as $image)
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <img src="{{ Storage::url($image->generated_image) }}" class="card-img-top" alt="{{ $image->prompt }}">
                    <div class="card-body">
                        <p class="card-text">{{ $image->prompt }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

