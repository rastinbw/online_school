<?php
namespace App\Exports;

use App\Includes\Constant;
use App\Models\Course;
use App\Models\Plan;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromArray;

class StudentsExport implements FromArray
{
    private $plan_id;
    private $course_id;
    private $student_id;

    public function __construct($plan_id = null, $course_id = null, $student_id = null) {
        $this->plan_id = $plan_id;
        $this->course_id = $course_id;
        $this->student_id = $student_id;
    }

    public function array(): array
    {
        $headers = [
            'نام',
            'نام خانوادگی',
            'کد ملی',
            'تاریخ تولد',
            'رشته',
            'پایه',
            'جنسیت',
            'کد اولیا',
            'شماره تلفن همراه',
            'شماره تلفن ثابت',
            'شماره تماس ناظر تحصیلی',
            'آدرس منزل',
            'ایمیل',
        ];

        $data = [$headers];

        if($this->plan_id != null)
            $students = Plan::find($this->plan_id)->students()->get();
        elseif($this->course_id != null) {
            $student_ids = [];
            $students = [];
            $plans = Course::find($this->course_id)->plans()->get();
            foreach ($plans as $plan){
                foreach ($plan->students as $student){
                    if(!in_array($student->id, $student_ids)){
                        array_push($student_ids, $student->id);
                        array_push($students, $student);
                    }
                }
            }

        } elseif($this->student_id != null) {
            $student = Student::find($this->student_id);
            $students = Student::where('referrer_code', $student->student_refer_code)->get();
        } else
            $students = Student::all();

        // generating students
        foreach ($students as $student) {
            $grade = $student->grade()->first();
            $field = $student->field()->first();

            $item = [
                $student->first_name,
                $student->last_name,
                $student->national_code,
                $student->birth_year . '/' . $student->birth_month . '/' . $student->birth_day,
                ($grade != null) ? $grade->title : "-",
                ($field != null) ? $field->title : "-",
                $student->gender == Constant::$GENDER_MALE ? Constant::$GENDER_MALE_TITLE : Constant::$GENDER_FEMALE_TITLE,
                $student->parent_code,
                $student->phone_number,
                $student->home_number,
                $student->parent_phone_number,
                $student->address,
                $student->email,
            ];

            array_push($data, $item);
        }

        return $data;
    }
}
