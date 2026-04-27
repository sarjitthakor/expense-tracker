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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('category_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->timestamp('date')->useCurrent();
            $table->boolean('is_recurring')->default(false);    

             $table->enum('recurring_frequency', [
                'daily',
                'weekly',
                'monthly',
                'yearly'
            ])->nullable();

            $table->string('currency', 10)->default('INR');

            $table->timestamps();

            // 🔥 Performance indexes
            $table->index('user_id');
            $table->index('category_id');
            $table->index('date');
            

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
