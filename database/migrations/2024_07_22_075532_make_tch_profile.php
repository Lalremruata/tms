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
        Schema::table('tch_users', function (Blueprint $table) {
                $table->id();
                $table->string('passw')->nullable();
                $table->integer('init')->default(1);
                $table->string('classes')->nullable();
                $table->string('sections')->nullable();
                $table->rememberToken();
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tch_users', function (Blueprint $table) {
            //
        });
    }
};
