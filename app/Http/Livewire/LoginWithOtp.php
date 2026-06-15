<?php

namespace App\Http\Livewire;
use Livewire\Component;
use App\Models\User;
use App\Models\Setting;
use App\Models\User_profile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use DB;
use Illuminate\Support\Facades\Auth;
$path_to_file = base_path(). '/vendor/twilio/sdk/src/Twilio/autoload.php';
if (is_file($path_to_file)) {
require base_path(). '/vendor/twilio/sdk/src/Twilio/autoload.php';
}
use Twilio\Rest\Client;

class LoginWithOtp extends Component
{	     
    public function render()
    {        
        $result = Setting::where('active',1)->where('key','twilio_sms')->first();
        if(!empty($result)){
            $value = json_decode($result->value,true);
            if($value['enable_sms']==1){
                return view('livewire.login-with-otp');
            }else{
                abort(404);
            }
        }else{
            abort(404);
        }
    	
    }
    public function send_otp()
    {
        
        $phone = request()->phone;  
        $e164 = request()->e164;     
        // dd($phone,$e164);
        $get_phone = User_profile::where('phone',$e164)->pluck('user_id')->first(); 
        $check = User::where('id',$get_phone)->whereNull('deleted_at')->first();
        if(!empty($check)){
            if($check->is_blocked==1){
                return response()->json(['result'=>"error",'message'=>'Your account has been deactivated by admin!, please contact admin!']);
            }else{
                // $code = 1234;
                $code = mt_rand(1000,9999);
                $account_sid = getenv("TWILIO_SID");
                $auth_token = getenv("TWILIO_TOKEN");
                $twilio_number = getenv("TWILIO_FROM");  
                $client = new Client($account_sid, $auth_token);           
                $message = $client->messages
                ->create($e164, 
                        [
                            "body" => "Dear Customer,use code $code to login to your account. Never share your OTP with anyone.",
                            "from" => $twilio_number
                        ]

                ); 
                $node = User_profile::where('user_id',$get_phone)->pluck('id')->first();
                DB::table('user_profiles')->where('id', $node)->update(['otp' => $code]);
                return response()->json(['result'=>"success",'message'=>'OTP sent your mobile number successfully!']);
            }               
        }else{
            return response()->json(['result'=>"error",'message'=>'Phone number does not exist!']);   
        }    
    }

    function verify_otp(){
        $phone = request()->phone;
        $otp = request()->otp;
        $check = User_profile::where('phone',$phone)->where('otp',$otp)->pluck('user_id')->first(); 
        $finduser = User::where('id',$check)->whereNull('deleted_at')->first(); 
        if(!empty($finduser)){  
            if($finduser->is_blocked==1){
                $return_url =  URL::to('/');
                return response()->json(['result'=>"error",'message'=>'your account has been deactivated by admin',"return_url" => $return_url]); 
            }else{
                Auth::login($finduser);
                $return_url =  URL::to('/');
                return response()->json(['result'=>"success",'message'=>'Logged in successfully!',"return_url" => $return_url]);   
            }        
            
        }else{
            return response()->json(['result'=>"error",'message'=>'Invalid OTP']);   
        }
    }

}
