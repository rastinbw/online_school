<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Course extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'courses';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
        'title','launch_date_year','launch_date_month','launch_date_day','online_day','start_hour',
        'start_min','finish_hour','finish_min','teacher_id','status', 'description', 'tag_id', 'room_id', 'room_url',
        'sessions_number', 'guest_login', 'guest_limit', 'op_login_first', 'max_users', 'is_online', 'is_free'
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function getLaunchDate()
    {
        return $this->launch_date_year . '-' . $this->launch_date_month . '-' . $this->launch_date_day;
    }

    public function getStartTime()
    {
        return $this->start_hour . ':' . $this->start_min;
    }

    public function getFinishTime()
    {
        return $this->finish_hour . ':' . $this->finish_min;
    }

    public function getOnline(){
        if($this->is_online == 1) {
            $src = url(URL::asset('images/check.png'));
            $title = "آنلاین";
        }
        else{
            $src = url(URL::asset('images/cross.png'));
            $title = "آفلاین";
        }


        return "<a title='".$title."'><img style='width:25px;height:25px;' src=".$src."></a>";
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function tag()
    {
        return $this->belongsTo('App\Models\Tag');
    }

    public function teacher()
    {
        return $this->belongsTo('App\Models\Teacher');
    }

    public function sessions()
    {
        return $this->hasMany('App\Models\Session');
    }

    public function tests()
    {
        return $this->hasMany('App\Models\Test');
    }

    public function plans()
    {
        return $this->belongsToMany('App\Models\Plan');
    }

    public function courseAccesses()
    {
        return $this->hasMany('App\Models\CourseAccess');
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
