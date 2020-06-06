<?php


namespace App\Http\Controllers\Admin;


use App\Includes\Constant;
use App\Models\Session;
use App\Models\Test;
use Carbon\Carbon;

class AdminController
{
    /**
     * @param Carbon $start_date
     * @param Carbon $finish_date
     * @param $check_tests
     * @param $check_sessions
     * @param $current_test_id
     * @param null $current_session_id
     * @return array
     */
    public static function checkOverlappedTestsAndSessions(Carbon $start_date, Carbon $finish_date, $check_tests, $check_sessions, $current_test_id = null, $current_session_id = null): array
    {
        $errors = [];
        if($check_tests){
            $overlapped_tests = Test::where(function ($query) use ($start_date, $current_test_id) {
                $query->where('exam_holding_type', '=', Constant::$SPECIAL_DATE_AND_TIME);
                $query->where('start_date', '<=', $start_date);
                $query->where('finish_date', '>=', $start_date);
            })->orWhere(function ($query) use ($finish_date, $current_test_id) {
                $query->where('exam_holding_type', '=', Constant::$SPECIAL_DATE_AND_TIME);
                $query->where('start_date', '<=', $finish_date);
                $query->where('finish_date', '>=', $finish_date);
            })->orWhere(function ($query) use ($start_date, $finish_date) {
                $query->where('exam_holding_type', '=', Constant::$SPECIAL_DATE_AND_TIME);
                $query->where('start_date', '<', $finish_date);
                $query->where('finish_date', '>', $start_date);
            })->get();

            foreach ($overlapped_tests as $test) {
                if ($test->id != $current_test_id){
                    $date = "{$test->exam_date_start_year}/{$test->exam_date_start_month}/{$test->exam_date_start_day}"
                        . " {$test->exam_date_start_hour}:{$test->exam_date_start_min} تا {$test->exam_date_finish_hour}:{$test->exam_date_finish_min}";

                    $error = "کلاس: {$test->course->title} - آزمون: {$test->title} - تاریخ: {$date}";
                    array_push($errors, $error);
                }
            }
        }

        if($check_sessions){
            $overlapped_sessions = Session::where(function ($query) use ($start_date) {
                $query->where('start_date', '<=', $start_date);
                $query->where('finish_date', '>=', $start_date);
            })->orWhere(function ($query) use ($finish_date) {
                $query->where('start_date', '<=', $finish_date);
                $query->where('finish_date', '>=', $finish_date);
            })->orWhere(function ($query) use ($start_date, $finish_date) {
                $query->where('start_date', '<', $finish_date);
                $query->where('finish_date', '>', $start_date);
            })->get();

            foreach ($overlapped_sessions as $session) {
                if ($session->id != $current_session_id) {
                    $date = "{$session->date_year}/{$session->date_month}/{$session->date_day}"
                        . " {$session->start_hour}:{$session->start_min} تا {$session->finish_hour}:{$session->finish_min}";

                    $error = "کلاس: {$session->course->title} - جلسه: {$session->title} - تاریخ: {$date}";
                    array_push($errors, $error);
                }
            }
        }

        return $errors;
    }
}
