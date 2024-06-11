@extends('layouts.app')

@section('content')
<!-- Include Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Radio+Canada+Big:wght@400;700&display=swap" rel="stylesheet">

<!-- Include Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<style>
    .radio-canada-big {
        font-family: "Radio Canada Big", sans-serif;
        font-weight: 400;
    }

    .radio-canada-big-bold {
        font-family: "Radio Canada Big", sans-serif;
        font-weight: 700;
    }
</style>

<div class="container text-center my-4">
    <h1 class="radio-canada-big-bold mb-5" style="font-size: 33px;">Generate Your Custom Image</h1>
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
            <label for="prompt" class="radio-canada-big">Prompt</label>
            <input type="text" class="form-control radio-canada-big" id="prompt" name="prompt" placeholder="Enter your image prompt" required>
        </div>
        <button type="submit" class="btn btn-primary radio-canada-big" style="background-color: #0c2461; color: white;">Create Pictures</button>
    </form>
    <hr>
    <h2 class="radio-canada-big-bold mb-5" style="font-size: 28px;">Picture Options</h2>
    <div class="row justify-content-center">
        @foreach($images as $image)
            <div class="col-md-4 d-flex justify-content-center mb-4">
                <div class="card shadow-sm">
                    <img src="{{ Storage::url($image->generated_image) }}" class="card-img-top" alt="{{ $image->prompt }}" style="border-radius: 4px;">
                    <div class="card-body text-center">
                        <p class="card-text radio-canada-big">{{ $image->prompt }}</p>
                        <button class="btn btn-primary radio-canada-big" style="background-color: #0c2461; color: white;" onclick="openCropModal('{{ Storage::url($image->generated_image) }}', '{{ $image->id }}')">Crop Image</button>
                        <div class="mt-3">
                            <button class="btn radio-canada-big" style="background-color: #0c2461; color: white;" onclick="upscaleImage('{{ $image->id }}', '{{ $image->midjourney_message_id }}', 'U1')">Top Left</button>
                            <button class="btn radio-canada-big" style="background-color: #0c2461; color: white;" onclick="upscaleImage('{{ $image->id }}', '{{ $image->midjourney_message_id }}', 'U2')">Top Right</button>
                            <button class="btn radio-canada-big" style="background-color: #0c2461; color: white;" onclick="upscaleImage('{{ $image->id }}', '{{ $image->midjourney_message_id }}', 'U3')">Bottom Left</button>
                            <button class="btn radio-canada-big" style="background-color: #0c2461; color: white;" onclick="upscaleImage('{{ $image->id }}', '{{ $image->midjourney_message_id }}', 'U4')">Bottom Right</button>
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
    let cropper;
    let imageId;
    let messageId;

    function openCropModal(imageUrl, id) {
        document.getElementById('imageToCrop').src = imageUrl;
        imageId = id;
        $('#cropModal').modal('show');
        $('#cropModal').on('shown.bs.modal', function () {
            cropper = new Cropper(document.getElementById('imageToCrop'), {
                aspectRatio: 16 / 9,
                viewMode: 1
            });
        }).on('hidden.bs.modal', function () {
            cropper.destroy();
            cropper = null;
        });
    }

    function cropImage() {
        const canvas = cropper.getCroppedCanvas();
        const croppedImage = canvas.toDataURL('image/png');
        const croppedCanvas = document.getElementById('croppedCanvas');
        const context = croppedCanvas.getContext('2d');
        croppedCanvas.width = canvas.width;
        croppedCanvas.height = canvas.height;
        context.drawImage(canvas, 0, 0);

        canvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('cropped_image', blob, 'cropped.png');

            fetch('{{ route('cp_image_generation.crop') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.href = '{{ url('cp-image-generation/cropped') }}/' + data.id;
                } else {
                    alert('Crop failed: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Crop failed: ' + error.message);
            });
        });
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
