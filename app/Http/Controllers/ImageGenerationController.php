<?php

// app/Http/Controllers/ImageGenerationController.php

namespace App\Http\Controllers;

use App\Models\ImageGeneration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImageGenerationController extends Controller
{
    // Display the form and the list of generated images
    public function index()
    {
        $images = ImgGen::all();
        return view('image_generation.index', compact('images'));
    }

    // Handle the form submission and generate the image
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'prompt' => 'required|string|max:255',
        ]);

        // Get the prompt from the request
        $prompt = $request->input('prompt');

        // Generate the image using the OpenAI API
        $imageData = $this->generateImage($prompt);

        // Create a unique path for storing the image
        $path = 'generated_images/' . uniqid() . '.png';
        
        // Store the image in the storage
        Storage::put($path, $imageData);

        // Save the prompt and image path to the database
        ImgGen::create([
            'prompt' => $prompt,
            'generated_image' => $path,
        ]);

        // Redirect back to the index page
        return redirect()->route('image_generation.index');
    }

    // Function to generate the image using the OpenAI API
    private function generateImage($prompt)
    {
        // Your OpenAI API key from the environment variables
        $apiKey = env('sk-proj-au0TTX2UgCIGz1k6QXX0T3BlbkFJAAyYFjeHxIJ6vuAKEYDf');

        // Make a POST request to the OpenAI API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])->post('https://api.openai.com/v1/images', [
            'prompt' => $prompt,
            'n' => 1,
            'size' => '512x512',
        ]);

        // Decode the image data from the API response
        $imageData = base64_decode($response->json()['data'][0]['image']);

        // Return the image data
        return $imageData;
    }
}
