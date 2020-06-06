<?php

namespace App\Http\Controllers\Admin;

use App\Includes\Constant;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\TestAccessRequest as StoreRequest;
use App\Http\Requests\TestAccessRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class TestAccessCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class TestAccessCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\TestAccess');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/testaccess');
        $this->crud->setEntityNameStrings('دسترسی آزمون', 'دسترسی آزمون ها');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->addFields([
            [
                'name' => 'has_access',
                'label' => 'دسترسی دارد؟',
                'type' => 'radio',
                'options' => [ // the key will be stored in the db, the value will be shown as label;
                    0 => 'خیر',
                    1  => 'بله',
                ],
                'inline' => true, // show the radios all on the same line?
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
                'name' => 'test_title',
                'label' => 'آزمون',
            ],
            [
                'name' => 'test_holding_type',
                'label' => 'نحوه برگزاری',
                'type' => 'radio',
                'options' => [ // the key will be stored in the db, the value will be shown as label;
                    Constant::$SPECIAL_DATE_AND_TIME => 'تاریخ و زمان مقرر',
                    Constant::$FREE_DATE_AND_TYPE => "شناور"
                ],
                // optional
                'inline' => true, // show the radios all on the same line?
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
