<?php namespace App\Http\Controllers\Admin;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Controllers\API\TestsController;
use App\Http\Requests\TestAccessRequest as StoreRequest;
use App\Http\Requests\TestAccessRequest as UpdateRequest;
use App\Includes\Constant;
use App\Models\TakingTest;
use App\Models\Test;
use App\Models\TestAccess;
use Carbon\Carbon;

class StudentTestAccessCrudController extends TestAccessCrudController
{
    public function setup()
    {
        parent::setup();

        // get the user_id parameter
        $student_id = \Route::current()->parameter('student_id');

        // set a different route for the admin panel buttons
        $this->crud->setRoute("admin/student/search/" . $student_id . "/testaccess");

        // show only that user's posts
        $this->crud->addClause('where', 'student_id', $student_id);

        $this->crud->addFilter([ // add a "simple" filter called Draft
            'type' => 'dropdown',
            'name' => 'test_holding_type',
            'label' => 'نحوه برگزاری',
        ], [
            Constant::$SPECIAL_DATE_AND_TIME => 'اریخ و زمان مقرر',
            Constant::$FREE_DATE_AND_TYPE => "شناور"
        ], function ($value) {
            $this->crud->addClause('where', 'test_holding_type', $value);
        }
        );

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

        $access = TestAccess::find($id);

        $this->data['extra'] = json_encode(
            [
                'old_has_access' => $access->has_access,
            ]
        );

        return view($this->crud->getEditView(), $this->data);
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
        $test = Test::find($access->test_id);

        $old = json_decode($request->input('extra'))->old_has_access;
        $new = $access->has_access;
        if ($new > $old){
            $taking = TakingTest::where([
                ['student_id', $access->student_id],
                ['test_id', $test->id],
            ])->first();

            if ($taking)
                $taking->delete();
        }

        // if test not reached it's end make it unchangeable
        if (($test->start_date <= Carbon::now() && $test->exam_holding_type == Constant::$SPECIAL_DATE_AND_TIME) ||
            ($test->finish_date <= Carbon::now() && $test->exam_holding_type == Constant::$FREE_TESTS)){
            $access->changeable = 0;
            $access->save();
        }


        return $redirect_location;
    }

}
