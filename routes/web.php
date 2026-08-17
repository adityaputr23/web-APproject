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
});

// TEMPORARY DEBUG — shows session, driver, and auth state (remove after login is confirmed working)
Route::get('/debug-session', function () {
    $dbUsersCount = 0;
    $adminExists  = false;
    $localSqlite  = base_path('database/database.sqlite');
    try {
        $dbUsersCount = \App\Models\User::count();
        $adminExists  = \App\Models\User::where('email', 'admin@apvisuals.com')->exists();
    } catch (\Throwable $e) {
        $dbUsersCount = 'Error: ' . $e->getMessage();
    }

    return response()->json([
        'session_driver'     => config('session.driver'),
        'session_id'         => session()->getId(),
        'session_has_token'  => session()->has('_token'),
        'csrf_token'         => csrf_token(),
        'auth_check'         => auth()->check(),
        'auth_user'          => auth()->user()?->email,
        'db_connection'      => config('database.default'),
        'db_file'            => config('database.connections.sqlite.database'),
        'db_users_count'     => $dbUsersCount,
        'admin_user_exists'  => $adminExists,
        'local_sqlite_exists'=> file_exists($localSqlite),
        'local_sqlite_size'  => file_exists($localSqlite) ? filesize($localSqlite) : 0,
        'app_env'            => app()->environment(),
        'app_key_set'        => !empty(config('app.key')),
        'vercel_env'         => getenv('VERCEL') ?: 'not set',
        'https'              => request()->isSecure(),
    ]);
});

