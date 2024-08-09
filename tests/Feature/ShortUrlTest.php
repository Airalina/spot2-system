<?php

namespace Tests\Feature;

use App\Models\ShortUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index_returns_short_url_collection()
    {
        ShortUrl::factory(3)->create();
        $response = $this->get(route('short-urls.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'original_url', 'code']
            ]
        ]);
    }

    public function test_can_store_short_url()
    {
        $url = $this->faker->url;
        $response = $this->postJson(route('short-urls.store'), [
            'original_url' => $url,
        ]);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'original_url',
                    'code',
                ]
            ]);
        $this->assertDatabaseHas('short_urls', [
            'original_url' => $url
        ]);
    }

    /** @test */
    public function test_can_show_short_url()
    {
        $shortUrl = ShortUrl::factory()->create();
        $response = $this->getJson(route('short-urls.show', $shortUrl->code));
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $shortUrl->id,
                'code' => $shortUrl->code,
            ],
        ]);
    }

    public function test_can_destroy_short_url()
    {
        // Crear un ShortUrl de prueba
        $shortUrl = ShortUrl::factory()->create();

        $response = $this->deleteJson(route('short-urls.destroy', $shortUrl->code));
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $shortUrl->id,
                'code' => $shortUrl->code,
            ],
        ]);
        $this->assertDatabaseMissing('short_urls', ['id' => $shortUrl->id]);
    }
}
