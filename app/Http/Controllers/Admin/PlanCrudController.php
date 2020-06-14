<?php

namespace App\Http\Controllers\Admin;

use App\Imports\PlanStudentsImport;
use App\Exports\StudentsExport;
use App\Includes\Constant;
use App\Models\Category;
use App\Models\Course;
use App\Models\Field;
use App\Models\Grade;
use App\Models\Plan;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\PlanRequest as StoreRequest;
use App\Http\Requests\PlanRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class PlanCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class PlanCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Plan');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/plan');
        $this->crud->setEntityNameStrings('طرح', 'طرح ها ');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->addFields(
            [
                [
                    'label' => 'تکمیل ظرفیت',
                    'name' => 'is_full',
                    'type' => 'radio',
                    'inline' => true,
                    'options' => [
                        0 => 'خیر',
                        1 => 'بله'
                    ],
                    'default' => 0,
                ],
                [
                    'name' => 'change_course_limit',
                    'label' => 'محدودیت تغییر کلاس های طرح ثبت نام شده',
                    'type' => 'checkbox',
                    'default' => true,
                ],
            ], 'update');

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
            [ // Select
                'label' => "دسته بندی ( میتوانید در بخش دسته بندی ها اقدام به اضافه کردن دسته بندی های جدید نمایید )",
                'type' => 'select2',
                'name' => 'category_id', // the db column for the foreign key
                'entity' => 'category', // the method that defines the relationship in your Model
                'attribute' => 'title', // foreign key attribute that is shown to user
                'model' => "App\Models\Category", // foreign key model
                'allows_null' => false,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [ // Select
                'label' => "پایه ( میتوانید در بخش پایه های تحصیلی اقدام به اضافه کردن پایه های جدید نمایید )",
                'type' => 'select2',
                'name' => 'grade_id', // the db column for the foreign key
                'entity' => 'grade', // the method that defines the relationship in your Model
                'attribute' => 'title', // foreign key attribute that is shown to user
                'model' => "App\Models\Grade", // foreign key model
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                    'dir' => 'rtl',
                ],
            ],
            [ // Select
                'label' => "رشته ( میتوانید در بخش رشته های تحصیلی اقدام به اضافه کردن رشته های جدید نمایید )",
                'type' => 'select2',
                'name' => 'field_id', // the db column for the foreign key
                'entity' => 'field', // the method that defines the relationship in your Model
                'attribute' => 'title', // foreign key attribute that is shown to user
                'model' => "App\Models\Field", // foreign key model
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                    'dir' => 'rtl',
                ],
            ],
            [
                'label' => "* کلاس ها",
                'type' => 'select2_multiple',
                'name' => 'courses', // the method that defines the relationship in your Model
                'entity' => 'courses', // the method that defines the  relationship in your Model
                'attribute' => 'title', // foreign key attribute that is shown to user
                'model' => "App\Models\Course", // foreign key model
                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                // 'select_all' => true, // show Select All and Clear buttons?
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                    'dir' => 'rtl',
                ],
            ],
            [
                'label' => 'طرح رایگان است؟',
                'name' => 'is_free',
                'type' => 'toggle',
                'inline' => true,
                'options' => [
                    0 => 'خیر',
                    1 => 'بله'
                ],
                'hide_when' => [
                    1 => ['region_three_price', 'region_two_price', 'region_one_price', 'discount', 'installment_types']
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                ],
                'default' => 0,
            ],
            [
                'name' => 'region_three_price',
                'label' => 'قیمت منطقه سه',
                'type' => 'number',
                'default' => 0,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                    'dir' => 'rtl',
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name' => 'region_two_price',
                'label' => 'قیمت منطقه دو',
                'type' => 'number',
                'default' => 0,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                    'dir' => 'rtl',
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name' => 'region_one_price',
                'label' => 'قیمت منطقه یک',
                'type' => 'number',
                'default' => 0,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                    'dir' => 'rtl',
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name' => 'discount',
                'label' => 'تخفیف (درصد)',
                'type' => 'number',
                'default' => 0,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
                    'dir' => 'rtl',
                ],
            ],
            [
                'label' => "مدل های قسطی",
                'type' => 'select2_multiple',
                'name' => 'installment_types', // the method that defines the relationship in your Model
                'entity' => 'installment_types', // the method that defines the relationship in your Model
                'attribute' => 'title', // foreign key attribute that is shown to user
                'model' => "App\Models\InstallmentType", // foreign key model
                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                // 'select_all' => true, // show Select All and Clear buttons?
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:15px',
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
                    'style' => 'margin-top:15px',
                    'dir' => 'rtl',
                ],
            ],
            [
                'label' => '<label style="color:#e55619">( فایل انتخابی باید به فرمت
                            <label style="font-family:Arial, Helvetica, sans-serif;">jpeg, jpg</label> و حداکثر حجم 1 مگابایت باشد )</label> کاور',
                'name' => "cover",
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
            [
                'name' => 'is_free',
                'label' => 'رایگان',
                'type' => 'select_from_array',
                'options' => [
                    0 => 'خیر',
                    1 => 'بله',
                ],
            ],
        ]);


        $this->crud->addFilter([ // select2 filter
            'name' => 'grade_id',
            'type' => 'select2',
            'label' => 'پایه',
        ], function () {
            return Grade::all()->keyBy('id')->pluck('title', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'grade_id', $value);
        });

        $this->crud->addFilter([ // select2 filter
            'name' => 'field_id',
            'type' => 'select2',
            'label' => 'رشته',
        ], function () {
            return Field::all()->keyBy('id')->pluck('title', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'field_id', $value);
        });

        $this->crud->addFilter([ // select2 filter
            'name' => 'category_id',
            'type' => 'select2',
            'label' => 'دسته بندی',
        ], function () {
            return Category::all()->keyBy('id')->pluck('title', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'category_id', $value);
        });

        $this->crud->addFilter([ // dropdown filter
            'name' => 'is_free',
            'type' => 'dropdown',
            'label' => 'رایگان',
        ], ['0' => 'خیر', '1' => 'بله'], function ($value) { // if the filter is active
            $this->crud->addClause('where', 'is_free', $value);
        });

        $this->crud->addButtonFromView('line', 'export_plan_students', 'export_plan_students', 'beginning');
        $this->crud->addButtonFromView('line', 'import_plan_students', 'import_plan_students', 'beginning');

        $this->crud->enableDetailsRow();
        $this->crud->allowAccess('details_row');
    }

    public function exportPlanStudents($plan_id)
    {
        $export = new StudentsExport($plan_id);
        return Excel::download($export, 'لیست دانش آموزان.xlsx');
    }

    public function importPlanStudents($plan_id)
    {
        return view('vendor/backpack/crud/import_plan_students')
            ->with('title', 'وارد کردن لیست دانش آموزان کلاس')
            ->with('plan_id', $plan_id);
    }

    public function importPlanStudentsExcel(Request $req)
    {
        Excel::import(
            new PlanStudentsImport($req->input('plan_id')),
            $req->file('file')->getRealPath()
        );

        return redirect(URL::to('/admin/plan'));
    }

    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->getSaveAction();
        $this->data['fields'] = $this->crud->getUpdateFields($id);
        $this->data['title'] = trans('backpack::crud.edit') . ' ' . $this->crud->entity_name;
        $this->data['id'] = $id;

        $this->data['extra'] = json_encode(['old_courses' => $this->data['entry']['courses']]);

        return view($this->crud->getEditView(), $this->data);
    }

    public function showDetailsRow($id)
    {
        $this->crud->hasAccessOrFail('details_row');

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;

        $plan = $this->crud->getEntry($id);
        $this->data['plan'] = $this->crud->getEntry($id);

        $field = $plan->field()->first();
        $grade = $plan->grade()->first();

        $this->data['field'] = ($field != null) ? $field->title : '-';
        $this->data['grade'] = ($grade != null) ? $grade->title : '-';

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getDetailsRowView(), $this->data);
    }

    public function store(StoreRequest $request)
    {
        $errors = [];
        // your additional operations before save here
        $size = $this->getBase64ImageSize($request->input('cover'));
        try {
            if ($size > 1200) {
                array_push($errors, '.حجم تصویر انتخاب شده بیشتر از 1 مگابایت است');
            }
        } catch (Exception $e) {
            abort(500);
        }

        foreach ($request->input('courses') as $id) {
            $course = Course::find($id);
            if ($request->input('is_free') && !$course->is_free)
                array_push($errors, ".کلاس {$course->title} رایگان نمیباشد");
            else if (!$request->input('is_free') && $course->is_free)
                array_push($errors, ".کلاس {$course->title} رایگان میباشد");
        }

        if (sizeof($errors) > 0)
            return back()->withErrors(['custom_fail' => true, 'errors' => $errors]);


        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        $errors = [];
        // your additional operations before save here
        $size = $this->getBase64ImageSize($request->input('cover'));
        try {
            if ($size > 1200) {
                array_push($errors, '.حجم تصویر انتخاب شده بیشتر از 1 مگابایت است');
            }
        } catch (Exception $e) {
            abort(500);
        }

        if ($request->input('change_course_limit')) {
            if (Plan::find($request->input('id'))->students->count() > 0) {
                $old_courses = json_decode($request->input('extra'))->old_courses;
                $old_courses_ids = array_map(function ($course) {
                    return $course->id;
                }, $old_courses);
                sort($old_courses_ids);

                $new_courses_ids = $request->input('courses');
                sort($new_courses_ids);

                if ($old_courses_ids != $new_courses_ids)
                    return back()->withErrors(['custom_fail' => true, 'errors' => ".دانش آموزانی در این طرح ثبت نام نموده اند. امکان تغییر کلاس های آن را ندارید"]);
            }
        }

        foreach ($request->input('courses') as $id) {
            $course = Course::find($id);
            if ($request->input('is_free') && !$course->is_free)
                array_push($errors, ".کلاس {$course->title} رایگان نمیباشد");
            else if (!$request->input('is_free') && $course->is_free)
                array_push($errors, ".کلاس {$course->title} رایگان میباشد");
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
