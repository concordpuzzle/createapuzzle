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
            'cropped_image' => 'required|image',
        ]);

        try {
            $croppedImage = $request->file('cropped_image');
            $path = $croppedImage->store('public/generated_images');
            Log::info('Cropped image saved successfully', ['path' => $path]);

            // Save cropped image information to database
            $image = CPImageGeneration::create([
                'prompt' => 'Cropped Image',
                'generated_image' => $path,
            ]);

            // Generate AI title and description
            $aiResponse = $this->generateTitleAndDescription($path);
            $title = $aiResponse['title'];
            $description = $aiResponse['description'];

            return response()->json(['success' => true, 'id' => $image->id, 'path' => Storage::url($path), 'title' => $title, 'description' => $description]);
        } catch (\Exception $e) {
            Log::error('Error during image cropping', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function showCropped($id)
    {
        $image = CPImageGeneration::findOrFail($id);
        return view('cp_image_generation.cropped', compact('image'));
    }

    public function generateTitleAndDescription($imagePath)
    {
        $apiKey = env('OPENAI_API_KEY');
        $imageUrl = Storage::url($imagePath);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])->post('https://api.openai.com/v1/engines/davinci-codex/completions', [
            'prompt' => "Generate a title and description for this image: $imageUrl",
            'max_tokens' => 60,
        ]);

        if ($response->successful()) {
            $generatedText = $response->json()['choices'][0]['text'];
            // Split the response into title and description
            $parts = explode("\n", $generatedText);
            $title = $parts[0] ?? 'Untitled';
            $description = $parts[1] ?? 'No description available.';

            return ['title' => $title, 'description' => $description];
        } else {
            Log::error('Failed to get a successful response from OpenAI API for title and description', ['response' => $response->body()]);
            return ['title' => 'Untitled', 'description' => 'No description available.'];
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
            'size' => '512x512',
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

public function createProduct(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'image_id' => 'required|exists:c_p_image_generations,id',
    ]);

    $image = CPImageGeneration::findOrFail($request->image_id);

    try {
        $woocommerce = new \Automattic\WooCommerce\Client(
            env('WOOCOMMERCE_SITE_URL'),
            env('WOOCOMMERCE_CONSUMER_KEY'),
            env('WOOCOMMERCE_CONSUMER_SECRET'),
            [
                'wp_api' => true,
                'version' => 'wc/v3',
            ]
        );

        $productData = [
            'name' => $request->title,
            'type' => 'simple',
            'regular_price' => '19.99',
            'description' => $request->description,
            'images' => [
                [
                    'src' => Storage::url($image->generated_image),
                ],
            ],
            'categories' => [
                [
                    'id' => 123,  // Replace with your specific category ID
                ],
            ],
        ];

        $product = $woocommerce->post('products', $productData);
        Log::info('Product created successfully in WooCommerce', ['product' => $product]);

        return redirect()->route('cp_image_generation.index')->with('success', 'Puzzle created and added to store successfully');
    } catch (\Exception $e) {
        Log::error('Error creating product in WooCommerce', ['error' => $e->getMessage()]);
        return redirect()->route('cp_image_generation.index')->with('error', 'An error occurred while creating the puzzle');
    }
}
