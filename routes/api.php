<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/endpoint/getOffice', [\App\Http\Controllers\PreferenceController::class, 'getOfficeEndpoint']); // get office
Route::get('/endpoint/getPersonnel/{id}', [\App\Http\Controllers\PreferenceController::class, 'getPersonnelEndpoint']); // get personnel
Route::post('/endpoint/kiosk', [\App\Http\Controllers\FeedbackController::class, 'kioskEndpoint']); // create feedback entry

Route::post('/auth/login', [\App\Http\Controllers\Auth\AuthController::class, 'authenticate']); // create authentication
Route::post('/auth/register', [\App\Http\Controllers\Auth\AuthController::class, 'register']); // create user

Route::middleware(['auth:sanctum'])->group(function () {
    // return $request->user();
    Route::get('/auth/user', [\App\Http\Controllers\Auth\AuthController::class, 'getUser']);
    /**
     * CLIENT
     */
    Route::post('/community/verify', [\App\Http\Controllers\Community\UserController::class, 'verifyUser']);
    Route::post('/community/update', [\App\Http\Controllers\Community\UserController::class, 'updateUser']);
    Route::post('/community/reset', [\App\Http\Controllers\Community\UserController::class, 'resetUser']);
    //
    Route::get('/community/feedback/{id}', [\App\Http\Controllers\Community\FeedbackController::class, 'index']);
    Route::post('/community/feedback', [\App\Http\Controllers\Community\FeedbackController::class, 'store']);
    Route::get('/community/feedback/response/{id}', [\App\Http\Controllers\Community\FeedbackController::class, 'getResponse']);
    Route::post('/community/feedback/response', [\App\Http\Controllers\Community\FeedbackController::class, 'response']);
    Route::post('/community/feedback/rating', [\App\Http\Controllers\Community\FeedbackController::class, 'rating']);
    //
    Route::get('/community/discussion', [\App\Http\Controllers\Community\DiscussionController::class, 'index']);
    Route::get('/community/discussion/{id}', [\App\Http\Controllers\Community\DiscussionController::class, 'getThread']);
    Route::post('/community/discussion/thread', [\App\Http\Controllers\Community\DiscussionController::class, 'thread']);
    Route::get('/community/discussion/poll/{discussionID}/{id}', [\App\Http\Controllers\Community\DiscussionController::class, 'getPoll']);
    Route::post('/community/discussion/poll', [\App\Http\Controllers\Community\DiscussionController::class, 'answer']);
    /**
     * 
     */
    Route::put('/my/account/{id}', [\App\Http\Controllers\MyController::class, 'updateUser']);
    Route::put('/my/setting/{id}', [\App\Http\Controllers\MyController::class, 'settingUser']);
    Route::put('/my/reset/{id}', [\App\Http\Controllers\MyController::class, 'resetUser']);
    /**
     * 
     */
    Route::get('/overview/{id}', [\App\Http\Controllers\PreferenceController::class, 'getOverview']); // get overview
    //
    Route::get('/getYear', [\App\Http\Controllers\PreferenceController::class, 'getYear']); // get year
    Route::get('/getSex', [\App\Http\Controllers\PreferenceController::class, 'getSex']); // get sex
    Route::get('/getRole', [\App\Http\Controllers\PreferenceController::class, 'getRole']); // get role
    Route::get('/getPosition', [\App\Http\Controllers\PreferenceController::class, 'getPosition']); // get position
    Route::get('/getOffice', [\App\Http\Controllers\PreferenceController::class, 'getOffice']); // get office
    Route::get('/getCategory', [\App\Http\Controllers\PreferenceController::class, 'getCategory']); // get category
    Route::get('/getPersonnel/{id}', [\App\Http\Controllers\PreferenceController::class, 'getPersonnel']); // get personnel
    /**
     * PREFERENCES
     */
    Route::get('/year', [\App\Http\Controllers\PreferenceYearController::class, 'index']); // get all year
    Route::post('/year', [\App\Http\Controllers\PreferenceYearController::class, 'store']); // store year
    Route::put('/year/{id}', [\App\Http\Controllers\PreferenceYearController::class, 'update']); // modify year
    Route::post('/year/disable/{id}', [\App\Http\Controllers\PreferenceYearController::class, 'disable']); // disable year
    Route::post('/year/enable/{id}', [\App\Http\Controllers\PreferenceYearController::class, 'enable']); // enable year
    
    Route::get('/office', [\App\Http\Controllers\PreferenceOfficeController::class, 'index']); // get all offices
    Route::post('/office', [\App\Http\Controllers\PreferenceOfficeController::class, 'store']); // store office
    Route::put('/office/{id}', [\App\Http\Controllers\PreferenceOfficeController::class, 'update']); // modify office
    Route::post('/office/disable/{id}', [\App\Http\Controllers\PreferenceOfficeController::class, 'disable']); // disable office
    Route::post('/office/enable/{id}', [\App\Http\Controllers\PreferenceOfficeController::class, 'enable']); // enable office
    
    Route::get('/position', [\App\Http\Controllers\PreferencePositionController::class, 'index']); // get all position
    Route::post('/position', [\App\Http\Controllers\PreferencePositionController::class, 'store']); // store position
    Route::put('/position/{id}', [\App\Http\Controllers\PreferencePositionController::class, 'update']); // modify position
    Route::post('/position/disable/{id}', [\App\Http\Controllers\PreferencePositionController::class, 'disable']); // disable position
    Route::post('/position/enable/{id}', [\App\Http\Controllers\PreferencePositionController::class, 'enable']); // enable position

    Route::get('/account', [\App\Http\Controllers\PreferenceAccountController::class, 'index']); // get all accounts
    Route::post('/account', [\App\Http\Controllers\PreferenceAccountController::class, 'store']); // store account
    Route::put('/account/{id}', [\App\Http\Controllers\PreferenceAccountController::class, 'update']); // modify account
    Route::post('/account/disable/{id}', [\App\Http\Controllers\PreferenceAccountController::class, 'disable']); // disable account
    Route::post('/account/enable/{id}', [\App\Http\Controllers\PreferenceAccountController::class, 'enable']); // enable account
    Route::put('/account/verify/{id}', [\App\Http\Controllers\PreferenceAccountController::class, 'verify']); // verify account
    Route::put('/account/reset/{id}', [\App\Http\Controllers\PreferenceAccountController::class, 'reset']); // reset account

    Route::get('/category', [\App\Http\Controllers\PreferenceCategoryController::class, 'index']); // get all category
    Route::post('/category', [\App\Http\Controllers\PreferenceCategoryController::class, 'store']); // store category
    Route::put('/category/{id}', [\App\Http\Controllers\PreferenceCategoryController::class, 'update']); // modify category
    Route::post('/category/disable/{id}', [\App\Http\Controllers\PreferenceCategoryController::class, 'disable']); // disable category
    Route::post('/category/enable/{id}', [\App\Http\Controllers\PreferenceCategoryController::class, 'enable']); // enable category

    Route::get('/message', [\App\Http\Controllers\PreferenceMessageController::class, 'index']); // get all messages
    Route::post('/message', [\App\Http\Controllers\PreferenceMessageController::class, 'store']); // store message
    Route::put('/message/{id}', [\App\Http\Controllers\PreferenceMessageController::class, 'update']); // modify message
    Route::post('/message/disable/{id}', [\App\Http\Controllers\PreferenceMessageController::class, 'disable']); // disable message
    Route::post('/message/enable/{id}', [\App\Http\Controllers\PreferenceMessageController::class, 'enable']); // enable message

    Route::get('/setting', [\App\Http\Controllers\SettingOfficeCategoryController::class, 'index']); // get all office/category setting
    Route::post('/setting', [\App\Http\Controllers\SettingOfficeCategoryController::class, 'store']); // store office/category
    Route::put('/setting/{id}', [\App\Http\Controllers\SettingOfficeCategoryController::class, 'update']); // modify store/category
    Route::post('/setting/disable/{id}', [\App\Http\Controllers\SettingOfficeCategoryController::class, 'disable']); // disable office/category
    Route::post('/setting/enable/{id}', [\App\Http\Controllers\SettingOfficeCategoryController::class, 'enable']); // enable office/category
    /**
     * FEEDBACK
     */
    Route::get('/feedback/overview/{id}', [\App\Http\Controllers\FeedbackController::class, 'getOverview']); // get all feedback overview
    Route::get('/feedback/list/{id}', [\App\Http\Controllers\FeedbackController::class, 'getList']); // get all feedback list
    Route::put('/feedback/receive/{id}', [\App\Http\Controllers\FeedbackController::class, 'receive']); // receive feedback
    Route::get('/feedback/detail/{id}', [\App\Http\Controllers\FeedbackController::class, 'getDetail']); // get all feedback detail
    Route::get('/feedback/response/{id}', [\App\Http\Controllers\FeedbackController::class, 'getResponse']); // get all feedback response
    Route::put('/feedback/response/{id}', [\App\Http\Controllers\FeedbackController::class, 'response']); // create feedback response
    Route::put('/feedback/complete/{id}', [\App\Http\Controllers\FeedbackController::class, 'complete']); // create feedback complete
    Route::put('/feedback/cancel/{id}', [\App\Http\Controllers\FeedbackController::class, 'cancel']); // create feedback cancel
    Route::get('/feedback/report/{id}', [\App\Http\Controllers\FeedbackController::class, 'getReport']); // get feedback report
    //
    Route::post('/feedback/entry/offline', [\App\Http\Controllers\FeedbackController::class, 'offline']); // create feedback entry
    Route::post('/feedback/entry/kiosk', [\App\Http\Controllers\FeedbackController::class, 'kiosk']); // create feedback entry
    //
    Route::get('/kiosk/{id}', [\App\Http\Controllers\PreferenceKioskController::class, 'index']); // get all kiosk
    Route::post('/kiosk', [\App\Http\Controllers\PreferenceKioskController::class, 'store']); // store kiosk
    Route::put('/kiosk/{id}', [\App\Http\Controllers\PreferenceKioskController::class, 'update']); // modify kiosk
    Route::post('/kiosk/disable/{id}', [\App\Http\Controllers\PreferenceKioskController::class, 'disable']); // disable kiosk
    Route::post('/kiosk/enable/{id}', [\App\Http\Controllers\PreferenceKioskController::class, 'enable']); // enable kiosk
    Route::get('/kiosk/detail/{id}', [\App\Http\Controllers\PreferenceKioskController::class, 'getKiosk']); // get kiosk detail
    Route::get('/kiosk/report/{id}', [\App\Http\Controllers\PreferenceKioskController::class, 'getReport']); // get kiosk report
    Route::get('/kiosk/office/report/{id}', [\App\Http\Controllers\PreferenceKioskController::class, 'getOfficeReport']); // get office kiosk report
    /**
     * DISCUSSION
     */
    Route::get('/discussion/overview/{id}', [\App\Http\Controllers\DiscussionController::class, 'getOverview']); // get all discussion overview
    Route::get('/discussion/list/{id}', [\App\Http\Controllers\DiscussionController::class, 'getList']); // get all discussion list
    Route::post('/discussion', [\App\Http\Controllers\DiscussionController::class, 'store']); // store discussion
    Route::put('/discussion/{id}', [\App\Http\Controllers\DiscussionController::class, 'update']); // modify discussion
    Route::post('/discussion/disable/{id}', [\App\Http\Controllers\DiscussionController::class, 'disable']); // disable discussion
    Route::post('/discussion/enable/{id}', [\App\Http\Controllers\DiscussionController::class, 'enable']); // enable discussion
    Route::get('/discussion/detail/{id}', [\App\Http\Controllers\DiscussionController::class, 'getDetail']); // get discussion detail
    Route::get('/discussion/thread/{id}', [\App\Http\Controllers\DiscussionController::class, 'getThread']); // get thread
    Route::get('/discussion/answer/{id}', [\App\Http\Controllers\DiscussionController::class, 'getAnswer']); // get answer
    Route::put('/discussion/thread/{id}', [\App\Http\Controllers\DiscussionController::class, 'thread']); // store thread
    Route::post('/discussion/thread/disable/{id}', [\App\Http\Controllers\DiscussionController::class, 'disableThread']); // disable answer
    Route::post('/discussion/thread/enable/{id}', [\App\Http\Controllers\DiscussionController::class, 'enableThread']); // enable answer
    Route::post('/discussion/answer', [\App\Http\Controllers\DiscussionController::class, 'answer']); // store answer
    Route::post('/discussion/answer/disable/{id}', [\App\Http\Controllers\DiscussionController::class, 'disableAnswer']); // disable answer
    Route::post('/discussion/answer/enable/{id}', [\App\Http\Controllers\DiscussionController::class, 'enableAnswer']); // enable answer
    Route::get('/discussion/report/{id}', [\App\Http\Controllers\DiscussionController::class, 'getReport']); // get report
    /**
     * 
     */
    Route::get('/report/summary', [\App\Http\Controllers\ReportController::class, 'getSummary']); // summary
    Route::get('/report/performance', [\App\Http\Controllers\ReportController::class, 'getPerformance']); // performance
    //
    Route::get('/report/feedback/summary', [\App\Http\Controllers\ReportController::class, 'getFeedbackSummary']); // feedback summary
    Route::get('/report/feedback/category', [\App\Http\Controllers\ReportController::class, 'getFeedbackCategory']); // feedback category
    Route::get('/report/feedback/status', [\App\Http\Controllers\ReportController::class, 'getFeedbackStatus']); // feedback status
    Route::get('/report/feedback/office', [\App\Http\Controllers\ReportController::class, 'getFeedbackOffice']); // feedback office
    Route::get('/report/feedback/kiosk', [\App\Http\Controllers\ReportController::class, 'getFeedbackKiosk']); // feedback kiosk
    Route::get('/report/feedback/kiosk/office', [\App\Http\Controllers\ReportController::class, 'getFeedbackKioskOffice']); // feedback kiosk office
    //
    Route::get('/report/discussion/summary', [\App\Http\Controllers\ReportController::class, 'getDiscussionSummary']); // discussion summary
});
