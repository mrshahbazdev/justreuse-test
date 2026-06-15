<?php
namespace App\Http\Livewire;
use Livewire\Component;
use App\Models\Package;
use App\Models\TblBulkPackPayment;
use App\Models\TblPayment;
use App\Models\TblPostedAdPackageInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ThankyouComponent extends Component
{
    public function render()
    {
        return view('livewire.thankyou_webview.thankyou-component');
    }

}
