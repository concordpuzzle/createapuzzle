<!-- resources/views/cp_image_generation/index.blade.php -->

@extends('layouts.app')

@livewireStyles
@livewireScripts

@livewire('navigation-menu')


@section('content')
<div class="container">
    <h1>Generate Your Custom Image</h1>
    <form method="POST" action="{{ route('cp_image_generation.store') }}">
        @csrf
        <div class="form-group">
            <label for="prompt">Prompt</label>
            <input type="text" class="form-control" id="prompt" name="prompt" placeholder="Enter your image prompt" required>
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
