<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanySettingsTable extends Migration
{
    public function up()
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique(); // One company per user
            $table->string('company_name');
            $table->string('phone')->nullable();
            $table->string('gstin')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable(); // Path to uploaded logo
            $table->text('address')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_settings');
    }
}
