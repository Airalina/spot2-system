<?php

namespace App\Observers;

use App\Models\ShortUrl;

class ShortUrlObserver
{
    /**
     * Handle the ShortUrl "creating" event.
     *
     * @param  \App\Models\ShortUrl  $shortUrl
     * @return void
     */
    public function creating(ShortUrl $shortUrl)
    {
        $shortUrl->code = $shortUrl->generateUniqueCode();
    }
}
