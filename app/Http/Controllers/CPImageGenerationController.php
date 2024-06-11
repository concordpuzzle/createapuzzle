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
        $apiEndpoint = $apiUrl . '/api/v1/midjourney/imagine';
        Log::info('Sending request to MidJourney API', [
            'url' => $apiEndpoint,
            'prompt' => $prompt,
            'headers' => ['Authorization' => 'Bearer ' . $apiKey]
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post($apiEndpoint, [
            'prompt' => $prompt,
        ]);

        Log::info('Response from MidJourney API', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to get a successful response from MidJourney API: ' . $response->body());
        }

        $messageId = $response->json()['messageId'];

        // Save the initial image data with messageId
        $image = CPImageGeneration::create([
            'prompt' => $prompt,
            'generated_image' => '',
            'midjourney_message_id' => $messageId
        ]);

        $progressUrl = $apiUrl . '/api/v1/midjourney/message/' . $messageId;
        $imageUrl = null;

        for ($i = 0; $i < 30; $i++) {
            sleep(10);

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
                    continue;
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

        $imageContents = file_get_contents($imageUrl);
        $imageName = 'generated_images/' . uniqid() . '.png';
        Storage::put('public/' . $imageName, $imageContents);

        $image->generated_image = $imageName;
        $image->save();

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
        'button' => 'required|string',
        'image_id' => 'required|integer|exists:cp_image_generations,id',
    ]);

    $button = $request->input('button');
    $imageId = $request->input('image_id');
    $apiKey = env('MIDJOURNEY_API_TOKEN');
    $apiUrl = env('MIDJOURNEY_API_URL');

    if (!$apiUrl || !$apiKey) {
        return response()->json(['success' => false, 'error' => 'MidJourney API URL or Token is not set.']);
    }

    $image = CPImageGeneration::find($imageId);

    if (!$image->midjourney_message_id) {
        return response()->json(['success' => false, 'error' => 'MidJourney message ID not found.']);
    }

    try {
        $apiEndpoint = $apiUrl . '/api/v1/midjourney/upscale';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post($apiEndpoint, [
            'messageId' => $image->midjourney_message_id,
            'button' => $button,
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to get a successful response from MidJourney API: ' . $response->body());
        }

        $responseData = $response->json();

        if (!$responseData['success']) {
            throw new \Exception('Upscaling failed: ' . $response->body());
        }

        $upscaleMessageId = $responseData['messageId'];

        // Polling mechanism to check the upscale status
        $progressUrl = $apiUrl . '/api/v1/midjourney/message/' . $upscaleMessageId;
        $imageUrl = null;

        for ($i = 0; $i < 30; $i++) {
            sleep(10);

            $progressResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->get($progressUrl);

            $progressData = $progressResponse->json();

            if (isset($progressData['status'])) {
                if ($progressData['status'] === 'DONE' && isset($progressData['uri'])) {
                    $imageUrl = $progressData['uri'];
                    break;
                } elseif ($progressData['status'] === 'FAILED') {
                    throw new \Exception('Upscaling failed: ' . $progressResponse->body());
                } elseif ($progressData['status'] === 'QUEUED' || $progressData['status'] === 'PROCESSING') {
                    continue;
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

        return redirect()->route('cp_image_generation.upscaled', ['image_url' => $imageUrl]);

    } catch (\Exception $e) {
        Log::error('Error upscaling image', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        return response()->json(['success' => false, 'error' => 'Failed to upscale image.']);
    }
}
public function showUpscaledImage(Request $request)
{
    $imageUrl = $request->input('image_url');

    if (!$imageUrl) {
        return redirect()->route('cp_image_generation.index')->with('error', 'No upscaled image URL provided.');
    }

    return view('cp_image_generation.upscaled', ['imageUrl' => $imageUrl]);
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