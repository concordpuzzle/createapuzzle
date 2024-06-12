@extends('layouts.app')

@section('content')
<!-- Include Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Radio+Canada+Big:wght@400&display=swap" rel="stylesheet">

<!-- Include Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<style>
    .radio-canada-big {
        font-family: "Radio Canada Big", sans-serif;
        font-weight: 400;
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
        font-size: 33px;
        text-align: center;
    }
    .append-button {
        font-size: 14px;
        background-color: #0c2461;
        color: white;
        margin-right: 5px;
    }
    .append-button:hover {
        color: #f5f6fa;
    }
    .create-button {
        font-size: 22px;
        background-color: #b71540;
        color: white;
        position: relative;
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
        left: 10%;
        margin-top: -0.5rem;
        margin-left: -0.5rem;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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
            <div class="mb-3">
                <button type="button" class="btn append-button radio-canada-big" onclick="appendText('photorealistic')">📸 Photorealistic</button>
                <button type="button" class="btn append-button radio-canada-big" onclick="appendText('illustration')">🖌️ Illustration</button>
                <button type="button" class="btn append-button radio-canada-big" onclick="appendText('painting')">🎨 Painting</button>
            </div><br>
            <input type="text" class="form-control radio-canada-big prompt-input" id="prompt" name="prompt" placeholder="Generate custom puzzle picture options with a prompt!" required><br>
        </div>
        <button type="submit" class="btn create-button radio-canada-big">
            <div class="spinner" id="mainSpinner"></div>
            Create Puzzle Pictures
        </button>
    </form>
    <br>
    <h2 class="radio-canada-big mb-5" style="font-size: 28px;">Picture Options</h2>
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

    <!-- Canvas to hold cropped image data -->
    <canvas id="croppedCanvas" style="display:none;"></canvas>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
    let imageId;
    let messageId;

    function appendText(text) {
        const promptInput = document.getElementById('prompt');
        promptInput.value += ` ${text}`;
    }

    document.getElementById('imageGenerationForm').addEventListener('submit', function () {
        document.getElementById('mainSpinner').style.display = 'inline-block';
    });

    function upscaleImage(buttonElement, imageId, messageId, button) {
        const spinner = buttonElement.querySelector('.spinner');
        spinner.style.display = 'inline-block';

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
            if (data.success) {
                location.href = '{{ url('cp-image-generation/upscaled') }}/' + data.id;
            } else {
                alert('Upscaling failed: ' + data.error);
            }
        })
        .catch(error => {
            spinner.style.display = 'none';
            console.error('Error:', error);
            alert('Upscaling failed: ' + error.message);
        });
    }
</script>
@endsection
