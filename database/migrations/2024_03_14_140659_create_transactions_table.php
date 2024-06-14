<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('pairs');
            $table->string('receiver');
            $table->double('amount');
            $table->double('output');
            $table->double('revenue');
            $table->enum('status', ["Waiting", "Processing", "Done", "Failed"]);
            $table->foreignId('customer_id');
            $table->foreignId('dispenser_id');
            $table->timestamps();
        });

        DB::update("ALTER TABLE transactions AUTO_INCREMENT = 1100;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
