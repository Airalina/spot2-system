<?php

namespace App\Models;

use App\Observers\ShortUrlObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([ShortUrlObserver::class])]
class ShortUrl extends Model
{
    use HasFactory;
        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['original_url', 'code'];

    public function generateUniqueCode()
    {
        $parsedUrl = parse_url($this->original_url);
        $pathLength = isset($parsedUrl['path']) ? strlen($parsedUrl['path']) : 0;
        $length = max(8, min(6, $pathLength));
        $code = Str::random($length);

        while (self::where('code', $code)->exists()) {
            $code = Str::random($length);
        }
        return $code;
    }

}
