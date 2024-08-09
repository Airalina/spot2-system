<?php

namespace App\Services;

use App\Models\ShortUrl;
use Illuminate\Support\Facades\Cache;

class ShortUrlCacheService
{
    public function getShortUrl($code)
    {
        return Cache::remember("short_url_{$code}", 30, function () use ($code) {
            return ShortUrl::where('code', $code)->firstOrFail();
        });
    }

    public function forgetShortUrl($code)
    {
        Cache::forget("short_url_{$code}");
    }

    public function getAllShortUrls()
    {
        return Cache::remember('short_urls_all', 30, function () {
            return ShortUrl::all();
        });
    }
}