<?php

namespace App\Http\Controllers\Admin;

use App\Includes\Constant;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CourseAccessRequest as StoreRequest;
use App\Http\Requests\CourseAccessRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class CourseAccessCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CourseAccessCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\CourseAccess');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/courseaccess');
        $this->crud->setEntityNameStrings('دسترسی به کلاس','دسترسی به کلاس ها');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->addFields([
            [
                'name' => 'has_access',
                'label' => 'دسترسی دارد؟',
                'type' => 'toggle',
                'options' => [ // the key will be stored in the db, the value will be shown as label;
                    0 => 'خیر',
                    1  => 'بله',
                ],
                'hide_when' => [
                    1 => ['access_deny_reason']
                ],
                'default' => 1,
            ],
            [
                'name' => 'changeable',
                'label' => 'تغییر پذیری غیر دستی',
                'type' => 'radio',
                'options' => [ // the key will be stored in the db, the value will be shown as label;
                    0 => 'خیر',
                    1  => 'بله',
                ],
                'inline' => true, // show the radios all on the same line?
            ],
            [
                'name' => 'access_deny_reason',
                'label' => "علت عدم دسترسی",
                'type' => 'select2_from_array',
                'options' => Constant::$ACCESS_DENY_REASONS,
                'allows_null' => false,
                'default' => 0,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
        ], 'update');


        $this->crud->addColumns([
            [
                'name' => 'course_title',
                'label' => 'کلاس',
            ],
            [
                'name' => 'course_teacher',
                'label' => 'استاد',
            ],
            [
                'name' => 'changeable',
                'label' => 'تغییر پذیری غیر دستی',
                'type' => 'select_from_array',
                'options' => [
                    0 => 'خیر',
                    1  => 'بله',
                ],
            ],
            [
                'name' => 'has_access',
                'label' => 'دسترسی',
                'type' => 'select_from_array',
                'options' => [
                    0 => 'ندارد',
                    1  => 'دارد',
                ],
            ],
        ]);

        $this->crud->denyAccess([ 'create', 'delete', 'reorder']);
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
