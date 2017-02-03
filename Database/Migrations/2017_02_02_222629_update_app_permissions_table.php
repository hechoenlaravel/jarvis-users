<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAppPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_permissions', function (Blueprint $table) {
            $table->string('module');
        });
        Schema::table('app_roles', function (Blueprint $table) {
            $table->string('module');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_permissions', function (Blueprint $table) {
            $table->dropColumn('module');
        });
        Schema::table('app_roles', function (Blueprint $table) {
            $table->dropColumn('module');
        });
    }
}
