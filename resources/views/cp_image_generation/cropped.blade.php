@extends('layouts.app')

<?php use Illuminate\Support\Facades\Storage; ?>

@section('content')
<div class="container text-center">
    <h1 class="page-title">Your New 500 Piece Puzzle!</h1>
    <p>To purchase it, publish to ConcordPuzzle.com with the button below.</p><br>
    <div class="card mb-4 shadow-sm mx-auto" style="max-width: 600px;">
        <img src="{{ Storage::url($image->generated_image) }}" class="card-img-top rounded-image" alt="Cropped Image">
        <div class="card-body">
            <form id="publishPuzzleForm" method="POST" action="{{ route('cp_image_generation.create_product') }}" target="_blank">
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

<script>
    document.getElementById('publishPuzzleForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Open the form action in a new tab
        var form = event.target;
        var newTab = window.open(form.action, '_blank');
        newTab.focus();

        // Set a timer for 10 seconds before refreshing the current tab
        setTimeout(function() {
            window.location.href = '{{ route('dashboard') }}';
        }, 10000);
    });
</script>
@endsection
