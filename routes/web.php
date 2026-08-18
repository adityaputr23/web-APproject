<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\EnquiryController;

// Public Portfolio Routes
Route::get('/', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::post('/enquire', [PortfolioController::class, 'storeEnquiry'])->name('portfolio.enquire');

// Admin Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout.get'); // Quick helper for sidebar GET request

// Protected Admin Dashboard Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Projects CRUD
    Route::resource('projects', ProjectController::class)->except(['show']);
    
    // Skills CRUD
    Route::resource('skills', SkillController::class)->except(['show']);
    
    // Enquiries inbox
    Route::get('/enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
    Route::post('/enquiries/{enquiry}/toggle-read', [EnquiryController::class, 'toggleRead'])->name('enquiries.toggle-read');
    Route::delete('/enquiries/{enquiry}', [EnquiryController::class, 'destroy'])->name('enquiries.destroy');
    
    // Settings edit/update
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Cloudinary direct upload signature endpoint
    Route::get('/cloudinary-signature', [ProjectController::class, 'getCloudinarySignature'])->name('cloudinary.signature');
});

// TEMPORARY DEBUG ROUTE — Remove after fixing
Route::get('/debug-session', function () {
    return response()->json([
        'session_driver'   => config('session.driver'),
        'session_id'       => session()->getId(),
        'session_all'      => session()->all(),
        'is_authenticated' => auth()->check(),
        'auth_user'        => auth()->check() ? auth()->user()->email : null,
        'app_url'          => config('app.url'),
        'cookies_received' => array_keys(request()->cookies->all()),
        'db_user_count'    => \App\Models\User::count(),
        'db_connection'    => config('database.default'),
        'session_encrypt'  => config('session.encrypt'),
        'session_secure'   => config('session.secure'),
        'session_same_site'=> config('session.same_site'),
        'session_cookie'   => config('session.cookie'),
    ]);
});
