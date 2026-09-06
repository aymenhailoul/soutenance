<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\Page;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Equipment $consumable;
    protected Site $siteA;
    protected Site $siteB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $page = Page::create(['name' => 'Stock', 'route' => 'stock.index']);
        $this->user->pages()->attach($page->id);

        $this->siteA = Site::create(['name' => 'Casablanca HQ']);
        $this->siteB = Site::create(['name' => 'Rabat Branch']);

        $this->consumable = Equipment::create([
            'name' => 'Câble Ethernet RJ45 5m',
            'is_consumable' => true,
            'quantity' => 10,
            'min_stock' => 5,
            'status' => 'Available',
            'condition' => 'New',
        ]);
    }

    public function test_stock_entry_increases_quantity(): void
    {
        $response = $this->actingAs($this->user)->post(route('stock.store'), [
            'equipment_id' => $this->consumable->id,
            'movement' => 'Entrée',
            'quantity' => 5,
            'prix_achat' => 15.00,
            'comment' => 'Livraison fournisseur',
        ]);

        $response->assertRedirect(route('stock.index'));
        $this->assertEquals(15, $this->consumable->fresh()->quantity);
    }

    public function test_stock_exit_decreases_quantity(): void
    {
        $response = $this->actingAs($this->user)->post(route('stock.store'), [
            'equipment_id' => $this->consumable->id,
            'movement' => 'Sortie',
            'quantity' => 4,
            'comment' => 'Utilisé pour installation',
        ]);

        $response->assertRedirect(route('stock.index'));
        $this->assertEquals(6, $this->consumable->fresh()->quantity);
    }

    public function test_stock_exit_exceeding_available_stock_is_rejected(): void
    {
        $response = $this->actingAs($this->user)->post(route('stock.store'), [
            'equipment_id' => $this->consumable->id,
            'movement' => 'Sortie',
            'quantity' => 20, // Only 10 available!
            'comment' => 'Demande excessive',
        ]);

        $response->assertSessionHasErrors('quantity');
        $this->assertEquals(10, $this->consumable->fresh()->quantity); // Stock remains unchanged!
    }

    public function test_stock_transfer_between_sites_records_locations_and_updates_site_stocks(): void
    {
        // First initialize stock at siteA
        \App\Models\SiteStock::create([
            'equipment_id' => $this->consumable->id,
            'site_id' => $this->siteA->id,
            'quantity' => 10,
        ]);

        $response = $this->actingAs($this->user)->post(route('stock.store'), [
            'equipment_id' => $this->consumable->id,
            'movement' => 'Transfert',
            'quantity' => 3,
            'source_site_id' => $this->siteA->id,
            'destination_site_id' => $this->siteB->id,
            'comment' => 'Transfert Casa vers Rabat',
        ]);

        $response->assertRedirect(route('stock.index'));

        $this->assertDatabaseHas('stock_movements', [
            'equipment_id' => $this->consumable->id,
            'movement' => 'Transfert',
            'quantity' => 3,
            'source_site_id' => $this->siteA->id,
            'destination_site_id' => $this->siteB->id,
        ]);

        // Source site decreased from 10 to 7
        $this->assertDatabaseHas('site_stocks', [
            'equipment_id' => $this->consumable->id,
            'site_id' => $this->siteA->id,
            'quantity' => 7,
        ]);

        // Destination site increased from 0 to 3
        $this->assertDatabaseHas('site_stocks', [
            'equipment_id' => $this->consumable->id,
            'site_id' => $this->siteB->id,
            'quantity' => 3,
        ]);

        // Total equipment quantity remains 10
        $this->assertEquals(10, $this->consumable->fresh()->quantity);
    }

    public function test_bulk_stock_movement_on_non_consumable_is_rejected(): void
    {
        $laptop = Equipment::create([
            'name' => 'Dell Latitude 5420',
            'is_consumable' => false,
            'quantity' => 1,
            'status' => 'Available',
            'condition' => 'New',
        ]);

        $response = $this->actingAs($this->user)->post(route('stock.store'), [
            'equipment_id' => $laptop->id,
            'movement' => 'Entrée',
            'quantity' => 5,
            'comment' => 'Essai d\'ajout en masse',
        ]);

        $response->assertSessionHasErrors('movement');
        $this->assertEquals(1, $laptop->fresh()->quantity);
    }

    public function test_transfer_with_same_source_and_destination_is_rejected(): void
    {
        $response = $this->actingAs($this->user)->post(route('stock.store'), [
            'equipment_id' => $this->consumable->id,
            'movement' => 'Transfert',
            'quantity' => 2,
            'source_site_id' => $this->siteA->id,
            'destination_site_id' => $this->siteA->id,
        ]);

        $response->assertSessionHasErrors('destination_site_id');
    }
}
