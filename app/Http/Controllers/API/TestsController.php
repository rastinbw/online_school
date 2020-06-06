<?php


namespace App\Http\Controllers\API;
use App\Includes\Constant;
use App\Includes\Helper;
use App\Models\CourseAccess;
use App\Models\TestAccess;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;

class TestsController extends BaseController
{
    public function getStudentTestList(Request $req){
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        // get inputs
        $course_id = $req->input('course_id');
        $date_from = $req->input('date_from') ? explode('-', $req->input('date_from')) : null;
        $date_to = $req->input('date_to') ? explode('-', $req->input('date_to')) : null;

        $filtered_tests = $this->filterTests(
            $this->getStudentAllTests($student),
            $course_id, $date_from, $date_to
        );

        return $this->sendResponse(
            Constant::$SUCCESS,
            $this->categorizeTests($filtered_tests, $student->id)
        );
    }

    public function getStudentAllTests($student){
        $tests = [];
        foreach ($student->plans as $plan){
            foreach ($plan->courses as $course){
                $tests = array_merge($tests, $course->tests->toArray());
            }
        }

        return Helper::removeSimilarObjects($tests);
    }

    private function filterTests($tests, $course_id = null, $date_from = null, $date_to = null){
        $result = [];
        $condition = true;

        $date_from = ($date_from) ? Verta::getGregorian(
            $date_from[0],
            $date_from[1],
            $date_from[2]
        ) : null;

        $date_to = ($date_to) ? Verta::getGregorian(
            $date_to[0],
            $date_to[1],
            $date_to[2]
        ) : null;

        if ($course_id || $date_from || $date_to) {
            foreach ($tests as $test) {
                if ($course_id) $condition = $condition && $test['course_id'] == $course_id;
                if ($date_from) $condition = $condition && $test['start_date'] >= new Carbon("{$date_from[0]}-{$date_from[1]}-{$date_from[2]}");
                if ($date_to) $condition = $condition && $test['start_date'] <= new Carbon("{$date_to[0]}-{$date_to[1]}-{$date_to[2]}");
                if ($condition)
                    array_push($result, $test);
                $condition = true;
            }
        }else $result = $tests;

        return $result;
    }

    private function categorizeTests($tests, $student_id)
    {
        $categorized_tests = [
            Constant::$RUNNING_TESTS => [],
            Constant::$REMAINING_TESTS => [],
            Constant::$FREE_TESTS => [],
            Constant::$TAKEN_TESTS => [],
        ];

        foreach ($tests as $test){
            $test = (object)$test;
            if ($test->exam_holding_type == Constant::$SPECIAL_DATE_AND_TIME){
                if($this->checkIfTestIsRunning($test))
                    array_push(
                        $categorized_tests[Constant::$RUNNING_TESTS],
                        $this->buildTestObject($test, $student_id)
                    );

                if($this->checkIfTestIsRemaining($test))
                    array_push(
                        $categorized_tests[Constant::$REMAINING_TESTS],
                        $this->buildTestObject($test, $student_id)
                    );

                if($this->checkIfTestIsTaken($test))
                    array_push(
                        $categorized_tests[Constant::$TAKEN_TESTS],
                        $this->buildTestObject($test, $student_id)
                    );
            } else{
                array_push(
                    $categorized_tests[Constant::$FREE_TESTS],
                    $this->buildTestObject($test, $student_id)
                );
            }
        }

        return $categorized_tests;
    }

    private function checkIfTestIsRemaining($test)
    {
        return $test->start_date > Carbon::now();
    }

    private function checkIfTestIsTaken($test){
        return $test->finish_date < Carbon::now();
    }

    private function checkIfTestIsRunning($test)
    {
        $test_start_date = new Carbon($test->start_date);
        $test_finish_date = new Carbon($test->finish_date);

        $reached = Carbon::now() >= $test_start_date;
        $not_passed = Carbon::now() <= $test_finish_date;

        return ($reached && $not_passed);
    }

    private function getTestDuration($test){
        if ($test->exam_holding_type == Constant::$FREE_TESTS)
            return $test->exam_duration;

        $test_start_date = new Carbon($test->start_date);
        $test_finish_date = new Carbon($test->finish_date);
        return $test_finish_date->diffInMinutes($test_start_date);
    }

    private function getStudentTestAccess($test, $student_id)
    {
        if ($test->start_date > Carbon::now())
            return false;

        $test_access = TestAccess::where([
            ['test_id', $test->id],
            ['student_id', $student_id]
        ])->first();

        if (!$test_access)
            return false;

        return $test_access->has_access;
    }

    public function buildTestObject($test, $student_id, $set_course_access = true)
    {
        $result_access_date = null;
        $result_access_time = null;
        $qa_access_date = null;
        $qa_access_time = null;

        if ($test->result_access_type == Constant::$SPECIAL_DATE_AND_TIME){
            $result_access_date = "{$test->result_access_date_year}/{$test->result_access_date_month}/{$test->result_access_date_day}";
            $result_access_time = "{$test->result_access_date_hour}/{$test->result_access_date_min}";
        }

        if ($test->qa_access_type == Constant::$SPECIAL_DATE_AND_TIME){
            $qa_access_date = "{$test->qa_access_date_year}/{$test->qa_access_date_month}/{$test->qa_access_date_day}";
            $qa_access_time = "{$test->qa_access_date_hour}/{$test->qa_access_date_min}";
        }

        $object = [
            'id' => $test->id,
            'course_id' => $test->course_id,
            'title' => $test->title,
            'type' => $test->exam_holding_type,
            'has_negative_score' => $test->has_negative_score,
            'start_date' => "{$test->exam_date_start_year}/{$test->exam_date_start_month}/{$test->exam_date_start_day}",
            'start_time' => "{$test->exam_date_start_hour}:{$test->exam_date_start_min}",
            'finish_date' => "{$test->exam_date_finish_year}/{$test->exam_date_finish_month}/{$test->exam_date_finish_day}",
            'finish_time' => "{$test->exam_date_finish_hour}:{$test->exam_date_finish_min}",
            'result_access_date' => $result_access_date,
            'result_access_time' => $result_access_time,
            'qa_access_date' => $qa_access_date,
            'qa_access_time' => $qa_access_time,
            'duration' => $this->getTestDuration($test),
            'reached_start_date_time' => $this->checkIfTestIsRemaining($test) ? 0 : 1,
            'passed_finish_date_time' => $this->checkIfTestIsTaken($test) ? 1 : 0,
            'test_access' => $this->getStudentTestAccess($test, $student_id) ? 1 : 0
        ];

        if ($set_course_access){
            $object['test_course_access'] = CourseAccess::where([
                ['student_id', $student_id],
                ['course_id', $test->course_id]
            ])->first()->has_access;
        }

        return $object;
    }

}
