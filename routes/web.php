<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\UserSurveyController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Admin\ThemeController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Direct access survey route for customers (no login required)
// These routes don't use site.access middleware to allow public access via email links
Route::get('/survey/{survey}', [UserSurveyController::class, 'customerSurvey'])->name('customer.survey');
Route::post('/survey/{survey}/submit', [UserSurveyController::class, 'customerStore'])->name('customer.survey.submit');

// Language setting route
Route::post('/set-language', function(\Illuminate\Http\Request $request) {
    $language = $request->input('language', 'en');
    
    // Check if the locale is available in the database
    $availableLocales = \App\Models\Translation::getAvailableLocales();
    
    if (in_array($language, $availableLocales)) {
        session(['locale' => $language]);
        app()->setLocale($language);
        return response()->json(['success' => true, 'locale' => $language]);
    } else {
        return response()->json(['success' => false, 'message' => 'Locale not available'], 400);
    }
})->name('set.language');

// Get translations for frontend
Route::get('/translations/{locale?}', function($locale = null) {
    if ($locale) {
        $translations = \App\Models\Translation::whereHas('translationHeader', function($query) use ($locale) {
            $query->where('locale', $locale);
        })->with('translationHeader')->get();
    } else {
        $translations = \App\Models\Translation::with('translationHeader')->get();
    }
    
    $result = [];
    foreach ($translations as $translation) {
        $locale = $translation->translationHeader->locale;
        if (!isset($result[$locale])) {
            $result[$locale] = [];
        }
        $result[$locale][$translation->key] = $translation->value;
    }
    
    return response()->json($result);
})->name('get.translations');

// Demo route for database translations
Route::get('/demo/translations', function() {
    return view('demo.translation-demo');
})->name('demo.translations');

// Autocomplete route for customer names
Route::get('/customers/autocomplete', [CustomerController::class, 'autocomplete'])->name('customers.autocomplete');
// Customer lookup by code route
Route::get('/customers/lookup-by-code', [CustomerController::class, 'lookupByCode'])->name('customers.lookup-by-code');

Auth::routes();

// Account disabled route
Route::get('/account-disabled', [\App\Http\Controllers\Auth\AccountStatusController::class, 'showAccountDisabled'])->name('account.disabled');

// TEMP: Test route to disable a user (remove after testing)
Route::get('/test-disable-user/{id}', function($id) {
    $user = \App\Models\User::find($id);
    if ($user) {
        $user->status = 0;
        $user->disabled_reason = 'Test reason: Account disabled for testing the new feature implementation.';
        $user->disabled_at = now();
        $user->save();
        
        // Store in cache like the middleware would
        Cache::put('disabled_user_' . $user->id, [
            'disabled_reason' => $user->disabled_reason,
            'account_type' => 'User',
            'disabled_at' => $user->disabled_at
        ], 60);
        
        return redirect()->route('account.disabled', ['uid' => $user->id]);
    }
    return 'User not found';
});

// Profile routes
Route::middleware(['auth:web,admin', 'account.status'])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\Auth\ProfileController::class, 'showProfileForm'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Auth\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\Auth\ProfileController::class, 'changePassword'])->name('profile.password');
    Route::post('/profile/check-current-password', [\App\Http\Controllers\Auth\ProfileController::class, 'checkCurrentPassword'])->name('profile.checkCurrentPassword');
});

Route::middleware(['auth', 'account.status'])->group(function () {
    Route::get('/home', [UserSurveyController::class, 'index'])->name('home');
    Route::get('/index', [UserSurveyController::class, 'index'])->name('index');
    
    // Apply site access middleware to survey routes
    Route::middleware(['site.access'])->group(function () {
        Route::get('/surveys/{survey}', [UserSurveyController::class, 'show'])->name('surveys.show');
        Route::post('/surveys/{survey}', [UserSurveyController::class, 'store'])->name('surveys.store');
        Route::get('/surveys/{survey}/customers', [UserSurveyController::class, 'getCustomers'])->name('surveys.customers');
        Route::post('/surveys/{survey}/broadcast', [UserSurveyController::class, 'broadcastSurvey'])->name('surveys.broadcast');
        Route::get('/broadcast/progress/{batchId}', [UserSurveyController::class, 'getBroadcastProgress'])->name('broadcast.progress');
    });
    
    Route::post('/check-account-exists', [UserSurveyController::class, 'checkAccountExists'])->name('check.account.exists');
    Route::get('/surveys/thankyou', [UserSurveyController::class, 'thankyou'])->name('surveys.thankyou');
    
    // Survey response routes for surveyors
    Route::get('/surveys/{survey}/responses', [\App\Http\Controllers\SurveyResponseController::class, 'index'])->name('surveys.responses.index');
    Route::get('/surveys/{survey}/responses/{account_name}', [\App\Http\Controllers\SurveyResponseController::class, 'show'])->name('surveys.responses.show');
    Route::patch('/surveys/{survey}/responses/{account_name}/toggle-resubmission', [\App\Http\Controllers\SurveyResponseController::class, 'toggleResubmission'])->name('surveys.responses.toggle-resubmission');
});

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::middleware(['auth:admin', 'account.status'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
        Route::patch('/customers/{id}', [CustomerController::class, 'update'])->name('admin.customers.update');
        Route::get('/customers/check-email-availability', [CustomerController::class, 'checkEmailAvailability'])->name('admin.customers.check-email-availability');
        
        // Admin management routes - using same view as users
        Route::get('/admins/create', [App\Http\Controllers\Admin\UserManagementController::class, 'create'])->defaults('mode', 'admin')->name('admin.admins.create');
        Route::get('/admins/data', [App\Http\Controllers\Admin\AdminManagementController::class, 'data'])->name('admin.admins.data');
        Route::post('/admins', [App\Http\Controllers\Admin\AdminManagementController::class, 'store'])->name('admin.admins.store');
        Route::get('/check-email-availability', [App\Http\Controllers\Admin\AdminManagementController::class, 'checkEmailAvailability'])->name('admin.check-email-availability');
        Route::get('/check-name-availability', [App\Http\Controllers\Admin\AdminManagementController::class, 'checkNameAvailability'])->name('admin.check-name-availability');
        Route::get('/check-contact-number-availability', [App\Http\Controllers\Admin\AdminManagementController::class, 'checkContactNumberAvailability'])->name('admin.check-contact-number-availability');

        // Theme management routes
        Route::get('/themes', [ThemeController::class, 'index'])->name('admin.themes.index');
        Route::get('/themes/create', [ThemeController::class, 'create'])->name('admin.themes.create');
        Route::post('/themes', [ThemeController::class, 'store'])->name('admin.themes.store');
        Route::get('/themes/{theme}/edit', [ThemeController::class, 'edit'])->name('admin.themes.edit');
        Route::put('/themes/{theme}', [ThemeController::class, 'update'])->name('admin.themes.update');
        Route::delete('/themes/{theme}', [ThemeController::class, 'destroy'])->name('admin.themes.destroy');
        Route::post('/themes/{theme}/activate', [ThemeController::class, 'activate'])->name('admin.themes.activate');

        // Survey routes
        Route::resource('surveys', \App\Http\Controllers\Admin\SurveyController::class)->names([
            'index' => 'admin.surveys.index',
            'create' => 'admin.surveys.create',
            'store' => 'admin.surveys.store',
            'show' => 'admin.surveys.show',
            'edit' => 'admin.surveys.edit',
            'update' => 'admin.surveys.update',
            'destroy' => 'admin.surveys.destroy'
        ]);

        // Survey questions routes
        Route::get('surveys/{survey}/questions/create', [\App\Http\Controllers\Admin\SurveyQuestionController::class, 'create'])
            ->name('admin.surveys.questions.create');
        Route::post('surveys/{survey}/questions', [\App\Http\Controllers\Admin\SurveyQuestionController::class, 'store'])
            ->name('admin.surveys.questions.store');
        Route::get('surveys/{survey}/questions/{question}/edit', [\App\Http\Controllers\Admin\SurveyQuestionController::class, 'edit'])
            ->name('admin.surveys.questions.edit');
        Route::put('surveys/{survey}/questions/{question}', [\App\Http\Controllers\Admin\SurveyQuestionController::class, 'update'])
            ->name('admin.surveys.questions.update');
        Route::delete('surveys/{survey}/questions/{question}', [\App\Http\Controllers\Admin\SurveyQuestionController::class, 'destroy'])
            ->name('admin.surveys.questions.destroy');

        // Survey broadcast routes
        Route::get('surveys/{survey}/customers', [\App\Http\Controllers\UserSurveyController::class, 'getCustomers'])
            ->name('admin.surveys.customers');
        Route::post('surveys/{survey}/broadcast', [\App\Http\Controllers\UserSurveyController::class, 'broadcastSurvey'])
            ->name('admin.surveys.broadcast');
        Route::get('broadcast/progress/{batchId}', [\App\Http\Controllers\UserSurveyController::class, 'getBroadcastProgress'])
            ->name('admin.broadcast.progress');
            
        // Survey response routes
        Route::get('surveys/{survey}/responses', [\App\Http\Controllers\Admin\SurveyResponseController::class, 'index'])
            ->name('admin.surveys.responses.index');
        Route::get('surveys/{survey}/report', [\App\Http\Controllers\Admin\SurveyResponseController::class, 'report'])
            ->name('admin.surveys.report');
        Route::get('surveys/{survey}/export-excel', [\App\Http\Controllers\Admin\SurveyResponseController::class, 'exportExcel'])
            ->name('admin.surveys.export.excel');
        Route::get('surveys/{survey}/export-detailed-excel', [\App\Http\Controllers\Admin\SurveyResponseController::class, 'exportDetailedExcel'])
            ->name('admin.surveys.export.detailed');
        Route::get('surveys/{survey}/unique-respondents', [\App\Http\Controllers\Admin\SurveyResponseController::class, 'uniqueRespondents'])
            ->name('admin.surveys.unique-respondents');
        Route::get('surveys/{survey}/responses/{account_name}', [\App\Http\Controllers\Admin\SurveyResponseController::class, 'show'])
            ->name('admin.surveys.responses.show');
            
        // Add toggle resubmission route
        Route::patch('surveys/{survey}/responses/{account_name}/toggle-resubmission', 
            [\App\Http\Controllers\Admin\SurveyResponseController::class, 'toggleResubmission'])
            ->name('admin.surveys.responses.toggle-resubmission');

        Route::patch('surveys/{survey}/toggle-status', [\App\Http\Controllers\Admin\SurveyController::class, 'toggleStatus'])
            ->name('admin.surveys.toggle-status');
            
        // Survey logo update route
        Route::patch('surveys/{survey}/update-logo', [\App\Http\Controllers\Admin\SurveyController::class, 'updateLogo'])
            ->name('admin.surveys.update-logo');
        // Survey department logo update route
        Route::patch('surveys/{survey}/update-department-logo', [\App\Http\Controllers\Admin\SurveyController::class, 'updateDepartmentLogo'])
            ->name('admin.surveys.update-department-logo');
        // Survey deployment settings update route
        Route::patch('surveys/{survey}/update-deployment', [\App\Http\Controllers\Admin\SurveyController::class, 'updateDeployment'])
            ->name('admin.surveys.update-deployment');
        Route::get('/users/create', [\App\Http\Controllers\Admin\UserManagementController::class, 'create'])->defaults('mode', 'user')->name('admin.users.create');
        Route::post('/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('admin.users.store');
        Route::get('/users/data', [\App\Http\Controllers\Admin\UserManagementController::class, 'data'])->name('admin.users.data');
        
        // Sites by SBUs route for dynamic loading
        Route::get('/sites/by-sbus', [\App\Http\Controllers\Admin\UserManagementController::class, 'getSitesBySbus'])->name('admin.sites.by-sbus');

        // Logo management routes
        Route::resource('logos', \App\Http\Controllers\Admin\LogoController::class)->names([
            'index' => 'admin.logos.index',
            'create' => 'admin.logos.create',
            'store' => 'admin.logos.store',
            'show' => 'admin.logos.show',
            'edit' => 'admin.logos.edit',
            'update' => 'admin.logos.update',
            'destroy' => 'admin.logos.destroy'
        ]);
        Route::post('logos/{logo}/activate', [\App\Http\Controllers\Admin\LogoController::class, 'activate'])->name('admin.logos.activate');
        
        // Translation management routes (for admins)
        Route::prefix('translations')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\TranslationController::class, 'index'])->name('admin.translations.index');
            Route::get('/create', [\App\Http\Controllers\Admin\TranslationController::class, 'create'])->name('admin.translations.create');
            Route::post('/', [\App\Http\Controllers\Admin\TranslationController::class, 'store'])->name('admin.translations.store');
            Route::get('/{translation}/edit', [\App\Http\Controllers\Admin\TranslationController::class, 'edit'])->name('admin.translations.edit');
            Route::put('/{translation}', [\App\Http\Controllers\Admin\TranslationController::class, 'update'])->name('admin.translations.update');
            Route::delete('/{translation}', [\App\Http\Controllers\Admin\TranslationController::class, 'destroy'])->name('admin.translations.destroy');
            Route::post('/clear-cache', [\App\Http\Controllers\Admin\TranslationController::class, 'clearCache'])->name('admin.translations.clearCache');
            Route::post('/deploy-languages', [\App\Http\Controllers\Admin\TranslationController::class, 'deployLanguages'])->name('admin.translations.deployLanguages');
            Route::post('/export', [\App\Http\Controllers\Admin\TranslationController::class, 'export'])->name('admin.translations.export');
            Route::post('/add-language', [\App\Http\Controllers\Admin\TranslationController::class, 'addLanguage'])->name('admin.translations.addLanguage');
        });
        
        // API routes for SBU and Site data
        Route::get('/api/sbus-with-sites', [\App\Http\Controllers\Admin\SurveyController::class, 'getSbusWithSites'])->name('admin.api.sbus-with-sites');
        
        // Survey title uniqueness check
        Route::post('/surveys/check-title-uniqueness', [\App\Http\Controllers\Admin\SurveyController::class, 'checkTitleUniqueness'])->name('admin.surveys.check-title-uniqueness');
    });
});

// Health check route (publicly accessible)
Route::get('/health', [\App\Http\Controllers\UserSurveyController::class, 'healthCheck'])->name('health.check');

// Developer Routes - Secret Access Path (Base64 Encoded)
// Original path: secret-dev-access-fastdev-2025
Route::prefix('c2VjcmV0LWRldi1hY2Nlc3MtZmFzdGRldi0yMDI1')->group(function () {
    Route::get('/login', [\App\Http\Controllers\DeveloperController::class, 'showLoginForm'])->name('developer.login');
    Route::post('/login', [\App\Http\Controllers\DeveloperController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\DeveloperController::class, 'logout'])->name('developer.logout');

    Route::middleware(['auth:developer', 'developer.access'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DeveloperController::class, 'dashboard'])->name('developer.dashboard');
        
        // Survey Management
        Route::get('/surveys', [\App\Http\Controllers\DeveloperController::class, 'surveys'])->name('developer.surveys');
        Route::patch('/surveys/{id}/enable', [\App\Http\Controllers\DeveloperController::class, 'enableSurvey'])->name('developer.surveys.enable');
        Route::patch('/surveys/{id}/disable', [\App\Http\Controllers\DeveloperController::class, 'disableSurvey'])->name('developer.surveys.disable');
        Route::delete('/surveys/{id}', [\App\Http\Controllers\DeveloperController::class, 'deleteSurvey'])->name('developer.surveys.delete');
        
        // Admin Management
        Route::get('/admins', [\App\Http\Controllers\DeveloperController::class, 'admins'])->name('developer.admins');
        Route::patch('/admins/{id}/toggle-status', [\App\Http\Controllers\DeveloperController::class, 'toggleAdminStatus'])->name('developer.admins.toggle-status');
        Route::delete('/admins/{id}', [\App\Http\Controllers\DeveloperController::class, 'deleteAdmin'])->name('developer.admins.delete');
        
        // User Management
        Route::get('/users', [\App\Http\Controllers\DeveloperController::class, 'users'])->name('developer.users');
        Route::patch('/users/{id}/toggle-status', [\App\Http\Controllers\DeveloperController::class, 'toggleUserStatus'])->name('developer.users.toggle-status');
        Route::delete('/users/{id}', [\App\Http\Controllers\DeveloperController::class, 'deleteUser'])->name('developer.users.delete');
        
        // Translation management routes (for developers)
        Route::prefix('translations')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\TranslationController::class, 'index'])->name('developer.translations.index');
            Route::get('/create', [\App\Http\Controllers\Admin\TranslationController::class, 'create'])->name('developer.translations.create');
            Route::post('/', [\App\Http\Controllers\Admin\TranslationController::class, 'store'])->name('developer.translations.store');
            Route::get('/{translation}/edit', [\App\Http\Controllers\Admin\TranslationController::class, 'edit'])->name('developer.translations.edit');
            Route::put('/{translation}', [\App\Http\Controllers\Admin\TranslationController::class, 'update'])->name('developer.translations.update');
            Route::delete('/{translation}', [\App\Http\Controllers\Admin\TranslationController::class, 'destroy'])->name('developer.translations.destroy');
            Route::post('/clear-cache', [\App\Http\Controllers\Admin\TranslationController::class, 'clearCache'])->name('developer.translations.clearCache');
            Route::post('/export', [\App\Http\Controllers\Admin\TranslationController::class, 'export'])->name('developer.translations.export');
            Route::post('/add-language', [\App\Http\Controllers\Admin\TranslationController::class, 'addLanguage'])->name('developer.translations.addLanguage');
        });
        
        // User Logs Management
        Route::prefix('logs')->group(function () {
            Route::get('/', [\App\Http\Controllers\Developer\UserLogsController::class, 'index'])->name('developer.logs.index');
            Route::get('/user-activity', [\App\Http\Controllers\Developer\UserLogsController::class, 'userActivity'])->name('developer.logs.user-activity');
            Route::get('/login-activity', [\App\Http\Controllers\Developer\UserLogsController::class, 'loginActivity'])->name('developer.logs.login-activity');
            Route::get('/user-activity/data', [\App\Http\Controllers\Developer\UserLogsController::class, 'getUserActivityData'])->name('developer.logs.user-activity.data');
            Route::get('/login-activity/data', [\App\Http\Controllers\Developer\UserLogsController::class, 'getLoginActivityData'])->name('developer.logs.login-activity.data');
        });
    });
});
