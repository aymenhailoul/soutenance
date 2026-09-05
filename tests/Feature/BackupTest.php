<?php

namespace Tests\Feature;

use App\Models\Backup;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->user = User::factory()->create();

        $backupPage = Page::create(['name' => 'Sauvegardes', 'route' => 'backups.index']);
        $this->user->pages()->attach([$backupPage->id]);
    }

    public function test_can_list_backups_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('backups.index'));

        $response->assertStatus(200);
        $response->assertSee('Historique des Sauvegardes');
    }

    public function test_can_trigger_database_backup(): void
    {
        $response = $this->actingAs($this->user)->post(route('backups.store'));

        $response->assertRedirect(route('backups.index'));

        $this->assertDatabaseHas('backups', [
            'status' => 'completed',
            'disk' => 'local',
        ]);
    }

    public function test_can_delete_backup(): void
    {
        $backup = Backup::create([
            'filename' => 'backup_test.sqlite',
            'disk' => 'local',
            'size' => 1024,
            'type' => 'database',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->user)->delete(route('backups.destroy', $backup));

        $response->assertRedirect(route('backups.index'));
        $this->assertDatabaseMissing('backups', ['id' => $backup->id]);
    }
}
