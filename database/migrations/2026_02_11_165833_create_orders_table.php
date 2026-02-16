<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('address_line');
            $table->string('city');
            $table->string('postal_code', 20);
            $table->string('country');

            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');

            $table->decimal('total', 10, 2);

            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_payment_intent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
