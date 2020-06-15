<?php


namespace App\Http\Controllers\API;
use App\Includes\Constant;
use App\Includes\Helper;
use App\Models\CourseAccess;
use App\Models\TakingTest;
use App\Models\Test;
use App\Models\TestAccess;
use App\Models\TestRecord;
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
        if (!$student_id)
            return false;

        $status = $this->getStudentTestStatus($student_id, $test->id);
        if ($status == Constant::$TEST_IS_TAKING)
            return true;
        else if ($status == Constant::$TEST_TAKEN)
            return false;

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
            'is_taking' => ($this->getStudentTestStatus($student_id, $test->id) == Constant::$TEST_IS_TAKING) ? 1 : 0,
            'has_taken' => ($this->getStudentTestStatus($student_id, $test->id) == Constant::$TEST_TAKEN) ? 1 : 0,
            'reached_start_date_time' => $this->checkIfTestIsRemaining($test) ? 0 : 1,
            'passed_finish_date_time' => $this->checkIfTestIsTaken($test) ? 1 : 0,
            'test_access' => $this->getStudentTestAccess($test, $student_id) ? 1 : 0
        ];

        if ($set_course_access){
            $access = 0;
            if ($student_id){
                $access = CourseAccess::where([
                    ['student_id', $student_id],
                    ['course_id', $test->course_id]
                ])->first()->has_access;
            }
            $object['test_course_access'] = $access;
        }

        return $object;
    }

    public function getStudentTestStatus($student_id, $test_id){
        $taking = TakingTest::where([
            ['student_id', $student_id],
            ['test_id', $test_id],
        ])->first();

        if (!$taking)
            return Constant::$TEST_NOT_TAKEN;

        $test = Test::find($test_id);
        $duration = $this->getTestDuration($test);
        $finish_date = new Carbon($taking->enter_date);
        $finish_date->addMinutes($duration);

        if ($finish_date <= Carbon::now())
            return Constant::$TEST_TAKEN;
        else
            return Constant::$TEST_IS_TAKING;
    }

    public function getTakingTestDuration($taking){
        $test = Test::find($taking->test_id);
        $duration = $this->getTestDuration($test);
        $finish_date = new Carbon($taking->enter_date);
        $finish_date->addMinutes($duration);

        if ($finish_date <= Carbon::now())
            return 0;
        else
            return $finish_date->diffInMinutes(Carbon::now());
    }

    public function enterTest(Request $req){
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $test = Test::find($req->input('test_id'));

        $taking = TakingTest::where([
            ['student_id', $student->id],
            ['test_id', $test->id],
        ])->first();

        if ($taking){
            $duration = $this->getTakingTestDuration($taking);
        }else{
            $duration = $this->getTestDuration($test);
            $taking = new TakingTest();
            $taking->student_id = $student->id;
            $taking->test_id = $test->id;
            $taking->enter_date = Carbon::now();
            $taking->save();
        }

        $record = TestRecord::where([
            ['student_id', $student->id],
            ['test_id', $test->id],
        ])->first();

        $data = [
            'duration' => $duration,
            'questions' => $test->questions_file,
            'questions_count' => sizeof(json_decode($test->options)),
            'answers' => ($record) ? $record->answers : null
        ];

        return $this->sendResponse(
            Constant::$SUCCESS,
            $data
        );
    }

    public function saveTestRecord(Request $req){
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $test = Test::find($req->input('test_id'));

        $record = TestRecord::where([
            ['student_id', $student->id],
            ['test_id', $test->id],
        ])->first();
        $record->answers = $req->input('answers');
        $record->save();

        return $this->sendResponse(
            Constant::$SUCCESS,
            null
        );
    }
}
