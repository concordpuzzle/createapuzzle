<?php 
// app/Http/Controllers/CPImageGenerationController.php

namespace App\Http\Controllers;

use App\Models\CPImageGeneration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CPImageGenerationController extends Controller
{
    public function index()
    {
        $images = CPImageGeneration::all();
        return view('cp_image_generation.index', compact('images'));
    }

    public function store(Request $request)
    {
        Log::info('Form submission received', ['request' => $request->all()]);

        $request->validate([
            'prompt' => 'required|string|max:255',
        ]);

        $prompt = $request->input('prompt');
        $imageData = $this->generateImage($prompt);

        if ($imageData) {
            $path = 'generated_images/' . uniqid() . '.png';
            Storage::put($path, $imageData);

            CPImageGeneration::create([
                'prompt' => $prompt,
                'generated_image' => $path,
            ]);

            Log::info('Image generated and saved successfully', ['path' => $path]);

            return redirect()->route('cp_image_generation.index')->with('success', 'Image generated successfully.');
        } else {
            Log::error('Failed to generate image');

            return redirect()->route('cp_image_generation.index')->with('error', 'Failed to generate image.');
        }
    }

    private function generateImage($prompt)
    {
        $apiKey = env('OPENAI_API_KEY');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])->post('https://api.openai.com/v1/images', [
            'prompt' => $prompt,
            'n' => 1,
            'size' => '512x512',
        ]);

        if ($response->successful()) {
            $responseData = $response->json();

            if (isset($responseData['data']) && isset($responseData['data'][0]['image'])) {
                return base64_decode($responseData['data'][0]['image']);
            } else {
                Log::error('OpenAI API response does not contain expected data format', ['response' => $responseData]);
            }
        } else {
            Log::error('Failed to get a successful response from OpenAI API', ['response' => $response->body()]);
        }

        return null;
    }
}
