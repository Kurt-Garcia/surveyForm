<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Facades\LogActivity;

class ExportLogController extends Controller
{
    /**
     * Log export activity
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logExport(Request $request)
    {
        $request->validate([
            'export_type' => 'required|string|in:copy,csv,excel,pdf,print',
            'entity_type' => 'required|string'
        ]);

        $admin = Auth::guard('admin')->user();
        $exportType = $request->input('export_type');
        $entityType = $request->input('entity_type');

        // Format description based on export type
        $description = "Exported as " . ucfirst($exportType) . ".";

        // Log the activity
        activity()
            ->causedBy($admin)
            ->withProperties([
                'export_type' => $exportType,
                'entity_type' => $entityType
            ])
            ->event('exported')
            ->log($description);

        return response()->json(['success' => true]);
    }
}