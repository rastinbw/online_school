<?php
namespace App\Imports;

use App\Models\NationalCodePlanPair;
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
