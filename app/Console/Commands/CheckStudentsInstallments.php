<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\AccessController;
use App\Includes\Constant;
use App\Models\CourseAccess;
use App\Models\Installment;
use App\Models\Plan;
use App\Models\Student;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Console\Command;

class CheckStudentsInstallments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'online-school:check_students_installments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command would check students installments and change their access to courses.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
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
}
