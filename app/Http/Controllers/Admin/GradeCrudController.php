<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Grade;
use App\Models\Student;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\GradeRequest as StoreRequest;
use App\Http\Requests\GradeRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class GradeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class GradeCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Grade');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/grade');
        $this->crud->setEntityNameStrings('پایه', 'پایه ها');

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
        if (Grade::where([['title', $request->input('title')]])->first()) {
            return back()->withErrors([
                'custom_fail' => true,
                'errors' => ['.پایه تحصیلی با این عنوان قبلا ایجاد شده است'],
            ]);
        }

        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        if (Grade::where([['title', $request->input('title')]])->first()) {
            return back()->withErrors([
                'custom_fail' => true,
                'errors' => ['.پایه تحصیلی با این عنوان قبلا ایجاد شده است'],
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
        if (Student::where('grade_id', $id)->first()) {
            array_push(
                $errors,
                "دانش آموزانی با این پایه تحصیلی وجود دارند. برای حذف این پایه ابتدا اقدام به حذف آن ها کنید."
            );
        }

        if (Course::where('grade_id', $id)->first()) {
            array_push(
                $errors,
                "کلاس هایی با این پایه تحصیلی وجود دارند. برای حذف این پایه ابتدا اقدام به حذف آن ها کنید."
            );
        }

        if (sizeof($errors) > 0) {
            return response()->json(array('errors' => $errors), 400);
        }

        return $this->crud->delete($id);
    }
}
