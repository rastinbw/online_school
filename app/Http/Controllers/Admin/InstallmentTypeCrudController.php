<?php

namespace App\Http\Controllers\Admin;

use App\Includes\Constant;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\InstallmentTypeRequest as StoreRequest;
use App\Http\Requests\InstallmentTypeRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
use Hekmatinasser\Verta\Verta;

/**
 * Class InstallmentTypeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class InstallmentTypeCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\InstallmentType');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/installmentType');
        $this->crud->setEntityNameStrings('مدل قسطی', 'مدل های قسطی');
        $time = Verta::now(); //1396-02-02 15:32:08

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
                'name' => 'director',
                'label' => '* تعداد اقساط',
                'type' => 'number',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'type' => 'number',
                'label' => 'فاصله زمانی بین اقساط (روز)',
                'name' => 'span',
                'default' => 30,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'percentage_of_price_increase',
                'label' => 'درصد افزایش قیمت',
                'type' => 'number',
                'default' => 0,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'discount_disable',
                'label' => 'وضعیت تخفیف',
                'type' => 'radio',
                'options' => [ // the key will be stored in the db, the value will be shown as label;
                    Constant::$DISCOUNT_DISABLE_FALSE => 'عدم حذف',
                    Constant::$DISCOUNT_DISABLE_TRUE  => 'حذف',
                ],
                // optional
                'inline' => true, // show the radios all on the same line?
            ],
        ], 'update/create/both');

        $this->crud->addColumns([
            [
                'name' => 'title',
                'label' => 'عنوان',
            ],
            [
                'name' => 'director',
                'label' => 'تعداد اقساط',
            ],
            [
                'label' => 'فاصله زمانی بین اقساط (روز)',
                'name' => 'span',
            ],
            [
                'name' => 'percentage_of_price_increase',
                'label' => 'درصد افزایش قیمت',
            ],
            [
                'name' => 'discount_disable',
                'label' => 'حذف تخفیف',
            ],
            [
                'name' => 'discount_disable',
                'label' => 'وضعیت تخفیف',
                'type' => 'select_from_array',
                'options' => [
                    Constant::$DISCOUNT_DISABLE_FALSE => 'عدم حذف',
                    Constant::$DISCOUNT_DISABLE_TRUE  => 'حذف',
                ],
            ],
        ]);


        $this->crud->addFilter([ // add a "simple" filter called Draft
            'type' => 'dropdown',
            'name' => 'discount_disable',
            'label' => 'حذف تخفیف',
        ], [
            Constant::$DISCOUNT_DISABLE_FALSE => 'عدم حذف',
            Constant::$DISCOUNT_DISABLE_TRUE  => 'حذف',
        ], function ($value) {
            $this->crud->addClause('where', 'discount_disable', $value);
        }
        );
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
