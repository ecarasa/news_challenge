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
        Schema::create('reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('reservation_id');

            $table->string('id')->unique();
            $table->string('full_name');
            $table->string('email');
            $table->integer('number_of_guests');
            $table->enum('status', ['PAID', 'WITHDRAWN', 'EXPIRED', 'CANCELED', 'RETURNED']);
            $table->date('date');
            $table->decimal('amount', 8, 2)->default(220);
            $table->decimal('discount', 8, 2);
            $table->decimal('total_amount', 8, 2);
            $table->enum('payment_type', ['Cash', 'Credit Card', 'Online', 'Debit Card']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
