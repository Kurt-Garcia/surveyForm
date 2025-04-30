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

        // Import data from CSV file
        $csvFile = fopen(public_path('sample_custlist.csv'), 'r');
        $firstLine = true;

        while (($data = fgetcsv($csvFile)) !== false) {
            if ($firstLine) {
                $firstLine = false;
                continue;
            }

            DB::table('TBLCUSTOMER')->insert([
                'MDCODE' => $data[0],
                'CUSTCODE' => $data[1],
                'CUSTNAME' => $data[2],
                'CONTACTCELLNUMBER' => $data[3],
                'CONTACTPERSON' => $data[4],
                'CONTACTLANDLINE' => $data[5],
                'ADDRESS' => $data[6],
                'FREQUENCYCATEGORY' => $data[7],
                'MCPDAY' => $data[8],
                'MCPSCHEDULE' => $data[9],
                'GEOLOCATION' => $data[10],
                'LASTUPDATED' => $data[11],
                'LASTPURCHASE' => $data[12],
                'LATITUDE' => $data[13],
                'LONGITUDE' => $data[14],
                'STOREIMAGE' => $data[15],
                'SYNCSTAT' => $data[16],
                'DATES_TAMP' => $data[17],
                'TIME_STAMP' => $data[18],
                'ISLOCKON' => $data[19],
                'PRICECODE' => $data[20],
                'STOREIMAGE2' => $data[21],
                'CUSTTYPE' => $data[22],
                'ISVISIT' => $data[23],
                'DEFAULTORDTYPE' => $data[24],
                'CITYMUNCODE' => $data[25],
                'REGION' => $data[26],
                'PROVINCE' => $data[27],
                'MUNICIPALITY' => $data[28],
                'BARANGAY' => $data[29],
                'AREA' => $data[30],
                'WAREHOUSE' => $data[31],
                'KASOSYO' => $data[32],
                'EMAIL' => $data[33],
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