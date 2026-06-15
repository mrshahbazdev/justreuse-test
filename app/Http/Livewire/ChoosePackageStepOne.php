<?php

namespace App\Http\Livewire;

use App\Models\Package;
use App\Models\TblCoupon;
use App\Models\TblPayment;
use App\Models\TblPaymentsMethod;
use App\Models\TblPost;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChoosePackageStepOne extends Component {

    public $list_of_packs, $post_info, $payment_methods;

    public function render() {

        $this->payment_methods = TblPaymentsMethod::where('active', '1')->get()->toArray();

        //check posted id
        $getpostid = request()->post;
        $check_post_package = TblPost::check_post_expired($getpostid);
        
        $check_is_paid = TblPayment::where('post_id', $getpostid)->where('active', '1')->count();
        if ($check_is_paid > 0) {
            if (($check_post_package['expired'] != "Expired") || $check_post_package['expired'] == ""){
                abort(404);
            }            
        }

        $this->post_info = TblPost::where('id', $getpostid)->get();
        if ($this->post_info->count(0) == 0) {
            abort(404);
        }
        //check posted id

        $this->list_of_packs = Package::where('active', '1')->where('lft', '!=', '1')
                        ->where('bulk_ads', '0')
                        ->orderBy('lft', 'asc')
                        ->get()->toArray();
        return view('livewire.choose_package_step.step-one');
    }

    public function get_pack_info() {

        $curr_date = date('Y-m-d');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $coup_code = request()->code;
            $coupon_det = TblCoupon::where('coupon_code', $coup_code)
                            ->where('start_date', '<=', $curr_date)
                            ->where('end_date', '>=', $curr_date)
                            ->get()->toArray();
            if (count($coupon_det) > 0) {

                $user_id = Auth::user()->id;
                $limit_type = $coupon_det[0]['limit_type'];
                $limit_value = $coupon_det[0]['limit_value'];
                $coupon_id = $coupon_det[0]['id'];
                $coupon_type = $coupon_det[0]['type'];
                $coupon_value = $coupon_det[0]['value'];
                $copon_tax = $coupon_det[0]['tax'];

                $array = [
                    'id' => $coupon_id,
                    'type' => $coupon_type,
                    'value' => $coupon_value,
                    'tax' => $copon_tax
                ];
                //individual person coupon usage check
                if ($limit_type == "individual") {
                    $coupon_used_count = TblPayment::where('user_id', $user_id)->where('active', '1')->where('coupon_id', $coupon_id)->get()->count();
                    if ($limit_value == $coupon_used_count) {
                        return response()->json(["result" => "failed", "message" => "Limit reached. <br> Already Coupen used " . $limit_value . " time."]);
                    } else {
                        return response()->json(["result" => "success", "array_data" => $array]);
                    }
                }
                //overall person coupon usage check
                if ($limit_type == "overall") {
                    $coupon_used_count = TblPayment::where('coupon_id', $coupon_id)->where('active', '1')->get()->count();
                    if ($limit_value == $coupon_used_count) {
                        return response()->json(["result" => "failed", "message" => "Coupen Expired"]);
                    } else {
                        return response()->json(["result" => "success", "array_data" => $array]);
                    }
                }
            } else {
                return response()->json(["result" => "failed", "message" => "Invalid Coupen Given"]);
            }
        } else {
            return response()->json(['result' => "failed", "message" => "something wrong"]);
        }
    }

}
