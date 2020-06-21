<?php


namespace App\Http\Controllers\API;

use App\Includes\Constant;
use App\Includes\Helper;
use App\Models\CourseAccess;
use App\Models\Help;
use App\Models\TakingTest;
use App\Models\Test;
use App\Models\TestAccess;
use App\Models\TestRecord;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;

class WorkbookController extends BaseController
{
    public function getWorkbook(Request $req)
    {
        $student = $this->check_token($req->input('token'));
        if (!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $test = Test::find($req->input('test_id'));

        $record = TestRecord::where([
            ['student_id', $student->id],
            ['test_id', $test->id],
        ])->first();

        if ($record && !$record->workbook)
            return $this->sendResponse(Constant::$NO_WORKBOOK, null);

        $workbook = json_decode($record->workbook, true);
        $this->calculateIndependentValues($workbook, json_decode($test->factors), $test->id);

        $record->workbook = $workbook;
        $record->save();

        return $this->sendResponse(Constant::$SUCCESS, $record->workbook);
    }

    public function saveWorkbook($student, $test, $answers)
    {
        $answers = json_decode($answers);
        $options = json_decode($test->options);
        $factors = json_decode($test->factors);

        $check_table = $this->getCheckTable($options, $factors, $answers);
        $workbook = $this->createWorkbook($factors);
        $this->calculateCounts($workbook, $check_table, $factors);
        $this->calculatePercent($workbook, $factors, $test->has_negative_score);

        $record = TestRecord::where([
            ['student_id', $student->id],
            ['test_id', $test->id],
        ])->first();
        $record->workbook = $workbook;
        $record->save();
    }

    private function getOptionAnswer($options, $q_number)
    {
        foreach ($options as $option) {
            if ($option->q_number == $q_number)
                return $option->co_number;
        }

        return null;
    }

    private function getCheckTable($options, $factors, $answers)
    {
        $answers = (array)$answers;
        $table = [];

        if ($factors) {
            foreach ($factors as $factor) {
                for ($i = (int)$factor->q_number_from; $i <= (int)$factor->q_number_to; $i++) {
                    $answer = $answers[$i];
                    $co_answer = $this->getOptionAnswer($options, $i);
                    if ($answer == $co_answer) {
                        $table[$factor->lesson_title][$i] = Constant::$CORRECT;
                    } elseif ($answer == Constant::$EMPTY) {
                        $table[$factor->lesson_title][$i] = Constant::$EMPTY;
                    } else {
                        $table[$factor->lesson_title][$i] = Constant::$WRONG;
                    }
                }
            }
        } else {
            foreach ($options as $option) {
                $answer = $answers[$option->q_number];
                if ($answer == $option->co_number) {
                    $table[$option->q_number] = Constant::$CORRECT;
                } elseif ($answer == "empty") {
                    $table[$option->q_number] = Constant::$EMPTY;
                } else {
                    $table[$option->q_number] = Constant::$WRONG;
                }
            }
        }

        return $table;
    }

    private function createWorkbook($factors)
    {
        $workbook = [];
        $workbook[Constant::$TOTAL] = [
            Constant::$QUESTIONS_COUNT => null,
            Constant::$CORRECT_COUNT => null,
            Constant::$WRONG_COUNT => null,
            Constant::$EMPTY_COUNT => null
        ];

        if ($factors) {
            foreach ($factors as $factor) {
                $workbook[$factor->lesson_title][Constant::$QUESTIONS_COUNT] = null;
                $workbook[$factor->lesson_title][Constant::$CORRECT_COUNT] = null;
                $workbook[$factor->lesson_title][Constant::$WRONG_COUNT] = null;
                $workbook[$factor->lesson_title][Constant::$EMPTY_COUNT] = null;
                $workbook[$factor->lesson_title][Constant::$PERCENT] = null;
                $workbook[$factor->lesson_title][Constant::$MAX_PERCENT] = null;
                $workbook[$factor->lesson_title][Constant::$AVERAGE_PERCENT] = null;
                $workbook[$factor->lesson_title][Constant::$RANK] = null;
                $workbook[$factor->lesson_title][Constant::$LEVEL] = null;
            }
        } else {
            $workbook[Constant::$TOTAL][Constant::$PERCENT] = null;
            $workbook[Constant::$TOTAL][Constant::$MAX_PERCENT] = null;
            $workbook[Constant::$TOTAL][Constant::$AVERAGE_PERCENT] = null;
            $workbook[Constant::$TOTAL][Constant::$RANK] = null;
            $workbook[Constant::$TOTAL][Constant::$LEVEL] = null;
        }

        return $workbook;
    }

    private function calculateCounts(&$workbook, $check_table, $factors)
    {
        if ($factors) {
            $total_counts = 0;
            $total_correct_counts = 0;
            $total_wrong_counts = 0;
            $total_empty_counts = 0;

            foreach ($factors as $factor) {
                $counts = array_count_values($check_table[$factor->lesson_title]);
                $total_factor_counts = sizeof($check_table[$factor->lesson_title]);
                $wrong_counts = isset($counts[Constant::$WRONG]) ? $counts[Constant::$WRONG] : 0;
                $correct_counts = isset($counts[Constant::$CORRECT]) ? $counts[Constant::$CORRECT] : 0;
                $empty_counts = isset($counts[Constant::$EMPTY]) ? $counts[Constant::$EMPTY] : 0;

                $workbook[$factor->lesson_title][Constant::$QUESTIONS_COUNT] = $total_factor_counts;
                $workbook[$factor->lesson_title][Constant::$WRONG_COUNT] = $wrong_counts;
                $workbook[$factor->lesson_title][Constant::$CORRECT_COUNT] = $correct_counts;
                $workbook[$factor->lesson_title][Constant::$EMPTY_COUNT] = $empty_counts;

                $total_counts += $total_factor_counts;
                $total_correct_counts += $correct_counts;
                $total_wrong_counts += $wrong_counts;
                $total_empty_counts += $empty_counts;
            }

            $workbook[Constant::$TOTAL][Constant::$QUESTIONS_COUNT] = $total_counts;
            $workbook[Constant::$TOTAL][Constant::$WRONG_COUNT] = $total_wrong_counts;
            $workbook[Constant::$TOTAL][Constant::$CORRECT_COUNT] = $total_correct_counts;
            $workbook[Constant::$TOTAL][Constant::$EMPTY_COUNT] = $total_empty_counts;
        } else {
            $counts = array_count_values($check_table);
            $total_counts = sizeof($check_table);
            $total_correct_counts = isset($counts[Constant::$WRONG]) ? $counts[Constant::$WRONG] : 0;
            $total_wrong_counts = isset($counts[Constant::$CORRECT]) ? $counts[Constant::$CORRECT] : 0;
            $total_empty_counts = isset($counts[Constant::$EMPTY]) ? $counts[Constant::$EMPTY] : 0;

            $workbook[Constant::$TOTAL][Constant::$QUESTIONS_COUNT] = $total_counts;
            $workbook[Constant::$TOTAL][Constant::$WRONG_COUNT] = $total_wrong_counts;
            $workbook[Constant::$TOTAL][Constant::$CORRECT_COUNT] = $total_correct_counts;
            $workbook[Constant::$TOTAL][Constant::$EMPTY_COUNT] = $total_empty_counts;
        }
    }

    private function calculatePercent(&$workbook, $factors, $has_negative)
    {
        if ($factors) {
            foreach ($factors as $factor) {
                $workbook[$factor->lesson_title][Constant::$PERCENT] = $this->percentFormula(
                    $workbook[$factor->lesson_title][Constant::$QUESTIONS_COUNT],
                    $workbook[$factor->lesson_title][Constant::$CORRECT_COUNT],
                    $has_negative ? $workbook[$factor->lesson_title][Constant::$WRONG_COUNT] : 0
                );
            }
        } else {
            $workbook[Constant::$TOTAL][Constant::$PERCENT] = $this->percentFormula(
                $workbook[Constant::$TOTAL][Constant::$QUESTIONS_COUNT],
                $workbook[Constant::$TOTAL][Constant::$CORRECT_COUNT],
                $has_negative ? $workbook[Constant::$TOTAL][Constant::$WRONG_COUNT] : 0
            );
        }
    }

    private function calculateIndependentValues(&$workbook, $factors, $test_id)
    {
        $workbooks = $this->getTestWorkbooks($test_id);
        if ($factors) {
            foreach ($factors as $factor) {
                $percents = $this->getWorkbookPercents($workbooks, $factor->lesson_title);
                $ordered = $percents;
                rsort($ordered);

                $workbook[$factor->lesson_title][Constant::$MAX_PERCENT] = $ordered[0];

                $workbook[$factor->lesson_title][Constant::$AVERAGE_PERCENT] = Helper::truncate(
                    array_sum($percents) / count($percents)
                );

                $workbook[$factor->lesson_title][Constant::$RANK] = $this->getRank(
                    $percents,
                    $ordered,
                    $workbook[$factor->lesson_title][Constant::$PERCENT]
                );

                $workbook[$factor->lesson_title][Constant::$LEVEL] = $this->getLevel(
                    $percents,
                    $workbook[$factor->lesson_title][Constant::$PERCENT]
                );
            }
        }else{
            $percents = $this->getWorkbookPercents($workbooks, Constant::$TOTAL);
            $ordered = $percents;
            rsort($ordered);

            $workbook[Constant::$TOTAL][Constant::$MAX_PERCENT] = $ordered[0];

            $workbook[Constant::$TOTAL][Constant::$AVERAGE_PERCENT] = Helper::truncate(
                array_sum($percents) / count($percents)
            );

            $workbook[Constant::$TOTAL][Constant::$RANK] = $this->getRank(
                $percents,
                $ordered,
                $workbook[Constant::$TOTAL][Constant::$PERCENT]
            );

            $workbook[Constant::$TOTAL][Constant::$LEVEL] = $this->getLevel(
                $percents,
                $workbook[Constant::$TOTAL][Constant::$PERCENT]
            );
        }
    }

    private function percentFormula($total, $correct, $wrong)
    {
        return Helper::truncate(($correct * 3 - $wrong) / ($total * 3) * 100);
    }

    private function getTestWorkbooks($test_id): array
    {
        $workbooks = [];
        $records = TestRecord::where('test_id', $test_id)->get();
        foreach ($records as $record) {
            array_push($workbooks, $record->workbook);
        }
        return $workbooks;
    }

    private function getWorkbookPercents($workbooks, $lesson)
    {
        $result = [];
        foreach ($workbooks as $workbook) {
            $w = json_decode($workbook, true);
            $percent = isset($w[$lesson]) ? $w[$lesson][Constant::$PERCENT] : 0;
            array_push($result, $percent);
        }

        return $result;
    }

    private function getRank($list, $ordered_list, $value)
    {
        foreach ($list as $key => $v) {
            foreach ($ordered_list as $ordered_key => $ordered_value) {
                if ($v === $ordered_value) {
                    $key = $ordered_key;
                    break;
                }
            }

            if ($v == $value)
                return ((int) $key + 1);
        }

        return 1;
    }

    private function getLevel($list, $value)
    {
        // average
        $avg = array_sum($list) / count($list);

        // standard deviation
        $num_of_elements = count($list);
        $variance = 0.0;
        foreach($list as $i)
            $variance += pow(($i - $avg), 2);

        $s =(float)sqrt($variance/$num_of_elements);

        // z value
        $z = ($value - $avg) / $s;

        return round(1000 * $z + 5000);
    }

}
