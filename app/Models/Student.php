<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'students';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
        'landing_page_id', 'verification_code','verified','token','first_name','last_name','national_code', 'home_number','phone_number',
        'parent_phone_number','parent_code', 'password','email','address','gender','grade_id','field_id',
        'enrollment_certificate_image','national_card_image', 'region', 'sky_room_id', 'unprotected_password', 'status'
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function getNameAttribute(){
        return "{$this->first_name} {$this->last_name}";
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function landingPage()
    {
        return $this->belongsTo('App\Models\LandingPage');
    }

    public function grade()
    {
        return $this->belongsTo('App\Models\Grade');
    }

    public function field()
    {
        return $this->belongsTo('App\Models\Field');
    }

    public function plans()
    {
        return $this->belongsToMany('App\Models\Plan');
    }

    public function courseAccesses()
    {
        return $this->hasMany('App\Models\CourseAccess');
    }

    public function testAccesses()
    {
        return $this->hasMany('App\Models\TestAccess');
    }

    public function installments()
    {
        return $this->hasMany('App\Models\Installment');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction');
    }

    public function test_records()
    {
        return $this->hasMany('App\Models\TestRecord');
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
