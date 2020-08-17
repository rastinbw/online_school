<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Plan;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CategoryRequest as StoreRequest;
use App\Http\Requests\CategoryRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
use Exception;

/**
 * Class CategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CategoryCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Category');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/category');
        $this->crud->setEntityNameStrings('دسته بندی', 'دسته بندی ها');

        $this->crud->allowAccess('reorder');
        $this->crud->enableReorder('title', 1);

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
                'name' => 'description',
                'label' => 'توضیحات',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'label' => '<label style="color:#e55619">( فایل انتخابی باید به فرمت
                            <label style="font-family:Arial, Helvetica, sans-serif;">jpeg, jpg</label> و حداکثر حجم 1 مگابایت باشد )</label> لوگو',
                'name' => "logo",
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
                'name' => 'title',
                'label' => 'عنوان',
            ],
        ]);

    }

    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');

        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->getSaveAction();
        $this->data['fields'] = $this->crud->getUpdateFields($id);
        $this->data['title'] = trans('backpack::crud.edit') . ' ' . $this->crud->entity_name;
        $this->data['id'] = $id;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getEditView(), $this->data);
    }

    public function store(StoreRequest $request)
    {
        return $this->save($request, 0);
    }

    public function update(UpdateRequest $request)
    {
        return $this->save($request, 1);
    }

    public function save($request, $type){
        $category = Category::where('title', $request->input('title'))->first();

        if ($category != null) {
            if($type == 0){
                return back()->withErrors([
                    'custom_fail' => true,
                    'errors' => ['.دسته بندی با این عنوان قبلا ایجاد شده است'],
                ]);
            }else{
                if ($category->id != $request->input('id')) {
                    return back()->withErrors([
                        'custom_fail' => true,
                        'errors' => ['.دسته بندی با این عنوان قبلا ایجاد شده است'],
                    ]);
                }
            }
        }

        $size = $this->getBase64ImageSize($request->input('logo'));
        try {
            if ($size > 1200) {
                return back()->withErrors(['custom_fail' => true, 'errors' => ['.حجم تصویر انتخاب شده بیشتر از 1 مگابایت است']]);
            }
        } catch (Exception $e) {
            abort(500);
        }

        if($type == 0)
            $redirect_location = parent::storeCrud($request);
        else
            $redirect_location = parent::updateCrud($request);

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

        $errors = [];
        if (Plan::where('category_id', $id)->first()) {
            array_push(
                $errors,
                "طرح هایی با این دسته بندی وجود دارند. برای حذف این پایه ابتدا اقدام به حذف آن ها کنید."
            );
        }

        if (sizeof($errors) > 0) {
            return response()->json(array('errors' => $errors), 400);
        }

        return $this->crud->delete($id);
    }
}
