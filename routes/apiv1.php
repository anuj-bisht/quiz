<?php

use Illuminate\Http\Request;

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
Use App\Rest;

Route::post('register', 'UserController@register');

Route::post('login', 'UserController@authenticate');
Route::post('loginSocial', 'UserController@loginSocial');
Route::post('loginNormal', 'UserController@loginNormal');
Route::post('sendotp', 'UserController@sendotp');
Route::post('resendotp', 'UserController@Resendotp');
Route::get('testmail', 'ExamController@testmail');
Route::post('getsetting', 'CommonController@getsetting');
Route::post('socialcheck', 'UserController@socialcheck');
Route::any('testNotification', 'UserController@testNotification');
Route::post('contactus', 'CommonController@contactus');

Route::get('getAllDistrict', 'CommonController@getAllDistrict');
Route::get('getAllBlocks', 'CommonController@getAllBlocksByDistrictId');
Route::get('getAllClasses', 'CommonController@getAllClasses');
Route::post('getAllSchool', 'CommonController@getAllSchool');

Route::post('getSubject', 'CommonController@getSubject');

Route::post('getChapterBySubject', 'CommonController@getChapterBySubject');
Route::post('getPlanList', 'CommonController@getPlanList');
Route::post('getPlanDetails', 'CommonController@getPlanDetails');
Route::get('getPlanList', 'CommonController@getPlanList')->middleware('localization');





Route::group(['middleware' => ['jwt.verify']], function() {

    
    Route::post('/getExamList', 'CommonController@getExamList');
    Route::post('/exams/chapterWiseExam','ExamController@chanperWiseExam');
    Route::post('/exams/startExam', 'ExamController@startExam');
    Route::post('/exams/finishExam', 'ExamController@finishExam');
    Route::post('/exams/list','ExamController@examsList');
    Route::post('/exams/result','ExamController@examsResult');
    Route::post('/exams/answersheet','ExamController@answersheet');
    Route::post('/exams/examHistrory','ExamController@examHistrory');
    Route::post('/exams/check','ExamController@checkExamExist');
    Route::post('/exams/subjectWiseRank','ExamController@subjectWiseRank');
    Route::post('/exams/globalRank','ExamController@globalRank');
    Route::post('/exams/userRank','ExamController@userRank');

    Route::post('search', 'CommonController@search');
    

    Route::post('/users/getProfileData', 'UserController@getProfileData');
    Route::post('/users/subscriptions', 'UserController@UserSubscriptions');

    Route::post('/orders/generateOrder', 'OrderController@generateOrder');
    Route::post('/orders/verifyPayment', 'OrderController@verifyPayment');



    Route::post('apilogout', 'UserController@apilogout');     
    Route::post('changePassword', 'UserController@changePassword');    
    Route::post('editMyProfile', 'UserController@editMyProfile');             
    Route::post('/users/getProfile', 'UserController@getProfile');
    Route::post('/users/getTrainerByCategory', 'UserController@getTrainerByCategory');
    Route::post('/users/editMyProfile', 'UserController@editMyProfile');
    Route::post('/users/getMyProfile', 'UserController@getMyProfile');
    
    Route::post('/plans/getPlanByCategory', 'PlanController@getPlanByCategory');
    
    
    Route::post('/users/getMyNotifications', 'UserController@getMyNotifications');    
    Route::post('/users/getSettings', 'UserController@getSettings');
    Route::post('/orders/getsubscription', 'OrderController@getsubscription');

    Route::post('/myExamList', 'CommonController@myExamList');

    
    Route::post('/users/addReview', 'UserController@addReview');    
        
    
});

