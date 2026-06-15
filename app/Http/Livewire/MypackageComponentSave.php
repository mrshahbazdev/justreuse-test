<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\TblPost;
use App\Models\TblBulkPackPayment;
use App\Models\TblPayment;
use App\Models\Package;
use App\Models\TblPostedAdPackageInfo;
use Illuminate\Support\Facades\Auth;
use Livewire\Request;
use Session;
use Illuminate\Support\Facades\URL;

class MypackageComponentSave extends Component
{

    public function render()
    {
        /*if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $formdata = request()->all();
            $user_id = Auth::user()->id;
            $post_ids = $formdata['postid'];
            $remaining_cnt = TblBulkPackPayment::get_remaining_ads($formdata['package_id']);
            // get package id 
            $get_package_id = TblBulkPackPayment::where('id', $formdata['package_id'])->pluck('package_id')->first();
            // get package validity type 
            $get_package_type = Package::where('id', $get_package_id)->pluck('bulk_type')->first();
            if ((count($post_ids) == $remaining_cnt) || (count($post_ids) <= $remaining_cnt)) {

                foreach ($post_ids as $value) {
                    $package = TblBulkPackPayment::where('id', $formdata['package_id'])
                        ->first();
                    $update_inactive_post = TblPayment::where('post_id', $value)->where('active', 1)->get();
                    if (!empty($update_inactive_post)) {
                        foreach ($update_inactive_post as $inpost) {
                            $inpost->update([
                                "active" => "0"
                            ]);
                        }
                    }
                    $check_post = TblPayment::where('post_id', $value)
                        ->where('active', '1')
                        ->whereDate('end_date', '>=', date("Y-m-d"))->first();
                    if (empty($check_post)) {
                        $curr_date = date('Y-m-d H:i:s');
                        $free_package = Package::where('name', "Free")->where('lft', 1)->pluck('duration')->first();
                        $end_date = date('Y-m-d H:i:s', strtotime($curr_date . "+" . $free_package . " days"));
                        TblPayment::create([
                            's_payment_id' => $package->s_payment_id,
                            'package_id' => $package->package_id,
                            'payment_type' => $package->payment_type,
                            'payment_loc_ref_id' => $package->payment_loc_ref_id,
                            'user_id' => $user_id,
                            'post_id' => $value,
                            'start_date' => ($get_package_type == 1) ? $package->start_date : $curr_date,
                            'end_date' => ($get_package_type == 1) ? $package->end_date : $end_date,
                            'live_days' => $package->live_days,
                            'package_amount' => $package->package_amount,
                            'payment_status' => $package->payment_status,
                            'coupon_id' => $package->coupon_id,
                            'is_bulk' => $formdata['package_id'],
                        ]);

                        $release_from_type_free = TblPostedAdPackageInfo::where('post_id', $value);
                        $release_from_type_free->update([
                            "active" => "0"
                        ]);
                    }
                }
                session()->flash('message', 'Post added to the package successfully!.');
                Session()->flash('class', 'success');
            } else {
                session()->flash('message', 'Max limit reached!.');
                Session()->flash('class', 'error');
            }
            redirect('mypackage');
        }*/

        return view('livewire.mypackage.show', [
            'list' => TblPost::orderBy('id', 'desc')->paginate(10)
        ]);
    }
	
	
	public function assignAds()
	{
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $formdata = request()->all();
            $user_id = Auth::user()->id;
            $post_ids = $formdata['postid'];
            $remaining_cnt = TblBulkPackPayment::get_remaining_ads($formdata['package_id']);
            /* get package id */
            $get_package_id = TblBulkPackPayment::where('id', $formdata['package_id'])->pluck('package_id')->first();
            /* get package validity type */
            $get_package_type = Package::where('id', $get_package_id)->pluck('bulk_type')->first();
            if ((count($post_ids) == $remaining_cnt) || (count($post_ids) <= $remaining_cnt)) {

                foreach ($post_ids as $value) {
                    $package = TblBulkPackPayment::where('id', $formdata['package_id'])
                        ->first();
                    $update_inactive_post = TblPayment::where('post_id', $value)->where('active', 1)->get();
                    if (!empty($update_inactive_post)) {
                        foreach ($update_inactive_post as $inpost) {
                            $inpost->update([
                                "active" => "0"
                            ]);
                        }
                    }
                    $check_post = TblPayment::where('post_id', $value)
                        ->where('active', '1')
                        ->whereDate('end_date', '>=', date("Y-m-d"))->first();
                    if (empty($check_post)) {
                        $curr_date = date('Y-m-d H:i:s');
                        $free_package = Package::where('name', "Free")->where('lft', 1)->pluck('duration')->first();
                        $end_date = date('Y-m-d H:i:s', strtotime($curr_date . "+" . $free_package . " days"));
                        TblPayment::create([
                            's_payment_id' => $package->s_payment_id,
                            'package_id' => $package->package_id,
                            'payment_type' => $package->payment_type,
                            'payment_loc_ref_id' => $package->payment_loc_ref_id,
                            'user_id' => $user_id,
                            'post_id' => $value,
                            'start_date' => ($get_package_type == 1) ? $package->start_date : $curr_date,
                            'end_date' => ($get_package_type == 1) ? $package->end_date : $end_date,
                            'live_days' => $package->live_days,
                            'package_amount' => $package->package_amount,
                            'payment_status' => $package->payment_status,
                            'coupon_id' => $package->coupon_id,
                            'is_bulk' => $formdata['package_id'],
                        ]);

                        $release_from_type_free = TblPostedAdPackageInfo::where('post_id', $value);
                        $release_from_type_free->update([
                            "active" => "0"
                        ]);
                    }
                }
                session()->flash('message', 'Post added to the package successfully!.');
                Session()->flash('class', 'success');
            } else {
                session()->flash('message', 'Max limit reached!.');
                Session()->flash('class', 'error');
            }
            return redirect('mypackage');
        }
	}
	
}
