<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TBLCUSTOMER', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('MDCODE');
            $table->string('CUSTCODE');
            $table->string('CUSTNAME');
            $table->string('CONTACTCELLNUMBER')->nullable();
            $table->string('CONTACTPERSON')->nullable();
            $table->string('CONTACTLANDLINE')->nullable();
            $table->text('ADDRESS')->nullable();
            $table->integer('FREQUENCYCATEGORY')->nullable();
            $table->integer('MCPDAY')->nullable();
            $table->string('MCPSCHEDULE')->nullable();
            $table->string('GEOLOCATION')->nullable();
            $table->integer('LASTUPDATED')->default(0);
            $table->integer('LASTPURCHASE')->default(0);
            $table->decimal('LATITUDE', 10, 6)->nullable();
            $table->decimal('LONGITUDE', 10, 6)->nullable();
            $table->string('STOREIMAGE')->nullable();
            $table->integer('SYNCSTAT')->default(0);
            $table->string('DATES_TAMP')->nullable();
            $table->string('TIME_STAMP')->nullable();
            $table->boolean('ISLOCKON')->default(false);
            $table->integer('PRICECODE')->nullable();
            $table->string('STOREIMAGE2')->nullable();
            $table->string('CUSTTYPE')->nullable();
            $table->string('ISVISIT')->nullable();
            $table->string('DEFAULTORDTYPE')->nullable();
            $table->string('CITYMUNCODE')->nullable();
            $table->string('REGION')->nullable();
            $table->string('PROVINCE')->nullable();
            $table->string('MUNICIPALITY')->nullable();
            $table->string('BARANGAY')->nullable();
            $table->string('AREA')->nullable();
            $table->string('WAREHOUSE')->nullable();
            $table->string('KASOSYO')->nullable();
            $table->string('EMAIL')->nullable();
            $table->timestamps();
        });

        // Ensure sites are seeded before importing customers
        $seeder = new \Database\Seeders\SbuAndSiteSeeder();
        $seeder->run();

        // Import data from CSV file
        $csvFile = fopen(public_path('sample_custlist.csv'), 'r');
        $firstLine = true;

        while (($data = fgetcsv($csvFile)) !== false) {
            if ($firstLine) {
                $firstLine = false;
                continue;
            }

            DB::table('TBLCUSTOMER')->insert([
                'site_id' => $data[0],
                'MDCODE' => $data[1],
                'CUSTCODE' => $data[2],
                'CUSTNAME' => $data[3],
                'CONTACTCELLNUMBER' => $data[4],
                'CONTACTPERSON' => $data[5],
                'CONTACTLANDLINE' => $data[6],
                'ADDRESS' => $data[7],
                'FREQUENCYCATEGORY' => $data[8],
                'MCPDAY' => $data[9],
                'MCPSCHEDULE' => $data[10],
                'GEOLOCATION' => $data[11],
                'LASTUPDATED' => $data[12],
                'LASTPURCHASE' => $data[13],
                'LATITUDE' => $data[14],
                'LONGITUDE' => $data[15],
                'STOREIMAGE' => $data[16],
                'SYNCSTAT' => $data[17],
                'DATES_TAMP' => $data[18],
                'TIME_STAMP' => $data[19],
                'ISLOCKON' => $data[20],
                'PRICECODE' => $data[21],
                'STOREIMAGE2' => $data[22],
                'CUSTTYPE' => $data[23],
                'ISVISIT' => $data[24],
                'DEFAULTORDTYPE' => $data[25],
                'CITYMUNCODE' => $data[26],
                'REGION' => $data[27],
                'PROVINCE' => $data[28],
                'MUNICIPALITY' => $data[29],
                'BARANGAY' => $data[30],
                'AREA' => $data[31],
                'WAREHOUSE' => $data[32],
                'KASOSYO' => $data[33],
                'EMAIL' => $data[34],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        fclose($csvFile);
    }

    public function down(): void
    {
        Schema::dropIfExists('TBLCUSTOMER');
    }
};