<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TestRecordsExport;
use App\Includes\Constant;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\TestRequest as StoreRequest;
use App\Http\Requests\TestRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
use Hekmatinasser\Verta\Verta;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class TestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class TestCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Test');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/test');
        $this->crud->setEntityNameStrings('آزمون', 'آزمون ها');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $time = Verta::now(); //1396-02-02 15:32:08
        $first_tab = "اطلاعات کلی آزمون";
        $second_tab = "نحوه و زمان برگزاری";
        $third_tab = "گزینه های آزمون";
        $forth_tab = "فایل های آزمون";

        $this->crud->addFields([
            [
                'name' => 'title',
                'label' => '* عنوان',
                'type' => 'text',
                'tab' => $first_tab,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'has_negative_score',
                'label' => 'اعمال نمره منفی؟',
                'type' => 'radio',
                'tab' => $first_tab,
                'options' => [
                    0 => "خیر",
                    1 => "بله",
                ],
                'inline' => true,
                'default' => true,
            ],

            // RESULT ACCESS
            [
                'label' => 'دسترسی به کارنامه',
                'name' => 'result_access_type',
                'type' => 'toggle',
                'tab' => $first_tab,
                'inline' => true,
                'options' => [
                    Constant::$FREE_DATE_AND_TYPE => 'بلافاصله بعد از آزمون',
                    Constant::$SPECIAL_DATE_AND_TIME => 'در تاریخ و زمان معین'
                ],
                'hide_when' => [
                    Constant::$FREE_DATE_AND_TYPE =>
                        ['result_access_date_title', 'result_access_date_year','result_access_date_month',
                        'result_access_date_day', 'result_access_date_hour', 'result_access_date_min']
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:10px',
                ],
                'default' => Constant::$FREE_DATE_AND_TYPE,
            ],
            [
                'name' => 'result_access_date_title',
                'label' => 'تاریخ و زمان دسترسی به کارنامه',
                'type' => 'title',
                'tab' => $first_tab,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:10px',
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'result_access_date_min',
                'label' => 'دقیقه',
                'type' => 'number',
                'tab' => $first_tab,
                'default' => $time->minute,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'form-group col-md-1',
                ],
            ],
            [
                'name' => 'result_access_date_hour',
                'label' => 'ساعت',
                'type' => 'number',
                'tab' => $first_tab,
                'default' => $time->hour,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'form-group col-md-2',
                ],
            ],
            [
                'name' => 'result_access_date_day',
                'label' => 'روز',
                'type' => 'number',
                'tab' => $first_tab,
                'default' => $time->day,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'form-group col-md-3',
                ],
            ],
            [
                'name' => 'result_access_date_month',
                'label' => 'ماه',
                'type' => 'number',
                'tab' => $first_tab,
                'default' => $time->month,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'form-group col-md-3',
                ],
            ],
            [
                'name' => 'result_access_date_year',
                'label' => 'سال',
                'type' => 'number',
                'tab' => $first_tab,
                'default' => $time->year,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'form-group col-md-3',
                ],
            ],

            // QA ACCESS
            [
                'label' => 'دسترسی به سوالات و پاسخنامه',
                'name' => 'qa_access_type',
                'type' => 'toggle',
                'tab' => $first_tab,
                'inline' => true,
                'options' => [
                    Constant::$FREE_DATE_AND_TYPE => 'بلافاصله بعد از آزمون',
                    Constant::$SPECIAL_DATE_AND_TIME => 'در تاریخ و زمان معین'
                ],
                'hide_when' => [
                    Constant::$FREE_DATE_AND_TYPE => ['qa_access_date_title', 'qa_access_date_year', 'qa_access_date_month', 'qa_access_date_day',
                         'qa_access_date_hour', 'qa_access_date_min']
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:10px',
                ],
                'default' => Constant::$FREE_DATE_AND_TYPE,
            ],
            [
                'name' => 'qa_access_date_title',
                'label' => 'تاریخ و زمان دسترسی به سولات و پاسخنامه',
                'type' => 'title',
                'tab' => $first_tab,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:10px',
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'qa_access_date_min',
                'label' => 'دقیقه',
                'type' => 'number',
                'tab' => $first_tab,
                'default' => $time->minute,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'form-group col-md-1',
                ],
            ],
            [
                'name' => 'qa_access_date_hour',
                'label' => 'ساعت',
                'type' => 'number',
                'tab' => $first_tab,
                'default' => $time->hour,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'form-group col-md-2',
                ],
            ],
            [
                'name' => 'qa_access_date_day',
                'label' => 'روز',
                'type' => 'number',
                'tab' => $first_tab,
                'default' => $time->day,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'form-group col-md-3',
                ],
            ],
            [
                'name' => 'qa_access_date_month',
                'label' => 'ماه',
                'type' => 'number',
                'tab' => $first_tab,
                'default' => $time->month,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'form-group col-md-3',
                ],
            ],
            [
                'name' => 'qa_access_date_year',
                'label' => 'سال',
                'type' => 'number',
                'tab' => $first_tab,
                'default' => $time->year,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'form-group col-md-3',
                ],
            ],
            [
                'name' => 'check_for_tests_overlapping',
                'label' => 'بررسی همزمانی با آزمون های دیگر',
                'type' => 'checkbox',
                'default' => true,
            ],
            [
                'name' => 'check_for_sessions_overlapping',
                'label' => 'بررسی همزمانی با جلسات کلاس ها',
                'type' => 'checkbox',
                'default' => true,
            ],
        ], 'update/create/both');

        $this->crud->addFields([
             [
                 'label' => 'نحوه زمان بندی آزمون',
                 'name' => 'exam_holding_type',
                 'type' => 'toggle',
                 'inline' => true,
                 'tab' => $second_tab,
                 'options' => [
                     Constant::$FREE_DATE_AND_TYPE => 'شناور',
                     Constant::$SPECIAL_DATE_AND_TIME => 'تاریخ و زمان مقرر',
                 ],
                 'hide_when' => [
                     Constant::$SPECIAL_DATE_AND_TIME => ['exam_duration']
                 ],
                 'wrapperAttributes' => [
                     'style' => 'margin-top:10px',
                 ],
                 'default' => Constant::$SPECIAL_DATE_AND_TIME,
             ],
             // TEST START DATE AND TIME
             [
                 'name' => 'exam_date_start_title',
                 'label' => 'تاریخ و زمان شروع آزمون',
                 'type' => 'title',
                 'tab' => $second_tab,
                 'attributes' => [
                     'dir' => 'rtl',
                 ],
                 'wrapperAttributes' => [
                     'style' => 'margin-top:10px',
                     'dir' => 'rtl',
                 ],
             ],
             [
                 'name' => 'exam_date_start_min',
                 'label' => 'دقیقه',
                 'type' => 'number',
                 'tab' => $second_tab,
                 'default' => $time->minute,
                 'attributes' => [
                     'dir' => 'rtl',
                 ],
                 'wrapperAttributes' => [
                     'dir' => 'rtl',
                     'class' => 'form-group col-md-1',
                 ],
             ],
             [
                 'name' => 'exam_date_start_hour',
                 'label' => 'ساعت',
                 'type' => 'number',
                 'tab' => $second_tab,
                 'default' => $time->hour,
                 'attributes' => [
                     'dir' => 'rtl',
                 ],
                 'wrapperAttributes' => [
                     'dir' => 'rtl',
                     'class' => 'form-group col-md-2',
                 ],
             ],
             [
                 'name' => 'exam_date_start_day',
                 'label' => 'روز',
                 'type' => 'number',
                 'tab' => $second_tab,
                 'default' => $time->day,
                 'attributes' => [
                     'dir' => 'rtl',
                 ],
                 'wrapperAttributes' => [
                     'dir' => 'rtl',
                     'class' => 'form-group col-md-3',
                 ],
             ],
             [
                 'name' => 'exam_date_start_month',
                 'label' => 'ماه',
                 'type' => 'number',
                 'tab' => $second_tab,
                 'default' => $time->month,
                 'attributes' => [
                     'dir' => 'rtl',
                 ],
                 'wrapperAttributes' => [
                     'dir' => 'rtl',
                     'class' => 'form-group col-md-3',
                 ],
             ],
             [
                 'name' => 'exam_date_start_year',
                 'label' => 'سال',
                 'type' => 'number',
                 'tab' => $second_tab,
                 'default' => $time->year,
                 'attributes' => [
                     'dir' => 'rtl',
                 ],
                 'wrapperAttributes' => [
                     'dir' => 'rtl',
                     'class' => 'form-group col-md-3',
                 ],
             ],

             // TEST FINISH DATE AND TIME
             [
                 'name' => 'exam_date_finish_title',
                 'label' => 'تاریخ و زمان پایان آزمون',
                 'type' => 'title',
                 'tab' => $second_tab,
                 'attributes' => [
                     'dir' => 'rtl',
                 ],
                 'wrapperAttributes' => [
                     'style' => 'margin-top:10px',
                     'dir' => 'rtl',
                 ],
             ],
             [
                 'name' => 'exam_date_finish_min',
                 'label' => 'دقیقه',
                 'type' => 'number',
                 'tab' => $second_tab,
                 'default' => $time->minute,
                 'attributes' => [
                     'dir' => 'rtl',
                 ],
                 'wrapperAttributes' => [
                     'dir' => 'rtl',
                     'class' => 'form-group col-md-1',
                 ],
             ],
             [
                 'name' => 'exam_date_finish_hour',
                 'label' => 'ساعت',
                 'type' => 'number',
                 'tab' => $second_tab,
                 'default' => $time->hour,
                 'attributes' => [
                     'dir' => 'rtl',
                 ],
                 'wrapperAttributes' => [
                     'dir' => 'rtl',
                     'class' => 'form-group col-md-2',
                 ],
             ],
             [
                 'name' => 'exam_date_finish_day',
                 'label' => 'روز',
                 'type' => 'number',
                 'tab' => $second_tab,
                 'default' => $time->day,
                 'attributes' => [
                     'dir' => 'rtl',
                 ],
                 'wrapperAttributes' => [
                     'dir' => 'rtl',
                     'class' => 'form-group col-md-3',
                 ],
             ],
             [
                 'name' => 'exam_date_finish_month',
                 'label' => 'ماه',
                 'type' => 'number',
                 'tab' => $second_tab,
                 'default' => $time->month,
                 'attributes' => [
                     'dir' => 'rtl',
                 ],
                 'wrapperAttributes' => [
                     'dir' => 'rtl',
                     'class' => 'form-group col-md-3',
                 ],
             ],
             [
                 'name' => 'exam_date_finish_year',
                 'label' => 'سال',
                 'type' => 'number',
                 'tab' => $second_tab,
                 'default' => $time->year,
                 'attributes' => [
                     'dir' => 'rtl',
                 ],
                 'wrapperAttributes' => [
                     'dir' => 'rtl',
                     'class' => 'form-group col-md-3',
                 ],
             ],
             [
                 'name' => 'exam_duration',
                 'label' => 'مهلت آزمون (دقیقه)',
                 'tab' => $second_tab,
                 'type' => 'number',
                 'attributes' => [
                     'dir' => 'rtl',
                 ],
                 'wrapperAttributes' => [
                     'style' => 'margin-top:10px',
                     'dir' => 'rtl',
                 ],
             ],
         ], 'update/create/both');

        $this->crud->addFields([
            [
                'name' => 'options',
                'label' => 'گزینه های آزمون',
                'type' => 'child',
                'entity_singular' => 'اضافه کردن گزینه',
                'tab' => $third_tab,
                'columns' => [
                    [
                        'label' => 'شماره سوال',
                        'type' => 'child_text',
                        'name' => 'q_number',
                    ],
                    [
                        'label' => 'گزینه صحیح',
                        'type' => 'child_select',
                        'name' => 'co_number',
                        'data' => [1,2,3,4],
                    ],
                ],
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'factors',
                'label' => 'درس های آزمون (این فیلد اختیاری است ولی درصورت مشخص کردن درس باید تمام گزینه های آزمون را پوشش دهد)',
                'type' => 'child',
                'entity_singular' => 'اضافه',
                'tab' => $third_tab,
                'columns' => [
                    [
                        'label' => 'از شماره سوال',
                        'type' => 'child_text',
                        'name' => 'q_number_from',
                    ],
                    [
                        'label' => 'تا شماره سوال',
                        'type' => 'child_text',
                        'name' => 'q_number_to',
                    ],
                    [
                        'label' => 'عنوان درس',
                        'type' => 'child_text',
                        'name' => 'lesson_title',
                    ],
                    [
                        'label' => 'ضریب',
                        'type' => 'child_text',
                        'name' => 'value',
                    ],
                ],
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],

        ], 'update/create/both');

        $this->crud->addFields([
            [ // Upload
                'name' => 'questions_file',
                'label' => '<label style="color:#e55619">( فایل انتخابی باید به فرمت
                            <label style="font-family:Arial, Helvetica, sans-serif;">pdf</label> و حداکثر حجم 5 مگابایت باشد )</label> فایل سوالات',
                'type' => 'upload',
                'upload' => true,
                'disk' => 'public',
                'tab' => $forth_tab
            ],
            [ // Upload
                'name' => 'answers_file',
                'label' => '<label style="color:#e55619">( فایل انتخابی باید به فرمت
                            <label style="font-family:Arial, Helvetica, sans-serif;">pdf</label> و حداکثر حجم 5 مگابایت باشد )</label> فایل پاسخنامه',                'type' => 'upload',
                'upload' => true,
                'disk' => 'public',
                'tab' => $forth_tab
            ],
        ], 'update/create/both');

        $this->crud->addColumns([
            [
                'name' => 'title',
                'label' => 'عنوان'
            ],
            [
                'name' => "result_access_date",
                'label' => "تاریخ دسترسی به کارنامه"
            ],
            [
                'name' => "qa_access_date",
                'label' => "تاریخ دسترسی به سوالات و پاسخنامه"
            ],
            [
                'name' => 'has_negative_score',
                'label' => 'نمره منفی',
                'type' => 'radio',
                'options' => [ // the key will be stored in the db, the value will be shown as label;
                    1 => 'دارد',
                    0  => 'دارد',
                ],
                // optional
                'inline' => true, // show the radios all on the same line?
            ],
        ]);

        $this->crud->addButtonFromView('line', 'export_test_records', 'export_test_records', 'beginning');

    }

    public function exportTestRecords($course_id, $test_id){
        $export = new TestRecordsExport($test_id);
        return Excel::download($export, 'لیست نمرات.xlsx');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        // return print("<pre>" . print_r($request, true) . "</pre>");
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
