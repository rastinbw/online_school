<?php

namespace App\Console;

use App\Http\Controllers\Admin\AccessController;
use App\Includes\Constant;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\Installment;
use App\Models\Plan;
use App\Models\Student;
use App\Models\Test;
use App\Models\TestAccess;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        '\App\Console\Commands\CheckStudentsInstallments',
        '\App\Console\Commands\DeleteNotVerifiedStudents',
        '\App\Console\Commands\CheckTestsDate',
        '\App\Console\Commands\CheckStudentsProfileCompletion',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // command: php artisan schedule:run

        // $schedule->command('online-school:delete_not_verified_students')->everyMinute();
        $schedule->call(function (){ $this->deleteNotVerifiedStudents(); });
        // $schedule->command('online-school:check_tests_date')->everyMinute();
        $schedule->call(function (){ $this->checkTestsDate(); });
        // $schedule->command('online-school:check_students_installments')->everyMinute();
        // $schedule->call(function (){ $this->checkStudentsInstallments(); });
        // $schedule->command('online-school:check_students_profile_completion')->everyMinute();
        // $schedule->call(function (){ $this->checkStudentsProfileCompletion(); });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    private function checkStudentsProfileCompletion(){
        $students = Student::where([
            ['is_profile_completed', 0],
            ['created_at', '<=', Carbon::now()->subHours(336)->toDateTimeString()]
        ])->get();

        foreach ($students as $student){
            foreach ($student->plans as $plan){
                foreach ($plan->courses as $course){
                    $access = CourseAccess::where([
                        ['student_id', $student->id],
                        ['course_id', $course->id],
                    ])->first();

                    if($access->changeable) {
                        $access->has_access = 0;
                        $access->access_deny_reason = Constant::$ACCESS_DENY_REASON_PROFILE_NOT_COMPLETED;
                        $access->save();

                        AccessController::changeStudentCourseTestAccesses($course->id, $student->id, 0);
                    }
                }
            }
        }
    }

    private function checkStudentsInstallments(){
        $installments = Installment::where([
            ['transaction_id', null],
            ['date', '<=', Carbon::now()],
        ])->get();

        foreach ($installments as $installment){
            $student = Student::find($installment->student_id);
            $courses = Plan::find($installment->plan_id)->courses;
            foreach ($courses as $course){
                $access = CourseAccess::where([
                    ['student_id', $student->id],
                    ['course_id', $course->id],
                ])->first();

                if($access->changeable) {
                    $access->has_access = 0;
                    $access->access_deny_reason = Constant::$ACCESS_DENY_REASON_INSTALLMENT_NOT_PAID;
                    $access->save();

                    AccessController::changeStudentCourseTestAccesses($course->id, $student->id, 0);
                }
            }
        }
    }

    private function deleteNotVerifiedStudents(){
        $students = Student::where([
            ['verified', '=', 0],
            ['created_at', '<=', Carbon::now()->subMinutes(1)->toDateTimeString()]
        ]);

        $students->delete();
    }

    private function checkTestsDate(){
        $tests = Test::where([
            ['start_date', '<=', Carbon::now()->subMinutes(5)],
            ['exam_holding_type', Constant::$SPECIAL_DATE_AND_TIME]
        ])->orWhere([
            ['finish_date', '<=', Carbon::now()],
            ['exam_holding_type', Constant::$FREE_DATE_AND_TYPE]
        ])->get();


        foreach ($tests as $test){
            $course = Course::find($test->course_id);
            foreach ($course->plans as $plan){
                foreach ($plan->students as $student){
                    $access = TestAccess::where([
                        ['student_id', $student->id],
                        ['test_id', $test->id],
                    ])->first();

                    if($access->changeable) {
                        $access->has_access = 0;
                        $access->save();
                    }
                }
            }
        }
    }
}
