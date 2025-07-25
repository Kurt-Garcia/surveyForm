<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing survey response headers that don't have start_time and end_time
        $headers = DB::table('survey_response_headers')
            ->whereNull('start_time')
            ->orWhereNull('end_time')
            ->get();

        foreach ($headers as $header) {
            // Generate realistic start and end times based on the existing date
            $baseDate = Carbon::parse($header->date ?? $header->created_at);
            
            // Random time during the day
            $startTime = $baseDate->copy()->addHours(rand(8, 17))->addMinutes(rand(0, 59));
            
            // Duration between 2-15 minutes
            $durationMinutes = rand(2, 15);
            $endTime = $startTime->copy()->addMinutes($durationMinutes);
            
            DB::table('survey_response_headers')
                ->where('id', $header->id)
                ->update([
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'updated_at' => now()
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set start_time and end_time to null for all records
        DB::table('survey_response_headers')
            ->update([
                'start_time' => null,
                'end_time' => null
            ]);
    }
};