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
        Log::info('Image generation request received', ['request' => $request->all()]);
    
        $request->validate([
            'prompt' => 'required|string|max:255',
        ]);
    
        $prompt = $request->input('prompt');
        $apiKey = env('MIDJOURNEY_API_TOKEN');
        $apiUrl = env('MIDJOURNEY_API_URL');
    
        if (!$apiUrl || !$apiKey) {
            Log::error('MidJourney API URL or Token is not set', [
                'apiUrl' => $apiUrl,
                'apiKey' => $apiKey
            ]);
            return redirect()->route('cp_image_generation.index')->with('error', 'MidJourney API URL or Token is not set.');
        }
    
        try {
            // Log the request being sent to MidJourney API
            $apiEndpoint = $apiUrl . '/api/v1/midjourney/imagine';
            Log::info('Sending request to MidJourney API', [
                'url' => $apiEndpoint,
                'prompt' => $prompt,
                'headers' => ['Authorization' => 'Bearer ' . $apiKey]
            ]);
    
            // Send the prompt to MidJourney API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->post($apiEndpoint, [
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
    
            // Assuming the API returns a messageId and status
            $messageId = $response->json()['messageId'];
    
            // Track the progress of the image generation
            $progressUrl = $apiUrl . '/api/v1/midjourney/message/' . $messageId;
            $imageUrl = null;
    
            // Polling mechanism to check the image generation status
            for ($i = 0; $i < 30; $i++) { // Increase the number of retries to allow more time for processing
                sleep(10); // Wait for 10 seconds before checking the status again
    
                $progressResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json'
                ])->get($progressUrl);
    
                Log::info('Progress response from MidJourney API', [
                    'status' => $progressResponse->status(),
                    'body' => $progressResponse->body(),
                ]);
    
                $progressData = $progressResponse->json();
    
                if (isset($progressData['status'])) {
                    if ($progressData['status'] === 'DONE' && isset($progressData['uri'])) {
                        $imageUrl = $progressData['uri'];
                        break;
                    } elseif ($progressData['status'] === 'FAILED') {
                        throw new \Exception('Image generation failed: ' . $progressResponse->body());
                    } elseif ($progressData['status'] === 'QUEUED' || $progressData['status'] === 'PROCESSING') {
                        continue; // Continue polling
                    } else {
                        throw new \Exception('Unexpected response: ' . $progressResponse->body());
                    }
                } else {
                    throw new \Exception('Unexpected response: ' . $progressResponse->body());
                }
            }
    
            if (!$imageUrl) {
                throw new \Exception('Failed to get image URL after polling.');
            }
    
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
        

    public function upscale(Request $request)
{
    $request->validate([
        'button' => 'required|string|in:U1,U2,U3,U4',
        'image_id' => 'required|exists:cp_image_generations,id',
    ]);

    $button = $request->input('button');
    $image = CPImageGeneration::findOrFail($request->input('image_id'));
    $messageId = $image->message_id;  // Ensure this is the correct field

    $apiKey = env('MIDJOURNEY_API_TOKEN');
    $apiUrl = env('MIDJOURNEY_API_URL');

    if (!$apiUrl || !$apiKey) {
        Log::error('MidJourney API URL or Token is not set', [
            'apiUrl' => $apiUrl,
            'apiKey' => $apiKey
        ]);
        return response()->json(['success' => false, 'error' => 'MidJourney API URL or Token is not set.']);
    }

    try {
        // Send the upscaling request to MidJourney API
        $upscaleEndpoint = $apiUrl . '/api/v1/midjourney/button';
        $upscaleResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post($upscaleEndpoint, [
            'messageId' => $messageId,
            'button' => $button,
        ]);

        Log::info('Upscale response from MidJourney API', [
            'status' => $upscaleResponse->status(),
            'body' => $upscaleResponse->body(),
        ]);

        if ($upscaleResponse->failed()) {
            throw new \Exception('Failed to upscale image: ' . $upscaleResponse->body());
        }

        // Track the progress of the upscaling
        $upscaleMessageId = $upscaleResponse->json()['messageId'];
        $upscaleUrl = null;
        $progressUrl = $apiUrl . '/api/v1/midjourney/message/' . $upscaleMessageId;

        for ($i = 0; $i < 30; $i++) {
            sleep(10);

            $upscaleProgressResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->get($progressUrl);

            Log::info('Upscale progress response from MidJourney API', [
                'status' => $upscaleProgressResponse->status(),
                'body' => $upscaleProgressResponse->body(),
            ]);

            $upscaleProgressData = $upscaleProgressResponse->json();

            if (isset($upscaleProgressData['status'])) {
                if ($upscaleProgressData['status'] === 'DONE' && isset($upscaleProgressData['uri'])) {
                    $upscaleUrl = $upscaleProgressData['uri'];
                    break;
                } elseif ($upscaleProgressData['status'] === 'FAILED') {
                    throw new \Exception('Upscale failed: ' . $upscaleProgressResponse->body());
                } elseif ($upscaleProgressData['status'] === 'QUEUED' || $upscaleProgressData['status'] === 'PROCESSING') {
                    continue; // Continue polling
                } else {
                    throw new \Exception('Unexpected response: ' . $upscaleProgressResponse->body());
                }
            } else {
                throw new \Exception('Unexpected response: ' . $upscaleProgressResponse->body());
            }
        }

        if (!$upscaleUrl) {
            throw new \Exception('Failed to get upscaled image URL after polling.');
        }

        // Store the upscaled image locally
        $upscaleImageContents = file_get_contents($upscaleUrl);
        $upscaleImageName = 'generated_images/upscaled_' . uniqid() . '.png';
        Storage::put('public/' . $upscaleImageName, $upscaleImageContents);

        // Save the upscaled image information to the database
        $upscaledImage = CPImageGeneration::create([
            'prompt' => 'Upscaled ' . $button . ' part of ' . $messageId,
            'generated_image' => $upscaleImageName,
        ]);

        return response()->json(['success' => true, 'image' => $upscaledImage]);
    } catch (\Exception $e) {
        Log::error('Error during upscaling', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
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