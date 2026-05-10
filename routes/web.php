<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Welcome page
Route::get('/', function () {
    if (auth('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    if (auth('student')->check()) {
        return redirect()->route('student.dashboard');
    }

    return redirect()->route('admin.landing');
})->name('welcome');

// Admin Landing Page
Route::get('/admin', function () {
    if (auth('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('admin.landing');
})->name('admin.landing');

// Student Landing Page
Route::get('/student', function () {
    if (auth('student')->check()) {
        return redirect()->route('student.dashboard');
    }
    return view('student.landing');
})->name('student.landing');

// Admin Auth Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit')->middleware('throttle:5,1');
    Route::get('register', [AdminAuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AdminAuthController::class, 'register'])->name('register.submit');
    Route::get('forgot', [AdminAuthController::class, 'showForgot'])->name('forgot');
    Route::post('forgot', [AdminAuthController::class, 'forgot'])->name('forgot.submit');
    Route::get('reset', [AdminAuthController::class, 'showReset'])->name('reset');
    Route::post('reset', [AdminAuthController::class, 'reset'])->name('reset.submit');
    Route::get('verify', [AdminAuthController::class, 'verify'])->name('verify');

    // Protected Admin Routes
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::post('profile/update', [AdminController::class, 'updateProfile'])->name('profile.update');
        Route::post('profile/image', [AdminController::class, 'uploadImage'])->name('profile.image');
        Route::post('profile/password', [AdminController::class, 'changePassword'])->name('profile.password');
        Route::post('fine-rate', [AdminController::class, 'updateFineRate'])->name('fine-rate');
        Route::delete('users/{userId}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        Route::prefix('books')->name('books.')->group(function () {
            Route::get('/', [BookController::class, 'index'])->name('index');
            Route::get('data', [BookController::class, 'dataTable'])->name('data');
            Route::post('/', [BookController::class, 'store'])->name('store');
            Route::put('/', [BookController::class, 'update'])->name('update');
            Route::delete('{book}', [BookController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
            Route::delete('{category}', [CategoryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [RequestController::class, 'index'])->name('index');
            Route::post('{reqId}/approve', [RequestController::class, 'approve'])->name('approve');
            Route::post('{reqId}/deny', [RequestController::class, 'deny'])->name('deny');
            Route::post('{reqId}/return', [RequestController::class, 'returnBook'])->name('return');
            Route::post('{reqId}/pay-fine', [RequestController::class, 'payFine'])->name('pay-fine');
        });

        Route::get('notifications', function (Request $request) {
            $user = Auth::guard('admin')->user();

            return response()->json([
                'unread_count' => $user->unreadNotifications->count(),
                'notifications' => $user->unreadNotifications->take(10)->map(function ($n) {
                    return [
                        'id' => $n->id,
                        'data' => $n->data,
                        'created_at' => $n->created_at->diffForHumans(),
                    ];
                }),
            ]);
        })->name('notifications');

        Route::post('notifications/{id}/read', function ($id) {
            $user = Auth::guard('admin')->user();
            $user->notifications()->where('id', $id)->first()?->markAsRead();

            return response()->json(['success' => true]);
        })->name('notifications.read');

        Route::post('notifications/read-all', function () {
            $user = Auth::guard('admin')->user();
            $user->unreadNotifications->markAsRead();

            return response()->json(['success' => true]);
        })->name('notifications.read-all');
    });
});

// Student Auth Routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('login', [StudentAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [StudentAuthController::class, 'login'])->name('login.submit')->middleware('throttle:5,1');
    Route::get('register', [StudentAuthController::class, 'showRegister'])->name('register');
    Route::post('register', [StudentAuthController::class, 'register'])->name('register.submit');

    // Protected Student Routes
    Route::middleware(['auth:student'])->group(function () {
        Route::get('dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        Route::post('logout', [StudentAuthController::class, 'logout'])->name('logout');
        Route::post('request', [StudentController::class, 'requestBook'])->name('request');
        Route::get('receipt/{id}', [StudentController::class, 'receipt'])->name('receipt');

        Route::get('notifications', function (Request $request) {
            $user = Auth::guard('student')->user();

            return response()->json([
                'unread_count' => $user->unreadNotifications->count(),
                'notifications' => $user->unreadNotifications->take(10)->map(function ($n) {
                    return [
                        'id' => $n->id,
                        'data' => $n->data,
                        'created_at' => $n->created_at->diffForHumans(),
                    ];
                }),
            ]);
        })->name('notifications');

        Route::post('notifications/{id}/read', function ($id) {
            $user = Auth::guard('student')->user();
            $user->notifications()->where('id', $id)->first()?->markAsRead();

            return response()->json(['success' => true]);
        })->name('notifications.read');

        Route::post('notifications/read-all', function () {
            $user = Auth::guard('student')->user();
            $user->unreadNotifications->markAsRead();

            return response()->json(['success' => true]);
        })->name('notifications.read-all');
    });
});
