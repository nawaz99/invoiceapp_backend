<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'user_id')) {
                $table->foreignId('user_id')
                    ->after('id')
                    ->constrained()
                    ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
}
