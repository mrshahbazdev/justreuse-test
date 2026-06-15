<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    use HasFactory;

    protected $table = 'business_profiles';

    protected $casts = [
        'hours' => 'string',
    ];

    protected $fillable = ['brand_name','brand_logo','brand_title','contact_number',
    'description','city','address','is_company','hours','verifcation_id','about_us'];



    public static function country($country_short,$country_long){
       // dd($country_short,$country_long);

        $tbl_country = TblCountry::where('code', $country_short)->where('name', $country_long)->get();
        //dd($tbl_country);

        if ($tbl_country->count() == 0) {
            $country_id = TblCountry::create([
                'code' => $country_short,
                'name' => $country_long
            ])->id;
        } else {
            $country_id = $tbl_country[0]->id;
            //dd($country_id);
        }
return $country_id;
}
public static function state($country_id,$state_short,$state_long){

    if($state_short == null && $state_long == null){
        return;
    }
else{
    $tbl_state = TblState::where('country_id', $country_id)->where('code', $state_short)->where('name', $state_long)->get();
    if ($tbl_state->count() == 0) {
        $state_id = TblState::create([
            'country_id' => $country_id,
            'code' => $state_short,
            'name' => $state_long
        ])->id;
    } else {
        $state_id = $tbl_state[0]->id;
    }
}
return $state_id;
}

public static function city($country_id,$state_id,$main_city_name,$city_lat,$city_lag,$city_name){
    if($main_city_name == null){
        return;
    }
    else{

    $tbl_cities = TblCity::where('country_id', $country_id)->where('state_id', $state_id)->where('name', $main_city_name)->where('locality', $city_name)->get();
    if ($tbl_cities->count() == 0) {
        $city_id = TblCity::create([
            'country_id' => $country_id,
            'state_id' => $state_id,
            'locality' => $city_name,
            'name' => $main_city_name,
            'latitude' => $city_lat,
            'logitude' => $city_lag
        ])->id;
    } else {
        $city_id = $tbl_cities[0]->id;
    }
}
return $city_id;
}

public static function location_details($local_area,$district,$state,$country){

if($local_area != $district){
if ($local_area !=''&& $district !='' && $state != '' && $country !='' ) {
    $data = BusinessProfile::select("business_profiles.id as id", "business_profiles.brand_name as brand_name", "business_profiles.brand_title as brand_title", "business_profiles.brand_logo as brand_logo", "business_profiles.description as description", "business_profiles.address as address", "business_profiles.city as city", "business_profiles.verifcation_id as verifcation_id","tbl_cities.name as district", "tbl_cities.locality as locality", "tbl_states.name as state", "tbl_countries.name as country")->join("tbl_cities", "tbl_cities.id", "=", "business_profiles.city")
        ->join("tbl_states", "tbl_states.id", "=", "tbl_cities.state_id")
        ->join("tbl_countries", "tbl_countries.id", "=", "tbl_states.country_id")
        ->where('tbl_cities.locality', 'like', '%' . $local_area . '%')
        ->where('tbl_cities.name', 'like', '%' . $district . '%')
        ->where('tbl_states.name', 'like', '%' . $state . '%')
        ->where('tbl_countries.name', 'like', '%' . $country . '%')
        ->get();
        //dd($data);
}
}
else{
   
    $data = BusinessProfile::select("business_profiles.id as id", "business_profiles.brand_name as brand_name", "business_profiles.brand_title as brand_title", "business_profiles.brand_logo as brand_logo", "business_profiles.description as description", "business_profiles.address as address", "business_profiles.city as city","business_profiles.verifcation_id as verifcation_id", "tbl_cities.name as district", "tbl_cities.locality as locality", "tbl_states.name as state", "tbl_countries.name as country")->join("tbl_cities", "tbl_cities.id", "=", "business_profiles.city")
    ->join("tbl_states", "tbl_states.id", "=", "tbl_cities.state_id")
    ->join("tbl_countries", "tbl_countries.id", "=", "tbl_states.country_id")
    ->where('tbl_cities.name', 'like', '%' . $district . '%')
    ->where('tbl_states.name', 'like', '%' . $state . '%')
    ->where('tbl_countries.name', 'like', '%' . $country . '%')
    ->get();
}
return $data;

}
public static function location_basedetails($states,$countries){
$data='';

    if($states != '' && $countries !=''){

   $data = BusinessProfile::select("business_profiles.id as id", "business_profiles.brand_name as brand_name", "business_profiles.brand_title as brand_title", "business_profiles.brand_logo as brand_logo", "business_profiles.description as description", "business_profiles.address as address", "business_profiles.city as city", "business_profiles.verifcation_id as verifcation_id","tbl_cities.name as district", "tbl_cities.locality as locality", "tbl_states.name as state", "tbl_countries.name as country")->join("tbl_cities", "tbl_cities.id", "=", "business_profiles.city")
        ->join("tbl_states", "tbl_states.id", "=", "tbl_cities.state_id")
        ->join("tbl_countries", "tbl_countries.id", "=", "tbl_states.country_id")
        ->where('tbl_states.name', 'like', '%' . $states->name . '%')
         ->where('tbl_countries.name', 'like', '%' . $countries->name . '%')
        ->get();
       
    }
   
    elseif($countries != null){

        $data = BusinessProfile::select("business_profiles.id as id", "business_profiles.brand_name as brand_name", "business_profiles.brand_title as brand_title", "business_profiles.brand_logo as brand_logo", "business_profiles.description as description", "business_profiles.address as address", "business_profiles.city as city", "business_profiles.verifcation_id as verifcation_id","tbl_cities.name as district", "tbl_cities.locality as locality", "tbl_states.name as state", "tbl_countries.name as country")->join("tbl_cities", "tbl_cities.id", "=", "business_profiles.city")
        ->join("tbl_states", "tbl_states.id", "=", "tbl_cities.state_id")
        ->join("tbl_countries", "tbl_countries.id", "=", "tbl_states.country_id")
        ->where('tbl_countries.name', 'like', '%' . $countries->name . '%')
        ->get();
    }
   
    return $data;
}

public static function query_details($shop_search,$lc){
   // dd($shop_search,$lc);
   
if(($shop_search != '') && ($lc != '')){
   
  $data = BusinessProfile::select("business_profiles.id as id", "business_profiles.brand_name as brand_name", "business_profiles.brand_title as brand_title", "business_profiles.brand_logo as brand_logo", "business_profiles.description as description", "business_profiles.address as address", "business_profiles.city as city", "business_profiles.verifcation_id as verifcation_id","tbl_cities.name as district", "tbl_cities.locality as locality", "tbl_states.name as state", "tbl_countries.name as country")->join("tbl_cities", "tbl_cities.id", "=", "business_profiles.city")
    ->join("tbl_states", "tbl_states.id", "=", "tbl_cities.state_id")
    ->join("tbl_countries", "tbl_countries.id", "=", "tbl_states.country_id")
    ->where('brand_title', 'like', '%' . $shop_search . '%')
    ->orwhere('description', 'like', '%' . $shop_search . '%')
    ->where('tbl_cities.locality', 'like', '%' . $lc . '%')
    ->where('tbl_cities.name', 'like', '%' . $lc . '%')
    ->where('tbl_states.name', 'like', '%' . $lc . '%')
    ->where('tbl_countries.name', 'like', '%' . $lc . '%')
    ->get();
  

 
}
    

 return $data;
}
public static function query_only($q,$countries,$states){

if($countries->name !='' && $states->name !=''){
 
   if ($q != ''){

    // $data= BusinessProfile::where('description', 'like', '%' . $q . '%')->orWhere('brand_title', 'like', '%' . $q . '%')->paginate(5)->setpath('');
    $data = BusinessProfile::select("business_profiles.id as id", "business_profiles.brand_name as brand_name", "business_profiles.brand_title as brand_title", "business_profiles.brand_logo as brand_logo", "business_profiles.description as description", "business_profiles.address as address", "business_profiles.city as city", "business_profiles.verifcation_id as verifcation_id","tbl_cities.name as district", "tbl_cities.locality as locality", "tbl_states.name as state", "tbl_countries.name as country")
    ->join("tbl_cities", "tbl_cities.id", "=", "business_profiles.city")
    ->join("tbl_states", "tbl_states.id", "=", "tbl_cities.state_id")
    ->join("tbl_countries", "tbl_countries.id", "=", "tbl_states.country_id")
    ->where('brand_title', 'like', '%' . $q . '%')
    ->orwhere('description', 'like', '%' . $q . '%')
    ->where('tbl_states.name', 'like', '%' . $states->name . '%')
    ->where('tbl_countries.name', 'like', '%' . $countries->name . '%')
    ->get();
    }

    }
    
//     else{
//         //sjj');
// $data = BusinessProfile::select("business_profiles.id as id", "business_profiles.brand_name as brand_name", "business_profiles.brand_title as brand_title", "business_profiles.brand_logo as brand_logo", "business_profiles.description as description", "business_profiles.address as address", "business_profiles.city as city", "business_profiles.verifcation_id as verifcation_id","tbl_cities.name as district", "tbl_cities.locality as locality", "tbl_states.name as state", "tbl_countries.name as country")->join("tbl_cities", "tbl_cities.id", "=", "business_profiles.city")
//         ->join("tbl_states", "tbl_states.id", "=", "tbl_cities.state_id")
//         ->join("tbl_countries", "tbl_countries.id", "=", "tbl_states.country_id")
//         ->where('brand_title', 'like', '%' . $q . '%')
//         ->where('description', 'like', '%' . $q . '%')
//         ->get();
//     }
    return $data;
    }



public static function get_details($lc){
  
    $locat = explode(",", $lc);

        if ($lc != '') {
            
            foreach ($locat as $location) {
                //$data= BusinessProfile::where('is_company', 'Yes')->where('')->get();
                $datas[] = BusinessProfile::select("business_profiles.id as id", "business_profiles.brand_name as brand_name", "business_profiles.brand_title as brand_title", "business_profiles.brand_logo as brand_logo", "business_profiles.description as description", "business_profiles.address as address", "business_profiles.city as city","business_profiles.verifcation_id as verifcation_id", "tbl_cities.name as district", "tbl_cities.locality as locality", "tbl_states.name as state", "tbl_countries.name as country")->join("tbl_cities", "tbl_cities.id", "=", "business_profiles.city")
                    ->join("tbl_states", "tbl_states.id", "=", "tbl_cities.state_id")
                    ->join("tbl_countries", "tbl_countries.id", "=", "tbl_states.country_id")
                    ->orwhere('tbl_cities.locality', 'like', '%' . $location . '%')
                    ->orwhere('tbl_cities.name', 'like', '%' . $location . '%')
                    ->orwhere('tbl_states.name', 'like', '%' . $location . '%')
                    ->orwhere('tbl_countries.name', 'like', '%' . $location . '%')
                    ->get();
            }
         
            foreach ($datas as $key => $innerArray) {
                foreach ($innerArray as $innerValue) {
                    if (!empty($innerValue)) {
                        continue 2;
                    }
                }
                unset($datas[$key]);
            }
      
            $data='';
        
            if (count($datas) > 0) {

                foreach ($datas as $dat) {
             
                    $data = $dat;
                    //    foreach($dat as $t){

                    //     $data=$t;
                    //    }
                    //  dd($data);
                    return $data;
                }
            }
            else{
                return $data;

               // return view('livewire.shoponlist')->with('error', 'No Data Found!');
            }
        }

        else {
            $data = BusinessProfile::where('is_company', 'Yes')->get();
            return $data;
        }
}

public static function query_country($q,$countries){

    if($countries->name != ''){
   
        if ($q != ''){
        $data = BusinessProfile::select("business_profiles.id as id", "business_profiles.brand_name as brand_name", "business_profiles.brand_title as brand_title", "business_profiles.brand_logo as brand_logo", "business_profiles.description as description", "business_profiles.address as address", "business_profiles.city as city", "business_profiles.verifcation_id as verifcation_id","tbl_cities.name as district", "tbl_cities.locality as locality", "tbl_states.name as state", "tbl_countries.name as country")->join("tbl_cities", "tbl_cities.id", "=", "business_profiles.city")
        ->join("tbl_states", "tbl_states.id", "=", "tbl_cities.state_id")
        ->join("tbl_countries", "tbl_countries.id", "=", "tbl_states.country_id")
        ->where('brand_title', 'like', '%' . $q . '%')
        ->orwhere('description', 'like', '%' . $q . '%')
        ->where('tbl_countries.name', 'like', '%' . $countries->name . '%')
        ->get();
        }
   
        return $data;

}
}

public static function withoutlocation($q){
    $data = BusinessProfile::select("business_profiles.id as id", "business_profiles.brand_name as brand_name", "business_profiles.brand_title as brand_title", "business_profiles.brand_logo as brand_logo", "business_profiles.description as description", "business_profiles.address as address", "business_profiles.city as city", "business_profiles.verifcation_id as verifcation_id","tbl_cities.name as district", "tbl_cities.locality as locality", "tbl_states.name as state", "tbl_countries.name as country")->join("tbl_cities", "tbl_cities.id", "=", "business_profiles.city")
    ->join("tbl_states", "tbl_states.id", "=", "tbl_cities.state_id")
    ->join("tbl_countries", "tbl_countries.id", "=", "tbl_states.country_id")
    ->where('brand_title', 'like', '%' . $q . '%')
    ->orwhere('description', 'like', '%' . $q . '%')
    ->get();
    return $data;
 
}

}
