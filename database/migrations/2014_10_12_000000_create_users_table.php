<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('pin_no')->nullable();
            $table->string('avatar')->nullable()->comment('profile picture');
            $table->string('user_role')->nullable()->comment('user,admin');
            $table->tinyInteger('status')->default(0)->comment('1=active,0=in active');
            $table->tinyInteger('is_invite')->default(0)->comment('0=invitation,1=registered user');
            $table->timestamp('registered_at')->comment('pin no verify at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
