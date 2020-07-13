<?php namespace App\Http\Controllers\Admin;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\MessageRequest as StoreRequest;
use App\Http\Requests\MessageRequest as UpdateRequest;
use App\Includes\HttpRequest;
use App\Models\Course;
use App\Models\Plan;
use App\Models\SmsTemplate;

class PlanMessageCrudController extends MessageCrudController
{

    public function setup()
    {
        parent::setup();

        // get the user_id parameter
        $plan_id = \Route::current()->parameter('plan_id');

        // set a different route for the admin panel buttons
        $this->crud->setRoute("admin/plan/search/" . $plan_id . "/message");

        // show only that user's posts
        $this->crud->addClause('where', 'plan_id', $plan_id);

    }

    public function store(StoreRequest $request)
    {
        $template = SmsTemplate::find($request->input('sms_template_id'));
        $plan = Plan::find(\Route::current()->parameter('plan_id'));
        $params = json_decode($request->input('params'));

        $tokens = "";
        foreach ($params as $param){
            $param = (array)$param;
            $val = preg_replace("/\s+/", "", $param['value']);
            $num = $param['number'] == 1 ? '' : $param['number'];
            $tokens = $tokens . "&token{$num}={$val}";
        }

        $students = $plan->students()->get(['phone_number']);
        foreach ($students as $student){
            $url = 'https://api.kavenegar.com/v1/' .
                env('SMS_API_KEY') .
                '/verify/lookup.json?receptor=' .
                $student->phone_number .
                '&template=' .
                $template->name .
                $tokens;

            $http = new HttpRequest($url);
            $http->get();
        }

        $redirect_location = parent::storeCrud();

        $message = $this->data['entry'];

        $message->plan_id = \Route::current()->parameter('plan_id');
        $message->save();

        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        $redirect_location = parent::updateCrud();

        return $redirect_location;
    }

}
