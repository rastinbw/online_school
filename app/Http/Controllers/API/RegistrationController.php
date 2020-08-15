<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Admin\AccessController;
use App\Http\Controllers\Admin\CourseTestCrudController;
use App\Includes\Constant;
use App\Includes\Helper;
use App\Includes\HttpRequest;
use App\Models\LandingPage;
use App\Models\NationalCodePlanPair;
use App\Models\Plan;
use App\Models\Student;
use App\Models\StudentPasswordReset;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class RegistrationController extends BaseController
{
    public function sendVerificationCode(Request $req)
    {
        $phone_number = Helper::convertPersianToEnglish($req->input('phone_number'));

        // checking phone number
        $repetitive_phone = Student::where([
            ['phone_number', $phone_number],
            ['verified', 1]
        ])->exists();

        if ($repetitive_phone) {
            return $this->sendResponse(Constant::$REPETITIVE_PHONE_NUMBER, null);
        } else {
            // check for in process student
            $student = Student::where([
                ['phone_number', $phone_number],
                ['verified', 0]
            ])->first();

            // create student if not exist
            if (!$student) {
                $student = new Student();
                $student->phone_number = $phone_number;
            }


            //generate and send verification code
            // $code = 1111;
            $code = mt_rand(1000, 9999);
            $student->verification_code = $code;
            $student->landing_page_id = $req->input('lp_id');
            $student->save();

            $url = 'https://api.kavenegar.com/v1/' .
                env('SMS_API_KEY') .
                '/verify/lookup.json?receptor=' .
                $student->phone_number .
                '&template=' .
                env('TEMPLATE_VERIFY') .
                '&token=' .
                $code;

            $http = new HttpRequest($url);
            $http->get();

            return $this->sendResponse(Constant::$SUCCESS, null);
        }
    }

    public function confirmPhoneNumber(Request $req)
    {
        $result = Student::where('verification_code', $req->input('code'));

        // to prevent a low probable bug
        if ($result->count() > 1)
            return $this->sendResponse(Constant::$INVALID_VERIFICATION_CODE, null);

        $student = $result->first();
        if ($student)
            return $this->sendResponse(Constant::$SUCCESS, ['id' => $student->id]);
        else
            return $this->sendResponse(Constant::$INVALID_VERIFICATION_CODE, null);
    }

    public function completeRegistration(Request $req)
    {
        $national_code = Helper::convertPersianToEnglish($req->input('national_code'));
        $phone_number = Helper::convertPersianToEnglish($req->input('phone_number'));
        $password = Helper::convertPersianToEnglish($req->input('password'));

        if (Student::where('national_code', $national_code)->exists())
            return $this->sendResponse(Constant::$REPETITIVE_NATIONAL_CODE, null);

        $student = Student::find($req->input('id'));

        // check id validity
        if (!$student) return $this->sendResponse(Constant::$INVALID_ID, null);
        if ($student->phone_number != $phone_number)
            return $this->sendResponse(Constant::$INVALID_ID, null);

        $student->national_code = $national_code;
        $student->first_name = $req->input('first_name');
        $student->last_name = $req->input('last_name');
        $student->referrer_code = Helper::convertPersianToEnglish($req->input('referrer_code'));
        $student->region = $req->input('region');;
        $student->grade_id = $req->input('grade_id');;
        $student->field_id = $req->input('field_id');;
        $student->password = Hash::make($password);

        $student->token = bin2hex(random_bytes(16));
        $student->parent_code = $this->getCode('parent_code');
        $student->student_refer_code = "r{$this->getCode('student_refer_code')}";

        // set student verified
        $student->verification_code = null;
        $student->verified = 1;
        $student->save();

        //create user
        SkyRoomController::createUserStudent($student);

        // Add student to plans
        $pairs = NationalCodePlanPair::where('national_code', $national_code)->get();
        $access_list = [];
        foreach ($pairs as $pair) {
            $plan = Plan::find($pair->plan_id);
            $plan->students()->attach([$student->id]);

            // generate accesses
            $access_list = AccessController::createStudentPlanCourseAccesses($plan->id, $student->id, 1);
            AccessController::createStudentPlanTestAccesses($plan->id, $student->id, 1);
            CourseTestCrudController::generateStudentPlanTestRecords($plan->id, $student->id);
        }
        SkyRoomController::addStudentToRooms($access_list, $student->sky_room_id);

        $lp = ($student->landing_page_id) ? LandingPage::find($student->landing_page_id) : null;
        $plan_id = ($lp) ? $lp->plan_id : null;

        return $this->sendResponse(Constant::$SUCCESS, ['token' => $student->token, 'plan_id' => $plan_id]);
    }

    public function login(Request $req)
    {
        $phone_number = Helper::convertPersianToEnglish($req->input('phone_number'));
        $password = Helper::convertPersianToEnglish($req->input('password'));

        $student = Student::where([
            ['phone_number', $phone_number],
            ['verified', 1],
        ])->first();

        if (!$student)
            return $this->sendResponse(Constant::$INVALID_PHONE_NUMBER, null);

        if (!Hash::check($password, $student->password)) {
            if (!Hash::check(Helper::convertEnglishToPersian($password), $student->password)) {
                return $this->sendResponse(Constant::$INVALID_PASSWORD, null);
            }
        }

        $student->token = bin2hex(random_bytes(16));
        $student->save();

        return $this->sendResponse(Constant::$SUCCESS, ['token' => $student->token]);
    }

    public function checkToken(Request $req)
    {
        $student = $this->check_token($req->input('token'));
        if (!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $data = [
            'is_profile_completed' => $student->is_profile_completed,
            // 'passed_threshold' => $student->created_at <= Carbon::now()->subHours(336)->toDateTimeString() ? 1 : 0,
            'passed_threshold' => 0,
            'region' => $student->region,
            'full_name' => $student->name
        ];

        return $this->sendResponse(Constant::$SUCCESS, $data);

    }

    private function getCode($attr)
    {
        $code = $this->generateCode(100000, 999999, $attr);
        if ($code) {
            return $code;
        } else {
            // assign 8 digit parent code if couldn't succeed in 10000 try
            $code = $this->generateCode(10000000, 99999999, $attr);
            if ($code) return $code;
            else return 'NA';
        }
    }

    private function generateCode($bottom, $top, $attr)
    {
        $limitation = 10000;
        for ($i = 0; $i <= $limitation; $i++) {
            //creating code
            $code = mt_rand($bottom, $top);

            //check if code not exists
            if (Student::where($attr, '=', $code)->exists()) {
                continue;
            }
            return $code;
        }
        return null;
    }

    public function sendResetPasswordLink(Request $req)
    {
        $student = Student::where('phone_number', $req->input('phone_number'))->first();
        if (!$student)
            return $this->sendResponse(Constant::$INVALID_PHONE_NUMBER, null);

        $reset = StudentPasswordReset::where([
            ['national_code', '=', $student->national_code],
        ])->orderBy('created_at', 'desc')->first();

        if ($reset) {
            $created_at = $reset->created_at;
            $now = Carbon::now();
            if ($now->diffInMinutes($created_at) < 3) {
                return $this->sendResponse(Constant::$INVALID_REQUEST, null);
            }
        }

        $reset = new StudentPasswordReset();
        $reset->national_code = $student->national_code;
        $reset->save();

        $link = env('WWW_APP_URL')
            . '/api/student/password/reset/form/'
            . $student->token;

        $url = 'https://api.kavenegar.com/v1/' .
            env('SMS_API_KEY') .
            '/verify/lookup.json?receptor=' .
            $student->phone_number .
            '&template=' .
            env('TEMPLATE_FORGET_PASSWORD') .
            '&token=' .
            $link;

        $http = new HttpRequest($url);
        $http->get();

        return $this->sendResponse(Constant::$SUCCESS, null);
    }

    public function resetPasswordForm($token)
    {
        $student = $this->check_token($token);
        if (!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        if ($student) {
            return view('reset_password')->with('student', $student);
        } else {
            abort(404);
        }

    }

    public function resetPassword(Request $req)
    {
        $password = $req->input('password');
        $password_repeat = $req->input('password_confirmation');

        if (strlen($password) < 6 || strlen($password) > 12) {
            return back()->withErrors(['error' => '.رمز عبور باید بین 6 تا 12 کاراکتر باشد']);
        } else if ($password != $password_repeat) {
            return back()->withErrors(['error' => '.رمز عبور با تکرار آن مطابقت ندارد']);
        } else {
            $student = Student::where([
                ['national_code', '=', $req->input('national_code')],
            ])->first();
            $student->password = Hash::make($password);
            $student->save();
            return back()->with('ok', '.رمز عبور با موفقیت تغییر یافت');
        }
    }

    public function getLandingPage(Request $req, $lp_id){
        $lp = LandingPage::find($lp_id);

        if (!$lp)
            return $this->sendResponse(Constant::$LP_NOT_FOUND, null);

        $result =  [
            'id' => $lp->id,
            'plan_id' => $lp->plan_id,
            'title' => $lp->title,
            'second_title' => $lp->second_title,
            'description' => $lp->description,
            'button_text' => $lp->button_text,
            'cover' => $lp->cover,
            'video_link' => $lp->video_link
        ];

        return $this->sendResponse(Constant::$SUCCESS, $result);

    }
}
