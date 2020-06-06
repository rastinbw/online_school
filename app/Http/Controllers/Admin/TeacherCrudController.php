<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\API\SkyRoomController;
use App\Includes\HttpError;
use App\Includes\Skyroom;
use App\Models\Teacher;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\TeacherRequest as StoreRequest;
use App\Http\Requests\TeacherRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
use Exception;

/**
 * Class TeacherCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class TeacherCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Teacher');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/teacher');
        $this->crud->setEntityNameStrings('استاد', 'اساتید');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->addFields([
            [
                'label' => 'وضعیت استاد',
                'name' => 'status',
                'type' => 'radio',
                'inline' => true,
                'options' => [
                    0 => 'غیرفعال',
                    1 => 'فعال',
                ],
                'default' => 0,
            ],
        ], 'update');

        $this->crud->addFields([
            [
                'name' => 'username',
                'label' => '* نام کاربری',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'password',
                'label' => '* رمزعبور',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'first_name',
                'label' => '* نام',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'last_name',
                'label' => '* نام خانوادگی',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'profession',
                'label' => '* تخصص',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'graduation',
                'label' => 'تحصیلات',
                'type' => 'textarea',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'record',
                'label' => 'سوابق',
                'type' => 'textarea',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'compilation',
                'label' => 'تالیفات',
                'type' => 'textarea',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'description',
                'label' => 'توضیحات',
                'type' => 'textarea',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'label' => '<label style="color:#e55619">( فایل انتخابی باید به فرمت
                            <label style="font-family:Arial, Helvetica, sans-serif;">jpeg, jpg</label> و حداکثر حجم 1 مگابایت باشد )</label> تصویر پرسنل',
                'name' => "avatar",
                'type' => 'image',
                'upload' => true,
                'crop' => true, // set to true to allow cropping, false to disable
                'aspect_ratio' => 1, // ommit or set to 0 to allow any aspect ratio
                'disk' => 'public', // in case you need to show images from a different disk
                // 'prefix' => 'images' // in case you only store the filename in the database, this text will be prepended to the database value
            ],

        ], 'update/create/both');

        $this->crud->addColumns([
            [
                'name' => 'username',
                'label' => 'نام کاربری',
            ],
            [
                'name' => 'password',
                'label' => 'رمز عبور',
            ],
            [
                'name' => 'first_name',
                'label' => 'نام',
            ],
            [
                'name' => 'last_name',
                'label' => 'نام خانوادگی',
            ],
            [
                'name' => 'profession',
                'label' => 'تخصص',
            ],
            [
                'name' => 'status',
                'label' => 'وضعیت',
                'type' => 'select_from_array',
                'options' => [
                    0 => 'غیر فعال',
                    1  => 'فعال',
                ],
            ],
        ]);
    }

    public function store(StoreRequest $request)
    {
        $size = $this->getBase64ImageSize($request->input('avatar'));
        try {
            if ($size > 1200) {
                return back()->withErrors(['custom_fail' => true, 'errors' => ['.حجم تصویر انتخاب شده بیشتر از 1 مگابایت است']]);
            }
        } catch (Exception $e) {
            abort(500);
        }

        if (Teacher::where([['username', $request->input('username')]])->first()) {
            return back()->withErrors([
                'custom_fail' => true,
                'errors' => ['.استاد با این نام کاربری قبلا ایجاد شده است'],
            ]);
        }

        $redirect_location = parent::storeCrud($request);

        $teacher = $this->data['entry'];
        SkyRoomController::createTeacherUser($teacher);

        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        $size = $this->getBase64ImageSize($request->input('avatar'));
        try {
            if ($size > 1200) {
                return back()->withErrors(['custom_fail' => true, 'errors' => ['.حجم تصویر انتخاب شده بیشتر از 1 مگابایت است']]);
            }
        } catch (Exception $e) {
            abort(500);
        }

        if (Teacher::where([
            ['username', $request->input('username')],
            ['id', '<>', $request->input('id')],
        ])->first()) {
            return back()->withErrors([
                'custom_fail' => true,
                'errors' => ['.استاد با این نام کاربری قبلا ایجاد شده است'],
            ]);
        }

        $redirect_location = parent::updateCrud($request);

        $teacher = $this->data['entry'];
        SkyRoomController::updateTeacherUser($teacher);

        return $redirect_location;
    }

    public function getBase64ImageSize($base64Image)
    { //return memory size in B, KB, MB
        try {
            $size_in_bytes = (int) (strlen(rtrim($base64Image, '=')) * 3 / 4);
            $size_in_kb = $size_in_bytes / 1024;

            return $size_in_kb;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        $api = new Skyroom(env('SKY_ROOM_API_URL'));
        $api->call('deleteUser', array("user_id" => Teacher::find($id)->sky_room_id));

        return $this->crud->delete($id);
    }

}
