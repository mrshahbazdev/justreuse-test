<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\TblPayment;
use App\Models\TblDefaultCurrency;
use App\Models\TblBulkPackPayment;
use Livewire\WithPagination;


class BulkPaymentsComponent extends Component
{

    use WithPagination;
    public $search;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {

        $bulks = TblBulkPackPayment::where('tbl_bulk_pack_payments.active',1)->join('users', 'tbl_bulk_pack_payments.user_id', '=', 'users.id')
            ->join('packages', 'tbl_bulk_pack_payments.package_id', '=', 'packages.id')
            ->join('tbl_default_currencies', 'tbl_bulk_pack_payments.currency_id', '=', 'tbl_default_currencies.id')
            ->select('tbl_bulk_pack_payments.*', 'users.name','users.email', 'tbl_default_currencies.currency_hex as currency_hex','packages.bulk_limit as ads_limit','packages.name as package_name', 'packages.price as package_price', 'packages.duration')
            ->where('users.name', 'like', '%' . $this->search . '%')
            ->orwhere('tbl_bulk_pack_payments.s_payment_id', 'like', '%' . $this->search . '%')
            ->orderBy('tbl_bulk_pack_payments.created_at', 'desc')
            ->paginate(10);
        return view('livewire.admin.bulk_payments.show', compact('bulks'));
    }
}
