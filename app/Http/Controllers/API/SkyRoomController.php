<?php


namespace App\Http\Controllers\API;


use App\Includes\HttpError;
use App\Includes\Skyroom;
use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;

class SkyRoomController extends BaseController
{
    public static function addStudentToRooms($access_list, $sky_room_id)
    {
        $rooms = array_map(function ($access){
            return array(
                'room_id' => Course::find($access->course_id)->room_id,
                'access' => Skyroom::USER_ACCESS_NORMAL
            );
        }, $access_list);

        $api = new Skyroom(env('SKY_ROOM_API_URL'));
        $api->call('addUserRooms', array(
            'user_id' => $sky_room_id,
            'rooms' => $rooms,
        ));

        if (HttpError::IsError($result)) {
            foreach ($access_list as $access){
                $access->has_access = 0;
                $access->save();
            }
        }
    }


    public static function changeStudentAccessToRoom($access)
    {
        $api = new Skyroom(env('SKY_ROOM_API_URL'));
        $action = ($access->has_access) ? 'addUserRooms' : 'removeUserRooms';
        $rooms = ($access->has_access)
            ? [['room_id' => Course::find($access->course_id)->room_id, 'access' => Skyroom::USER_ACCESS_NORMAL]]
            : [Course::find($access->course_id)->room_id];

        $api->call($action, array(
            'user_id' => Student::find($access->student_id)->sky_room_id,
            'rooms' => $rooms
        ));

        if (HttpError::IsError($result)) {
             $access->has_access = !$access->has_access;
             $access->save();
        }
    }

    /**
     * @param $student
     */
    public static function createUserStudent($student)
    {
        $params = array(
            'username' => $student->national_code,
            'nickname' => $student->name,
            'password' => $student->national_code,
            'fname' => $student->first_name,
            'lname' => $student->last_name,
        );

        $api = new Skyroom(env('SKY_ROOM_API_URL'));
        $result = $api->call('createUser', $params);

        if (!HttpError::IsError($result)) {
            $student->sky_room_id = $result['result'];
            $student->status = 1;
            $student->save();
        }

    }

    /**
     * @param $student
     */
    public static function updateUserStudent($student)
    {
        $params = array(
            "user_id" => $student->sky_room_id,
            'nickname' => $student->name,
            'fname' => $student->first_name,
            'lname' => $student->last_name,
        );

        $api = new Skyroom(env('SKY_ROOM_API_URL'));
        $api->call('updateUser', $params);
    }

    /**
     * @param $teacher
     */
    public static function createTeacherUser($teacher)
    {
        $params = array(
            'username' => $teacher->username,
            'nickname' => $teacher->list_title,
            'password' => $teacher->password,
            'fname' => $teacher->first_name,
            'lname' => $teacher->last_name,
        );

        $api = new Skyroom(env('SKY_ROOM_API_URL'));
        $result = $api->call('createUser', $params);

        if (!HttpError::IsError($result)) {
            $teacher->sky_room_id = $result['result'];
            $teacher->status = 1;
            $teacher->save();
        }
    }

    /**
     * @param $teacher
     */
    public static function updateTeacherUser($teacher)
    {
        $params = array(
            "user_id" => $teacher->sky_room_id,
            'username' => $teacher->username,
            'nickname' => $teacher->list_title,
            'password' => $teacher->password,
            'fname' => $teacher->first_name,
            'lname' => $teacher->last_name,
            'status' => $teacher->status,
        );

        $api = new Skyroom(env('SKY_ROOM_API_URL'));
        $api->call('updateUser', $params);

        if (HttpError::IsError($result)) {
            $teacher->status = 0;
            $teacher->save();
        }
    }

    /**
     * @param $course
     */
    public static function createRoom($course)
    {
        $params = array(
            'name' => "course#{$course->id}",
            'title' => "کلاس {$course->title} - {$course->teacher->name}",
            'guest_login' => ($course->guest_login) ? true : false,
            'op_login_first' => ($course->op_login_first) ? true : false
        );

        if($course->guest_login)
            $params['guest_limit'] = $course->guest_limit;

        $api = new Skyroom(env('SKY_ROOM_API_URL'));
        $result = $api->call('createRoom', $params);

        if (!HttpError::IsError($result)) {
            $room_id = $result['result'];

            // get room url
            $url_result = $api->call('getRoomUrl', array(
                'room_id' => $room_id,
                'language' => 'fa',
            ));

            if (!HttpError::IsError($url_result)) {
                $room_url = $url_result['result'];
                $course->room_id = $room_id;
                $course->room_url = $room_url;
                $course->status = 1;
                $course->save();
            }

            // add teacher to room
            $add_user_room_result = $api->call('addUserRooms', array(
                'user_id' => Teacher::find($course->teacher_id)->sky_room_id,
                'rooms' => array(
                    array('room_id' => $room_id, 'access' => Skyroom::USER_ACCESS_OPERATOR),
                ),
            ));

            if (HttpError::IsError($add_user_room_result)) {
                $course->status = 0;
                $course->save();
            }
        }
    }

    /**
     * @param $course
     * @param $old_teacher_id
     */
    public static function updateRoom($course, $old_teacher_id)
    {
        $params = array(
            'room_id' => $course->room_id,
            'title' => "کلاس {$course->title} - {$course->teacher->name}",
            'max_users' => $course->max_users,
            'guest_limit' => $course->guest_limit,
            'guest_login' => ($course->guest_login) ? true : false,
            'op_login_first' => ($course->op_login_first) ? true : false,
            'status' => $course->status
        );

        $api = new Skyroom(env('SKY_ROOM_API_URL'));
        $result = $api->call('updateRoom', $params);

        if (HttpError::IsError($result)) {
            $course->status = 0;
            $course->save();
        }

        if ($course->teacher_id != $old_teacher_id) {
            // remove teacher from room
            $remove_user_room_result = $api->call('removeUserRooms', array(
                'user_id' => Teacher::find($old_teacher_id)->sky_room_id,
                'rooms' => [$course->room_id]
            ));

            // add teacher to room
            $add_user_room_result = $api->call('addUserRooms', array(
                'user_id' => Teacher::find($course->teacher_id)->sky_room_id,
                'rooms' => array(
                    array('room_id' => $course->room_id, 'access' => Skyroom::USER_ACCESS_OPERATOR),
                ),
            ));

            if (HttpError::IsError($remove_user_room_result) ||
                HttpError::IsError($add_user_room_result)) {
                $course->status = 0;
                $course->save();
            }

        }
    }

    public static function updateRoomTitle($course, $session)
    {
        if($course->status){
            $added = $session->is_online ? " - {$session->title}" : "";

            //update room
            $params = array(
                'room_id' => $course->room_id,
                'title' => "کلاس {$course->title} - {$course->teacher->name}" . $added,
            );

            $api = new Skyroom(env('SKY_ROOM_API_URL'));
            $api->call('updateRoom', $params);
        }
    }

}
