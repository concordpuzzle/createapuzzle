<!-- resources/views/cp_image_generation/index.blade.php -->
@extends('layouts.app')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
</script>
<div class="container">
    <h1>Generate Your Custom Image</h1>
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
    <form method="POST" action="{{ route('cp_image_generation.store') }}" enctype="multipart/form-data">
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
                        <button class="btn btn-primary" onclick="openCropModal('{{ Storage::url($image->generated_image) }}')">Crop Image</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Crop Modal -->
    <div id="cropModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img id="imageToCrop" src="" style="max-width: 100%;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="cropButton">Crop</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<canvas id="croppedCanvas" style="display:none;"></canvas>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
let cropper;

function openCropModal(imageUrl) {
    console.log('Opening crop modal with image URL:', imageUrl); // Log the image URL
    $('#imageToCrop').attr('src', imageUrl);
    $('#cropModal').modal('show');

    $('#cropModal').on('shown.bs.modal', function () {
        console.log('Initializing Cropper.js');
        cropper = new Cropper(document.getElementById('imageToCrop'), {
            aspectRatio: 1.35 / 1,  // Change to your desired ratio
            viewMode: 1
        });
    }).on('hidden.bs.modal', function () {
        console.log('Destroying Cropper.js instance');
        cropper.destroy();
        cropper = null;
    });
}

$('#cropButton').on('click', function() {
    console.log('Crop button clicked');
    cropImage();
});

function cropImage() {
    const canvas = cropper.getCroppedCanvas();
    const croppedImage = canvas.toDataURL('image/png');
    const croppedCanvas = document.getElementById('croppedCanvas');
    const context = croppedCanvas.getContext('2d');
    croppedCanvas.width = canvas.width;
    croppedCanvas.height = canvas.height;
    context.drawImage(canvas, 0, 0);

    console.log('Cropped image generated');

    // Send the cropped image data to the server
    canvas.toBlob(function(blob) {
        const formData = new FormData();
        formData.append('cropped_image', blob, 'cropped.png');

        console.log('Sending cropped image to server', formData);

        fetch('{{ route('cp_image_generation.crop') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log('Server response', data);
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
    }, 'image/png');
}




</script>
@endsection
