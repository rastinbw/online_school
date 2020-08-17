<?php


namespace App\Http\Controllers\API;


use App\Includes\Constant;
use App\Models\Category;
use App\Models\Field;
use App\Models\Grade;
use App\Models\Help;
use Illuminate\Http\Request;

class DataController extends BaseController
{
    public function getGradeList(Request $req){
        $grades = Grade::all()->map(function ($grade) {
            return ['id' => $grade->id, 'title' => $grade->title];
        });

        return $this->sendResponse(Constant::$SUCCESS, $grades);
    }

    public function getFieldList(Request $req){
        $fields = Field::all()->map(function ($field) {
            return ['id' => $field->id, 'title' => $field->title];
        });

        return $this->sendResponse(Constant::$SUCCESS, $fields);
    }

    public function getCategoryList(Request $req){
        $categories = Category::orderBy('rgt')->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'description' => $category->description,
                'logo' => $category->logo
            ];
        });
        return $this->sendResponse(Constant::$SUCCESS, $categories);
    }

    public function getHelpList(Request $req){
        $helps = Help::all()->map(function ($help) {
            return [
                'id' => $help->id,
                'title' => $help->title,
                'video_link' => $help->video_link
            ];
        });

        return $this->sendResponse(Constant::$SUCCESS, $helps);
    }


}
