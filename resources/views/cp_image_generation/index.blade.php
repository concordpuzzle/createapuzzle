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
    }
    .create-button:hover {
        color: #f5f6fa;
    }
    .spinner {
        display: none;
        margin: 0 auto 20px;
        animation: spin 2s linear infinite;
    }
    @keyframes spin {
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
            </div>
            <br>
            <input type="text" class="form-control radio-canada-big prompt-input" id="prompt" name="prompt" placeholder="Generate custom puzzle picture options with a prompt!" required><br>
        </div>
        <div class="spinner">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="44" zoomAndPan="magnify" viewBox="0 0 33 32.999998" height="44" preserveAspectRatio="xMidYMid meet" version="1.0">
                <defs>
                    <clipPath id="dbc89d26b1">
                        <path d="M 0 6.179688 L 32 6.179688 L 32 27.179688 L 0 27.179688 Z M 0 6.179688 " clip-rule="nonzero"/>
                    </clipPath>
                    <clipPath id="9e9b04673f">
                        <path d="M 6 6.179688 L 32 6.179688 L 32 27.179688 L 6 27.179688 Z M 6 6.179688 " clip-rule="nonzero"/>
                    </clipPath>
                    <clipPath id="456132df0d">
                        <path d="M 7 6.179688 L 14 6.179688 L 14 15 L 7 15 Z M 7 6.179688 " clip-rule="nonzero"/>
                    </clipPath>
                </defs>
                <rect x="-3.3" width="39.6" fill="#ffffff" y="-3.3" height="39.599998" fill-opacity="1"/>
                <rect x="-3.3" width="39.6" fill="#ffffff" y="-3.3" height="39.599998" fill-opacity="1"/>
                <g clip-path="url(#dbc89d26b1)">
                    <path fill="#96d14a" d="M 29.945312 20.421875 C 28.835938 21.195312 27.339844 20.671875 26.046875 19.296875 C 25.84375 19.078125 25.722656 18.570312 25.339844 18.78125 C 25.183594 18.863281 25.21875 19.382812 25.234375 19.703125 C 25.339844 21.636719 25.628906 23.574219 24.910156 25.46875 C 24.589844 26.308594 24.011719 26.707031 23.183594 26.839844 C 21.359375 27.128906 19.523438 27.167969 17.691406 27.0625 C 16.324219 26.984375 15.945312 26.175781 16.472656 24.789062 C 16.757812 24.039062 17.09375 23.300781 16.324219 22.628906 C 15.539062 21.945312 14.632812 22.050781 13.792969 22.378906 C 12.652344 22.828125 12.472656 24.023438 13.402344 24.878906 C 13.847656 25.289062 14.175781 25.707031 13.90625 26.257812 C 13.6875 26.707031 13.214844 26.867188 12.648438 26.816406 C 10.949219 26.664062 9.242188 26.542969 7.539062 26.472656 C 6.566406 26.433594 6.222656 25.910156 6.195312 25.019531 C 6.144531 23.320312 6.019531 21.628906 6.15625 19.925781 C 6.203125 19.398438 6.179688 18.703125 5.714844 18.4375 C 5.355469 18.234375 4.914062 18.738281 4.535156 18.972656 C 3.34375 19.714844 2.195312 19.617188 1.3125 18.691406 C 0.179688 17.503906 0 15.628906 0.894531 14.328125 C 1.636719 13.25 2.566406 13.070312 4.046875 13.671875 C 4.601562 13.902344 5.109375 14.355469 5.78125 14.160156 C 6.078125 12.480469 5.902344 10.769531 6.082031 9.082031 C 6.320312 6.8125 5.804688 6.796875 8.652344 6.683594 C 10.15625 6.304688 11.703125 6.308594 13.238281 6.222656 C 13.867188 6.191406 14.492188 6.253906 14.632812 7.078125 C 14.761719 7.832031 14.707031 8.496094 13.804688 8.730469 C 13.703125 8.757812 13.632812 8.902344 13.546875 8.992188 C 13.210938 9.898438 13.574219 10.574219 14.328125 10.980469 C 15.460938 11.589844 16.703125 11.648438 17.886719 11.195312 C 18.882812 10.816406 19.097656 9.679688 18.394531 8.828125 C 17.949219 8.285156 17.308594 7.8125 17.769531 7.003906 C 18.195312 6.253906 18.957031 6.429688 19.644531 6.449219 C 20.988281 6.496094 22.335938 6.527344 23.679688 6.585938 C 25.1875 6.648438 25.667969 7.140625 25.902344 8.683594 C 26.117188 10.117188 25.589844 11.460938 25.484375 12.855469 C 25.386719 14.1875 25.476562 14.335938 26.789062 14.039062 C 27.675781 13.835938 28.5625 13.839844 29.449219 14.046875 C 29.972656 14.164062 30.367188 14.359375 30.59375 14.859375 C 32.996094 16.933594 31.695312 19.199219 29.945312 20.421875 Z M 29.945312 20.421875 " fill-opacity="1" fill-rule="nonzero"/>
                </g>
                <g clip-path="url(#9e9b04673f)">
                    <path fill="#ecc02c" d="M 17.753906 17.699219 C 16.996094 18.863281 15.984375 19.914062 15.351562 21.125 C 15.183594 21.441406 15.03125 21.78125 14.890625 22.125 C 14.511719 22.148438 14.144531 22.234375 13.792969 22.378906 C 12.652344 22.828125 12.472656 24.023438 13.402344 24.878906 C 13.484375 24.953125 13.5625 25.03125 13.632812 25.109375 L 13.632812 25.105469 C 13.949219 25.445312 14.125 25.808594 13.90625 26.257812 C 13.6875 26.707031 13.210938 26.867188 12.648438 26.816406 C 12.632812 26.816406 12.617188 26.816406 12.601562 26.8125 C 10.914062 26.660156 9.226562 26.542969 7.539062 26.472656 C 6.566406 26.433594 6.222656 25.910156 6.195312 25.019531 C 6.144531 23.320312 6.019531 21.628906 6.15625 19.925781 C 6.175781 19.738281 6.175781 19.554688 6.164062 19.371094 C 6.777344 18.5 7.519531 17.765625 8.386719 17.160156 C 9.679688 16.265625 11.253906 15.894531 12.671875 15.253906 C 14.394531 14.476562 15.515625 13.515625 16.589844 11.945312 C 16.703125 11.777344 16.816406 11.613281 16.925781 11.445312 C 17.253906 11.398438 17.578125 11.3125 17.886719 11.195312 C 18.824219 10.839844 19.070312 9.8125 18.511719 8.984375 C 19.070312 8.109375 19.675781 7.269531 20.335938 6.472656 C 21.449219 6.507812 22.566406 6.535156 23.679688 6.585938 C 23.765625 6.585938 23.847656 6.59375 23.929688 6.601562 C 23.351562 7.25 22.839844 7.957031 22.488281 8.863281 C 21.238281 12.066406 19.617188 14.832031 17.753906 17.699219 Z M 31.257812 15.546875 C 30.046875 15.515625 28.828125 15.476562 27.636719 15.570312 C 25.90625 15.707031 24.917969 16.878906 23.488281 17.730469 C 21.699219 18.792969 19.542969 19.222656 17.882812 20.535156 C 17.1875 21.09375 16.539062 21.699219 15.9375 22.359375 C 16.074219 22.433594 16.203125 22.523438 16.324219 22.628906 C 17.09375 23.300781 16.757812 24.039062 16.472656 24.789062 C 15.945312 26.175781 16.324219 26.984375 17.691406 27.0625 C 19.523438 27.167969 21.359375 27.128906 23.183594 26.839844 C 23.226562 26.832031 23.265625 26.824219 23.308594 26.816406 C 23.925781 26.71875 24.027344 26.578125 24.382812 26.292969 C 24.554688 26.140625 24.691406 25.953125 24.789062 25.742188 C 24.832031 25.652344 24.875 25.5625 24.910156 25.46875 C 25.628906 23.574219 25.339844 21.636719 25.234375 19.703125 C 25.21875 19.382812 25.183594 18.863281 25.339844 18.78125 C 25.722656 18.570312 25.84375 19.078125 26.046875 19.296875 C 26.585938 19.871094 27.160156 20.296875 27.730469 20.539062 C 27.792969 20.363281 27.871094 20.191406 27.957031 20.027344 C 28.738281 18.558594 30.269531 17.320312 31.707031 16.308594 C 31.589844 16.035156 31.441406 15.78125 31.257812 15.546875 Z M 31.257812 15.546875 " fill-opacity="1" fill-rule="nonzero"/>
                </g>
                <path fill="#000000" d="M 25.875 17.855469 C 25.449219 18.078125 25.109375 18.367188 24.691406 18.613281 C 23.777344 19.148438 23.3125 19.796875 22.632812 20.601562 C 21.867188 21.511719 20.878906 22.210938 19.839844 22.804688 C 18.644531 23.484375 17.625 24.367188 16.777344 25.460938 C 16.582031 25.722656 16.433594 26.011719 16.328125 26.324219 C 16.261719 26.136719 16.234375 25.949219 16.246094 25.753906 C 16.980469 24.621094 17.96875 23.636719 18.992188 22.871094 C 19.984375 22.128906 21.40625 21.632812 22.183594 20.664062 C 22.796875 19.902344 23.335938 19.203125 24.125 18.652344 C 25.078125 17.984375 26.011719 17.476562 27.105469 17.222656 C 26.984375 17.546875 26.195312 17.6875 25.875 17.855469 Z M 18.074219 12.554688 C 17.070312 13.371094 16.789062 14.308594 15.523438 15.222656 C 14.460938 15.988281 12.992188 17.652344 11.980469 18.496094 C 10.457031 19.761719 10.066406 20.5 8.175781 21.824219 C 7.257812 22.464844 6.734375 23.730469 6.1875 24.746094 C 6.191406 24.835938 6.191406 24.929688 6.195312 25.019531 C 6.195312 25.28125 6.246094 25.539062 6.339844 25.78125 C 6.726562 24.804688 7.164062 23.792969 7.78125 23.023438 C 8.566406 22.046875 9.703125 21.355469 10.582031 20.460938 C 11.453125 19.570312 12.425781 18.691406 13.226562 17.742188 C 14.207031 16.574219 16.222656 15.152344 17.355469 13.957031 C 19.070312 12.160156 20.15625 11.28125 21.332031 9.238281 C 20.09375 10.707031 19.015625 11.785156 18.074219 12.554688 Z M 18.074219 12.554688 " fill-opacity="1" fill-rule="nonzero"/>
                <g clip-path="url(#456132df0d)">
                    <path fill="#fbeee8" d="M 10.394531 11.84375 C 10.070312 11.753906 9.578125 11.746094 9.277344 11.925781 C 9.269531 11.953125 9.273438 11.976562 9.441406 11.988281 C 9.089844 12.097656 8.753906 12.546875 8.640625 12.820312 C 8.425781 13.34375 8.527344 13.910156 8.863281 14.355469 C 9.527344 15.234375 10.707031 15.097656 11.355469 14.269531 C 12.011719 13.433594 11.308594 12.09375 10.394531 11.84375 Z M 9.84375 14.378906 C 9.171875 14.152344 8.867188 13.460938 9.332031 12.871094 C 9.582031 12.546875 9.910156 12.398438 10.3125 12.417969 C 10.519531 12.433594 10.882812 12.546875 11.015625 12.738281 C 10.996094 12.632812 11.007812 12.625 11.027344 12.621094 C 11.203125 12.769531 11.285156 13.070312 11.285156 13.289062 C 11.289062 13.898438 10.523438 14.605469 9.84375 14.378906 Z M 13.738281 8.769531 C 13.378906 8.328125 12.738281 8.015625 12.230469 7.976562 C 11.898438 7.949219 11.488281 7.910156 11.484375 8.125 C 10.300781 8.496094 10.535156 10.0625 11.566406 10.515625 C 12.214844 10.800781 13.054688 10.746094 13.582031 10.285156 C 13.390625 9.929688 13.359375 9.496094 13.546875 8.992188 C 13.605469 8.914062 13.671875 8.839844 13.738281 8.765625 Z M 13.148438 10.007812 C 12.742188 10.339844 12.109375 10.269531 11.6875 9.960938 C 11.097656 9.527344 11.167969 8.503906 11.96875 8.441406 C 12 8.304688 12.253906 8.386719 12.460938 8.453125 C 13.050781 8.636719 13.796875 9.472656 13.148438 10.007812 Z M 7.296875 7.300781 C 7.246094 7.125 7.226562 6.945312 7.246094 6.765625 C 7.332031 6.753906 7.425781 6.746094 7.527344 6.738281 C 7.527344 6.835938 7.542969 6.929688 7.570312 7.023438 C 7.765625 7.617188 8.800781 7.710938 9.300781 7.492188 C 9.761719 7.289062 9.875 6.851562 9.75 6.46875 C 9.867188 6.453125 9.984375 6.4375 10.097656 6.421875 C 10.316406 6.976562 10.191406 7.648438 9.550781 7.945312 C 8.898438 8.25 7.550781 8.121094 7.296875 7.300781 Z M 7.296875 7.300781 " fill-opacity="1" fill-rule="nonzero"/>
                </g>
            </svg>
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

    document.getElementById('imageGenerationForm').addEventListener('submit', function () {
        document.querySelector('.spinner').style.display = 'block';
    });

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
