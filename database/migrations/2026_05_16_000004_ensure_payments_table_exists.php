<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            return;
        }

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('transaction_id', 64);
            $table->unsignedInteger('amount');
            $table->string('type', 20);
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->cascadeOnDelete();

            $table->unique(['order_id', 'transaction_id', 'type'], 'payments_order_tx_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
