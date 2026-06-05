<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\AssetController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\ResourceController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\TicketMessageController;
use App\Http\Controllers\API\UserController;

// Public auth endpoints
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Public read-only catalog (knowledge base + resources are citizen-facing)
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{category}', [CategoryController::class, 'show']);
Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/search', [ArticleController::class, 'search']);
// Public detail resolves by slug (the UI links use the article slug, not the id).
Route::get('articles/{article:slug}', [ArticleController::class, 'show']);
Route::post('articles/{article}/helpful', [ArticleController::class, 'incrementHelpful']);
Route::get('resources', [ResourceController::class, 'index']);
Route::get('resources/{resource}', [ResourceController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    // Session
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Unified quick search (role-scoped inside the controller)
    Route::get('search', [SearchController::class, 'index']);

    // Dashboard command-center summary (role-scoped inside the controller)
    Route::get('dashboard/summary', [DashboardController::class, 'summary']);

    // Tickets (controller filters by role)
    Route::apiResource('tickets', TicketController::class);
    Route::get('tickets/{ticket}/messages', [TicketMessageController::class, 'index']);
    Route::post('tickets/{ticket}/messages', [TicketMessageController::class, 'store']);
    Route::post('tickets/{ticket}/close', [TicketController::class, 'markClosed']);

    // Asset reads for any authenticated user (legacy view)
    Route::get('assets', [AssetController::class, 'index']);
    Route::get('assets/{asset}', [AssetController::class, 'show']);

    // Admin + analyst only — staff who maintain the knowledge base + catalog.
    Route::middleware('role:admin,analyst')->group(function () {
        Route::post('tickets/{ticket}/resolve', [TicketController::class, 'markResolved']);

        // Edit-context fetch for an article: does NOT increment views_count.
        Route::get('admin/articles/{article}', [ArticleController::class, 'showForEdit']);

        // Analysts maintain the KB + catalog alongside admins.
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
        Route::apiResource('articles', ArticleController::class)->except(['index', 'show']);
        Route::apiResource('resources', ResourceController::class)->except(['index', 'show']);
    });

    // Admin only — user management, internal assets, ticket assignment.
    Route::middleware('role:admin')->group(function () {
        // Assigning a ticket is an admin-only action (auto-moves status to in_progress).
        Route::post('tickets/{ticket}/assign', [TicketController::class, 'assign']);
        Route::get('users/assignable', [UserController::class, 'assignable']);

        Route::apiResource('assets', AssetController::class)->except(['index', 'show']);

        Route::get('users', [UserController::class, 'index']);
        Route::put('users/{user}', [UserController::class, 'update']);
    });
});
