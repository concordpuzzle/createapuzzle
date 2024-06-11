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
    }
    .prompt-input {
        font-size: 33px;
    }
    .append-button {
        font-size: 14px;
        background-color: #0c2461;
        color: white;
        margin-right: 5px;
    }
    .create-button {
        font-size: 22px;
        background-color: #b71540;
        color: white;
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
    <form method="POST" action="{{ route('cp_image_generation.store') }}" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="form-group">
            <div class="mb-3">
                <button type="button" class="btn append-button radio-canada-big" onclick="appendText('photorealistic')">📸 Photorealistic</button>
                <button type="button" class="btn append-button radio-canada-big" onclick="appendText('illustration')">🖌️ Illustration</button>
                <button type="button" class="btn append-button radio-canada-big" onclick="appendText('painting')">🎨 Painting</button>
            </div>
            <label for="prompt" class="radio-canada-big">Generate custom puzzle picture options with a prompt.</label>
            <input type="text" class="form-control radio-canada-big prompt-input" id="prompt" name="prompt" placeholder="Enter your image prompt" required>
        </div>
        <button type="submit" class="btn create-button radio-canada-big">Create Puzzle Pictures</button>
    </form>
    <br>
    <h2 class="radio-canada-big mb-5" style="font-size: 28px;">Picture Options</h2>
    <div class="row justify-content-center">
        @foreach($images as $image)
            <div class="col-md-4 d-flex justify-content-center mb-4">
                <div class="card shadow-sm">
                    <img src="{{ Storage::url($image->generated_image) }}" class="card-img-top" alt="{{ $image->prompt }}" style="border-radius: 4px;">
                    <div class="card-body text-center">
                        <p class="card-text radio-canada-big">{{ $image->prompt }}</p>
                        <div class="mt-3">
                            <div class="row mb-2">
                                <div class="col">
                                    <button class="btn upscale-button radio-canada-big w-100" onclick="upscaleImage('{{ $image->id }}', '{{ $image->midjourney_message_id }}', 'U1')">Top Left</button>
                                </div>
                                <div class="col">
                                    <button class="btn upscale-button radio-canada-big w-100" onclick="upscaleImage('{{ $image->id }}', '{{ $image->midjourney_message_id }}', 'U2')">Top Right</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <button class="btn upscale-button radio-canada-big w-100" onclick="upscaleImage('{{ $image->id }}', '{{ $image->midjourney_message_id }}', 'U3')">Bottom Left</button>
                                </div>
                                <div class="col">
                                    <button class="btn upscale-button radio-canada-big w-100" onclick="upscaleImage('{{ $image->id }}', '{{ $image->midjourney_message_id }}', 'U4')">Bottom Right</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

    function upscaleImage(imageId, messageId, button) {
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
            if (data.success) {
                location.href = '{{ url('cp-image-generation/upscaled') }}/' + data.id;
            } else {
                alert('Upscaling failed: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Upscaling failed: ' + error.message);
        });
    }
</script>
@endsection
