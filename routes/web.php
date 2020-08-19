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
    return \File::get(public_path() . '/index.html');
});
Route::get('/signup', function () {
    return \File::get(public_path() . '/index.html');
});
Route::get('/register', function () {
    return \File::get(public_path() . '/index.html');
});
Route::get('/tutorials', function () {
    return \File::get(public_path() . '/index.html');
});
Route::get('/categories', function () {
    return \File::get(public_path() . '/index.html');
});
Route::get('/parentView/{code}', function () {
    return \File::get(public_path() . '/index.html');
});
Route::get('/lp/{id}', function () {
    return \File::get(public_path() . '/index.html');
});
Route::get('/plans/{id}', function () {
    return \File::get(public_path() . '/index.html');
});
Route::get('/dashboard/{payment_id}/transaction', function () {
    return \File::get(public_path() . '/index2.html');
});
Route::get('/dashboard', function () {
    return \File::get(public_path() . '/index2.html');
});
Route::get('/dashboard/{test_id}/scores', function () {
    return \File::get(public_path() . '/index2.html');
});
Route::get('dashboard/mytests', function () {
    return \File::get(public_path() . '/index2.html');
});
Route::get('/b9f3e91f74cc7967b90c.worker.js', function () {
    return \File::get(public_path() . '/b9f3e91f74cc7967b90c.worker.js');
});



// CRUD
Route::get('/admin/dashboard', function () {
    return redirect('/admin/student');
});

// Main Page routes
Route::get('/api/mainPage', 'API\MainPageController@sendMainPage');
Route::get('/api/courses', 'API\MainPageController@sendCourses');
Route::get('/api/teachers', 'API\MainPageController@sendTeachers');
Route::get('/api/links', 'API\MainPageController@sendLinks');
Route::get('/api/about', 'API\MainPageController@sendAbout');
Route::get('/api/parent/{parent_code}', 'API\ParentsPageController@getParentPage');

// Registration Routes
Route::post('/api/registration/code', 'API\RegistrationController@sendVerificationCode');
Route::post('/api/registration/confirm', 'API\RegistrationController@confirmPhoneNumber');
Route::post('/api/registration/complete', 'API\RegistrationController@completeRegistration');
Route::post('/api/login', 'API\RegistrationController@login');
Route::post('/api/token/check', 'API\RegistrationController@checkToken');
Route::post('/api/student/password/reset/link/send', 'API\RegistrationController@sendResetPasswordLink');
Route::get('/api/student/password/reset/form/{token}', 'API\RegistrationController@resetPasswordForm');
Route::post('/api/student/password/reset', 'API\RegistrationController@resetPassword');
Route::get('/api/lp/get/{lp_id}', 'API\RegistrationController@getLandingPage');

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
Route::get('/api/helps', 'API\DataController@getHelpList');
Route::get('/api/provinces', 'API\DataController@getProvinces');
Route::get('/api/cities/{p_id}', 'API\DataController@getCities');

// Plans Routes
Route::get('/api/plans/{category_id}/{tag_id}/{grade_id}/{field_id}', 'API\PlansController@getPlanList');
Route::post('/api/student/plans', 'API\PlansController@getStudentPlanList');
Route::post('/api/student/coursesbyday', 'API\PlansController@getStudentCoursesByDay');
Route::post('/api/plan/courses/{is_public}', 'API\PlansController@getPlanCourses');
Route::post('/api/session/videolink', 'API\PlansController@getSessionVideoLink');
Route::get('/api/session/videolink/download/{token}/{plan_id}/{session_id}', 'API\PlansController@getSessionVideoDownloadLink');
Route::post('/api/course/online', 'API\PlansController@getCurrentOnlineCourse');
Route::post('/api/plan/free/register', 'API\PlansController@registerInFreePlan');
Route::post('/api/plan/registered', 'API\PlansController@hasRegisteredToPlan');
Route::get('/api/plan/{plan_id}/info', 'API\PlansController@getPlanInfo');

// Transaction Routes
Route::post('/api/records/financial', 'API\TransactionController@getStudentFinancialRecords');
Route::get('/api/plan/pay/{token}/{plan_id}/{payment_type}/{installment_type_id}/{discount_code}', 'API\TransactionController@payForPlan');
Route::get('/api/plan/pay/done', 'API\TransactionController@payForPlanIsDone');
Route::get('/api/installment/pay/{token}/{installment_id}', 'API\TransactionController@payForInstallment');
Route::get('/api/installment/pay/done', 'API\TransactionController@payForInstallmentIsDone');
Route::get('/api/{transaction_id}/transaction', 'API\TransactionController@getTransaction');

// Tests Routes
Route::post('/api/student/tests', 'API\TestsController@getStudentTestList');
Route::post('/api/test/enter', 'API\TestsController@enterTest');
Route::post('/api/test/save', 'API\TestsController@saveTestRecord');
Route::get('/api/test/{test_id}/pdf', 'API\TestsController@getTestPdfFile');
Route::get('/api/time/current', 'API\TestsController@getCurrentTime');
Route::post('/api/test/answers', 'API\TestsController@getAnswers');

// Workbook Routes
Route::post('/api/student/test/workbook', 'API\WorkbookController@getWorkbook');
