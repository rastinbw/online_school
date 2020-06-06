<?php

namespace App\Models;

use App\Includes\Constant;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'tests';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
        'title', 'has_negative_score', 'result_access_type', 'result_access_date_year','result_access_date_month',
        'result_access_date_day', 'result_access_date_hour', 'result_access_date_min', 'qa_access_type', 'qa_access_date_year',
        'qa_access_date_month', 'qa_access_date_day', 'qa_access_date_hour', 'qa_access_date_min', 'exam_holding_type',
        'exam_duration', 'exam_date_start_year', 'exam_date_start_month', 'exam_date_start_day', 'exam_date_start_hour',
        'exam_date_start_min', 'exam_date_finish_year', 'exam_date_finish_month', 'exam_date_finish_day', 'exam_date_finish_hour',
        'exam_date_finish_min', 'questions_file', 'answers_file','options','factors','course_id', 'start_date', 'finish_date'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function setQuestionsFileAttribute($value)
    {
        $attribute_name = "questions_file";
        $disk = "public";
        $destination_path = "files/tests/questions";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);

        // return $this->{$attribute_name}; // uncomment if this is a translatable field
    }

    public function setAnswersFileAttribute($value)
    {
        $attribute_name = "answers_file";
        $disk = "public";
        $destination_path = "files/tests/answers";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);

        // return $this->{$attribute_name}; // uncomment if this is a translatable field
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($obj) {
            \Storage::disk('public')->delete($obj->questions_file);
            \Storage::disk('public')->delete($obj->answer_file);
        });
    }

    public function getResultAccessDateAttribute()
    {
        if ($this->result_access_type == Constant::$SPECIAL_DATE_AND_TIME)
            return $this->result_access_date_year
                   . '-' . $this->result_access_date_month
                   . '-' . $this->result_access_date_day;
        else
            return '-';
    }

    public function getQaAccessDateAttribute()
    {
        if ($this->qa_access_type == Constant::$SPECIAL_DATE_AND_TIME)
            return $this->qa_access_date_year
                . '-' . $this->qa_access_date_month
                . '-' . $this->qa_access_date_day;
        else
            return '-';
    }

    public function getExamDateStartAttribute(){
        return $this->exam_date_start_year
               . '-' . $this->exam_date_start_month
               . '-' . $this->exam_date_start_day;
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function course()
    {
        return $this->belongsTo('App\Models\Course');
    }

    public function test_records()
    {
        return $this->hasMany('App\Models\TestRecord');
    }

    public function testAccesses()
    {
        return $this->hasMany('App\Models\TestAccess');
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
