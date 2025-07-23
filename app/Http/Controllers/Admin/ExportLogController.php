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
            'mode' => 'nullable|string|in:admin,user',
            'survey_title' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        $admin = Auth::guard('admin')->user();
        $exportType = $request->input('export_type');
        $entityType = $request->input('entity_type');
        $mode = $request->input('mode', 'user'); // Default to 'user' if not provided
        $surveyTitle = $request->input('survey_title');

        // Use custom description if provided, otherwise format based on export type, entity type, and survey title
        if ($request->has('description')) {
            $description = $request->input('description');
        } else if ($entityType === 'responses' && $surveyTitle) {
            $description = "Exported as " . ucfirst($exportType) . " the Individual Responses List of " . $surveyTitle;
        } else {
            // Default format for other entity types
            $listType = $mode === 'admin' ? "Admin's List" : "Surveyor's List";
            $description = "Exported as " . ucfirst($exportType) . ". " . $listType;
        }

        // Log the activity
        $properties = [
            'export_type' => $exportType,
            'entity_type' => $entityType,
            'mode' => $mode
        ];
        
        // Add survey title to properties if available
        if ($surveyTitle) {
            $properties['survey_title'] = $surveyTitle;
        }
        
        activity()
            ->causedBy($admin)
            ->withProperties($properties)
            ->event('exported')
            ->log($description);

        return response()->json(['success' => true]);
    }
}