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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barcodes_id')->constrained('barcodes')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('phone');
            $table->string('external_id');
            $table->string('checkout_link');
            $table->double('subtotal');
            $table->double('ppn');
            $table->double('total');
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
