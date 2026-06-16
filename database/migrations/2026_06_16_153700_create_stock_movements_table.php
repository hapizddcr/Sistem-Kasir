<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment', 'sale', 'purchase', 'return', 'opname']);
            $table->integer('quantity');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['product_id', 'type']);
            $table->index('reference_type');
            $table->index('reference_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
