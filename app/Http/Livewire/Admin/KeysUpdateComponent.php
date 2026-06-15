<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\ReportType;
use App\Models\TblPaymentsMethod;
use Illuminate\Support\Facades\URL;

class KeysUpdateComponent extends Component
{

    public $keys_info;

    public function render()
    {
        $this->keys_info = TblPaymentsMethod::where('id',request()->id)->get();
		return view('livewire.admin.payment_methods.keysupdate');
    }

}