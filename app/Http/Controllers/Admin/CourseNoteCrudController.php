<?php namespace App\Http\Controllers\Admin;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\NoteRequest as StoreRequest;
use App\Http\Requests\NoteRequest as UpdateRequest;
use App\Includes\Constant;
use App\Models\Course;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;

class CourseNoteCrudController extends NoteCrudController
{

    public function setup()
    {
        parent::setup();

        // get the user_id parameter
        $course_id = \Route::current()->parameter('course_id');

        // set a different route for the admin panel buttons
        $this->crud->setRoute("admin/course/search/" . $course_id . "/note");

        // show only that user's posts
        $this->crud->addClause('where', 'course_id', $course_id);
//        $this->crud->addClause('where', 'user_id', '=', \Auth::user()->id);

    }

    public function store(StoreRequest $request)
    {
        $redirect_location = parent::storeCrud();

        $note = $this->data['entry'];
        $course = Course::find(\Route::current()->parameter('course_id'));
        $note->course_id = $course->id;
        $note->save();

        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        $redirect_location = parent::updateCrud();

        // $this->data['entry'];
        return $redirect_location;
    }

}
