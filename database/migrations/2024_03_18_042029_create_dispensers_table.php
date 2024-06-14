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
        Schema::create('dispensers', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->double('balance')->default(0);
            $table->text('pk');
            $table->boolean('used')->default(false);
            $table->foreignId('ticker_id');
            $table->foreignId('customer_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispensers');
    }
};
