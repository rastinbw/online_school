<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use PhpOffice\PhpSpreadsheet\Reader\Html;

class Session extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'sessions';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
        'title','date_year','date_month','date_day','start_hour','start_min','finish_hour','finish_min', 'video_download_link',
        'course_id','status','video_link','notes','description', 'is_online', 'held', 'is_free', 'start_date', 'finish_date'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function setNotesAttribute($value)
    {
        $attribute_name = "notes";
        $disk = "public";
        $destination_path = "files/sessions";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);

        // return $this->{$attribute_name}; // uncomment if this is a translatable field
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($obj) {
            \Storage::disk('public')->delete($obj->notes);
        });
    }

    public function getDate()
    {
        return $this->date_year . '-' . $this->date_month . '-' . $this->date_day;
    }

    public function getStartTime()
    {
        return $this->start_hour . ':' . $this->start_min;
    }

    public function getFinishTime()
    {
        return $this->finish_hour . ':' . $this->finish_min;
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

    public function changeOnline()
    {
        if($this->is_online == 1) {
            $src = url(URL::asset('images/check.png'));
            $title = "آنلاین";
        }
        else{
            $src = url(URL::asset('images/cross.png'));
            $title = "آفلاین";
        }

        return "<a title='".$title."' href='changeonline/".urlencode($this->id)."'><img style='width:25px;height:25px;' src=".$src."></a>";
    }

    public function changeHeld()
    {
        if($this->held == 1) {
            $src = url(URL::asset('images/check.png'));
            $title = "برگزار شده";
        }
        else{
            $src = url(URL::asset('images/cross.png'));
            $title = "برگزار نشده";
        }

        return "<a title='".$title."' href='changeheld/".urlencode($this->id)."'><img style='width:25px;height:25px;' src=".$src."></a>";
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
