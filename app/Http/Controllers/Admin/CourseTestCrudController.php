<?php namespace App\Http\Controllers\Admin;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\TestRequest as StoreRequest;
use App\Http\Requests\TestRequest as UpdateRequest;
use App\Includes\Constant;
use App\Models\Course;
use App\Models\Plan;
use App\Models\Session;
use App\Models\Test;
use App\Models\TestAccess;
use App\Models\TestRecord;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Validator;

class CourseTestCrudController extends TestCrudController
{

    public function setup()
    {
        parent::setup();

        // get the user_id parameter
        $course_id = \Route::current()->parameter('course_id');

        // set a different route for the admin panel buttons
        $this->crud->setRoute("admin/course/search/" . $course_id . "/test");

        // show only that user's posts
        $this->crud->addClause('where', 'course_id', $course_id);

    }

    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->getSaveAction();
        $this->data['fields'] = $this->crud->getUpdateFields($id);
        $this->data['title'] = trans('backpack::crud.edit') . ' ' . $this->crud->entity_name;
        $this->data['id'] = $id;

        $test = Test::find($id);

        $this->data['extra'] = json_encode(
            [
                'old_start_date' => $test->start_date,
                'old_finish_date' => $test->finish_date,
                'old_duration' => $test->exam_duration,
                'old_type' => $test->exam_holding_type,
                'old_factors' => $test->factors,
                'old_options' => $test->options,
                'reached_start_date_time' => $test->start_date <= Carbon::now(),
                'reached_finish_date_time' => $test->finish_date <= Carbon::now(),
            ]
        );

        return view($this->crud->getEditView(), $this->data);
    }

    public function store(StoreRequest $request)
    {
        if (!$request->has('questions_file')) {
            return back()->withErrors(['custom_fail' => true, 'errors' => ['.فایل سوالات را انتخاب کنید']]);
        }

        $errors = array();
        $q_validator = $this->generateQuestionsValidator($request, $errors);
        $a_validator = $this->generateAnswersValidator($request, $errors);

        if ($q_validator != null) {
            if ($q_validator->fails())
                return back()->withErrors(['custom_fail' => true, 'errors' => $errors]);
        }

        if ($a_validator != null) {
            if ($a_validator->fails())
                return back()->withErrors(['custom_fail' => true, 'errors' => $errors]);
        }

        $gDateStart = Verta::getGregorian(
            $request->input('exam_date_start_year'),
            $request->input('exam_date_start_month'),
            $request->input('exam_date_start_day')
        );

        $gDateFinish = Verta::getGregorian(
            $request->input('exam_date_finish_year'),
            $request->input('exam_date_finish_month'),
            $request->input('exam_date_finish_day')
        );

        $start_date = new Carbon("{$gDateStart[0]}-{$gDateStart[1]}-{$gDateStart[2]} {$request->input('exam_date_start_hour')}:{$request->input('exam_date_start_min')}");
        $finish_date = new Carbon("{$gDateFinish[0]}-{$gDateFinish[1]}-{$gDateFinish[2]} {$request->input('exam_date_finish_hour')}:{$request->input('exam_date_finish_min')}");

        // check for tests and sessions overlapping
        if ($request->input('exam_holding_type') == Constant::$SPECIAL_DATE_AND_TIME) {
            $errors = AdminController::checkOverlappedTestsAndSessions(
                $start_date,
                $finish_date,
                $request->input('check_for_tests_overlapping'),
                $request->input('check_for_sessions_overlapping')
            );
            if (sizeof($errors) > 0)
                return back()->withErrors(['custom_fail' => true, 'errors' => $errors]);
        }

        // check factors and options coverage
        $options = json_decode($request->input('options'));
        $factors = json_decode($request->input('factors'));

        if (!$this->checkCoverage($factors, $options))
            return back()->withErrors(['custom_fail' => true, 'errors' => '.گزینه های آزمون با درس های آزمون همخوانی ندارد']);

        $redirect_location = parent::storeCrud();

        // after saving
        $test = $this->data['entry'];
        $test->course_id = \Route::current()->parameter('course_id');
        $test->start_date = $start_date;
        $test->finish_date = $finish_date;
        $test->save();

        // generate test records
        $this->generateTestRecords(\Route::current()->parameter('course_id'), $test->id);

        // generate test accesses
        AccessController::createTestAccessesForCourseTest(\Route::current()->parameter('course_id'), $test->id);

        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        if ($request->has('questions_file')) {
            if ($request->file('questions_file') == null) {
                return back()->withErrors(['custom_fail' => true, 'errors' => ['.فایل سوالات را انتخاب کنید']]);
            }
        }

        $errors = array();
        $q_validator = $this->generateQuestionsValidator($request, $errors);
        $a_validator = $this->generateAnswersValidator($request, $errors);

        if ($q_validator != null) {
            if ($q_validator->fails())
                return back()->withErrors(['custom_fail' => true, 'errors' => $errors]);
        }

        if ($a_validator != null) {
            if ($a_validator->fails())
                return back()->withErrors(['custom_fail' => true, 'errors' => $errors]);
        }

        $gDateStart = Verta::getGregorian(
            $request->input('exam_date_start_year'),
            $request->input('exam_date_start_month'),
            $request->input('exam_date_start_day')
        );

        $gDateFinish = Verta::getGregorian(
            $request->input('exam_date_finish_year'),
            $request->input('exam_date_finish_month'),
            $request->input('exam_date_finish_day')
        );

        $start_date = new Carbon("{$gDateStart[0]}-{$gDateStart[1]}-{$gDateStart[2]} {$request->input('exam_date_start_hour')}:{$request->input('exam_date_start_min')}");
        $finish_date = new Carbon("{$gDateFinish[0]}-{$gDateFinish[1]}-{$gDateFinish[2]} {$request->input('exam_date_finish_hour')}:{$request->input('exam_date_finish_min')}");

        $old_start_date = json_decode($request->input('extra'))->old_start_date;
        $old_finish_date = json_decode($request->input('extra'))->old_finish_date;
        $old_duration = json_decode($request->input('extra'))->old_duration;
        $old_type = json_decode($request->input('extra'))->old_type;
        $old_options = json_decode($request->input('extra'))->old_options;
        $old_factors = json_decode($request->input('extra'))->old_factors;
        $reached_start_date_time = json_decode($request->input('extra'))->reached_start_date_time;
        $reached_finish_date_time = json_decode($request->input('extra'))->reached_finish_date_time;

        if ($reached_start_date_time && $old_type != $request->input('exam_holding_type'))
            return back()->withErrors(['custom_fail' => true, 'errors' => '.این آزمون شروع شده است. قادر به تغییر نحوه برگزاری آن نیستید']);

        if ($old_type == Constant::$FREE_TESTS && $reached_start_date_time && $old_duration != $request->input('exam_duration'))
            return back()->withErrors(['custom_fail' => true, 'errors' => '.این آزمون شناور قبلا شروع شده است. قادر به تغییر مدت زمان آن نیستید']);

        if ($reached_start_date_time && ($old_start_date != $start_date || $old_finish_date != $finish_date)) {
            // check if we are updating a not finished free test finish date
            if ($old_type == Constant::$FREE_TESTS && !$reached_finish_date_time) {
                if ($old_start_date != $start_date)
                    return back()->withErrors(['custom_fail' => true, 'errors' => '.این آزمون شناور قبلا شروع شده است. تنها قادر به تغییر زمان پایان آن هستید']);
            } else {
                return back()->withErrors(['custom_fail' => true, 'errors' => '.این آزمون برگزار شده است و قادر به تغییر زمان شروع و پایان آن نیستید']);
            }
        }

        if ($reached_start_date_time && $old_options != $request->input('options'))
            return back()->withErrors(['custom_fail' => true, 'errors' => '.این آزمون شروع شده است. قادر به تغییر گزینه های آزمون نیستید']);

        if ($reached_start_date_time && $old_factors != $request->input('factors'))
            return back()->withErrors(['custom_fail' => true, 'errors' => '.این آزمون شروع شده است. قادر به تغییر درس های آزمون نیستید']);

        // check for tests overlapping
        if ($request->input('exam_holding_type') == Constant::$SPECIAL_DATE_AND_TIME) {
            $errors = AdminController::checkOverlappedTestsAndSessions(
                $start_date,
                $finish_date,
                $request->input('check_for_tests_overlapping'),
                $request->input('check_for_sessions_overlapping'),
                $request->input('id')
            );
            if (sizeof($errors) > 0)
                return back()->withErrors(['custom_fail' => true, 'errors' => $errors]);
        }

        // check factors and options coverage
        $options = json_decode($request->input('options'));
        $factors = json_decode($request->input('factors'));

        if (!$this->checkCoverage($factors, $options))
            return back()->withErrors(['custom_fail' => true, 'errors' => '.گزینه های آزمون با درس های آزمون همخوانی ندارد']);

        $redirect_location = parent::updateCrud();

        // after saving
        $test = $this->data['entry'];
        $test->start_date = $start_date;
        $test->finish_date = $finish_date;
        $test->save();

        return $redirect_location;
    }

    /**
     * @param $file
     * @param $max_msg
     * @param $ext_msg
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function getValidator($file, $max_msg, $ext_msg)
    {
        if (!$file)
            return null;

        return Validator::make(
            array(
                'file' => $file,
                'extension' => strtolower($file->getClientOriginalExtension()),
            ),
            [
                'file' => 'required|max:6000',
                'extension' => 'required|in:pdf'
            ],
            [
                'file.max' => $max_msg,
                'extension.in' => $ext_msg
            ]
        );
    }

    /**
     * @param UpdateRequest $request
     * @param $errors
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function generateQuestionsValidator(StoreRequest $request, &$errors)
    {
        $q_validator = $this->getValidator(
            $request->file('questions_file'),
            '.حجم فایل سوالات انتخاب شده بیشتر از 5 مگابایت است',
            '.فرمت فایل سوالات انتخاب شده درست نمی باشد'
        );

        if ($q_validator) {
            $q_validator_results = $q_validator->errors()->messages();

            if (key_exists('file', $q_validator_results))
                array_push($errors, $q_validator_results['file'][0]);
            if (key_exists('extension', $q_validator_results))
                array_push($errors, $q_validator_results['extension'][0]);
        }

        return $q_validator;
    }

    /**
     * @param UpdateRequest $request
     * @param $errors
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function generateAnswersValidator(UpdateRequest $request, &$errors)
    {
        $a_validator = $this->getValidator(
            $request->file('answers_file'),
            '.حجم فایل پاسخنامه انتخاب شده بیشتر از 5 مگابایت است',
            '.فرمت فایل پاسخنامه انتخاب شده درست نمی باشد'
        );

        if ($a_validator) {
            $a_validator_results = $a_validator->errors()->messages();

            if (key_exists('file', $a_validator_results))
                array_push($errors, $a_validator_results['file'][0]);
            if (key_exists('extension', $a_validator_results))
                array_push($errors, $a_validator_results['extension'][0]);
        }

        return $a_validator;
    }

    /**
     * @param $course_id
     * @param $test
     */
    public function generateTestRecords($course_id, $test_id)
    {
        $course = Course::find($course_id);
        $student_ids = [];
        foreach ($course->plans()->get() as $plan) {
            foreach ($plan->students as $student) {
                if (!in_array($student->id, $student_ids))
                    array_push($student_ids, $student->id);
            }
        }

        foreach ($student_ids as $id) {
            $record = new TestRecord();
            $record->student_id = $id;
            $record->test_id = $test_id;
            $record->save();
        }
    }

    /**
     * @param $plan_id
     * @param $student_id
     */
    public static function generateStudentPlanTestRecords($plan_id, $student_id)
    {
        foreach (Plan::find($plan_id)->courses()->get() as $course) {
            foreach ($course->tests as $test) {
                $has_record = TestRecord::where([
                    ['student_id', $student_id],
                    ['test_id', $test->id],
                ])->exists();

                if (!$has_record) {
                    $record = new TestRecord();
                    $record->student_id = $student_id;
                    $record->test_id = $test->id;
                    $record->save();
                }
            }
        }
    }

    /**
     * @param $factors
     * @param $options
     * @return bool
     */
    private function checkCoverage($factors, $options): bool
    {
        if ($factors != null) {
            $questions_from_options = [];
            $questions_from_factors = [];

            foreach ($factors as $factor) {
                for ($i = (int)$factor->q_number_from; $i <= (int)$factor->q_number_to; $i++)
                    array_push($questions_from_factors, $i);
            }

            foreach ($options as $option) {
                array_push($questions_from_options, (int)$option->q_number);
            }

            sort($questions_from_options);
            sort($questions_from_factors);

            if ($questions_from_factors != $questions_from_options)
                return false;
        }

        return true;
    }


}
