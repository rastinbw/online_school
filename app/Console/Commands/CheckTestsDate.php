<?php

namespace App\Console\Commands;

use App\Includes\Constant;
use App\Models\Course;
use App\Models\Test;
use App\Models\TestAccess;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckTestsDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'online-school:check_tests_date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command would check tests date and changes students access to them.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tests = Test::where([
            ['start_date', '<=', Carbon::now()->subMinutes(5)],
            ['exam_holding_type', Constant::$SPECIAL_DATE_AND_TIME]
        ])->orWhere([
            ['finish_date', '<=', Carbon::now()],
            ['exam_holding_type', Constant::$FREE_DATE_AND_TYPE]
        ])->get();


        foreach ($tests as $test){
            $course = Course::find($test->course_id);
            foreach ($course->plans as $plan){
                foreach ($plan->students as $student){
                    $access = TestAccess::where([
                        ['student_id', $student->id],
                        ['test_id', $test->id],
                    ])->first();

                    if($access->changeable) {
                        $access->has_access = 0;
                        $access->save();
                    }
                }
            }
        }
    }
}
