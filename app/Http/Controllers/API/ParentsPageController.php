<?php


namespace App\Http\Controllers\API;

use App\Includes\Constant;
use App\Models\Course;
use App\Models\Plan;
use App\Models\Student;
use App\Models\Test;
use App\Models\TestRecord;
use Carbon\Carbon;

class ParentsPageController extends BaseController
{
    public function getParentPage($parent_code){
        $student = Student::where('parent_code', $parent_code)->first();
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_PARENT_CODE, null);

        $data = [
            'info' => $this->getStudentInfo($student),
            'financial' => $this->getStudentFinancial($student),
            'tests' => $this->getStudentTests($student)
        ];

        return $this->sendResponse(
            Constant::$SUCCESS,
            $data
        );
    }

    private function getStudentInfo($student)
    {
        $data = [];
        $data['first_name'] = $student->first_name;
        $data['last_name'] = $student->last_name;
        $data['grade'] = $student->grade->title;
        $data['field'] = $student->field->title;
        $data['national_code'] = $student->national_code;
        $data['phone_number'] = $student->phone_number;

        return $data;
    }

    private function getStudentFinancial($student){
        foreach ($student->transactions as $transaction){
            $plan = Plan::find($transaction->plan_id);
            $region_price = $plan->region_price($student->region);
            $data = [];
            if ($transaction->success){
                array_push($data, [
                    'title' => $transaction->title,
                    'paid_price' => $transaction->paid_amount . "",
                    'plan_price' => $region_price,
                    'type' => $transaction->transaction_payment_type,
                    'date_year' => $transaction->date_year,
                    'date_month' => $transaction->date_month,
                    'date_day' => $transaction->date_day
                ]);
            }

            return $data;
        }
    }

    private function getStudentTests($student){
        $records = TestRecord::where('student_id', $student->id)->get();
        $data = [];

        foreach ($records as $record){
            $test = Test::where('id', $record->test_id)->first();
            $course = Course::where('id', $test['course_id'])->first();
            if ($record->workbook){
                if ($test->result_access_type == Constant::$SPECIAL_DATE_AND_TIME){
                    if (Carbon::now() <= Carbon::create(
                            $test->result_access_date_year,
                            $test->result_access_date_month,
                            $test->result_access_date_day)
                    ) continue;
                }

                array_push($data,[
                    'test_title' => $test->title,
                    'course_title' => $course->title,
                    'date_year' => $test->exam_date_start_year,
                    'date_month' => $test->exam_date_start_month,
                    'date_day' => $test->exam_date_start_day,
                    'date_hour' => $test->exam_date_start_hour,
                    'date_min' => $test->exam_date_start_min,
                    'workbook' => $record->workbook
                ]);
            }
        }

        return $data;
    }
}
