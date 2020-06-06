<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'transactions';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['success', 'title', 'issue_tracking_no', 'order_no', 'student_id', 'card_pan_hash', 'card_pan_mask', 'authority',
        'paid_amount', 'plan_id', 'transaction_payment_type', 'payment_type_id', 'date_year', 'date_month', 'date_day', 'installment_id', 'discount_code'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function plan()
    {
        return $this->belongsTo('App\Models\Plan');
    }

    public function installments()
    {
        return $this->hasMany('App\Models\Installment');
    }

    public function student()
    {
        return $this->belongsTo('App\Models\Student');
    }

    public function getDateAttribute()
    {
        return $this->date_year . '-' . $this->date_month . '-' . $this->date_day;
    }

    public function getStudentNationalCodeAttribute()
    {
        return Student::find($this->student_id)->national_code;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
