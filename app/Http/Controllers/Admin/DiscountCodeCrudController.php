<?php

namespace App\Http\Controllers\Admin;

use App\Includes\Constant;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\DiscountCodeRequest as StoreRequest;
use App\Http\Requests\DiscountCodeRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;

/**
 * Class DiscountCodeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class DiscountCodeCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\DiscountCode');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/discountCode');
        $this->crud->setEntityNameStrings('کد تخفیف', 'کد های تخفیف');
        $time = Verta::now(); //1396-02-02 15:32:08

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->addColumns([
            [
                'name' => 'code',
                'label' => 'کد تخفیف',
            ],
            [
                'name' => 'discount_percent',
                'label' => 'درصد تخفیف',
            ],
            [
                'name' => 'discount_price',
                'label' => 'مبلغ تخفیف',
            ],
            [
                'name' => "deadline_date_string",
                'label' => "تاریخ اتمام اعتبار", // Table column heading
            ],
            [
                'name' => 'use_limit',
                'label' => 'تعداد محدودیت استفاده',
            ],
            [
                'name' => 'use_count',
                'label' => 'تعداد استفاده شده',
            ],
        ]);

        $this->crud->addFields([
            [
                'name' => 'code',
                'label' => 'کد',
                'default' => $this->getToken(6),
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                ],
            ],
            [
                'label' => 'نوع کاهش قیمت',
                'name' => 'type',
                'type' => 'toggle',
                'inline' => true,
                'options' => [
                    Constant::$DISCOUNT_TYPE_PERCENT => 'درصد',
                    Constant::$DISCOUNT_TYPE_PRICE => 'مبلغ'
                ],
                'hide_when' => [
                    Constant::$DISCOUNT_TYPE_PERCENT =>
                        ['discount_price'],
                    Constant::$DISCOUNT_TYPE_PRICE =>
                        ['discount_percent']
                ],
                'default' => Constant::$FREE_DATE_AND_TYPE,
            ],
            [
                'name' => 'discount_percent',
                'label' => 'درصد تخفیف',
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
                'name' => 'discount_price',
                'label' => 'مبلغ تخفیف',
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
                'name' => 'use_limit',
                'label' => 'تعداد محدودیت استفاده',
                'type' => 'number',
                'default' => 0,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:10px',
                    'dir' => 'rtl',
                ],
            ],

            // DATE
            [
                'name' => 'deadline_date_title',
                'label' => 'تاریخ اتمام اعتبار',
                'type' => 'title',
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'style' => 'margin-top:10px',
                    'dir' => 'rtl',
                ],
            ],
            [
                'name' => 'deadline_date_day',
                'label' => 'روز',
                'type' => 'number',
                'default' => $time->day,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'col-md-4',
                ],
            ],
            [
                'name' => 'deadline_date_month',
                'label' => 'ماه',
                'type' => 'number',
                'default' => $time->month,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'col-md-4',
                ],
            ],
            [
                'name' => 'deadline_date_year',
                'label' => 'سال',
                'type' => 'number',
                'default' => $time->year,
                'attributes' => [
                    'dir' => 'rtl',
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl',
                    'class' => 'col-md-4',
                ],
            ],
            [
                'label' => "طرح ها",
                'type' => 'select2_multiple',
                'name' => 'plans', // the method that defines the relationship in your Model
                'entity' => 'plans', // the method that defines the  relationship in your Model
                'attribute' => 'title', // foreign key attribute that is shown to user
                'model' => "App\Models\Plan", // foreign key model
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
        ], 'update/create/both');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        $discount = $this->data['entry'];

        $gDateDeadline = Verta::getGregorian(
            $request->input('deadline_date_year'),
            $request->input('deadline_date_month'),
            $request->input('deadline_date_day')
        );

        $discount->deadline_date = new Carbon("{$gDateDeadline[0]}-{$gDateDeadline[1]}-{$gDateDeadline[2]}");
        // $discount->code = $this->getToken(6);
        $discount->save();


        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        $discount = $this->data['entry'];

        $gDateDeadline = Verta::getGregorian(
            $request->input('deadline_date_year'),
            $request->input('deadline_date_month'),
            $request->input('deadline_date_day')
        );

        $discount->deadline_date = new Carbon("{$gDateDeadline[0]}-{$gDateDeadline[1]}-{$gDateDeadline[2]}");
        $discount->save();
        return $redirect_location;
    }

    function crypto_rand_secure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }

    function getToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max-1)];
        }

        return $token;
    }
}
