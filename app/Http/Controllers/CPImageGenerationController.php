<?php 
// app/Http/Controllers/CPImageGenerationController.php
// app/Http/Controllers/CPImageGenerationController.php
// app/Http/Controllers/CPImageGenerationController.php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\CPImageGeneration;
use Automattic\WooCommerce\Client;


class CPImageGenerationController extends Controller
{
    // Display the form and the list of generated images
    public function index()
    {
        $images = CPImageGeneration::all();
        return view('cp_image_generation.index', compact('images'));
    }

    // Handle the form submission and generate the image
    public function store(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:255',
        ]);

        $prompt = $request->input('prompt');

        try {
            $imageUrl = $this->generateImage($prompt);
            if ($imageUrl) {
                $imagePath = $this->saveImageFromUrl($imageUrl);
                $image = CPImageGeneration::create([
                    'prompt' => $prompt,
                    'generated_image' => $imagePath,
                ]);
                Log::info('Image generated and saved successfully', ['path' => $imagePath]);
                return redirect()->route('cp_image_generation.index')->with('success', 'Image generated successfully');
            } else {
                Log::error('Failed to generate image');
                return redirect()->route('cp_image_generation.index')->with('error', 'Failed to generate image');
            }
        } catch (\Exception $e) {
            Log::error('Error during image generation', ['error' => $e->getMessage()]);
            return redirect()->route('cp_image_generation.index')->with('error', 'An error occurred during image generation');
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
            Log::error('Error during image cropping', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
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
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])->post('https://api.openai.com/v1/images/generations', [
            'prompt' => $prompt,
            'n' => 1,
            'size' => '1024x1024',
        ]);

        if ($response->successful()) {
            return $response->json()['data'][0]['url'];
        } else {
            Log::error('Failed to get a successful response from OpenAI API', ['response' => $response->body()]);
            return null;
        }
    }

    private function saveImageFromUrl($url)
    {
        $contents = file_get_contents($url);
        $name = 'generated_images/' . uniqid() . '.png';
        Storage::put('public/' . $name, $contents);
        return $name;
    }

public function createProduct(Request $request)
{
    $request->validate([
        'image_id' => 'required|integer',
    ]);

    $image = CPImageGeneration::findOrFail($request->image_id);

    try {
        $woocommerce = new Client(
            env('WOOCOMMERCE_SITE_URL'),
            env('WOOCOMMERCE_CONSUMER_KEY'),
            env('WOOCOMMERCE_CONSUMER_SECRET'),
            [
                'wp_api' => true,
                'version' => 'wc/v3',
                'verify_ssl' => false, // Disable SSL verification for testing
            ]
        );

        // Generate the correct URL for the image
        $imageUrl = asset('storage/' . $image->generated_image);

        $data = [
            'name' => 'Custom Puzzle - ' . $image->prompt,
            'type' => 'simple',
            'regular_price' => '19.99',
            'description' => 'A custom puzzle generated from your image prompt.',
            'images' => [
                [
                    'src' => $imageUrl,
                    'alt' => $image->prompt,
                ],
            ],
            'categories' => [
                [
                    'id' => 1, // Replace with your actual category ID
                ],
            ],
        ];

        $product = $woocommerce->post('products', $data);

        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    } catch (\Exception $e) {
        Log::error('Error creating WooCommerce product', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        if ($e instanceof \Automattic\WooCommerce\HttpClient\HttpClientException) {
            $request = $e->getRequest();
            $response = $e->getResponse();
            Log::error('Request', ['request' => $request]);
            Log::error('Response', ['response' => $response]);
        }

        return response()->json(['success' => false, 'error' => 'cURL Error: ' . $e->getMessage()]);
    }
}

    
    

}