<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('admin_users', 'is_seeder')) {
            Schema::table('admin_users', function (Blueprint $table) {
                $table->renameColumn('is_seeder', 'superadmin');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('admin_users', 'superadmin')) {
            Schema::table('admin_users', function (Blueprint $table) {
                $table->renameColumn('superadmin', 'is_seeder');
            });
        }
    }
};