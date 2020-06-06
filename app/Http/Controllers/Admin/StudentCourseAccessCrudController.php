<?php namespace App\Http\Controllers\Admin;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Controllers\API\SkyRoomController;
use App\Http\Requests\CourseAccessRequest as StoreRequest;
use App\Http\Requests\CourseAccessRequest as UpdateRequest;

class StudentCourseAccessCrudController extends CourseAccessCrudController
{

    public function setup()
    {
        parent::setup();

        // get the user_id parameter
        $student_id = \Route::current()->parameter('student_id');

        // set a different route for the admin panel buttons
        $this->crud->setRoute("admin/student/search/" . $student_id . "/courseaccess");

        // show only that user's posts
        $this->crud->addClause('where', 'student_id', $student_id);

        $this->crud->addFilter([ // add a "simple" filter called Draft
            'type' => 'dropdown',
            'name' => 'has_access',
            'label' => 'دسترسی',
        ], [
            0 => 'ندارد',
            1  => 'دارد',
        ], function ($value) {
            $this->crud->addClause('where', 'has_access', $value);
        }
        );
    }

    public function store(StoreRequest $request)
    {
        $redirect_location = parent::storeCrud();

        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        $redirect_location = parent::updateCrud();

        $access = $this->data['entry'];
        AccessController::changeStudentCourseTestAccesses($access->course_id, $access->student_id, $access->has_access);
        SkyRoomController::changeStudentAccessToRoom($access);

        return $redirect_location;
    }

}
