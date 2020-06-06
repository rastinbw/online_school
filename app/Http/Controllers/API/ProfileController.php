<?php


namespace App\Http\Controllers\API;
use App\Http\Controllers\Admin\AccessController;
use App\Includes\Constant;
use App\Includes\Helper;
use App\Models\CourseAccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends BaseController
{
    public function getProfile(Request $req){
        // checking token
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $profile = [
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'address' => $student->address,
            'parent_phone_number' => $student->parent_phone_number,
            'home_number' => $student->home_number,
            'student_refer_code' => $student->student_refer_code,
            'gender' => $student->gender,
            'grade' => $student->grade ? ['id' => $student->grade->id, 'title' => $student->grade->title] : null,
            'field' => $student->field ? ['id' => $student->field->id, 'title' => $student->field->title] : null,
            'national_card_image'=> $student->national_card_image,
            'enrollment_certificate_image' => $student->enrollment_certificate_image
        ];

        return $this->sendResponse(Constant::$SUCCESS, $profile);
    }

    public function setProfile(Request $req){
        // checking token
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $student->first_name = $req->input('first_name');
        $student->last_name = $req->input('last_name');
        $student->email = $req->input('email');
        $student->address = $req->input('address');
        $student->parent_phone_number = $req->input('parent_phone_number');
        $student->home_number = $req->input('home_number');
        $student->gender = $req->input('gender');
        $student->grade_id = $req->input('grade_id');
        $student->field_id = $req->input('field_id');
        $student->save();

        $this->setProfileCompletion($student);

        //update user
        SkyRoomController::updateUserStudent($student);

        return $this->sendResponse(Constant::$SUCCESS, null);
    }

    public function changePassword(Request $req){
        // checking token
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        if(!Hash::check($req->input('old_password'), $student->password))
            return $this->sendResponse(Constant::$INVALID_PASSWORD, null);

        $student->password = Hash::make($req->input('new_password'));
        $student->save();

        return $this->sendResponse(Constant::$SUCCESS, null);
    }

    public function uploadNationalCardImage(Request $req){
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $action = $req->input('action');
        $file =  $req->file('image');

        $path = Helper::uploadFileToDisk(
            $action,
            $student,
            'national_card_image',
            'public',
            'images/national_cards',
            '.png',
            $file
        );

        $this->setProfileCompletion($student);

        return $this->sendResponse(Constant::$SUCCESS, ['path' => $path]);
    }

    public function uploadEnrollmentCertificateImage(Request $req){
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $action = $req->input('action');
        $file =  $req->file('image');

        $path = Helper::uploadFileToDisk(
            $action,
            $student,
            'enrollment_certificate_image',
            'public',
            'images/enrollment_certificates',
            '.png',
            $file
        );

        $this->setProfileCompletion($student);

        return $this->sendResponse(Constant::$SUCCESS, ['path' => $path]);
    }

    private function setProfileCompletion($student){
        $profile_was_completed = $student->is_profile_completed;
        $profile_is_completed = (
            $student->first_name !== null &&
            $student->last_name !== null &&
            $student->email !== null &&
            $student->address !== null &&
            $student->gender !== null &&
            $student->grade_id !== null &&
            $student->field_id !== null &&
            $student->parent_phone_number !== null &&
            $student->enrollment_certificate_image !== null &&
            $student->national_card_image !== null
        );

        if($profile_is_completed &&
           !$profile_was_completed &&
           $student->created_at <= Carbon::now()->subHours(336)->toDateTimeString()
        ){
            foreach ($student->plans as $plan){
                foreach ($plan->courses as $course){
                    $access = CourseAccess::where([
                        ['student_id', $student->id],
                        ['course_id', $course->id],
                    ])->first();

                     $access->has_access = 1;
                     $access->save();
                     AccessController::changeStudentCourseTestAccesses($course->id, $student->id, 1);
                }
            }
        }

        $student->is_profile_completed = $profile_is_completed;
        $student->save();


    }


}
