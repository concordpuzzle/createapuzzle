@extends('layouts.app')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<div class="container text-center">
    <h1>Crop Your Upscaled Image</h1>
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
    <div class="mb-4">
        <img id="imageToCrop" src="{{ Storage::url($image->generated_image) }}" class="img-fluid" alt="Upscaled Image">
    </div>
    <button class="btn btn-primary" onclick="cropImage()">Crop Image</button>

    <!-- Crop Modal -->
    <div id="cropModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img id="imageToCropModal" src="" style="max-width: 100%;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="submitCroppedImage()">Submit Cropped Image</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

    function cropImage() {
        const image = document.getElementById('imageToCrop');
        $('#cropModal').modal('show');
        $('#cropModal').on('shown.bs.modal', function () {
            const imageToCropModal = document.getElementById('imageToCropModal');
            imageToCropModal.src = image.src;
            cropper = new Cropper(imageToCropModal, {
                aspectRatio: 16 / 9,
                viewMode: 1
            });
        }).on('hidden.bs.modal', function () {
            cropper.destroy();
            cropper = null;
        });
    }

    function submitCroppedImage() {
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
