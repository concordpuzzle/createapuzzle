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
use App\Models\PublishedProduct; // Add this at the top of your controller
use App\Models\Like; // Add this line
use Intervention\Image\Facades\Image; // Ensure this is included

class CPImageGenerationController extends Controller
{
    public function index()
    {
        $userId = auth()->user()->id;
        
        // Fetch user's generated images
        $images = CPImageGeneration::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Fetch published products sorted by likes
        $publishedProducts = Product::with('likes')->get()->sortByDesc(function ($product) {
            return $product->likes->count();
        });
    
        return view('cp_image_generation.index', compact('images', 'publishedProducts'));
    }
    


    // app/Http/Controllers/CPImageGenerationController.php

public function publishedPuzzles()
{
    $userId = auth()->user()->id;
    $publishedProducts = PublishedProduct::where('user_id', $userId)
                            ->orderBy('created_at', 'desc')
                            ->get();

    return view('cp_image_generation.published_puzzles', compact('publishedProducts'));
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
                'user_id' => auth()->user()->id, // Associate with the authenticated user
                'prompt' => $prompt,
                'generated_image' => $imageName,
                'midjourney_message_id' => $messageId,
                'image_type' => 'original',
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
    
            // Save the image information to the database with the original prompt
            $originalImage = CPImageGeneration::where('midjourney_message_id', $messageId)->first();
            $image = CPImageGeneration::create([
                'user_id' => auth()->user()->id, // Associate with the authenticated user
                'prompt' => $originalImage->prompt, // Retain the original prompt
                'generated_image' => $imageName,
                'midjourney_message_id' => $newMessageId,
                'image_type' => 'upscaled',
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
    $user = auth()->user();
    $prompt = $image->prompt;
    $userName = explode(' ', $user->name)[0]; // Get the first name of the user

    try {
        // Call OpenAI API to generate a title and short description
        $openaiApiKey = env('OPENAI_API_KEY');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $openaiApiKey,
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a creative assistant who generates straightforward titles and descriptions for products, without useless adjectives.'
                ],
                [
                    'role' => 'user',
                    'content' => "Create a unique title and short description for a jigsaw puzzle based on this prompt: '$prompt'. The title should end with '500 Piece Puzzle'. The short description should explain the image based on the prompt, acknowledge that the puzzle was made by {$userName} and that it was made on the Make a Puzzle platform. The short description should be very clear and concise, explaining exactly what the image is. No marketing language. Title should be succinct and descriptive."
                ]
            ],
            'max_tokens' => 100,
            'n' => 1,
            'stop' => ['\n']
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to get a successful response from OpenAI API: ' . $response->body());
        }

        $openaiResult = $response->json();
        $generatedText = $openaiResult['choices'][0]['message']['content'];

        // Extract title and short description
        $generatedLines = explode("\n", trim($generatedText));
        $title = str_replace('Title:', '', $generatedLines[0]);
        $title = str_replace('"', '', $title);
        $shortDescription = str_replace('Description:', '', implode(' ', array_slice($generatedLines, 1)));

        // Detailed Description
        $description = "Completed puzzle dimensions: 20.5” by 15”
Individual pieces dimensions: 0.75” by 1.25”
Puzzle style: Ribbon cut
Puzzle finish: High quality gloss on textured card stock
Packaging: High quality paper sleeve and reusable box
Shipping material: Off-white polymailer

—

We are Concord Puzzle, a small puzzle maker located in Concord, Massachusetts. We’re known for our dedication to crafting high-quality and engaging puzzles, with a focus on wholesome entertainment. Our unique packaging style reflects our homemade touch, and we prioritize customer interaction and feedback to continually improve our products. With a commitment to creating memorable experiences, we aim to provide joy and satisfaction to puzzle enthusiasts of all ages.

<a href=\"http://rb.gy/reo16m\"><img class=\"wp-image-824 alignleft\" src=\"http://concordpuzzle.com/wp-content/uploads/2024/03/cropped-Concord-Puzzle-91-300x300.png\" alt=\"\" width=\"45\" height=\"45\" /></a>

<a class=\"button product_type_simple add_to_cart_button\" href=\"http://rb.gy/reo16m\" rel=\"nofollow\" data-quantity=\"1\" aria-label=\"Add to cart: “Jaguar 500 Piece Puzzle”\" aria-describedby=\"\">Browse hundreds of puzzles</a>

For questions regarding our puzzles, email us <a href=\"mailto:jeremy@concordpuzzle.com\">here</a>.";

        $woocommerce = new \Automattic\WooCommerce\Client(
            env('WOOCOMMERCE_STORE_URL'),
            env('WOOCOMMERCE_CONSUMER_KEY'),
            env('WOOCOMMERCE_CONSUMER_SECRET'),
            [
                'version' => 'wc/v3',
            ]
        );

        $relativeUrl = Storage::url($image->generated_image);
        $publicUrl = url($relativeUrl);

        // URL of the additional images to overlay
        $overlayImageUrl = 'http://concordpuzzle.com/wp-content/uploads/2024/06/Concord-Puzzle-16.png';
        $additionalImage1 = 'https://concordpuzzle.com/wp-content/uploads/2024/04/Concord-Puzzle-2024-06-07T114127.702-768x534.png';
        $additionalImage2 = 'https://concordpuzzle.com/wp-content/uploads/2024/04/Tight-fit.-2024-04-30T135654.378-416x256.png';

        // Log the paths
        Log::info('Main Image Path: ' . Storage::path('public/' . $image->generated_image));
        Log::info('Overlay Image URL: ' . $overlayImageUrl);

        // Crop the original image to 1.436:1 ratio and overlay the additional image
        try {
            $mainImagePath = Storage::path('public/' . $image->generated_image);
            if (!file_exists($mainImagePath)) {
                throw new \Exception('Main image file does not exist at path: ' . $mainImagePath);
            }

            $mainImage = Image::make($mainImagePath);
            
            // Crop to 1.436:1 ratio
            $cropWidth = $mainImage->width();
            $cropHeight = round($cropWidth / 1.436);
            if ($cropHeight > $mainImage->height()) {
                $cropHeight = $mainImage->height();
                $cropWidth = round($cropHeight * 1.436);
            }
            $mainImage->crop($cropWidth, $cropHeight);

            // Save the cropped image locally
            $croppedImageName = 'generated_images/cropped_' . uniqid() . '.png';
            $mainImage->save(Storage::path('public/' . $croppedImageName));

            // Load the overlay image
            $overlayImageContents = @file_get_contents($overlayImageUrl);
            if ($overlayImageContents === false) {
                throw new \Exception('Overlay image URL is not accessible: ' . $overlayImageUrl);
            }

            $overlayImage = Image::make($overlayImageContents)->resize($mainImage->width(), $mainImage->height());
            $mainImage->insert($overlayImage, 'center');
            $overlayedImageName = 'generated_images/overlayed_' . uniqid() . '.png';
            $overlayedImagePath = Storage::path('public/' . $overlayedImageName);
            $mainImage->save($overlayedImagePath);

            // Generate public URL for the overlayed image
            $overlayedPublicUrl = Storage::url($overlayedImageName);

        } catch (\Exception $e) {
            Log::error('Error generating overlayed image', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->with('error', 'Failed to generate overlayed image.');
        }

        // Add the product to the WooCommerce store
        $data = [
            'name' => $title,
            'short_description' => $shortDescription,
            'description' => $description,
            'images' => [
                ['src' => url($overlayedPublicUrl)],
                ['src' => $publicUrl],
                ['src' => $additionalImage1],
                ['src' => $additionalImage2],
            ],
            'type' => 'simple',
            'regular_price' => '14.93',
            'categories' => [
                ['id' => 1712], // Replace with the ID of the "Community Made" category
                ['id' => 17]  // Replace with the ID of the "500 Piece Puzzle" category
            ],
        ];

        $product = $woocommerce->post('products', $data);

        // Save the product information to the database
        PublishedProduct::create([
            'image_id' => $image->id,
            'user_id' => $user->id,
            'title' => $title,
            'description' => $description,
            'product_id' => $product->id,
            'product_url' => $product->permalink,
            'cropped_image' => $croppedImageName, // Save the cropped image name
        ]);

        // Get the product URL
        $productUrl = $product->permalink;
        return redirect($productUrl);

    } catch (HttpClientException $e) {
        $error = $e->getMessage();
        Log::error("Error creating product: cURL Error: $error");
        return back()->with('error', $error);
    } catch (\Exception $e) {
        Log::error('Error generating title and description', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        return back()->with('error', 'Failed to generate title and description.');
    }
}

        // Other methods..
public function puzzleFeed()
{
    $publishedProducts = PublishedProduct::with('user', 'likes')
        ->withCount('likes')
        ->orderBy('likes_count', 'desc')
        ->get();
    return view('cp_image_generation.puzzle_feed', compact('publishedProducts'));
}



public function crop(Request $request)
{
    Log::info('Cropping image request received', ['request' => $request->all()]);

    $request->validate([
        'cropped_image' => 'required|image',
        'original_image_id' => 'required|integer', // Ensure original image ID is provided
    ]);

    try {
        $croppedImage = $request->file('cropped_image');
        Log::info('Cropped image file details', ['file' => $croppedImage]);

        $path = $croppedImage->store('public/generated_images');
        Log::info('Cropped image saved successfully', ['path' => $path]);

        // Remove 'public/' from the path before storing in the database
        $storedPath = str_replace('public/', '', $path);

        // Fetch the original image's prompt
        $originalImage = CPImageGeneration::findOrFail($request->input('original_image_id'));
        
        // Save cropped image information to database
        $image = CPImageGeneration::create([
            'user_id' => auth()->user()->id, // Associate with the authenticated user
            'prompt' => $originalImage->prompt, // Retain the original prompt
            'generated_image' => $storedPath,
            'image_type' => 'cropped',
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

    public function likeProduct(Request $request)
{
    $productId = $request->input('product_id');
    $userId = auth()->user()->id;

    // Check if the user already liked this product
    $existingLike = Like::where('user_id', $userId)->where('product_id', $productId)->first();

    if ($existingLike) {
        return response()->json(['success' => false, 'message' => 'You already liked this product']);
    }

    // Add a new like
    Like::create([
        'user_id' => $userId,
        'product_id' => $productId
    ]);

    // Update the likes count on the product
    $product = PublishedProduct::find($productId);
    $product->likes_count = $product->likes->count();
    $product->save();

    return response()->json(['success' => true, 'likes_count' => $product->likes_count]);
}

    
public function unlikeProduct(Request $request)
{
    $productId = $request->input('product_id');
    $userId = auth()->user()->id;

    // Check if the user already liked this product
    $existingLike = Like::where('user_id', $userId)->where('product_id', $productId)->first();

    if (!$existingLike) {
        return response()->json(['success' => false, 'message' => 'You have not liked this product']);
    }

    // Remove the like
    $existingLike->delete();

    // Update the likes count on the product
    $product = PublishedProduct::find($productId);
    $product->likes_count = $product->likes->count();
    $product->save();

    return response()->json(['success' => true, 'likes_count' => $product->likes_count]);
}

public function welcomeFeed()
{
    $publishedProducts = PublishedProduct::with('user')->get();
    $popularProducts = PublishedProduct::with('user')
                        ->withCount('likes')
                        ->orderBy('likes_count', 'desc')
                        ->take(20)
                        ->get();
    return view('welcome', compact('publishedProducts', 'popularProducts'));
}


}