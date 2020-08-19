<?php

namespace App\Http\Controllers\Admin;

use App\Models\SchoolConfig;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\SchoolConfigRequest as StoreRequest;
use App\Http\Requests\SchoolConfigRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class SchoolConfigCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class SchoolConfigCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\SchoolConfig');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/schoolConfig');
        $this->crud->setEntityNameStrings('تنظیمات', 'تنظیمات');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $first_tab = "لینک داغ";

        $this->crud->addFields([
            [
                'name' => 'hot_link_url',
                'label' => 'آدرس لینک داغ',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
                'tab' => $first_tab,
            ],
            [
                'name' => 'hot_link_title',
                'label' => 'عنوان لینک داغ',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
                'tab' => $first_tab,
            ]
        ], 'update');


        $this->crud->denyAccess(['list', 'create', 'reorder', 'delete']);
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
        $this->data['schoolConfig'] = true;

        $config = SchoolConfig::find(1);

        if ($config->id != $id) {
            return abort(404);
        }

        return view($this->crud->getEditView(), $this->data);
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
