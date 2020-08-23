<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\NoteRequest as StoreRequest;
use App\Http\Requests\NoteRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class NoteCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class NoteCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Note');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/note');
        $this->crud->setEntityNameStrings('جزوه', 'جزوات');

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
            [ // Upload
                'name' => 'file',
                'label' => 'فایل جزوه *',
                'type' => 'upload',
                'upload' => true,
                'disk' => 'public'
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
