<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //return view('welcome');
    return redirect()->to('/login');
});

Auth::routes();
   
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/testemailsend', 'UserController@testemailsend');
Route::get('/crontest', 'Controller@crontest');
Route::get('/chkevent', 'UserController@chkevent');

Route::get('/maketodaystip', 'TipController@maketodaystip');
Route::get('/subscriptionReminder', 'Controller@subscriptionReminder');
Route::get('/lastreminder', 'Controller@lastreminder');





Route::post('/getStatesByCountry','Controller@getStatesByCountry');

Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('config:cache');
    // return what you want
});


Route::group(['prefix' => 'front','middleware' => ['auth']], function() {    
     
});

Route::group(['prefix' => 'admin','middleware' => ['auth']], function() {    
    
    Route::resource('roles','RoleController');
    

    Route::resource('exams','ExamController');
    Route::any('/exams/ajaxData', 'ExamController@ajaxData'); 
    Route::post('/exams/destroy','ExamController@destroyBulk')->name('destroyExams');

    Route::resource('users','UserController');
    Route::any('/users/ajaxData', 'UserController@ajaxData');  
    Route::any('/users/settings/{id}', 'UserController@settings');
    Route::get('sendNotifications', 'UserController@sendNotifications')->name('sendNotifications');
    Route::get('sendNotificationAllUser', 'UserController@sendNotificationAllUser')->name('sendNotificationAllUser');
    Route::post('/sendNotificationUser', 'UserController@sendNotificationUser')->name('sendNotificationUser'); 

    Route::resource('categories','CategoryController');
    Route::any('/categories/ajaxData', 'CategoryController@ajaxData');  
    

    Route::resource('plans','PlanController');
    Route::any('/plans/ajaxData', 'PlanController@ajaxData'); 

    Route::resource('subscriptions','SubscriptionController');
    Route::any('/subscriptions/ajaxData', 'SubscriptionController@ajaxData'); 
    
    Route::resource('districts','DistrictController');
    Route::any('/districts/ajaxData', 'DistrictController@ajaxData'); 
    Route::post('/districts/destroy','DistrictController@destroyBulk')->name('destroyDistricts');

    Route::resource('blocks','BlockController');
    Route::any('/blocks/ajaxData', 'BlockController@ajaxData'); 
    Route::any('/blocks/ajaxGetBlockByDistrict', 'BlockController@ajaxGetBlockByDistrict'); 
    Route::post('/blocks/destroy','BlockController@destroyBulk')->name('destroyBlocks');

    Route::resource('schools','SchoolController');
    Route::any('/schools/ajaxData', 'SchoolController@ajaxData'); 
    Route::post('/schools/destroy','SchoolController@destroyBulk')->name('destroySchools');

    
    Route::resource('classes','ClassesController');
    Route::any('/classes/ajaxData', 'ClassesController@ajaxData'); 
    Route::post('/classes/destroy','ClassesController@destroyBulk')->name('destroyClasses');
    
    Route::any('/classes/ajaxGetSubjectByClass', 'ClassesController@ajaxGetSubjectByClass'); 
    Route::resource('subjects','SubjectController');
    Route::any('/subjects/ajaxData', 'SubjectController@ajaxData'); 
    Route::post('/subjects/destroy','SubjectController@destroyBulk')->name('destroySubjects');
    

    Route::any('/chapters/ajaxGetChapterBySubject', 'ChapterController@ajaxGetChapterBySubject'); 
    Route::resource('chapters','ChapterController');
    Route::any('/chapters/ajaxData', 'ChapterController@ajaxData'); 
    Route::post('/chapters/destroy','ChapterController@destroyBulk')->name('destroyChapter');
    

    Route::resource('questions','QuestionController');
    Route::any('/questions/ajaxData', 'QuestionController@ajaxData'); 
    Route::post('/questions/destroy','QuestionController@destroyBulk')->name('destroyQuestions');

    Route::resource('topics','TopicController');
    Route::any('/topics/ajaxData', 'TopicController@ajaxData'); 

    
    Route::resource('contactus', 'ContactusController');  
    Route::any('/contactus/ajaxData', 'ContactusController@ajaxData'); 

    Route::resource('pages','PageController');
    Route::any('/pages/ajaxData', 'PageController@ajaxData'); 

    Route::any('/uploads/csvupload', 'UploadController@csvupload'); 
    

});
Route::get('admin/uploads/export', [UploadController::class,'testExcl']);




