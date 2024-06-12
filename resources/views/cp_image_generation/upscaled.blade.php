@extends('layouts.app')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<div class="container text-center">
    <h1 class="mt-4">Crop Your Puzzle Picture Option</h1>
    <p class="mb-4">Use the cropping tool below to adjust your puzzle picture to your liking. When you're satisfied with the crop, click "Crop Puzzle" to proceed.</p>
    
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

    <button class="btn crop-puzzle-btn mb-4" onclick="submitCroppedImage()">Crop Puzzle</button>
    
    <div class="mb-4">
        <img id="imageToCrop" src="{{ Storage::url($image->generated_image) }}" class="img-fluid" alt="Upscaled Image">
    </div>

    <!-- Canvas to hold cropped image data -->
    <canvas id="croppedCanvas" style="display:none;"></canvas>
</div>

<style>
    .crop-puzzle-btn {
        background-color: #b71540;
        color: white;
        border-radius: 3px;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        margin-top: 15px;
        margin-bottom: 15px;
    }

    .crop-puzzle-btn:hover {
        background-color: #a21336;
    }

    .rounded-image {
        border-radius: 4px;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
    let cropper;

    window.onload = function() {
        const image = document.getElementById('imageToCrop');
        cropper = new Cropper(image, {
            aspectRatio: 1.35 / 1,
            viewMode: 1
        });
    }

    function submitCroppedImage() {
        if (!cropper) {
            console.error('Cropper is not initialized');
            return;
        }

        const canvas = cropper.getCroppedCanvas();
        const croppedImage = canvas.toDataURL('image/png');
        const croppedCanvas = document.getElementById('croppedCanvas');
        const context = croppedCanvas.getContext('2d');
        croppedCanvas.width = canvas.width;
        croppedCanvas.height = canvas.height;
        context.drawImage(canvas, 0, 0);

        // Send the cropped image data to the server
        canvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('cropped_image', blob, 'cropped.png');
            formData.append('original_image_id', '{{ $image->id }}'); // Add the original image ID

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
</script>

@endsection
