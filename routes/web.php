<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\UserSurveyController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\CustomerController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Direct access survey route for customers (no login required)
Route::get('/survey/{survey}', [UserSurveyController::class, 'customerSurvey'])->name('customer.survey');
Route::post('/survey/{survey}/submit', [UserSurveyController::class, 'customerStore'])->name('customer.survey.submit');

// Autocomplete route for customer names
Route::get('/customers/autocomplete', [CustomerController::class, 'autocomplete'])->name('customers.autocomplete');
// Customer lookup by code route
Route::get('/customers/lookup-by-code', [CustomerController::class, 'lookupByCode'])->name('customers.lookup-by-code');

Auth::routes();

// Password change routes - accessible by both users and admins
Route::middleware(['auth:web,admin'])->group(function () {
    Route::get('/password/change', [ChangePasswordController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/password/change', [ChangePasswordController::class, 'changePassword']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [UserSurveyController::class, 'index'])->name('home');
    Route::get('/index', [UserSurveyController::class, 'index'])->name('index');
    Route::get('/surveys/{survey}', [UserSurveyController::class, 'show'])->name('surveys.show');
    Route::post('/surveys/{survey}', [UserSurveyController::class, 'store'])->name('surveys.store');
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

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
        
        // Admin management routes
        Route::get('/admins/create', [App\Http\Controllers\Admin\AdminManagementController::class, 'create'])->name('admin.admins.create');
        Route::post('/admins', [App\Http\Controllers\Admin\AdminManagementController::class, 'store'])->name('admin.admins.store');

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

        // Survey response routes
        Route::get('surveys/{survey}/responses', [\App\Http\Controllers\Admin\SurveyResponseController::class, 'index'])
            ->name('admin.surveys.responses.index');
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
        Route::get('/users/create', [\App\Http\Controllers\Admin\UserManagementController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('admin.users.store');

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
    });
});
