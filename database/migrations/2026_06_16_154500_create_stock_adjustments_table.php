<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->date('adjustment_date');
            $table->enum('reason', ['opname', 'damage', 'expired', 'lost', 'found', 'correction', 'other']);
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'approved', 'rejected'])->default('approved');
            $table->timestamps();
            $table->softDeletes();
            $table->index('reference');
            $table->index('adjustment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
