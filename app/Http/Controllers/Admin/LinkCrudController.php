<?php

namespace App\Http\Controllers\Admin;

use App\Models\Link;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\LinkRequest as StoreRequest;
use App\Http\Requests\LinkRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class LinkCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class LinkCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Link');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/link');
        $this->crud->setEntityNameStrings('لینک های ارتباطی', 'لینک های ارتباطی');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->addFields([
            [
                'name' => 'telegram',
                'label' => 'تلگرام',
                'type' => 'text',
                'attributes' => [
                    'style' => 'font-size: 20px',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'instagram',
                'label' => 'اینستاگرام',
                'type' => 'text',
                'attributes' => [
                    'style' => 'font-size: 20px',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'email',
                'label' => 'ایمیل',
                'type' => 'text',
                'attributes' => [
                    'style' => 'font-size: 20px',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'tel1',
                'label' => 'شماره تلفن اول',
                'type' => 'text',
                'attributes' => [
                    'style' => 'font-size: 20px',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'tel2',
                'label' => 'شماره تلفن دوم',
                'type' => 'text',
                'attributes' => [
                    'style' => 'font-size: 20px',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
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
        $this->data['link'] = true;

        $link = Link::find(1);

        if ($link->id != $id) {
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
