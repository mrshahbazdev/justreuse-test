<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Package;
use App\Models\TblCoupon;
use Exception;

class PackagesList extends Component
{
    public $data = null;
    public $business_packs = null;
    public $coupons = null;

    public function render()
    {
        $this->data = Package::where('active', '1')->where('bulk_ads', '0')->orderBy('id', 'desc')->get();
        $this->business_packs = Package::where('active', '1')->where('bulk_ads', '1')->orderBy('id', 'desc')->get();
        $this->coupons = TblCoupon::whereNull('deleted_at')->where('end_date', '>=', date("Y-m-d"))->orderBy('id', 'desc')->get();

        add_action("apm_main",function(){
            echo view('livewire.packages-list', ['list' => $this->data, 'coupons' => $this->coupons, 'business_packs' => $this->business_packs])->render();
        },20,1);

        return view('livewire.sample_content');
    }

    // public function render()
    // {
    //     $data = Package::where('active', '1')->where('bulk_ads', '0')->orderBy('id', 'desc')->get();
    //     $business_packs = Package::where('active', '1')->where('bulk_ads', '1')->orderBy('id', 'desc')->get();
    //     $coupons = TblCoupon::whereNull('deleted_at')->where('end_date', '>=', date("Y-m-d"))->orderBy('id', 'desc')->get();
    //     return view('livewire.packages-list', ['list' => $data, 'coupons' => $coupons, 'business_packs' => $business_packs]);
    // }
}
