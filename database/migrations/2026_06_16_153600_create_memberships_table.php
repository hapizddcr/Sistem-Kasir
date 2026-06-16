<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('slug', 60)->unique();
            $table->decimal('min_spent', 15, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->integer('points_multiplier')->default(1);
            $table->string('color', 20)->default('#6b7280');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
