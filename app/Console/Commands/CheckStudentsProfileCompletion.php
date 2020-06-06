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

class CheckStudentsProfileCompletion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'online-school:check_students_profile_completion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command would check students profile completion.";

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
}
