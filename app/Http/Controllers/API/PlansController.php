<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Admin\AccessController;
use App\Http\Controllers\Admin\CourseTestCrudController;
use App\Includes\Constant;
use App\Includes\Helper;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\Plan;
use App\Models\Session;
use Illuminate\Http\Request;

class PlansController extends BaseController
{
    public function registerInFreePlan(Request $req){
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $plan = Plan::find($req->input('plan_id'));
        if (!$plan->is_free)
            return $this->sendResponse(Constant::$PLAN_NOT_FREE, null);

        $this->registerInPlan($student, $plan);
        return $this->sendResponse(Constant::$SUCCESS, null);
    }

    public function registerInPlan($student, $plan){
        $plan->students()->attach([$student->id]);

        // generate accesses
        $access_list = AccessController::createStudentPlanCourseAccesses($plan->id, $student->id, 1);
        AccessController::createStudentPlanTestAccesses($plan->id, $student->id, 1);
        CourseTestCrudController::generateStudentPlanTestRecords($plan->id, $student->id);
        SkyRoomController::addStudentToRooms($access_list, $student->sky_room_id);
    }

    public function getPlanList($category_id, $tag_id, $grade_id, $field_id){
        // get courses with appropriate filters
        $query = [];
        $orQuery = [];

        if ($grade_id != 'null') {
            array_push($query, ['grade_id', '=', $grade_id]);
            array_push($orQuery, ['grade_id', '=', null]);
        }

        if ($field_id != 'null') {
            array_push($query, ['field_id', '=', $field_id]);
            array_push($orQuery, ['field_id', '=', null]);
        }

        array_push($query, ['category_id', '=', $category_id]);

        $plans = Plan::where($query)->orWhere($orQuery)->get()->map(function ($plan){
            return $this->buildPlanObject($plan);
        })->toArray();

        if ($tag_id != 'null') {
            $tagFilteredPlans = $this->getPlansByTag($tag_id);
            $plans = Helper::getIntersect($plans, $tagFilteredPlans);
        }

        return $this->sendResponse(Constant::$SUCCESS, $plans);
    }

    public function getStudentPlanList(Request $req){
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $plans = $student->plans()->get()->map(function ($plan){
            return $this->buildPlanObject($plan);
        })->toArray();

        return $this->sendResponse(Constant::$SUCCESS, $plans);
    }

    public function hasRegisteredToPlan(Request $req){
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $plan = Plan::find($req->input('plan_id'));
        if($plan == null)
            return $this->sendResponse(Constant::$INVALID_ID, null);

        $has_registered = ($plan->students->contains($student->id)) ? true : false;

        return $this->sendResponse(Constant::$SUCCESS, $has_registered);
    }

    public function getStudentCoursesByDay(Request $req){
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $courses = [];
        foreach($student->plans as $plan)
            $courses = array_merge($courses, $plan->courses->toArray());

        $courses = array_map(function ($course){
            unset($course['pivot']);
            return $course;
        }, $courses);


        $days = [];
        foreach (Constant::$DAYS as $DAY)
            $days[$DAY] = [];

        foreach(Helper::removeSimilarObjects($courses) as $course){
            $startTime = "{$course['start_hour']} : {$course['start_min']}";
            $finishTime = "{$course['finish_hour']} : {$course['finish_min']}";
            array_push($days[$course['online_day']], ["title" => $course['title'], "start" => $startTime, "finish" => $finishTime]);
        }

        return $this->sendResponse(Constant::$SUCCESS, $days);
    }

    public function getPlanCourses(Request $req){
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $plan = Plan::find($req->input('plan_id'));
        if($plan == null)
            return $this->sendResponse(Constant::$INVALID_ID, null);

        $courses = $plan->courses->map(function($course) use ($plan, $student){
            return $this->buildCourseObject($plan, $course, $student);
        });

        return $this->sendResponse(Constant::$SUCCESS, $courses);
    }

    public function getSessionVideoLink(Request $req){
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $plan = Plan::find($req->input('plan_id'));
        if($plan == null)
            return $this->sendResponse(Constant::$INVALID_ID, null);

        $session = Session::find($req->input('session_id'));
        if($session == null)
            return $this->sendResponse(Constant::$INVALID_ID, null);

        $course = Course::find($session->course_id);

        $access = CourseAccess::where([
            ['student_id', $student->id],
            ['course_id', $course->id],
        ])->first();

        $has_registered = ($plan->students->contains($student->id)) ? 1 : 0;

        if($this->getSessionAccess($course, $session, $access, $has_registered) && $session->video_link)
            return $this->sendResponse(Constant::$SUCCESS, $session->video_link);

        return $this->sendResponse(Constant::$VIDEO_UNAVAILABLE, null);
    }

    public function getSessionVideoDownloadLink(Request $req){
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $plan = Plan::find($req->input('plan_id'));
        if($plan == null)
            return $this->sendResponse(Constant::$INVALID_ID, null);

        $session = Session::find($req->input('session_id'));
        if($session == null)
            return $this->sendResponse(Constant::$INVALID_ID, null);

        $course = Course::find($session->course_id);

        $access = CourseAccess::where([
            ['student_id', $student->id],
            ['course_id', $course->id],
        ])->first();

        $has_registered = ($plan->students->contains($student->id)) ? 1 : 0;

        if($this->getSessionAccess($course, $session, $access, $has_registered) && $session->video_download_link)
            return $this->sendResponse(Constant::$SUCCESS, $session->video_download_link);

        return $this->sendResponse(Constant::$DOWNLOAD_UNAVAILABLE, null);
    }

    public function getCurrentOnlineCourse()
    {
        $session = Session::where('is_online', 1)->first();
        $data = null;

        if ($session){
            $data = [
                'course_title' => $session->course->title,
                'session_title' => $session->title,
                'teacher_name' => $session->course->teacher->name,
                'teacher_avatar' => $session->course->teacher->avatar,
                'room_url' => $session->course->room_url,
            ];
        }

        return $this->sendResponse(Constant::$SUCCESS, $data);
    }

    private function buildCourseObject($plan, $course, $student){
        $access = CourseAccess::where([
            ['student_id', $student->id],
            ['course_id', $course->id],
        ])->first();

        $has_registered = ($access) ? 1 : 0;

        $has_free_sessions = Session::where([
            ['course_id', $course->id],
            ['is_free', 1]
        ])->exists();

        return [
            "id" => $course->id,
            "title" => $course->title,
            "launch_date_year" => $course->launch_date_year,
            "launch_date_month" => $course->launch_date_month,
            "launch_date_day" => $course->launch_date_day,
            "online_day" => $course->online_day,
            "start_hour" => $course->start_hour,
            "start_min" => $course->start_min,
            "finish_hour" => $course->finish_hour,
            "finish_min" => $course->finish_min,
            "status" => $course->status,
            "room_url" => $course->room_url,
            "is_online" => $course->is_online,
            "is_free" => $course->is_free,
            "has_free_sessions" => $has_free_sessions ? 1 : 0,
            "has_registered" => $has_registered,
            "access_denied" => ($access != null) ? ($access->has_access ? 0 : 1) : 0,
            "deny_access_reason" => ($access != null) ? $access->access_deny_reason : 0,
            "description" => $course->description,
            "teacher" => $course->teacher()->get()->map(function($teacher){
                return $this->buildTeacherObject($teacher);
            })[0],
            "tag" => $course->tag()->get()->map(function($tag){
               return $this->buildTagObject($tag);
            })[0],
            "sessions" => $course->sessions->map(function ($session) use ($access, $course, $has_registered){
               return $this->buildSessionObject($course, $session, $access, $has_registered);
            }),
            "tests" => $course->tests->map(function ($test) use ($student){
                return $this->buildTestObject($test, $student);
            })
        ];
    }

    private function buildSessionObject($course, $session, $course_access, $has_registered){
        return [
            "id" => $session->id,
            "title" => $session->title,
            "date_year" => $session->date_year,
            "date_month" => $session->date_month,
            "date_day" => $session->date_day,
            "start_hour" => $session->start_hour,
            "start_min" => $session->start_min,
            "finish_hour" => $session->finish_hour,
            "finish_min" => $session->finish_min,
            "notes" => $session->notes,
            "is_free" => $session->is_free,
            "downloadable" => $session->video_download_link != null ? 1 : 0,
            "is_online" => $session->is_online,
            "held" => $session->held,
            "has_access" => $this->getSessionAccess($course, $session, $course_access, $has_registered),
            "description" => $session->description,
        ];
    }

    private function buildTagObject($tag){
        return [
            "id" => $tag->id,
            "title" => $tag->title,
        ];
    }

    private function buildTeacherObject($teacher)
    {
        return [
            "id" => $teacher->id,
            "avatar" => $teacher->avatar,
            "first_name" => $teacher->first_name,
            "last_name" => $teacher->last_name,
            "profession" => $teacher->profession,
            "graduation" => $teacher->graduation,
            "record" => $teacher->record,
            "compilation" => $teacher->compilation,
            "description" => $teacher->description,
        ];
    }

    private function buildPlanObject($plan){
        $grade = ($plan->grade) ? $plan->grade->title : "همه پایه ها";
        $field = ($plan->field) ? $plan->field->title : "همه رشته ها";

        $plan = [
            'id' => $plan->id,
            'title' => $plan->title,
            'cover' => $plan->cover,
            'description' => $plan->description,
            'is_free' => $plan->is_free,
            'is_full' => $plan->is_full,
            'discount' => $plan->discount,
            'region_one_price' => $plan->region_one_price,
            'region_two_price' => $plan->region_two_price,
            'region_three_price' => $plan->region_three_price,
            'installment_types' => $plan->installment_types()->get()->map(function($installment){
                return [
                    'id' => $installment->id,
                    'title' => $installment->title,
                    'director' => $installment->director,
                    'percentage_of_price_increase' => $installment->percentage_of_price_increase,
                    'discount_disable' => $installment->discount_disable,
                    'span' => $installment->span
                ];
            }),
            'category_id' => $plan->category->id,
            'grade' => $grade,
            'field' => $field,
        ];

        return $plan;
    }

    private function getPlansByTag($tag_id){
        $courses = Course::where('tag_id', $tag_id)->get();

        $planList = [];
        foreach ($courses as $course){
            $plans = $course->plans()->get()->map(function ($plan){
                return $this->buildPlanObject($plan);
            })->toArray();

            $planList = array_merge($plans, $planList);
        }

        return Helper::removeSimilarObjects($planList);
    }

    /**
     * @param $course
     * @param $session
     * @param $course_access
     * @param $has_registered
     * @return bool|int
     */
    private function getSessionAccess($course, $session, $course_access, $has_registered)
    {
        $access = 0;
        if ($session->held || $session->is_online) {
            if (!$course->is_free) {
                if ($has_registered)
                    $access = ($course_access->has_access) ? 1 : $session->is_free;
                else
                    $access = $session->is_free;
            } else
                $access = $has_registered;
        }
        return $access;
    }

    private function hasStudentRegisteredToCourse($course, $student)
    {
        foreach ($course->plans as $plan){
            if($plan->students->contains($student->id))
                return 1;
        }

        return 0;
    }

    private function buildTestObject($test, $student)
    {
        $tc = new TestsController();
        return $tc->buildTestObject($test, $student->id, false);
    }

}
