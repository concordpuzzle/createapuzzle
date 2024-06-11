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
use Automattic\WooCommerce\Client;

class CPImageGenerationController extends Controller
{
    public function index()
    {
        $images = CPImageGeneration::all();
        return view('cp_image_generation.index', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:255',
        ]);
    
        $prompt = $request->input('prompt');
        $apiKey = env('MIDJOURNEY_API_TOKEN');
        $apiUrl = env('MIDJOURNEY_API_URL');
    
        // Log the environment variables
        Log::info('Environment Variables', [
            'MIDJOURNEY_API_URL' => $apiUrl,
            'MIDJOURNEY_API_TOKEN' => $apiKey,
        ]);
    
        if (!$apiUrl || !$apiKey) {
            Log::error('MidJourney API URL or Token is not set');
            return redirect()->route('cp_image_generation.index')->with('error', 'MidJourney API URL or Token is not set.');
        }
    
        try {
            // Log the request being sent to MidJourney API
            Log::info('Sending request to MidJourney API', [
                'url' => $apiUrl,
                'prompt' => $prompt,
                'headers' => ['Authorization' => 'Bearer ' . $apiKey]
            ]);
    
            // Send the prompt to MidJourney API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post($apiUrl, [
                'prompt' => $prompt,
            ]);
    
            // Log the response received from MidJourney API
            Log::info('Response from MidJourney API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
    
            if ($response->failed()) {
                throw new \Exception('Failed to get a successful response from MidJourney API: ' . $response->body());
            }
    
            $imageUrl = $response->json()['image_url'];
    
            // Store the image locally
            $imageContents = file_get_contents($imageUrl);
            $imageName = 'generated_images/' . uniqid() . '.png';
            Storage::put('public/' . $imageName, $imageContents);
    
            // Save the image information to the database
            $image = CPImageGeneration::create([
                'prompt' => $prompt,
                'generated_image' => $imageName,
            ]);
    
            return redirect()->route('cp_image_generation.index')->with('success', 'Image generated successfully.');
        } catch (\Exception $e) {
            Log::error('Error generating image', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->route('cp_image_generation.index')->with('error', 'Failed to generate image.');
        }
    }
    
    public function crop(Request $request)
    {
        Log::info('Cropping image request received', ['request' => $request->all()]);

        $request->validate([
            'cropped_image' => 'required|image',
        ]);

        try {
            $croppedImage = $request->file('cropped_image');
            Log::info('Cropped image file details', ['file' => $croppedImage]);

            $path = $croppedImage->store('public/generated_images');
            Log::info('Cropped image saved successfully', ['path' => $path]);

            // Remove 'public/' from the path before storing in the database
            $storedPath = str_replace('public/', '', $path);

            // Save cropped image information to database
            $image = CPImageGeneration::create([
                'prompt' => 'Cropped Image',
                'generated_image' => $storedPath,
            ]);

            // Generate AI title and description
            $aiResponse = $this->generateTitleAndDescription($storedPath);
            $title = $aiResponse['title'];
            $description = $aiResponse['description'];

            return response()->json([
                'success' => true,
                'id' => $image->id,
                'path' => Storage::url($storedPath),
                'title' => $title,
                'description' => $description,
            ]);
        } catch (\Exception $e) {
            Log::error('Error during image cropping', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function generateTitleAndDescription($imagePath)
    {
        // Placeholder function to generate a title and description using AI.
        // You would replace this with a call to your AI service.
        return [
            'title' => 'Generated Title',
            'description' => 'Generated Description',
        ];
    }
}