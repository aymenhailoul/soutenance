<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('serial_number', 100)->unique()->nullable();
            $table->string('asset_tag', 100)->unique()->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('warranty_end_date')->nullable();
            $table->enum('status', ['Available', 'Assigned', 'In Maintenance', 'Broken', 'Retired', 'Lost'])->default('Available');
            $table->enum('condition', ['New', 'Good', 'Fair', 'Damaged'])->default('Good');
            $table->unsignedBigInteger('site_id')->nullable();
            $table->text('notes')->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('min_stock')->default(0);
            $table->boolean('is_consumable')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
