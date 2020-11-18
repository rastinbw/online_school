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
                'تعداد کل سوالات',
                'تعداد صحیح',
                'تعداد غلط',
                'تعداد نزده',
                'درصد',
                'بیشترین درصد',
                'میانگین درصد',
                'رتبه',
                'تراز',
            ];

            $records = TestRecord::where('test_id', $this->test_id)->get();
        }else{
            $headers = [
                'کلاس',
                'عنوان',
                'تاریخ',
                'تعداد کل سوالات',
                'تعداد صحیح',
                'تعداد غلط',
                'تعداد نزده',
                'درصد',
                'بیشترین درصد',
                'میانگین درصد',
                'رتبه',
                'تراز',
            ];

            $records = TestRecord::where('student_id', $this->student_id)->get();
        }


        $data = [$headers];

        foreach ($records as $record) {
            $w = $this->getWorkbook($record);

            if($this->test_id){
                $student = Student::find($record->student_id);
                if($student){
                    $item = [
                        $student->first_name,
                        $student->last_name,
                        $student->national_code,
                        ($w) ? $w['total']->questions_count : '',
                        ($w) ? $w['total']->correct_count : '',
                        ($w) ? $w['total']->wrong_count : '',
                        ($w) ? $w['total']->empty_count : '',
                        ($w) ? $w['total']->percent : '',
                        ($w) ? $w['total']->max_percent : '',
                        ($w) ? $w['total']->average_percent : '',
                        ($w) ? $w['total']->rank : '',
                        ($w) ? $w['total']->level : '',
                    ];
                }

            }else{
                $test = Test::find($record->test_id);
                if($test){
                    $course = Course::find($test->course_id);
                    $item = [
                        "{$course->title} - {$course->teacher->first_name} {$course->teacher->last_name}",
                        $test->title,
                        $test->exam_date_start,
                        ($w) ? $w['total']->questions_count : '',
                        ($w) ? $w['total']->correct_count : '',
                        ($w) ? $w['total']->wrong_count : '',
                        ($w) ? $w['total']->empty_count : '',
                        ($w) ? $w['total']->percent : '',
                        ($w) ? $w['total']->max_percent : '',
                        ($w) ? $w['total']->average_percent : '',
                        ($w) ? $w['total']->rank : '',
                        ($w) ? $w['total']->level : '',
                    ];
                }
            }

            array_push($data, $item);
        }

        return $data;
    }

    private function getWorkbook($record){
        $w = json_decode($record->workbook);
        if ($record->workbook)
            return (array)$w;
        else
            return null;
    }
}
