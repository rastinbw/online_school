<?php
namespace App\Exports;

use App\Includes\Constant;
use App\Models\Course;
use App\Models\InstallmentType;
use App\Models\Plan;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromArray;

class InstallmentsExport implements FromArray
{
    private $student_id;

    public function __construct($student_id = null) {
        $this->student_id = $student_id;
    }

    public function array(): array
    {
        $headers = [
            'طرح',
            'مدل قسطی',
            'مقدار',
            'تاریخ',
            'وضعیت',
        ];

        $data = [$headers];
        $installments = Student::find($this->student_id)->installments;

        foreach ($installments as $installment) {
            $item = [
                Plan::find($installment->plan_id)->title,
                $installment->installment_type_id ?
                    InstallmentType::find($installment->installment_type_id)->title :
                    '-',
                $installment->amount,
                "{$installment->date_year}-{$installment->date_month}-{$installment->date_day}",
                ($installment->transaction_id) ? "پرداخت شده" : "بدهکار"
            ];

            array_push($data, $item);
        }

        return $data;
    }
}
