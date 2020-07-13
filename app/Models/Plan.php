<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'plans';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
        'title','region_one_price','region_two_price','region_three_price', 'is_free', 'is_full',
        'sub_description','description', 'cover', 'category_id' ,'field_id','grade_id', 'discount', 'slider_plan_id'
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function setCoverAttribute($value)
    {
        $attribute_name = "cover";
        $disk = "public";
        $destination_path = "images/plans";

        // if the image was erased
        if ($value==null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (starts_with($value, 'data:image'))
        {
            // 0. Make the image
            $image = \Image::make($value);

            // 1. Generate a filename.
            if ($this->{$attribute_name} != null)
                $filename = str_after($this->{$attribute_name}, $destination_path . '/');
            else
                $filename = md5($value.time()).'.jpg';

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());
            // 3. Save the path to the database
            $this->attributes[$attribute_name] = $destination_path.'/'.$filename;
        }
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($obj) {
            \Storage::disk('public')->delete($obj->cover);
        });
    }

    public function region_price($region){
        switch ($region){
            case 1:
                return $this->region_one_price;
                break;
            case 2:
                return $this->region_two_price;
                break;
            case 3:
                return $this->region_three_price;
                break;
            default:
                return $this->region_two_price;
        }
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function courses()
    {
        return $this->belongsToMany('App\Models\Course');
    }

    public function sliderPlan()
    {
        return $this->hasOne('App\Models\SliderPlan');
    }

    public function grade()
    {
        return $this->belongsTo('App\Models\Grade');
    }

    public function field()
    {
        return $this->belongsTo('App\Models\Field');
    }

    public function students()
    {
        return$this->belongsToMany('App\Models\Student');
    }

    public function installments()
    {
        return $this->hasMany('App\Models\Installment');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction');
    }

    public function installment_types()
    {
        return $this->belongsToMany('App\Models\InstallmentType');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function messages()
    {
        return $this->hasMany('App\Models\Message');
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
