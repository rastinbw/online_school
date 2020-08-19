<?php


namespace App\Http\Controllers\API;
use App\Includes\Constant;
use App\Includes\Helper;
use App\Models\About;
use App\Models\Course;
use App\Models\Link;
use App\Models\Plan;
use App\Models\SchoolConfig;
use App\Models\SliderPlan;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class MainPageController extends BaseController
{
    public function loginAsParent(Request $req){
        $code = $req->input('code');
        $student = Student::where('parent_code', $code)->first();

        if($student)
            return $this->sendResponse(Constant::$SUCCESS, $student);
        else
            return $this->sendResponse(Constant::$INVALID_PARENT_CODE, null);
    }

    public function sendMainPage(Request $req){
        $page = [
            'links' => $this->getLinks(),
            'about' => $this->getAbout(),
            'teachers' => $this->getTeachers(),
            'courses' => $this->getCourses(),
            'sliders' => $this->getSliders(),
            'hotlink' => $this->getHotLink()
        ];

        return $this->sendResponse(Constant::$SUCCESS, $page);
    }

    public function sendTeachers(Request $req){
        return $this->sendResponse(Constant::$SUCCESS, $this->getTeachers());
    }

    public function sendLinks(Request $req){
        return $this->sendResponse(Constant::$SUCCESS, $this->getLinks());
    }

    public function sendAbout(Request $req){
        return $this->sendResponse(Constant::$SUCCESS,  $this->getAbout());
    }

    public function sendCourses(Request $req){
        return $this->sendResponse(Constant::$SUCCESS,  $this->getCourses());
    }

    public function sendSliders(Request $req){
        return $this->sendResponse(Constant::$SUCCESS,  $this->getSliders());
    }

    private function getAbout(){
        return About::all()[0]->content;
    }

    private function getLinks(){
        $links = Link::all()[0];
        return [
            'telegram' => $links->telegram,
            'instagram' => $links->instagram,
            'email' => $links->email,
            'tel1' => $links->tel1,
            'tel2' => $links->tel2
        ];
    }

    private function getTeachers(){
        $teachers = Teacher::all()->shuffle();
        $teachers = $teachers->map(function ($teacher){
            $default = "images/teachers/default.png";
            return [
                'id' => $teacher->id,
                'avatar' => $teacher->avatar ? $teacher->avatar : $default,
                'first_name' => $teacher->first_name,
                'last_name' => $teacher->last_name,
                'graduation' => $teacher->graduation,
                'record' => $teacher->record,
                'compilation' => $teacher->compilation,
                'description' => $teacher->description
            ];
        });

        return $teachers;
    }

    private function getCourses()
    {
        $courses = Course::all();
        $courses = $courses->map(function ($course){
            return [
                'id' => $course->id,
                'title' => ($course->display_title) ? $course->display_title : $course->title,
                'day' => $course->online_day,
                'teacher' => $course->teacher->name
            ];
        });

        return $courses;
    }

    public function getSliders(){
        $sliders = SliderPlan::all();
        $sliders = $sliders->map(function ($slider){
            return [
                'plan_id' => $slider->plan_id,
                'link' => $slider->link,
                'cover' => $slider->cover
            ];
        });

        return $sliders;
    }

    public function getHotLink(){
        $config = SchoolConfig::find(1);

        if ($config->hot_link_title == null || $config->hot_link_url == null)
            return null;

        return [
            'title' => $config->hot_link_title,
            'url' => $config->hot_link_url
        ];
    }
}
