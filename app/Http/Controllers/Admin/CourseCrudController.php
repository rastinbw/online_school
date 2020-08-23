<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StudentsExport;
use App\Http\Controllers\API\SkyRoomController;
use App\Includes\Constant;
use App\Includes\HttpError;
use App\Includes\Skyroom;
use App\Models\Course;
use App\Models\Field;
use App\Models\Grade;
use App\Models\Plan;
use App\Models\SkyRoomError;
use App\Models\Tag;
use App\Models\Teacher;
use App\Models\Test;
use App\Models\TestAccess;
use App\Models\TestRecord;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CourseRequest as StoreRequest;
use App\Http\Requests\CourseRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
use Hekmatinasser\Verta\Verta;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class CourseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CourseCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Course');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/course');
        $this->crud->setEntityNameStrings('کلاس', 'کلاس ها');

        $time = Verta::now(); //1396-02-02 15:32:08

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->addFields([
            [
                'label' => 'وضعیت کلاس',
                'name' => 'status',
                'type' => 'radio',
                'inline' => true,
                'options' => [
                    0 => 'غیرفعال',
                    1 => 'فعال',
                ],
                'default' => 0,
            ],
            [
                'label' => 'اتمام کلاس',
                'name' => 'course_done',
                'type' => 'radio',
                'inline' => true,
                'options' => [
                    0 => 'خیر',
                    1 => 'بله',
                ],
                'default' => 0,
            ],
        ], 'update');

        $this->crud->addFields([
            [
                'name' => 'title',
                'label' => '* عنوان',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'display_title',
                'label' => 'عنوان نمایشی',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            // LAUNCH DATE
            [
                'name' => 'launch_date_day',
                'label' => 'روز شروع کلاس',
                'type' => 'number',
                'default' => $time->day,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'col-md-4',
                ],
            ],
            [
                'name' => 'launch_date_month',
                'label' => 'ماه شروع کلاس',
                'type' => 'number',
                'default' => $time->month,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'col-md-4',
                ],
            ],
            [
                'name' => 'launch_date_year',
                'label' => 'سال شروع کلاس',
                'type' => 'number',
                'default' => $time->year,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'col-md-4',
                ],
            ],

            // START AND FINISH TIME
            [
                'name' => 'finish_min',
                'label' => '* دقیقه پایان',
                'default' => $time->minute,
                'type' => 'number',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                    'dir' => 'rtl',
                    'class' => 'col-md-3',
                ],
            ],
            [
                'name' => 'finish_hour',
                'label' => 'ساعت پایان',
                'type' => 'number',
                'default' => $time->hour + 1,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                    'dir' => 'rtl',
                    'class' => 'col-md-3',
                ],
            ],
            [
                'name' => 'start_min',
                'label' => 'دقیقه شروع',
                'type' => 'number',
                'default' => $time->minute,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                    'dir' => 'rtl',
                    'class' => 'col-md-3',
                ],
            ],
            [
                'name' => 'start_hour',
                'label' => 'ساعت شروع',
                'default' => $time->hour,
                'type' => 'number',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                    'dir' => 'rtl',
                    'class' => 'col-md-3',
                ],
            ],
            [
                'name' => 'online_day',
                'label' => "روز برگزاری",
                'type' => 'select2_from_array',
                'options' => [
                    Constant::$SATURDAY => Constant::$SATURDAY,
                    Constant::$SUNDAY => Constant::$SUNDAY,
                    Constant::$MONDAY => Constant::$MONDAY,
                    Constant::$TUESDAY => Constant::$TUESDAY,
                    Constant::$WEDNESDAY => Constant::$WEDNESDAY,
                    Constant::$THURSDAY => Constant::$THURSDAY,
                    Constant::$FRIDAY => Constant::$FRIDAY,
                ],
                'allows_null' => false,
                'default' => 'شنبه',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                    'dir' => 'rtl',
                ],
            ],
            [ // Select
                'label' => "تگ ( میتوانید در بخش تگ ها اقدام به اضافه کردن تگ جدید نمایید )",
                'type' => 'select2',
                'name' => 'tag_id', // the db column for the foreign key
                'entity' => 'tag', // the method that defines the relationship in your Model
                'attribute' => 'title', // foreign key attribute that is shown to user
                'model' => "App\Models\Tag", // foreign key model
                'allows_null' => false,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [ // Select
                'label' => "استاد ( میتوانید در بخش اساتید اقدام به اضافه کردن استاد جدید نمایید )",
                'type' => 'select2',
                'name' => 'teacher_id', // the db column for the foreign key
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => 'list_title', // foreign key attribute that is shown to user
                'model' => "App\Models\Teacher", // foreign key model
                'allows_null' => false,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'label' => 'کلاس رایگان است؟',
                'name' => 'is_free',
                'type' => 'radio',
                'inline' => true,
                'options' => [
                    0 => 'خیر',
                    1 => 'بله',
                ],
                'default' => 0,
            ],
            [
                'label' => 'اپراتور باید اول از همه وارد شود؟',
                'name' => 'op_login_first',
                'type' => 'radio',
                'inline' => true,
                'options' => [
                    0 => 'خیر',
                    1 => 'بله',
                ],
                'default' => 0,
            ],
            [
                'label' => 'امکان ورود مهمان',
                'name' => 'guest_login',
                'type' => 'toggle',
                'inline' => true,
                'options' => [
                    0 => 'ندارد',
                    1 => 'دارد',
                ],
                'hide_when' => [
                    0 => ['guest_limit']
                ],
                'default' => 0,
            ],
            [
                'name' => 'guest_limit',
                'label' => 'سقف تعداد مهمان ها',
                'type' => 'number',
                'default' => 100,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'description',
                'label' => 'توضیحات',
                'type' => 'textarea',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
        ], 'update/create/both');

        $this->crud->addColumns([
            [
                'name' => 'title',
                'label' => 'عنوان',
            ],
            [
                'name' => 'display_title',
                'label' => 'عنوان نمایشی',
            ],
            [
                'name' => 'status',
                'label' => 'وضعیت',
                'type' => 'select_from_array',
                'options' => [
                    0 => 'غیر فعال',
                    1  => 'فعال',
                ],
            ],
            [
                // run a function on the CRUD model and show its return value
                'name' => "is_online",
                'label' => "وضعیت آنلاینی", // Table column heading
                'type' => "model_function",
                'function_name' => 'getOnline', // the method in your Model
                'limit' => 1000
            ],
        ]);

        $this->crud->addFilter([ // select2 filter
            'name' => 'teacher_id',
            'type' => 'select2',
            'label' => 'استاد',
        ], function () {
            return Teacher::all()->keyBy('id')->pluck('list_title', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'teacher_id', $value);
        });

        $this->crud->addFilter([ // select2 filter
            'name' => 'tag_id',
            'type' => 'select2',
            'label' => 'تگ',
        ], function () {
            return Tag::all()->keyBy('id')->pluck('title', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'tag_id', $value);
        });

        $this->crud->addFilter([ // dropdown filter
            'name' => 'online_day',
            'type' => 'dropdown',
            'label'=> 'روز برگزاری',
        ],
        ['شنبه'=>'شنبه', 'یکشنبه'=>'یکشنبه', 'دوشنبه'=>'دوشنبه', 'سه شنبه'=>'سه شنبه', 'چهارشنبه'=>'چهارشنبه', 'پنجشنبه'=>'پنجشنبه', 'جمعه'=>'جمعه'],
        function($value) { // if the filter is active
            $this->crud->addClause('where', 'online_day', $value);
        });

        $this->crud->addFilter([ // dropdown filter
            'name' => 'is_free',
            'type' => 'dropdown',
            'label'=> 'رایگان',
        ], ['0'=>'خیر', '1'=>'بله'], function($value) { // if the filter is active
            $this->crud->addClause('where', 'is_free', $value);
        });

        $this->crud->addButtonFromView('line', 'export_course_students', 'export_course_students', 'beginning');
        $this->crud->addButtonFromView('line', 'course_note', 'course_note', 'beginning');
        $this->crud->addButtonFromView('line', 'course_test', 'course_test', 'beginning');
        $this->crud->addButtonFromView('line', 'course_session', 'course_session', 'beginning');

        $this->crud->enableDetailsRow();
        $this->crud->allowAccess('details_row');
    }


    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->getSaveAction();
        $this->data['fields'] = $this->crud->getUpdateFields($id);
        $this->data['title'] = trans('backpack::crud.edit') . ' ' . $this->crud->entity_name;
        $this->data['id'] = $id;

       $this->data['extra'] = json_encode(['old_teacher_id' => Course::find($id)->teacher_id]);

        return view($this->crud->getEditView(), $this->data);
    }


    public function showDetailsRow($id)
    {
        $this->crud->hasAccessOrFail('details_row');

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;

        $course = $this->crud->getEntry($id);
        $this->data['course'] = $this->crud->getEntry($id);

        $this->data['is_free'] = $course->is_free ? "بله" : "خیر";
        $this->data['guest_login'] = $course->guest_login ? "بله" : "خیر";
        $this->data['op_login_first'] = $course->op_login_first ? "بله" : "خیر";

        if ($course->guest_limit)
            $this->data['guest_limit'] = $course->guest_limit;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getDetailsRowView(), $this->data);
    }

    public function exportCourseStudents($course_id){
        $export = new StudentsExport(null, $course_id);
        return Excel::download($export, 'لیست دانش آموزان.xlsx');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);

        $course = $this->data['entry'];

        //create room
        SkyRoomController::createRoom($course);

        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $errors = [];
        $plans = Course::find($request->input('id'))->plans;
        foreach ($plans as $plan){
            if($request->input('is_free') && !$plan->is_free)
                array_push($errors, ".این کلاس در طرح غیر رایگان {$plan->title} قرار دارد");
            else if(!$request->input('is_free') && $plan->is_free)
                array_push($errors, ".این کلاس در طرح رایگان {$plan->title} قرار دارد");
        }

        if (sizeof($errors) > 0)
            return back()->withErrors(['custom_fail' => true, 'errors' => $errors]);

        $redirect_location = parent::updateCrud($request);
        $course = $this->data['entry'];

        //update room
        SkyRoomController::updateRoom($course, json_decode($request->input('extra'))->old_teacher_id);

        return $redirect_location;
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        $api = new Skyroom(env('SKY_ROOM_API_URL'));
        $api->call('deleteRoom', array("room_id" => Course::find($id)->room_id));

        $course = Course::find($id);
        foreach ($course->tests as $test){
            TestRecord::where('test_id', $test->id)->delete();
            TestAccess::where('test_id', $test->id)->delete();
        }
        $course->tests->each->delete();

        $course->sessions->each->delete();
        $course->courseAccesses->each->delete();

        return $this->crud->delete($id);
    }

}
