<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Package;
use App\Models\TblCoupon;
use Exception;

class PackagesList extends Component
{

    public function render()
    {
        $data = Package::where('active', '1')->where('bulk_ads', '0')->orderBy('id', 'desc')->get();
        $business_packs = Package::where('active', '1')->where('bulk_ads', '1')->orderBy('id', 'desc')->get();
        $coupons = TblCoupon::whereNull('deleted_at')->where('end_date', '>=', date("Y-m-d"))->orderBy('id', 'desc')->get();
        return view('livewire.packages-list', ['list' => $data, 'coupons' => $coupons, 'business_packs' => $business_packs]);
    }
}
