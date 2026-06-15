<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\TblPayment;
use App\Models\TblDefaultCurrency;
use Livewire\WithPagination;
use App\Models\TblBulkPackPayment;
use Illuminate\Support\Facades\DB;

class PaymentComponent extends Component
{

    use WithPagination;
    public $search;
    public $orders_toggle = ''; // Set a default value

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {

        if ($this->orders_toggle === 'bulk_orders') {
            
            $payment = TblBulkPackPayment::where('tbl_bulk_pack_payments.active',1)->join('users', 'tbl_bulk_pack_payments.user_id', '=', 'users.id')
            ->join('packages', 'tbl_bulk_pack_payments.package_id', '=', 'packages.id')
            ->join('tbl_default_currencies', 'tbl_bulk_pack_payments.currency_id', '=', 'tbl_default_currencies.id')
            ->select('tbl_bulk_pack_payments.*', 'users.name','users.email', 'tbl_default_currencies.currency_hex as currency_hex','packages.bulk_limit as ads_limit','packages.name as package_name', 'packages.price as package_price', 'packages.duration')
            ->where('users.name', 'like', '%' . $this->search . '%')
            ->orwhere('tbl_bulk_pack_payments.s_payment_id', 'like', '%' . $this->search . '%')
            ->orderBy('tbl_bulk_pack_payments.created_at', 'desc')
            ->paginate(10);
          
            // Add conditions for bulk orders if needed
            
        } elseif ($this->orders_toggle === 'payment_orders') {
          
            $payment = TblPayment::join('users', 'tbl_payments.user_id', '=', 'users.id')
        ->join('tbl_posts', 'tbl_payments.post_id', '=', 'tbl_posts.id')
        ->join('packages', 'tbl_payments.package_id', '=', 'packages.id')
        ->leftJoin('tbl_coupons', 'tbl_payments.coupon_id', '=', 'tbl_coupons.id')
        ->select('tbl_payments.*', 'users.name','users.email', 'tbl_posts.title', 'tbl_posts.slug', 'packages.name as package_name', 'packages.price as package_price', 'packages.duration', 'tbl_coupons.coupon_code', 'tbl_coupons.type', 'tbl_coupons.value', 'tbl_coupons.tax')
        ->where(function($query) {
            $query->where('tbl_posts.title', 'like', '%' . $this->search . '%')
                ->orWhere('users.name', 'like', '%' . $this->search . '%')
                ->orWhere('tbl_payments.s_payment_id', 'like', '%' . $this->search . '%');
        })
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('tbl_bulk_pack_payments')
                ->whereRaw('tbl_payments.s_payment_id = tbl_bulk_pack_payments.s_payment_id')
                ->whereRaw('tbl_payments.package_id = tbl_bulk_pack_payments.package_id')
                ->whereRaw('tbl_payments.payment_loc_ref_id = tbl_bulk_pack_payments.payment_loc_ref_id');
        })
        ->orderBy('tbl_payments.created_at', 'desc')
        ->paginate(10);
            
            
        } else{
            $payment = TblPayment::join('users', 'tbl_payments.user_id', '=', 'users.id')
            ->join('tbl_posts', 'tbl_payments.post_id', '=', 'tbl_posts.id')
            ->join('packages', 'tbl_payments.package_id', '=', 'packages.id')
            ->leftJoin('tbl_coupons', 'tbl_payments.coupon_id', '=', 'tbl_coupons.id')
            ->select('tbl_payments.*', 'users.name','users.email', 'tbl_posts.title', 'tbl_posts.slug', 'packages.name as package_name', 'packages.price as package_price', 'packages.duration', 'tbl_coupons.coupon_code', 'tbl_coupons.type', 'tbl_coupons.value', 'tbl_coupons.tax')
            ->where('tbl_posts.title', 'like', '%' . $this->search . '%')
            ->orwhere('users.name', 'like', '%' . $this->search . '%')
            ->orwhere('tbl_payments.s_payment_id', 'like', '%' . $this->search . '%')
            ->orderBy('tbl_payments.created_at', 'desc')
            ->paginate(10);
        }
        
        // $payment = TblPayment::join('users', 'tbl_payments.user_id', '=', 'users.id')
        //     ->join('tbl_posts', 'tbl_payments.post_id', '=', 'tbl_posts.id')
        //     ->join('packages', 'tbl_payments.package_id', '=', 'packages.id')
        //     ->leftJoin('tbl_coupons', 'tbl_payments.coupon_id', '=', 'tbl_coupons.id')
        //     ->select('tbl_payments.*', 'users.name','users.email', 'tbl_posts.title', 'tbl_posts.slug', 'packages.name as package_name', 'packages.price as package_price', 'packages.duration', 'tbl_coupons.coupon_code', 'tbl_coupons.type', 'tbl_coupons.value', 'tbl_coupons.tax')
        //     ->where('tbl_posts.title', 'like', '%' . $this->search . '%')
        //     ->orwhere('users.name', 'like', '%' . $this->search . '%')
        //     ->orwhere('tbl_payments.s_payment_id', 'like', '%' . $this->search . '%')
        //     ->orderBy('tbl_payments.created_at', 'desc')
        //     ->paginate(10);


        return view('livewire.admin.payments.show', compact('payment'));
    }
}
