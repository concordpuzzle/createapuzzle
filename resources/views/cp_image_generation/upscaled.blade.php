@extends('layouts.app')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<div class="container">
    <h1>Upscaled Image</h1>
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <img id="imageToCrop" src="{{ $imageUrl }}" class="card-img-top" alt="Upscaled Image">
                <div class="card-body">
                    <button class="btn btn-primary" onclick="cropImage()">Crop Image</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Canvas to hold cropped image data -->
    <canvas id="croppedCanvas" style="display:none;"></canvas>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
    let cropper;
    $(document).ready(function() {
        cropper = new Cropper(document.getElementById('imageToCrop'), {
            aspectRatio: 16 / 9,  // Change to your desired ratio
            viewMode: 1
        });
    });

    function cropImage() {
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
                    location.href = '{{ url('cp-image-generation/cropped') }}/' + data.id;  // Redirect to cropped view
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
