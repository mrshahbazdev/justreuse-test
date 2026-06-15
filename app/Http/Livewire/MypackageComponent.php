<?php

namespace App\Http\Livewire;

use App\Models\TblBulkPackPayment;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Package;
use App\Models\TblPostValue;
use App\Models\TblPostedAdPackageInfo;
use App\Models\TblPayment;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Session;

class MypackageComponent extends Component
{

    use WithPagination;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user_id = Auth::user()->id;
        $list_of_payments = TblBulkPackPayment::join('packages', 'packages.id', '=', 'tbl_bulk_pack_payments.package_id')
            ->join('tbl_default_currencies', 'tbl_default_currencies.id', '=', 'tbl_bulk_pack_payments.currency_id')
            ->where('tbl_bulk_pack_payments.user_id', $user_id)
            ->where('tbl_bulk_pack_payments.active', '1')
            ->orderBy('tbl_bulk_pack_payments.created_at', 'desc')            
            ->select(['tbl_bulk_pack_payments.s_payment_id','tbl_default_currencies.currency_hex as currency_hex', 'tbl_bulk_pack_payments.package_amount', 'tbl_bulk_pack_payments.created_at', 'tbl_bulk_pack_payments.start_date', 'tbl_bulk_pack_payments.end_date', 'tbl_bulk_pack_payments.id', 'packages.name as pack_name', 'packages.bulk_limit'])
            ->paginate(15);
            return view('livewire.mypackage.show', ['list_of_payments' => $list_of_payments]);
    }
}
