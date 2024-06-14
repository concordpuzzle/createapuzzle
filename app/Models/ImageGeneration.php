<?php

namespace App\Models;
// app/Http/Controllers/ImageGenerationController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImageGeneration;
use Illuminate\Support\Facades\Storage;
use OpenAI;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImgGen extends Model
{
    use HasFactory;
}

class ImageGenerationControl extends Controller
{
    public function index()
    {
        $images = ImgGen::all();
        return view('staging', compact('images'));
    }

    public function generate(Request $request)
    {
        $request->validate(['prompt' => 'required|string|max:255']);

        $prompt = $request->input('prompt');
        $imageData = $this->generateImage($prompt);

        $path = 'generated_images/' . uniqid() . '.png';
        Storage::disk('public')->put($path, base64_decode($imageData));

        ImgGen::create([
            'prompt' => $prompt,
            'generated_image' => $path,
        ]);

        return redirect()->route('staging.index');
    }

    private function generateImage($prompt)
    {
        $response = OpenAI::image()->create([
            'prompt' => $prompt,
            'n' => 1,
            'size' => '512x512',
        ]);

        return $response['data'][0]['image'];
    }
}
