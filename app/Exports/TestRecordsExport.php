<?php
namespace App\Exports;

use App\Includes\Constant;
use App\Models\Course;
use App\Models\Plan;
use App\Models\Student;
use App\Models\Test;
use App\Models\TestRecord;
use Maatwebsite\Excel\Concerns\FromArray;

class TestRecordsExport implements FromArray
{
    private $test_id;
    private $student_id;

    public function __construct($test_id = null, $student_id = null) {
        $this->test_id = $test_id;
        $this->student_id = $student_id;
    }

    public function array(): array
    {
        if (!($this->test_id || $this->student_id)){
            return null;
        }

        if($this->test_id){
            $headers = [
                'نام',
                'نام خانوادگی',
                'کد ملی',
                'نمره',
            ];

            $records = TestRecord::where('test_id', $this->test_id)->get();
        }else{
            $headers = [
                'کلاس',
                'عنوان',
                'تاریخ',
                'نمره',
            ];

            $records = TestRecord::where('student_id', $this->student_id)->get();
        }


        $data = [$headers];

        foreach ($records as $record) {
            if($this->test_id){
                $student = Student::find($record->student_id);
                $item = [
                    $student->first_name,
                    $student->last_name,
                    $student->national_code,
                    $record->score
                ];
            }else{
                $test = Test::find($record->test_id);
                $course = Course::find($test->course_id);
                $item = [
                    "{$course->title} - {$course->teacher->first_name} {$course->teacher->last_name}",
                    $test->title,
                    $test->exam_date_start,
                    $record->score
                ];
            }

            array_push($data, $item);
        }

        return $data;
    }
}
