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
use Automattic\WooCommerce\HttpClient\HttpClientException;


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
                'midjourney_message_id' => $messageId,
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
    
    public function showUpscaled($id)
    {
        $image = CPImageGeneration::findOrFail($id);
        return view('cp_image_generation.upscaled', compact('image'));
    }
    

    public function upscale(Request $request)
{
    Log::info('Upscale request received', ['request' => $request->all()]);

    $request->validate([
        'button' => 'required|string',
        'message_id' => 'required|string|max:40',
    ]);

    $button = $request->input('button');
    $messageId = $request->input('message_id');
    $apiKey = env('MIDJOURNEY_API_TOKEN');
    $apiUrl = env('MIDJOURNEY_API_URL');

    if (!$apiUrl || !$apiKey) {
        Log::error('MidJourney API URL or Token is not set', [
            'apiUrl' => $apiUrl,
            'apiKey' => $apiKey
        ]);
        return response()->json(['error' => 'MidJourney API URL or Token is not set.'], 500);
    }

    try {
        // Send the upscale request to MidJourney API
        $apiEndpoint = $apiUrl . '/api/v1/midjourney/button';
        Log::info('Sending upscale request to MidJourney API', [
            'url' => $apiEndpoint,
            'messageId' => $messageId,
            'button' => $button,
            'headers' => ['Authorization' => 'Bearer ' . $apiKey]
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post($apiEndpoint, [
            'messageId' => $messageId,
            'button' => $button,
        ]);

        Log::info('MidJourney API upscale response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to get a successful response from MidJourney API: ' . $response->body());
        }

        // Assuming the API returns a new messageId for the upscaled image
        $newMessageId = $response->json()['messageId'];

        // Polling mechanism to check the upscale status
        $progressUrl = $apiUrl . '/api/v1/midjourney/message/' . $newMessageId;  // Corrected endpoint
        $upscaledImageUrl = null;

        for ($i = 0; $i < 30; $i++) { // Increase the number of retries to allow more time for processing
            sleep(10); // Wait for 10 seconds before checking the status again

            $progressResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->get($progressUrl);

            Log::info('Upscale progress response from MidJourney API', [
                'status' => $progressResponse->status(),
                'body' => $progressResponse->body(),
            ]);

            $progressData = $progressResponse->json();

            if (isset($progressData['status'])) {
                if ($progressData['status'] === 'DONE' && isset($progressData['uri'])) {
                    $upscaledImageUrl = $progressData['uri'];
                    break;
                } elseif ($progressData['status'] === 'FAILED') {
                    throw new \Exception('Upscaling failed: ' . $progressResponse->body());
                } elseif ($progressData['status'] === 'QUEUED' || $progressData['status'] === 'PROCESSING') {
                    continue; // Continue polling
                } else {
                    throw new \Exception('Unexpected response: ' . $progressResponse->body());
                }
            } else {
                throw new \Exception('Unexpected response: ' . $progressResponse->body());
            }
        }

        if (!$upscaledImageUrl) {
            throw new \Exception('Failed to get upscaled image URL after polling.');
        }

        // Store the upscaled image locally
        $imageContents = file_get_contents($upscaledImageUrl);
        $imageName = 'generated_images/upscaled_' . uniqid() . '.png';
        Storage::put('public/' . $imageName, $imageContents);

        // Save the image information to the database
        $image = CPImageGeneration::create([
            'prompt' => 'Upscaled Image',
            'generated_image' => $imageName,
            'midjourney_message_id' => $newMessageId,
        ]);

        return response()->json(['success' => true, 'id' => $image->id]);
    } catch (\Exception $e) {
        Log::error('Error upscaling image', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        return response()->json(['error' => 'Failed to upscale image.'], 500);
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

public function showCropped($id)
{
    $image = CPImageGeneration::findOrFail($id);
    return view('cp_image_generation.cropped', compact('image'));
}
 
public function createProduct(Request $request)
    {
        $image = CPImageGeneration::findOrFail($request->input('image_id'));

        try {
            $woocommerce = new Client(
                env('WOOCOMMERCE_STORE_URL'),
                env('WOOCOMMERCE_CONSUMER_KEY'),
                env('WOOCOMMERCE_CONSUMER_SECRET'),
                [
                    'version' => 'wc/v3',
                ]
            );

            $relativeUrl = Storage::url($image->generated_image);
            $publicUrl = url($relativeUrl);

            // Log the public URL for debugging
            Log::info("Public URL: " . $publicUrl);

            $data = [
                'name' => $request->input('title'),
                'description' => $request->input('description'),
                'images' => [
                    [
                        'src' => $publicUrl
                    ]
                ],
                'type' => 'simple',
                'regular_price' => '19.99',
            ];

            $product = $woocommerce->post('products', $data);

            // Get the product URL
            $productUrl = $product->permalink;
            return redirect($productUrl);

        } catch (HttpClientException $e) {
            $error = $e->getMessage();
            Log::error("Error creating product: cURL Error: $error");
            return back()->with('error', $error);
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