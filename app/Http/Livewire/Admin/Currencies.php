<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\TblCurrency;
use App\Models\TblPost;
use App\Models\Setting;
use App\Models\TblDefaultCurrency;
use App\Models\User;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use DB;

class Currencies extends Component
{

    use WithPagination;

    public $currency_hex, $currency_name, $short_code, $currency_id;
    public $cnfopen = 0;
    public $insertMode = false;
    public $search;

    public function render()
    {
        $search = !empty($this->search) ? $this->search : "";
        $default_currencies = TblDefaultCurrency::orderBy('short_code', 'ASC')->get();
        $currencies = TblCurrency::where('short_code', 'Like', '%' . $search . '%')->where('active', 0)->whereNull('deleted_at')->orderBy('created_at', 'desc')->paginate(10);
        return view('livewire.admin.currencies.show', [
            'currencies' => $currencies,
            'default_currencies' => $default_currencies
        ]);
    }

    private function resetInputFields()
    {
        $this->currency_id = '0';
        $this->short_code = '';
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->insertMode = false;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->insertMode = true;
    }

    public function store()
    {
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'error');

            $this->resetInputFields();
            $this->closeModal();
            $this->insertMode = false;
            return;            
        }

        $get_default = TblDefaultCurrency::where('id', $this->short_code)->first();
        if ($this->currency_id == "0") {
            $this->validate([
                'short_code' => 'required'
            ]);
            $check = TblCurrency::where('short_code', $get_default->short_code)->whereNull('deleted_at')->where('active', 0)->count();
            if ($check > 0) {
                session()->flash('message', 'This currency code already added.');
                Session()->flash('class', 'error');
            } else {
                TblCurrency::create([
                    'currency_hex' => $get_default->currency_hex,
                    'currency_name' => $get_default->currency_name,
                    'short_code' => $get_default->short_code,
                    'default_currency_id' => $get_default->id
                ]);
                session()->flash('message', 'New currency added successfully!');
                Session()->flash('class', 'success');
            }
        }
        $this->resetInputFields();
        $this->closeModal();
        $this->insertMode = false;
    }

    public function deleteReq($id)
    {
        $this->cnfopen = $id;
    }

    public function deleteCan()
    {
        $this->cnfopen = 0;
    }

    public function delete($id)
    {
        $this->cnfopen = 0;
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'error');
            return;
        }
    
        
        $check_post = TblPost::where('currency_id', $id)->whereNull('deleted_at')->count();
        $settings = Setting::get_logos();
        if ($check_post > 0) {
            session()->flash('message', 'Currency exists in the posts');
            Session()->flash('class', 'error');
        } else if ($settings['default_currency'] == $id) {
            session()->flash('message', 'This is the default currency!');
            Session()->flash('class', 'error');
        } else {
            TblCurrency::find($id)->delete();
            session()->flash('message', 'Deleted Successfully.');
            Session()->flash('class', 'success');
        }
    }


}
