<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProviderUserIdColumnInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider_user_id')->nullable();
            $table->string('password')->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->string('stu_id')->nullable()->change();
            $table->string('major')->nullable()->change();
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
            $table->dropColumn('provider_user_id');
            $table->string('password')->change();
            $table->string('name')->change();
            $table->string('stu_id')->change();
            $table->string('major')->change();
        });
    }
}
