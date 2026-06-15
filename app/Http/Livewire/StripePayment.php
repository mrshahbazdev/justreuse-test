<?php

namespace App\Http\Livewire;

use App\Models\TblCategory;
use App\Models\TblCity;
use App\Models\TblFieldsDetail;
use App\Models\TblFieldsOption;
use App\Models\TblPost;
use App\Models\TblPostValue;
use App\Models\TblReview;
use App\Models\User;
use App\Models\TblPostView;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Http\Livewire\DB;
use App\Models\Package;
use App\Models\ReportType;
use App\Models\TblPayment;
use App\Models\TblPostedAdPackageInfo;

class StripePayment extends Component {

    public function render() {
        try {           
            return view('livewire.stripe-payment');
        } catch (Exception $e) {
            abort(404);
        }
    }

}
