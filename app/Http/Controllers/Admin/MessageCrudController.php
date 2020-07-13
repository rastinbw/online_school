<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\MessageRequest as StoreRequest;
use App\Http\Requests\MessageRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class MessageCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class MessageCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Message');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/message');
        $this->crud->setEntityNameStrings('پیام', 'پیام ها');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->addFields([
            [ // Select
                'label' => "قالب پیامک",
                'type' => 'select2',
                'name' => 'sms_template_id', // the db column for the foreign key
                'entity' => 'sms_template', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\SmsTemplate", // foreign key model
                'allows_null' => false,
            ],
            [
                'name' => 'params',
                'label' => '* توکن ها',
                'type' => 'child',
                'entity_singular' => 'اضافه',
                'columns' => [
                    [
                        'label' => 'شماره',
                        'type' => 'child_select',
                        'name' => 'number',
                        'data' => [1,2,3],
                    ],
                    [
                        'label' => 'مقدار',
                        'type' => 'child_text',
                        'name' => 'value',
                    ],
                ],
                'max' => 3,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ]
            ],
        ], 'create');

        $this->crud->addColumns([
            [
                'label' => "قالب پیامک", // Table column heading
                'type' => "select",
                'name' => 'sms_template_id', // the column that contains the ID of that connected entity;
                'entity' => 'sms_template', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\SmsTemplate", // foreign key model
            ],
            [
                'name' => 'tokens',
                'label' => 'توکن ها',
            ],
        ]);

        $this->crud->denyAccess([ 'update']);

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
