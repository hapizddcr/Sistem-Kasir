<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number', 30)->unique();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->date('return_date');
            $table->enum('reason', ['damaged', 'wrong_item', 'expired', 'other'])->default('other');
            $table->text('notes')->nullable();
            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index('return_number');
            $table->index('return_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_returns');
    }
};
