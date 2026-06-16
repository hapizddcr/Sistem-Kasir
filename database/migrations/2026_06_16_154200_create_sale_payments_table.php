<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->enum('method', ['cash', 'qris', 'transfer', 'card']);
            $table->decimal('amount', 15, 2);
            $table->string('reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('sale_id');
            $table->index('method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
    }
};
