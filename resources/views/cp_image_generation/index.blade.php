@extends('layouts.app')

@section('content')
<!-- Include Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Radio+Canada+Big:wght@400&family=Arvo:wght@700&display=swap" rel="stylesheet">

<!-- Include Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<style>
    .radio-canada-big {
        font-family: "Radio Canada Big", sans-serif;
        font-weight: 400;
    }
    .arvo-bold {
        font-family: "Arvo", serif;
        font-weight: 700;
        color: #b71540;
    }
    .upscale-button {
        font-size: 12px;
        background-color: #0c2461;
        color: white;
        position: relative;
    }
    .upscale-button:hover {
        color: #f5f6fa;
    }
    .prompt-input {
        font-size: 20px;
        text-align: center;
    }
    .create-button {
        font-size: 22px;
        background-color: #b71540;
        color: white;
        position: relative;
        padding-left: 15px;
        padding-right: 15px;
    }
    .create-button:hover {
        color: #f5f6fa;
    }
    .spinner {
        display: none;
        width: 1rem;
        height: 1rem;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #555;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-top: -0.5rem;
        margin-left: -0.5rem;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .modal-body {
        max-height: 60vh;
        overflow-y: auto;
    }
</style>

<div class="container text-center my-4">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <form id="imageGenerationForm" method="POST" action="{{ route('cp_image_generation.store') }}" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="form-group">
            <input type="text" class="form-control radio-canada-big prompt-input" id="prompt" name="prompt" placeholder="Generate custom puzzle picture options with a prompt!" required><br>
        </div>
        <button type="submit" class="btn create-button radio-canada-big" id="createButton">
            <div class="spinner" id="mainSpinner"></div>
            <span id="createButtonText">Create Puzzle Pictures</span>
        </button>
    </form>
    <br>
    <h2 class="radio-canada-big mb-5" style="font-size: 28px;">Picture Options</h2>
    @if($images->isEmpty())
        <p class="text-muted radio-canada-big">No images generated yet. Start by entering a prompt and creating your first custom puzzle picture.</p>
    @else
        <div class="row justify-content-center">
            @foreach($images as $image)
                @if($image->image_type == 'original')
                <div class="col-md-4 d-flex justify-content-center mb-4">
                    <div class="card shadow-sm">
                        <img src="{{ Storage::url($image->generated_image) }}" class="card-img-top" alt="{{ $image->prompt }}" style="border-radius: 4px;">
                        <div class="card-body text-center">
                            <p class="card-text radio-canada-big">{{ $image->prompt }}</p>
                            <div class="mt-3">
                                <div class="row mb-2">
                                    <div class="col">
                                        <button class="btn upscale-button radio-canada-big w-100" onclick="upscaleImage(this, '{{ $image->id }}', '{{ $image->midjourney_message_id }}', 'U1')">
                                            <div class="spinner" id="U1Spinner"></div>
                                            Top Left
                                        </button>
                                    </div>
                                    <div class="col">
                                        <button class="btn upscale-button radio-canada-big w-100" onclick="upscaleImage(this, '{{ $image->id }}', '{{ $image->midjourney_message_id }}', 'U2')">
                                            <div class="spinner" id="U2Spinner"></div>
                                            Top Right
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <button class="btn upscale-button radio-canada-big w-100" onclick="upscaleImage(this, '{{ $image->id }}', '{{ $image->midjourney_message_id }}', 'U3')">
                                            <div class="spinner" id="U3Spinner"></div>
                                            Bottom Left
                                        </button>
                                    </div>
                                    <div class="col">
                                        <button class="btn upscale-button radio-canada-big w-100" onclick="upscaleImage(this, '{{ $image->id }}', '{{ $image->midjourney_message_id }}', 'U4')">
                                            <div class="spinner" id="U4Spinner"></div>
                                            Bottom Right
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="loadingModalLabel" class="arvo-bold">Community Made Puzzles</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <h5 id="loadingModalText" class="arvo-bold">Creating Puzzle Pictures.</h5>
                </div>
                <div class="mt-4">
                    <div class="row">
                        @foreach($publishedProducts as $product)
                            <div class="col-md-4 mb-4">
                                <div class="card shadow-sm h-100">
                                    <a href="{{ $product->product_url }}" class="block mb-4">
                                        <img src="{{ Storage::url($product->cropped_image) }}" alt="{{ $product->title }}" class="w-full h-auto rounded-lg" style="border-radius: 10px;">
                                    </a>
                                    <div class="card-body text-center">
                                        <a href="{{ $product->product_url }}" target="_blank" class="text-gray-500 text-sm">Open in a new tab <i class="fa fa-external-link"></i></a>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between align-items-center">
                                        <span class="text-gray-500 text-sm">{{ $product->likes->count() }} likes</span>
                                        <button class="like-button ml-2 {{ $product->likes->contains('user_id', auth()->id()) ? 'liked' : '' }}" onclick="toggleLike({{ $product->id }}, this)">
                                            <i class="fa fa-heart{{ $product->likes->contains('user_id', auth()->id()) ? '' : '-o' }}"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://kit.fontawesome.com/21428d3739.js" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    let loadingText = document.getElementById('loadingModalText');
    let dots = 1;

    function changeLoadingText() {
        loadingText.textContent = 'Creating Puzzle Pictures' + '.'.repeat(dots);
        dots = (dots % 3) + 1;
    }

    document.getElementById('imageGenerationForm').addEventListener('submit', function () {
        $('#loadingModal').modal('show');
        setInterval(changeLoadingText, 500);
    });

    function upscaleImage(buttonElement, imageId, messageId, button) {
        const spinner = buttonElement.querySelector('.spinner');
        spinner.style.display = 'inline-block';

        $('#loadingModal').modal('show');
        document.getElementById('loadingModalText').textContent = 'Upscaling your image...';

        fetch('{{ route('cp_image_generation.upscale') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                button: button,
                message_id: messageId
            })
        })
        .then(response => response.json())
        .then(data => {
            spinner.style.display = 'none';
            $('#loadingModal').modal('hide');
            if (data.success) {
                location.href = '{{ url('cp-image-generation/upscaled') }}/' + data.id;
            } else {
                alert('Upscaling failed: ' + data.error);
            }
        })
        .catch(error => {
            spinner.style.display = 'none';
            $('#loadingModal').modal('hide');
            console.error('Error:', error);
            alert('Upscaling failed: ' + error.message);
        });
    }
</script>
@endsection
