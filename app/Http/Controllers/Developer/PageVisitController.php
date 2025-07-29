<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PageVisitLog;
use App\Services\PageVisitService;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class PageVisitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:developer');
        $this->middleware('developer.access');
    }

    /**
     * Display the page visit logs dashboard
     */
    public function index()
    {
        $stats = PageVisitService::getPageVisitStats(30);
        $mostVisitedPages = PageVisitService::getMostVisitedPages(30, 10);
        $userActivitySummary = PageVisitService::getUserActivitySummary(30);
        
        // Get chart data for the last 7 days
        $chartData = $this->getChartData();
        
        return view('developer.logs.page-visits', compact(
            'stats', 
            'mostVisitedPages', 
            'userActivitySummary',
            'chartData'
        ));
    }

    /**
     * Get page visit data for DataTables
     */
    public function getData(Request $request)
    {
        $query = PageVisitLog::with([])
            ->completed()
            ->orderBy('start_time', 'desc');

        // Apply filters
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->filled('date_from')) {
            $query->where('start_time', '>=', Carbon::parse($request->date_from)->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('start_time', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        if ($request->filled('route_name')) {
            $query->where('route_name', 'like', '%' . $request->route_name . '%');
        }

        return DataTables::of($query)
            ->addColumn('user_info', function ($log) {
                $badgeClass = match($log->user_type) {
                    'admin' => 'bg-primary',
                    'user' => 'bg-success', 
                    'developer' => 'bg-warning text-dark',
                    default => 'bg-secondary'
                };
                
                return '<div>' .
                       '<span class="badge ' . $badgeClass . ' mb-1">' . ucfirst($log->user_type) . '</span><br>' .
                       '<strong>' . e($log->user_name) . '</strong><br>' .
                       '<small class="text-muted">' . e($log->user_email) . '</small>' .
                       '</div>';
            })
            ->addColumn('page_info', function ($log) {
                $title = $log->display_title;
                $url = parse_url($log->page_url, PHP_URL_PATH);
                
                return '<div>' .
                       '<strong>' . e($title) . '</strong><br>' .
                       '<small class="text-muted">' . e($url) . '</small>' .
                       '</div>';
            })
            ->addColumn('visit_time', function ($log) {
                return '<div>' .
                       '<strong>Start:</strong> ' . $log->start_time->format('M d, Y H:i:s') . '<br>' .
                       '<strong>End:</strong> ' . ($log->end_time ? $log->end_time->format('M d, Y H:i:s') : 'N/A') .
                       '</div>';
            })
            ->addColumn('duration', function ($log) {
                if (!$log->duration_seconds) {
                    return '<span class="text-muted">N/A</span>';
                }
                
                $duration = $log->formatted_duration;
                $class = '';
                
                if ($log->duration_seconds < 30) {
                    $class = 'text-danger'; // Very short visit
                } elseif ($log->duration_seconds < 300) {
                    $class = 'text-warning'; // Short visit
                } else {
                    $class = 'text-success'; // Good engagement
                }
                
                return '<span class="' . $class . '">' . $duration . '</span>';
            })
            ->addColumn('actions', function ($log) {
                return '<button class="btn btn-sm btn-outline-info" onclick="showVisitDetails(' . $log->id . ')">' .
                       '<i class="bi bi-eye"></i> Details' .
                       '</button>';
            })
            ->rawColumns(['user_info', 'page_info', 'visit_time', 'duration', 'actions'])
            ->make(true);
    }

    /**
     * Get visit details
     */
    public function getVisitDetails($id)
    {
        $visit = PageVisitLog::findOrFail($id);
        
        return response()->json([
            'visit' => $visit,
            'formatted_duration' => $visit->formatted_duration,
            'additional_data' => $visit->additional_data,
        ]);
    }

    /**
     * Get chart data for page visits over time
     */
    private function getChartData()
    {
        $days = [];
        $adminVisits = [];
        $userVisits = [];
        $developerVisits = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('M d');
            
            $adminVisits[] = PageVisitLog::whereDate('start_time', $date)
                ->where('user_type', 'admin')
                ->completed()
                ->count();
                
            $userVisits[] = PageVisitLog::whereDate('start_time', $date)
                ->where('user_type', 'user')
                ->completed()
                ->count();
                
            $developerVisits[] = PageVisitLog::whereDate('start_time', $date)
                ->where('user_type', 'developer')
                ->completed()
                ->count();
        }
        
        return [
            'labels' => $days,
            'admin_visits' => $adminVisits,
            'user_visits' => $userVisits,
            'developer_visits' => $developerVisits,
        ];
    }

    /**
     * Export page visit data
     */
    public function export(Request $request)
    {
        // This can be implemented later if needed
        return response()->json(['message' => 'Export functionality coming soon']);
    }
}