<?php

namespace App\Http\Livewire;

use App\Models\Package;
use App\Models\TblCoupon;
use App\Models\TblPayment;
use App\Models\TblPaymentsMethod;
use App\Models\TblPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class ChooseMultiplePackage extends Component {

    public $list_of_packs, $payment_methods, $list_of_packs_fea;

    public function render() {

        if (!Auth::user()) {
            abort('404');
        }
        $this->payment_methods = TblPaymentsMethod::where('active', '1')->get()->toArray();
        $this->list_of_packs = Package::where('active', '1')->where('lft', '!=', '1')
                        ->where('bulk_ads', '1')
                        ->where('ad_type', 'top_ad')
                        ->orderBy('lft', 'asc')
                        ->get()->toArray();
        $this->list_of_packs_fea = Package::where('active', '1')->where('lft', '!=', '1')
                        ->where('bulk_ads', '1')
                        ->where('ad_type', 'feature_ad')
                        ->orderBy('lft', 'asc')
                        ->get()->toArray();
        return view('livewire.choose_multiple_package.show');
    }

    public function update_bulk_pack_cart() {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $pack_id = request()->pack_id;
            $mode = request()->mode;
            $pack_tbl = Package::where('id', $pack_id)->get()->first();

            $cart = Session::get('cart-selected-bulk-packs');
            if (empty($cart)) {
                $cart[$pack_id] = $pack_tbl; //array("id"=>$pack_id,"name"=>$mode);
                Session::put('cart-selected-bulk-packs', $cart);
            } else {
                if (array_key_exists($pack_id, $cart)) {
                    unset($cart[$pack_id]);
                    Session::put('cart-selected-bulk-packs', $cart);
                } else {
                    $cart[$pack_id] = $pack_tbl; //array("id"=>$pack_id,"name"=>$mode);
                    Session::put('cart-selected-bulk-packs', $cart);
                }
            }


            return response()->json(["result" => "success", "data" => $cart]);
        }
    }

}
