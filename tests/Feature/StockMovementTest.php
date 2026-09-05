<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\Page;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Equipment $consumable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $page = Page::create(['name' => 'Stock & Consommables', 'route' => 'stock.index']);
        $this->user->pages()->attach($page->id);

        $this->consumable = Equipment::create([
            'name' => 'Cable RJ45 Cat6 2m',
            'is_consumable' => true,
            'quantity' => 50,
            'min_stock' => 10,
        ]);
    }

    public function test_can_list_stock_movements(): void
    {
        StockMovement::create([
            'equipment_id' => $this->consumable->id,
            'movement' => 'Entrée',
            'quantity' => 20,
            'prix_achat' => 15.00,
            'montant' => 300.00,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('stock.index'));

        $response->assertStatus(200);
        $response->assertSee('Cable RJ45 Cat6 2m');
        $response->assertSee('Entrée');
    }

    public function test_can_create_stock_movement(): void
    {
        $response = $this->actingAs($this->user)->post(route('stock.store'), [
            'equipment_id' => $this->consumable->id,
            'movement' => 'Entrée',
            'quantity' => 10,
            'prix_achat' => 15.00,
            'comment' => 'Achat lot de 10 cables',
        ]);

        $response->assertRedirect(route('stock.index'));

        $this->assertDatabaseHas('stock_movements', [
            'equipment_id' => $this->consumable->id,
            'movement' => 'Entrée',
            'quantity' => 10,
        ]);
    }
}
