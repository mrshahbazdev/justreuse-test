<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\TblAdminCountry;
use App\Models\TblCurrency;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use DB;


class Countries extends Component
{

    use WithPagination;
    public $country_id, $code, $name, $search;
    private $pagination = 25;
    public $insertMode = false;
    public $updateMode=false;
    public $cnfopen = 0;   
    /**
     *  Livewire Lifecycle Hook
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        //$this->test();
        $currency_list = TblCurrency::get();
        return view('livewire.admin.country.compo', [
            'list' => TblAdminCountry::where('name','like','%'.$this->search.'%')->paginate($this->pagination),
            'currency_list'=>$currency_list
        ]);
        
    }

    public function test()
    {

 

        /////
        		//get current country
		$current_ip = $_SERVER["REMOTE_ADDR"];
        //$url= "http://ipinfo.io/".$current_ip;
		$url= "http://www.geoplugin.net/json.gp?ip=".$current_ip;
        $ch = curl_init();     
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_NOBODY  , true);  // we don't need body
        $output=curl_exec($ch);    
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
dd($httpcode);

		if($hodttpce==200)
		{
			$j  = json_decode($output);
			$country_name = $j->geoplugin_countryName;
			if($country_name!="" || $country_name!=null)
			{
				$get_new =  TblAdminCountry::where('name', $country_name)->first();
				if($get_new->currency_code!=""){
					$default_currency = TblCurrency::where('short_code',$get_new->currency_code)->first();
				}
			}
		}
    }


    public function enable_country()
    {
        $id = request()->id;
        $val = request()->active;     
        $node = TblAdminCountry::find($id); 
        $node->update([
            'active' => $val,
        ]);
        return response()->json(['message'=>'updated successfully']);        
    }
    public function set_country_currency()
    {
        //start check demo user
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            return response()->json(['message' => $isDemoUser["message"]]);
        }
        //end check demo user
        
        $id = request()->country_id;
        $val = request()->currency_code;     
        $node = TblAdminCountry::find($id); 
        $node->update([
            'currency_code' => $val,
        ]);
        return response()->json(['message'=>$node->name.' - currency updated successfully']);        
    }
    

    public function active_multiple_countries()
    {
        //start check demo user
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            return response()->json(['message' => $isDemoUser["message"]]);
        }
        //end check demo user

        $ids = request()->ids;
        $allpageids = request()->allpageids;

        TblAdminCountry::whereIn('id', explode(",", $allpageids))->update(['active' => 0]);

        // active selected ids
        if(!empty($ids)){
            TblAdminCountry::whereIn('id', explode(",", $ids))->update(['active' => 1]);
        }

        return response()->json(['message' => "Update countries successfully.."]);

    }


    public function active_all_countries()
    {
        //start check demo user
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            return response()->json(['message' => $isDemoUser["message"]]);
        }
    //end check demo user

        $value = request()->value;
        // TblAdminCountry::update(['active' => $value]);
        $affected = DB::table('tbl_admin_countries')->update(array('active' => $value));
        $result = ($value == 1) ? "All countries has been activated successfully.." : "All countries has been de-activated successfully.." ;
        return response()->json(['message' => $result]);

    }


}