<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 30)->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->date('sale_date');
            $table->enum('status', ['draft', 'completed', 'cancelled', 'refunded'])->default('completed');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('change_amount', 15, 2)->default(0);
            $table->integer('points_earned')->default(0);
            $table->integer('points_used')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('invoice_number');
            $table->index('status');
            $table->index('sale_date');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
