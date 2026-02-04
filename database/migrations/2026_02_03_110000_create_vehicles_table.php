<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('plaque');
            $table->string('marque');
            $table->string('modele')->nullable();
            $table->integer('annee')->nullable();
            $table->integer('kilometrage')->default(0);
            $table->enum('carburant', ['essence', 'diesel', 'hybride', 'electrique'])->default('essence');
            $table->string('couleur')->nullable();
            $table->timestamps();
            
            $table->unique(['plaque']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
