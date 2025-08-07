<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IncreaseInvoicePrecisionTo20 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE invoices MODIFY subtotal DECIMAL(20,2)');
        DB::statement('ALTER TABLE invoices MODIFY tax DECIMAL(20,2)');
        DB::statement('ALTER TABLE invoices MODIFY total DECIMAL(20,2)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE invoices MODIFY subtotal DECIMAL(15,2)');
        DB::statement('ALTER TABLE invoices MODIFY tax DECIMAL(15,2)');
        DB::statement('ALTER TABLE invoices MODIFY total DECIMAL(15,2)');
    }
}
