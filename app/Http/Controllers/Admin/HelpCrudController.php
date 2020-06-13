<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\HelpRequest as StoreRequest;
use App\Http\Requests\HelpRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class HelpCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class HelpCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Help');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/help');
        $this->crud->setEntityNameStrings('آموزش', 'آموزش ها');

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
                'name' => 'video_link',
                'label' => '* لینک ویدیو',
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
