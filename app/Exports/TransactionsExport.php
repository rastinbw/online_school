<?php
namespace App\Exports;

use App\Includes\Constant;
use App\Models\Course;
use App\Models\Plan;
use App\Models\Student;
use App\Models\Test;
use App\Models\TestRecord;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromArray;

class TransactionsExport implements FromArray
{
    private $plan_id;

    public function __construct($plan_id = null) {
        $this->plan_id = $plan_id;
    }

    public function array(): array
    {
        $headers = [
            'عنوان',
            'نام',
            'کد ملی',
            'تاریخ',
           // 'مبلغ پرداختی',
        ];

        $transactions = Transaction::where([
            ['plan_id', $this->plan_id],
            ['success', 1],
        ])->get();

        $data = [$headers];

        foreach ($transactions as $transaction) {
            $student = Student::find($transaction->student_id);
            if ($student) {
                $name = $student->first_name . " " . $student->last_name;
                $national_code = $student->national_code;
            }
            else {
                $name = "حذف شده";
                $national_code = "حذف شده";
            }

            $item = [
                $transaction->title,
                $name,
                $national_code,
                $transaction->date_year . "/" . $transaction->date_month . "/" . $transaction->date_day,
               // $transaction->paid_amount
            ];

            array_push($data, $item);
        }

        return $data;
    }
}
