<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Tag;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\TagRequest as StoreRequest;
use App\Http\Requests\TagRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class TagCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class TagCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Tag');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/tag');
        $this->crud->setEntityNameStrings('تگ', 'تگ ها');

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
        ], 'update/create/both');

        $this->crud->addColumns([
            [
                'name' => 'title',
                'label' => 'عنوان',
            ],
        ]);
    }

    public function store(StoreRequest $request)
    {
        if (Tag::where([['title', $request->input('title')]])->first()) {
            return back()->withErrors([
                'custom_fail' => true,
                'errors' => ['.تگ با این عنوان قبلا ایجاد شده است'],
            ]);
        }

        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        if (Tag::where([['title', $request->input('title')]])->first()) {
            return back()->withErrors([
                'custom_fail' => true,
                'errors' => ['.تگ با این عنوان قبلا ایجاد شده است'],
            ]);
        }

        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        $errors = [];

        if (Course::where('tag_id', $id)->first()) {
            array_push(
                $errors,
                "کلاس هایی با این تگ وجود دارند. برای حذف این تگ ابتدا اقدام به حذف آن ها کنید."
            );
        }

        if (sizeof($errors) > 0) {
            return response()->json(array('errors' => $errors), 400);
        }

        return $this->crud->delete($id);
    }
}
