<?php namespace App\Http\Controllers\Admin;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\SessionRequest as StoreRequest;
use App\Http\Requests\SessionRequest as UpdateRequest;
use App\Includes\Constant;
use App\Models\Course;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;

class CourseSessionCrudController extends SessionCrudController
{

    public function setup()
    {
        parent::setup();

        // get the user_id parameter
        $course_id = \Route::current()->parameter('course_id');

        // set a different route for the admin panel buttons
        $this->crud->setRoute("admin/course/search/" . $course_id . "/session");

        // show only that user's posts
        $this->crud->addClause('where', 'course_id', $course_id);
//        $this->crud->addClause('where', 'user_id', '=', \Auth::user()->id);

    }

    public function create()
    {
        $this->crud->hasAccessOrFail('create');
        $this->crud->setOperation('create');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->getSaveAction();
        $this->data['fields'] = $this->crud->getCreateFields();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;

        $course_id = \Route::current()->parameter('course_id');
        $course = Course::find($course_id);
        if($course->is_free)
            $this->data['fields']['is_free']['wrapperAttributes']['style'] = 'display:none';

        $this->data['fields']['start_hour']['value'] = $course->start_hour;
        $this->data['fields']['start_min']['value'] = $course->start_min;
        $this->data['fields']['finish_hour']['value'] = $course->finish_hour;
        $this->data['fields']['finish_min']['value'] = $course->finish_min;
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getCreateView(), $this->data);
    }

    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');
        $this->crud->setOperation('update');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->getSaveAction();
        $this->data['fields'] = $this->crud->getUpdateFields($id);
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.edit').' '.$this->crud->entity_name;

        $this->data['id'] = $id;

        $course_id = \Route::current()->parameter('course_id');
        if(Course::find($course_id)->is_free)
            $this->data['fields']['is_free']['wrapperAttributes']['style'] = 'display:none';


        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getEditView(), $this->data);
    }

    public function store(StoreRequest $request)
    {
        $gDate = Verta::getGregorian(
            $request->input('date_year'),
            $request->input('date_month'),
            $request->input('date_day')
        );

        $start_date = new Carbon("{$gDate[0]}-{$gDate[1]}-{$gDate[2]} {$request->input('start_hour')}:{$request->input('start_min')}");
        $finish_date = new Carbon("{$gDate[0]}-{$gDate[1]}-{$gDate[2]} {$request->input('finish_hour')}:{$request->input('finish_min')}");

        // check for tests and sessions overlapping
        $errors = AdminController::checkOverlappedTestsAndSessions(
            $start_date,
            $finish_date,
            $request->input('check_for_tests_overlapping'),
            $request->input('check_for_sessions_overlapping')
        );
        if (sizeof($errors) > 0)
            return back()->withErrors(['custom_fail' => true, 'errors' => $errors]);

        $redirect_location = parent::storeCrud();

        $session = $this->data['entry'];
        $course = Course::find(\Route::current()->parameter('course_id'));

        $session->course_id = $course->id;
        $session->start_date = $start_date;
        $session->finish_date = $finish_date;
        $session->save();

        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        $gDate = Verta::getGregorian(
            $request->input('date_year'),
            $request->input('date_month'),
            $request->input('date_day')
        );

        $start_date = new Carbon("{$gDate[0]}-{$gDate[1]}-{$gDate[2]} {$request->input('start_hour')}:{$request->input('start_min')}");
        $finish_date = new Carbon("{$gDate[0]}-{$gDate[1]}-{$gDate[2]} {$request->input('finish_hour')}:{$request->input('finish_min')}");

        // check for tests and sessions overlapping
        $errors = AdminController::checkOverlappedTestsAndSessions(
            $start_date,
            $finish_date,
            $request->input('check_for_tests_overlapping'),
            $request->input('check_for_sessions_overlapping'),
            null,
            $request->input('id')
        );
        if (sizeof($errors) > 0)
            return back()->withErrors(['custom_fail' => true, 'errors' => $errors]);

        $redirect_location = parent::updateCrud();

        $session = $this->data['entry'];
        $session->start_date = $start_date;
        $session->finish_date = $finish_date;
        $session->save();

        // $this->data['entry'];
        return $redirect_location;
    }

}
