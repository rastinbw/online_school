<?php

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
    return redirect('/admin');
});

// CRUD
Route::get('/admin/dashboard', function (){
    return redirect('/admin/student');
});

// Main Page routes
Route::get('/api/mainPage', 'API\MainPageController@sendMainPage');
Route::get('/api/courses', 'API\MainPageController@sendCourses');
Route::get('/api/teachers', 'API\MainPageController@sendTeachers');
Route::get('/api/links', 'API\MainPageController@sendLinks');
Route::get('/api/about', 'API\MainPageController@sendAbout');
Route::post('/api/parent', 'API\MainPageController@loginAsParent');

// Registration Routes
Route::post('/api/registration/code', 'API\RegistrationController@sendVerificationCode');
Route::post('/api/registration/confirm', 'API\RegistrationController@confirmPhoneNumber');
Route::post('/api/registration/complete', 'API\RegistrationController@completeRegistration');
Route::post('/api/login', 'API\RegistrationController@login');
Route::post('/api/token/check', 'API\RegistrationController@checkToken');
Route::post('/api/student/password/reset/link/send', 'API\RegistrationController@sendResetPasswordLink');
Route::get('/api/student/password/reset/form/{token}', 'API\RegistrationController@resetPasswordForm');
Route::post('/api/student/password/reset', 'API\RegistrationController@resetPassword');


// Profile Routes
Route::post('/api/profile/get', 'API\ProfileController@getProfile');
Route::post('/api/profile/set', 'API\ProfileController@setProfile');
Route::post('/api/profile/changePassword', 'API\ProfileController@changePassword');
Route::post('/api/profile/upload/nationalCardImage', 'API\ProfileController@uploadNationalCardImage');
Route::post('/api/profile/upload/enrollmentCertificateImage', 'API\ProfileController@uploadEnrollmentCertificateImage');

// Data Routes
Route::get('/api/grades', 'API\DataController@getGradeList');
Route::get('/api/fields', 'API\DataController@getFieldList');
Route::get('/api/categories', 'API\DataController@getCategoryList');

// Plans Routes
Route::get('/api/plans/{category_id}/{tag_id}/{grade_id}/{field_id}', 'API\PlansController@getPlanList');
Route::post('/api/student/plans', 'API\PlansController@getStudentPlanList');
Route::post('/api/student/coursesbyday', 'API\PlansController@getStudentCoursesByDay');
Route::post('/api/plan/courses', 'API\PlansController@getPlanCourses');
Route::post('/api/session/videolink', 'API\PlansController@getSessionVideoLink');
Route::post('/api/session/videolink/download', 'API\PlansController@getSessionVideoDownloadLink');
Route::get('/api/course/online', 'API\PlansController@getCurrentOnlineCourse');
Route::post('/api/plan/free/register', 'API\PlansController@registerInFreePlan');

// Transaction Routes
Route::post('/api/records/financial', 'API\TransactionController@getStudentFinancialRecords');
Route::get('/api/plan/pay/{token}/{plan_id}/{payment_type}/{installment_type_id}/{discount_code}', 'API\TransactionController@payForPlan');
Route::get('/api/plan/pay/done', 'API\TransactionController@payForPlanIsDone');
Route::get('/api/installment/pay/{token}/{installment_id}', 'API\TransactionController@payForInstallment');
Route::get('/api/installment/pay/done', 'API\TransactionController@payForInstallmentIsDone');

// Tests Routes
Route::post('/api/student/tests', 'API\TestsController@getStudentTestList');

// Arvan Routes
