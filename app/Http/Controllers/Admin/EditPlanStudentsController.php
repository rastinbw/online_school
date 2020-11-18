<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\API\PlansController;
use App\Http\Controllers\Controller;
use App\Models\CourseAccess;
use App\Models\Plan;
use App\Models\Student;
use App\Models\TestAccess;
use Illuminate\Http\Request;

class EditPlanStudentsController extends Controller
{
    public function show_students($plan_id){
        $plan = Plan::find($plan_id);
        $students = $plan->students;
        return view('edit_plan_students')->with('plan', $plan)->with('students', $students);
    }

    public function add_student_to_plan(Request $request){
        $plan = Plan::find($request->input('plan_id'));
        $student = Student::where('national_code',$request->input('national_code'))->first();

        $pc = new PlansController();
        $pc->registerInPlan($student, $plan);

        return response()->json(array('result' => 'success', 200));
    }

    public function remove_students_from_plan(Request $request){
        $plan = Plan::find($request->input('plan_id'));
        $sid_list = $request->input('student_id_list');

        foreach ($sid_list as $student_id)
        {
            // $pc = new PlansController();
            // $pc->unregisterFromPlan($id, $plan);
            $plan->students()->detach($student_id);
            foreach ($plan->courses as $course){
                $belongs_to_course_via_other_plans = false;
                foreach ($course->plans as $plan){
                    if ($plan->students->contains($student_id)) {
                        $belongs_to_course_via_other_plans = true;
                        break;
                    }
                }

                if (!$belongs_to_course_via_other_plans){
                    CourseAccess::where([
                        ['student_id',$student_id],
                        ['course_id',$course->id]
                    ])->delete();

                    foreach ($course->tests as $test){
                        TestAccess::where([
                            ['student_id',$student_id],
                            ['test_id',$test->id]
                        ])->delete();
                    }
                }
            }
        }


        return response()->json(array('result' => $sid_list), 200);
    }
}
