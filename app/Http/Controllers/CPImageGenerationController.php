<?php 
// app/Http/Controllers/CPImageGenerationController.php
// app/Http/Controllers/CPImageGenerationController.php
// app/Http/Controllers/CPImageGenerationController.php

namespace App\Http\Controllers;

use App\Models\CPImageGeneration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\ImageManagerStatic as Image;



class CPImageGenerationController extends Controller
{
    public function index()
    {
        $images = CPImageGeneration::all();
        return view('cp_image_generation.index', compact('images'));
    }

    public function crop(Request $request)
    {
        $request->validate([
            'cropped_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        if ($request->hasFile('cropped_image')) {
            $image = $request->file('cropped_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'public/generated_images/' . $imageName;
    
            // Save the cropped image
            Storage::put($imagePath, file_get_contents($image->getRealPath()));
    
            return response()->json(['success' => true, 'image_path' => Storage::url($imagePath)]);
        }
    
        return response()->json(['success' => false]);
    }

    public function store(Request $request)
    {
        Log::info('Form submission received', ['request' => $request->all()]);

        $request->validate([
            'prompt' => 'required|string|max:255',
        ]);

        $prompt = $request->input('prompt');
        $imageUrl = $this->generateImage($prompt);

        if ($imageUrl) {
            $imagePath = $this->saveImageFromUrl($imageUrl);

            CPImageGeneration::create([
                'prompt' => $prompt,
                'generated_image' => $imagePath,
            ]);

            Log::info('Image generated and saved successfully', ['path' => $imagePath]);

            return redirect()->route('cp_image_generation.index')->with('success', 'Image generated successfully.');
        } else {
            Log::error('Failed to generate image');

            return redirect()->route('cp_image_generation.index')->with('error', 'Failed to generate image.');
        }
    }

    private function generateImage($prompt)
    {
        $apiKey = env('OPENAI_API_KEY');
        Log::info('Using API Key', ['api_key' => $apiKey]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])->post('https://api.openai.com/v1/images/generations', [
            'prompt' => $prompt,
            'n' => 1,
            'size' => '1792x1024',
        ]);

        Log::info('OpenAI API response', ['response' => $response->body()]);

        if ($response->successful()) {
            $responseData = $response->json();

            if (isset($responseData['data']) && isset($responseData['data'][0]['url'])) {
                return $responseData['data'][0]['url'];
            } else {
                Log::error('OpenAI API response does not contain expected data format', ['response' => $responseData]);
            }
        } else {
            Log::error('Failed to get a successful response from OpenAI API', ['response' => $response->body()]);
        }

        return null;
    }

    private function saveImageFromUrl($url)
    {
        $imageContents = file_get_contents($url);
        $imageName = uniqid() . '.png';
        $imagePath = 'public/generated_images/' . $imageName;

        Storage::put($imagePath, $imageContents);

        return $imagePath;
    }
}
