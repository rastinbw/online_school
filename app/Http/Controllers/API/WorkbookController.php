<?php


namespace App\Http\Controllers\API;
use App\Includes\Constant;
use App\Includes\Helper;
use App\Models\CourseAccess;
use App\Models\TakingTest;
use App\Models\Test;
use App\Models\TestAccess;
use App\Models\TestRecord;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;

class WorkbookController extends BaseController
{
    public function getWorkbook(Request $req){
        $student = $this->check_token($req->input('token'));
        if(!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $test = Test::find($req->input('test_id'));

        $record = TestRecord::where([
            ['student_id', $student->id],
            ['test_id', $test->id],
        ])->first();

        if ($record && !$record->answers)
            return $this->sendResponse(Constant::$NO_ANSWERS, null);

        $answers = json_decode($record->answers);
        $options = json_decode($test->options);
        $factors = json_decode($test->factors);


        return $this->sendResponse(Constant::$SUCCESS, $this->getOptionAnswer($options, "5"));

    }

    private function getOptionAnswer($options, $q_number){
        foreach ($options as $option){
            if ($option->q_number == $q_number)
                return $option->co_number;
        }
    }
}
