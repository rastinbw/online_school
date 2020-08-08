<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    CRUD::resource('course', 'CourseCrudController');
    CRUD::resource('field', 'FieldCrudController');
    CRUD::resource('grade', 'GradeCrudController');
    CRUD::resource('plan', 'PlanCrudController');
    CRUD::resource('test', 'TestCrudController');
    CRUD::resource('tag', 'TagCrudController');
    CRUD::resource('category', 'CategoryCrudController');
    CRUD::resource('student', 'StudentCrudController');
    CRUD::resource('teacher', 'TeacherCrudController');
    CRUD::resource('session', 'SessionCrudController');
    CRUD::resource('link', 'LinkCrudController');
    CRUD::resource('about', 'AboutCrudController');
    CRUD::resource('transaction', 'TransactionCrudController');
    CRUD::resource('installmentType', 'InstallmentTypeCrudController');
    CRUD::resource('discountCode', 'DiscountCodeCrudController');
    CRUD::resource('help', 'HelpCrudController');
    CRUD::resource('sliderPlan', 'SliderPlanCrudController');
    CRUD::resource('smsTemplate', 'SmsTemplateCrudController');


    // Course Crud Routes
    Route::group(['prefix' => 'course/search/{course_id}'], function () {
        CRUD::resource('session', 'CourseSessionCrudController');
        CRUD::resource('test', 'CourseTestCrudController');

        // Test Crud Routes
        Route::group(['prefix' => 'test/{test_id}'], function () {
            Route::get('export_test_records', 'TestCrudController@exportTestRecords');
        });

        // Session Crud Routes
        Route::group(['prefix' => 'session/{session_id}'], function () {
            Route::get('attend_course', 'SessionCrudController@attendCourse');
        });
        Route::get('changeonline/{session_id}', 'SessionCrudController@changeOnline');
        Route::get('changeheld/{session_id}', 'SessionCrudController@changeHeld');
    });

    Route::group(['prefix' => 'course/{course_id}'], function () {
        Route::get('export_students', 'CourseCrudController@exportCourseStudents');
    });

    // Plan Crud Routes
    Route::group(['prefix' => 'plan/{plan_id}'], function () {
        Route::get('export_students', 'PlanCrudController@exportPlanStudents');
        Route::get('export_plan_transactions', 'PlanCrudController@exportPlanTransactions');
        Route::get('import_students', 'PlanCrudController@importPlanStudents');
    });
    Route::post('import_plan_students_excel', 'PlanCrudController@importPlanStudentsExcel');

    Route::group(['prefix' => 'plan/search/{plan_id}'], function () {
        CRUD::resource('message', 'PlanMessageCrudController');
    });

    // Student CRUD Other Routes
    Route::group(['prefix' => 'student/search/{student_id}'], function () {
        CRUD::resource('courseaccess', 'StudentCourseAccessCrudController');
        CRUD::resource('testaccess', 'StudentTestAccessCrudController');
    });

    Route::get('exportStudents', 'StudentCrudController@export_students');
    Route::get('student/{id}/export_student_test_records', 'StudentCrudController@exportStudentTestRecords');
    Route::get('student/{id}/export_student_installments', 'StudentCrudController@exportStudentInstallments');
    Route::get('student/{id}/export_student_referees', 'StudentCrudController@exportStudentReferees');
    Route::get('student/{id}/national_card/download', 'StudentCrudController@download_national_card');
    Route::get('student/{id}/enrollment_certificate/download', 'StudentCrudController@download_enrollment_certificate');

}); // this should be the absolute last line of this file
