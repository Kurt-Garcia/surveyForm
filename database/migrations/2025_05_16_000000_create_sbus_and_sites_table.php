<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sbus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sbu_id')->constrained('sbus')->onDelete('cascade');
            $table->string('name');
            $table->boolean('is_main')->default(false);
            $table->timestamps();
        });

        // Insert default SBUs
        DB::table('sbus')->insert([
            ['name' => 'FDC', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'FUI', 'created_at' => now(), 'updated_at' => now()]
        ]);

        // Get SBU IDs
        $fdcId = DB::table('sbus')->where('name', 'FDC')->value('id');
        $fuiId = DB::table('sbus')->where('name', 'FUI')->value('id');

        // Insert FDC sites
        DB::table('sites')->insert([
            ['sbu_id' => $fdcId, 'name' => 'FDC Tagbilaran', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fdcId, 'name' => 'FDC Ubay', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            
            ['sbu_id' => $fdcId, 'name' => 'FDC Tacloban', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fdcId, 'name' => 'FDC Ormoc', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fdcId, 'name' => 'FDC Sogod', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            
            ['sbu_id' => $fdcId, 'name' => 'FDC Calbayog', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fdcId, 'name' => 'FDC Bogongan', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fdcId, 'name' => 'FDC Catarman', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            
            ['sbu_id' => $fdcId, 'name' => 'FDC Roxas', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fdcId, 'name' => 'FDC Kalibo', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            
            ['sbu_id' => $fdcId, 'name' => 'FDC Gensan', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fdcId, 'name' => 'FDC Koronadal', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            
            ['sbu_id' => $fdcId, 'name' => 'FDC CDO', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fdcId, 'name' => 'FDC Valencia', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fdcId, 'name' => 'FDC Iligan', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            
            ['sbu_id' => $fdcId, 'name' => 'FDC RX/RO', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fdcId, 'name' => 'FDC Cebu', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fdcId, 'name' => 'FDC Davao', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            
            ['sbu_id' => $fdcId, 'name' => 'FDC Punturin', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fdcId, 'name' => 'FDC Bignay', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()]
        ]);

        // Insert FUI sites
DB::table('sites')->insert([
            ['sbu_id' => $fuiId, 'name' => 'NAI Cebu', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fuiId, 'name' => 'NAI Bohol', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            
            ['sbu_id' => $fuiId, 'name' => 'NAI Iloilo', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fuiId, 'name' => 'NAI Roxas', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            
            ['sbu_id' => $fuiId, 'name' => 'NAI Bacolod', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fuiId, 'name' => 'NAI Dumaguete', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            
            ['sbu_id' => $fuiId, 'name' => 'NAI Leyte', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fuiId, 'name' => 'NAI Samar', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fuiId, 'name' => 'NAI Borongan', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            
            ['sbu_id' => $fuiId, 'name' => 'MNC Cebu', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fuiId, 'name' => 'MNC Bohol', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            
            ['sbu_id' => $fuiId, 'name' => 'MNC Ozamiz', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fuiId, 'name' => 'MNC Dipolog', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            
            ['sbu_id' => $fuiId, 'name' => 'Shell Cebu', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fuiId, 'name' => 'Shell Bohol', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            
            ['sbu_id' => $fuiId, 'name' => 'Shell Leyte', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fuiId, 'name' => 'Shell Samar', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()],
            
            ['sbu_id' => $fuiId, 'name' => 'Shell Negros', 'is_main' => true, 'created_at' => now(), 'updated_at' => now()],
            ['sbu_id' => $fuiId, 'name' => 'Shell Panay', 'is_main' => false, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('sites');
        Schema::dropIfExists('sbus');
    }
};