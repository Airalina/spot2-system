<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShortUrlRequest;
use App\Http\Resources\ShortUrlCollection;
use App\Http\Resources\ShortUrlResource;
use App\Models\ShortUrl;
use App\Services\ShortUrlCacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * @OA\Info(
 *     title="Short URL API",
 *     version="1.0.0"
 * )
 */
class ShortUrlController extends Controller
{
    protected $cacheService;

    public function __construct(ShortUrlCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * @OA\Get(
     *     path="/api/short-urls",
     *     summary="Get list of shortened URLs",
     *     @OA\Response(
     *         response=200,
     *         description="A list of shortened URLs",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="original_url", type="string", example="https://example.com"),
     *                 @OA\Property(property="code", type="string", example="abc123")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An internal server error occurred.")
     *         )
     *     )
     * )
     */
    public function index(): ShortUrlCollection
    {
        $urls = $this->cacheService->getAllShortUrls();
        return new ShortUrlCollection($urls);
    }

    /**
     * @OA\Post(
     *     path="/api/short-urls",
     *     summary="Create a new shortened URL",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"original_url"},
     *             @OA\Property(property="original_url", type="string", example="https://example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="The created shortened URL",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="original_url", type="string", example="https://example.com"),
     *             @OA\Property(property="code", type="string", example="abc123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="original_url",
     *                     type="array",
     *                     @OA\Items(type="string", example="The original url field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An internal server error occurred.")
     *         )
     *     )
     * )
     */
    public function store(StoreShortUrlRequest $request): ShortUrlResource
    {
        $dataValidated = $request->validated();
        $shortUrl = ShortUrl::create([
            'original_url' => $dataValidated['original_url'],
        ]);

        return new ShortUrlResource($shortUrl);
    }

    /**
     * @OA\Get(
     *     path="/api/short-urls/{code}",
     *     summary="Get the original URL by code",
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The original URL",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="original_url", type="string", example="https://example.com"),
     *             @OA\Property(property="code", type="string", example="abc123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="URL not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Resource not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An internal server error occurred.")
     *         )
     *     )
     * )
     */
    public function show($code): ShortUrlResource
    {
        $shortUrl = $this->cacheService->getShortUrl($code);
        return new ShortUrlResource($shortUrl);
    }

    /**
     * @OA\Delete(
     *     path="/api/short-urls/{code}",
     *     summary="Delete a shortened URL",
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="URL deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="URL not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Resource not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An internal server error occurred.")
     *         )
     *     )
     * )
     */
    public function destroy($code): ShortUrlResource
    {
        $shortUrl = $this->cacheService->getShortUrl($code);
        $shortUrl->delete();

        return new ShortUrlResource($shortUrl);
    }
    //  $url = Cache::remember("short_url_{$code}", 60, function () use ($code) {
    //     return ShortUrl::where('code', $code)->firstOrFail();
    // });
}
