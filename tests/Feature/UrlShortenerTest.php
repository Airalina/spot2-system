<?php

namespace Tests\Feature;

use App\Models\ShortUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UrlShortenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_short_url()
    {
        $response = $this->postJson('/api/short-urls', [
            'original_url' => 'https://www.original.com'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'original_url',
                         'code',
                     ]
                 ]);

        $this->assertDatabaseHas('short_urls', [
            'original_url' => 'https://www.original.com'
        ]);
    }

    public function test_can_redirect_to_original_url()
    {
        $shortUrl = ShortUrl::factory()->create();
        $response = $this->get('/12345');

        $response->assertRedirect('https://www.example.com');
    }

}
