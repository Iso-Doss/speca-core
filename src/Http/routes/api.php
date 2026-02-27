<?php

use Illuminate\Support\Facades\Route;
use Speca\SpecaCore\Http\Controllers\Api\V1\ActivityLogController;
use Speca\SpecaCore\Http\Controllers\Api\V1\AuthController;
use Speca\SpecaCore\Http\Controllers\Api\V1\CountryController;
use Speca\SpecaCore\Http\Controllers\Api\V1\UrlShortener\UrlController;
use Speca\SpecaCore\Http\Controllers\Api\V1\UrlShortener\UrlHistoryController;
use Speca\SpecaCore\Http\Controllers\Api\V1\UserController;
use Speca\SpecaCore\Http\Controllers\Api\V1\UserPermissionCategoryController;
use Speca\SpecaCore\Http\Controllers\Api\V1\UserPermissionController;
use Speca\SpecaCore\Http\Controllers\Api\V1\UserProfileController;
use Speca\SpecaCore\Http\Controllers\Api\V1\UserRoleController;

Route::prefix(config('speca-core.route.api.prefix', 'api/v1'))->name(config('speca-core.name', 'speca-core').'.')->group(function () {
    Route::controller(AuthController::class)->prefix('/auth')->name('auth.')->group(function () {
        Route::post('sign-up', [AuthController::class, 'signUp'])->name('sign-up');
        Route::post('sign-in', [AuthController::class, 'signIn'])->name('sign-in');
        Route::post('sign-out', [AuthController::class, 'signOut'])->name('sign-out');

        Route::name('google.')->prefix('/google')->group(function () {
            Route::get('/', [AuthController::class, 'google'])->name('index');
            Route::get('/callback', [AuthController::class, 'googleCallback'])->name('google-callback');
        });

        Route::controller(AuthController::class)->prefix('/password')->name('password.')->group(function () {
            Route::post('forgot', [AuthController::class, 'forgotPassword'])->name('forgot');
            Route::post('reset', [AuthController::class, 'resetPassword'])->name('reset');
        });
    });

    // Route::middleware('oauth')->group(function () {
    // User permission category endpoints.
    Route::controller(UserPermissionCategoryController::class)->prefix('/user-category-permission')->name('user-category-permission.')->group(function () {
        Route::get('', [UserPermissionCategoryController::class, 'index'])->name('index');
        Route::post('', [UserPermissionCategoryController::class, 'create'])->name('create');
        Route::get('{userPermissionCategoryId}', [UserPermissionCategoryController::class, 'show'])->name('show');
        Route::put('{userPermissionCategoryId}', [UserPermissionCategoryController::class, 'update'])->name('update');
        Route::put('/enable-disable/{userPermissionCategoryId}', [UserPermissionCategoryController::class, 'enableOrDisable'])->name('enable-disable');
        Route::put('/group-action/{action}', [UserPermissionCategoryController::class, 'groupAction'])->name('group-action');
        Route::post('/export', [UserPermissionCategoryController::class, 'export'])->name('export');
        Route::delete('{userPermissionCategoryId}', [UserPermissionCategoryController::class, 'delete'])->name('delete');
        Route::get('restore/{userPermissionCategoryId}', [UserPermissionCategoryController::class, 'restore'])->name('restore');
        Route::delete('force-delete/{userPermissionCategoryId}', [UserPermissionCategoryController::class, 'forceDelete'])->name('force-delete');
    });

    // User permission endpoints.
    Route::controller(UserPermissionController::class)->prefix('/user-permission')->name('user-permission.')->group(function () {
        Route::get('', [UserPermissionController::class, 'index'])->name('index');
        Route::post('', [UserPermissionController::class, 'create'])->name('create');
        Route::get('{userPermissionId}', [UserPermissionController::class, 'show'])->name('show');
        Route::put('{userPermissionId}', [UserPermissionController::class, 'update'])->name('update');
        Route::put('/enable-disable/{userPermissionId}', [UserPermissionController::class, 'enableOrDisable'])->name('enable-disable');
        Route::put('/group-action/{action}', [UserPermissionController::class, 'groupAction'])->name('group-action');
        Route::post('/export', [UserPermissionController::class, 'export'])->name('export');
        Route::delete('{userPermissionId}', [UserPermissionController::class, 'delete'])->name('delete');
        Route::get('restore/{userPermissionId}', [UserPermissionController::class, 'restore'])->name('restore');
        Route::delete('force-delete/{userPermissionId}', [UserPermissionController::class, 'forceDelete'])->name('force-delete');
    });

    // User role endpoints.
    Route::controller(UserRoleController::class)->prefix('/user-role')->name('user-role.')->group(function () {
        Route::get('', [UserRoleController::class, 'index'])->name('index');
        Route::post('', [UserRoleController::class, 'create'])->name('create');
        Route::get('{userRoleId}', [UserRoleController::class, 'show'])->name('show');
        Route::put('{userRoleId}', [UserRoleController::class, 'update'])->name('update');
        Route::put('/enable-disable/{userRoleId}', [UserRoleController::class, 'enableOrDisable'])->name('enable-disable');
        Route::put('/group-action/{action}', [UserRoleController::class, 'groupAction'])->name('group-action');
        Route::post('/export', [UserRoleController::class, 'export'])->name('export');
        Route::delete('{userRoleId}', [UserRoleController::class, 'delete'])->name('delete');
        Route::get('restore/{userRoleId}', [UserRoleController::class, 'restore'])->name('restore');
        Route::delete('force-delete/{userRoleId}', [UserRoleController::class, 'forceDelete'])->name('force-delete');
    });

    // User profile endpoints.
    Route::controller(UserProfileController::class)->prefix('/user-profile')->name('user-profile.')->group(function () {
        Route::get('', [UserProfileController::class, 'index'])->name('index');
        Route::post('', [UserProfileController::class, 'create'])->name('create');
        Route::get('{userProfileId}', [UserProfileController::class, 'show'])->name('show');
        Route::put('{userProfileId}', [UserProfileController::class, 'update'])->name('update');
        Route::put('{userProfileId}/enable-disable/{status}', [UserProfileController::class, 'enableOrDisable'])->name('enable-disable');
        Route::put('/group-action/{action}', [UserProfileController::class, 'groupAction'])->name('group-action');
        Route::post('/export', [UserProfileController::class, 'export'])->name('export');
        Route::delete('{userProfileId}', [UserProfileController::class, 'delete'])->name('delete');
        Route::get('restore/{userProfileId}', [UserProfileController::class, 'restore'])->name('restore');
        Route::delete('force-delete/{userProfileId}', [UserProfileController::class, 'forceDelete'])->name('force-delete');
    });

    // User endpoints.
    Route::controller(UserController::class)->prefix('/user')->name('user.')->group(function () {
        Route::get('', [UserController::class, 'index'])->name('index');
        Route::post('', [UserController::class, 'create'])->name('create');
        Route::put('/resend-confirmation-email/{email}', [UserController::class, 'resendConfirmationEmail'])->name('resend-confirmation-email');
        Route::get('{userId}', [UserController::class, 'show'])->name('show');
        Route::put('{userId}', [UserController::class, 'update'])->name('update');
        Route::put('{userId}/enable-disable/{status}', [UserController::class, 'enableOrDisable'])->name('enable-disable');
        Route::put('/group-action/{action}', [UserController::class, 'groupAction'])->name('group-action');
        Route::post('/export', [UserController::class, 'export'])->name('export');
        Route::delete('{userId}', [UserController::class, 'delete'])->name('delete');
        Route::get('restore/{userId}', [UserController::class, 'restore'])->name('restore');
        Route::delete('force-delete/{userId}', [UserController::class, 'forceDelete'])->name('force-delete');
    });

    // Activity log endpoints.
    Route::controller(ActivityLogController::class)->prefix('/activity-log')->name('activity-log.')->group(function () {
        Route::get('', [ActivityLogController::class, 'index'])->name('index');
        Route::post('/export', [ActivityLogController::class, 'export'])->name('export');
    });

    // User two-factor endpoints.
    // Route::controller(TwoFactorController::class)->middleware('two_factor')->name('two-factor.')->prefix('/two-factor')->group(function () {
    //    Route::post('re-send-code', [TwoFactorController::class, 'reSendTwoFactorCode'])->name('re-send-code');
    //    Route::post('verify', [TwoFactorController::class, 'verifyTwoFactorCode'])->name('verify');
    //    Route::get('emergency-codes', [TwoFactorController::class, 'emergencyCodes'])->name('emergency-codes');
    //    Route::post('mark-emergency-codes-as-copied', [TwoFactorController::class, 'markEmergencyCodesAsCopied'])->name('mark-emergency-codes-as-copied');
    // });

    // Channel endpoints.
    // Route::controller(ChannelController::class)->prefix('channel')->name('channel.')->group(function () {
    //    Route::get('', [ChannelController::class, 'index'])->name('list')->withoutMiddleware('oauth');
    //    Route::post('', [ChannelController::class, 'create'])->name('create');
    //    Route::put('{channelId}', [ChannelController::class, 'update'])->name('update');
    //    Route::put('{channelId}/enable-disable/{status}', [ChannelController::class, 'enableOrDisable'])->name('enable-disable');
    //    Route::delete('{channelId}', [ChannelController::class, 'delete'])->name('delete');
    // });

    // Country endpoints.
    Route::controller(CountryController::class)->prefix('country')->name('country.')->group(function () {
        Route::get('', [CountryController::class, 'index'])->name('index');
        Route::post('', [CountryController::class, 'create'])->name('create');
        Route::get('{countryId}', [CountryController::class, 'show'])->name('show');
        Route::put('{countryId}', [CountryController::class, 'update'])->name('update');
        Route::put('/enable-disable/{countryId}', [CountryController::class, 'enableOrDisable'])->name('enable-disable');
        Route::delete('{countryId}', [CountryController::class, 'delete'])->name('delete');
        Route::get('restore/{countryId}', [CountryController::class, 'restore'])->name('restore');
        Route::delete('force-delete/{countryId}', [CountryController::class, 'forceDelete'])->name('force-delete');
    });

    // Notification endpoints.
    // Route::controller(NotificationController::class)->prefix("/notification")->name('notification.')->group(function () {
    //    Route::get('', 'index')->name('list');
    //    Route::put('mark-as-read-or-as-unread/{notificationId}/{status}', 'markAsReadOrAsUnread')->name('mark-as-read-or-as-unread');
    //    Route::delete('{id}', 'delete')->name('delete');
    //    Route::put('mark-all-as-read-or-as-unread/{status}', 'markAllAsReadOrAsUnread')->name('mark-all-as-read-or-as-unread');
    //    Route::delete('delete-all', 'deleteAll')->name('delete-all');
    // });
    // });

    // Url shortener url endpoints.
    Route::prefix('url-shortener/')->name('url-shortener.')->group(function () {
        Route::controller(UrlController::class)->prefix('/url')->name('url.')->group(function () {
            Route::get('', [UrlController::class, 'index'])->name('index');
            Route::post('', [UrlController::class, 'create'])->name('create');
            Route::post('create-many', [UrlController::class, 'createMany'])->name('create-many');
            Route::get('{urlId}', [UrlController::class, 'show'])->name('show');
            Route::put('{urlId}', [UrlController::class, 'update'])->name('update');
            Route::put('{urlId}/enable-disable/{status}', [UrlController::class, 'enableOrDisable'])->name('enable-disable');
            Route::delete('{urlId}', [UrlController::class, 'delete'])->name('delete');
            Route::get('restore/{urlId}', [UrlController::class, 'restore'])->name('restore');
            Route::delete('force-delete/{urlId}', [UrlController::class, 'forceDelete'])->name('force-delete');
        });

        // Url shortener url history endpoints.
        Route::controller(UrlHistoryController::class)->prefix('/url-history')->name('url-history.')->group(function () {
            Route::get('', [UrlHistoryController::class, 'index'])->name('index');
            Route::post('', [UrlHistoryController::class, 'create'])->name('create');
            Route::get('{urlHistoryId}', [UrlHistoryController::class, 'show'])->name('show');
            Route::put('{urlHistoryId}', [UrlHistoryController::class, 'update'])->name('update');
            Route::put('{urlHistoryId}/enable-disable/{status}', [UrlHistoryController::class, 'enableOrDisable'])->name('enable-disable');
            Route::delete('{urlHistoryId}', [UrlHistoryController::class, 'delete'])->name('delete');
            Route::get('restore/{urlHistoryId}', [UrlHistoryController::class, 'restore'])->name('restore');
            Route::delete('force-delete/{urlHistoryId}', [UrlHistoryController::class, 'forceDelete'])->name('force-delete');
        });
    });
});
