
<?php

namespace App\Http\Controllers;

use App\Mail\NewEnquiryMail;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\Project;
use App\Models\Enquiry;
use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PortfolioController extends Controller
{
    /**
     * Display the portfolio landing page.
     */
    public function index(Request $request)
    {
        // Log page view if visitor is not logged in as admin (to keep metrics clean)
        if (!Auth::check()) {
            try {
                PageView::create([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Throwable $e) {
                // Ignore metrics error if database is initializing
            }
        }

        // Fetch settings
        $settings = [];
        try {
            $settingsRaw = Setting::all();
            foreach ($settingsRaw as $s) {
                $settings[$s->key] = $s->value;
            }
        } catch (\Throwable $e) {
            // Fallback to empty settings if DB is initializing
        }

        // Fetch skills grouped by category
        try {
            $creativeSkills = Skill::where('category', 'creative')
                ->orderBy('order', 'asc')
                ->get();

            $engineeringSkills = Skill::where('category', 'engineering')
                ->orderBy('order', 'asc')
                ->get();
        } catch (\Throwable $e) {
            $creativeSkills = collect();
            $engineeringSkills = collect();
        }

        // Fetch projects
        try {
            $projects = Project::orderBy('order', 'asc')->get();
        } catch (\Throwable $e) {
            $projects = collect();
        }

        return view('portfolio.index', compact('settings', 'creativeSkills', 'engineeringSkills', 'projects'));
    }

    /**
     * Store a new enquiry and send email notification.
     */
    public function storeEnquiry(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|min:5',
        ]);

        // Save to database (admin inbox)
        $enquiry = Enquiry::create($validated);

        // Send email notification to Gmail (non-blocking, won't fail the request)
        try {
            Mail::to(config('mail.from.address'))->send(new NewEnquiryMail($enquiry));
        } catch (\Exception $e) {
            // Email failed but enquiry is already saved — log for debugging
            \Log::warning('Email notification failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim! Kami akan membalas dalam 24 jam. 🚀',
            'enquiry' => $enquiry,
        ]);
    }
}
