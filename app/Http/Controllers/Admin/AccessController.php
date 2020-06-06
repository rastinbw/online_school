<?php


namespace App\Http\Controllers\Admin;


use App\Includes\Constant;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\Plan;
use App\Models\TestAccess;
use Carbon\Carbon;

class AccessController
{
    /**
     * @param $plan_id
     * @param $student_id
     * @param $access
     * @return mixed
     */
    public static function createStudentPlanCourseAccesses($plan_id, $student_id, $access)
    {
        $access_list = [];
        foreach (Plan::find($plan_id)->courses()->get() as $course) {
            $has_access = CourseAccess::where([
                ['student_id', $student_id],
                ['course_id', $course->id],
            ])->exists();

            if (!$has_access) {
                $a = new CourseAccess();
                $a->student_id = $student_id;
                $a->course_id = $course->id;
                $a->has_access = $access;
                $a->save();

                array_push($access_list, $a);
            }

        }
        return $access_list;
    }

    public static function createStudentPlanTestAccesses($plan_id, $student_id, $access)
    {
        foreach (Plan::find($plan_id)->courses()->get() as $course) {
            self::createStudentCourseTestAccesses($course->id, $student_id, $access);
        }
    }

    public static function createStudentCourseTestAccesses($course_id, $student_id, $access){
        foreach (Course::find($course_id)->tests as $test){
            $has_access = TestAccess::where([
                ['student_id', $student_id],
                ['test_id', $test->id],
            ])->exists();

            if(!$has_access){
                $a = new TestAccess();
                $a->student_id = $student_id;
                $a->test_id = $test->id;
                $a->has_access = $access;
                $a->save();
            }
        }
    }

    public static function createTestAccessesForCourseTest($course_id, $test_id){
        foreach (Course::find($course_id)->plans as $plan){
            foreach ($plan->students as $student){
                $has_access = TestAccess::where([
                    ['student_id', $student->id],
                    ['test_id', $test_id],
                ])->exists();

                if (!$has_access){
                    $course_access = CourseAccess::where([
                        ['student_id',  $student->id],
                        ['course_id', $course_id],
                    ])->first();

                    $access = new TestAccess();
                    $access->student_id = $student->id;
                    $access->test_id = $test_id;
                    $access->has_access = ($course_access) ? $course_access->has_access : 1;
                    $access->save();
                }
            }
        }
    }

    public static function changeStudentCourseTestAccesses($course_id, $student_id, $access){
        foreach (Course::find($course_id)->tests as $test){
            $test_access = TestAccess::where([
                ['student_id', $student_id],
                ['test_id', $test->id],
            ])->first();

            if ($test_access){
                if(($test->start_date <= Carbon::now() && $test->exam_holding_type == Constant::$SPECIAL_DATE_AND_TIME) ||
                    ($test->finish_date <= Carbon::now() && $test->exam_holding_type == Constant::$FREE_DATE_AND_TYPE))
                    if ($access) continue;
                $test_access->has_access = $access;
                $test_access->save();
            }
        }
    }
}
