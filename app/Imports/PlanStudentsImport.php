<?php
namespace App\Imports;

use App\Http\Controllers\API\PlansController;
use App\Includes\Helper;
use App\Models\NationalCodePlanPair;
use App\Models\Plan;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToCollection;

class PlanStudentsImport  implements ToCollection
{
    private $plan_id;

    public function __construct($plan_id) {
        $this->plan_id = $plan_id;
    }

    public function collection($rows)
    {
        foreach ($rows as $row)
        {
            //$national_code = Helper::convertPersianToEnglish($row[0]);
            $student = Student::where('national_code', $row[0])->first();
            if ($student){
                $pc = new PlansController();
                $pc->registerInPlan($student, Plan::find($this->plan_id));
            }else{
                if(!NationalCodePlanPair::where([
                        ['national_code', $row[0]],
                        ['plan_id', $this->plan_id]]
                )->exists()){
                    NationalCodePlanPair::create([
                        'plan_id' => $this->plan_id,
                        'national_code' => $row[0],
                    ]);
                }
            }
        }
    }
}
