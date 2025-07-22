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
            'entity_type' => 'required|string',
            'mode' => 'nullable|string|in:admin,user'
        ]);

        $admin = Auth::guard('admin')->user();
        $exportType = $request->input('export_type');
        $entityType = $request->input('entity_type');
        $mode = $request->input('mode', 'user'); // Default to 'user' if not provided

        // Format description based on export type and mode
        $listType = $mode === 'admin' ? "Admin's List" : "Surveyor's List";
        $description = "Exported as " . ucfirst($exportType) . ". " . $listType;

        // Log the activity
        activity()
            ->causedBy($admin)
            ->withProperties([
                'export_type' => $exportType,
                'entity_type' => $entityType,
                'mode' => $mode
            ])
            ->event('exported')
            ->log($description);

        return response()->json(['success' => true]);
    }
}