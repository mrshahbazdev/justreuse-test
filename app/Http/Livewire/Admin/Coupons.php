<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\TblCoupon;
use App\Models\User;
use Livewire\WithPagination;

class Coupons extends Component
{

    use WithPagination;
    public $coupon_id, $coupon_code, $coupon_title, $value, $start_date, $end_date, $description, $editdata, $type, $limit_type, $limit_value, $tax;
    public $insertMode = false;
    public $updateMode=false;
    public $cnfopen = 0;
    public $search;
   

    public function render()
    {
        $search = !empty($this->search) ? $this->search : "";
        $coupons = TblCoupon::whereNull('deleted_at')->where('coupon_code','Like', '%' . $search . '%')->paginate(10);
        return view('livewire.admin.coupon.compo', [
            'list' => $coupons,
        ]);
        
    }

    // for back button redirect page
    public function back()
    {   
        return redirect()->route('admin/coupon');
    }

    public function create()
    {           
        $this->insertMode=true;
        $this->updateMode = false;
    }
    public function store($formdata){   

         //start check demo user
         $isDemoUser = User::isDemoUser();
         if($isDemoUser["result"]==true)
         {
            $this->insertMode = false;
            $this->updateMode=false;
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'error'); 
            return redirect()->route('admin/coupon');
         }
     //end check demo user

    $allpages = TblCoupon::where('coupon_code',$formdata['coupon_code'])->first();
        if(empty($allpages)){
            if($formdata['type']=="fixed"){
               $offer_amnt = $formdata['fixed_discount'];
            }else{
               $offer_amnt = $formdata['percentage_discount'];
            }
            if($formdata['limit_type']=="all"){
               $limit_value = $formdata['all_value'];
            }else{
               $limit_value = $formdata['individual_value'];
            }
            TblCoupon::create([
            'coupon_code' => $formdata['coupon_code'],
            'coupon_title' => $formdata['coupon_title'],
            'type' => $formdata['type'],
            'value' => $offer_amnt,
            'start_date' => $formdata['start_date'],
            'end_date' => $formdata['end_date'],
            'description' => $formdata['description'],
            'limit_type' => $formdata['limit_type'],
            'limit_value' => $limit_value,
            'tax' => $formdata['tax'],
            ]);
            $this->insertMode = false;
            $this->updateMode=false;
            session()->flash('message', 'Coupon added successfully.');
            Session()->flash('class', 'success'); 
            return redirect()->route('admin/coupon');
        }else{
            session()->flash('message', 'Coupon code already exist!.');
            Session()->flash('class', 'error');  
            return redirect()->route('admin/coupon');       
        }
        
    }

    public function edit($id)
    {
        $this->editdata = TblCoupon::find($id);     
        $this->updateMode=true; 
        $this->insertMode=false;
    }

    public function update($formdata)
    {        

        //start check demo user
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
           $this->insertMode = false;
           $this->updateMode=false;
           session()->flash('message', $isDemoUser["message"]);
           Session()->flash('class', 'error'); 
           return redirect()->route('admin/coupon');
        }
     //end check demo user

        $staticpages = TblCoupon::find($formdata['coupon_id']); 
        if($formdata['type']=="fixed"){
            $offer_amnt = $formdata['fixed_discount'];
        }else{
            $offer_amnt = $formdata['percentage_discount'];
        }
        if($formdata['limit_type']=="all"){
            $limit_value = $formdata['all_value'];
        }else{
            $limit_value = $formdata['individual_value'];
        }
        $staticpages->update([
            'coupon_code' => $formdata['coupon_code'],
            'coupon_title' => $formdata['coupon_title'],
            'type' => $formdata['type'],
            'value' => $offer_amnt,
            'start_date' => $formdata['start_date'],
            'end_date' => $formdata['end_date'],
            'description' => $formdata['description'],   
            'limit_type' => $formdata['limit_type'],
            'limit_value' => $limit_value, 
            'tax' => $formdata['tax'],        
        ]);
        $this->insertMode = false;
        $this->updateMode=false;
        session()->flash('message', 'Updated successfully.');
        Session()->flash('class', 'success'); 
        return redirect()->route('admin/coupon');
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
        }
        else{

        $record1 = TblCoupon::where('id', $id);
        $record1->delete();
        session()->flash('message', 'Deleted successfully.');
        Session()->flash('class', 'success'); 

        }
    }

}