<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InstallmentsExport;
use App\Exports\StudentsExport;
use App\Exports\TestRecordsExport;
use App\Includes\Constant;
use App\Includes\Helper;
use App\Includes\Skyroom;
use App\Models\CourseAccess;
use App\Models\Field;
use App\Models\Grade;
use App\Models\Installment;
use App\Models\Student;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\StudentRequest as StoreRequest;
use App\Http\Requests\StudentRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class StudentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class StudentCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Student');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/student');
        $this->crud->setEntityNameStrings('دانش آموز', 'دانش آموزان');

//        $plan = Plan::find(30);
//        foreach ($plan->courses as $course){
//            CourseAccess::where([
//                ['student_id', 437],
//                ['course_id', $course->id]
//            ])->delete();
//
//            CourseAccess::where([
//                ['student_id', 606],
//                ['course_id', $course->id]
//            ])->delete();
//        }
//
//        $plan->students()->detach(437);
//        $plan->students()->detach(606);

          //$students = Student::where('grade_id', 19)->get();
//        dd($students);

//        $students = Student::where('national_code', '2580974555')->get();
//
//        dd(sizeof($students));
//        foreach ($students as $student){
//            $url = 'https://api.kavenegar.com/v1/' .
//                env('SMS_API_KEY') .
//                '/verify/lookup.json?receptor=' .
//                $student->phone_number .
//                '&template=' .
//                'yazdahomiSummerCoursesLink' .
//                '&token=18&token2=ریاضی&token3=https://www.skyroom.online/ch/amirdaneshmand08/course111';
//
//            $http = new HttpRequest($url);
//            $http->get();
//        }

//        foreach (Plan::all() as $plan){
//            $student = Student::where('national_code', '2580974555')->first();
//            $pc = new PlansController();
//            $pc->registerInPlan($student, $plan);
//        }

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->addFields([
            [
                'name' => 'region',
                'label' => 'منطقه',
                'type' => 'select2_from_array',
                'options' => [
                    Constant::$REGION_ONE => Constant::$REGION_ONE,
                    Constant::$REGION_TWO => Constant::$REGION_TWO,
                    Constant::$REGION_THREE => Constant::$REGION_THREE,
                ],
                'allows_null' => false,
                'default' => 1,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
        ],'update');

        $this->crud->addColumns([
            [
                'name' => 'first_name',
                'label' => 'نام',
            ],
            [
                'name' => 'last_name',
                'label' => 'نام خانوادگی',
            ],
            [
                'name' => 'national_code',
                'label' => 'کد ملی',
            ],
            [
                'name' => 'phone_number',
                'label' => 'شماره تماس',
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
        ]);

        $this->crud->enableDetailsRow();
        $this->crud->addClause('where', 'verified', '=', 1);

        $this->crud->denyAccess([ 'create', 'reorder']);
        $this->crud->allowAccess('details_row');

        $this->crud->addFilter([ // add a "simple" filter called Draft
            'type' => 'dropdown',
            'name' => 'gender',
            'label' => 'جنسیت',
        ], [
            Constant::$GENDER_FEMALE => 'دختر',
            Constant::$GENDER_MALE => 'پسر',
        ], function ($value) {
            $this->crud->addClause('where', 'gender', $value);
        }
        );

        $this->crud->addFilter([ // select2 filter
            'name' => 'field_id',
            'type' => 'select2',
            'label' => 'رشته',
        ], function () {
            return Field::all()->keyBy('id')->pluck('title', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'field_id', $value);
        });

        $this->crud->addFilter([ // select2 filter
            'name' => 'grade_id',
            'type' => 'select2',
            'label' => 'پایه',
        ], function () {
            return Grade::all()->keyBy('id')->pluck('title', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'grade_id', $value);
        });

        $this->crud->addButtonFromView('top', 'export_students', 'export_students', 'beginning');
        $this->crud->addButtonFromView('line', 'export_student_referees', 'export_student_referees', 'beginning');
        $this->crud->addButtonFromView('line', 'export_student_test_records', 'export_student_test_records', 'beginning');
        $this->crud->addButtonFromView('line', 'export_student_installments', 'export_student_installments', 'beginning');
        $this->crud->addButtonFromView('line', 'download_national_card', 'download_national_card', 'beginning');
        $this->crud->addButtonFromView('line', 'download_enrollment_certificate', 'download_enrollment_certificate', 'beginning');
        $this->crud->addButtonFromView('line', 'student_test_access', 'student_test_access', 'beginning');
        $this->crud->addButtonFromView('line', 'student_course_access', 'student_course_access', 'beginning');

    }

    public function download_national_card($id){
        $student = $this->crud->getEntry($id);

        if($student->national_card_image == null){
            $error = "." . $student->first_name . " " . $student->last_name
                     . " کارت ملی خود را آپلود نکرده است";

            return back()->withErrors(['custom_fail' => true, 'errors' => [$error]]);
        }

        return Helper::download(
            $student->national_card_image,
            'کارت ملی ' . $student->first_name . " " . $student->last_name,
            '.png'
        );
    }

    public function download_enrollment_certificate($id){
        $student = $this->crud->getEntry($id);

        if($student->enrollment_certificate_image == null){
            $error = "." . $student->first_name . " " . $student->last_name
                . " گواهی اشتغال به تحصیل خود را آپلود نکرده است";

            return back()->withErrors(['custom_fail' => true, 'errors' => [$error]]);
        }

        return Helper::download(
            $student->enrollment_certificate_image,
            'گواهی اشتغال به تحصیل ' . $student->first_name . " " . $student->last_name,
            '.png'
        );
    }

    public function export_students()
    {
        $export = new StudentsExport();
        return Excel::download($export, 'لیست دانش آموزان.xlsx');
    }

    public function exportStudentTestRecords($id){
        $export = new TestRecordsExport(null, $id);
        return Excel::download($export, 'لیست نمرات.xlsx');
    }

    public function exportStudentReferees($id){
        $export = new StudentsExport(null, null,$id);
        return Excel::download($export, 'للیست معرفی شدگان.xlsx');
    }

    public function exportStudentInstallments($id){
        $export = new InstallmentsExport($id);
        return Excel::download($export, 'لیست اقساط.xlsx');
    }

    public function showDetailsRow($id)
    {
        $this->crud->hasAccessOrFail('details_row');

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;

        $student = $this->crud->getEntry($id);
        $this->data['student'] = $this->crud->getEntry($id);

        $field = $student->field()->first();
        $grade = $student->grade()->first();

        $this->data['field'] = ($field != null) ? $field->title : '-';
        $this->data['grade'] = ($grade != null) ? $grade->title : '-';
        $this->data['gender'] = ($student->gender === Constant::$GENDER_MALE) ? Constant::$GENDER_MALE_TITLE : Constant::$GENDER_FEMALE_TITLE;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getDetailsRowView(), $this->data);
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
        $old_region = Student::find($request->input('id'))->region;

        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        $student = $this->data['entry'];
        if ($old_region != $student->region){
            foreach ($student->plans as $plan){
                $price_difference =
                    $this->getPlanPriceByRegion($plan, $student->region) -
                    $this->getPlanPriceByRegion($plan, $old_region);

                if ($price_difference > 0){
                    foreach ($plan->courses as $course){
                        $access = CourseAccess::where([
                            ['student_id', $student->id],
                            ['course_id', $course->id],
                        ])->first();

                        $access->has_access = 0;
                        $access->access_deny_reason = Constant::$ACCESS_DENY_REASON_REMAINING_DEBT_NOT_PAID;
                        $access->save();

                        AccessController::changeStudentCourseTestAccesses($course->id, $student->id, 0);
                    }

                    // create installment for fee
                    $installment = new Installment();
                    $installment->student_id = $student->id;
                    $installment->plan_id = $plan->id;
                    $installment->amount = $price_difference;
                    $installment->is_region_fee_installment = 1;

                    $date = Verta::now();
                    $installment->date_year = $date->year;
                    $installment->date_month = $date->month;
                    $installment->date_day = $date->day;

                    $gDate = Verta::getGregorian($date->year,$date->month,$date->day);
                    $installment->date = new Carbon("{$gDate[0]}-{$gDate[1]}-{$gDate[2]}");

                    $installment->save();
                }

            }
        }
        return $redirect_location;
    }

    private function getPlanPriceByRegion($plan, $region){
        switch ($region){
            case Constant::$REGION_ONE:
                return $plan['region_one_price'];
            case Constant::$REGION_TWO:
                return $plan['region_two_price'];
            case Constant::$REGION_THREE:
                return $plan['region_three_price'];
        }
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        $api = new Skyroom(env('SKY_ROOM_API_URL'));
        $api->call('deleteUser', array("user_id" => Student::find($id)->sky_room_id));

        return $this->crud->delete($id);
    }
}
