<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('nik')->after('name')->nullable();
            $table->unsignedBigInteger('departemen_id')->after('name')->nullable();
            $table->string('jenis_kelamin')->after('name')->nullable();
            $table->timestamp('tanggal_lahir')->after('name')->nullable();
            $table->string('foto_profil')->after('id')->nullable();
            $table->string('roles')->after('password')->nullable()->default('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nik');
            $table->dropColumn('departemen_id');
            $table->dropColumn('jenis_kelamin');
            $table->dropColumn('tanggal_lahir');
            $table->dropColumn('roles');
        });
    }
};
