<?php

namespace Bannerplugins\Bannermap;
//namespace Plugins\Stripe;
use App\Models\TblPayment;
use App\Models\TblCity;
use App\Models\TblState;
use App\Models\TblCountry;
use App\Models\TblPost;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class BannerMapController
{

    public function km_ads_from_cur_dist()
    {
        if($_SERVER["REQUEST_METHOD"]=="POST")
        {
           
            $lat = request()->lat;
            $lon = request()->lon;
            $dist = request()->dist;
            $country = request()->current_country;
            $state = request()->current_state;
            $city = request()->current_cityname;

        // $lat    = 9.9202912;//munichalai
        // $lon    = 78.1294314;
        // $dist = 100; // Km

        if(!empty($lat)){
            $query = "
            SELECT * FROM (
                SELECT *, 
                    (
                        (
                            (
                                acos(
                                    sin(( $lat * pi() / 180))
                                    *
                                    sin(( `latitude` * pi() / 180)) + cos(( $lat * pi() /180 ))
                                    *
                                    cos(( `latitude` * pi() / 180)) * cos((( $lon - `logitude`) * pi()/180)))
                            ) * 180/pi()
                        ) * 60 * 1.1515 * 1.609344
                    )
                as distance FROM `tbl_cities`
            ) tbl_cities
            WHERE distance <= $dist
        ";

        $results = DB::select($query);
        }else{
            $results = "";
        }

       

            if(empty($results)){
                $get_city_based = TblCity::where('name', $city)->first();   
                if(empty($get_city_based)){
                    $get_state_based = TblState::where('name', $state)->pluck('id')->first();
                    if(!empty($get_state_based)){
                        $results = TblCity::where('state_id', $get_state_based)->get();
                    }else{
                    $results = array();
                    }
                }else{
                    $results = $get_city_based;
                }  
            }
            $paid_ads = $this->get_paid_ads_for_map();

            $final_result = [];
            
           
            foreach($results as $r){     
                
                $getArrDet = TblPost::where('city',$r->id)->whereIn('id',$paid_ads)->limit(10)
                ->inRandomOrder()->get()->toArray();
                $lc = (is_null($r->locality))?$r->name:$r->locality;
                $html = "";
                if(count($getArrDet)>0){
                    $html .= "<h2 class='bg-green-500 text-center text-white p-1'>$lc</h2><ul>";
                    foreach($getArrDet as $k)
                    {
                        $ad_title = $k['title'];
                        $ad_slug = URL::to('/'.$k["slug"]);
    
                        $html .= "<li class='mt-2 underline'><a href='$ad_slug' target='_blank'>$ad_title</a></li>";
                    }
                    $html .="</ul>";
    
                    $final_result[] = array(
                        "latitude" =>$r->latitude,
                        "logitude"=>$r->logitude,
                        "icon"=>URL::to('images/loc-mark.png'),
                        "description"=>$html
                    );
                }

            }
            return response()->json(["result"=>"success","data"=>$final_result]);

            }
        
    }

    public function get_paid_ads_for_map(){
        $curr_date = date('Y-m-d H:i:s');
        $blockedUsers = User::blocked_users();
        $payment_ids_array = TblPayment::whereNotIn('tbl_payments.user_id',$blockedUsers)                        
                            ->where('tbl_payments.active','1')
                            ->where('tbl_payments.start_date','<=',$curr_date)
                            ->where('tbl_payments.end_date','>=',$curr_date)                                               
                            ->pluck('tbl_payments.post_id')->toArray(); 
        return $payment_ids_array;
    }

}