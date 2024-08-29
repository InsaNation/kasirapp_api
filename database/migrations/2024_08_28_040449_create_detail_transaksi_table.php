<?php

// database/migrations/xxxx_xx_xx_create_detail_transaksi_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailTransaksiTable extends Migration
{
    public function up()
    {
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->constrained('transaksi')->onDelete('cascade');
            $table->string('product_name');
            $table->decimal('product_price', 15, 2);
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_transaksi');
    }
}
