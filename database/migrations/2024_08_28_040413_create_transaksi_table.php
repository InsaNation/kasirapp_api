<?php

// database/migrations/xxxx_xx_xx_create_transaksi_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiTable extends Migration
{
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_code')->unique();
            $table->integer('total_items');
            $table->decimal('total_price', 15, 2);
            $table->decimal('change', 10, 2); // Add change column
            $table->decimal('bayar', 10, 2); // Add bayar column
            $table->string('cashier')->nullable(); // Add cashier column
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
}

