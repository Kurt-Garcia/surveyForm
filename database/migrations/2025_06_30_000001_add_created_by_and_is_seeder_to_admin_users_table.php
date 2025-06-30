<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('admin_users', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('id');
            $table->boolean('is_seeder')->default(false)->after('created_by');
            $table->foreign('created_by')->references('id')->on('admin_users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['created_by', 'is_seeder']);
        });
    }
};
