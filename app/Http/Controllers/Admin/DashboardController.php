<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Enquiry;
use App\Models\PageView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard overview.
     */
    public function index()
    {
        // 1. Gather stats
        $totalProjects = Project::count();
        $totalViews    = PageView::count();
        $activeSkills  = Skill::count();
        $newEnquiries  = Enquiry::where('is_read', false)->count();

        // 2. Recent Activities feed
        $recentProjects = Project::orderBy('updated_at', 'desc')->take(3)->get()->map(function ($item) {
            return [
                'type'        => 'project',
                'title'       => 'Created / Updated "' . $item->title . '" Project',
                'description' => 'Category: ' . $item->category,
                'time'        => $item->updated_at,
            ];
        });

        $recentEnquiries = Enquiry::orderBy('created_at', 'desc')->take(3)->get()->map(function ($item) {
            return [
                'type'        => 'enquiry',
                'title'       => 'New Client Inquiry: ' . $item->name,
                'description' => '"' . Str::limit($item->message, 60) . '"',
                'time'        => $item->created_at,
            ];
        });

        $activities = $recentProjects->concat($recentEnquiries)->sortByDesc('time')->take(5)->values();

        // 3. Simple Analytics: Views over the last 7 days
        $analytics = PageView::select(DB::raw('DATE(viewed_at) as date'), DB::raw('count(*) as views'))
            ->where('viewed_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('views', 'date');

        // Fill in missing days with 0 views
        $trafficData = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateString              = Carbon::now()->subDays($i)->format('Y-m-d');
            $label                   = Carbon::now()->subDays($i)->format('D');
            $trafficData[$label]     = $analytics[$dateString] ?? 0;
        }

        return view('admin.dashboard', compact(
            'totalProjects',
            'totalViews',
            'activeSkills',
            'newEnquiries',
            'activities',
            'trafficData'
        ));
    }
}
