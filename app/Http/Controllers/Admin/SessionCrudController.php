<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\API\SkyRoomController;
use App\Includes\HttpError;
use App\Includes\Skyroom;
use App\Models\Course;
use App\Models\Session;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\SessionRequest as StoreRequest;
use App\Http\Requests\SessionRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Redirect;

/**
 * Class SessionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class SessionCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Session');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/session');

        $this->crud->setEntityNameStrings('جلسه', 'جلسات کلاس ');

        $time = Verta::now(); //1396-02-02 15:32:08
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->addColumns([
            [
                'name' => 'title',
                'label' => 'عنوان',
            ],
            [
                // run a function on the CRUD model and show its return value
                'name' => "date",
                'label' => "تاریخ جلسه", // Table column heading
                'type' => "model_function",
                'function_name' => 'getDate', // the method in your Model
            ],
            [
                // run a function on the CRUD model and show its return value
                'name' => "start_time",
                'label' => "زمان شروع", // Table column heading
                'type' => "model_function",
                'function_name' => 'getStartTime', // the method in your Model
            ],
            [
                // run a function on the CRUD model and show its return value
                'name' => "finish_time",
                'label' => "زمان پایان", // Table column heading
                'type' => "model_function",
                'function_name' => 'getFinishTime', // the method in your Model
            ],
            [
                'name' => 'is_free',
                'label' => 'رایگان',
                'type' => 'select_from_array',
                'options' => [
                    0 => 'خیر',
                    1  => 'بله',
                ],
            ],
            [
                // run a function on the CRUD model and show its return value
                'name' => "is_online",
                'label' => "وضعیت آنلاینی", // Table column heading
                'type' => "model_function",
                'function_name' => 'changeOnline', // the method in your Model
                'limit' => 1000
            ],
            [
                // run a function on the CRUD model and show its return value
                'name' => "held",
                'label' => "وضعیت برگزاری", // Table column heading
                'type' => "model_function",
                'function_name' => 'changeHeld', // the method in your Model
                'limit' => 1000
            ],
        ]);

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
            // DATE
            [
                'name' => 'date_day',
                'label' => 'روز',
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
                'name' => 'date_month',
                'label' => 'ماه',
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
                'name' => 'date_year',
                'label' => 'سال',
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
                'label' => 'دقیقه پایان',
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
                'label' => 'جلسه رایگان است؟',
                'name' => 'is_free',
                'type' => 'radio',
                'inline' => true,
                'options' => [
                    0 => 'خیر',
                    1 => 'بله',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                ],
                'default' => 0,
            ],
            [
                'name' => 'video_link',
                'label' => 'لینک ویدیو',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'video_download_link',
                'label' => 'لینک دانلود ویدیو',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                    'dir' => 'rtl',
                ],
            ],
            [ // Upload
                'name' => 'notes',
                'label' => 'فایل جزوه جلسه',
                'type' => 'upload',
                'upload' => true,
                'disk' => 'public'
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
            [
                'name' => 'check_for_tests_overlapping',
                'label' => 'بررسی همزمانی با آزمون',
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

        $this->crud->addButtonFromView('line', 'attend_course', 'attend_course', 'beginning');

    }

    public function changeOnline($course_id, $session_id){
        $course = Course::find($course_id);
        $session = Session::find($session_id);

        /// check for other courses online status
        if(!$session->is_online){
            $other_course = Course::where('is_online', 1)->first();
            if($other_course && $other_course->id != $course->id){
                return back()->withErrors([
                    'custom_fail' => true,
                    'errors' => [".کلاس {$other_course->title} ({$other_course->teacher->name}) درحال حاضر آنلاین میباشد"],
                ]);
            }
        }

        $session->is_online = !$session->is_online;
        $course->is_online = !$session->is_online;
        $session->save();
        $course->save();

        if($session->is_online){
            // set all other sessions offline
            $sessions = $course->sessions;
            foreach ($sessions as $s){
                if($s->id != $session_id){
                    $s->is_online = 0;
                    $s->save();
                }
            }

            $session->held = 0;
            $session->save();
        }

        SkyRoomController::updateRoomTitle($course, $session);

        return back();
    }

    public function changeHeld($course_id, $session_id){
        $session = Session::find($session_id);
        $session->held = !$session->held;
        $session->is_online = 0;
        $session->save();

        return back();
    }

    public function attendCourse($course_id, $session_id){
        $course = Course::find($course_id);
        if($course->status)
            return Redirect::to($course->room_url);
        else
            return back()->withErrors([
                'custom_fail' => true,
                'errors' => ['.کلاس فعال نمیباشد'],
            ]);

    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
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
