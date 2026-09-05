<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $page = Page::create(['name' => 'Catégories', 'route' => 'categories.index']);
        $this->user->pages()->attach($page->id);
    }

    public function test_can_list_categories(): void
    {
        Category::create(['name' => 'Serveurs', 'description' => 'Matériel serveur']);

        $response = $this->actingAs($this->user)->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Serveurs');
    }

    public function test_can_create_category(): void
    {
        $response = $this->actingAs($this->user)->post(route('categories.store'), [
            'name' => 'Réseau',
            'description' => 'Switches et routeurs',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Réseau']);
    }

    public function test_can_update_category(): void
    {
        $category = Category::create(['name' => 'Ancien nom']);

        $response = $this->actingAs($this->user)->put(route('categories.update', $category), [
            'name' => 'Nouveau nom',
            'description' => 'Mise à jour description',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Nouveau nom']);
    }

    public function test_can_delete_category_without_equipment(): void
    {
        $category = Category::create(['name' => 'À supprimer']);

        $response = $this->actingAs($this->user)->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
