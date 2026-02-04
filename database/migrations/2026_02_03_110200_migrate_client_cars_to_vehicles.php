<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migrates existing car_brand and matricule from clients to vehicles table.
     */
    public function up(): void
    {
        // Get all clients with car info
        $clients = DB::table('clients')
            ->whereNotNull('matricule')
            ->orWhereNotNull('car_brand')
            ->get();

        foreach ($clients as $client) {
            // Only migrate if there's a matricule (required for vehicle)
            if ($client->matricule) {
                // Check if vehicle with this plaque already exists
                $exists = DB::table('vehicles')->where('plaque', $client->matricule)->exists();
                
                if (!$exists) {
                    DB::table('vehicles')->insert([
                        'client_id' => $client->id,
                        'plaque' => $client->matricule,
                        'marque' => $client->car_brand ?? 'Inconnu',
                        'modele' => null,
                        'annee' => null,
                        'kilometrage' => 0,
                        'carburant' => 'essence',
                        'couleur' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Remove old columns from clients table
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['car_brand', 'matricule']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the columns
        Schema::table('clients', function (Blueprint $table) {
            $table->string('car_brand')->nullable();
            $table->string('matricule')->nullable();
        });

        // Note: Data migration back would need to be handled manually
    }
};
