<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Admin\AccessController;
use App\Includes\Constant;
use App\Models\CourseAccess;
use App\Models\DiscountCode;
use App\Models\Installment;
use App\Models\InstallmentType;
use App\Models\Plan;
use App\Models\Student;
use App\Models\Transaction;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Zarinpal\Laravel\Facade\Zarinpal;

class TransactionController extends BaseController
{
    public function payForPlan($token, $plan_id, $payment_type, $installment_type_id, $discount_code)
    {
//        $student = $this->check_token($token);
//        if (!$student)
//            return $this->sendResponse(Constant::$INVALID_TOKEN, null);
//
//        $installment_type_id = ($installment_type_id != 'null') ? $installment_type_id : null;
//        $discount_code = ($discount_code != 'null') ? $discount_code : null;
//
//        $plan = Plan::find($plan_id);
//        $region_price = $plan->region_price($student->region);
//        $installment_type = InstallmentType::find($installment_type_id);
//        $price = $this->getPrice($region_price, $plan->discount, $discount_code, $installment_type);
//
//        if ($payment_type == Constant::$PAYMENT_TYPE_INSTALLMENT)
//            $amount = $this->calculateInstallments($price, $installment_type)[0];
//        else
//            $amount = $price;
//
//        // creating transaction
//        $time = Verta::now();
//        $transaction = new Transaction();
//        $transaction->order_no = $this->getOrderNo();
//        $transaction->title = ($payment_type == Constant::$PAYMENT_TYPE_INSTALLMENT)
//            ? $plan->title . " (پیش پرداخت)"
//            : $plan->title;
//        $transaction->paid_amount = $amount;
//        $transaction->plan_id = $plan->id;
//        $transaction->date_year = $time->year;
//        $transaction->date_month = $time->month;
//        $transaction->date_day = $time->day;
//        $transaction->transaction_payment_type = $payment_type;
//        $transaction->installment_type_id = ($payment_type == Constant::$PAYMENT_TYPE_INSTALLMENT)
//            ? $installment_type_id : null;
//        $transaction->student_id = $student->id;
//        $transaction->discount_code = $discount_code;
//
//        $results = Zarinpal::request(
//            env('APP_URL') . '/api/plan/pay/done',
//            $amount,
//            $transaction->title,
//            $student->email,
//            $student->phone_number
//        );
//
//        $transaction->authority = $results['Authority'];
//        $transaction->save();

        return Redirect::to("api/payment");
        //return Zarinpal::redirect();
    }

    public function payForPlanIsDone(Request $req)
    {
        $authority = $req->input('Authority');
        $status = $req->input('Status');

        $transaction = Transaction::where('authority', $authority)->first();

        if ($transaction) {
            $result = Zarinpal::verify('OK', $transaction->paid_amount, $authority);
            if ($result['Status'] == 'success') {
                // updating transaction
                $transaction->success = 1;
                $transaction->issue_tracking_no = $result['RefID'];
                $transaction->card_pan_mask = $result['ExtraDetail']['Transaction']['CardPanMask'];
                $transaction->card_pan_hash = $result['ExtraDetail']['Transaction']['CardPanHash'];
                $transaction->save();

                // fetching transaction data
                $plan = Plan::find($transaction->plan_id);
                $student = Student::find($transaction->student_id);
                $discount_code = DiscountCode::where('code', $transaction->discount_code)->first();
                $dc = ($discount_code) ? $discount_code->code : null;

                // calculating installments
                if ($transaction->transaction_payment_type == Constant::$PAYMENT_TYPE_INSTALLMENT) {
                    $installment_type = InstallmentType::find($transaction->installment_type_id);
                    $price = $this->getPrice(
                        $plan->region_price($student->region), $plan->discount, $dc, $installment_type
                    );
                    $amounts = $this->calculateInstallments($price, $installment_type);

                    $counter = 0;
                    $date = Verta::now();
                    foreach ($amounts as $amount) {
                        $installment = new Installment();
                        $installment->student_id = $student->id;
                        $installment->plan_id = $plan->id;
                        $installment->installment_type_id = $transaction->installment_type_id;
                        $installment->transaction_id = ($counter == 0) ? $transaction->id : null;
                        $installment->amount = $amount;

                        $installment->date_year = $date->year;
                        $installment->date_month = $date->month;
                        $installment->date_day = $date->day;

                        $gDate = Verta::getGregorian($date->year, $date->month, $date->day);
                        $installment->date = new Carbon("{$gDate[0]}-{$gDate[1]}-{$gDate[2]}");

                        $date->addDays($installment_type->span);
                        $installment->save();

                        if ($counter == 0) {
                            $transaction->installment_id = $installment->id;
                            $transaction->save();
                        }

                        $counter++;
                    }
                }

                // increasing dc use_count
                if ($discount_code){
                    $discount_code->use_count = $discount_code->use_count + 1;
                    $discount_code->save();
                }

                // register student into plan
                $pc = new PlansController();
                $pc->registerInPlan($student, $plan);
            } else {
                $transaction->success = 0;
                $transaction->save();
            }
        }

        return Redirect::to(env('APP_URL') . '/dashboard/' . $transaction->id . '/transaction');
    }

    public function payForInstallment($token, $installment_id)
    {
        $student = $this->check_token($token);
        if (!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $installment = Installment::find($installment_id);

        if ($installment) {
            $plan = Plan::find($installment->plan_id);
            $student = Student::find($installment->student_id);

            // creating transaction
            $time = Verta::now();
            $transaction = new Transaction();
            $transaction->order_no = $this->getOrderNo();
            $transaction->title = $plan->title . " (پرداخت قسط)";
            $transaction->paid_amount = $installment->amount;
            $transaction->plan_id = $plan->id;
            $transaction->date_year = $time->year;
            $transaction->date_month = $time->month;
            $transaction->date_day = $time->day;
            $transaction->transaction_payment_type = Constant::$PAYMENT_TYPE_INSTALLMENT;
            $transaction->installment_type_id = $installment->installment_type_id;
            $transaction->installment_id = $installment->id;
            $transaction->student_id = $student->id;

            $results = Zarinpal::request(
                env('APP_URL') . '/api/installment/pay/done',
                $transaction->paid_amount,
                $transaction->title,
                $student->email,
                $student->phone_number
            );

            $transaction->authority = $results['Authority'];
            $transaction->save();

            return Zarinpal::redirect();
        } else
            return $this->sendResponse(Constant::$INVALID_INSTALLMENT_ID, null);
    }

    public function payForInstallmentIsDone(Request $req)
    {
        $authority = $req->input('Authority');
        $status = $req->input('Status');

        $transaction = Transaction::where('authority', $authority)->first();

        if ($transaction) {
            $result = Zarinpal::verify('OK', $transaction->paid_amount, $authority);
            if ($result['Status'] == 'success') {
                // updating transaction
                $transaction->success = 1;
                $transaction->issue_tracking_no = $result['RefID'];
                $transaction->card_pan_mask = $result['ExtraDetail']['Transaction']['CardPanMask'];
                $transaction->card_pan_hash = $result['ExtraDetail']['Transaction']['CardPanHash'];
                $transaction->save();

                $student = Student::find($transaction->student_id);
                $installment = Installment::find($transaction->installment_id);

                if (!$installment->transaction_id) {
                    $installment->transaction_id = $transaction->id;
                    $installment->save();
                }

                if ($installment->date <= Carbon::now()) {
                    // check for other not paid installments
                    $exists = Installment::where([
                        ['student_id', $student->id],
                        ['plan_id', $installment->plan_id],
                        ['date', '<=', Carbon::now()],
                        ['transaction_id', null]
                    ])->exists();

                    if (!$exists) {
                        foreach (Plan::find($installment->plan_id)->courses as $course) {
                            $access = CourseAccess::where([
                                ['student_id', $student->id],
                                ['course_id', $course->id],
                            ])->first();

                            $access->has_access = 1;
                            $access->save();
                            AccessController::changeStudentCourseTestAccesses($course->id, $student->id, 1);
                        }
                    }
                }
            } else {
                $transaction->success = 0;
                $transaction->save();
            }
        }

        return Redirect::to(env('APP_URL') . '/dashboard/' . $transaction->id . '/transaction');
    }

    public function getStudentFinancialRecords(Request $req)
    {
        $student = $this->check_token($req->input('token'));
        if (!$student)
            return $this->sendResponse(Constant::$INVALID_TOKEN, null);

        $installments = $this->constructStudentInstallmentsByPlan($student);

        $transactions = $student->transactions()->get()->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'plan_id' => $transaction->plan_id,
                'success' => $transaction->success,
                'title' => $transaction->title,
                'issue_tracking_no' => $transaction->issue_tracking_no,
                'order_no' => $transaction->order_no,
                'paid_amount' => $transaction->paid_amount,
                'transaction_payment_type' => $transaction->transaction_payment_type,
                'date_year' => $transaction->date_year,
                'date_month' => $transaction->date_month,
                'date_day' => $transaction->date_day
            ];
        });

        $data = [
            'transactions' => $transactions,
            'installments' => $installments
        ];

        return $this->sendResponse(Constant::$SUCCESS, $data);
    }

    public function getTransaction($transaction_id){
        $transaction = Transaction::find($transaction_id);
        if (!$transaction)
            return $this->sendResponse(Constant::$INVALID_ID, null);

        $result = [
                'id' => $transaction->id,
                'plan_id' => $transaction->plan_id,
                'success' => $transaction->success,
                'title' => $transaction->title,
                'issue_tracking_no' => $transaction->issue_tracking_no,
                'order_no' => $transaction->order_no,
                'paid_amount' => $transaction->paid_amount,
                'transaction_payment_type' => $transaction->transaction_payment_type,
                'date_year' => $transaction->date_year,
                'date_month' => $transaction->date_month,
                'date_day' => $transaction->date_day
        ];

        return $this->sendResponse(Constant::$SUCCESS, $result);
    }
//    public function registerInPlan(Request $req)
//    {
//        $student = $this->check_token($req->input('token'));
//        if (!$student)
//            return $this->sendResponse(Constant::$INVALID_TOKEN, null);
//
//        $plan = Plan::find($req->input('plan_id'));
//        $payment_type = $req->input('payment_type');
//        $transaction = $this->generateTransaction($req, $payment_type, $student->id);
//
//        if ($transaction->success) {
//            if ($payment_type == Constant::$PAYMENT_TYPE_INSTALLMENT) {
//                $installment_type = InstallmentType::find($req->input('installment_type_id'));
//
//                $amounts = $this->calculateInstallments(
//                    $plan->region_price($student->region),
//                    $plan->discount,
//                    $installment_type
//                );
//
//                $counter = 0;
//                $date = Verta::now();
//                foreach ($amounts as $amount) {
//                    $installment = new Installment();
//                    $installment->student_id = $student->id;
//                    $installment->plan_id = $req->input('plan_id');
//                    $installment->installment_type_id = $req->input('installment_type_id');
//                    $installment->transaction_id = ($counter == 0) ? $transaction->id : null;
//                    $installment->amount = $amount;
//
//                    $installment->date_year = $date->year;
//                    $installment->date_month = $date->month;
//                    $installment->date_day = $date->day;
//
//                    $gDate = Verta::getGregorian($date->year, $date->month, $date->day);
//                    $installment->date = new Carbon("{$gDate[0]}-{$gDate[1]}-{$gDate[2]}");
//
//                    $date->addDays($installment_type->span);
//                    $installment->save();
//
//                    $counter++;
//                }
//
//            }
//
//            $plan->students()->attach([$student->id]);
//
//            // generate accesses
//            $access_list = AccessController::createStudentPlanCourseAccesses($plan->id, $student->id, 1);
//            AccessController::createStudentPlanTestAccesses($plan->id, $student->id, 1);
//            CourseTestCrudController::generateStudentPlanTestRecords($plan->id, $student->id);
//            SkyRoomController::addStudentToRooms($access_list, $student->sky_room_id);
//        }
//
//        return $this->sendResponse(Constant::$SUCCESS, $transaction);
//    }
//
//    /**
//     * @param Request $req (plan_id, payment_success, issue_tracking_no, paid_amount)
//     * @param $type
//     * @param $student_id
//     * @return Transaction
//     */
//    public function generateTransaction(Request $req, $type, $student_id): Transaction
//    {
//        $plan = Plan::find($req->input('plan_id'));
//        $time = Verta::now();
//
//        $transaction = new Transaction();
//        $transaction->issue_tracking_no = $req->input('success') ?
//            $req->input('issue_tracking_no')
//            : null;
//        $transaction->order_no = $this->getOrderNo();
//        $transaction->title = $plan->title;
//        $transaction->success = $req->input('success');
//        $transaction->paid_amount = $req->input('paid_amount');
//        $transaction->plan_id = $plan->id;
//        $transaction->date_year = $time->year;
//        $transaction->date_month = $time->month;
//        $transaction->date_day = $time->day;
//        $transaction->transaction_payment_type = $type;
//        $transaction->student_id = $student_id;
//        $transaction->save();
//        return $transaction;
//    }

    /**
     * @param $region_price
     * @param $plan_discount
     * @param $discount_code
     * @param $installment_type
     * @return false|float|int
     */
    private function getPrice($region_price, $plan_discount, $discount_code, $installment_type)
    {
        if ($discount_code) {
            $price = $this->applyDiscountCode($region_price, $discount_code);
            if ($price == $region_price)
               $price = $this->applyPlanDiscount($region_price, $plan_discount, $installment_type);
        } else
            $price = $this->applyPlanDiscount($region_price, $plan_discount, $installment_type);

        return $price;
    }

    private function calculateInstallments($price, $type)
    {
        $finalPrice = $price + ($price * $type->percentage_of_price_increase / 100);

        $installments = [];
        for ($i = 0; $i < $type->director; $i++) {
            array_push($installments, ceil($finalPrice / $type->director));
        }

        return $installments;
    }

    private function applyDiscountCode($price, $discount_code)
    {
        $amount = $price;
        $code = DiscountCode::where('code', $discount_code)->first();
        if ($code && $code->deadline_date > Carbon::now() && $code->use_limit > $code->use_count) {
            if ($code->type == Constant::$DISCOUNT_TYPE_PERCENT)
                $amount = ceil($amount - ($amount * $code->discount_percent / 100));
            else
                $amount = $amount - $code->discount_price;
        }

        return $amount;
    }

    private function applyPlanDiscount($price, $discount, $installment_type)
    {
        if ($installment_type)
            $price = ($installment_type->discount_disable)
                ? $price
                : $price - ($price * $discount / 100);
        else
            $price = $price - ($price * $discount / 100);

        return $price;
    }

    private function getOrderNo()
    {
        if (Transaction::count() > 0)
            return Transaction::max('order_no') + 1;
        else
            return 111111;
    }

    private function constructStudentInstallmentsByPlan($student)
    {
        $result = [];
        foreach ($student->plans as $plan) {
            $installments = Installment::where([
                ['student_id', $student->id],
                ['plan_id', $plan->id],
            ])->get();

            if (sizeof($installments) > 0){
                $result[$plan->title] = [];

                $count = 1;
                foreach ($installments as $installment) {
                    $title = Plan::find($installment->plan_id)->title;
                    $can_pay = $this->canPay($installment, $installments);

                    array_push($result[$title], [
                        'id' => $installment->id,
                        'number' => $count,
                        'amount' => $installment->amount,
                        'installment_type_id' => $installment->installment_type_id,
                        'transaction_id' => $installment->transaction_id,
                        'is_region_fee_installment' => $installment->is_region_fee_installment,
                        'plan_id' => $plan->id,
                        'year' => $installment->date_year,
                        'month' => $installment->date_month,
                        'day' => $installment->date_day,
                        'can_pay' => $can_pay,
                        'must_pay' => ($installment->date <= Carbon::now() && $can_pay) ? 1 : 0
                    ]);
                    $count++;
                }
            }
        }

        return $result;
    }

    private function canPay($installment, $installments)
    {
        if ($installment->transaction_id)
            return 0;

        if ($installment->is_region_fee_installment == 1)
            return 1;

        $least_date = Carbon::create("9999-12-12");
        foreach ($installments as $i) {
            if ($i->is_region_fee_installment && $i->transaction_id === null)
                return 0;

            if ($i->date < $least_date && $i->transaction_id == null)
                $least_date = $i->date;
        }

        if ($installment->date == $least_date)
            return 1;
        else
            return 0;
    }




}
