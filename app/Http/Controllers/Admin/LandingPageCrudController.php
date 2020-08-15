<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\LandingPageRequest as StoreRequest;
use App\Http\Requests\LandingPageRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
use Exception;

/**
 * Class LandingPageCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class LandingPageCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\LandingPage');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/landingPage');
        $this->crud->setEntityNameStrings('صفحه فرود', 'صفحات فرود');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->addFields([
            [
                'name' => 'title',
                'label' => '* عنوان',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'second_title',
                'label' => '* عنوان دوم',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'button_text',
                'label' => '* متن دکمه فرم ثبت نام (حداکثر 20 کاراکتر)',
                'type' => 'text',
                'default' => 'ثبت نام در طرح',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [ // Select
                'label' => "طرح مرتبط",
                'type' => 'select2',
                'name' => 'plan_id', // the db column for the foreign key
                'entity' => 'plan', // the method that defines the relationship in your Model
                'attribute' => 'title', // foreign key attribute that is shown to user
                'model' => "App\Models\Plan", // foreign key model
                'allows_null' => true,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'video_link',
                'label' => 'لینک ویدیو',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'description',
                'label' => '* توضیحات',
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
                            <label style="font-family:Arial, Helvetica, sans-serif;">jpeg, jpg</label> و حداکثر حجم 5 مگابایت باشد )</label> کاور *',
                'name' => "cover",
                'type' => 'image',
                'upload' => true,
                'crop' => true, // set to true to allow cropping, false to disable
                'aspect_ratio' => 0, // ommit or set to 0 to allow any aspect ratio
                'disk' => 'public', // in case you need to show images from a different disk
                // 'prefix' => 'images' // in case you only store the filename in the database, this text will be prepended to the database value
            ],
        ], 'update/create/both');


        $this->crud->addColumns([
            [
                'name' => 'title',
                'label' => 'عنوان',
            ],
            [
                'name' => 'lp_link',
                'label' => 'لینک اشتراک گزاری',
            ],
        ]);
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $errors = [];
        // your additional operations before save here
        $size = $this->getBase64ImageSize($request->input('cover'));
        try {
            if ($size > 5200) {
                array_push($errors, '.حجم تصویر انتخاب شده بیشتر از 5 مگابایت است');
            }
        } catch (Exception $e) {
            abort(500);
        }

        if (sizeof($errors) > 0)
            return back()->withErrors(['custom_fail' => true, 'errors' => $errors]);

        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        $lp = $this->crud->entry;
        $lp->lp_link = env('APP_URL') . '/lp/' . $lp->id;
        $lp->save();

        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $errors = [];
        // your additional operations before save here
        $size = $this->getBase64ImageSize($request->input('cover'));
        try {
            if ($size > 5200) {
                array_push($errors, '.حجم تصویر انتخاب شده بیشتر از 5 مگابایت است');
            }
        } catch (Exception $e) {
            abort(500);
        }

        if (sizeof($errors) > 0)
            return back()->withErrors(['custom_fail' => true, 'errors' => $errors]);

        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function getBase64ImageSize($base64Image)
    { //return memory size in B, KB, MB
        try {
            $size_in_bytes = (int)(strlen(rtrim($base64Image, '=')) * 3 / 4);
            $size_in_kb = $size_in_bytes / 1024;

            return $size_in_kb;
        } catch (Exception $e) {
            return $e;
        }
    }
}
