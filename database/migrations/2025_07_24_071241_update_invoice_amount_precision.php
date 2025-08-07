<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInvoiceAmountPrecision extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('tax', 15, 2)->change();
            $table->decimal('total', 15, 2)->change();
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('subtotal', 8, 2)->change();
            $table->decimal('tax', 8, 2)->change();
            $table->decimal('total', 8, 2)->change();
        });
    }
}
