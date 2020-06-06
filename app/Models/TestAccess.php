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
        $course = Course::find($this->test->course_id);
        return $course->title;
    }

    public function getCourseTeacherAttribute(){
        $course = Course::find($this->test->course_id);
        return $course->teacher->list_title;
    }

    public function getTestTitleAttribute(){
        return $this->test->title;
    }

    public function getTestHoldingTypeAttribute(){
        return $this->test->exam_holding_type;
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
