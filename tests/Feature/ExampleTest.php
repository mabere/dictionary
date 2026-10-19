<?php

namespace Tests\Feature;

use App\Models\DictionaryVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_public_layout_shows_visitor_summary(): void
    {
        DictionaryVisit::create([
            'path' => 'dictionary',
            'route_name' => 'dictionary.index',
            'keyword' => 'laika',
            'ip_hash' => str_repeat('a', 64),
            'user_agent_hash' => str_repeat('b', 64),
            'occurred_on' => today(),
        ]);

        DictionaryVisit::create([
            'path' => 'dictionary/1',
            'route_name' => 'dictionary.show',
            'dictionary_entry_id' => null,
            'ip_hash' => str_repeat('a', 64),
            'user_agent_hash' => str_repeat('c', 64),
            'occurred_on' => today(),
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Visitor publik')
            ->assertSee('Hari ini')
            ->assertSee('>1</strong>', false);
    }
}
