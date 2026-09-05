<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Equipment;
use App\Models\Page;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $page = Page::create(['name' => 'Sites', 'route' => 'sites.index']);
        $this->user->pages()->attach($page->id);

        $this->client = Client::create([
            'name' => 'Client Maroc Telecom',
            'code' => 'CLI-IAM',
        ]);
    }

    public function test_can_list_sites(): void
    {
        Site::create([
            'name' => 'Siege Rabat',
            'client_id' => $this->client->id,
            'city' => 'Rabat',
        ]);

        $response = $this->actingAs($this->user)->get(route('sites.index'));

        $response->assertStatus(200);
        $response->assertSee('Siege Rabat');
    }

    public function test_can_create_site(): void
    {
        $response = $this->actingAs($this->user)->post(route('sites.store'), [
            'name' => 'Agence Casablanca',
            'client_id' => $this->client->id,
            'code' => 'SITE-CAS-01',
            'city' => 'Casablanca',
            'contact_name' => 'M. Bennani',
            'contact_phone' => '+212 600 000000',
        ]);

        $response->assertRedirect(route('sites.index'));
        $this->assertDatabaseHas('sites', [
            'name' => 'Agence Casablanca',
            'code' => 'SITE-CAS-01',
        ]);
    }

    public function test_can_update_site(): void
    {
        $site = Site::create([
            'name' => 'Ancien Site',
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('sites.update', $site), [
            'name' => 'Nouveau Site Modifié',
            'client_id' => $this->client->id,
            'city' => 'Tanger',
        ]);

        $response->assertRedirect(route('sites.index'));
        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'name' => 'Nouveau Site Modifié',
            'city' => 'Tanger',
        ]);
    }

    public function test_can_delete_site_without_equipment(): void
    {
        $site = Site::create([
            'name' => 'Site À Supprimer',
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('sites.destroy', $site));

        $response->assertRedirect(route('sites.index'));
        $this->assertDatabaseMissing('sites', ['id' => $site->id]);
    }
}
