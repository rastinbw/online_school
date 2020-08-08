<?php

namespace App\Http\Controllers\Admin;

use App\Includes\Constant;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\TransactionRequest as StoreRequest;
use App\Http\Requests\TransactionRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

class TransactionCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Transaction');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/transaction');
        $this->crud->setEntityNameStrings('ترکنش', 'تراکنش ها');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->denyAccess([ 'create', 'update', 'reorder']);

        $this->crud->addColumns([
            [
                'name' => 'title',
                'label' => 'عنوان',
            ],
            [
                'name' => 'order_no',
                'label' => 'شماره سفارش',
            ],
            [
                'name' => 'issue_tracking_no',
                'label' => 'کد پیگیری',
            ],
            [
                'name' => 'transaction_payment_type',
                'label' => 'نوع پرداخت',
                'type' => 'select_from_array',
                'options' => [
                    Constant::$PAYMENT_TYPE_COMPLETE => 'کامل',
                    Constant::$PAYMENT_TYPE_INSTALLMENT  => 'قسط',
                ],
            ],
            [
                'name' => 'date',
                'label' => 'تاریخ',
            ],
            [
                'name' => 'paid_amount',
                'label' => 'مبلغ',
            ],
            [
                'name' => 'student_name',
                'label' => 'نام دانش آموز',
            ],
            [
                'name' => 'student_national_code',
                'label' => 'کد ملی دانش آموز',
            ],
            [
                'name' => 'success',
                'label' => 'وضعیت پرداخت',
                'type' => 'select_from_array',
                'options' => [
                    1 => 'موفق',
                    0  => 'ناموفق',
                ],
            ],
        ]);

        $this->crud->addFilter([ // add a "simple" filter called Draft
            'type' => 'dropdown',
            'name' => 'transaction_payment_type',
            'label' => 'نوع پرداخت',
        ], [
            Constant::$PAYMENT_TYPE_COMPLETE => 'کامل',
            Constant::$PAYMENT_TYPE_INSTALLMENT => 'قسط',
        ], function ($value) {
            $this->crud->addClause('where', 'transaction_payment_type', $value);
        }
        );

        $this->crud->addFilter([ // add a "simple" filter called Draft
            'type' => 'dropdown',
            'name' => 'success',
            'label' => 'وضعیت پرداخت',
        ], [
            1 => 'موفق',
            0 => 'ناموفق',
        ], function ($value) {
            $this->crud->addClause('where', 'success', $value);
        }
        );

//        $this->crud->enableBulkActions();
//        $this->crud->addBulkDeleteButton();
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
