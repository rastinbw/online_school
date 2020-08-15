<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class TestAccess extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'test_accesses';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['student_id', 'test_id', 'has_access', 'changeable'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function getCourseTitleAttribute(){
        if ($this->test){
            $course = Course::find($this->test->course_id);
            return $course->title;
        }else
            return "یافت نشد";
    }


    public function getCourseTeacherAttribute(){
        if ($this->test){
            $course = Course::find($this->test->course_id);
            return $course->teacher->list_title;
        }else
            return "یافت نشد";
    }

    public function getTestTitleAttribute(){
        if ($this->test)
            return $this->test->title;
        else
            return "یافت نشد";
    }

    public function getTestHoldingTypeAttribute(){
        if ($this->test)
            return $this->test->exam_holding_type;
        else
            return "یافت نشد";
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function test()
    {
        return $this->belongsTo('App\Models\Test');
    }

    public function student()
    {
        return $this->belongsTo('App\Models\Student');
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
