<?php
namespace App\Http\Livewire;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\BusinessProfile;
use App\Models\TblPost;
use App\Models\Feature;
use App\Models\TblChat;
use App\Models\TblCity;
use App\Models\Setting;
use App\Models\TblLanguage;
use App\Models\Languages;
use App\Models\TblPostedAdPackageInfo;
use Exception;
use App\Models\VerificationAttachment;
use App\Models\Verificationrequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
//use App\Http\Livewire\ApiBaseComponent as BaseComponent;
use Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\TblCategory;
use App\Models\TblSavedPosts;
use App\Models\TblReview;
use App\Models\Package;
use App\Models\TblPaymentsMethod;
use App\Models\TblCountry;
use App\Models\TblState;
use App\Models\TblPayment;
use App\Models\User_profile;
use App\Models\TblBlockeduser;
use App\Models\TblCustomField;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Passwords\PasswordBroker;
use App\Models\TblStaticpage;
use App\Models\ReportType;
use App\Models\TblReportThisAd;
use App\Models\TblFieldsDetail;
use App\Models\TblFieldsOption;
use App\Models\TblPostValue;
use App\Models\TblCoupon;
use App\Models\TblReportThisUser;
use App\Models\TblExchangedPost;
use Livewire\WithFileUploads;
use App\Models\TblBulkPackPayment;
use App\Models\TblPostView;
use App\Models\TblFollowers;
use App\Models\TblInvitedFriends;
use App\Mail\InviteFriendMail;
use App\Models\TblNotifications;
use Illuminate\Support\Facades\Session;
use Storage;
use Image;
use Carbon\Carbon;
use Mail;
use App\Mail\ContactUsMail;
use App\Models\TblContactUs;
use App\Models\TblBanners;
use App\Models\TblBannerAdvertisement;
use App\Models\TblPostCheckout;
use App\Models\TblShippingAddress;
use App\Models\TblBuynowOrder;
use App\Models\TblCourierInfo;
use App\Models\TblPostInsight;
use App\Models\TblCurrency;
use App\Models\TblDefaultCurrency;
use App\Models\TblLikeAndDislikePost;
use Carbon\CarbonPeriod;
use App\Models\TblPostMethod;
use App\Models\TblSellerReviews;
use App\Models\FeaturesMappingGroup;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Notifications\OtpNotification;
$path_to_file = base_path() . '/vendor/twilio/sdk/src/Twilio/autoload.php';
if (is_file($path_to_file)) {
    require base_path() . '/vendor/twilio/sdk/src/Twilio/autoload.php';
}
use Twilio\Rest\Client;
class ApiComponent extends Component
{
    use WithFileUploads;
    public function validate_apple_token()
    {
        return response()->view("api.appletokenvalidator")->header('Content-Type', 'application/json');
    }
    public function getBearerToken()
    {
        $headers = apache_request_headers();
        if (!empty($headers['Token'])) {
            if (Str::startsWith($headers['Token'], 'Bearer ')) {
                $data = array('token' => Str::substr($headers['Token'], 7), 'code' => 200);
                return $data;
            } else {
                $data = array('code' => 0);
                return $data;
            }
        } else {
            $data = array('code' => 0);
            return $data;
        }
    }
    public function sendError($message)
    {
        $response = [
            'success' => false,
            'code' => 0,
            'message' => $message
        ];
        return $response;
    }
    public function sendSuccess($message)
    {
        $response = [
            'success' => true,
            'code' => 200,
            'message' => $message
        ];
        return $response;
    }
    /*
      |--------------------------------------------------------------------------
      | Get User Details for Sidebar
      |--------------------------------------------------------------------------
      | Yeh function sidebar ke liye user ka tamam zaroori data ek sath return karta hai.
      | (This function returns all necessary user data for the sidebar at once)
      */
    public function get_user_details()
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user_id = $this->getLoggedUser($token['token']);

            if (!empty($user_id)) {

                // Pehle User ko find karein
                $loguser = User::where('id', $user_id)->first();

                // Agar user hi nahi mila (delete ho gaya hai)
                if (!$loguser) {
                    return response()->json($this->sendError("User account not found."));
                }

                // --- YEH HAI ASAL FIX ---
                // User profile ko find karein, agar nahi milta toh bana dein (Find or Create)
                $user_profile_data = User_profile::firstOrCreate(
                    ['user_id' => $user_id], // Is ID se find karo
                    ['phone' => $loguser->phone] // Agar nahi milta, toh in values se create kardo
                );
                // --- FIX END ---


                // 1. Get Ads Count
                $total_ads = TblPost::where('user_id', $user_id)->where('active', 1)->whereNull('deleted_at')->count();

                // 2. Get Active Ads Count
                $curr_date = date('Y-m-d H:i:s');
                $payment_ids_array = TblPayment::where('user_id', $user_id)->where('active', '1')->where('start_date', '<=', $curr_date)->where('end_date', '>=', $curr_date)->pluck('post_id')->toArray();
                $free_ids_array = TblPostedAdPackageInfo::where('user_id', $user_id)->where('active', '1')->where('start_date', '<=', $curr_date)->where('end_date', '>=', $curr_date)->pluck('post_id')->toArray();
                $post_ids = array_merge($payment_ids_array, $free_ids_array);

                $active_ads = 0;
                if (!empty($post_ids)) {
                    $active_ads = TblPost::whereIn('id', $post_ids)->where('active', 1)->whereNull('deleted_at')->count();
                }

                // 3. Get Orders & Sales Count
                $total_orders = TblBuynowOrder::where('user_id', $user_id)->count();
                $total_sales = TblBuynowOrder::where('seller_id', $user_id)->count();

                // 4. Get Exchange Count (Pending)
                $incoming_exchanges = TblExchangedPost::where('post_owner_id', $user_id)->where('status', 'pending')->count();
                $outgoing_exchanges = TblExchangedPost::where('user_id', $user_id)->where('status', 'pending')->count();

                // 5. Get Verification Status
                $verification_status = 'initiate'; // Default
                $verification_request = Verificationrequest::where('user_id', $user_id)->first();

                if ($verification_request) {
                    if ($verification_request->is_approved == 1) {
                        $verification_status = 'Approved';
                    } elseif (!empty($verification_request->decline_reason)) {
                        $verification_status = 'Decline';
                    } else {
                        $verification_status = 'Pending';
                    }
                }

                // 6. Get Currency
                $currency_code = TblCurrency::where('id', $loguser->preferred_currency)->value('short_code');

                // 7. Assemble Response Data
                $user_details = [
                    'name' => $loguser->name,
                    'email' => $loguser->email,
                    'user_id' => $user_profile_data->user_id,
                    'phone' => $user_profile_data->phone,
                    'profile_image' => !empty($loguser->profile_photo_path) ? URL::to('/storage/' . $loguser->profile_photo_path) : "",
                    'mobile_verified' => $user_profile_data->mobile_verified, // 0 -- unverfied 1 - verified
                    'verification_status' => $verification_status, // 'initiate', 'Pending', 'Approved', 'Decline'
                    'pref_lang' => $loguser->preferred_language,
                    'pref_curr_code' => $currency_code,

                    // Stats for the sidebar
                    'stats' => [
                        'total_ads' => $total_ads,
                        'active_ads' => $active_ads,
                        'orders' => $total_orders,
                        'sales' => $total_sales,
                        'exchanges' => $incoming_exchanges + $outgoing_exchanges,
                    ]
                ];

                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $user_details,
                ];
            } else {
                $response = $this->sendError("Invalid User!");
            }
        } else {
            $response = $this->sendError("Invalid Authorization Bearer Token!");
        }
        return response()->json($response);
    }
    /* Get currently logged in user */
    public function getLoggedUser($token)
    {
        return User::where('api_token', $token)->pluck('id')->first();
    }
    /* insert country , state, city - lat and lng for post locations */
    public function insert_locations($cntry_short, $cntry_lng, $state_short, $state_lng, $city, $locality, $lat, $lng)
    {
        $country_id = $state_id = $city_id = $predefined_imgs = "";
        /* get country info */
        $tbl_country = TblCountry::where('code', $cntry_short)->where('name', $cntry_lng)->get();
        if ($tbl_country->count() == 0) {
            $country_id = TblCountry::create([
                'code' => $cntry_short,
                'name' => $cntry_lng
            ])->id;
        } else {
            $country_id = $tbl_country[0]->id;
        }
        /* get state info */
        $tbl_state = TblState::where('country_id', $country_id)->where('code', $state_short)->where('name', $state_lng)->get();
        if ($tbl_state->count() == 0) {
            $state_id = TblState::create([
                'country_id' => $country_id,
                'code' => $state_short,
                'name' => $state_lng
            ])->id;
        } else {
            $state_id = $tbl_state[0]->id;
        }
        /* get city info */
        $tbl_cities = TblCity::where('country_id', $country_id)->where('state_id', $state_id)->where('name', $city)->where('locality', $locality)->get();
        if ($tbl_cities->count() == 0) {
            $city_id = TblCity::create([
                'country_id' => $country_id,
                'state_id' => $state_id,
                'name' => $city,
                'locality' => $locality,
                'latitude' => $lat,
                'logitude' => $lng
            ])->id;
        } else {
            $city_id = $tbl_cities[0]->id;
        }
        $data = array('country_id' => $country_id, 'state_id' => $state_id, 'city_id' => $city_id);
        return $data;
    }
    public function post_currency($id)
    {
        /* show the curreny symbol */
        $settings = Setting::get_logos();
        $post_currency_id = TblPost::where('id', $id)->pluck('currency_id')->first();
        $slected_currency = !empty($post_currency_id) ? $post_currency_id : $settings['default_currency'];
        $currency_symbol = TblPost::get_post_currency($slected_currency);
        return $currency_symbol[0];
    }
    /* ======================== Common Function End ================ */
    //User registeration
    public function register(Request $request)
    {
        $input = $request->all();

        // Validation checks (Same as before)
        $checkuser = User::where('email', $request->email)->whereNull('deleted_at')->first();
        $checkmobile = User_profile::where('phone', $request->phone_number)->pluck('user_id')->first();

        $get_user = "";
        if (!empty($checkmobile)) {
            $get_user = User::where('id', $checkmobile)->whereNull('deleted_at')->first();
        }

        // Agar User Naya Hai
        if (empty($checkuser) && empty($get_user)) {

            // 1. OTP Generate karein (Website wala logic)
            $otp = rand(100000, 999999);

            // 2. User Create karein (OTP fields ke sath)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone_number,
                'otp' => $otp, // OTP Database me save
                'otp_expires_at' => now()->addMinutes(10), // Expiry time
            ]);

            $role = "User";
            $user->assignRole([$role]);

            User_profile::create([
                'user_id' => $user->id,
                'phone' => $request->phone_number
            ]);

            // 4. Ab OTP Email Send karein
            try {
                $user->notify(new OtpNotification($otp));
            } catch (\Exception $e) {
                // Agar email fail ho jaye to log karein taake app crash na ho
                \Log::error("OTP Email Failed: " . $e->getMessage());
            }

            // Welcome mail (Optional - agar aap rakhna chahein)
            // ... (Welcome mail code here if needed) ...

            $response = [
                'success' => true,
                'code' => 200,
                'message' => "User registered successfully! Check email for OTP.",
                'data' => $user // Frontend ko User ID bhejna zaroori hai
            ];

        } else if (!empty($checkuser)) {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Email id already exist!"
            ];
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Mobile number already exist!"
            ];
        }

        return response()->json($response);
    }
    //User Login
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('Classified')->plainTextToken;
            User::where('id', $user->id)->update(array('api_token' => Str::substr($token, 2)));
            $success['token'] = Str::substr($token, 2);
            $success['name'] = $user->name;
            $success['is_blocked'] = $user->is_blocked;
            $success['user_id'] = $user->id;
            $success['email_verified_at'] = $user->email_verified_at;
            if ($user->is_blocked == 1) {
                $message = "Your account has beed deactived. If you want to activate please contact admin!";
            } else {
                $message = "Loggedin successfully!";
            }
            $response = [
                'success' => true,
                'code' => 200,
                'message' => $message,
                'data' => $success
            ];
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Mismatch crendials!",
            ];
        }
        return response()->json($response);
    }
    //check sms package 
    public function check_sms_package()
    {
        $result = Setting::where('active', 1)->where('key', 'twilio_sms')->first();
        if (!empty($result)) {
            $value = json_decode($result->value, true);
            if ($value['enable_sms'] == 1) {
                $response = [
                    'success' => true,
                    'code' => 200,
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
            ];
        }
        return response()->json($response);
    }
    // send otp
    public function send_otp(Request $request)
    {
        $phone = $request->phone_number;
        $country_code = $request->country_code;
        $e164 = "+" . $request->country_code . $request->phone_number;
        $get_phone = User_profile::where('phone', $e164)->pluck('user_id')->first();
        $check = User::where('id', $get_phone)->whereNull('deleted_at')->first();
        $errorArray = [
            'success' => false,
            'code' => 0,
            'message' => 'Kindly register and try this.!'
        ];
        if (empty($get_phone)) {
            $response = $errorArray;
        } else if (!empty($check)) {
            if ($check->is_blocked == 1) {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => 'Your account has been deactivated by admin!, please contact admin!'
                ];
            } else {
                $code = mt_rand(1000, 9999);
                $result = Setting::where('active', 1)->where('key', 'twilio_sms')->first();
                if (!empty($result)) {
                    $value = json_decode($result->value, true);
                    if ($value['enable_sms'] == 1) {
                        if (!empty($value['twilio_sid']) && !empty($value['twilio_token']) && !empty($value['twilio_from'])) {
                            $account_sid = $value['twilio_sid'];
                            $auth_token = $value['twilio_token'];
                            $twilio_number = $value['twilio_from'];
                            $client = new Client($account_sid, $auth_token);
                            $message = $client->messages->create(
                                $e164,
                                [
                                    "body" => "Dear Customer,use code $code to login to your account. Never share your OTP with anyone.",
                                    "from" => $twilio_number
                                ]
                            );
                            if (!empty($message->sid)) {
                                $node = User_profile::where('user_id', $get_phone)->pluck('id')->first();
                                User_profile::where('id', $node)->update(array('otp' => $code));
                                $response = [
                                    'success' => true,
                                    'code' => 200,
                                    'message' => 'OTP sent your mobile number successfully!'
                                ];
                            } else {
                                $response = [
                                    'success' => false,
                                    'code' => 0,
                                    'message' => 'Please try again later!'
                                ];
                            }
                        } else {
                            $response = [
                                'success' => false,
                                'code' => 0,
                                'message' => 'Please try again later!'
                            ];
                        }
                    } else {
                        $response = [
                            'success' => false,
                            'code' => 0,
                            'message' => 'Please try again later!'
                        ];
                    }
                } else {
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => 'Please try again later!'
                    ];
                }
            }
        } else {
            $response = $errorArray;
        }
        return response()->json($response);
    }
    // get otp
    public function get_otp($id, $e164)
    {
        $code = mt_rand(1000, 9999);
        $result = Setting::where('active', 1)->where('key', 'twilio_sms')->first();
        if (!empty($result)) {
            $value = json_decode($result->value, true);
            if ($value['enable_sms'] == 1) {
                if (!empty($value['twilio_sid']) && !empty($value['twilio_token']) && !empty($value['twilio_from'])) {
                    $account_sid = $value['twilio_sid'];
                    $auth_token = $value['twilio_token'];
                    $twilio_number = $value['twilio_from'];
                    $client = new Client($account_sid, $auth_token);
                    $message = $client->messages->create(
                        $e164,
                        [
                            "body" => "Dear Customer,use code $code to verify your mobile number. KyAUIajLbWN",
                            "from" => $twilio_number
                        ]
                    );
                    if (!empty($message->sid)) {
                        $node = User_profile::where('user_id', $id)->pluck('id')->first();
                        User_profile::where('id', $node)->update(array('otp' => $code));
                        $response = "success";
                    } else {
                        $response = "fail";
                    }
                } else {
                    $response = "fail";
                }
            } else {
                $response = "fail";
            }
        } else {
            $response = "fail";
        }
        return $response;
    }
    // send opt to mobile
    public function send_otp_for_mobile_verification(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $e164 = "+" . $request->country_code . $request->phone_number;
                $get_phone = User_profile::where('phone', $request->phone_number)->first();
                if (!empty($get_phone)) {
                    $check = User::where('id', $get_phone->user_id)->whereNull('deleted_at')->first();
                    if (empty($check)) {
                        $return_val = $this->get_otp($user, $e164);
                        if ($return_val == "success") {
                            $response = $this->sendSuccess("Verification sent your entered mobile number successfully!");
                        } else {
                            $response = $this->sendError("Please try again later!");
                        }
                    } else {
                        if ($check->id == $user) {
                            $return_val = $this->get_otp($user, $e164);
                            if ($return_val == "success") {
                                $response = $this->sendSuccess("Verification sent your entered mobile number successfully!");
                            } else {
                                $response = $this->sendError("Please try again later!");
                            }
                        } else {
                            $response = $this->sendError("Phone number already exist!");
                        }
                    }
                } else {
                    $return_val = $this->get_otp($user, $e164);
                    if ($return_val == "success") {
                        $response = $this->sendSuccess("Verification sent your entered mobile number successfully!");
                    } else {
                        $response = $this->sendError("Please try again later!");
                    }
                }
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    // otp verification success
    public function mobile_verification_success(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $node = User_profile::where('user_id', $user)->first();
                if (!empty($request->otp)) {
                    if ($node->otp == $request->otp) {
                        User_profile::where('user_id', $node->user_id)->update(array('phone' => $request->phone_number, 'mobile_verified' => 1));
                        User::where('id', $node->user_id)->update(array('phone' => $request->phone_number));
                        $response = $this->sendSuccess("Verified successfully!");
                    } else {
                        $response = $this->sendError("Invalid OTP");
                    }
                } else {
                    User_profile::where('user_id', $node->user_id)->update(array('phone' => $request->phone_number, 'mobile_verified' => 1));
                    User::where('id', $node->user_id)->update(array('phone' => $request->phone_number));
                    $response = $this->sendSuccess("Verified successfully!");
                }
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    // republish post
    public function republish_post(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $post_id = $request->id;
                $post = TblPost::where('id', $post_id)->where('user_id', $user)->first();
                if (!empty($post)) {
                    $post_payment = TblPayment::where('post_id', $post_id)->where('user_id', $user)->first();
                    $post_free = TblPostedAdPackageInfo::where('post_id', $post_id)->where('user_id', $user)->first();

                    if (!empty($post_payment)) {
                        $post_payment->start_date = Carbon::now();
                        $post_payment->end_date = Carbon::now()->addDays(30);
                        $post_payment->active = 1;
                        $post_payment->save();
                    } else if (!empty($post_free)) {
                        $post_free->start_date = Carbon::now();
                        $post_free->end_date = Carbon::now()->addDays(30);
                        $post_free->active = 1;
                        $post_free->save();
                    } else {
                        // create a free package
                        TblPostedAdPackageInfo::create([
                            'user_id' => $user,
                            'post_id' => $post_id,
                            'publish_count' => 1,
                            'start_date' => Carbon::now(),
                            'end_date' => Carbon::now()->addDays(30),
                            'active' => 1,
                        ]);
                    }

                    $post->active = 1;
                    $post->sold_status = 0;
                    $post->save();

                    $response = [
                        'success' => true,
                        'code' => 200,
                        'data' => ['message' => 'Post republished successfully!']
                    ];
                } else {
                    $response = $this->sendError("Post not found.");
                }
            } else {
                $response = $this->sendError("Invalid User.");
            }
        } else {
            $response = $this->sendError("Invalid Token.");
        }
        return response()->json($response);
    }

    // otp verify
    public function verify_otp(Request $request)
    {
        // 1. Validation
        if (!$request->has('user_id') || !$request->has('otp')) {
            return response()->json([
                'success' => false,
                'code' => 0,
                'message' => 'User ID and OTP are required.'
            ]);
        }

        $otp = $request->otp;
        $user_id = $request->user_id;

        // 2. Find User directly from Users table (not User_profile)
        $user = User::where('id', $user_id)->whereNull('deleted_at')->first();

        if (!empty($user)) {

            // 3. Check if OTP matches
            // Note: Type casting (string/int) issue se bachne k liye == use kia hai
            if ($user->otp == $otp) {

                // 4. Check for Expiry (Optional but recommended)
                // Agar aapne expiry set ki thi to ye check karein, warna skip kar dein
                if ($user->otp_expires_at && $user->otp_expires_at < now()) {
                    return response()->json([
                        'success' => false,
                        'code' => 0,
                        'message' => 'OTP has expired. Please request a new one.'
                    ]);
                }

                // 5. Verify Email & Clear OTP
                // Email Verified timestamp set karein
                if ($user->email_verified_at == null) {
                    $user->email_verified_at = now();
                }

                // OTP clear kar dein taake dubara use na ho sake (Security)
                $user->otp = null;
                $user->otp_expires_at = null;

                // 6. Create Token (Login the user)
                $token = $user->createToken('Classified')->plainTextToken;

                // Legacy: Agar aapki DB me 'api_token' column hai to update karein
                $user->api_token = Str::substr($token, 2);
                $user->save();

                // 7. Prepare Success Response
                $success['token'] = Str::substr($token, 2);
                $success['user_id'] = $user->id;
                $success['name'] = $user->name;
                $success['email'] = $user->email;
                $success['is_blocked'] = $user->is_blocked;

                if ($user->is_blocked == 1) {
                    $message = "Your account has been deactivated. Please contact admin!";
                } else {
                    $message = "Email verified and Logged in successfully!";
                }

                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => $message,
                    'data' => $success
                ];

            } else {
                // OTP Match Nahi Hua
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => 'Invalid OTP Code!'
                ];
            }

        } else {
            // User Nahi Mila
            $response = [
                'success' => false,
                'code' => 0,
                'message' => 'User not found!'
            ];
        }

        return response()->json($response);
    }
    //Logged in user - Added Post
    public function mypost(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $page = $request->has('page') ? $request->get('page') : 1;
                $limit = $request->has('limit') ? $request->get('limit') : 10;
                $search = $request->has('s') ? $request->get('s') : "";
                $package_id = $request->has('package_id') ? $request->get('package_id') : "";
                /* get total post count added by user */
                if (!empty($package_id) && $package_id != "24a4a3c5-814c-4866-a4f3-65228cd18de5") {    //free package id give manually
                    $posts_cnt = TblPost::select("tbl_posts.*")
                        ->where('tbl_posts.user_id', $user)
                        ->where('tbl_posts.title', 'like', '%' . $search . '%')
                        ->whereNull('tbl_posts.deleted_at')
                        ->where('tbl_posts.active', 1)
                        ->Join("tbl_payments", function ($join) use ($user, $package_id) {
                            $join->on("tbl_payments.post_id", "=", "tbl_posts.id")
                                ->where("tbl_payments.user_id", "=", $user)
                                ->where("tbl_payments.package_id", "=", $package_id)
                                ->where("tbl_payments.active", "=", '1')
                                ->whereDate("tbl_payments.end_date", ">=", date("Y-m-d"));
                        })
                        ->count();
                } elseif (!empty($package_id) && $package_id == "24a4a3c5-814c-4866-a4f3-65228cd18de5") {
                    $posts_cnt = TblPost::select("tbl_posts.*")
                        ->where('tbl_posts.user_id', $user)
                        ->where('tbl_posts.title', 'like', '%' . $search . '%')
                        ->whereNull('tbl_posts.deleted_at')
                        ->where('tbl_posts.active', 1)
                        ->orderBy('tbl_posts.created_at', 'desc')
                        ->Join("tbl_posted_ad_package_infos", function ($join) use ($user) {
                            $join->on("tbl_posted_ad_package_infos.post_id", "=", "tbl_posts.id")
                                ->where("tbl_posted_ad_package_infos.user_id", "=", $user)
                                ->where("tbl_posted_ad_package_infos.active", "=", '1');
                        })
                        ->count();
                } else {
                    $posts_cnt = TblPost::where('user_id', $user)
                        ->where('title', 'Like', '%' . $search . '%')
                        ->where('active', 1)
                        ->whereNull('deleted_at')
                        ->orderBy('created_at', 'desc')->count();
                }
                /* get total post added by user */
                if (!empty($package_id) && $package_id != "24a4a3c5-814c-4866-a4f3-65228cd18de5") {    //free package id give manually
                    $posts = TblPost::select("tbl_posts.*")
                        ->where('tbl_posts.user_id', $user)
                        ->where('tbl_posts.title', 'like', '%' . $search . '%')
                        ->whereNull('tbl_posts.deleted_at')
                        ->where('tbl_posts.active', 1)
                        ->Join("tbl_payments", function ($join) use ($user, $package_id) {
                            $join->on("tbl_payments.post_id", "=", "tbl_posts.id")
                                ->where("tbl_payments.user_id", "=", $user)
                                ->where("tbl_payments.package_id", "=", $package_id)
                                ->where("tbl_payments.active", "=", '1')
                                ->whereDate("tbl_payments.end_date", ">=", date("Y-m-d"));
                        })
                        ->limit($limit)->offset(($page - 1) * $limit)->get();
                } elseif (!empty($package_id) && $package_id == "24a4a3c5-814c-4866-a4f3-65228cd18de5") {
                    $posts = TblPost::select("tbl_posts.*")
                        ->where('tbl_posts.user_id', $user)
                        ->where('tbl_posts.title', 'like', '%' . $search . '%')
                        ->whereNull('tbl_posts.deleted_at')
                        ->where('tbl_posts.active', 1)
                        ->orderBy('tbl_posts.created_at', 'desc')
                        ->Join("tbl_posted_ad_package_infos", function ($join) use ($user) {
                            $join->on("tbl_posted_ad_package_infos.post_id", "=", "tbl_posts.id")
                                ->where("tbl_posted_ad_package_infos.user_id", "=", $user)
                                ->where("tbl_posted_ad_package_infos.active", "=", '1');
                        })
                        ->limit($limit)->offset(($page - 1) * $limit)->get();
                } else {
                    $posts = TblPost::where('user_id', $user)
                        ->where('title', 'Like', '%' . $search . '%')
                        ->whereNull('deleted_at')
                        ->where('active', 1)
                        ->orderBy('created_at', 'desc')->limit($limit)->offset(($page - 1) * $limit)->get();
                }
                $post_data = array();
                foreach ($posts as $post) {
                    $images = TblPost::get_single_post_information($post->id); //
                    $viewcount = TblPostInsight::views_count($post->id);
                    $likecount = TblPost::get_likes_count($post->id);
                    $check_post_package = TblPost::check_post_expired($post->id); //
                    if ($check_post_package['ads_type'] != "free") {
                        if ($check_post_package['expired'] == "Expired") {
                            $package_string_republish = 0;
                            $package_string_free = 1;
                            $package_string = "";
                            $bulk_validity = "";
                        } else {
                            if ($check_post_package['is_bulk'] != 0) {
                                $bulk_pack_string = "Bulk Package - ";
                            } else {
                                $bulk_pack_string = "";
                            }
                            $package_string = $bulk_pack_string . $check_post_package['ads_type'];
                            $bulk_validity = $check_post_package['bulk_type'];
                            $package_string_republish = 0;
                            $package_string_free = 0;
                        }
                    } else {
                        $pst_cnt = Package::where('lft', 1)->first();
                        $check_with_cnt = $pst_cnt->single_pack_limit;
                        if (($check_post_package['expired'] == "Expired") && ($check_post_package['post_count'] < $check_with_cnt)) {
                            $package_string_republish = 1;
                            $package_string_free = 0;
                        } else {
                            $package_string_republish = 0;
                            $package_string_free = 1;
                        }
                        $package_string = "";
                        $bulk_validity = "";
                    }
                    if ($check_post_package['expired'] == "Expired") {
                        $is_exp = 1;
                    } else {
                        $is_exp = 0;
                    }
                    $currency = $this->post_currency($post->id);
                    // if (!empty($user)) {
                    //     $post_currency_id = TblPost::where('id', $post->price)->value('currency_id');
                    //     $user_currency = TblPost::userCurrencyConversion($user, $post->price, $post_currency_id);
                    //     $price = $user_currency['convert_cur'];
                    //     $symbol = $user_currency['convert_sym'];
                    // } else {
                    //     $price = $post->price;
                    //     $symbol = $currency;
                    // }
                    $price = $post->price;
                    $symbol = $currency;
                    $post_data[] = array(
                        'id' => $post->id,
                        'title' => $post->title,
                        'price' => $price,
                        'image' => $images['images'],
                        'expired_from' => $check_post_package['from_date'],
                        'expired_to' => $check_post_package['to_date'],
                        'currency_symbol' => $symbol,
                        'view_count' => $viewcount,
                        'like_count' => $likecount,
                        'created_at' => (string) $post->created_at,
                        'package_type' => $package_string,
                        'validity' => $bulk_validity,
                        'republish' => $package_string_republish,
                        'sell_fast' => $package_string_free,
                        'is_exp' => $is_exp,
                        'sold_status' => $post->sold_status, // 1 - sold 0 - sale
                        'giving_away' => $post->giving_away // 1 - yes 0 - no
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'total_count' => $posts_cnt,
                    'data' => $post_data,
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    // List main categories
    public function getCategories(Request $request)
    {
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 10;
        $categories = TblCategory::withDepth()->having('depth', '=', 0)->whereNull('deleted_at')->orderBy('list_order', 'asc')->limit($limit)->offset(($page - 1) * $limit)->get();
        $cat = array();
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                // dd($user);
                $lan_code = User::where('id', $user)->value('preferred_language');
            } else {
                $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
        }
        foreach ($categories as $category) {
            $icon = !empty($category->app_image) ? URL::to('storage') . '/' . $category->app_image : URL::to('storage/noimage150.png');
            $cate_title = Languages::where('lang_code', $lan_code)->where('lang_org_text', $category->title)->value('lang_text');
            $cat[] = array(
                'id' => $category->id,
                'title' => !empty($cate_title) ? $cate_title : $category->title,
                //'title' => $category->title,
                'slug' => $category->slug,
                'image' => $icon
            );
        }
        /* retrun json response */
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $cat,
        ];
        return response()->json($response);
    }
    //Get Sub categories based on selected main category
    public function getSubcategories(Request $request)
    {
        $subcategories = TblCategory::orderBy('list_order', 'asc')->descendantsAndSelf($request->get('cid'));
        $cat = array();
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                // dd($user);
                $lan_code = User::where('id', $user)->value('preferred_language');
            } else {
                $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
        }
        foreach ($subcategories as $subcategory) {
            if ($subcategory->parent_id != "") {
                $child = TblCategory::where('id', $subcategory->parent_id)->pluck('parent_id')->first();
                $subcate_title = Languages::where('lang_code', $lan_code)->where('lang_org_text', $subcategory->title)->value('lang_text');
                if (empty($child)) {
                    $cat[] = array(
                        'id' => $subcategory->id,
                        'label' => !empty($subcate_title) ? $subcate_title : $subcategory->title,
                        'uuid' => $subcategory->uuid,
                        'parent_id' => $subcategory->parent_id,
                        'title' => !empty($subcate_title) ? $subcate_title : $subcategory->title,
                        'value' => $subcategory->id,
                        'slug' => $subcategory->slug,
                        'image' => !empty($subcategory->app_image) ? URL::to('storage') . '/' . $subcategory->app_image : URL::to('storage/noimage150.png')
                    );
                }
            }
        }
        $category_banner = array();
        $get_cat_banners = TblCategory::get_cat_banners(NULL, $request->get('cid'));
        if (!empty($get_cat_banners)) {
            $category_banner = $get_cat_banners;
        }
        //paid banners for this selected category
        $paid_category_banner = array();
        $get_paid_cat_banners = TblCategory::get_paid_cat_banners(NULL, $request->get('cid'), "app");
        if (!empty($get_paid_cat_banners)) {
            $paid_category_banner = $get_paid_cat_banners;
        }
        $visible_banners = array_merge($category_banner, $paid_category_banner);
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $cat,
            'banners' => $visible_banners
        ];
        return response()->json($response);
    }
    //Add to fav
    public function update_to_fav(Request $request)
    {
        $token = $this->getBearerToken();
        // if (!empty($token['code']) && ($token['code'] == 200)) {
        //     $user = $this->getLoggedUser($token['token']);
        //     if (!empty($user)) {
        //         // dd($user);
        //         $lan_code = User::where('id', $user)->value('preferred_language');
        //     } else {
        //         $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
        //         $response = [
        //             'success' => false,
        //             'code' => 0,
        //             'message' => "Invalid User"
        //         ];
        //     }
        // } else {
        //     $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
        // }
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $request->validate([
                    'post_id' => 'required',
                ]);
                $check = TblSavedPosts::where('user_id', $user)->where('post_id', $request->post_id)->get();
                $settings = Setting::get_logos();
                $site_name = $settings['name'];
                $get_user_info = User::where('id', $user)->first();
                $get_post_info = TblPost::where('id', $request->post_id)->first();
                $get_seller_info = User::where('id', $get_post_info->user_id)->first();
                $slug = TblPost::get_post_slug($get_post_info->slug);
                if ($check->count() > 0) {
                    $bb = TblSavedPosts::where('post_id', $request->post_id)->get()[0]->id;
                    TblSavedPosts::find($bb)->delete();
                    // notification start
                    $headers = array(
                        'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                        'Content-Type: application/json',
                    );
                    $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                    // $message1 = array("notifydata" => array('to_id' => $get_post_info->user_id, 'from_id' => $user, 'message' => "Post removed from whish list by " . $get_user_info->name . "!. Post Name - " . $get_post_info->title, 'notify_from' => 'post_like_remove', 'notify_title' => "Post removed from whish list In " . $site_name . "!..", 'post_id' => $request->post_id, 'slug' => $slug));
                    // Define the original texts
                    $originalMessageText = 'Post removed from wishlist by :user!. Post Name - :post';
                    $originalTitleText = 'Post removed from wishlist in :site!';
                    $lan_code = User::where('id', $get_post_info->user_id)->value('preferred_language');
                    // Translate the messages
                    $translatedMessage = TblNotifications::translate($lan_code, $originalMessageText, [
                        'user' => $get_user_info->name,
                        'post' => $get_post_info->title,
                    ]);
                    $translatedTitle = TblNotifications::translate($lan_code, $originalTitleText, [
                        'site' => $site_name,
                    ]);
                    $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                    $message1 = array(
                        "notifydata" => array(
                            'to_id' => $get_post_info->user_id,
                            'from_id' => $user,
                            'message' => $translatedMessage,
                            'notify_from' => 'post_like_remove',
                            'notify_title' => $translatedTitle,
                            'post_id' => $request->post_id,
                            'slug' => $slug
                        )
                    );
                    // TblPost::send_push_notification($fcmid, $message1);
                    // notification end
                    $response = $this->sendSuccess("Removed Favorite successfully!");
                } else {
                    TblSavedPosts::create([
                        'user_id' => $user,
                        'post_id' => $request->post_id,
                    ]);
                    // // notification start
                    $headers = array(
                        'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                        'Content-Type: application/json',
                    );
                    $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                    // $message1 = array("notifydata" => array('to_id' => $get_post_info->user_id, 'from_id' => $user, 'message' => "Liked your post by " . $get_user_info->name . "!. Post Name - " . $get_post_info->title, 'notify_from' => 'post_like', 'notify_title' => "Like a post In " . $site_name . " !..", 'post_id' => $request->post_id, 'slug' => $slug));
                    $originalMessageText = 'Liked your post by :user!. Post Name - :post';
                    $originalTitleText = 'Liked a post in :site!';
                    $lan_code = User::where('id', $get_post_info->user_id)->value('preferred_language');
                    // Translate the messages
                    $translatedMessage = TblNotifications::translate($lan_code, $originalMessageText, [
                        'user' => $get_user_info->name,
                        'post' => $get_post_info->title,
                    ]);
                    $translatedTitle = TblNotifications::translate($lan_code, $originalTitleText, [
                        'site' => $site_name,
                    ]);
                    $message1 = array(
                        "notifydata" => array(
                            'to_id' => $get_post_info->user_id,
                            'from_id' => $user,
                            'message' => $translatedMessage,
                            'notify_from' => 'post_like',
                            'notify_title' => $translatedTitle,
                            'post_id' => $request->post_id,
                            'slug' => $slug
                        )
                    );
                    // TblPost::send_push_notification($fcmid, $message1);
                    // notification end
                    $response = $this->sendSuccess("Added to Favorite successfully!");
                }
            } else {
                $response = $this->sendError("Invalid User!");
            }
        } else {
            $response = $this->sendError("Invalid Authorization Bearer Token!");
        }
        return response()->json($response);
    }
    /* My fav post list */
    /* Likes and Dislikes post */
    public function likes_and_dislike(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $request->validate([
                    'post_id' => 'required',
                    'likes' => 'required',
                    'dislikes' => 'required'
                ]);
                $check = TblLikeAndDislikePost::where('user_id', $user)->where('post_id', $request->post_id)->get();
                $settings = Setting::get_logos();
                $site_name = $settings['name'];
                $get_user_info = User::where('id', $user)->first();
                $get_post_info = TblPost::where('id', $request->post_id)->first();
                $get_seller_info = User::where('id', $get_post_info->user_id)->first();
                $slug = TblPost::get_post_slug($get_post_info->slug);
                $no_likes = TblLikeAndDislikePost::where('user_id', $user)->where('post_id', $request->post_id)->value('dislikes');
                $likes = TblLikeAndDislikePost::where('user_id', $user)->where('post_id', $request->post_id)->value('likes');
                if ($check->count() > 0 && ($no_likes == 0) && ($likes == 1)) {
                    $bb = TblLikeAndDislikePost::where('post_id', $request->post_id)->value('id');
                    // dd($bb,$request->likes,$request->dislikes);
                    $dislikes = TblLikeAndDislikePost::find($bb);
                    $dislikes->update([
                        'likes' => $request->likes,
                        'dislikes' => $request->dislikes
                    ]);
                    // notification start
                    $headers = array(
                        'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                        'Content-Type: application/json',
                    );
                    $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                    // $message1 = array("notifydata" => array('to_id' => $get_post_info->user_id, 'from_id' => $user, 'message' => "Post removed from whish list by " . $get_user_info->name . "!. Post Name - " . $get_post_info->title, 'notify_from' => 'post_like_remove', 'notify_title' => "Post removed from whish list In " . $site_name . "!..", 'post_id' => $request->post_id, 'slug' => $slug));
                    // Define the original texts
                    $originalMessageText = 'Removed the like from  by :user!. Post Name - :post';
                    $originalTitleText = 'Removed Like from post in :site!';
                    $lan_code = User::where('id', $get_post_info->user_id)->value('preferred_language');
                    // Translate the messages
                    $translatedMessage = TblNotifications::translate($lan_code, $originalMessageText, [
                        'user' => $get_user_info->name,
                        'post' => $get_post_info->title,
                    ]);
                    $translatedTitle = TblNotifications::translate($lan_code, $originalTitleText, [
                        'site' => $site_name,
                    ]);
                    $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                    $message1 = array(
                        "notifydata" => array(
                            'to_id' => $get_post_info->user_id,
                            'from_id' => $user,
                            'message' => $translatedMessage,
                            'notify_from' => 'post_like_remove',
                            'notify_title' => $translatedTitle,
                            'post_id' => $request->post_id,
                            'slug' => $slug
                        )
                    );
                    // TblPost::send_push_notification($fcmid, $message1);
                    // notification end
                    $response = $this->sendSuccess("Disliked successfully!");
                } else {
                    if ($check->count() > 0) {
                        $aa = TblLikeAndDislikePost::where('post_id', $request->post_id)->value('id');
                        // dd($bb,$request->likes,$request->dislikes);
                        $add_likes = TblLikeAndDislikePost::find($aa);
                        $add_likes->update([
                            'likes' => $request->likes,
                            'dislikes' => $request->dislikes
                        ]);
                    } else {
                        TblLikeAndDislikePost::create([
                            'user_id' => $user,
                            'post_id' => $request->post_id,
                            'likes' => $request->likes,
                            'dislikes' => $request->dislikes
                        ]);
                    }
                    // // notification start
                    $headers = array(
                        'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                        'Content-Type: application/json',
                    );
                    $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                    // $message1 = array("notifydata" => array('to_id' => $get_post_info->user_id, 'from_id' => $user, 'message' => "Liked your post by " . $get_user_info->name . "!. Post Name - " . $get_post_info->title, 'notify_from' => 'post_like', 'notify_title' => "Like a post In " . $site_name . " !..", 'post_id' => $request->post_id, 'slug' => $slug));
                    $originalMessageText = 'Liked your post by :user!. Post Name - :post';
                    $originalTitleText = 'Liked a post in :site!';
                    $lan_code = User::where('id', $get_post_info->user_id)->value('preferred_language');
                    // Translate the messages
                    $translatedMessage = TblNotifications::translate($lan_code, $originalMessageText, [
                        'user' => $get_user_info->name,
                        'post' => $get_post_info->title,
                    ]);
                    $translatedTitle = TblNotifications::translate($lan_code, $originalTitleText, [
                        'site' => $site_name,
                    ]);
                    $message1 = array(
                        "notifydata" => array(
                            'to_id' => $get_post_info->user_id,
                            'from_id' => $user,
                            'message' => $translatedMessage,
                            'notify_from' => 'post_like',
                            'notify_title' => $translatedTitle,
                            'post_id' => $request->post_id,
                            'slug' => $slug
                        )
                    );
                    // TblPost::send_push_notification($fcmid, $message1);
                    // notification end
                    $response = $this->sendSuccess("Liked successfully!");
                }
            } else {
                $response = $this->sendError("Invalid User!");
            }
        } else {
            $response = $this->sendError("Invalid Authorization Bearer Token!");
        }
        // TblLikeAndDislikePost::create([
        //     'user_id' => "50b3f19f-546f-4cee-93cc-7a5788731776",
        //     'post_id' => "b0a922d5-5bda-4da3-9899-eeece358a056
        //     ",
        // ]);
        // $response = $this->sendSuccess("Added  successfully!");
        return response()->json($response);
    }
    public function my_fav_posts(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $favposts = TblSavedPosts::where('user_id', $user)->pluck('post_id')->toArray();
                $page = $request->has('page') ? $request->get('page') : 1;
                $limit = $request->has('limit') ? $request->get('limit') : 10;
                $search = $request->has('s') ? $request->get('s') : "";
                $datas = TblPost::select("tbl_posts.*", "tbl_cities.name as city_name", "tbl_saved_posts.id as fav_id")
                    ->whereIn('tbl_posts.id', $favposts)
                    ->whereNull('deleted_at')
                    ->where('tbl_posts.sold_status', 0)
                    ->where('tbl_posts.title', 'like', '%' . $search . '%')
                    ->join("tbl_cities", "tbl_cities.id", "=", "tbl_posts.city")
                    ->join("tbl_saved_posts", function ($join) use ($user) {
                        $join->on("tbl_saved_posts.post_id", "=", "tbl_posts.id")
                            ->where("tbl_saved_posts.user_id", "=", $user);
                    })
                    ->limit($limit)->offset(($page - 1) * $limit)->get();
                $datas_count = TblPost::select("tbl_posts.*", "tbl_cities.name as city_name", "tbl_saved_posts.id as fav_id")
                    ->whereIn('tbl_posts.id', $favposts)
                    ->whereNull('deleted_at')
                    ->where('tbl_posts.sold_status', 0)
                    ->where('tbl_posts.title', 'like', '%' . $search . '%')
                    ->join("tbl_cities", "tbl_cities.id", "=", "tbl_posts.city")
                    ->join("tbl_saved_posts", function ($join) use ($user) {
                        $join->on("tbl_saved_posts.post_id", "=", "tbl_posts.id")
                            ->where("tbl_saved_posts.user_id", "=", $user);
                    })->count();
                $fav = array();
                foreach ($datas as $data) {
                    $favs = TblSavedPosts::where('user_id', $user)->where('post_id', $data['id'])->get();
                    $images = TblPost::where('id', $data['id'])->pluck('images')->first();
                    if (!empty($images)) {
                        $imgUrl = explode(',', $images)[0];
                        $imgName = str_replace("adpost/predefined/", '', $imgUrl);
                        $is_file = base_path() . '/storage/app/public/adpost/applist/' . $imgName;
                        if (is_file($is_file)) {
                            $post_img = URL::to('storage/adpost/applist/' . $imgName);
                        } else {
                            $post_img = URL::to('storage/' . $imgUrl);
                        }
                    } else {
                        $post_img = URL::to('storage/noimage150.png');
                    }
                    $currency = $this->post_currency($data['id']);
                    $additional_data = $this->getAdditionalInfo($data['id']);
                    $additional_info = array();
                    foreach ($additional_data as $additional_data) {
                        $additional_info[] = array(
                            'lable' => $additional_data['label'],
                            'value' => $additional_data['value']
                        );
                    }
                    $getPackType = TblPayment::where('post_id', $data['id'])->get();
                    if ($getPackType->count() > 0) {
                        $packageID = $getPackType[0]->package_id;
                        $ad_type = Package::where('id', $packageID)->pluck('ad_type')->toArray();
                    } else {
                        $ad_type = "";
                    }
                    // get locality & city
                    $city_name = "";
                    if ($data['city_name'] == $data['locality']) {
                        $city_name = $data['city_name'];
                    } elseif ($data['locality'] != "") {
                        $city_name = $data['locality'];
                    } else {
                        $city_name = $data['city_name'];
                    }
                    // get locality & city end
                    $fav[] = array(
                        'id' => $data['fav_id'],
                        'post_id' => $data['id'],
                        'title' => $data['title'],
                        'city_name' => $city_name,
                        'price' => $data['price'],
                        'description' => $data['description'],
                        'ad_type' => !empty($ad_type[0]) ? str_replace('_', ' ', strtoupper($ad_type[0])) : "",
                        'image' => $post_img,
                        'is_fav' => !empty($favs) && ($favs->count() > 0) ? true : false,
                        'currency_symbol' => $currency,
                        'custom_fields' => $additional_info,
                        'created_at' => (string) $data['created_at'],
                        'giving_away' => $data['giving_away']
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'total_count' => $datas_count,
                    'data' => $fav,
                ];
                return response()->json($response);
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User!"
                ];
                return response()->json($response);
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!"
            ];
            return response()->json($response);
        }
    }
    /* User get profile */
    public function get_profile()
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $user_profile = User_profile::where('user_id', $user)->first();
                $total_ads = TblPost::where('user_id', $user)->where('active', 1)->whereNull('deleted_at')->count();
                $curr_date = date('Y-m-d H:i:s');
                $payment_ids_array = TblPayment::where('user_id', $user)->where('active', '1')->where('start_date', '<=', $curr_date)->where('end_date', '>=', $curr_date)->pluck('post_id')->toArray();
                $free_ids_array = TblPostedAdPackageInfo::where('user_id', $user)->where('active', '1')->where('start_date', '<=', $curr_date)->where('end_date', '>=', $curr_date)->pluck('post_id')->toArray();
                $post_ids = array_merge($payment_ids_array, $free_ids_array);
                if (!empty($post_ids)) {
                    $active_ads = TblPost::whereIn('id', $post_ids)->where('active', 1)->whereNull('deleted_at')->count();
                } else {
                    $active_ads = 0;
                }
                $loguser = User::where('id', $user)->first();
                $user_profiles['name'] = $loguser->name;
                $user_profiles['websocket_id'] = $loguser->websocket_id;
                $user_profiles['email'] = $loguser->email;
                $user_profiles['id'] = $user_profile->id;
                $user_profiles['first_name'] = $user_profile->first_name;
                $user_profiles['last_name'] = $user_profile->last_name;
                $user_profiles['address_line1'] = $user_profile->address_line1;
                $user_profiles['address_line2'] = $user_profile->address_line2;
                $user_profiles['date_of_birth'] = !empty($user_profile->date_of_birth) ? date('d-m-Y', strtotime($user_profile->date_of_birth)) : null;
                $user_profiles['user_id'] = $user_profile->user_id;
                $user_profiles['gender'] = $user_profile->gender;
                $user_profiles['phone'] = $user_profile->phone;
                $user_profiles['description'] = $user_profile->description;
                $user_profiles['country_code'] = $user_profile->country_code;
                $user_profiles['fb_connected'] = !empty($loguser->facebook_id) ? 1 : 0; // 1 fb connected 0 not connected
                $user_profiles['is_fb_login'] = $loguser->is_fb_login; // 1 - disbale the disconnect btn 0 means show the disconnect button
                $user_profiles['profile'] = !empty($loguser->profile_photo_path) ? URL::to('/storage/' . $loguser->profile_photo_path) : "";
                $user_profiles['show_mobile'] = $user_profile->show_mobile;  // 0 -- hide 1 - show mobile
                $user_profiles['mobile_verified'] = $user_profile->mobile_verified;  // 0 -- unverfied 1 - verified
                $user_profiles['allow_call'] = $user_profile->allow_call;  // 0 -- no 1 - allow
                $user_profiles['stripe_public_key'] = $user_profile->stripe_public_key;
                $user_profiles['stripe_private_key'] = $user_profile->stripe_private_key;
                $user_profiles['total_ads'] = $total_ads;
                $user_profiles['total_active_ads'] = $active_ads;
                $user_profiles['pref_lang'] = $loguser->preferred_language;
                $currency_code = TblCurrency::where('id', $loguser->preferred_currency)->value('short_code');
                $user_profiles['pref_curr'] = $loguser->preferred_currency;
                $user_profiles['pref_curr_code'] = $currency_code;
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $user_profiles,
                ];
            } else {
                $response = $this->sendError("Invalid User!");
            }
        } else {
            $response = $this->sendError("Invalid Authorization Bearer Token!");
        }
        return response()->json($response);
    }
    /* user update profile */
    public function update_profile(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $user_profile = User_profile::where('user_id', $user)->first();
                $loguser = User::where('id', $user)->first();
                if (!empty($request->profile)) {
                    $extension = explode('/', explode(':', substr($request->profile, 0, strpos($request->profile, ';')))[1])[1];
                    $imageName = 'profile-photos/' . Str::random(15) . '.' . $extension;
                    $replace = substr($request->profile, 0, strpos($request->profile, ',') + 1);
                    $img = str_replace($replace, '', $request->profile);
                    $k = str_replace(' ', '+', $img);
                    Storage::disk('public')->put($imageName, base64_decode($k));
                    $loguser->update([
                        'profile_photo_path' => $imageName,
                    ]);
                }
                // dd($request->pref_curr);
                $loguser->update([
                    'preferred_language' => $request->pref_lang,
                    'preferred_currency' => $request->pref_curr,
                ]);
                // update websocket id
                if (!empty($request->websocket_id)) {
                    $loguser->update([
                        'websocket_id' => $request->websocket_id
                    ]);
                }
                if (!empty($request->name)) {
                    $loguser->update([
                        'name' => $request->name
                    ]);
                }
                if (!empty($request->date_of_birth)) {
                    $user_profile->update([
                        'date_of_birth' => date('Y-m-d', strtotime($request->date_of_birth))
                    ]);
                }
                $user_profile->update([
                    'first_name' => !empty($request->first_name) ? $request->first_name : $user_profile->first_name,
                    'last_name' => !empty($request->last_name) ? $request->last_name : $user_profile->last_name,
                    'address_line1' => !empty($request->address_line1) ? $request->address_line1 : $user_profile->address_line1,
                    'address_line2' => !empty($request->address_line2) ? $request->address_line2 : $user_profile->address_line2,
                    'gender' => !empty($request->gender) ? $request->gender : $user_profile->gender,
                    'description' => !empty($request->description) ? $request->description : $user_profile->description,
                    'show_mobile' => !empty($request->show_mobile) ? $request->show_mobile : 0,
                    'allow_call' => !empty($request->allow_call) ? $request->allow_call : 0,
                    'stripe_private_key' => $request->stripe_private_key,
                    'stripe_public_key' => $request->stripe_public_key,
                    'country_code' => !empty($request->country_code) ? $request->country_code : $user_profile->country_code,
                ]);
                if (!empty($request->phone)) {
                    $check_phone = User_profile::where('phone', $request->phone)->where('user_id', '!=', $user)->pluck('user_id')->first();
                    if (!empty($check_phone)) {
                        $get_ph_user = User::where('id', $check_phone)->whereNull('deleted_at')->first();
                        if (empty($get_ph_user)) {
                            $user_profile->update([
                                'phone' => $request->phone
                            ]);
                            $loguser->update([
                                'phone' => $request->phone
                            ]);
                        } else {
                            return $this->sendError("Mobile number already exists!");
                        }
                    } else {
                        $user_profile->update([
                            'phone' => $request->phone
                        ]);
                        $loguser->update([
                            'phone' => $request->phone
                        ]);
                    }
                }
                $response = $this->sendSuccess("Updated successfully");
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    /* Post Detail */
    public function post_detail($id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $postinfo = TblPost::with([
                    'city:id,latitude,logitude',
                    'category:id,title',
                    'user:id,name,email,profile_photo_path,phone,created_at,current_chat_status',
                    'reviews.user:id,name,profile_photo_path'
                ])
                    ->where('id', $id)
                    ->first();
                // dd($postinfo);
                $post_latlong = TblCity::where('id', $postinfo->city)->first();
                $lati = $post_latlong->latitude ?? null;
                $longi = $post_latlong->logitude ?? null;
                $category_name = $postinfo->category->title ?? null;
                $city_name = TblPost::getPostloc($postinfo['city']);
                $related_posts = TblPost::get_related_products($postinfo['category_id'], $id);
                $user_info = User::where('id', $postinfo['user_id'])->get(['name', 'email', 'profile_photo_path', 'phone', 'created_at', 'current_chat_status']);
                $avg_rating = TblReview::rate_avg($id);
                $reviews = TblReview::join('users', 'tbl_reviews.user_id', '=', 'users.id')->get(['tbl_reviews.*', 'users.name', 'users.profile_photo_path'])->where('post_id', $id);
                $data = array();
                $images = array();
                /* Post images */
                $ima = explode(',', $postinfo['images']);
                $imagesone = array();
                $imagestwo = array();
                if (!empty($ima[0])) {
                    foreach ($ima as $image) {
                        $imgUrl = str_replace('adpost/predefined/', '', $image);
                        $is_file = base_path() . '/storage/app/public/adpost/appdetail/' . $imgUrl;
                        if (is_file($is_file)) {
                            $imagesone[] = URL::to('storage/adpost/predefined/' . $imgUrl);
                        } else {
                            $imagestwo[] = URL::to('storage/adpost/predefined/' . $imgUrl);
                        }
                    }
                } else {
                    $imagesone[] = URL::to('storage/noimage150.png');
                }
                $images = array_merge($imagesone, $imagestwo);
                $review_info = array();
                $pending_review_info = array();
                $favs = "";
                $favs = TblSavedPosts::where('user_id', $user)->where('post_id', $id)->get(['id']);
                $currency = $this->post_currency($postinfo['id']);
                $additional_data = $this->getAdditionalInfo($id);
                $others = $this->getAdditionalInfoOthers($id, $postinfo['category_id']);
                $iscar = ($postinfo['category_id'] == 64) ? true : false;
                $featuresdata = [];
                $token = $this->getBearerToken();
                if (!empty($token['code']) && ($token['code'] == 200)) {
                    $user = $this->getLoggedUser($token['token']);
                    if (!empty($user)) {
                        $lan_code = User::where('id', $user)->value('preferred_language');
                    } else {
                        $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                        $response = [
                            'success' => false,
                            'code' => 0,
                            'message' => "Invalid User"
                        ];
                    }
                } else {
                    $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                }
                $additional_info = array();
                foreach ($additional_data as $additional_data) {
                    $label = Languages::where('lang_code', $lan_code)->where('lang_org_text', $additional_data['label'])->value('lang_text');
                    $additional_info[] = array(
                        'lable' => !empty($label) ? $label : $additional_data['label'],
                        'value' => $additional_data['value']
                    );
                }
                $features_info = array();
                foreach ($others as $others_data) {
                    if ($category_name == 'Cars') {
                        $label = Languages::where('lang_code', $lan_code)
                            ->where('lang_org_text', $others_data['label'])
                            ->value('lang_text');
                        $translated_label = !empty($label) ? $label : $others_data['label'];
                        $value_data = $others_data['value'];
                        $translated_values = [];
                        if (is_array($value_data)) {
                            foreach ($value_data as &$val_data) {
                                if (isset($val_data['label'])) {
                                    $val_label = Languages::where('lang_code', $lan_code)
                                        ->where('lang_org_text', $val_data['label'])
                                        ->value('lang_text');
                                    $val_data['label'] = !empty($val_label) ? $val_label : $val_data['label'];
                                }
                            }
                            $translated_values = $value_data;
                        } else {
                            $translated_values = Languages::where('lang_code', $lan_code)
                                ->where('lang_org_text', $value_data)
                                ->value('lang_text') ?? $value_data;
                        }
                        $features_info[] = [
                            'label' => $translated_label,
                            'value' => $translated_values
                        ];
                    } else {
                        $label = Languages::where('lang_code', $lan_code)->where('lang_org_text', $others_data['label'])->value('lang_text');
                        $features_info[] = array(
                            'label' => !empty($label) ? $label : $others_data['label'],
                            'value' => $others_data['value']
                        );
                    }
                }
                $getPackType = TblPayment::where('post_id', $postinfo['id'])->get(['package_id']);
                $ad_type = "";
                if ($getPackType->count() > 0) {
                    $packageID = $getPackType[0]->package_id;
                    $ad_type = Package::where('id', $packageID)->pluck('ad_type')->toArray();
                }
                $final_city_name = !empty($postinfo['locality']) ? $postinfo['locality'] : $city_name;
                $full_city_name = !empty($postinfo['completeAddress']) ? $postinfo['completeAddress'] : $final_city_name;
                $product_condition = TblPost::get_product_condition($postinfo['id']);
                // FIXED: Check if $show_mobile exists before accessing properties
                $show_mobile = User_profile::where('user_id', $postinfo['user_id'])->first(['show_mobile', 'phone', 'allow_call']);
                $phonenumber = "";
                $allow_call = 0;

                // Check if record exists
                if ($show_mobile) {
                    $allow_call = $show_mobile->allow_call ?? 0;
                    if ($show_mobile->show_mobile == 1) {
                        $phonenumber = $show_mobile->phone ?? "";
                    }
                } else {
                    // If no user_profile record exists, create a default one
                    $show_mobile = User_profile::firstOrCreate(
                        ['user_id' => $postinfo['user_id']],
                        [
                            'show_mobile' => 0,
                            'phone' => '',
                            'allow_call' => 0,
                            'country_code' => '', // Add other required fields
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
                    $allow_call = 0;
                }
                //posted user is company or not
                $seller_checking = Verificationrequest::where('user_id', $postinfo['user_id'])->value('is_company');
                $verify_id = Verificationrequest::where('user_id', $postinfo['user_id'])->value('id');
                $has_shop = BusinessProfile::where('verifcation_id', $verify_id)->exists();
                if ($has_shop == true) {
                    $shop_profile = 'yes';
                } else {
                    $shop_profile = 'no';
                }
                if ($seller_checking == null) {
                    $seller_checking = 'no';
                }
                $brand_logo = "";
                $brand_name = "";
                $brand_created_at = "";
                $user_name = $user_info[0]->name;
                if ($seller_checking == 'Yes' || $seller_checking == 'yes') {
                    $verification = Verificationrequest::where('user_id', $postinfo['user_id'])->first();
                    $is_company = $verification->is_company;
                    $verication_id = $verification->id;
                    $datas = BusinessProfile::where('verifcation_id', $verication_id)->first();
                    if (!empty($datas)) {
                        $brand_logo = (!empty($datas->brand_logo)) ? URL::to('storage/business-profile/' . $datas->brand_logo) : URL::asset('storage/profile-avatar.jpg');
                        $brand_created_at = TblChat::timeAgo($datas->created_at, $lan_code) . " On JustreUsed";
                        $brand_name = $datas->brand_name;
                        $user_name = $datas->brand_name;
                        $profile = (!empty($datas->brand_logo)) ? URL::to('storage/business-profile/' . $datas->brand_logo) : URL::asset('storage/profile-avatar.jpg');
                    } else {
                        $brand_logo = URL::asset('storage/profile-avatar.jpg');
                        $brand_created_at = "";
                        $brand_name = "";
                        $user_name = $user_info[0]->name;
                        $profile = !empty($user_info[0]->profile_photo_path) ? URL::to('storage/' . $user_info[0]->profile_photo_path) : URL::to('storage/profile-avatar.jpg');
                    }
                } else {
                    $profile = !empty($user_info[0]->profile_photo_path) ? URL::to('storage/' . $user_info[0]->profile_photo_path) : URL::to('storage/profile-avatar.jpg');
                }
                $get_address_cnt = TblShippingAddress::where('user_id', $user)->count();
                $post_views_cnt = TblPostInsight::views_count($postinfo['id']);
                $created_at = TblChat::timeAgo($postinfo['created_at'], $lan_code);
                $likecount = TblPost::get_likes_count($postinfo['id']);
                $comments_count = TblPost::get_comments_count($postinfo['id']);
                $price = $postinfo['price'];
                $symbol = $currency;
                Carbon::setLocale($lan_code);
                if (isset($ad_type[0]) && !empty($ad_type)) {
                    $trans_ad_type = Languages::where('lang_code', $lan_code)->where('lang_org_text', $ad_type[0])->value('lang_text');
                }
                $user_chat_sts = Languages::where('lang_code', $lan_code)->where('lang_org_text', $user_info[0]->current_chat_status)->value('lang_text');
                $likes = TblLikeAndDislikePost::where('post_id', $postinfo['id'])->where('likes', 1)->count();
                $dislikes = TblLikeAndDislikePost::where('post_id', $postinfo['id'])->where('dislikes', 1)->count();
                $user_likes = TblLikeAndDislikePost::where('user_id', $user)->where('post_id', $id)->where('likes', 1)->get(['id']);
                $is_exist = TblLikeAndDislikePost::where('user_id', $user)->where('post_id', $id)->exists();
                if ($is_exist) {
                    if (!empty($user_likes) && ($user_likes->count() > 0)) {
                        $is_like = 1;
                    } else {
                        $is_like = 2;
                    }
                } else {
                    $is_like = 0;
                }
                $user_profile = User_profile::where('user_id', $postinfo['user_id'])->first();
                $country_code = $user_profile->country_code;
                $data = array(
                    'post_id' => $postinfo['id'],
                    'title' => $postinfo['title'],
                    'slug' => $postinfo['slug'],
                    'show_number' => $postinfo['show_number'],
                    'price' => number_format($price, 2),
                    'description' => $postinfo['description'],
                    'city_name' => $full_city_name,
                    'posted_by_id' => $postinfo['user_id'],
                    'posted_by' => $user_name,
                    'posted_by_user_profle' => $profile,
                    'user_joined_on' => \Carbon\Carbon::parse($user_info[0]->created_at)->isoFormat('DD MMM YYYY'),
                    'email' => $user_info[0]->email,
                    'post_user_mobile' => $user_info[0]->phone,
                    'country_code' => $country_code,
                    'phone' => $phonenumber,
                    'iscompany' => $seller_checking,
                    'shop_profile' => $shop_profile,
                    'company_logo' => $brand_logo,
                    'company_name' => $brand_name,
                    'company_joins' => $brand_created_at,
                    'user_status' => $user_chat_sts,
                    'allow_call' => $show_mobile->allow_call,
                    'category_name' => $category_name,
                    'created_at' => $created_at,
                    'star_rate' => round($avg_rating),
                    'images' => $images,
                    'is_fav' => !empty($favs) && ($favs->count() > 0) ? true : false,
                    'currency_sybmol' => $symbol,
                    'custom_fields' => $additional_info,
                    'other_features' => !empty($features_info) ? $features_info : "",
                    'ad_type' => !empty($trans_ad_type) ? $trans_ad_type : "",
                    'logged_user_id' => !empty($user) ? $user : "",
                    'sold_status' => $postinfo['sold_status'],
                    'video_url' => !empty($postinfo['video_url']) ? $postinfo['video_url'] : "",
                    'is_exchange' => $postinfo['exchange_to_buy'],  // 0 - no exchange , 1 - exchange available
                    'product_condition' => !empty($product_condition) ? $product_condition : "",
                    'giving_away' => $postinfo['giving_away'], // 0 - no 1 - yes
                    'fixed_price' => $postinfo['fixed_price'], // 0 no 1 yes
                    'buy_now' => $postinfo['instant_buy'], // 0 no 1 yes
                    'address_count' => $get_address_cnt,
                    'views_count' => $post_views_cnt,
                    'like_count' => $likecount,
                    'comment_count' => $comments_count,
                    'latitude' => $lati,
                    'longitude' => $longi,
                    'likes' => $likes,
                    'dislikes' => $dislikes,
                    'is_liked' => $is_like, //!empty($user_likes) && ($user_likes->count() > 0) ? true : false,
                    // 'complete_address' =>$full_city_name,
                    'iscar' => $iscar,
                );
                foreach ($reviews as $review) {
                    if ($review->approved == 1) {
                        $review_info[] = array(
                            'id' => $review->id,
                            'post_id' => $review->post_id,
                            'posted_by' => $review->name,
                            'ratings' => (float) $review->ratings, // Cast to float
                            'comment' => $review->comment,
                            'created_at' => \Carbon\Carbon::parse($review->created_at)->isoFormat('DD MMM YYYY'),
                            'approved' => 1,
                            'profile_image' => !empty($review->profile_photo_path) ? URL::to('storage/' . $review->profile_photo_path) : URL::to('storage/profile-avatar.jpg'),
                        );
                    } else if (($review->approved != 1) && !empty($user) && ($user == $review->user_id)) {
                        $pending_review_info[] = array(
                            'id' => $review->id,
                            'post_id' => $review->post_id,
                            'posted_by' => $review->name,
                            'ratings' => $review->ratings,
                            'comment' => $review->comment,
                            'created_at' => \Carbon\Carbon::parse($review->created_at)->isoFormat('DD MMM YYYY'), //date('d M Y', strtotime($review->created_at)),
                            'approved' => 0,
                            'profile_image' => !empty($review->profile_photo_path) ? URL::to('storage/' . $review->profile_photo_path) : URL::to('storage/profile-avatar.jpg'),
                        );
                    }
                }

                $avg_rating = TblSellerReviews::rate_avg($postinfo['user_id']);
                $checkExist = TblReview::where('post_id', $id)->where('user_id', $user)->get();
                $post = TblPost::where('id', $id)->first();
                if ($checkExist->count() == 0) {
                    if ($post['user_id'] == $user) {
                        $check_review = false;
                    } else {
                        $check_review = true;
                    }
                } else {
                    $check_review = false;
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'post_info' => $data,
                    // 'seller_more_items' => $seller_more_items,
                    'review_info' => array_merge($review_info, $pending_review_info),
                    // 'related_post' => $related_pinfo,
                    'seller_review_list' => round($avg_rating),
                    'check_review' => $check_review
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 401,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $postinfo = TblPost::where('id', $id)->first()->toArray();
            $post_latlong = TblCity::where('id', $postinfo['city'])->get(['latitude', 'logitude'])->first();
            $lati = $post_latlong->latitude;
            $longi = $post_latlong->logitude;
            $city_name = TblPost::getPostloc($postinfo['city']);
            $category_name = TblCategory::find($postinfo['category_id'])->title;
            $avg_rating = TblReview::rate_avg($id);
            $related_posts = TblPost::get_related_products($postinfo['category_id'], $id);
            // $user_info = User::leftjoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')->where('users.id', $postinfo['user_id'])->get(['users.*', 'user_profiles.phone']);
            $user_info = User::where('id', $postinfo['user_id'])->get(['name', 'email', 'profile_photo_path', 'phone', 'created_at']);
            $reviews = TblReview::join('users', 'tbl_reviews.user_id', '=', 'users.id')->get(['tbl_reviews.*', 'users.name', 'users.profile_photo_path'])->where('post_id', $id);
            $data = array();
            $images = array();
            /* Post images */
            $ima = explode(',', $postinfo['images']);
            $imagesone = array();
            $imagestwo = array();
            if (!empty($ima[0])) {
                foreach ($ima as $image) {
                    $imgUrl = str_replace('adpost/predefined/', '', $image);
                    $is_file = base_path() . '/storage/app/public/adpost/appdetail/' . $imgUrl;
                    if (is_file($is_file)) {
                        $imagesone[] = URL::to('storage/adpost/appdetail/' . $imgUrl);
                    } else {
                        $imagestwo[] = URL::to('storage/adpost/predefined/' . $imgUrl);
                    }
                }
            } else {
                $imagesone[] = URL::to('storage/noimage150.png');
            }
            $images = array_merge($imagesone, $imagestwo);
            $review_info = array();
            $pending_review_info = array();
            $favs = "";
            // $favs = TblSavedPosts::where('user_id', $user)->where('post_id', $id)->get(['id']);
            $currency = $this->post_currency($postinfo['id']);
            $additional_data = $this->getAdditionalInfo($id);
            $others = $this->getAdditionalInfoOthers($id, $postinfo['category_id']);
            $iscar = ($postinfo['category_id'] == 64) ? true : false;
            $featuresdata = [];
            $token = $this->getBearerToken();
            if (!empty($token['code']) && ($token['code'] == 200)) {
                $user = $this->getLoggedUser($token['token']);
                if (!empty($user)) {
                    // dd($user);
                    $lan_code = User::where('id', $user)->value('preferred_language');
                } else {
                    $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => "Invalid User"
                    ];
                }
            } else {
                $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
            }
            $additional_info = array();
            foreach ($additional_data as $additional_data) {
                $label = Languages::where('lang_code', $lan_code)->where('lang_org_text', $additional_data['label'])->value('lang_text');
                $additional_info[] = array(
                    'lable' => !empty($label) ? $label : $additional_data['label'],
                    'value' => $additional_data['value']
                );
            }
            $getPackType = TblPayment::where('post_id', $postinfo['id'])->get(['package_id']);
            $ad_type = "";
            if ($getPackType->count() > 0) {
                $packageID = $getPackType[0]->package_id;
                $ad_type = Package::where('id', $packageID)->pluck('ad_type')->toArray();
            }
            $final_city_name = !empty($postinfo['locality']) ? $postinfo['locality'] : $city_name;
            $full_city_name = !empty($postinfo['completeAddress']) ? $postinfo['completeAddress'] : $final_city_name;
            $product_condition = TblPost::get_product_condition($postinfo['id']);
            $show_mobile = User_profile::where('user_id', $postinfo['user_id'])->get(['show_mobile', 'phone', 'allow_call'])->first();
            $phonenumber = "";
            if ($show_mobile->show_mobile == 1) {
                $phonenumber = $show_mobile->phone;
            }
            //posted user is company or not
            $seller_checking = Verificationrequest::where('user_id', $postinfo['user_id'])->value('is_company');
            $posted_by = $user_info[0]->name;
            $verify_id = Verificationrequest::where('user_id', $postinfo['user_id'])->value('id');
            $has_shop = BusinessProfile::where('verifcation_id', $verify_id)->exists();
            if ($has_shop == true) {
                $shop_profile = 'yes';
            } else {
                $shop_profile = 'no';
            }
            if ($seller_checking == null) {
                $seller_checking = 'no';
            }
            $brand_logo = "";
            $brand_name = "";
            $brand_created_at = "";
            if ($seller_checking == 'Yes' || $seller_checking == 'yes') {
                $verification = Verificationrequest::where('user_id', $postinfo['user_id'])->first();
                $is_company = $verification->is_company;
                $verication_id = $verification->id;
                $datas = BusinessProfile::where('verifcation_id', $verication_id)->first();
                if (!empty($datas)) {
                    $brand_logo = (!empty($datas->brand_logo)) ? URL::to('storage/business-profile/' . $datas->brand_logo) : URL::asset('storage/profile-avatar.jpg');
                    $brand_created_at = TblChat::timeAgo($datas->created_at, $lan_code) . " On JustreUsed";
                    $brand_name = $datas->brand_name;
                    $posted_by = $datas->brand_name;
                    $profile = (!empty($datas->brand_logo)) ? URL::to('storage/business-profile/' . $datas->brand_logo) : URL::asset('storage/profile-avatar.jpg');
                } else {
                    $brand_logo = URL::asset('storage/profile-avatar.jpg');
                    $brand_name = "";
                    $brand_created_at = "";
                    $posted_by = $user_info[0]->name;
                    $profile = !empty($user_info[0]->profile_photo_path) ? URL::to('storage/' . $user_info[0]->profile_photo_path) : URL::to('storage/profile-avatar.jpg');
                }
            } else {
                $profile = !empty($user_info[0]->profile_photo_path) ? URL::to('storage/' . $user_info[0]->profile_photo_path) : URL::to('storage/profile-avatar.jpg');
            }
            // $get_address_cnt = TblShippingAddress::where('user_id', $user)->count();
            $post_views_cnt = TblPostInsight::views_count($postinfo['id']);
            $created_at = TblChat::timeAgo($postinfo['created_at'], $lan_code);
            //$check_post_package = TblPost::check_post_expired($postinfo['id']);
            $likecount = TblPost::get_likes_count($postinfo['id']);
            $comments_count = TblPost::get_comments_count($postinfo['id']);
            if (!empty($user)) {
                $post_currency_id = TblPost::where('id', $postinfo['id'])->value('currency_id');
                $user_currency = TblPost::userCurrencyConversion($user, $postinfo['price'], $post_currency_id);
                $price = $user_currency['convert_cur'];
                $symbol = $user_currency['convert_sym'];
            } else {
                $price = $postinfo['price'];
                $symbol = $currency;
            }
            Carbon::setLocale($lan_code);
            if (isset($ad_type[0]) && !empty($ad_type)) {
                $trans_ad_type = Languages::where('lang_code', $lan_code)->where('lang_org_text', $ad_type[0])->value('lang_text');
            }
            $likes = TblLikeAndDislikePost::where('post_id', $postinfo['id'])->where('likes', 1)->count();
            $dislikes = TblLikeAndDislikePost::where('post_id', $postinfo['id'])->where('dislikes', 1)->count();
            $data = array(
                'post_id' => $postinfo['id'],
                'title' => $postinfo['title'],
                'slug' => $postinfo['slug'],
                'show_number' => $postinfo['show_number'],
                'price' => number_format($postinfo['price'], 2),
                'description' => $postinfo['description'],
                'city_name' => $full_city_name,
                'posted_by_id' => $postinfo['user_id'],
                'posted_by' => $posted_by,
                // 'posted_by_user_profle' => !empty($user_info[0]->profile_photo_path) ? URL::to('storage/' . $user_info[0]->profile_photo_path) : URL::to('storage/profile-avatar.jpg'),
                'posted_by_user_profle' => $profile,
                'user_joined_on' => \Carbon\Carbon::parse($user_info[0]->created_at)->isoFormat('DD MMM YYYY'), //date('d M Y', strtotime($user_info[0]->created_at)),
                'email' => $user_info[0]->email,
                'post_user_mobile' => $user_info[0]->phone,
                'phone' => $phonenumber,
                'iscompany' => $seller_checking,
                'shop_profile' => $shop_profile,
                'company_logo' => $brand_logo,
                'company_name' => $brand_name,
                'company_joins' => $brand_created_at,
                'allow_call' => $show_mobile->allow_call,
                'category_name' => $category_name,
                'created_at' => $created_at,
                'star_rate' => round($avg_rating),
                'images' => $images,
                // 'is_fav' => !empty($favs) && ($favs->count() > 0) ? true : false,
                'currency_sybmol' => $currency,
                'custom_fields' => $additional_info,
                'other_features' => !empty($others) ? $others : "",
                'ad_type' => !empty($ad_type[0]) ? $trans_ad_type : "",
                'logged_user_id' => !empty($user) ? $user : "",
                'sold_status' => $postinfo['sold_status'],
                'video_url' => !empty($postinfo['video_url']) ? $postinfo['video_url'] : "",
                'is_exchange' => $postinfo['exchange_to_buy'],  // 0 - no exchange , 1 - exchange available
                'product_condition' => !empty($product_condition) ? $product_condition : "",
                'giving_away' => $postinfo['giving_away'], // 0 - no 1 - yes
                'fixed_price' => $postinfo['fixed_price'], // 0 no 1 yes
                'buy_now' => $postinfo['instant_buy'], // 0 no 1 yes
                // 'address_count' => $get_address_cnt,
                'views_count' => $post_views_cnt,
                'like_count' => $likecount,
                'comment_count' => $comments_count,
                'latitude' => $lati,
                'longitude' => $longi,
                'likes' => $likes,
                'dislikes' => $dislikes,
                'iscar' => $iscar,
            );
            //seller more items 
            $seller_more_items = array();
            $seller_single_info = "";
            /* get unexpired payment post */
            $payment_ids_array = TblPost::get_unexpired_payment_post_ids();
            /* get unexpired free post */
            $free_ids_array = TblPost::get_unexpired_free_post_ids();
            $unexpired_posts = array_merge($payment_ids_array, $free_ids_array);
            $get_seller_items = TblPost::where('user_id', $postinfo['user_id'])->where('id', '!=', $postinfo['id'])->whereIn('id', $unexpired_posts)->whereNull('deleted_at')->where('active', 1)->limit(20)->get();
            foreach ($get_seller_items as $get_seller_item) {
                $seller_single_info = TblPost::get_single_post_information($get_seller_item['id']);
                $currency_symbol = $this->post_currency($get_seller_item['id']);
                $favpst = "";
                // $favpst = TblSavedPosts::where('user_id', $user)->where('post_id', $get_seller_item['id'])->get();
                $additional_data = $this->getAdditionalInfo($get_seller_item['id']);
                $token = $this->getBearerToken();
                if (!empty($token['code']) && ($token['code'] == 200)) {
                    $user = $this->getLoggedUser($token['token']);
                    if (!empty($user)) {
                        // dd($user);
                        $lan_code = User::where('id', $user)->value('preferred_language');
                    } else {
                        $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                        $response = [
                            'success' => false,
                            'code' => 0,
                            'message' => "Invalid User"
                        ];
                    }
                } else {
                    $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                }
                $additional_info = array();
                foreach ($additional_data as $additional_data) {
                    $label = Languages::where('lang_code', $lan_code)->where('lang_org_text', $additional_data['label'])->value('lang_text');
                    $additional_info[] = array(
                        'lable' => !empty($label) ? $label : $additional_data['label'],
                        'value' => $additional_data['value']
                    );
                }
                //seller logo  start
                $seller_logo = '';
                $seller_brand = '';
                $seller = TblPost::where('id', $get_seller_item['id'])->value('user_id');
                $verify_id = Verificationrequest::where('user_id', $get_seller_item['id'])->value('id');
                $has_shop = BusinessProfile::where('verifcation_id', $verify_id)->exists();
                if ($has_shop == true) {
                    $shop_profile = 'yes';
                } else {
                    $shop_profile = 'no';
                }
                $seller_check = Verificationrequest::where('user_id', $seller)->first();
                if (!empty($seller_check)) {
                    if ($seller_check->is_company == 'yes') {
                        $seller_logo = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_logo');
                        $seller_brand = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_name');
                    }
                }
                $brand_logo = !empty($seller_logo) ? URL::to('storage/business-profile/thumb50/' . $seller_logo) : URL::asset('storage/profile-avatar.jpg');
                //seller logo end
                $rcity_name = TblPost::getPostloc($get_seller_item['city']);
                $final_rcity_name = !empty($get_seller_item['locality']) ? $get_seller_item['locality'] : $rcity_name;
                $ad_type = TblPost::getAddtype($get_seller_item['id']);
                if (isset($ad_type->ad_type) && !empty($ad_type->ad_type)) {
                    $trans_ad_type1 = Languages::where('lang_code', $lan_code)->where('lang_org_text', $ad_type->ad_type)->value('lang_text');
                } else {
                    $trans_ad_type1 = "";
                }
                $seller_more_items[] = array(
                    'id' => $get_seller_item['id'],
                    'title' => $get_seller_item['title'],
                    'price' => number_format($get_seller_item['price'], 2),
                    'description' => $get_seller_item['description'],
                    'created_at' => \Carbon\Carbon::parse($get_seller_item['created_at'])->isoFormat('DD MMM YYYY'), //date('d M Y', strtotime($get_seller_item['created_at'])),
                    'images' => $seller_single_info['images'],
                    'ad_type' => !empty($trans_ad_type1) ? $trans_ad_type1 : "",
                    'city_name' => $final_rcity_name,
                    'custom_fields' => $additional_info,
                    'shop_profile' => $shop_profile,
                    'brand_name' => $seller_brand,
                    'brand_logo' => $brand_logo,
                    'sellerId' => $seller,
                    'currency_symbol' => $currency_symbol,
                    // 'is_fav' => !empty($favpst) && ($favpst->count() > 0) ? true : false,
                    'giving_away' => $get_seller_item['giving_away']
                );
            }
            foreach ($reviews as $review) {
                if ($review->approved == 1) {
                    $review_info[] = array(
                        'id' => $review->id,
                        'post_id' => $review->post_id,
                        'posted_by' => $review->name,
                        'ratings' => $review->ratings,
                        'comment' => $review->comment,
                        'created_at' => \Carbon\Carbon::parse($review->created_at)->isoFormat('DD MMM YYYY'), //date('d M Y', strtotime($review->created_at)),
                        'approved' => 1,
                        'profile_image' => !empty($review->profile_photo_path) ? URL::to('storage/' . $review->profile_photo_path) : URL::to('storage/profile-avatar.jpg'),
                    );
                } else if (($review->approved != 1) && !empty($user) && ($user == $review->user_id)) {
                    $pending_review_info[] = array(
                        'id' => $review->id,
                        'post_id' => $review->post_id,
                        'posted_by' => $review->name,
                        'ratings' => $review->ratings,
                        'comment' => $review->comment,
                        'created_at' => \Carbon\Carbon::parse($review->created_at)->isoFormat('DD MMM YYYY'), //date('d M Y', strtotime($review->created_at)),
                        'approved' => 0,
                        'profile_image' => !empty($review->profile_photo_path) ? URL::to('storage/' . $review->profile_photo_path) : URL::to('storage/profile-avatar.jpg'),
                    );
                }
            }
            /* Related Post */
            $related_pinfo = array();
            $related_single_info = "";
            foreach ($related_posts as $related_post) {
                $userExistCheck = User::where('id', $related_post['user_id'])->get()->count();
                if ($userExistCheck > 0) {
                    $related_single_info = TblPost::get_single_post_information($related_post['id']);
                    $currency_symbol = $this->post_currency($related_post['id']);
                    $favpst = "";
                    // $favpst = TblSavedPosts::where('user_id', $user)->where('post_id', $related_post['id'])->get();
                    $rcity_name = TblPost::getPostloc($related_post['city']);
                    $final_rcity_name = !empty($related_post['locality']) ? $related_post['locality'] : $rcity_name;
                    $additional_data = $this->getAdditionalInfo($related_post['id']);
                    $token = $this->getBearerToken();
                    if (!empty($token['code']) && ($token['code'] == 200)) {
                        $user = $this->getLoggedUser($token['token']);
                        if (!empty($user)) {
                            // dd($user);
                            $lan_code = User::where('id', $user)->value('preferred_language');
                        } else {
                            $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                            $response = [
                                'success' => false,
                                'code' => 0,
                                'message' => "Invalid User"
                            ];
                        }
                    } else {
                        $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                    }
                    $additional_info = array();
                    foreach ($additional_data as $additional_data) {
                        $label = Languages::where('lang_code', $lan_code)->where('lang_org_text', $additional_data['label'])->value('lang_text');
                        $additional_info[] = array(
                            'lable' => !empty($label) ? $label : $additional_data['label'],
                            'value' => $additional_data['value']
                        );
                    }
                    //seller logo  start
                    $seller_logo = '';
                    $seller_brand = '';
                    $seller = TblPost::where('id', $related_post['id'])->value('user_id');
                    $verify_id = Verificationrequest::where('user_id', $related_post['id'])->value('id');
                    $has_shop = BusinessProfile::where('verifcation_id', $verify_id)->exists();
                    if ($has_shop == true) {
                        $shop_profile = 'yes';
                    } else {
                        $shop_profile = 'no';
                    }
                    $seller_check = Verificationrequest::where('user_id', $seller)->first();
                    if (!empty($seller_check)) {
                        if ($seller_check->is_company == 'yes') {
                            $seller_logo = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_logo');
                            $seller_brand = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_name');
                        }
                    }
                    $brand_logo = !empty($seller_logo) ? URL::to('storage/business-profile/thumb50/' . $seller_logo) : URL::asset('storage/profile-avatar.jpg');
                    //seller logo end
                    $ad_type = TblPost::getAddtype($related_post['id']);
                    if (!empty($user)) {
                        $post_currency_id = TblPost::where('id', $related_post['id'])->value('currency_id');
                        $user_currency = TblPost::userCurrencyConversion($user, $related_post['price'], $post_currency_id);
                        $price = $user_currency['convert_cur'];
                        $symbol = $user_currency['convert_sym'];
                    } else {
                        $price = $related_post['price'];
                        $symbol = $currency_symbol;
                    }
                    if (isset($ad_type->ad_type) && !empty($ad_type->ad_type)) {
                        $trans_ad_type1 = Languages::where('lang_code', $lan_code)->where('lang_org_text', $ad_type->ad_type)->value('lang_text');
                    } else {
                        $trans_ad_type2 = "";
                    }
                    $related_pinfo[] = array(
                        'id' => $related_post['id'],
                        'title' => $related_post['title'],
                        'price' => number_format($related_post['price'], 2),
                        'description' => $related_post['description'],
                        'created_at' => \Carbon\Carbon::parse($related_post['created_at'])->isoFormat('DD MMM YYYY'), //date('d M Y', strtotime($related_post['created_at'])),
                        'images' => $related_single_info['images'],
                        'custom_fields' => $additional_info,
                        'shop_profile' => $shop_profile,
                        'ad_type' => !empty($trans_ad_type2) ? $trans_ad_type2 : "",
                        'city_name' => $final_rcity_name,
                        'brand_name' => $seller_brand,
                        'brand_logo' => $brand_logo,
                        'sellerId' => $seller,
                        'currency_symbol' => $currency_symbol,
                        // 'is_fav' => !empty($favpst) && ($favpst->count() > 0) ? true : false,
                        'giving_away' => $related_post['giving_away']
                    );
                }
            }
            $avg_rating = TblSellerReviews::rate_avg($postinfo['user_id']);
            $check_review = false;
            $response = [
                'success' => true,
                'code' => 200,
                'post_info' => $data,
                'seller_more_items' => $seller_more_items,
                'review_info' => array_merge($review_info, $pending_review_info),
                'related_post' => $related_pinfo,
                'seller_review_list' => round($avg_rating),
                'check_review' => $check_review
            ];
        }
        return response()->json($response);
    }
    public function post_detail_get($id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                /* Related Post */
                $related_pinfo = array();
                $related_single_info = "";
                $postinfo = TblPost::with([
                    'city:id,latitude,logitude',
                    'category:id,title',
                    'user:id,name,email,profile_photo_path,phone,created_at,current_chat_status',
                    'reviews.user:id,name,profile_photo_path'
                ])
                    ->where('id', $id)
                    ->first();
                $related_posts = TblPost::get_related_products($postinfo['category_id'], $id);
                foreach ($related_posts as $related_post) {
                    $userExistCheck = User::where('id', $related_post['user_id'])->get()->count();
                    if ($userExistCheck > 0) {
                        $related_single_info = TblPost::get_single_post_information($related_post['id']);
                        $currency_symbol = $this->post_currency($related_post['id']);
                        $favpst = "";
                        $favpst = TblSavedPosts::where('user_id', $user)->where('post_id', $related_post['id'])->get();
                        $rcity_name = TblPost::getPostloc($related_post['city']);
                        $final_rcity_name = !empty($related_post['locality']) ? $related_post['locality'] : $rcity_name;
                        $additional_data = $this->getAdditionalInfo($related_post['id']);
                        $token = $this->getBearerToken();
                        if (!empty($token['code']) && ($token['code'] == 200)) {
                            $user = $this->getLoggedUser($token['token']);
                            if (!empty($user)) {
                                // dd($user);
                                $lan_code = User::where('id', $user)->value('preferred_language');
                            } else {
                                $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                                $response = [
                                    'success' => false,
                                    'code' => 0,
                                    'message' => "Invalid User"
                                ];
                            }
                        } else {
                            $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                        }
                        $additional_info = array();
                        foreach ($additional_data as $additional_data) {
                            $label = Languages::where('lang_code', $lan_code)->where('lang_org_text', $additional_data['label'])->value('lang_text');
                            $additional_info[] = array(
                                'lable' => !empty($label) ? $label : $additional_data['label'],
                                'value' => $additional_data['value']
                            );
                        }
                        //seller logo  start
                        $seller_logo = '';
                        $seller_brand = '';
                        $seller = TblPost::where('id', $related_post['id'])->value('user_id');
                        $verify_id = Verificationrequest::where('user_id', $related_post['id'])->value('id');
                        $has_shop = BusinessProfile::where('verifcation_id', $verify_id)->exists();
                        if ($has_shop == true) {
                            $shop_profile = 'yes';
                        } else {
                            $shop_profile = 'no';
                        }
                        $seller_check = Verificationrequest::where('user_id', $seller)->first();
                        if (!empty($seller_check)) {
                            if ($seller_check->is_company == 'yes') {
                                $seller_logo = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_logo');
                                $seller_brand = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_name');
                            }
                        }
                        $brand_logo = !empty($seller_logo) ? URL::to('storage/business-profile/thumb50/' . $seller_logo) : URL::asset('storage/profile-avatar.jpg');
                        //seller logo end
                        $ad_type = TblPost::getAddtype($related_post['id']);
                        // if (!empty($user)) {
                        //     $post_currency_id = TblPost::where('id', $related_post['id'])->value('currency_id');
                        //     $user_currency = TblPost::userCurrencyConversion($user, $related_post['price'], $post_currency_id);
                        //     $price = $user_currency['convert_cur'];
                        //     $symbol = $user_currency['convert_sym'];
                        // } else {
                        //     $price = $related_post['price'];
                        //     $symbol = $currency_symbol;
                        // }
                        $price = $related_post['price'];
                        $symbol = $currency_symbol;
                        if (isset($ad_type->ad_type) && !empty($ad_type->ad_type)) {
                            $trans_ad_type2 = Languages::where('lang_code', $lan_code)->where('lang_org_text', $ad_type->ad_type)->value('lang_text');
                        } else {
                            $trans_ad_type2 = "";
                        }
                        $related_pinfo[] = array(
                            'id' => $related_post['id'],
                            'title' => $related_post['title'],
                            'price' => number_format($price, 2),
                            'description' => $related_post['description'],
                            'created_at' => \Carbon\Carbon::parse($related_post['created_at'])->isoFormat('DD MMM YYYY'), // date('d M Y', strtotime($related_post['created_at'])),
                            'images' => $related_single_info['images'],
                            'custom_fields' => $additional_info,
                            'ad_type' => !empty($ad_type->ad_type) ? $trans_ad_type2 : "",
                            'city_name' => $final_rcity_name,
                            'brand_name' => $seller_brand,
                            'brand_logo' => $brand_logo,
                            'sellerId' => $seller,
                            'shop_profile' => $shop_profile,
                            'currency_symbol' => $symbol,
                            'is_fav' => !empty($favpst) && ($favpst->count() > 0) ? true : false,
                            'giving_away' => $related_post['giving_away']
                        );
                    }
                }
                //seller more items 
                $seller_more_items = array();
                $seller_single_info = "";
                /* get unexpired payment post */
                $payment_ids_array = TblPost::get_unexpired_payment_post_ids();
                /* get unexpired free post */
                $free_ids_array = TblPost::get_unexpired_free_post_ids();
                $unexpired_posts = array_merge($payment_ids_array, $free_ids_array);
                $get_seller_items = TblPost::where('user_id', $postinfo['user_id'])->where('id', '!=', $postinfo['id'])->whereIn('id', $unexpired_posts)->whereNull('deleted_at')->where('active', 1)->limit(20)->get();
                foreach ($get_seller_items as $get_seller_item) {
                    $seller_single_info = TblPost::get_single_post_information($get_seller_item['id']);
                    $currency_symbol = $this->post_currency($get_seller_item['id']);
                    $favpst = "";
                    $favpst = TblSavedPosts::where('user_id', $user)->where('post_id', $get_seller_item['id'])->get();
                    $additional_data = $this->getAdditionalInfo($get_seller_item['id']);
                    $token = $this->getBearerToken();
                    if (!empty($token['code']) && ($token['code'] == 200)) {
                        $user = $this->getLoggedUser($token['token']);
                        if (!empty($user)) {
                            // dd($user);
                            $lan_code = User::where('id', $user)->value('preferred_language');
                        } else {
                            $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                            $response = [
                                'success' => false,
                                'code' => 0,
                                'message' => "Invalid User"
                            ];
                        }
                    } else {
                        $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                    }
                    $additional_info = array();
                    foreach ($additional_data as $additional_data) {
                        $label = Languages::where('lang_code', $lan_code)->where('lang_org_text', $additional_data['label'])->value('lang_text');
                        $additional_info[] = array(
                            'lable' => !empty($label) ? $label : $additional_data['label'],
                            'value' => $additional_data['value']
                        );
                    }
                    //seller logo  start
                    $seller_logo = '';
                    $seller_brand = '';
                    $seller = TblPost::where('id', $get_seller_item['id'])->value('user_id');
                    $verify_id = Verificationrequest::where('user_id', $get_seller_item['id'])->value('id');
                    $has_shop = BusinessProfile::where('verifcation_id', $verify_id)->exists();
                    if ($has_shop == true) {
                        $shop_profile = 'yes';
                    } else {
                        $shop_profile = 'no';
                    }
                    $seller_check = Verificationrequest::where('user_id', $seller)->first();
                    if (!empty($seller_check)) {
                        if ($seller_check->is_company == 'yes') {
                            $seller_logo = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_logo');
                            $seller_brand = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_name');
                        }
                    }
                    $brand_logo = !empty($seller_logo) ? URL::to('storage/business-profile/thumb50/' . $seller_logo) : URL::asset('storage/profile-avatar.jpg');
                    //seller logo end
                    $rcity_name = TblPost::getPostloc($get_seller_item['city']);
                    $final_rcity_name = !empty($get_seller_item['locality']) ? $get_seller_item['locality'] : $rcity_name;
                    $ad_type = TblPost::getAddtype($get_seller_item['id']);
                    // if (!empty($user)) {
                    //     $post_currency_id = TblPost::where('id', $get_seller_item['id'])->value('currency_id');
                    //     $user_currency = TblPost::userCurrencyConversion($user, $get_seller_item['price'], $post_currency_id);
                    //     $price = $user_currency['convert_cur'];
                    //     $symbol = $user_currency['convert_sym'];
                    // } else {
                    //     $price = $get_seller_item['price'];
                    //     $symbol = $currency_symbol;
                    // }
                    $price = $get_seller_item['price'];
                    $symbol = $currency_symbol;
                    if (isset($ad_type->ad_type) && !empty($ad_type->ad_type)) {
                        $trans_ad_type1 = Languages::where('lang_code', $lan_code)->where('lang_org_text', $ad_type->ad_type)->value('lang_text');
                    } else {
                        $trans_ad_type1 = "";
                    }
                    $seller_more_items[] = array(
                        'id' => $get_seller_item['id'],
                        'title' => $get_seller_item['title'],
                        'price' => number_format($price, 2),
                        'description' => $get_seller_item['description'],
                        'created_at' => \Carbon\Carbon::parse($get_seller_item['created_at'])->isoFormat('DD MMM YYYY'), //date('d M Y', strtotime($get_seller_item['created_at'])),
                        'images' => $seller_single_info['images'],
                        'ad_type' => !empty($ad_type->ad_type) ? $trans_ad_type1 : "",
                        'city_name' => $final_rcity_name,
                        'brand_name' => $seller_brand,
                        'brand_logo' => $brand_logo,
                        'sellerId' => $seller,
                        'shop_profile' => $shop_profile,
                        'custom_fields' => $additional_info,
                        'currency_symbol' => $symbol,
                        'is_fav' => !empty($favpst) && ($favpst->count() > 0) ? true : false,
                        'giving_away' => $get_seller_item['giving_away']
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'seller_more_items' => $seller_more_items,
                    'related_post' => $related_pinfo
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 401,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!"
            ];
        }
        return response()->json($response);
    }
    /* Get post detail by slug value */
    public function posts_detail($slug)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $postinfo = TblPost::where('slug', $slug)->first()->toArray();
                $id = TblPost::where('slug', $slug)->value('id');
                $post_latlong = TblCity::where('id', $postinfo['city'])->get(['latitude', 'logitude'])->first();
                $lati = $post_latlong->latitude;
                $longi = $post_latlong->logitude;
                $city_name = TblPost::getPostloc($postinfo['city']);
                $category_name = TblCategory::find($postinfo['category_id'])->title;
                $avg_rating = TblReview::rate_avg($id);
                $related_posts = TblPost::get_related_products($postinfo['category_id'], $id);
                // $user_info = User::leftjoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')->where('users.id', $postinfo['user_id'])->get(['users.*', 'user_profiles.phone']);
                $user_info = User::where('id', $postinfo['user_id'])->get(['name', 'email', 'profile_photo_path', 'created_at']);
                $reviews = TblReview::join('users', 'tbl_reviews.user_id', '=', 'users.id')->get(['tbl_reviews.*', 'users.name', 'users.profile_photo_path'])->where('post_id', $id);
                $data = array();
                $images = array();
                /* Post images */
                $ima = explode(',', $postinfo['images']);
                $imagesone = array();
                $imagestwo = array();
                if (!empty($ima[0])) {
                    foreach ($ima as $image) {
                        $imgUrl = str_replace('adpost/predefined/', '', $image);
                        $is_file = base_path() . '/storage/app/public/adpost/appdetail/' . $imgUrl;
                        if (is_file($is_file)) {
                            $imagesone[] = URL::to('storage/adpost/appdetail/' . $imgUrl);
                        } else {
                            $imagestwo[] = URL::to('storage/adpost/predefined/' . $imgUrl);
                        }
                    }
                } else {
                    $imagesone[] = URL::to('storage/noimage150.png');
                }
                $images = array_merge($imagesone, $imagestwo);
                $review_info = array();
                $pending_review_info = array();
                $favs = "";
                $favs = TblSavedPosts::where('user_id', $user)->where('post_id', $id)->get(['id']);
                $currency = $this->post_currency($postinfo['id']);
                $additional_data = $this->getAdditionalInfo($id);
                //dd($additional_data);
                $additional_info = array();
                foreach ($additional_data as $additional_data) {
                    $additional_info[] = array(
                        'lable' => $additional_data['label'],
                        'value' => $additional_data['value']
                    );
                }
                $getPackType = TblPayment::where('post_id', $postinfo['id'])->get(['package_id']);
                $ad_type = "";
                if ($getPackType->count() > 0) {
                    $packageID = $getPackType[0]->package_id;
                    $ad_type = Package::where('id', $packageID)->pluck('ad_type')->toArray();
                }
                $final_city_name = !empty($postinfo['locality']) ? $postinfo['locality'] : $city_name;
                $product_condition = TblPost::get_product_condition($postinfo['id']);
                $show_mobile = User_profile::where('user_id', $postinfo['user_id'])->get(['show_mobile', 'phone', 'allow_call'])->first();
                $phonenumber = "";
                if ($show_mobile->show_mobile == 1) {
                    $phonenumber = $show_mobile->phone;
                }
                $get_address_cnt = TblShippingAddress::where('user_id', $user)->count();
                $post_views_cnt = TblPostInsight::views_count($postinfo['id']);
                $created_at = TblChat::timeAgo($postinfo['created_at']);
                //$check_post_package = TblPost::check_post_expired($postinfo['id']);
                $likecount = TblPost::get_likes_count($postinfo['id']);
                $comments_count = TblPost::get_comments_count($postinfo['id']);
                $data = array(
                    'post_id' => $postinfo['id'],
                    'title' => $postinfo['title'],
                    'slug' => $postinfo['slug'],
                    'price' => $postinfo['price'],
                    'description' => $postinfo['description'],
                    'city_name' => $final_city_name,
                    'posted_by_id' => $postinfo['user_id'],
                    'posted_by' => $user_info[0]->name,
                    'posted_by_user_profle' => !empty($user_info[0]->profile_photo_path) ? URL::to('storage/' . $user_info[0]->profile_photo_path) : URL::to('storage/profile-avatar.jpg'),
                    'user_joined_on' => date('d M Y', strtotime($user_info[0]->created_at)),
                    'email' => $user_info[0]->email,
                    'phone' => $phonenumber,
                    'allow_call' => $show_mobile->allow_call,
                    'category_name' => $category_name,
                    'created_at' => $created_at,
                    'star_rate' => round($avg_rating),
                    'images' => $images,
                    'is_fav' => !empty($favs) && ($favs->count() > 0) ? true : false,
                    'currency_sybmol' => $currency,
                    'custom_fields' => $additional_info,
                    'ad_type' => !empty($ad_type[0]) ? str_replace('_', ' ', strtoupper($ad_type[0])) : "",
                    'logged_user_id' => !empty($user) ? $user : "",
                    'sold_status' => $postinfo['sold_status'],
                    'video_url' => !empty($postinfo['video_url']) ? $postinfo['video_url'] : "",
                    'is_exchange' => $postinfo['exchange_to_buy'],  // 0 - no exchange , 1 - exchange available
                    'product_condition' => !empty($product_condition) ? $product_condition : "",
                    'giving_away' => $postinfo['giving_away'], // 0 - no 1 - yes
                    'fixed_price' => $postinfo['fixed_price'], // 0 no 1 yes
                    'buy_now' => $postinfo['instant_buy'], // 0 no 1 yes
                    'address_count' => $get_address_cnt,
                    'views_count' => $post_views_cnt,
                    'like_count' => $likecount,
                    'comment_count' => $comments_count,
                    'latitude' => $lati,
                    'longitude' => $longi
                );
                //seller more items 
                $seller_more_items = array();
                $seller_single_info = "";
                $get_seller_items = TblPost::where('user_id', $postinfo['user_id'])->where('id', '!=', $postinfo['id'])->whereNull('deleted_at')->where('active', 1)->limit(20)->get();
                foreach ($get_seller_items as $get_seller_item) {
                    $seller_single_info = TblPost::get_single_post_information($get_seller_item['id']);
                    $currency_symbol = $this->post_currency($get_seller_item['id']);
                    $favpst = "";
                    $favpst = TblSavedPosts::where('user_id', $user)->where('post_id', $get_seller_item['id'])->get();
                    $rcity_name = TblPost::getPostloc($get_seller_item['city']);
                    $final_rcity_name = !empty($get_seller_item['locality']) ? $get_seller_item['locality'] : $rcity_name;
                    $ad_type = TblPost::getAddtype($get_seller_item['id']);
                    if (!empty($user)) {
                        $post_currency_id = TblPost::where('id', $get_seller_item['id'])->value('currency_id');
                        $user_currency = TblPost::userCurrencyConversion($user, $get_seller_item['price'], $post_currency_id);
                        $price = $user_currency['convert_cur'];
                        $symbol = $user_currency['convert_sym'];
                    } else {
                        $price = $get_seller_item['price'];
                        $symbol = $currency_symbol;
                    }
                    $seller_more_items[] = array(
                        'post_id' => $get_seller_item['id'],
                        'title' => $get_seller_item['title'],
                        'price' => $get_seller_item['price'],
                        'description' => $get_seller_item['description'],
                        'created_at' => date('d M Y', strtotime($get_seller_item['created_at'])),
                        'image' => $seller_single_info['images'],
                        'ad_type' => !empty($ad_type->ad_type) ? str_replace('_', ' ', strtoupper($ad_type->ad_type)) : "",
                        'city_name' => $final_rcity_name,
                        'currency_symbol' => $currency_symbol,
                        'is_fav' => !empty($favpst) && ($favpst->count() > 0) ? true : false,
                        'giving_away' => $get_seller_item['giving_away']
                    );
                }
                foreach ($reviews as $review) {
                    if ($review->approved == 1) {
                        $review_info[] = array(
                            'id' => $review->id,
                            'post_id' => $review->post_id,
                            'posted_by' => $review->name,
                            'ratings' => $review->ratings,
                            'comment' => $review->comment,
                            'created_at' => date('d M Y', strtotime($review->created_at)),
                            'approved' => 1,
                            'profile_image' => !empty($review->profile_photo_path) ? URL::to('storage/' . $review->profile_photo_path) : URL::to('storage/profile-avatar.jpg'),
                        );
                    } else if (($review->approved != 1) && !empty($user) && ($user == $review->user_id)) {
                        $pending_review_info[] = array(
                            'id' => $review->id,
                            'post_id' => $review->post_id,
                            'posted_by' => $review->name,
                            'ratings' => $review->ratings,
                            'comment' => $review->comment,
                            'created_at' => date('d M Y', strtotime($review->created_at)),
                            'approved' => 0,
                            'profile_image' => !empty($review->profile_photo_path) ? URL::to('storage/' . $review->profile_photo_path) : URL::to('storage/profile-avatar.jpg'),
                        );
                    }
                }
                /* Related Post */
                $related_pinfo = array();
                $related_single_info = "";
                foreach ($related_posts as $related_post) {
                    $userExistCheck = User::where('id', $related_post['user_id'])->get()->count();
                    if ($userExistCheck > 0) {
                        $related_single_info = TblPost::get_single_post_information($related_post['id']);
                        $currency_symbol = $this->post_currency($related_post['id']);
                        $favpst = "";
                        $favpst = TblSavedPosts::where('user_id', $user)->where('post_id', $related_post['id'])->get();
                        $rcity_name = TblPost::getPostloc($related_post['city']);
                        $final_rcity_name = !empty($related_post['locality']) ? $related_post['locality'] : $rcity_name;
                        $ad_type = TblPost::getAddtype($related_post['id']);
                        $related_pinfo[] = array(
                            'post_id' => $related_post['id'],
                            'title' => $related_post['title'],
                            'price' => $related_post['price'],
                            'description' => $related_post['description'],
                            'created_at' => date('d M Y', strtotime($related_post['created_at'])),
                            'image' => $related_single_info['images'],
                            'ad_type' => !empty($ad_type->ad_type) ? str_replace('_', ' ', strtoupper($ad_type->ad_type)) : "",
                            'city_name' => $final_rcity_name,
                            'currency_symbol' => $currency_symbol,
                            'is_fav' => !empty($favpst) && ($favpst->count() > 0) ? true : false,
                            'giving_away' => $related_post['giving_away']
                        );
                    }
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'post_info' => $data,
                    'seller_more_items' => $seller_more_items,
                    'review_info' => array_merge($review_info, $pending_review_info),
                    'related_post' => $related_pinfo
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
            // $postinfo = TblPost::where('id', $id)->first()->toArray();
            // $post_latlong = TblCity::where('id', $postinfo['city'])->get(['latitude','logitude'])->first();
            // $lati = $post_latlong->latitude;
            // $longi = $post_latlong->logitude;
            // $city_name = TblPost::getPostloc($postinfo['city']);
            // $category_name = TblCategory::find($postinfo['category_id'])->title;
            // $avg_rating = TblReview::rate_avg($id);
            // $related_posts = TblPost::get_related_products($postinfo['category_id'], $id);
            // // $user_info = User::leftjoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')->where('users.id', $postinfo['user_id'])->get(['users.*', 'user_profiles.phone']);
            // $user_info = User::where('id', $postinfo['user_id'])->get(['name','email','profile_photo_path','created_at']);
            // $reviews = TblReview::join('users', 'tbl_reviews.user_id', '=', 'users.id')->get(['tbl_reviews.*', 'users.name', 'users.profile_photo_path'])->where('post_id', $id);
            // $data = array();
            // $images = array();
            // /* Post images */
            // $ima = explode(',', $postinfo['images']);
            // $imagesone = array();
            // $imagestwo = array();
            // if (!empty($ima[0])) {
            //     foreach ($ima as $image) {
            //         $imgUrl = str_replace('adpost/predefined/', '', $image);
            //         $is_file = base_path() . '/storage/app/public/adpost/appdetail/' . $imgUrl;
            //         if (is_file($is_file)) {
            //             $imagesone[] = URL::to('storage/adpost/appdetail/' . $imgUrl);
            //         } else {
            //             $imagestwo[] = URL::to('storage/adpost/predefined/' . $imgUrl);
            //         }
            //     }
            // }else{
            //     $imagesone[] = URL::to('storage/noimage150.png');
            // }
            // $images = array_merge($imagesone, $imagestwo);
            // $review_info = array();
            // $pending_review_info = array();
            // $favs = "";
            // // $favs = TblSavedPosts::where('user_id', $user)->where('post_id', $id)->get(['id']);
            // $currency = $this->post_currency($postinfo['id']);
            // $additional_data = $this->getAdditionalInfo($id);
            // //dd($additional_data);
            // $additional_info = array();
            // foreach ($additional_data as $additional_data) {
            //     $additional_info[] = array(
            //        'lable' => $additional_data['label'],
            //         'value' => $additional_data['value']
            //     );
            // }
            // $getPackType = TblPayment::where('post_id', $postinfo['id'])->get(['package_id']);
            // $ad_type = "";
            // if ($getPackType->count() > 0) {
            //     $packageID = $getPackType[0]->package_id;
            //     $ad_type = Package::where('id', $packageID)->pluck('ad_type')->toArray();
            // }
            // $final_city_name = !empty($postinfo['locality']) ? $postinfo['locality'] : $city_name;
            // $product_condition = TblPost::get_product_condition($postinfo['id']);
            // $show_mobile = User_profile::where('user_id', $postinfo['user_id'])->get(['show_mobile','phone','allow_call'])->first();
            // $phonenumber = "";
            // if ($show_mobile->show_mobile == 1) {
            //     $phonenumber = $show_mobile->phone;
            // }
            // // $get_address_cnt = TblShippingAddress::where('user_id', $user)->count();
            // $post_views_cnt = TblPostInsight::views_count($postinfo['id']);
            // $created_at = TblChat::timeAgo($postinfo['created_at']);
            // //$check_post_package = TblPost::check_post_expired($postinfo['id']);
            // $likecount = TblPost::get_likes_count($postinfo['id']);
            // $comments_count = TblPost::get_comments_count($postinfo['id']);
            // $data = array(
            //     'post_id' => $postinfo['id'],
            //     'title' => $postinfo['title'],
            //     'slug' => $postinfo['slug'],
            //     'price' => $postinfo['price'],
            //     'description' => $postinfo['description'],
            //     'city_name' => $final_city_name,
            //     'posted_by_id' => $postinfo['user_id'],
            //     'posted_by' => $user_info[0]->name,
            //     'posted_by_user_profle' => !empty($user_info[0]->profile_photo_path) ? URL::to('storage/' . $user_info[0]->profile_photo_path) : URL::to('storage/profile-avatar.jpg'),
            //     'user_joined_on' => date('d M Y', strtotime($user_info[0]->created_at)),
            //     'email' => $user_info[0]->email,
            //     'phone' => $phonenumber,
            //     'allow_call' => $show_mobile->allow_call,
            //     'category_name' => $category_name,
            //     'created_at' => $created_at,
            //     'star_rate' => round($avg_rating),
            //     'images' => $images,
            //     // 'is_fav' => !empty($favs) && ($favs->count() > 0) ? true : false,
            //     'currency_sybmol' => $currency,
            //     'custom_fields' => $additional_info,
            //     'ad_type' => !empty($ad_type[0]) ? str_replace('_', ' ', strtoupper($ad_type[0])) : "",
            //     'logged_user_id' => !empty($user) ? $user : "",
            //     'sold_status' => $postinfo['sold_status'],
            //     'video_url' => !empty($postinfo['video_url']) ? $postinfo['video_url'] : "",
            //     'is_exchange' => $postinfo['exchange_to_buy'],  // 0 - no exchange , 1 - exchange available
            //     'product_condition' => !empty($product_condition) ? $product_condition : "",
            //     'giving_away' => $postinfo['giving_away'], // 0 - no 1 - yes
            //     'fixed_price' => $postinfo['fixed_price'], // 0 no 1 yes
            //     'buy_now' => $postinfo['instant_buy'], // 0 no 1 yes
            //     // 'address_count' => $get_address_cnt,
            //     'views_count' => $post_views_cnt,
            //     'like_count' => $likecount,
            //     'comment_count' => $comments_count,
            //     'latitude' => $lati,
            //     'longitude' => $longi
            // );
            // //seller more items 
            // $seller_more_items = array();
            // $seller_single_info = "";
            // $get_seller_items = TblPost::where('user_id', $postinfo['user_id'])->where('id', '!=', $postinfo['id'])->whereNull('deleted_at')->where('active', 1)->limit(20)->get();
            // foreach ($get_seller_items as $get_seller_item) {
            //     $seller_single_info = TblPost::get_single_post_information($get_seller_item['id']);
            //     $currency_symbol = $this->post_currency($get_seller_item['id']);
            //     $favpst = "";
            //     // $favpst = TblSavedPosts::where('user_id', $user)->where('post_id', $get_seller_item['id'])->get();
            //     $rcity_name = TblPost::getPostloc($get_seller_item['city']);
            //     $final_rcity_name = !empty($get_seller_item['locality']) ? $get_seller_item['locality'] : $rcity_name;
            //     $ad_type = TblPost::getAddtype($get_seller_item['id']);
            //     $seller_more_items[] = array(
            //         'post_id' => $get_seller_item['id'],
            //         'title' => $get_seller_item['title'],
            //         'price' => $get_seller_item['price'],
            //         'created_at' => date('d M Y', strtotime($get_seller_item['created_at'])),
            //         'image' => $seller_single_info['images'],
            //         'ad_type' => !empty($ad_type->ad_type) ? str_replace('_', ' ', strtoupper($ad_type->ad_type)) : "",
            //         'city_name' => $final_rcity_name,
            //         'currency_symbol' => $currency_symbol,
            //         // 'is_fav' => !empty($favpst) && ($favpst->count() > 0) ? true : false,
            //         'giving_away' => $get_seller_item['giving_away']
            //     );
            // }
            // foreach ($reviews as $review) {
            //     if ($review->approved == 1) {
            //         $review_info[] = array(
            //             'id' => $review->id,
            //             'post_id' => $review->post_id,
            //             'posted_by' => $review->name,
            //             'ratings' => $review->ratings,
            //             'comment' => $review->comment,
            //             'created_at' => date('d M Y', strtotime($review->created_at)),
            //             'approved' => 1,
            //             'profile_image' => !empty($review->profile_photo_path) ? URL::to('storage/' . $review->profile_photo_path) : URL::to('storage/profile-avatar.jpg'),
            //         );
            //     } else if (($review->approved != 1)) {
            //         $pending_review_info[] = array(
            //             'id' => $review->id,
            //             'post_id' => $review->post_id,
            //             'posted_by' => $review->name,
            //             'ratings' => $review->ratings,
            //             'comment' => $review->comment,
            //             'created_at' => date('d M Y', strtotime($review->created_at)),
            //             'approved' => 0,
            //             'profile_image' => !empty($review->profile_photo_path) ? URL::to('storage/' . $review->profile_photo_path) : URL::to('storage/profile-avatar.jpg'),
            //         );
            //     }
            // }
            // /* Related Post */
            // $related_pinfo = array();
            // $related_single_info = "";
            // foreach ($related_posts as $related_post) {
            //     $userExistCheck = User::where('id',$related_post['user_id'])->get()->count();
            //     if($userExistCheck>0)
            //     {
            //         $related_single_info = TblPost::get_single_post_information($related_post['id']);
            //         $currency_symbol = $this->post_currency($related_post['id']);
            //         $favpst = "";
            //         // $favpst = TblSavedPosts::where('user_id', $user)->where('post_id', $related_post['id'])->get();
            //         $rcity_name = TblPost::getPostloc($related_post['city']);
            //         $final_rcity_name = !empty($related_post['locality']) ? $related_post['locality'] : $rcity_name;
            //         $ad_type = TblPost::getAddtype($related_post['id']);
            //         $related_pinfo[] = array(
            //             'post_id' => $related_post['id'],
            //             'title' => $related_post['title'],
            //             'price' => $related_post['price'],
            //             'created_at' => date('d M Y', strtotime($related_post['created_at'])),
            //             'image' => $related_single_info['images'],
            //             'ad_type' => !empty($ad_type->ad_type) ? str_replace('_', ' ', strtoupper($ad_type->ad_type)) : "",
            //             'city_name' => $final_rcity_name,
            //             'currency_symbol' => $currency_symbol,
            //             // 'is_fav' => !empty($favpst) && ($favpst->count() > 0) ? true : false,
            //             'giving_away' => $related_post['giving_away']
            //         );
            //     }
            // }
            // $response = [
            //     'success' => true,
            //     'code' => 200,
            //     'post_info' => $data,
            //     'seller_more_items' => $seller_more_items,
            //     'review_info' => array_merge($review_info, $pending_review_info),
            //     'related_post' => $related_pinfo
            // ];
        }
        return response()->json($response);
    }
    /* Get custome filed info based on category - for post detail display */
    public function getAdditionalInfo($post_id)
    {
        $arraydet = array();
        $post_detail = TblPostValue::where('post_id', $post_id)->where('active', '1')->get();
        $i = 1;
        foreach ($post_detail as $j) {
            $i++;
            $field_id = $j['field_id'];
            $post_value = $j['value'];
            $tbl_fields = TblFieldsDetail::where('id', $field_id)->first();
            if (!$tbl_fields) {
                // Skip known system fields that aren't in tbl_fields_detail
                $system_fields = ['category', 'sub', 'currency', 'latitude', 'longitude', 'custom'];
                if (!in_array($field_id, $system_fields)) {
                    // Log the missing field ID for debugging (only for non-system fields)
                    \Log::warning("Missing field detail for ID: " . $field_id);
                }
                continue;
            }
            $tbl_fields_type = $tbl_fields->type;
            $tbl_fields_label = $tbl_fields->name;
            if ($tbl_fields_type == "select" || $tbl_fields == "autocomplete") {
                if ($tbl_fields->form_field_name == "brandwithmodel") {
                    $tbl_fields_label = "Brand & Model";
                    $brand_id = explode(',', $post_value)[0];
                    $get_options = TblFieldsOption::where('id', 'Like', '%' . $brand_id . '%')->pluck('key')->first();
                    // dd(Str::title(str_replace('-', ' ', explode(',', $post_value)[1])),$get_options);
                    // dd(explode(',', $post_value)[1]);
                    // $val1  = explode(',', $post_value)[1];
                    // $model = Str::title(str_replace('-', ' ', $val1));
                    // dd($model);
                    // $post_value = $get_options . ', ' . $model;
                    $model = "";
                    if (!empty(explode(',', $post_value)[1]) && isset(explode(',', $post_value)[1])) {
                        $model = Str::title(str_replace('-', ' ', explode(',', $post_value)[1]));
                        ;
                        $post_value = $get_options . ', ' . Str::title(str_replace('-', ' ', explode(',', $post_value)[1]));
                    } else {
                        $post_value = $get_options;
                    }
                }
                // else {
                //     $get_options = TblFieldsOption::where('field_id', $field_id)->where('value', $post_value)->get();
                //     $post_value = $get_options[0]->key;
                // }
            }
            if ($tbl_fields_type == "checkbox-group") {
                $checkedvalues = "";
                $post_value = explode(',', $post_value);
                foreach ($post_value as $k) {
                    $get_options = TblFieldsOption::where('field_id', $field_id)->where('value', $k)->pluck('key')->first();
                    if (!empty($get_options)) {
                        $checkedvalues .= $get_options . ",";
                    }
                }
                $post_value = rtrim($checkedvalues, ',');
            }
            $arraydet[] = array('type' => $tbl_fields_type, 'label' => $tbl_fields_label, 'value' => $post_value);
            if ($tbl_fields_label == 'Year') {
                $year = $post_value;
            }
        }
        // if (isset($brand_id) && isset($model) && isset($year)) {
        //     if (!empty($brand_id) && !empty($model) && !empty($year)) {
        //         if (str_contains($model, 'Audi')) {
        //             $model = trim(str_replace("Audi", "", $model));
        //         }
        //         $features = Feature::where('brand_id', $brand_id)->where('model', 'Like', '%' . $model . '%')->where('year_from', $year)->first();
        //         if (!empty($features)) {
        //             $other_features = $features->other_features;
        //         }
        //     }
        // }
        return $arraydet;
    }
    public function formatString($value)
    {
        // Step 1: Replace underscores with spaces
        $value = str_replace('_', ' ', $value);
        // Step 2: Capitalize each word
        $value = ucwords($value);
        // Step 3: Ensure the final 'l' remains lowercase
        $value = preg_replace('/\bL\b/', 'l', $value); // Make sure 'L' becomes 'l'
        return $value;
    }
    public function getAdditionalInfoOthers($post_id, $category_id)
    {
        $arraydet = array();
        $post_detail = TblPostValue::where('post_id', $post_id)->where('active', '1')->get();
        $i = 1;
        $features = array();
        $query_data = [];
        $brand = "";
        $model = "";
        $breed = "";
        $label_name = "";
        foreach ($post_detail as $j) {
            $i++;
            $field_id = $j['field_id'];
            $post_value = $j['value'];
            $tbl_fields = TblFieldsDetail::where('id', $field_id)->first();
            if (!$tbl_fields) {
                // Skip known system fields that aren't in tbl_fields_detail
                $system_fields = ['category', 'sub', 'currency', 'latitude', 'longitude', 'custom'];
                if (!in_array($field_id, $system_fields)) {
                    // Log the missing field ID for debugging (only for non-system fields)
                    \Log::warning("Missing field detail in getAdditionalInfoOthers for ID: " . $field_id);
                }
                continue;
            }
            $tbl_fields_type = $tbl_fields->type;
            $tbl_fields_label = $tbl_fields->name;
            if ($tbl_fields_type == "select" || $tbl_fields == "autocomplete") {
                if ($tbl_fields->form_field_name == "brandwithmodel") {
                    $tbl_fields_label = "Brand & Model";
                    $brand_id = explode(',', $post_value)[0];
                    $get_options = TblFieldsOption::where('id', 'Like', '%' . $brand_id . '%')->pluck('key')->first();
                    // dd(Str::title(str_replace('-', ' ', explode(',', $post_value)[1])),$get_options);
                    // dd(explode(',', $post_value)[1]);
                    // $val1  = explode(',', $post_value)[1];
                    // $model = Str::title(str_replace('-', ' ', $val1));
                    // dd($model);
                    // $post_value = $get_options . ', ' . $model;
                    $model = "";
                    $brand = $get_options;
                    if (!empty(explode(',', $post_value)[1]) && isset(explode(',', $post_value)[1])) {
                        $model = Str::title(str_replace('-', ' ', explode(',', $post_value)[1]));
                        ;
                        $post_value = $get_options . ', ' . Str::title(str_replace('-', ' ', explode(',', $post_value)[1]));
                    } else {
                        $post_value = $get_options;
                    }
                } else {
                    $get_options = TblFieldsOption::where('field_id', $field_id)->where('value', $post_value)->get();
                    if ($tbl_fields_label == "Dog Breed Group") {
                        $breed = TblFieldsOption::where('field_id', $field_id)->where('value', $post_value)->value('key');
                    }
                    if ($tbl_fields_label == "Label Or Name") {
                        $label_name = TblFieldsOption::where('field_id', $field_id)->where('value', $post_value)->value('key');
                    }
                    if (!empty($get_options[0]->key) && isset($get_options[0]->key)) {
                        $post_value = $get_options[0]->key;
                    }
                }
            }
            if ($tbl_fields_type == "checkbox-group") {
                $checkedvalues = "";
                $post_value = explode(',', $post_value);
                foreach ($post_value as $k) {
                    $get_options = TblFieldsOption::where('field_id', $field_id)->where('value', $k)->pluck('key')->first();
                    if (!empty($get_options)) {
                        $checkedvalues .= $get_options . ",";
                    }
                }
                $post_value = rtrim($checkedvalues, ',');
            }
            $arraydet[] = array('type' => $tbl_fields_type, 'label' => $tbl_fields_label, 'value' => $post_value);
        }
        // Push the $brand, $model, $breed, and $label_name values into $query_data array
        $query_data = [
            'brand' => $brand,
            'model' => $model,
            'breed' => $breed,
            'label_name' => $label_name
        ];
        $getFeatures = $this->getFeatures($query_data, $category_id);
        return $getFeatures;
    }
    public function getFeatures(array $query_data, $category_id)
    {
        // Start building the query
        $featureMap = FeaturesMappingGroup::where('cat_id', $category_id)->orderBy('list_order', "asc")->get();
        $featureItems = array();
        foreach ($featureMap as $featuresMap) {
            $featureItems[$featuresMap->features_title][] = explode(',', $featuresMap->features_items);
        }
        // print_r($query_data);exit;
        if (!empty($query_data['brand']) && !empty($query_data['model'])) {
            $query = Feature::where('cat_id', $category_id)->where('make', $query_data['brand'])->where('model', $query_data['model']);
        } elseif (!empty($query_data['label_name'])) {
            $query = Feature::where('cat_id', $category_id)->where('label_name', $query_data['label_name']);
        } elseif (!empty($query_data['breed'])) {
            $query = Feature::where('cat_id', $category_id)->where('dog_breed_group', $query_data['breed']);
        }
        $features = "";
        if (isset($query) && $query->count() > 0) {
            // Fetch the results from the database
            $features = $query->first()->toArray();
        }
        $featuresdata = array();
        $others_fea = array();
        if (!empty($features)) {
            // Convert the 'other_features' JSON string into an array
            $other_features = json_decode($features['other_features'], true);
            if ($category_id == '64') {
                // Loop through map values to match with features data
                foreach ($featureItems as $category => $fields) {
                    foreach ($fields as $fieldGroup) {
                        $categoryResults = [];
                        foreach ($fieldGroup as $field) {
                            // Check if the field exists in features data
                            if (isset($other_features[$field])) {
                                $categoryResults[$this->formatString($field)] = $other_features[$field];
                            }
                        }
                        // Add to result if there are matches for the category
                        if (!empty($categoryResults)) {
                            $featuresdata[$category] = $categoryResults;
                        }
                    }
                }
                foreach ($featuresdata as $f_key => $f_val) {
                    $valueEntry = array();
                    foreach ($f_val as $key => $val) {
                        $valueEntry[] = [
                            'label' => $key,
                            'value' => $val
                        ];
                    }
                    $entry = [
                        'label' => $f_key,
                        'value' => $valueEntry
                    ];
                    $others_fea[] = $entry;
                }
            } else {
                // Function to capitalize the first letter of array keys
                function capitalizeKeys($array)
                {
                    $newArray = [];
                    foreach ($array as $key => $value) {
                        // Capitalize the first letter of each key
                        $newKey = ucfirst($key);
                        // Recursively capitalize keys if the value is an array
                        if (is_array($value)) {
                            $newArray[$newKey] = capitalizeKeys($value);
                        } else {
                            $newArray[$newKey] = $value;
                        }
                    }
                    return $newArray;
                }
                // Separate 'other_features' into its own array and capitalize keys
                $others['OtherFeatures'] = capitalizeKeys($other_features);
                // Remove the original 'other_features' JSON string
                unset($others['other_features']);
                // Capitalize the rest of the keys in the main array
                $others = capitalizeKeys($others);
                foreach ($others as $key => $value) {
                    if ($key == 'OtherFeatures') {
                        foreach ($value as $f_key => $f_val) {
                            if (!is_numeric($f_key) && $f_key != 'Id Trim') {
                                $featuresdata[$this->formatString($f_key)] = $f_val;
                            }
                        }
                    } else {
                        if ($key != 'Id' && $key != 'Brand_id' && $key != 'Cat_id' && $key != 'Created_at' && $key != 'Updated_at') {
                            if (!is_numeric($key)) {
                                $featuresdata[$this->formatString($key)] = $value;
                            }
                        }
                    }
                }
                foreach ($featuresdata as $fea_key => $fea_val) {
                    $entry = [
                        'label' => $fea_key,
                        'value' => $fea_val
                    ];
                    $others_fea[] = $entry;
                }
            }
        }
        return $others_fea;
    }
    /* Add Post - by logged in user */
    public function total_post()
    {
        return TblPost::count() + 1;
    }
    public function add_post(Request $request)
    {
        // --- LOG START ---
        \Log::info("--- ADD POST API HIT ---");
        \Log::info("User ID asking: " . $request->user_id); // Agar param me hai to
        \Log::info("Raw Input Data:", $request->all());
        // -----------------

        // 1. Authenticate User
        $token = $this->getBearerToken();

        if (empty($token['code']) || $token['code'] != 200) {
            \Log::error("Add Post: Invalid Token");
            return response()->json($this->sendError("Invalid Authorization Bearer Token!"));
        }

        $user = $this->getLoggedUser($token['token']);
        if (empty($user)) {
            \Log::error("Add Post: User not found for token");
            return response()->json($this->sendError("Invalid User!"));
        }

        // 2. Validate Inputs
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'category_id' => 'required',
            'price' => 'required',
            'package_type' => 'required',
        ]);

        if ($validator->fails()) {
            \Log::error("Add Post Validation Fail: " . $validator->errors()->first());
            return response()->json($this->sendError($validator->errors()->first()));
        }

        // 3. Check Package Validity (Skip if 'free')
        $check_pack_info = null;
        if ($request->package_type != 'free') {
            $check_pack_info = Package::where('id', $request->package_type)->first();
            if (!$check_pack_info) {
                return response()->json($this->sendError("Invalid Package Type Selected."));
            }
        }

        // 4. Process Logic based on Package (Free vs Paid)
        if ($request->package_type == 'free' || ($check_pack_info && strtolower($check_pack_info->short_name) == "free")) {

            // If it's a real DB package, check limits
            if ($check_pack_info) {
                $check_with_cnt = $check_pack_info->single_pack_limit;
                $post_count = TblPostedAdPackageInfo::where('user_id', $user)->sum('publish_count');

                if ($post_count >= $check_with_cnt) {
                    return response()->json([
                        'success' => false,
                        'code' => 0,
                        'message' => "Max post count reached!"
                    ]);
                }
            }

            // Save Post
            try {
                \Log::info("Processing Free Post Insert for User: " . $user);
                $return_response = $this->add_post_to_db($request, $user);

                return response()->json([
                    'success' => true,
                    'code' => 200,
                    'message' => "Inserted successfully!",
                    'post_id' => $return_response['post_id']
                ]);
            } catch (\Exception $e) {
                \Log::error("Free Post Error: " . $e->getMessage());
                return response()->json($this->sendError("Error adding post: " . $e->getMessage()));
            }

        } else {
            // Paid Package Logic
            try {
                \Log::info("Processing Paid Post Insert for User: " . $user);
                $return_response = $this->add_post_to_db($request, $user);

                $currency_symbol = Setting::get_admin_default_currency();
                $currency_id = $currency_symbol['id'];

                $url = URL::to('/paypal-payment-process?pack_amt=' . $check_pack_info->price . '&cid=' . $currency_id . '&post_id=' . $return_response['post_id'] . '&live_days=' . $check_pack_info->duration . '&package_id=' . $check_pack_info->id . '&payment_type=paypal&coupon_id=&uid=' . $user . '&from_type=add-post');

                return response()->json([
                    'success' => true,
                    'code' => 100,
                    'post_id' => $return_response['post_id'],
                    'data' => $url
                ]);
            } catch (\Exception $e) {
                \Log::error("Paid Post Error: " . $e->getMessage());
                return response()->json($this->sendError("Error adding post: " . $e->getMessage()));
            }
        }
    }

    public function add_post_to_db($request, $user)
    {
        \Log::info("--- ENTERED add_post_to_db ---");

        $slug = Str::slug($request->title, "-");
        $alias_val = $slug . '-' . $this->total_post();

        // 1. Handle Location
        $locations = $this->insert_locations(
            $request->country_short ?? '',
            $request->country_long ?? '',
            $request->state_short ?? '',
            $request->state_long ?? '',
            $request->city_name ?? '',
            $request->locality ?? '',
            $request->latitude ?? 0,
            $request->logitude ?? 0
        );

        // 2. Handle Images (Full Logic Restored)
        $predefined_imgs = "";
        $settings = Setting::get_logos();

        if (!empty($request->images)) {
            \Log::info("Images found, starting processing...");
            $raw_images = $request->images;
            $images = [];

            // Check if array, or JSON string, or Comma separated
            if (is_array($raw_images)) {
                $images = $raw_images;
            } elseif (is_string($raw_images)) {
                $decoded = json_decode($raw_images, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $images = $decoded; // It was a JSON string ["..."]
                } else {
                    $images = explode(',', $raw_images); // It was comma separated
                }
            }

            $imagenamesArray = array();

            foreach ($images as $j) {
                if (is_array($j))
                    continue; // Skip if nested array

                $j = trim($j, '[]"'); // Clean up extra characters
                if (empty($j))
                    continue;

                $imgname = Str::random(15) . '.' . "jpg";
                $save_img_name = 'adpost/predefined/' . $imgname;

                // Handle Base64 header if present
                if (strpos($j, 'base64,') !== false) {
                    $img_parts = explode('base64,', $j);
                    $img = isset($img_parts[1]) ? $img_parts[1] : $j;
                } else {
                    $img = $j;
                }

                $img = str_replace(' ', '+', $img);
                $decoded_image = base64_decode($img);

                if ($decoded_image === false)
                    continue;

                try {
                    // Get Sizes
                    $imagesizeSet = Setting::get_image_size_settings();
                    $list_width = 222;
                    $list_height = 156;
                    $detail_width = 500;
                    $detail_height = 350;

                    if (isset($imagesizeSet['list']) && str_contains($imagesizeSet['list'], "*")) {
                        $list_size = explode('*', $imagesizeSet['list']);
                        $list_width = $list_size[0];
                        $list_height = $list_size[1];
                    }
                    if (isset($imagesizeSet['detail']) && str_contains($imagesizeSet['detail'], "*")) {
                        $detail_size = explode('*', $imagesizeSet['detail']);
                        $detail_width = $detail_size[0];
                        $detail_height = $detail_size[1];
                    }

                    // Save Normal
                    $path_web_original = "adpost/predefined/normal/" . $imgname;
                    $web_list_original = Image::make($decoded_image);
                    if (file_exists(public_path('storage/' . $settings['watermark']))) {
                        $web_list_original->insert(public_path('storage/' . $settings['watermark']), 'bottom-right', 10, 10);
                    }
                    Storage::put($path_web_original, (string) $web_list_original->encode());

                    // Save List
                    $path_web_list = "adpost/predefined/list/" . $imgname;
                    $web_list = Image::make($decoded_image)->resize($list_width, $list_height, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    if (file_exists(public_path('storage/' . $settings['watermark']))) {
                        $web_list->insert(public_path('storage/' . $settings['watermark']), 'bottom-right', 10, 10);
                    }
                    Storage::put($path_web_list, (string) $web_list->encode());

                    // Save Detail
                    $path = "adpost/predefined/" . $imgname;
                    $image = Image::make($decoded_image)->resize($detail_width, $detail_height, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    if (file_exists(public_path('storage/' . $settings['watermark']))) {
                        $image->insert(public_path('storage/' . $settings['watermark']), 'bottom-right', 10, 10);
                    }
                    Storage::put($path, (string) $image->encode());

                    // Save App List
                    $path_app_list = "adpost/applist/" . $imgname;
                    $app_list = Image::make($decoded_image);
                    if (file_exists(public_path('storage/' . $settings['watermark']))) {
                        $app_list->insert(public_path('storage/' . $settings['watermark']), 'bottom-right', 10, 10);
                    }
                    Storage::put($path_app_list, (string) $app_list->encode());

                    // Save App Detail
                    $path_app_detail = "adpost/appdetail/" . $imgname;
                    $app_detail = Image::make($decoded_image);
                    if (file_exists(public_path('storage/' . $settings['watermark']))) {
                        $app_detail->insert(public_path('storage/' . $settings['watermark']), 'bottom-right', 10, 10);
                    }
                    Storage::put($path_app_detail, (string) $app_detail->encode());

                    array_push($imagenamesArray, $save_img_name);

                } catch (\Exception $e) {
                    \Log::error("Image processing failed: " . $e->getMessage());
                }
            }
            $predefined_imgs = implode(',', $imagenamesArray);
            \Log::info("Images processed. Count: " . count($imagenamesArray));
        }

        $get_pack_info = null;
        if ($request->package_type != 'free') {
            $get_pack_info = Package::where('id', $request->package_type)->first();
        }

        // Active if 'free' (hardcoded) OR if package exists and short_name is 'free'
        $active_status = ($request->package_type == 'free' || ($get_pack_info && strtolower($get_pack_info->short_name) == "free")) ? 1 : 0;

        $cityNames = "";
        if (($request->city_name != "" && $request->locality != "") && ($request->city_name != $request->locality)) {
            $cityNames = $request->locality . ", " . $request->city_name;
        } elseif ($request->city_name == $request->locality) {
            $cityNames = $request->locality;
        }

        $price = $request->price;

        // 3. Create Post Record
        \Log::info("Creating TblPost Record...");
        $post_id = TblPost::create([
            'user_id' => $user,
            'category_id' => $request->category_id,
            'show_number' => $request->show_number ?? 0,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $price,
            'slug' => $alias_val,
            'city' => $locations['city_id'],
            'locality' => $cityNames,
            'images' => $predefined_imgs ?? "",
            'currency_id' => !empty($request->currency_id) ? $request->currency_id : $settings['default_currency'],
            'product_condition' => !empty($request->product_condition) ? $request->product_condition : "",
            'completeAddress' => !empty($request->completeAddress) ? $request->completeAddress : "",
            'exchange_to_buy' => !empty($request->is_exchange) ? $request->is_exchange : 0,
            'video_url' => !empty($request->product_video_url) ? $request->product_video_url : "",
            'active' => $active_status,
            'giving_away' => !empty($request->giving_away) ? $request->giving_away : 0,
            'fixed_price' => !empty($request->fixed_price) ? $request->fixed_price : 0,
            'instant_buy' => !empty($request->buy_now) ? $request->buy_now : 0,
            'shipping_rate' => !empty($request->shipping_rate) ? $request->shipping_rate : 0
        ])->id;

        \Log::info("TblPost Created with ID: " . $post_id);

        // ============================================================
        // 4. CUSTOM FIELDS SAVING LOGIC (WORKING VERSION)
        // ============================================================
        \Log::info("--- STARTING CUSTOM FIELDS SAVE ---");

        $custom_data_source = null;

        // Check 1: New JSON Structure 'custom_field'
        if ($request->filled('custom_field')) {
            $custom_data_source = $request->custom_field;
        }
        // Check 2: Fallback to old request->all() logic if 'custom_field' is missing
        else {
            $custom_data_source = $request->all();
        }

        // Decode if string
        if (is_string($custom_data_source)) {
            $custom_data_source = json_decode($custom_data_source, true);
        }

        if (is_array($custom_data_source)) {
            foreach ($custom_data_source as $key => $value) {

                // Skip known non-custom fields
                $skipfields = ['currency_id', 'shipping_rate', 'buy_now', 'fixed_price', 'giving_away', 'title', 'description', 'price', 'package_type', 'images', 'custom_field', 'user_id', 'show_number'];
                if (in_array($key, $skipfields))
                    continue;

                // Key logic: Extract ID (e.g., "1735409a" from "1735409a_select...")
                $field_parts = explode('_', $key);
                $field_id = $field_parts[0];

                // Validation
                if ($value === null || $value === "")
                    continue;

                // Save if field_id is present
                if (!empty($field_id)) {

                    // Handle Array Values
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    }

                    try {
                        TblPostValue::create([
                            'post_id' => $post_id,
                            'field_id' => $field_id,
                            'value' => $value,
                            'active' => 1,
                        ]);
                        // \Log::info("Saved Custom Field -> ID: $field_id");
                    } catch (\Exception $e) {
                        \Log::error("Failed to save Custom Field ($field_id): " . $e->getMessage());
                    }
                }
            }
        }

        \Log::info("--- CUSTOM FIELDS END ---");
        // ============================================================

        // 5. Package Info
        if ($request->package_type == 'free' && !$get_pack_info) {
            $living_days = 30; // Default 30 days for Free Ad if not config
        } else {
            $living_days = $get_pack_info->duration;
        }

        $curr_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime($curr_date . "+" . $living_days . " days"));

        TblPostedAdPackageInfo::create([
            'user_id' => $user,
            'post_id' => $post_id,
            'ad_type' => 'free',
            'start_date' => $curr_date,
            'end_date' => $end_date,
            'active' => '1'
        ]);

        $response = array(
            'type' => ($request->package_type == 'free' || ($get_pack_info && strtolower($get_pack_info->short_name) == "free")) ? "free" : "payment",
            'post_id' => $post_id,
            'package_id' => $request->package_type
        );

        return $response;
    }
    /* check product condition enabled for this category */
    public function check_product_condition_is_active(Request $request, $id)
    {
        $check = TblCategory::where("id", $id)->pluck('product_condition')->first();
        if ($check == 1) {
            $response = $this->sendSuccess("success");
        } else {
            $response = $this->sendError("no data");
        }
        return response()->json($response);
    }
    public function get_max_upload_img_limit()
    {
        $img_limit = Setting::get_image_size_settings();
        $response = [
            'code' => 200,
            'success' => true,
            'image_limit' => $img_limit['max_image_limit']
        ];
        return response()->json($response);
    }

    /* Get Post Single Post Info */
    public function get_single_post_info($id)
    {
        $postinfo = TblPost::where('id', $id)->first()->toArray();
        $city_name = TblPost::getPostloc($postinfo['city']);
        $city_info = TblCity::where('id', $postinfo['city'])->first();
        $country_info = TblCountry::where('id', $city_info['country_id'])->first();
        $state_info = TblState::where('id', $city_info['state_id'])->first();
        $images = array();
        /* Post images */
        $ima = explode(',', $postinfo['images']);
        $imagesone = array();
        $imagestwo = array();
        if (!empty($ima[0])) {
            foreach ($ima as $image) {
                $imgUrl = str_replace('adpost/predefined/', '', $image);
                $is_file = base_path() . '/storage/app/public/adpost/applist/' . $imgUrl;
                if (is_file($is_file)) {
                    $imagesone[] = URL::to('storage/adpost/applist/' . $imgUrl);
                } else {
                    $imagestwo[] = URL::to('storage/adpost/predefined/' . $imgUrl);
                }
            }
        }
        $images = array_merge($imagesone, $imagestwo);
        $currency = TblCurrency::where('id', $postinfo['currency_id'])->first();
        $custom_data = $this->get_single_post_custom_field($postinfo['category_id'], $postinfo['id']);
        $category_info = TblCategory::where('id', $postinfo['category_id'])->pluck('title')->toArray();
        if (!empty($custom_data)) {
            $is_custom_field = 1;
        } else {
            $is_custom_field = 0;
        }
        // get locality & city end
        $data = array(
            'id' => $postinfo['id'],
            'title' => $postinfo['title'],
            'description' => $postinfo['description'],
            'price' => $postinfo['price'],
            'show_number' => $postinfo['show_number'],
            'completeAddress' => $postinfo['completeAddress'],
            'latitude' => $city_info['latitude'],
            'longitude' => $city_info['logitude'],
            'country_long' => $country_info['name'],
            'country_short' => $country_info['code'],
            'state_long' => $state_info['name'],
            'state_short' => $state_info['code'],
            'city_name' => $city_info['name'],
            'images' => $images,
            'locality' => !empty($city_info['locality']) ? $city_info['locality'] : "",
            'currency_symbol' => $currency->currency_hex,
            'currency_id' => $currency->id,
            'currency_short' => $currency->short_code,
            'category_id' => $postinfo['category_id'],
            'category_name' => $category_info[0],
            'is_custom_fields' => $is_custom_field,
            'is_exchange' => $postinfo['exchange_to_buy'],
            'product_video_url' => $postinfo['video_url'],
            'giving_away' => $postinfo['giving_away'],
            'fixed_price' => $postinfo['fixed_price'],
            'product_condition' => !empty($postinfo['product_condition']) ? $postinfo['product_condition'] : "",
            'buy_now' => $postinfo['instant_buy'],
            'shipping_rate' => $postinfo['shipping_rate'],
            'cutom_fields' => $custom_data
        );
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $data,
        ];
        return response()->json($response);
    }
    /* get single post custom field value based on post id and cat id*/
    public function get_single_post_custom_field($catid, $posted_id)
    {
        $cfld = TblCustomField::where('cat_id', $catid)->get();
        $data = array();
        if (!empty($cfld[0])) {
            if ($cfld[0]->field_count > 0) {
                $arrayData = TblFieldsDetail::where('cat_id', $catid)->where('active', '1')->get();
                if ($arrayData->count() > 0) {
                    $loopmain = 0;
                    $type_text = $type_number = $type_textarea = $type_select = $type_checkbox = $type_radio = 0;
                    foreach ($arrayData as $r) {
                        $field_id = $r["id"];
                        $type = $r["type"];
                        $name = $field_id . '_' . $r["form_field_name"];
                        $label = $r['name'];
                        $required = $r['required'];
                        $postvalue = "";
                        $postDet = TblPostValue::where('post_id', $posted_id)->where('field_id', $field_id)->where('active', "1")->get();
                        if ($postDet->count() > 0) {
                            $postvalue = $postDet[0]->value;
                        }
                        //textfield
                        if ($type == "text") {
                            $type_text = 1;
                            $data['text'][] = array(
                                'value' => $postvalue,
                                'passing_label' => str_replace(" ", '*', strtolower($label)),
                                'label' => $label,
                                'name' => $name,
                                'required' => $required,
                            );
                        }
                        if ($type == "number") {
                            $type_number = 1;
                            $data['number'][] = array(
                                'value' => $postvalue,
                                'passing_label' => str_replace(" ", '*', strtolower($label)),
                                'label' => $label,
                                'name' => $name,
                                'required' => $required,
                            );
                        }
                        if ($type == "textarea") {
                            $type_textarea = 1;
                            $data['textarea'][] = array(
                                'name' => $name,
                                'passing_label' => str_replace(" ", '*', strtolower($label)),
                                'label' => $label,
                                'required' => $required,
                                'value' => $postvalue
                            );
                        }
                        if ($type == "select") {
                            $type_select = 1;
                            $arrayData = TblFieldsOption::where('cat_id', $catid)->where('form_field_name', $r["form_field_name"])->where('active', '1')->get();
                            if ($r["form_field_name"] == "brandwithmodel") {
                                // brands select
                                $options = array();
                                foreach ($arrayData as $k) {
                                    $kid = explode('-', $k["id"])[0];
                                    $options[] = array(
                                        'value' => $kid,
                                        'key' => $k["key"]
                                    );
                                }
                                $data['select'][] = array(
                                    'value' => !empty($postvalue) ? explode(',', $postvalue)[0] : "",
                                    'label' => "Brand",
                                    'passing_label' => str_replace(" ", '*', strtolower($label)),
                                    'name' => $name,
                                    'required' => $required,
                                    'options' => $options,
                                );
                                // model select
                                if (!empty($postvalue)) {
                                    $brand_kid = explode(',', $postvalue)[0];
                                    $arrayData = TblFieldsOption::where('id', 'like', '%' . $brand_kid . '%')->where('active', '1')->first();
                                    $model_select_options = array();
                                    $first_models = explode(',', $arrayData['value']);
                                    foreach ($first_models as $first_model) {
                                        $modelVal = Str::slug($first_model, "-");
                                        $model_select_options[] = array(
                                            'value' => $modelVal,
                                            'key' => $first_model
                                        );
                                    }
                                    $data['select'][] = array(
                                        'value' => explode(',', $postvalue)[1],
                                        'label' => "Model",
                                        'name' => $r["id"] . "_modelswithbrand",
                                        'passing_label' => "modelswithbrand",
                                        'required' => $required,
                                        'options' => $model_select_options,
                                    );
                                }
                            } else {
                                $options = array();
                                foreach ($arrayData as $k) {
                                    $options[] = array(
                                        'value' => $k["value"],
                                        'key' => $k["key"]
                                    );
                                }
                                $data['select'][] = array(
                                    'value' => $postvalue,
                                    'label' => $label,
                                    'passing_label' => str_replace(" ", '*', strtolower($label)),
                                    'name' => $name,
                                    'required' => $required,
                                    'options' => $options,
                                );
                            }
                        }
                        if ($type == "checkbox-group") {
                            $type_checkbox = 1;
                            $checkbox_arrayData = TblFieldsOption::where('cat_id', $catid)->where('form_field_name', $r["form_field_name"])->where('active', '1')->get();
                            foreach ($checkbox_arrayData as $k) {
                                $check_options[] = array(
                                    'value' => $k["value"],
                                    'key' => $k["key"]
                                );
                            }
                            $data['checkbox'][] = array(
                                'value' => !empty($postvalue) ? explode(',', $postvalue) : [],
                                'name' => $name,
                                'required' => $required,
                                'options' => $check_options
                            );
                        }
                        if ($type == "radio-group") {
                            $type_radio = 1;
                            $radio_arrayData = TblFieldsOption::where('cat_id', $catid)->where('form_field_name', $r["form_field_name"])->where('active', '1')->get();
                            foreach ($radio_arrayData as $k) {
                                $radio_options[] = array(
                                    'value' => $k["value"],
                                    'key' => $k["key"]
                                );
                            }
                            $data['radio'][] = array(
                                'value' => $postvalue,
                                'name' => $name,
                                'required' => $required,
                                'options' => $radio_options,
                            );
                        }
                        $loopmain++;
                    }
                    if ($type_text == 0) {
                        $data['text'] = array();
                    }
                    if ($type_number == 0) {
                        $data['number'] = array();
                    }
                    if ($type_textarea == 0) {
                        $data['textarea'] = array();
                    }
                    if ($type_select == 0) {
                        $data['select'] = array();
                    }
                    if ($type_checkbox == 0) {
                        $data['checkbox'] = array();
                    }
                    if ($type_radio == 0) {
                        $data['radio'] = array();
                    }
                }
            }
        }
        return $data;
    }
    /* Edit post -  by logged in user */
    public function edit_post(Request $request, $id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $node = TblPost::find($id);
                $pre_slug = explode('-', $node->slug);
                $pre_slug_id = end($pre_slug);
                $slug = Str::slug($request->title, "-");
                $alias_val = $slug . '-' . $pre_slug_id;
                $new_imgs = "";
                $old_images = array();
                $old_images = !empty($node->images) ? explode(',', $node->images) : "";
                if (!empty($request->images)) {
                    $images = explode(',', $request->images);
                    $imagenamesArr = [];
                    foreach ($images as $j) {
                        $imgname = Str::random(15) . '.' . "jpg";
                        $save_img_name = 'adpost/predefined/' . $imgname;
                        $replace = substr($j, 0, strpos($j, ',') + 1);
                        $img = str_replace($replace, '', $j);
                        $k = str_replace(' ', '+', $img);
                        $decoded_image = base64_decode($k);
                        /* Get watermark image */
                        $watermark_img_edit = Setting::get_logos();
                        /* get image size */
                        $imagesizeSet = Setting::get_image_size_settings();
                        /* get img width for list page */
                        if (str_contains($imagesizeSet['list'], "*") == true) {
                            $list_size = explode('*', $imagesizeSet['list']);
                            $list_width = $list_size[0];
                            $list_height = $list_size[1];
                        } else {
                            /* default - size : 222*156 */
                            $list_width = 222;
                            $list_height = 156;
                        }
                        /* get img width for detail page */
                        if (str_contains($imagesizeSet['detail'], "*") == true) {
                            $detail_size = explode('*', $imagesizeSet['detail']);
                            $detail_width = $detail_size[0];
                            $detail_height = $detail_size[1];
                        } else {
                            /* default - size : 500*350 */
                            $detail_width = 500;
                            $detail_height = 350;
                        }
                        /*image for web - original*/
                        $path_web_original = "adpost/predefined/normal/" . $imgname;
                        $web_list_original = Image::make($decoded_image);
                        $web_list_original->insert(public_path('storage/' . $watermark_img_edit['watermark']), 'bottom-right', 10, 10);
                        Storage::put($path_web_original, (string) $web_list_original->encode());
                        /* image for web - list - default - size : 222*156*/
                        $path_web_list = "adpost/predefined/list/" . $imgname;
                        $web_list = Image::make($decoded_image)->resize($list_width, $list_height, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $web_list->insert(public_path('storage/' . $watermark_img_edit['watermark']), 'bottom-right', 10, 10);
                        Storage::put($path_web_list, (string) $web_list->encode());
                        /* image for web detail page */
                        $path = "adpost/predefined/" . $imgname;
                        $image = Image::make($decoded_image)->resize($detail_width, $detail_height, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $image->insert(public_path('storage/' . $watermark_img_edit['watermark']), 'bottom-right', 10, 10);
                        Storage::put($path, (string) $image->encode());
                        /* image for app - list size : 160*160 */
                        $path_app_list = "adpost/applist/" . $imgname;
                        $app_list = Image::make($decoded_image)->resize(160, 160, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $app_list->insert(public_path('storage/' . $watermark_img_edit['watermark']), 'bottom-right', 10, 10);
                        Storage::put($path_app_list, (string) $app_list->encode());
                        /* image for app - detail size : 230*230  */
                        $path_app_detail = "adpost/appdetail/" . $imgname;
                        $app_detail = Image::make($decoded_image)->resize(600, 600, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $app_detail->insert(public_path('storage/' . $watermark_img_edit['watermark']), 'bottom-right', 10, 10);
                        Storage::put($path_app_detail, (string) $app_detail->encode());
                        array_push($imagenamesArr, $save_img_name);
                    }
                    $new_imgs = implode(',', $imagenamesArr);
                }
                $check_old_imgs = array();
                $check_new_imgs = array();
                $check_old_imgs = !empty($old_images) ? $old_images : [];
                $check_new_imgs = !empty($new_imgs) ? explode(',', $new_imgs) : [];
                $allimgs = array_merge($check_old_imgs, $check_new_imgs);
                $predefined_imgs = implode(',', $allimgs);
                $node->update(['images' => $predefined_imgs]);
                if (!empty($request->mainImage)) {
                    $mainImageUrl = $request->mainImage;
                    // Process existing images
                    $oldImages = !empty($node->images) ? explode(',', $node->images) : [];
                    // Check if mainImage is provided and exists in old images
                    $mainImageRelativePath = trim(str_replace(['storage/adpost/applist/', 'storage/adpost/predefined/'], '', parse_url($mainImageUrl, PHP_URL_PATH)), '/');
                    $mainImageFullPath = 'adpost/predefined/' . $mainImageRelativePath;
                    // Remove the full URL from the existing mainImage paths
                    $oldImages = array_map(function ($image) {
                        return 'adpost/predefined/' . pathinfo($image, PATHINFO_BASENAME);
                    }, $oldImages);
                    // Remove the mainImage from oldImages if it exists
                    $oldImages = array_diff($oldImages, [$mainImageFullPath]);
                    // Move mainImage to the beginning of a separate array
                    $mainImagesArray = [$mainImageFullPath];
                    // Filter out empty values and merge the mainImagesArray with the rest of the oldImages
                    $allImages = array_merge(array_filter($mainImagesArray), $oldImages);
                    // Update the 'images' field with the new image order
                    $predefinedImages = implode(',', array_filter($allImages));
                    $node->update(['images' => $predefinedImages]);
                }
                // dd($predefinedImgs);
                // dd($predefined_imgs,$predefinedImages);
                if (!empty($request->latitude)) {
                    $data = $this->insert_locations($request->country_short, $request->country_long, $request->state_short, $request->state_long, $request->city_name, $request->locality, $request->latitude, $request->logitude);
                    $cityid = $data['city_id'];
                } else {
                    $cityid = $node->city;
                }
                //store locality and city name
                if (($request->city_name != "" && $request->locality != "") && ($request->city_name != $request->locality)) {
                    $cityNames = $request->locality . ", " . $request->city_name;
                } elseif ($request->city_name == $request->locality) {
                    $cityNames = $request->locality;
                } else {
                    $cityNames = "";
                }
                //store locality and city name
                $currency_id_bf = $request->currency_id;
                // if (!empty($currency_id_bf)) {
                //     $default_crr = Setting::get_admin_default_currency();
                //     $curr_id = $default_crr['id'];
                //     $curr_short_code = $default_crr['short_code'];
                //     $post_currency = TblCurrency::where('id', $currency_id_bf)->value('short_code');
                //     // Fetching JSON
                //     $req_url = 'https://v6.exchangerate-api.com/v6/3b135c35e73f91d7427b14c4/pair/' . $post_currency . '/' . $curr_short_code . '/' . $request->price;
                //     $response_json = file_get_contents($req_url);
                //     // Continuing if we got a result
                //     if (false !== $response_json) {
                //         // Try/catch for json_decode operation
                //         try {
                //             // Decoding
                //             $response = json_decode($response_json);
                //             // Check for success
                //             if ('success' === $response->result) {
                //                 $price = $response->conversion_result;
                //                 $currency_id = $default_crr['id'];
                //             }
                //         } catch (Exception $e) {
                //             // Handle JSON parse error...
                //             $currency_id = $request->currency_id;
                //             $price = $request->price;
                //         }
                //     }
                // } else {
                //     $currency_id = $request->currency_id;
                //     $price = $request->price;
                // }
                $currency_id = $request->currency_id;
                $price = $request->price;

                $node->update([
                    'user_id' => $user,
                    'category_id' => $node->category_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'show_number' => $request->show_number,
                    'price' => $price,
                    'slug' => $alias_val,
                    'city' => $cityid,
                    'locality' => $cityNames,
                    'images' => !empty($predefinedImages) ? $predefinedImages : $predefined_imgs,
                    'currency_id' => !empty($currency_id) ? $currency_id : $node->currency_id,
                    'product_condition' => !empty($request->product_condition) ? $request->product_condition : $node->product_condition,
                    'completeAddress' => !empty($request->completeAddress) ? $request->completeAddress : $node->completeAddress,
                    'exchange_to_buy' => !empty($request->is_exchange) ? $request->is_exchange : 0,
                    'video_url' => !empty($request->product_video_url) ? $request->product_video_url : $node->video_url,
                    'giving_away' => !empty($request->giving_away) ? $request->giving_away : 0,
                    'fixed_price' => !empty($request->fixed_price) ? $request->fixed_price : 0,
                    'instant_buy' => !empty($request->buy_now) ? $request->buy_now : 0,
                    'shipping_rate' => !empty($request->shipping_rate) ? $request->shipping_rate : 0
                ]);
                /* post custome fields updation start */
                $formdata = $request->all();
                $new_form_fields = [];
                $skipfields = array('currency_id', 'shipping_rate', 'buy_now', 'fixed_price', 'giving_away', 'product_video_url', 'is_exchange', 'product_condition', 'title', 'category_id', 'price', 'package_type', 'description', 'country_long', 'country_short', 'state_long', 'state_short', 'city_name', 'locality', 'latitude', 'longitude', 'images');
                // brands and models custome fields updation
                $brand_model_fields = [];
                foreach ($formdata as $key => $value) {
                    if (in_array($key, $skipfields)) {
                        continue;
                    }
                    //brandwithmodels values imploding
                    if ((strpos($key, 'brandwithmodel') == true) || (strpos($key, 'modelswithbrand') == true)) {
                        if (strpos($key, 'brandwithmodel') == true) {
                            array_push($brand_model_fields, $value);
                        }
                        if (strpos($key, 'modelswithbrand') == true) {
                            array_push($brand_model_fields, $value);
                        }
                    }
                }
                foreach ($formdata as $key => $value) {
                    if (in_array($key, $skipfields)) {
                        continue;
                    }
                    if (strpos($key, 'modelswithbrand') == true) {
                        continue;
                    }
                    $field_id = explode('_', $key)[0];
                    //checkbox values imploding
                    if (strpos($key, 'checkbox') == true) {
                        $ckhval = explode(',', $value);
                        $value = implode(',', $ckhval);
                    }
                    //brandiwthmodel values imploding -- skipped itself if empty
                    if (strpos($key, 'brandwithmodel') == true) {
                        $value = implode(',', $brand_model_fields);
                    }
                    $post_value_exist = TblPostValue::where('post_id', $node->id)->where('field_id', $field_id)->where('active', '1');
                    $post_update = TblPost::find($node->id);
                    $value = ($value == null) ? "" : $value;
                    if ($post_value_exist->count() == 0) {
                        TblPostValue::create([
                            'post_id' => $node->id,
                            'field_id' => $field_id,
                            'value' => $value,
                            'active' => 1,
                        ]);
                        $post_update->update(['active' => 1]);
                    } else {
                        $post_value_exist->update([
                            'value' => $value
                        ]);
                    }
                    array_push($new_form_fields, $field_id);
                }
                //if any fields deactivated in TblFieldsDetail, do it same in TblPostValue 
                $all_fields_detail = TblFieldsDetail::where('cat_id', $node->category_id)->where('active', '1')->get('id');
                $upd1 = TblPostValue::where('post_id', $node->id)->whereNotIn('field_id', $all_fields_detail);
                $upd1->update(['active' => '0']);
                //if any field makes empty from form submit, updating below 
                $empty_form_fields = TblPostValue::where('post_id', $node->id)->whereNotIn('field_id', $new_form_fields)->where('active', '1')->get();
                foreach ($empty_form_fields as $r) {
                    $field_id = $r['field_id'];
                    $field_detail = TblFieldsDetail::where('id', $field_id)->where('active', '1')->get();
                    $filetype = $field_detail[0]->type;
                    if ($filetype == "file") {
                        continue;
                    }
                    $update_post_vals = TblPostValue::where('post_id', $node->id)->where('field_id', $field_id);
                    $update_post_vals->update(['value' => ""]);
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => "Updated successfully!",
                ];
                return response()->json($response);
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User!"
                ];
                return response()->json($response);
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!"
            ];
            return response()->json($response);
        }
    }
    /* remove post images */
    public function remove_post_img(Request $request)
    {
        $request->validate([
            'oldimage' => 'required',
            'post_id' => 'required'
        ]);
        $url = URL::to('storage/') . '/';
        $old_img = str_replace($url, '', $request->oldimage);
        /* remove the deleted image from the storage folder start */
        if (strpos($request->oldimage, 'applist') !== false) {
            $unmatched_img_name = str_replace("adpost/applist/", '', $old_img);
        } else {
            $unmatched_img_name = str_replace("adpost/predefined/", '', $old_img);
        }
        /* remove web normal img file */
        if (is_file(base_path() . '/storage/adpost/predefined/normal/' . $unmatched_img_name)) {
            $path = base_path() . '/storage/adpost/predefined/normal/' . $unmatched_img_name;
            unlink($path);
        }
        /* remove web image file */
        if (is_file(base_path() . '/storage/app/public/adpost/predefined/' . $unmatched_img_name)) {
            $path = base_path() . '/storage/app/public/adpost/predefined/' . $unmatched_img_name;
            unlink($path);
        }
        /* remove image file from app list folder */
        if (is_file(base_path() . '/storage/app/public/adpost/applist/' . $unmatched_img_name)) {
            $app_list = base_path() . '/storage/app/public/adpost/applist/' . $unmatched_img_name;
            unlink($app_list);
        }
        /* remove image file from app detail folder */
        if (is_file(base_path() . '/storage/app/public/adpost/appdetail/' . $unmatched_img_name)) {
            $app_detail = base_path() . '/storage/app/public/adpost/appdetail/' . $unmatched_img_name;
            unlink($app_detail);
        }
        $post_imgs = TblPost::where('id', $request->post_id)->first();
        $array = explode(',', $post_imgs['images']);
        $array = array_map(function ($value) {
            return str_replace("adpost/predefined/", '', $value);
        }, $array);
        $array = \array_diff($array, [$unmatched_img_name]);
        $data = array();
        $newimg = array();
        foreach ($array as $img) {
            if (!empty($img)) {
                $newimg[] = "adpost/predefined/" . $img;
                $data[] = URL::to('storage/adpost/predefined/' . $img);
            }
        }
        $final_imgs = !empty($newimg) ? implode(',', $newimg) : '';
        $post_imgs->update([
            'images' => $final_imgs
        ]);
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $data
        ];
        return response()->json($response);
        /* remove the deleted image from the storage folder end */
    }
    /* Delete post -  by logged in user */
    public function delete_post($id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $record1 = TblPost::where('id', $id);
                $record1->delete();
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => "Deleted successfully!",
                ];
                return response()->json($response);
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User!"
                ];
                return response()->json($response);
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!"
            ];
            return response()->json($response);
        }
    }
    /* Write review -  by logged in user */
    public function write_review(Request $request, $id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $request->validate([
                    'ratings' => 'required',
                    'comment' => 'required'
                ]);
                $checkExist = TblReview::where('post_id', $id)->where('user_id', $user)->get();
                if ($checkExist->count() == 0) {
                    $createe = TblReview::create([
                        "post_id" => $id,
                        "user_id" => $user,
                        "ratings" => $request->ratings,
                        "comment" => $request->comment,
                        "approved" => '0',
                        "spam" => '0'
                    ]);
                    // notification start
                    // $get_user_info = User::where('id', $user)->first();
                    // $get_post_info = TblPost::where('id', $id)->first();
                    // $get_seller_info = User::where('id', $get_post_info->user_id)->first();
                    // $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                    // $message = array("notifydata" => array('to_id' => $get_post_info->user_id, 'from_id' => $user, 'message' => "New comment posted by " . $get_user_info->name . "!. Post Name - " . $get_post_info->title, 'notify_from' => 'new_comment', 'notify_title' => "New Comment Added In Letgo!..", 'post_id' => $id, 'slug' => $get_post_info->slug));
                    // TblPost::send_push_notification($fcmid, $message);
                    // $slug = TblPost::get_post_slug($get_post_info->slug);
                    // $mail_data = array("send_maildata" => array('to_id' => $get_post_info->user_id, 'message' => "New comment posted by " . $get_user_info->name . "!. Post Name - " . $get_post_info->title, 'subject' => "New Comment Added In Letgo!..", 'ad_url' => $slug));
                    // $mail_key = "post_comment";
                    // Setting::notification_mail($mail_data, $mail_key);
                    // notification end
                    $response = [
                        'success' => true,
                        'code' => 200,
                        'message' => "Please wait for admin approval!",
                    ];
                    return response()->json($response);
                } else {
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => "Already you added the review for this post!",
                    ];
                    return response()->json($response);
                }
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User",
                ];
                return response()->json($response);
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!",
            ];
            return response()->json($response);
        }
    }
    /* Home Page */
    public function home(Request $request)
    {
        $token = $this->getBearerToken();
        $user = "";
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
        }
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 50;
        //merge payment ads with free ads based on which one have highest count of records
        $final_result_array = TblPost::merge_payment_with_free_ads();
        if (!empty($request->get('loc'))) {
            $lc = $request->loc;
        } elseif ($request->get('country')) {
            $lc = $request->country;
        } else {
            $lc = $request->city;
        }
        $ids = ""; // Initialize to prevent undefined variable error
        if (count($final_result_array) > 0) {
            $ids = str_replace(array('[', ']'), '', htmlspecialchars(json_encode($final_result_array), ENT_NOQUOTES));
        }
        $addressids = array();
        $latest_ads_lat = array();
        $feature_ads_lat = "";
        $shop_ads_lat = "";
        $latest_ads_city = array();
        $feature_ads_city = "";
        $feature_ads_state = "";
        $shop_ads_city = "";
        $shop_ads_state = "";
        $latest_ads_state = array();
        $feature_ads_country = "";
        $shop_ads_country = "";
        $latest_ads_country = array();
        //if user current location contains latitude and longitude
        if (!empty($request->get('latitude'))) {
            $addressids = $this->get_city_ids($request->latitude, $request->logitude, 20);
            if (!empty($addressids)) {
                // get latest ads for user current location of latitude and longitude                             
                $latest_ads_lat = TblPost::app_get_latest_ads_home_page($final_result_array, $ids, $limit, $page, $addressids);
                // get featureads ads for user current location of latitude and longitude
                $feature_ads_lat = TblPost::get_premium_ads('feature_ad', '10', $addressids);
                // get shopads ads for user current location of latitude and longitude
                $shop_ads_lat = TblPost::get_premium_ads('shop_vip', '10', $addressids);
            }
        }
        // CHECK FOR THE CURRENT LOC LATITUDE AND LONGITUDE HAVE RECORDS IF EMPTY MEANS GET RECORD FROM CURRENT CITY            
        if (!empty($request->get('city'))) {
            $current_city = TblCity::where('name', $request->city)->get(['latitude', 'logitude'])->first();
            if (!empty($current_city)) {
                $addressids = $this->get_city_ids($current_city->latitude, $current_city->logitude, 20);
                if (count($latest_ads_lat) == 0) {
                    // get latest ads for user current location of city
                    $latest_ads_city = TblPost::app_get_latest_ads_home_page($final_result_array, $ids, $limit, $page, $addressids);
                }
                if (empty($feature_ads_lat)) {
                    // get featureads ads for user current location of city
                    $feature_ads_city = TblPost::get_premium_ads('feature_ad', '10', $addressids);
                }
                if (empty($shop_ads_lat)) {
                    // get shopads ads for user current location of city
                    $shop_ads_city = TblPost::get_premium_ads('shop_vip', '10', $addressids);
                }
            }
        }
        // CHECK FOR THE CURRENT LOC OF CITY HAVE RECORDS IF EMPTY MEANS GET RECORD FROM CURRENT STATE
        if (!empty($request->get('state'))) {
            $current_state = TblState::where('name', $request->state)->pluck('id')->first();
            if (!empty($current_state)) {
                $get_state_cities = TblCity::where('state_id', $current_state)->get(['id']);
                foreach ($get_state_cities as $get_state_city) {
                    $addressids[] = $get_state_city->id;
                }
                if (count($latest_ads_city) == 0) {
                    // get latest ads for user current location of state         
                    $latest_ads_state = TblPost::app_get_latest_ads_home_page($final_result_array, $ids, $limit, $page, $addressids);
                }
                if (empty($feature_ads_city)) {
                    // get featureads ads for user current location of state
                    $feature_ads_state = TblPost::get_premium_ads('feature_ad', '10', $addressids);
                }
                if (empty($shop_ads_city)) {
                    // get featureads ads for user current location of state
                    $shop_ads_state = TblPost::get_premium_ads('shop_vip', '10', $addressids);
                }
            }
        }
        // CHECK FOR THE CURRENT LOC OF STATE HAVE RECORDS IF EMPTY MEANS GET RECORD FROM CURRENT COUNTRY
        if (!empty($request->get('country'))) {
            $current_country = TblCountry::where('name', $request->country)->pluck('id')->first();
            if (!empty($current_country)) {
                $get_country_cities = TblCity::where('country_id', $current_country)->get(['id']);
                foreach ($get_country_cities as $get_country_city) {
                    $addressids[] = $get_country_city->id;
                }
                if (empty($feature_ads_state)) {
                    // get featureads ads for user current location of country
                    $feature_ads_country = TblPost::get_premium_ads('feature_ad', '10', $addressids);
                }
                if (empty($shop_ads_state)) {
                    // get featureads ads for user current location of country
                    $shop_ads_country = TblPost::get_premium_ads('shop_vip', '10', $addressids);
                }
                if (count($latest_ads_state) == 0) {
                    // get latest ads for user current location of country         
                    $latest_ads_country = TblPost::app_get_latest_ads_home_page($final_result_array, $ids, $limit, $page, $addressids);
                }
            }
        }
        if (!empty($feature_ads_lat) && !empty($shop_ads_lat)) {
            $featurs_ad_lists = array_merge($shop_ads_lat, $feature_ads_lat);
        } else if (!empty($feature_ads_city) && !empty($shop_ads_city)) {
            $featurs_ad_lists = array_merge($shop_ads_city, $feature_ads_city);
        } else if (!empty($feature_ads_state) && !empty($shop_ads_state)) {
            $featurs_ad_lists = array_merge($shop_ads_state, $feature_ads_state);
        } else if (!empty($feature_ads_country) && !empty($shop_ads_country)) {
            $featurs_ad_lists = array_merge($shop_ads_country, $feature_ads_country);
        } else {
            $featur_ads = TblPost::get_premium_ads('feature_ad', '10');
            $shop_vip = TblPost::get_premium_ads('shop_vip', '10');
            $featurs_ad_lists = array_merge($shop_vip, $featur_ads);
        }
        $recommended_post = array();
        /* recommended post - feature posts */
        foreach ($featurs_ad_lists as $featurs_ad_list) {
            $fav = "";
            if (!empty($user)) {
                $fav = TblSavedPosts::where('user_id', $user)->where('post_id', $featurs_ad_list['id'])->get(['id']);
            }
            $currency = $this->post_currency($featurs_ad_list['id']);
            $single_info = TblPost::get_single_post_information($featurs_ad_list['id']);
            $additional_data = $this->getAdditionalInfo($featurs_ad_list['id']);
            $additional_info = array();
            foreach ($additional_data as $additional_data) {
                $additional_info[] = array(
                    'lable' => $additional_data['label'],
                    'value' => $additional_data['value']
                );
            }
            //seller logo  start
            $seller_logo = '';
            $seller_brand = '';
            $seller = TblPost::where('id', $featurs_ad_list['id'])->value('user_id');
            $seller_check = Verificationrequest::where('user_id', $seller)->first();
            // if (!empty($seller_check)) {
            //     if ($seller_check->is_company == 'yes') {
            //         $seller_logo = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_logo');
            //         $seller_brand = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_name');
            //     }
            // }
            // $brand_logo = !empty($seller_logo) ? URL::to('storage/business-profile/thumb50/' . $seller_logo) : URL::asset('storage/profile-avatar.jpg');
            //seller logo end
            // if (!empty($user)) {
            //     $post_currency_id = TblPost::where('id', $featurs_ad_list['id'])->value('currency_id');
            //     $user_currency = TblPost::userCurrencyConversion($user, $featurs_ad_list['price'], $post_currency_id);
            //     $price = $user_currency['convert_cur'];
            //     $symbol = $user_currency['convert_sym'];
            // } else {
            //     $price = $featurs_ad_list['price'];
            //     $symbol = $currency;
            // }
            $price = $featurs_ad_list['price'];
            $symbol = $currency;
            $recommended_post[] = array(
                'id' => $featurs_ad_list['id'],
                'title' => $featurs_ad_list['title'],
                'price' => $price,
                'description' => $featurs_ad_list['description'],
                'city_name' => !empty($featurs_ad_list['locality']) ? $featurs_ad_list['locality'] : $featurs_ad_list['city_name'],
                'created_at' => date('d M Y', strtotime($featurs_ad_list['created_at'])),
                'ad_type' => Str_replace('_', ' ', strtoupper($featurs_ad_list['ad_type'])),
                'images' => $single_info["images"],
                'custom_fields' => $additional_info,
                // 'brand_name' => $seller_brand,
                // 'brand_logo' => $brand_logo,
                'sellerId' => $seller,
                'is_fav' => !empty($fav) && ($fav->count() > 0) ? true : false,
                'currency_symbol' => $symbol,
                'giving_away' => $featurs_ad_list['giving_away']
            );
        }
        if (count($latest_ads_lat) > 0) {
            $lates_ads = $latest_ads_lat;
            $latest_ads_count = TblPost::app_get_latest_ads_count($final_result_array, $ids, $limit, $page, $addressids);
        } else if (count($latest_ads_city) > 0) {
            $lates_ads = $latest_ads_city;
            $latest_ads_count = TblPost::app_get_latest_ads_count($final_result_array, $ids, $limit, $page, $addressids);
        } else if (count($latest_ads_state) > 0) {
            $lates_ads = $latest_ads_state;
            $latest_ads_count = TblPost::app_get_latest_ads_count($final_result_array, $ids, $limit, $page, $addressids);
        } else if (count($latest_ads_country) > 0) {
            $lates_ads = $latest_ads_country;
            $latest_ads_count = TblPost::app_get_latest_ads_count($final_result_array, $ids, $limit, $page, $addressids);
        } else {
            $lates_ads = TblPost::app_get_latest_ads_home_page($final_result_array, $ids, $limit, $page);
            $latest_ads_count = TblPost::app_get_latest_ads_count($final_result_array, $ids, $limit, $page);
        }
        $newdatas = $lates_ads;
        $other_post = array();
        foreach ($newdatas as $newdata) {
            $fav = "";
            if (!empty($user)) {
                $fav = TblSavedPosts::where('user_id', $user)->where('post_id', $newdata['id'])->get(['id']);
            }
            $currency = $this->post_currency($newdata['id']);
            $adtype = TblPost::getAddtype($newdata['id']);
            $single_info = TblPost::get_single_post_information($newdata['id']);
            $additional_data = $this->getAdditionalInfo($newdata['id']);
            $additional_info = array();
            foreach ($additional_data as $additional_data) {
                $additional_info[] = array(
                    'lable' => $additional_data['label'],
                    'value' => $additional_data['value']
                );
            }
            $categor_id = TblPost::where('id', $newdata['id'])->value('category_id');
            $cat = TblCategory::where('id', $categor_id)->first();
            $cat_filter = array();
            $cat_name = "";
            // Agar category nahi mili to skip karo (500 error prevent karne k liye)
            if (empty($cat)) {
                continue;
            }
            if ($cat->parent_id == null) {
                $cat_filter = TblCategory::descendantsAndSelf($cat->id);
                // dd($cat->id);
                $cat_name = $cat->title;
                $token = $this->getBearerToken();
                if (!empty($token['code']) && ($token['code'] == 200)) {
                    $user = $this->getLoggedUser($token['token']);
                    if (!empty($user)) {
                        // dd($user);
                        $lan_code = User::where('id', $user)->value('preferred_language');
                    } else {
                        $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                        $response = [
                            'success' => false,
                            'code' => 0,
                            'message' => "Invalid User"
                        ];
                    }
                } else {
                    $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                }
            } else {
                $token = $this->getBearerToken();
                if (!empty($token['code']) && ($token['code'] == 200)) {
                    $user = $this->getLoggedUser($token['token']);
                    if (!empty($user)) {
                        // dd($user);
                        $lan_code = User::where('id', $user)->value('preferred_language');
                    } else {
                        $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                        $response = [
                            'success' => false,
                            'code' => 0,
                            'message' => "Invalid User"
                        ];
                    }
                } else {
                    $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                }
                $cate = TblCategory::where('id', $cat->parent_id)->first();
                // dd($cate->id);
                $cat_filter = TblCategory::descendantsAndSelf($cate->id);
                $cat_name = $cate->title;
            }
            $get_language = Languages::where('lang_code', $lan_code)->where('lang_org_text', $cat_name)->value('lang_text');
            $get_lang = (!empty($get_language) ? $get_language : $cat_name);
            //seller logo  start
            $seller_logo = '';
            $seller_brand = '';
            $seller = TblPost::where('id', $newdata['id'])->value('user_id');
            // $seller_check = Verificationrequest::where('user_id', $seller)->first();
            // if (!empty($seller_check)) {
            //     if ($seller_check->is_company == 'yes') {
            //         $seller_logo = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_logo');
            //         $seller_brand = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_name');
            //     }
            // }
            // $brand_logo = !empty($seller_logo) ? URL::to('storage/business-profile/thumb50/' . $seller_logo) :URL::asset('storage/profile-avatar.jpg');
            //seller logo end
            // if (!empty($user)) {
            //     $post_currency_id = TblPost::where('id', $newdata['id'])->value('currency_id');
            //     $user_currency = TblPost::userCurrencyConversion($user, $newdata['price'], $post_currency_id);
            //     $price = $user_currency['convert_cur'];
            //     $symbol = $user_currency['convert_sym'];
            // } else {
            //     $price = $newdata['price'];
            //     $symbol = $currency;
            // }
            $price = $newdata['price'];
            $symbol = $currency;
            $other_post[] = array(
                'id' => $newdata['id'],
                'slug' => $newdata['slug'],
                'title' => $newdata['title'],
                'price' => $price,
                'description' => $newdata['description'],
                'city_name' => !empty($newdata['locality']) ? $newdata['locality'] : $newdata['city_name'],
                'created_at' => date('d M Y', strtotime($newdata['created_at'])),
                'images' => $single_info["images"],
                'custom_fields' => $additional_info,
                'category' => $get_lang,
                // 'brand_name' => $seller_brand,
                // 'brand_logo' => $brand_logo,
                'sellerId' => $seller,
                'is_fav' => !empty($fav) && ($fav->count() > 0) ? true : false,
                'ad_type' => !empty($adtype) ? Str_replace('_', ' ', strtoupper($adtype->ad_type)) : "",
                'currency_symbol' => $symbol,
                'giving_away' => $newdata['giving_away']
            );
        }
        // if(!empty($lc)){
        //     $shops = BusinessProfile::get_details($lc);
        // }else{
        //     $shops = BusinessProfile::all();
        // }
        // $shop_list = array();
        // foreach ($shops as $shop) {
        //     $veri_id = $shop->verifcation_id;
        //     $seller_id = Verificationrequest::where('id', $veri_id)->value('user_id');
        //     $shop_list[] = array(
        //         'brand_name' => $shop->brand_name,
        //         'brand_title' => $shop->brand_title,
        //         'brand_logo' => Url::to('storage/business-profile/' . $shop->brand_logo),
        //         'description' => $shop->description,
        //         'contact_number' => $shop->contact_number,
        //         'address' => $shop->address,
        //         'seller_id' => $seller_id,
        //         'city' => TblCity::where('id',  $shop->city)->get('locality')
        //     );
        // }
        // $category_blocks = array();
        // $category_main = TblCategory::whereNull('parent_id')->pluck('id')->toArray();
        // $token = $this->getBearerToken();
        // if (!empty($token['code']) && ($token['code'] == 200)) {
        //     $user = $this->getLoggedUser($token['token']);
        //     if (!empty($user)) {
        //         // dd($user);
        //         $lan_code = User::where('id',$user)->value('preferred_language');
        //     } else {
        //         $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
        //         $response = [
        //             'success' => false,
        //             'code' => 0,
        //             'message' => "Invalid User"
        //         ];
        //     }
        // }else{
        //     $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
        // }
        // foreach ($category_main as $cat_id) {
        //     // dd($cat_id);
        //     $category_block = TblCategory::getHomeCatBlock($cat_id);
        //     $category_main_name = TblCategory::where('id', $cat_id)->value('title');
        //     $cat_main =Languages::where('lang_code',$lan_code)->where('lang_org_text',$category_main_name)->value('lang_text');
        //     $cat_main_name =!empty($cat_main)?$cat_main: $cate_blk['title'];
        //     foreach ($category_block as $cate_blk) {
        //         // dd($cate_blk);
        //         $cat_block_image = !empty($cate_blk['block_image']) ? URL::to('storage/category_block/' . $cate_blk['block_image']) : "";
        //         $cat_url = TblCategory::where('slug', $cate_blk['block_image_url'])->value('id');
        //         $cat_title =Languages::where('lang_code',$lan_code)->where('lang_org_text',$cate_blk['title'])->value('lang_text');
        //         $cat_tle =!empty($cat_title)?$cat_title: $cate_blk['title'];
        //         $cat_name =Languages::where('lang_code',$lan_code)->where('lang_org_text',$cate_blk['block_name'])->value('lang_text');
        //         $cat_nme =!empty($cat_name)?$cat_name: $cate_blk['block_name'];
        //         $category_blocks[$cat_main_name][] = array(
        //             'block_title' => $cat_tle,
        //             'category_id' => $cate_blk['category_id'],
        //             'block_name'  => $cat_nme,
        //             'block_type'  => $cate_blk['block_type'],
        //             'block_image' => $cat_block_image,
        //             'block_image_url' => $cat_url
        //         );
        //     }
        // }
        // dd($category_blocks);
        $response = [
            'success' => true,
            'code' => 200,
            'recommended_post' => $recommended_post,
            'other_post' => $other_post,
            'total_count' => $latest_ads_count,
            // 'shop_list' => $shop_list,
            // 'category_block' => $category_blocks
        ];
        return response()->json($response);
    }
    /* Home Page */
    public function homewithoutlocation(Request $request)
    {
        $token = $this->getBearerToken();
        $user = "";
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
        }
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 10;
        //merge payment ads with free ads based on which one have highest count of records
        $final_result_array = TblPost::merge_payment_with_free_ads();
        $ids = ""; // Initialize to prevent undefined variable error
        if (count($final_result_array) > 0) {
            $ids = str_replace(array('[', ']'), '', htmlspecialchars(json_encode($final_result_array), ENT_NOQUOTES));
        }
        $addressids = array();
        $latest_ads_lat = array();
        $feature_ads_lat = "";
        $latest_ads_city = array();
        $feature_ads_city = "";
        $feature_ads_state = "";
        $latest_ads_state = array();
        $feature_ads_country = "";
        $latest_ads_country = array();
        //if user current location contains latitude and longitude
        if (!empty($request->get('latitude'))) {
            $addressids = $this->get_city_ids($request->latitude, $request->logitude, 20);
            if (!empty($addressids)) {
                // get latest ads for user current location of latitude and longitude                             
                $latest_ads_lat = TblPost::app_get_latest_ads_home_page($final_result_array, $ids, $limit, $page, $addressids);
                // get featureads ads for user current location of latitude and longitude
                $feature_ads_lat = TblPost::get_premium_ads('feature_ad', '10', $addressids);
            }
        }
        // CHECK FOR THE CURRENT LOC LATITUDE AND LONGITUDE HAVE RECORDS IF EMPTY MEANS GET RECORD FROM CURRENT CITY            
        if (!empty($request->get('city'))) {
            $current_city = TblCity::where('name', $request->city)->get(['latitude', 'logitude'])->first();
            if (!empty($current_city)) {
                $addressids = $this->get_city_ids($current_city->latitude, $current_city->logitude, 20);
                if (count($latest_ads_lat) == 0) {
                    // get latest ads for user current location of city
                    $latest_ads_city = TblPost::app_get_latest_ads_home_page($final_result_array, $ids, $limit, $page, $addressids);
                }
                if (empty($feature_ads_lat)) {
                    // get featureads ads for user current location of city
                    $feature_ads_city = TblPost::get_premium_ads('feature_ad', '10', $addressids);
                }
            }
        }
        // CHECK FOR THE CURRENT LOC OF CITY HAVE RECORDS IF EMPTY MEANS GET RECORD FROM CURRENT STATE
        if (!empty($request->get('state'))) {
            $current_state = TblState::where('name', $request->state)->pluck('id')->first();
            if (!empty($current_state)) {
                $get_state_cities = TblCity::where('state_id', $current_state)->get(['id']);
                foreach ($get_state_cities as $get_state_city) {
                    $addressids[] = $get_state_city->id;
                }
                if (count($latest_ads_city) == 0) {
                    // get latest ads for user current location of state         
                    $latest_ads_state = TblPost::app_get_latest_ads_home_page($final_result_array, $ids, $limit, $page, $addressids);
                }
                if (empty($feature_ads_city)) {
                    // get featureads ads for user current location of state
                    $feature_ads_state = TblPost::get_premium_ads('feature_ad', '10', $addressids);
                }
            }
        }
        // CHECK FOR THE CURRENT LOC OF STATE HAVE RECORDS IF EMPTY MEANS GET RECORD FROM CURRENT COUNTRY
        if (!empty($request->get('country'))) {
            $current_country = TblCountry::where('name', $request->country)->pluck('id')->first();
            if (!empty($current_country)) {
                $get_country_cities = TblCity::where('country_id', $current_country)->get(['id']);
                foreach ($get_country_cities as $get_country_city) {
                    $addressids[] = $get_country_city->id;
                }
                if (empty($feature_ads_state)) {
                    // get featureads ads for user current location of country
                    $feature_ads_country = TblPost::get_premium_ads('feature_ad', '10', $addressids);
                }
                if (count($latest_ads_state) == 0) {
                    // get latest ads for user current location of country         
                    $latest_ads_country = TblPost::app_get_latest_ads_home_page($final_result_array, $ids, $limit, $page, $addressids);
                }
            }
        }
        if (!empty($feature_ads_lat)) {
            $featurs_ad_lists = $feature_ads_lat;
        } else if (!empty($feature_ads_city)) {
            $featurs_ad_lists = $feature_ads_city;
        } else if (!empty($feature_ads_state)) {
            $featurs_ad_lists = $feature_ads_state;
        } else if (!empty($feature_ads_country)) {
            $featurs_ad_lists = $feature_ads_country;
        } else {
            $featurs_ad_lists = TblPost::get_premium_ads('feature_ad', '10');
        }
        $recommended_post = array();
        /* recommended post - feature posts */
        foreach ($featurs_ad_lists as $featurs_ad_list) {
            $fav = "";
            if (!empty($user)) {
                $fav = TblSavedPosts::where('user_id', $user)->where('post_id', $featurs_ad_list['id'])->get(['id']);
            }
            $currency = $this->post_currency($featurs_ad_list['id']);
            $single_info = TblPost::get_single_post_information($featurs_ad_list['id']);
            $additional_data = $this->getAdditionalInfo($featurs_ad_list['id']);
            $additional_info = array();
            foreach ($additional_data as $additional_data) {
                $additional_info[] = array(
                    'lable' => $additional_data['label'],
                    'value' => $additional_data['value']
                );
            }
            $recommended_post[] = array(
                'id' => $featurs_ad_list['id'],
                'title' => $featurs_ad_list['title'],
                'price' => $featurs_ad_list['price'],
                'description' => $featurs_ad_list['description'],
                'city_name' => !empty($featurs_ad_list['locality']) ? $featurs_ad_list['locality'] : $featurs_ad_list['city_name'],
                'created_at' => date('d M Y', strtotime($featurs_ad_list['created_at'])),
                'ad_type' => Str_replace('_', ' ', strtoupper($featurs_ad_list['ad_type'])),
                'images' => $single_info["images"],
                'custom_fields' => $additional_info,
                'is_fav' => !empty($fav) && ($fav->count() > 0) ? true : false,
                'currency_symbol' => $currency,
                'giving_away' => $featurs_ad_list['giving_away']
            );
        }
        if (count($latest_ads_lat) > 0) {
            $lates_ads = $latest_ads_lat;
            $latest_ads_count = TblPost::app_get_latest_ads_count($final_result_array, $ids, $limit, $page, $addressids);
        } else if (count($latest_ads_city) > 0) {
            $lates_ads = $latest_ads_city;
            $latest_ads_count = TblPost::app_get_latest_ads_count($final_result_array, $ids, $limit, $page, $addressids);
        } else if (count($latest_ads_state) > 0) {
            $lates_ads = $latest_ads_state;
            $latest_ads_count = TblPost::app_get_latest_ads_count($final_result_array, $ids, $limit, $page, $addressids);
        } else if (count($latest_ads_country) > 0) {
            $lates_ads = $latest_ads_country;
            $latest_ads_count = TblPost::app_get_latest_ads_count($final_result_array, $ids, $limit, $page, $addressids);
        } else {
            $lates_ads = TblPost::app_get_latest_ads_home_page($final_result_array, $ids, $limit, $page);
            $latest_ads_count = TblPost::app_get_latest_ads_count($final_result_array, $ids, $limit, $page);
        }
        $newdatas = $lates_ads;
        $other_post = array();
        foreach ($newdatas as $newdata) {
            $fav = "";
            if (!empty($user)) {
                $fav = TblSavedPosts::where('user_id', $user)->where('post_id', $newdata['id'])->get(['id']);
            }
            $currency = $this->post_currency($newdata['id']);
            $adtype = TblPost::getAddtype($newdata['id']);
            $single_info = TblPost::get_single_post_information($newdata['id']);
            $additional_data = $this->getAdditionalInfo($newdata['id']);
            $additional_info = array();
            foreach ($additional_data as $additional_data) {
                $additional_info[] = array(
                    'lable' => $additional_data['label'],
                    'value' => $additional_data['value']
                );
            }
            $categor_id = TblPost::where('id', $newdata['id'])->value('category_id');
            $cat = TblCategory::where('id', $categor_id)->first();
            $cat_filter = array();
            $cat_name = "";
            if ($cat->parent_id == null) {
                $cat_filter = TblCategory::descendantsAndSelf($cat->id);
                // dd($cat->id);
                $cat_name = $cat->title;
            } else {
                $cate = TblCategory::where('id', $cat->parent_id)->first();
                // dd($cate->id);
                $cat_filter = TblCategory::descendantsAndSelf($cate->id);
                $cat_name = $cate->title;
            }
            $other_post[] = array(
                'id' => $newdata['id'],
                'title' => $newdata['title'],
                'price' => $newdata['price'],
                'description' => $newdata['description'],
                'city_name' => !empty($newdata['locality']) ? $newdata['locality'] : $newdata['city_name'],
                'created_at' => date('d M Y', strtotime($newdata['created_at'])),
                'images' => $single_info["images"],
                'custom_fields' => $additional_info,
                'category' => $cat_name,
                'is_fav' => !empty($fav) && ($fav->count() > 0) ? true : false,
                'ad_type' => !empty($adtype) ? Str_replace('_', ' ', strtoupper($adtype->ad_type)) : "",
                'currency_symbol' => $currency,
                'giving_away' => $newdata['giving_away']
            );
        }
        $response = [
            'success' => true,
            'code' => 200,
            'recommended_post' => $recommended_post,
            'other_post' => $other_post,
            'total_count' => $latest_ads_count
        ];
        return response()->json($response);
    }
    /* search list */
    public function searchlist(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $data = array();
                $addressids = array();
                $page = $request->has('page') ? $request->get('page') : 1;
                $limit = $request->has('limit') ? $request->get('limit') : 10;
                $cat_id = $request->category_id;
                $lat = $request->lat;
                $lng = $request->lon;
                $distance = $request->distance;
                $search = $request->s;
                $price_sort_by = $request->sort_by;
                $posted_within = $request->posted_within;
                /* no need to show the blocked user posts */
                $blockedUsers = User::blocked_users();
                $postDetail = TblPost::whereNotIn('tbl_posts.user_id', $blockedUsers)->where('tbl_posts.sold_status', 0);
                /* Text box search */
                if ($search != "") {
                    $postDetail->where('tbl_posts.title', 'like', '%' . $search . '%');
                }
                /* Category id filter */
                if ($cat_id != "") {
                    $cat = TblCategory::where('id', $cat_id)->first();
                    $subcategory = TblCategory::descendantsAndSelf($cat->id);
                    foreach ($subcategory as $subcat) {
                        $sids[] = $subcat->id;
                    }
                    $postDetail->whereIn('tbl_posts.category_id', $sids);
                }
                if (!empty($lat)) {
                    $cities_lat_lng = TblCity::where('latitude', 'like', '%' . bcdiv($lat, 1, 3) . '%')->where('logitude', 'like', '%' . bcdiv($lng, 1, 3) . '%')->pluck('id')->first();
                    $current_address_ids = array();
                    $addressidss = array();
                    if (!empty($cities_lat_lng)) {
                        $current_address_ids[] = $cities_lat_lng;
                    }
                    if (!empty($distance)) {
                        $addressidss = $this->get_city_ids($lat, $lng, $distance);
                    } else {
                        $addressidss = $this->get_city_ids($lat, $lng, 30);
                    }
                    $addressids = array_merge($current_address_ids, $addressidss);
                }
                if (!empty($addressids)) {
                    $postDetail->whereIn('tbl_posts.city', $addressids);
                }
                if (!empty($posted_within)) {
                    if ($posted_within == "today") {
                        $postDetail->whereDate('tbl_posts.created_at', Carbon::today());
                    } else if ($posted_within == "weekly") {
                        $previous_week = strtotime("-1 week +1 day");
                        $start_week = strtotime("last sunday midnight", $previous_week);
                        $end_week = strtotime("next saturday", $start_week);
                        $start_week = date("Y-m-d", $start_week);
                        $end_week = date("Y-m-d", $end_week);
                        $postDetail->whereBetween('tbl_posts.created_at', [$start_week, $end_week]);
                    } else if ($posted_within == "monthly") {
                        $postDetail->whereMonth('tbl_posts.created_at', '=', Carbon::now()->subMonth()->month);
                    }
                }
                /* custom fields fillter start */
                $array = array();
                $filter = $request->fullUrl();
                $postids = array();
                $numberids = array();
                $selectpostids = array();
                // Function to encode values in an array
                function encodeArrayValues($array)
                {
                    foreach ($array as $key => $value) {
                        if ($key !== 'price_range') {
                            $array[$key] = urlencode($value);
                        }
                    }
                    return $array;
                }
                // Parse the URL and encode values
                parse_str(parse_url($filter, PHP_URL_QUERY), $array);
                $array = encodeArrayValues($array);
                // dd($filter, $array);
                foreach ($array as $key => $value) {
                    if (preg_match('/field_/', $key)) {
                        Session::put(['is_custome_fields' => 1]);
                        break;
                    } else if ($key == 'price_range') {
                        Session::put(['is_custome_fields' => 1]);
                        break;
                    } else {
                        Session::forget('is_custome_fields');
                    }
                }
                $is_custome_checkbox = 0;
                $is_custome_select = 0;
                $is_custome_number = 0;
                // dd($array);
                /* type number filter */
                /* type number filter */
                foreach ($array as $key => $value) {
                    $cate = TblCategory::where('id', $request->category_id)->first();
                    if ($key == "price_range") {
                        $is_custome_number = 1;
                        $values = explode(',', $value);
                        $res1 = $values[0];
                        $res2 = $values[1];
                        if ((int) ($res1) && (int) ($res2)) {
                            $res11 = preg_replace("/[^0-9]/", "", $res1);
                            $res22 = preg_replace("/[^0-9]/", "", $res2);
                            $range1 = (int) round($res11);
                            $range2 = (int) round($res22);
                            DB::enableQueryLog();
                            $postvalues = TblPost::where('active', '1')
                                ->where('category_id', $cate->id)->whereBetween('price', [$range1, $range2])->get();
                            $query = DB::getQueryLog();
                            //    dd($query);
                            //    dd($postva);
                            $selectpostids = array();
                            $numberids = array();
                            foreach ($postvalues as $postvalue) {
                                $numberids[] = $postvalue->id;
                                $selectpostids[] = $postvalue->id;
                            }
                            // dd($selectpostids);
                        }
                    }
                }
                foreach ($array as $key => $value) {
                    if (preg_match('/field_/', $key)) {
                        $cate = TblCategory::where('id', $request->category_id)->first();
                        $field_name = explode('_', $key)[1];
                        if (!empty($field_name)) {
                            $first = (str_replace('*', ' ', ucfirst($field_name)));
                            if (($first != "Brand and model") && ($first != "Modelswithbrand")) {
                                $field_detail = TblFieldsDetail::where('name', $first)
                                    ->where('active', '1')
                                    ->where('cat_id', $cate->id)
                                    ->first();
                                if ($field_detail->type == "number") {
                                    $is_custome_number = 1;
                                    $values = explode(',', $value);
                                    $range1 = (int) round($values[0]);
                                    $range2 = (int) round($values[1]);
                                    $postvalues = TblPostValue::where('field_id', $field_detail->id)->whereBetween('value', [$range1, $range2])->get();
                                    foreach ($postvalues as $postvalue) {
                                        $numberids[] = $postvalue->post_id;
                                    }
                                }
                            }
                        }
                    }
                }
                /* type select filter */
                foreach ($array as $key => $value) {
                    if (preg_match('/field_/', $key)) {
                        $cate = TblCategory::where('id', $request->category_id)->first();
                        $field_name = explode('_', $key)[1];
                        if (!empty($field_name)) {
                            $first = (str_replace('*', ' ', ucfirst($field_name)));
                            if ($first == "Modelswithbrand") {
                                $is_custome_select = 1;
                                $postvalues = TblPostValue::where('field_id', $field_detail->id)
                                    ->where('active', '1')
                                    ->whereRaw("find_in_set('$value',value)")
                                    ->get();
                                foreach ($postvalues as $postvalue) {
                                    $selectpostids[] = $postvalue->post_id;
                                }
                            } else {
                                $field_detail = TblFieldsDetail::where('name', $first)
                                    ->where('active', '1')
                                    ->where('cat_id', $cate->id)
                                    ->first();
                                if ($field_detail->type == "select") {
                                    $is_custome_select = 1;
                                    if ($first == "Brand and model") {
                                        $get_brand_id = TblFieldsOption::where('id', 'like', '%' . $value . '%')->pluck('id')->first();
                                        $postvalues = TblPostValue::where('field_id', $field_detail->id)
                                            ->where('active', '1')
                                            ->whereRaw("find_in_set('$get_brand_id',value)")
                                            ->get();
                                    } else {
                                        DB::enableQueryLog();
                                        $postvalues = TblPostValue::where('field_id', $field_detail->id)
                                            ->where('active', '1')
                                            ->whereRaw("find_in_set('$value',value)")
                                            ->get();
                                        $query = DB::getQueryLog();
                                        // dd($query,$postvalues);
                                    }
                                    foreach ($postvalues as $postvalue) {
                                        $selectpostids[] = $postvalue->post_id;
                                    }
                                }
                            }
                        }
                    }
                }
                /* type text box and check box filter */
                foreach ($array as $key => $value) {
                    if (preg_match('/field_/', $key)) {
                        $cate = TblCategory::where('id', $request->category_id)->first();
                        $field_name = explode('_', $key)[1];
                        if (!empty($field_name)) {
                            $first = (str_replace('*', ' ', ucfirst($field_name)));
                            if (($first != "Brand and model") && ($first != "Modelswithbrand")) {
                                $field_detail = TblFieldsDetail::where('name', $first)
                                    ->where('active', '1')
                                    ->where('cat_id', $cate->id)
                                    ->first();
                                if ($field_detail->type == "radio-group") {
                                    $is_custome_checkbox = 1;
                                    $postvalues = TblPostValue::where('field_id', $field_detail->id)
                                        ->where('active', '1')
                                        ->whereRaw("find_in_set('" . $value . "',value)")
                                        ->get();
                                    foreach ($postvalues as $postvalue) {
                                        $postids[] = $postvalue->post_id;
                                    }
                                }
                                if ($field_detail->type == "checkbox-group") {
                                    $is_custome_checkbox = 1;
                                    $postvalues = TblPostValue::where('field_id', $field_detail->id)
                                        ->where('active', '1')
                                        ->whereRaw("find_in_set('" . $value . "',value)")
                                        ->get();
                                    foreach ($postvalues as $postvalue) {
                                        $postids[] = $postvalue->post_id;
                                    }
                                }
                            }
                        }
                    }
                }
                $value_session = session::get('is_custome_fields');
                // dd($selectpostids,$numberids,$postids,$is_custome_select,$is_custome_number,$is_custome_checkbox,$value_session);
                if ($value_session == 1) {
                    if (($is_custome_select == 1) && ($is_custome_number == 0) && ($is_custome_checkbox == 0)) {
                        if (!empty($postids)) {
                            $postDetail->whereIn('tbl_posts.id', $postids);
                        }
                        if (!empty($numberids)) {
                            $postDetail->whereIn('tbl_posts.id', $numberids);
                        }
                        $postDetail->whereIn('tbl_posts.id', $selectpostids);
                    } else if (($is_custome_number == 1) && ($is_custome_select == 0) && ($is_custome_checkbox == 0)) {
                        // dd('njdj');
                        if (!empty($postids)) {
                            $postDetail->whereIn('tbl_posts.id', $postids);
                        }
                        $postDetail->whereIn('tbl_posts.id', $numberids);
                        if (!empty($selectpostids)) {
                            $postDetail->whereIn('tbl_posts.id', $selectpostids);
                        }
                    } else if ($is_custome_checkbox == 1) {
                        if ($is_custome_select == 1) {
                            $postDetail->whereIn('tbl_posts.id', $selectpostids);
                        }
                        if ($is_custome_number == 1) {
                            $postDetail->whereIn('tbl_posts.id', $numberids);
                        }
                        $postDetail->whereIn('tbl_posts.id', $postids);
                    } else {
                        if ($is_custome_checkbox == 1) {
                            $postDetail->whereIn('tbl_posts.id', $postids);
                        }
                        if ($is_custome_number == 1) {
                            $postDetail->whereIn('tbl_posts.id', $numberids);
                        }
                        if ($is_custome_select == 1) {
                            $postDetail->whereIn('tbl_posts.id', $selectpostids);
                        }
                    }
                }
                /* custome filter end */
                $final_postdetail = $postDetail->pluck('id')->toArray();
                // dd($final_postdetail);
                $payment_ads_ids = array();
                $free_ads_ids = array();
                /* get unexpired payment post */
                $payment_ids_array = TblPost::get_unexpired_payment_post_ids();
                /* get unexpired free post */
                $free_ids_array = TblPost::get_unexpired_free_post_ids();
                foreach ($payment_ids_array as $payment_ids_array) {
                    if (in_array($payment_ids_array, $final_postdetail)) {
                        $payment_ads_ids[] = $payment_ids_array;
                    }
                }
                foreach ($free_ids_array as $free_ids_array) {
                    if (in_array($free_ids_array, $final_postdetail)) {
                        $free_ads_ids[] = $free_ids_array;
                    }
                }
                $get_ads_big_cnt = array(count($free_ads_ids), count($payment_ads_ids));
                $maxs = array_keys($get_ads_big_cnt, max($get_ads_big_cnt));
                if ($maxs[0] == 0) {
                    $big = $free_ads_ids;
                    $small = $payment_ads_ids;
                } else {
                    $big = $payment_ads_ids;
                    $small = $free_ads_ids;
                }
                /* show the lowest number records ads type on the top 4 */
                for ($pid = 0; $pid <= count($big); $pid += 4) {
                    $ids = (array_slice($small, $pid, 4));
                    if ($pid != 0) {
                        $pid = $pid + 4;
                    }
                    foreach ($ids as $k) {
                        array_splice($big, $pid, 0, $k);
                    }
                }
                // filter by price low to high - asc and high to low - desc   
                if (!empty($price_sort_by) && ($price_sort_by == "asc")) {
                    $final_result_array = TblPost::whereIn('id', $big)->orderBy(DB::raw("CAST(`price` AS DECIMAL(18,2))"), 'asc')->pluck('id');
                } else if (!empty($price_sort_by) && ($price_sort_by == "desc")) {
                    $final_result_array = TblPost::whereIn('id', $big)->orderBy(DB::raw("CAST(`price` AS DECIMAL(18,2))"), 'desc')->pluck('id');
                } else {
                    $final_result_array = $big;
                }
                /* show the record based on same array order */
                $ids = str_replace(array('[', ']'), '', htmlspecialchars(json_encode($final_result_array), ENT_NOQUOTES));
                if (count($final_result_array) > 0) {
                    $total_post_cnt = TblPost::whereIn('tbl_posts.id', $final_result_array)
                        ->orderByRaw(DB::raw("FIELD(tbl_posts.id, " . $ids . ")"))
                        ->whereNull('tbl_posts.deleted_at')->where('tbl_posts.sold_status', 0)->where('tbl_posts.active', 1)->get()->count();
                    $newdatas = TblPost::whereIn('tbl_posts.id', $final_result_array)
                        ->orderByRaw(DB::raw("FIELD(tbl_posts.id, " . $ids . ")"))
                        ->whereNull('tbl_posts.deleted_at')
                        ->where('tbl_posts.active', 1)
                        ->where('tbl_posts.sold_status', 0)
                        ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                        ->join('users', 'users.id', '=', 'tbl_posts.user_id')
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get(['tbl_posts.id as id', 'tbl_posts.giving_away as giving_away', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.currency_id as currency_id', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.description as description', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_cities.name as city_name', 'tbl_cities.latitude as c_lat', 'tbl_cities.logitude as c_lng', 'users.name as posted_by']);
                } else {
                    $total_post_cnt = 0;
                    $newdatas = "";
                }
                if (!empty($newdatas)) {
                    foreach ($newdatas as $newdata) {
                        $fav = "";
                        if (!empty($user)) {
                            $fav = TblSavedPosts::where('user_id', $user)->where('post_id', $newdata['id'])->get();
                        }
                        $currency = $this->post_currency($newdata['id']);
                        $adtype = TblPost::getAddtype($newdata['id']);
                        $single_info = TblPost::get_single_post_information($newdata['id']);
                        $city_name = !empty($newdata['locality']) ? $newdata['locality'] : $newdata['city_name'];
                        // if (!empty($user)) {
                        //     $post_currency_id = TblPost::where('id', $newdata['id'])->value('currency_id');
                        //     $user_currency = TblPost::userCurrencyConversion($user, $newdata['price'], $post_currency_id);
                        //     $price = $user_currency['convert_cur'];
                        //     $symbol = $user_currency['convert_sym'];
                        // } else {
                        //     $price = $newdata['price'];
                        //     $symbol = $currency;
                        // }
                        $price = $newdata['price'];
                        $symbol = $currency;
                        $data[] = array(
                            'id' => $newdata['id'],
                            'title' => $newdata['title'],
                            'price' => $price,
                            'description' => $newdata['description'],
                            'city_name' => $city_name,
                            'latitude' => $newdata['c_lat'],
                            'longitude' => $newdata['c_lng'],
                            'created_at' => date('d M Y', strtotime($newdata['created_at'])),
                            'images' => $single_info["images"],
                            'is_fav' => !empty($fav) && ($fav->count() > 0) ? true : false,
                            'ad_type' => !empty($adtype) ? Str_replace('_', ' ', strtoupper($adtype->ad_type)) : "",
                            'currency_symbol' => $symbol,
                            'giving_away' => $newdata['giving_away'],
                            'visited' => false,
                        );
                    }
                }
                $custom_fields = array();
                if (!empty($array)) {
                    foreach ($array as $key => $value) {
                        if (preg_match('/field_/', $key)) {
                            $first = (str_replace('field_', '', $key));
                            $custom_fields[] = array(
                                'name' => $first,
                                'value' => $value
                            );
                        }
                    }
                }
                // banner ads
                $category_banner = array();
                $get_cat_banners = TblCategory::get_cat_banners(NULL, $cat_id);
                if (!empty($get_cat_banners)) {
                    $category_banner = $get_cat_banners;
                }
                //paid banners for this selected category
                $paid_category_banner = array();
                $get_paid_cat_banners = TblCategory::get_paid_cat_banners(NULL, $cat_id, "app");
                if (!empty($get_paid_cat_banners)) {
                    $paid_category_banner = $get_paid_cat_banners;
                }
                $visible_banners = array_merge($category_banner, $paid_category_banner);
                $response = [
                    'success' => true,
                    'code' => 200,
                    'custom_fields' => $custom_fields,
                    'data' => $data,
                    'total_count' => $total_post_cnt,
                    'banners' => $visible_banners
                ];
            } else {
                return $this->sendError('Invalid User!');
            }
        } else {
            return $this->sendError('Invalid Authorization Bearer Token!');
        }
        return response()->json($response);
    }
    public function forgot_password(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);
        $email = $request->email;
        $user = User::where('email', $request->email)->whereNull('deleted_at')->first();
        if (!empty($user)) {
            $token = app('auth.password.broker')->createToken($user);
            // // $token = Str::random(60);
            // DB::table('password_resets')->insert(
            //     ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now()]
            // );
            // Mail::send('auth.verify', ['token' => $token, 'email' => $request->email], function ($message) use ($request) {
            //     $message->to($request->email);
            //     $message->subject('Reset Password Notification');
            // });
            $url = $request->app_url . '/reset-password/' . $token . '?email=' . $email;
            $mail_data = array("send_maildata" => array('to_id' => $user->id, 'message' => "", 'subject' => "", "ad_url" => $url));
            $mail_key = "app_forgot_password";
            $mail_response = Setting::notification_mail($mail_data, $mail_key);
            $response = [
                'success' => true,
                'code' => 200,
                'message' => 'Reset password link sent to your mail successfully!'
            ];
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => 'Invalid Email ID'
            ];
        }
        return response()->json($response);
    }
    public function change_password(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                // dd($user);
                $lan_code = User::where('id', $user)->value('preferred_language');
            } else {
                $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
        }
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $cuser = User::find($user);
                $request->validate([
                    'current_password' => 'required',
                    'password' => 'required',
                    'confirm_password' => 'required',
                ]);
                if (!Hash::check($request->current_password, $cuser->password)) {
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => 'Current password mismatch!'
                    ];
                    return response()->json($response);
                } else {
                    User::find($user)->update(['password' => Hash::make($request->password)]);
                    // sent notification
                    $settings = Setting::get_logos();
                    $site_name = $settings['name'];
                    $get_user_info = User::where('id', $user)->first();
                    $slug = url('/');
                    $get_admin = User::role('superadmin')->get();
                    $admin_id = $get_admin[0]->id;
                    $headers = array(
                        'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                        'Content-Type: application/json',
                    );
                    $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
                    $originalMessageText = 'Your account password has been updated.';
                    $originalTitleText = 'Password Updated in :site!';
                    // Translate the messages
                    $translatedMessage = TblNotifications::translate($lan_code, $originalMessageText, []);
                    $translatedTitle = TblNotifications::translate($lan_code, $originalTitleText, [
                        'site' => $site_name,
                    ]);
                    $message = array(
                        "notifydata" => array(
                            'to_id' => $user,
                            'from_id' => $admin_id,
                            'message' => $translatedMessage,
                            'notify_from' => 'update_password',
                            'notify_title' => $translatedTitle,
                            'post_id' => "",
                            'slug' => $slug
                        )
                    );
                    // $message = array("notifydata" => array('to_id' => $user, 'from_id' => $admin_id, 'message' => "Your account password has been updated.", 'notify_from' => 'update_password', 'notify_title' => "Password Updated In " . $site_name . " !..", 'post_id' => "", 'slug' => $slug));
                    TblPost::send_push_notification($fcmid, $message);
                    //end notification 
                    $response = [
                        'success' => true,
                        'code' => 200,
                        'message' => 'Your password has beed updated successfully!'
                    ];
                    return response()->json($response);
                }
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User",
                ];
                return response()->json($response);
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!",
            ];
            return response()->json($response);
        }
    }
    public function reset_password(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $email = $request->email;
        if (!empty($email)) {
            $user = User::where('email', $email)->whereNull('deleted_at')->pluck('id')->first();
            if (!empty($user)) {
                $get = DB::table('password_resets')->where('email', $email)->count();
                if ($get > 0) {
                    DB::table('password_resets')->where('email', $email)->delete();
                    User::find($user)->update(['password' => Hash::make($request->password)]);
                    $response = $this->sendSuccess("Your password has beed updated successfully!");
                } else {
                    $response = $this->sendError("Link has been expired, please try again the forgot password to reset the link");
                }
            } else {
                $response = $this->sendError("Invalid Credentials");
            }
        } else {
            $response = $this->sendError("Invalid Credentials");
        }
        return response()->json($response);
    }
    public function get_city_ids($lat, $lng, $distance)
    {
        $query = "
                SELECT id FROM (
                    SELECT *, 
                        (
                            (
                                (
                                    acos(
                                        sin(( $lat * pi() / 180))
                                        *
                                        sin(( `latitude` * pi() / 180)) + cos(( $lat * pi() /180 ))
                                        *
                                        cos(( `latitude` * pi() / 180)) * cos((( $lng - `logitude`) * pi()/180)))
                                ) * 180/pi()
                            ) * 60 * 1.1515 * 1.609344
                        )
                    as distance FROM `tbl_cities`
                ) tbl_cities
                WHERE distance <= $distance";
        $results = DB::select($query);
        $json_results = json_encode($results);
        $josn_decode_res = json_decode($json_results, true);
        $city_ids = array();
        foreach ($josn_decode_res as $josn_decode_res) {
            $city_ids[] = $josn_decode_res['id'];
        }
        return $city_ids;
    }
    public function blacklist_words()
    {
        $data = array();
        $data = TblPost::get_blacklist();
        $response = [
            'success' => true,
            'code' => 200,
            'data' => json_decode($data)
        ];
        return response()->json($response);
    }
    public function static_pages($slug)
    {
        $datas = TblStaticpage::where('slug', $slug)->first();
        $res = $datas->makeHidden(['slug', 'id', 'created_at', 'updated_at']);
        $data = array();
        if (!empty($datas)) {
            $data = array(
                'title' => $datas->title,
                'content' => $datas->content
            );
        }
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $data
        ];
        return response()->json($response);
    }
    public function report_types()
    {
        $reports = ReportType::where('type', 'post')->get();
        $data = array();
        foreach ($reports as $report) {
            $data[] = array(
                'id' => $report->id,
                'name' => $report->name
            );
        }
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $data
        ];
        return response()->json($response);
    }
    public function user_report_types()
    {
        $reports = ReportType::where('type', 'user')->get();
        $data = array();
        foreach ($reports as $report) {
            $data[] = array(
                'id' => $report->id,
                'name' => $report->name
            );
        }
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $data
        ];
        return response()->json($response);
    }
    public function post_report(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $check = TblReportThisAd::where('user_id', $user)->where('post_id', $request->post_id)->first();
                $check_post_user = TblPost::where('id', $request->post_id)->where('user_id', $user)->first();
                if (empty($check)) {
                    if (empty($check_post_user)) {
                        TblReportThisAd::create([
                            'user_id' => $user,
                            'post_id' => $request->post_id,
                            'report_type_id' => $request->report_type_id,
                            'comment' => $request->comment,
                        ]);
                        $response = [
                            'success' => true,
                            'code' => 200,
                            'message' => "Your report has been taken, we will take action as soon as possible.."
                        ];
                        return response()->json($response);
                    } else {
                        $response = [
                            'success' => false,
                            'code' => 0,
                            'message' => "You are the owner of this post!, so you cant report the post!",
                        ];
                        return response()->json($response);
                    }
                } else {
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => "You already posted the report for this post!",
                    ];
                    return response()->json($response);
                }
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User",
                ];
                return response()->json($response);
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!",
            ];
            return response()->json($response);
        }
    }
    public function post_user_report(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $check = TblReportThisUser::where('user_id', $user)->where('reported_user_id', $request->reported_user_id)->first();
                if (empty($check)) {
                    if ($user !== $request->reported_user_id) {
                        TblReportThisUser::create([
                            'user_id' => $user,
                            'reported_user_id' => $request->reported_user_id,
                            'report_type_id' => $request->report_type_id,
                            'comment' => $request->comment,
                        ]);
                        $response = [
                            'success' => true,
                            'code' => 200,
                            'message' => "Your report has been taken, we will take action as soon as possible.."
                        ];
                        return response()->json($response);
                    } else {
                        $response = [
                            'success' => false,
                            'code' => 0,
                            'message' => "You can't report yourself!",
                        ];
                        return response()->json($response);
                    }
                } else {
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => "You already repoted this user!",
                    ];
                    return response()->json($response);
                }
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User",
                ];
                return response()->json($response);
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!",
            ];
            return response()->json($response);
        }
    }
    public function welcome_chat(Request $request)
    {
        $token = $this->getBearerToken();
        // if (!empty($token['code']) && ($token['code'] == 200)) {
        //     $user = $this->getLoggedUser($token['token']);
        //     if (!empty($user)) {
        //         // dd($user);
        //         $lan_code = User::where('id', $user)->value('preferred_language');
        //     } else {
        //         $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
        //         $response = [
        //             'success' => false,
        //             'code' => 0,
        //             'message' => "Invalid User"
        //         ];
        //     }
        // } else {
        //     $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
        // }
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $check_chat = TblChat::where('post_id', $request->post_id)->where('from_id', $user)->where('to_id', $user)->first();
                $requestto = $request->to;
                $check_wel_chat = TblChat::where('post_id', $request->post_id)->where(function ($q) use ($user, $requestto) {
                    $q->where('tbl_chats.receiver', $user)
                        ->orWhere('tbl_chats.receiver', $requestto);
                })->orderBy('id', 'desc')->first();
                if (!empty($check_wel_chat)) {
                    $post_img = TblChat::getPostImg($request->post_id);
                    $sendername = TblChat::getSender($request->to);
                    $check_post_owner = TblPost::where('id', $request->post_id)->first();
                } else {
                    $msg = "Welcome!";
                    $to = $request->to;
                    $post_id = $request->post_id;
                    $user_id = $user;
                    $image = "";
                    $check_post_owner = TblPost::where('id', $post_id)->first();
                    if (!empty($check_post_owner)) {
                        if ($check_post_owner->user_id == $user_id) {
                            $receiver = $to;
                        } else {
                            $receiver = $user_id;
                        }
                    } else {
                        $receiver = $user_id;
                    }
                    $data = TblChat::create([
                        'post_id' => $post_id,
                        'msg' => $msg,
                        'from_id' => $user_id,
                        'to_id' => $to,
                        'receiver' => $receiver,
                    ]);
                    $slug = URL::to('/chatting?to=' . $user_id . '&p=' . $post_id . '&type=old');
                    //chat notification start
                    $settings = Setting::get_logos();
                    $site_name = $settings['name'];
                    $get_user_info = User::where('id', $user_id)->first();
                    $get_post_info = TblPost::where('id', $post_id)->first();
                    $get_seller_info = User::where('id', $to)->first();
                    $headers = array(
                        'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                        'Content-Type: application/json',
                    );
                    $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                    $lan_code = User::where('id', $to)->value('preferred_language');
                    $originalMessageText = 'Contacted you on :post!';
                    $originalTitleText = 'New chat in :site!';
                    // Translate the messages
                    $translatedMessage = TblNotifications::translate($lan_code, $originalMessageText, [
                        'post' => $get_post_info->title,
                    ]);
                    $translatedTitle = TblNotifications::translate($lan_code, $originalTitleText, [
                        'site' => $site_name,
                    ]);
                    $message1 = array(
                        "notifydata" => array(
                            'to_id' => $to,
                            'from_id' => $user_id,
                            'message' => $translatedMessage,
                            'notify_from' => 'chat',
                            'notify_title' => $translatedTitle,
                            'post_id' => $post_id,
                            'slug' => $slug
                        )
                    );
                    // $message1 = array("notifydata" => array('to_id' => $to, 'from_id' => $user_id, 'message' => "contacted you on " . $get_post_info->title, 'notify_from' => 'chat', 'notify_title' => "New chat In " . $site_name . " !..", 'post_id' => $post_id, 'slug' => $slug));
                    // TblPost::send_push_notification($fcmid, $message1);
                    // $slug = URL::to('/chat');
                    $mail_data = array("send_maildata" => array('to_id' => $to, 'message' => "New user contacted you on " . $get_post_info->title, 'subject' => "New chat In " . $site_name . " !..", 'ad_url' => $slug));
                    $mail_key = "post_chat";
                    // Setting::notification_mail($mail_data, $mail_key);
                    //chat notification end
                    $inser_id = $data->id;
                    $get_last_chat = TblChat::where('id', $inser_id)->first();
                    // if ($get_last_chat->msg == "Welcome!") {
                    //     return response()->json([]);
                    // }
                    $post_img = TblChat::getPostImg($get_last_chat->post_id);
                    $sender = (($user == $get_last_chat->from_id) ? $get_last_chat->to_id : $get_last_chat->from_id);
                    $sendername = TblChat::getSender($sender);
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => [
                        'post_id' => $request->post_id,
                        'to_id' => $request->to,
                        'post_title' => $check_post_owner->title,
                        'profile_image' => $post_img,
                        'customer_name' => $sendername,
                    ]
                ];
                return response()->json($response);
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User!",
                ];
                return response()->json($response);
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!",
            ];
            return response()->json($response);
        }
    }
    public function send_chat(Request $request)
    {
        $token = $this->getBearerToken();

        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $msg = $request->message;
                $to = $request->to;
                $post_id = $request->post_id;
                $read_id = $request->read_id;
                $user_id = $user;
                $image = $request->image;
                $make_offer = $request->make_offer;
                $accept_offer = $request->accept_offer;
                $denied_offer = $request->denied_offer;
                $shared_location = $request->location;
                $check_post_owner = TblPost::where('id', $post_id)->pluck('user_id')->first();
                if ($check_post_owner == $user_id) {
                    $receiver = $to;
                } else {
                    $receiver = $user_id;
                }
                //notification chat
                $headers = array(
                    'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                    'Content-Type: application/json',
                );
                $slug = URL::to('/chatting?to=' . $user_id . '&p=' . $post_id . '&type=old');
                $settings = Setting::get_logos();
                $site_name = $settings['name'];
                $get_user_info = User::where('id', $user_id)->first();
                $get_post_info = TblPost::where('id', $post_id)->first();
                $get_seller_info = User::where('id', $to)->first();
                $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                $originalMessageText = ':post';
                // $originalMessageText = '';
                // $originalTitleText = 'New chat in :site!';
                $originalTitleText = 'JustReused';
                $lan_code = User::where('id', $to)->value('preferred_language');
                // Translate the messages
                $translatedMessage = TblNotifications::translate($lan_code, $originalMessageText, [
                    'post' => $msg,
                ]);
                $translatedTitle = TblNotifications::translate($lan_code, $originalTitleText, [
                    'site' => $site_name,
                ]);
                $message1 = array(
                    "notifydata" => array(
                        'to_id' => $to,
                        'from_id' => $user_id,
                        'message' => $translatedMessage,
                        'notify_from' => 'chat',
                        'notify_title' => $translatedTitle,
                        'post_id' => $post_id,
                        'slug' => $slug
                    )
                );

                // $message1 = array("notifydata" => array('to_id' => $to, 'from_id' => $user_id, 'message' => $get_user_info->name . " sent you a text - " . $msg, 'notify_from' => 'chat', 'notify_title' => "New chat in " . $site_name . " !..", 'post_id' => $post_id, 'slug' => $slug));
                //TblPost::send_push_notification($fcmid, $message1);

                //end notification
                if (!empty($msg)) {
                    $createMsg = TblChat::create([
                        'post_id' => $post_id,
                        'read_id' => $read_id,
                        'msg' => $msg,
                        'from_id' => $user_id,
                        'to_id' => $to,
                        'receiver' => $receiver,
                        'make_offer' => $make_offer,
                        'accept_offer' => $accept_offer,
                        'denied_offer' => $denied_offer
                    ]);
                }
                if (!empty($shared_location)) {
                    $createMsg = TblChat::create([
                        'post_id' => $post_id,
                        'from_id' => $user_id,
                        'read_id' => $read_id,
                        'to_id' => $to,
                        'receiver' => $receiver,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'make_offer' => 0,
                        "location" => $shared_location,
                        'denied_offer' => 0
                    ]);
                }
                if (!empty($image)) {
                    $extension = explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                    $imageName = 'chatimage/' . Str::random(15) . '.' . $extension;
                    $replace = substr($image, 0, strpos($image, ',') + 1);
                    $img = str_replace($replace, '', $image);
                    $k = str_replace(' ', '+', $img);
                    Storage::disk('public')->put($imageName, base64_decode($k));
                    $createMsg = TblChat::create([
                        'post_id' => $post_id,
                        'read_id' => $read_id,
                        'from_id' => $user_id,
                        'to_id' => $to,
                        'receiver' => $receiver,
                        'attachment' => $imageName,
                        'make_offer' => 0,
                        'accept_offer' => 0,
                        'denied_offer' => 0
                    ]);
                }
                // sent notification for make offer, accept offer, denied offer
                if (($make_offer == 1) || ($accept_offer == 1) || ($denied_offer == 1)) {
                    $settings = Setting::get_logos();
                    $site_name = $settings['name'];
                    $get_user_info = User::where('id', $user_id)->first();
                    $get_post_info = TblPost::where('id', $post_id)->first();
                    $get_seller_info = User::where('id', $to)->first();
                    $slug = URL::to('/chatting?to=' . $user_id . '&p=' . $post_id . '&type=old');
                    if ($make_offer == 1) {
                        $notify_from = "offer_request";
                        $notify_title = "New Offer Request In " . $site_name . " !.";
                        $offer_msg = $get_user_info->name . " sent offer request on your product " . $get_post_info->title;
                    } elseif ($denied_offer == 1) {
                        $notify_from = "deny_offer";
                        $notify_title = "Offer Request Denied In " . $site_name . " !.";
                        $offer_msg = "offer request Denied for this post -  " . $get_post_info->title;
                    } else {
                        $notify_from = "accept_offer";
                        $notify_title = "Offer Request Accepted In " . $site_name . " !.";
                        $offer_msg = "offer request has been accepted for this post -  " . $get_post_info->title;
                    }
                    $headers = array(
                        'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                        'Content-Type: application/json',
                    );
                    $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                    $message1 = array("notifydata" => array('to_id' => $to, 'from_id' => $user_id, 'message' => $offer_msg, 'notify_from' => $notify_from, 'notify_title' => $notify_title, 'post_id' => $post_id, 'slug' => $slug));
                    TblPost::send_push_notification($fcmid, $message1);
                    $mail_data = array("send_maildata" => array('to_id' => $to, 'message' => $offer_msg, 'subject' => $notify_title, 'ad_url' => $slug));
                    $mail_key = "make_offer";
                    Setting::notification_mail($mail_data, $mail_key, $headers);
                }
                // notification end
                // retrun last message data
                $last_chat = TblChat::find($createMsg->id);
                if ($last_chat->from_id == $user) {
                    $sender = "Me";
                } else {
                    $sender = "Others";
                }
                $chatmsg = array(
                    'chat_id' => $last_chat->id,
                    'from_id' => $last_chat->from_id,
                    'to_id' => $last_chat->to_id,
                    'receiver_id' => $last_chat->receiver,
                    'user_id' => $user,
                    'message' => $last_chat->msg,
                    'attachment' => !empty($last_chat->attachment) ? URL::to('storage/') . '/' . $last_chat->attachment : "",
                    'sender' => $sender,
                    'date' => date('Y-m-d', strtotime($last_chat->created_at)),
                    'time' => date('H:i a', strtotime($last_chat->created_at)),
                    'make_offer' => $last_chat->make_offer,
                    'accept_offer' => $last_chat->accept_offer,
                    'denied_offer' => $last_chat->denied_offer,
                    "location" => $last_chat->location,
                    "latitude" => $last_chat->latitude,
                    "longitude" => $last_chat->longitude,
                    "read_status" => $last_chat->read_status,
                );
                // print_r($chatmsg);
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $chatmsg,
                    'message' => "Message sent successfully!"
                ];
                return response()->json($response);
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User!",
                ];
                return response()->json($response);
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!",
            ];
            return response()->json($response);
        }
    }
    public function mychat_list(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $chatlist_data = array();
                $search = $request->search;
                $page = $request->has('page') ? $request->get('page') : 1;
                $limit = $request->has('limit') ? $request->get('limit') : 10;
                $chatlist = TblChat::where('tbl_chats.from_id', $user)
                    ->join('tbl_posts', function ($join) use ($search) {
                        $join->on('tbl_chats.post_id', '=', 'tbl_posts.id')
                            ->whereNull('tbl_posts.deleted_at')
                            ->where('tbl_posts.title', 'like', '%' . $search . '%')
                            ->where('tbl_posts.sold_status', 0);
                    })
                    ->orWhere('tbl_chats.to_id', $user)
                    ->whereNotNull('tbl_chats.msg')
                    ->whereNull('tbl_chats.deleted_at')
                    ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
                    ->orderBy('tbl_chats.created_at', 'desc')
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);
                $count = array();
                $uniquePostCount = TblChat::where('tbl_chats.from_id', $user)
                    ->join('tbl_posts', function ($join) use ($search) {
                        $join->on('tbl_chats.post_id', '=', 'tbl_posts.id')
                            ->whereNull('tbl_posts.deleted_at')
                            ->where('tbl_posts.title', 'like', '%' . $search . '%')
                            ->where('tbl_posts.sold_status', 0);
                    })
                    ->orWhere('tbl_chats.to_id', $user)
                    ->whereNotNull('tbl_chats.msg')
                    ->whereNull('tbl_chats.deleted_at')
                    ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
                    ->orderBy('tbl_chats.created_at', 'desc')
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);
                $uniqueSenderCount = []; // Store unique sender IDs
                foreach ($uniquePostCount as $chat) {
                    $sender = ($user == $chat->from_id) ? $chat->to_id : $chat->from_id;
                    if (!in_array($sender, $uniqueSenderCount)) {
                        $uniqueSenderCount[] = $sender;
                    }
                }
                foreach ($chatlist as $chat) {
                    $visible_posts = TblPost::check_payment_pack_expired($chat->post_id);
                    if (!empty($visible_posts)) {
                        $sender = ($user == $chat->from_id) ? $chat->to_id : $chat->from_id;
                        $sendername = TblChat::getSender($sender);
                        $lastchat = TblChat::getLastChatApp($user, $sender, $chat->post_id);
                        $block_status = TblChat::checkBlockedApp($chat->to_id, $chat->post_id, $user);
                        $check_post_deleted = TblPost::where('id', $chat->post_id)->whereNull('deleted_at')->pluck('id');
                        $unread_count = TblChat::getUnreadCount($user, $sender, $chat->post_id);
                        // dd($unread_count);
                        $count[] = TblChat::getUnreadCount($user, $sender, $chat->post_id);
                        $user_status = User::where('id', $sender)->get();
                        // dd($lastchat);
                        if (count($user_status) > 0) {
                            $last_chat = !empty($user_status[0]->last_chat_seen) ? date('d-m-y h:i A', strtotime($user_status[0]->last_chat_seen)) : "";
                            $block_status_id = TblBlockeduser::where('post_id', $chat->post_id)->where('blocked_id', $user)->OrderBy('id', 'desc')->pluck('block_status')->first();
                            $block_status_by = TblBlockeduser::where('post_id', $chat->post_id)->where('blocked_id', $sender)->where('blocked_by', $user)->OrderBy('id', 'desc')->pluck('block_status')->first();
                            $block_status = (!empty($block_status_id) || !empty($block_status_by)) ? 1 : 0;
                            $unblock_status = !empty($block_status_by) ? 1 : 0;
                            $prof_img = !empty($user_status[0]->profile_photo_path) ? URL::to('storage/' . $user_status[0]->profile_photo_path) : URL::to('/storage/noimage150.png');
                            $last_chat_msg = !empty($lastchat) ? $lastchat['msg'] : "";
                            $last_chat_id = !empty($lastchat) ? $lastchat['id'] : "";
                            $last_chat_img = !empty($lastchat) ? URL::to('storage/') . '/' . $lastchat['attachment'] : "";
                            $check_blocked = !empty($block_status) ? $block_status : 0;
                            $check_unblocked = !empty($unblock_status) ? $unblock_status : 0;
                            $is_post_deleted = count($check_post_deleted) > 0 ? 0 : 1;
                            $is_user_online = ($user_status[0]->current_chat_status == "online") ? 1 : 0;
                            $getCreateTime = TblChat::getTimeZoneUserApp($lastchat['created_at'], request()->time);
                            // dd($getCreateTime);
                            $created_on = \Carbon\Carbon::parse($getCreateTime['converted_datetime'])->format('h:i a'); //date('h:i a', strtotime($getCreateTime['converted_datetime']));
                            // $time = Carbon::createFromFormat('Y-m-d H:i:s', $chat->created_at);
                            // $milsecond = $lastchat['created_at'];
                            $milsecond = $getCreateTime['converted_datetime'];
                            $getLastSeen = TblChat::getTimeZoneUserApp($user_status[0]->last_chat_seen, request()->time);
                            // dd($getLastSeen);
                            $last_seen_date = ($user_status[0]->current_chat_status == "online") ? "" : date('d-m-y h:i A', strtotime($getLastSeen['converted_datetime']));
                            $chatlist_data[] = array(
                                'post_id' => $chat->post_id,
                                'to_id' => $sender,
                                'post_title' => $chat->post_name,
                                'profile_image' => $prof_img,
                                'customer_name' => $sendername,
                                'last_chat_msg' => $last_chat_msg,
                                'last_chat_id' => $last_chat_id,
                                'last_chat_image' => $last_chat_img,
                                'created_at' => $created_on,
                                'msg_tym' => $milsecond,
                                'block_status' => $check_blocked,
                                'unblock_status' => $check_unblocked,
                                'post_deleted' => $is_post_deleted, // 1 - post deleted 0 - not deleted,
                                'user_status' => $is_user_online,
                                'last_seen' => $last_seen_date,
                                'unread_count' => $unread_count
                            );
                        }
                    }
                }
                // dd(array_sum($count));
                $not_read_count = array_sum($count);
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $chatlist_data,
                    'count' => $not_read_count,
                    'total_count' => count($uniqueSenderCount)
                ];
                return response()->json($response);
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User!",
                ];
                return response()->json($response);
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!",
            ];
            return response()->json($response);
        }
    }
    public function update_online_status(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                User::where('id', $user)->update(array('current_chat_status' => "online"));
                $response = $this->sendSuccess("success");
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function update_offline_status(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                User::where('id', $user)->update(array('current_chat_status' => "offline", "last_chat_seen" => date('Y-m-d H:i:s')));
                $response = $this->sendSuccess("success");
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function chat_detail(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            $token = $this->getBearerToken();
            if (!empty($token['code']) && ($token['code'] == 200)) {
                $user = $this->getLoggedUser($token['token']);
                if (!empty($user)) {
                    // dd($user);
                    $lan_code = User::where('id', $user)->value('preferred_language');
                } else {
                    $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => "Invalid User"
                    ];
                }
            } else {
                $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
            }
            Carbon::setLocale($lan_code);
            if (!empty($user)) {
                $to = $request->to;
                $post = $request->post;
                $data = array();
                $chatmsgdate = array();
                $page = $request->has('page') ? $request->get('page') : 1;
                $limit = $request->has('limit') ? $request->get('limit') : 10;
                // notification read status update
                TblNotifications::where('from_id', $to)->where('to_id', $user)->where('post_id', $post)->where('notify_from', 'chat')->update(array('read_status' => 1));
                // notification read status update
                $chatdetail = TblChat::where('post_id', $post)
                    ->where(function ($q) use ($user, $to) {
                        $q->where('tbl_chats.receiver', $user)
                            ->orWhere('tbl_chats.receiver', $to);
                    })
                    ->where(function ($q) {
                        $q->where('msg', '!=', 'Welcome!')
                            ->orWhereNull('msg'); // Include messages that are null
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get()->groupBy(function ($q) {
                        return $q->created_at->format('Y-m-d');
                    });
                // DB::enableQueryLog();
                $chat_detail_image = TblChat::where('post_id', $post)
                    ->where(function ($q) use ($user, $to) {
                        $q->where('tbl_chats.receiver', $user)
                            ->orWhere('tbl_chats.receiver', $to);
                    })
                    ->orderBy('created_at', 'asc')
                    ->get()->groupBy(function ($q) {
                        return $q->created_at->format('Y-m-d');
                    });
                $chatimgdate = array();
                foreach ($chat_detail_image as $date => $detail_image) {
                    $chatimage = array();
                    foreach ($detail_image as $detailImg) {
                        $attach = !empty($detailImg->attachment) ? URL::to('storage/') . '/' . $detailImg->attachment : "";
                        if (!empty($attach)) {
                            $chatimage[] = array(
                                'attachment' => $attach,
                            );
                        }
                    }
                    $chatimgdate[] = ($chatimage);
                }
                // $query = DB::getQueryLog();
                // dd($chatimgdate);
                $chatdetail_count = TblChat::where('post_id', $post)
                    ->where(function ($q) use ($user, $to) {
                        $q->where('tbl_chats.receiver', $user)
                            ->orWhere('tbl_chats.receiver', $to);
                    })
                    ->where(function ($q) {
                        $q->where('msg', '!=', 'Welcome!')
                            ->orWhereNull('msg'); // Include messages that are null
                    })
                    ->count();
                $get_denied_offer = TblChat::where('tbl_chats.post_id', $post)
                    ->where('tbl_chats.denied_offer', "=", 1)
                    ->where(function ($q) use ($user, $to) {
                        $q->where('tbl_chats.receiver', $user)
                            ->orWhere('tbl_chats.receiver', $to);
                    })
                    ->orderBy('tbl_chats.id', 'desc')
                    ->get(['tbl_chats.id'])->first();
                $get_make_offer = TblChat::where('tbl_chats.post_id', $post)
                    ->where('tbl_chats.make_offer', "=", 1)
                    ->where(function ($q) use ($user, $to) {
                        $q->where('tbl_chats.receiver', $user)
                            ->orWhere('tbl_chats.receiver', $to);
                    })
                    ->orderBy('tbl_chats.id', 'desc')
                    ->pluck('tbl_chats.id')->first();
                $get_make_offer_id = !empty($get_make_offer) ? $get_make_offer : "";
                $read_update = TblChat::where('tbl_chats.to_id', $user)
                    ->where('tbl_chats.post_id', '=', $post)->where('read_status', '0')->pluck('id')->toArray();
                if (count($read_update) > 0) {
                    DB::table('tbl_chats')->whereIn('id', $read_update)->update(array('read_status' => 1));
                }
                foreach ($chatdetail as $date => $detail) {
                    $chatmsg = array();
                    foreach ($detail as $detail) {
                        $from_id = $detail->from_id;
                        $sender = ($detail->from_id == $user) ? "Me" : "Others";
                        $attach = !empty($detail->attachment) ? URL::to('storage/') . '/' . $detail->attachment : "";
                        $creat_date = date('Y-m-d', strtotime($detail->created_at)); //\Carbon\Carbon::parse($detail->created_at)->isoFormat('DD MMM YYYY'); //date('Y-m-d', strtotime($detail->created_at));
                        $getTime = TblChat::getTimeZoneUserApp($detail->created_at, request()->time);
                        $create_time = \Carbon\Carbon::parse($getTime['converted_datetime'])->format('H:i');  //date('h:i a', strtotime($getTime['converted_datetime']));
                        $deoffer = 0;
                        if ($detail->denied_offer == 1) {
                            $deoffer = !empty($get_denied_offer) ? 1 : 0;
                        }
                        $chatmsg[] = array(
                            'chat_id' => $detail->id,
                            'from_id' => $detail->from_id,
                            'to_id' => $detail->to_id,
                            'receiver_id' => $detail->receiver,
                            'user_id' => $user,
                            'message' => $detail->msg,
                            'attachment' => $attach,
                            'sender' => $sender,
                            'date' => $creat_date,
                            'time' => $create_time,
                            'make_offer' => $detail->make_offer,
                            'accept_offer' => $detail->accept_offer,
                            'denied_offer' => $deoffer,
                            "location" => $detail->location,
                            "latitude" => $detail->latitude,
                            "longitude" => $detail->longitude,
                            "read_status" => $detail->read_status,
                        );
                    }
                    $token = $this->getBearerToken();
                    if (!empty($token['code']) && ($token['code'] == 200)) {
                        $user = $this->getLoggedUser($token['token']);
                        if (!empty($user)) {
                            // dd($user);
                            $lan_code = User::where('id', $user)->value('preferred_language');
                        } else {
                            $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                            $response = [
                                'success' => false,
                                'code' => 0,
                                'message' => "Invalid User"
                            ];
                        }
                    } else {
                        $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                    }
                    $grouped_date = TblChat::checkChatDate($date, $lan_code);
                    $date_fmt = empty($grouped_date) ? date('d-m-Y', strtotime($date)) : $grouped_date; //empty($grouped_date) ? date('d-m-Y', strtotime($date)) : $grouped_date; //\Carbon\Carbon::parse($date)->isoFormat(' MMM DD, YYYY')
                    $chatmsgdate[] = array('date' => $date_fmt, 'chats' => $chatmsg);
                }
                // dd($chatimgdate);
                // get last unread chats(without chat id records) end
                $sendername = TblChat::getSender($to);
                $sender_websocket_id = User::where('id', $to)->pluck('websocket_id')->first();
                $from_websocket_id = User::where('id', $user)->pluck('websocket_id')->first();
                //$post_price = TblPost::where('id', $post)->first();
                $post_price = TblPost::where('id', $post)->get(['price', 'giving_away'])->first();
                $post_price_amt = !empty($post_price) ? $post_price->price : "";
                $give_away_price = !empty($post_price) ? $post_price->giving_away : 0;
                $currency_symbol = $this->post_currency($post);
                $block_status_id = TblBlockeduser::where('post_id', $post)->where('blocked_id', $user)->OrderBy('id', 'desc')->pluck('block_status')->first();
                $block_status_by = TblBlockeduser::where('post_id', $post)->where('blocked_by', $user)->OrderBy('id', 'desc')->pluck('block_status')->first();
                $block_status = (!empty($block_status_id) || !empty($block_status_by)) ? 1 : 0;
                $unblock_status = !empty($block_status_by) ? 1 : 0;
                $phone = User_profile::where('user_id', $to)->pluck('phone')->first();
                $phone_num = !empty($phone) ? $phone : "";
                if (!isset($from_id)) {
                    $from_id = $user;
                }
                $sender_id = (($user == $from_id) ? $to : $from_id);
                $user_status = User::where('id', $sender_id)->first();
                $user_is_online = ($user_status->current_chat_status == "online") ? 1 : 0;
                $check_post_deleted = TblPost::where('id', $post)->whereNull('deleted_at')->pluck('id');
                $check_post_deleted_count = count($check_post_deleted) > 0 ? 0 : 1;
                $last_seen_chat = !empty($user_status->last_chat_seen) ? date('d-m-y h:i A', strtotime($user_status->last_chat_seen)) : "";
                $last_seen_time = ($user_status->current_chat_status == "online") ? "" : $last_seen_chat;
                $get_accept_offer = TblChat::where('tbl_chats.post_id', $post)
                    ->where('tbl_chats.accept_offer', "=", 1)
                    ->where(function ($q) use ($user, $to) {
                        $q->where('tbl_chats.receiver', $user)
                            ->orWhere('tbl_chats.receiver', $to);
                    })
                    ->orderBy('tbl_chats.id', 'desc')
                    ->get(['tbl_chats.id'])->first();
                $get_accept_offer_id = !empty($get_accept_offer) ? $get_accept_offer : "";
                $user_profile_image = !empty($user_status->profile_photo_path) ? URL::to('/storage/' . $user_status->profile_photo_path) : URL::to('/storage/noimage150.png');
                $check_blocked_status = !empty($block_status) ? $block_status : 0;
                $check_unblocked_status = !empty($unblock_status) ? $unblock_status : 0;
                $data = array(
                    'customer_name' => $sendername,
                    'to_websocket_id' => $sender_websocket_id,
                    'from_websocket_id' => $from_websocket_id,
                    'user_status' => $user_is_online,
                    'last_seen' => $last_seen_time,
                    'profile_image' => $user_profile_image,
                    'block_status' => $check_blocked_status,
                    'unblock_status' => $check_unblocked_status,
                    'messages' => $chatmsgdate,
                    'phone_number' => $phone_num,
                    'post_price' => number_format($post_price_amt, 2),
                    'currency_symbol' => $currency_symbol,
                    'edit_offer_id' => $get_make_offer_id,
                    'accepted_offer_id' => $get_accept_offer_id,
                    'post_deleted' => $check_post_deleted_count, // 1 - post deleted 0 - not deleted
                    'post_id' => $post,
                    "giving_away" => $give_away_price,
                    "lastmsgdata" => [],
                    "last_read_msg" => [],
                    "chat_count" => $chatdetail_count,
                    "chatImage" => $chatimgdate
                );
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
                return response()->json($response);
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User!",
                ];
                return response()->json($response);
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!",
            ];
            return response()->json($response);
        }
    }
    public function chat_detail_refresh(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $to = $request->to;
                $post = $request->post;
                $data = array();
                // $page = $request->has('page') ? $request->get('page') : 1;
                // $limit = $request->has('limit') ? $request->get('limit') : 10;
                // notification read status update
                TblNotifications::where('from_id', $to)->where('to_id', $user)->where('post_id', $post)->where('notify_from', 'chat')->update(array('read_status' => 1));
                // notification read status update
                //take out first msg id
                $get_first_msg = TblChat::where('tbl_chats.post_id', $post)
                    ->where(function ($q) use ($user, $to) {
                        $q->where('tbl_chats.receiver', $user)
                            ->orWhere('tbl_chats.receiver', $to);
                    })
                    ->orderBy('tbl_chats.id', 'asc')
                    ->get()->first();
                $chatdetail_count = TblChat::where('post_id', $post)
                    ->where(function ($q) use ($user, $to) {
                        $q->where('receiver', $user)
                            ->orWhere('receiver', $to);
                    })->count();
                $first_msg_id = $get_first_msg->id;
                $first_msg_read_state = $get_first_msg->read_status;
                $get_denied_offer = TblChat::where('tbl_chats.post_id', $post)
                    ->where('tbl_chats.denied_offer', "=", 1)
                    ->where(function ($q) use ($user, $to) {
                        $q->where('tbl_chats.receiver', $user)
                            ->orWhere('tbl_chats.receiver', $to);
                    })
                    ->orderBy('tbl_chats.id', 'desc')
                    ->get(['tbl_chats.id'])->first();
                $get_make_offer = TblChat::where('tbl_chats.post_id', $post)
                    ->where('tbl_chats.make_offer', "=", 1)
                    ->where(function ($q) use ($user, $to) {
                        $q->where('tbl_chats.receiver', $user)
                            ->orWhere('tbl_chats.receiver', $to);
                    })
                    ->orderBy('tbl_chats.id', 'desc')
                    ->pluck('tbl_chats.id')->first();
                $get_make_offer_id = !empty($get_make_offer) ? $get_make_offer : "";
                //MM
                $read_update = TblChat::where('tbl_chats.to_id', $user)
                    ->where('tbl_chats.post_id', '=', $post)->where('read_status', '0')->pluck('id')->toArray();
                if (count($read_update) > 0) {
                    DB::table('tbl_chats')->whereIn('id', $read_update)->update(array('read_status' => 1));
                }
                // get last read chats (chat id records)
                $userid = $user;
                $last_rec_id = $request->last_chat_id;
                if (!empty($last_rec_id)) {
                    if (!empty($post)) {
                        $check_blocked = TblBlockeduser::where('post_id', $post)->where('blocked_id', $to)->where('blocked_by', $userid)->first();
                        if (empty($check_blocked)) {
                            $last_readchat = TblChat::where('id', '>=', $last_rec_id)
                                ->where('post_id', $post)
                                ->where('read_status', 1)
                                ->where(function ($q) use ($userid, $to) {
                                    $q->where('tbl_chats.receiver', $userid)
                                        ->orWhere('tbl_chats.receiver', $to);
                                })
                                ->orderBy('created_at', 'desc')
                                ->take(10)
                                ->get()->groupBy(function ($q) {
                                    return $q->created_at->format('Y-m-d');
                                });
                        } else if ($check_blocked->block_status == 0) {
                            $last_readchat = TblChat::where('id', '>=', $last_rec_id)
                                ->where('post_id', $post)
                                ->where('read_status', 1)
                                ->where(function ($q) use ($userid, $to) {
                                    $q->where('tbl_chats.receiver', $userid)
                                        ->orWhere('tbl_chats.receiver', $to);
                                })
                                ->take(10)
                                ->get()->groupBy(function ($q) {
                                    return $q->created_at->format('Y-m-d');
                                });
                        } else {
                            $last_readchat = TblChat::where('id', '>=', $last_rec_id)
                                ->where('post_id', $post)
                                ->where('read_status', 1)
                                ->where('tbl_chats.created_at', "<=", date('Y-m-d H:i:s', strtotime($check_blocked->created_at)))
                                ->where(function ($q) use ($userid, $to) {
                                    $q->where('tbl_chats.receiver', $userid)
                                        ->orWhere('tbl_chats.receiver', $to);
                                })
                                ->take(10)
                                ->take(10)
                                ->get()->groupBy(function ($q) {
                                    return $q->created_at->format('Y-m-d');
                                });
                        }
                    } else {
                        $last_readchat = "";
                    }
                } else {
                    $last_readchat = "";
                }
                $lastreadmsg = array();
                $latest_msg_ar = array();
                if (!empty($last_readchat)) {
                    foreach ($last_readchat as $date => $detail) {
                        $chatread_msges = array();
                        foreach ($detail as $detail) {
                            $from_id = $detail->from_id;
                            $sender = ($detail->from_id == $user) ? "Me" : "Others";
                            $attach = !empty($detail->attachment) ? URL::to('storage/') . '/' . $detail->attachment : "";
                            $creat_date = date('Y-m-d', strtotime($detail->created_at));
                            // $create_time = date('H:i a', strtotime($detail->created_at));
                            //timezone
                            $getTime = TblChat::getTimeZoneUserApp($detail->created_at, request()->time);
                            $create_time = \Carbon\Carbon::parse($getTime['converted_datetime'])->format('H:i');
                            if ($detail->denied_offer == 1) {
                                $deoffer = !empty($get_denied_offer) ? 1 : 0;
                            } else {
                                $deoffer = 0;
                            }
                            $chatread_msges[] = array(
                                'chat_id' => $detail->id,
                                'from_id' => $detail->from_id,
                                'to_id' => $detail->to_id,
                                'receiver_id' => $detail->receiver,
                                'user_id' => $user,
                                'message' => $detail->msg,
                                'attachment' => $attach,
                                'sender' => $sender,
                                'date' => $creat_date,
                                'time' => $create_time,
                                'make_offer' => $detail->make_offer,
                                'accept_offer' => $detail->accept_offer,
                                'denied_offer' => $deoffer,
                                "location" => $detail->location,
                                "latitude" => $detail->latitude,
                                "longitude" => $detail->longitude,
                                "read_status" => $detail->read_status,
                            );
                        }
                        $token = $this->getBearerToken();
                        if (!empty($token['code']) && ($token['code'] == 200)) {
                            $user = $this->getLoggedUser($token['token']);
                            if (!empty($user)) {
                                // dd($user);
                                $lan_code = User::where('id', $user)->value('preferred_language');
                            } else {
                                $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                                $response = [
                                    'success' => false,
                                    'code' => 0,
                                    'message' => "Invalid User"
                                ];
                            }
                        } else {
                            $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                        }
                        $grouped_date = TblChat::checkChatDate($date, $lan_code);
                        $date_fmt = empty($grouped_date) ? date('d-m-Y', strtotime($date)) : $grouped_date;
                        $lastreadmsg[] = array(
                            'date' => $date_fmt,
                            'chats' => $chatread_msges
                        );
                        //skip first array data from multiple data                        
                        if (count($chatread_msges) == 0 || count($chatread_msges) == 1) {
                            $remove_first_one = []; //array_shift($chatread_msges);
                        } else {
                            $remove_first_one = array_splice($chatread_msges, 1, count($chatread_msges)); //array_shift($chatread_msges);
                        }
                        $latest_msg_ar[] = array(
                            'date' => $date_fmt,
                            'chats' => $remove_first_one
                        );
                    }
                }
                // get last unread chats(without chat id records) end
                $sender_websocket_id = User::where('id', $to)->pluck('websocket_id')->first();
                $from_websocket_id = User::where('id', $user)->pluck('websocket_id')->first();
                $block_status_id = TblBlockeduser::where('post_id', $post)->where('blocked_id', $user)->OrderBy('id', 'desc')->pluck('block_status')->first();
                $block_status_by = TblBlockeduser::where('post_id', $post)->where('blocked_by', $user)->OrderBy('id', 'desc')->pluck('block_status')->first();
                $block_status = (!empty($block_status_id) || !empty($block_status_by)) ? 1 : 0;
                $unblock_status = !empty($block_status_by) ? 1 : 0;
                //$sender_id = (($user == $from_id) ? $to : $from_id);
                $sender_id = $to;
                $user_status = User::where('id', $sender_id)->first();
                $user_is_online = ($user_status->current_chat_status == "online") ? 1 : 0;
                $check_post_deleted = TblPost::where('id', $post)->whereNull('deleted_at')->pluck('id');
                $check_post_deleted_count = count($check_post_deleted) > 0 ? 0 : 1;
                $last_seen_chat = !empty($user_status->last_chat_seen) ? date('d-m-y h:i A', strtotime($user_status->last_chat_seen)) : "";
                $last_seen_time = ($user_status->current_chat_status == "online") ? "" : $last_seen_chat;
                $get_accept_offer = TblChat::where('tbl_chats.post_id', $post)
                    ->where('tbl_chats.accept_offer', "=", 1)
                    ->where(function ($q) use ($user, $to) {
                        $q->where('tbl_chats.receiver', $user)
                            ->orWhere('tbl_chats.receiver', $to);
                    })
                    ->orderBy('tbl_chats.id', 'desc')
                    ->get(['tbl_chats.id'])->first();
                $get_accept_offer_id = !empty($get_accept_offer) ? $get_accept_offer : "";
                $check_blocked_status = !empty($block_status) ? $block_status : 0;
                $check_unblocked_status = !empty($unblock_status) ? $unblock_status : 0;
                $data = array(
                    'first_msg_id' => $first_msg_id,
                    'first_msg_read_state' => $first_msg_read_state,
                    'customer_name' => "",
                    'to_websocket_id' => $sender_websocket_id,
                    'from_websocket_id' => $from_websocket_id,
                    'user_status' => $user_is_online,
                    'last_seen' => $last_seen_time,
                    'profile_image' => "",
                    'block_status' => $check_blocked_status,
                    'unblock_status' => $check_unblocked_status,
                    'messages' => array(),
                    'phone_number' => "",
                    'post_price' => "",
                    'currency_symbol' => "",
                    'edit_offer_id' => $get_make_offer_id,
                    'accepted_offer_id' => $get_accept_offer_id,
                    'post_deleted' => $check_post_deleted_count, // 1 - post deleted 0 - not deleted
                    'post_id' => "",
                    "giving_away" => "",
                    "lastmsgdata" => $latest_msg_ar,
                    "last_read_msg" => $lastreadmsg, //remove it later
                    "chat_count" => $chatdetail_count
                );
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
                return response()->json($response);
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User!",
                ];
                return response()->json($response);
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!",
            ];
            return response()->json($response);
        }
    }
    public function delete_chat(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                TblChat::where('post_id', $request->post_id)
                    ->where('from_id', $user)
                    ->where('to_id', $request->to)
                    ->orWhere('from_id', $request->to)
                    ->Where('to_id', $user)->delete();
                // notification read status update
                // TblNotifications::where('from_id', $request->to)->where('to_id', $user)->orWhere('from_id', $user)->orWhere('to_id', $request->to)->where('post_id', $request->post_id)->where('notify_from', 'chat')->update(array('read_status' => 1));
                TblNotifications::where('from_id', $request->to)->where('to_id', $user)->orWhere('from_id', $user)->orWhere('to_id', $request->to)->where('post_id', $request->post_id)->where('notify_from', 'chat')->delete();
                // notification read status update  
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => "Deleted successfully!"
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invaid Token"
            ];
        }
        return response()->json($response);
    }
    public function get_default_currency()
    {
        /* show the curreny symbol */
        $default_crr = Setting::get_admin_default_currency();
        $curr_id = $default_crr['id'];
        $curr_short_code = $default_crr['short_code'];
        $curr_hex_code = $default_crr['currency_hex'];
        //,$default_crr['short_code'],$default_crr['id']
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $curr_hex_code,
            'currency_hex' => $curr_hex_code,
            'currency_short_code' => $curr_short_code,
            'currency_id' => $curr_id
        ];
        return response()->json($response);
    }
    public function get_default_currency_old()
    {
        /* show the curreny symbol */
        $settings = Setting::get_logos();
        $currency_symbol = TblPost::get_post_currency($settings['default_currency']);
        $response = [
            'success' => false,
            'code' => 200,
            'data' => $currency_symbol[0],
        ];
        return response()->json($response);
    }
    public function check_review(Request $request)
    {
        $post_id = $request->get('post_id');
        $token = $request->get('token');
        $user = $this->getLoggedUser($token);
        $checkExist = TblReview::where('post_id', $post_id)->where('user_id', $user)->get();
        $post = TblPost::where('id', $post_id)->first();
        if ($checkExist->count() == 0) {
            if ($post['user_id'] == $user) {
                $response = [
                    'success' => false,
                    'code' => 0
                ];
            } else {
                $response = [
                    'success' => true,
                    'code' => 200
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0
            ];
        }
        return response()->json($response);
    }
    public function get_nearby_post_locations(Request $request)
    {
        $cities_lat_lng = TblCity::where('latitude', 'like', '%' . bcdiv($request->latitude, 1, 3) . '%')->where('logitude', 'like', '%' . bcdiv($request->longitude, 1, 3) . '%')->first();
        $states = TblState::where('code', $request->state_short)->first();
        $countries = TblCountry::where('code', $request->country_short)->first();
        $addressids = array();
        if ($cities_lat_lng != "") {
            $addressid_dist = array();
            $distance = 20;
            $addressid_dist = $this->get_city_ids($request->latitude, $request->longitude, $distance);
            $addressids_cit[] = $cities_lat_lng->id;
            $addressids = array_merge($addressid_dist, $addressids_cit);
        } else if ($states != "") {
            $s_cities = TblCity::where('state_id', $states->id)->get();
            foreach ($s_cities as $s_city) {
                $addressids[] = $s_city->id;
            }
        } else if (!empty($countries)) {
            $c_cities = TblCity::where('country_id', $countries->id)->get();
            foreach ($c_cities as $c_city) {
                $addressids[] = $c_city->id;
            }
        }
        $data = array();
        if (!empty($addressids)) {
            foreach ($addressids as $addressid) {
                $checkpost = TblPost::where('city', $addressid)->first();
                if (!empty($checkpost)) {
                    $city = TblCity::where('id', $addressid)->first();
                    $state = TblState::where('id', $city->state_id)->first();
                    $country = TblCountry::where('id', $city->country_id)->first();
                    $c_locality = !empty($city->locality) ? $city->locality . ',' : "";
                    $data[] = array(
                        'latitude' => $city->latitude,
                        'longitude' => $city->logitude,
                        'name' => $c_locality . $city->name . ',' . $state->name . ',' . $country->name
                    );
                }
            }
            $response = [
                'success' => true,
                'code' => 200,
                'data' => $data
            ];
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "No data found!"
            ];
        }
        return response()->json($response);
    }
    public function choose_single_package(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $data = $data['package_info'] = $data['payment_method'] = array();
                //check posted id
                $getpostid = $request->post_id;
                $post_info = TblPost::where('id', $getpostid)->first();
                if ($post_info->user_id == $user) {
                    $check_post_package = TblPost::check_post_expired($getpostid);
                    $check_is_paid = TblPayment::where('post_id', $getpostid)->where('active', '1')->count();
                    if ((($check_post_package['expired'] != "Expired") || $check_post_package['expired'] == "") && ($check_is_paid > 0)) {
                        $response = $this->sendError("Already paid!");
                    } else {
                        $post_imgs = TblPost::get_single_post_information($getpostid);
                        if (empty($post_info)) {
                            $response = $this->sendError("Invalid post");
                        } else {
                            $payment_methods = TblPaymentsMethod::where('active', '1')->get();
                            foreach ($payment_methods as $payment_method) {
                                $data['payment_method'][] = array(
                                    'payment_method' => strtolower($payment_method->display_name)
                                );
                            }
                            $currency_symbol = Setting::get_admin_default_currency();
                            $list_of_packs = Package::where('active', '1')->where('lft', '!=', '1')
                                ->where('bulk_ads', '0')
                                ->orderBy('lft', 'asc')
                                ->get();
                            foreach ($list_of_packs as $list_of_pack) {
                                $data['package_info'][] = array(
                                    'package_id' => $list_of_pack->id,
                                    'amount' => $list_of_pack->price,
                                    'name' => $list_of_pack->name,
                                    'duration' => $list_of_pack->duration,
                                    "currency_symbol" => !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""
                                );
                            }
                            $data['post_title'] = $post_info->title;
                            $data['post_id'] = $post_info->id;
                            $data['post_image'] = $post_imgs['images'];
                            $response = [
                                'success' => true,
                                'code' => 200,
                                'data' => $data
                            ];
                        }
                    }
                } else {
                    $response = $this->sendError("Invalid post");
                }
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function is_valid_coupon(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $curr_date = date('Y-m-d');
                $coup_code = $request->code;
                $coupon_det = TblCoupon::where('coupon_code', $coup_code)
                    ->where('start_date', '<=', $curr_date)
                    ->where('end_date', '>=', $curr_date)
                    ->get()->toArray();
                $package = Package::where('id', $request->package_id)->get();
                if (count($coupon_det) > 0) {
                    $user_id = $user;
                    $limit_type = $coupon_det[0]['limit_type'];
                    $limit_value = $coupon_det[0]['limit_value'];
                    $coupon_id = $coupon_det[0]['id'];
                    $coupon_type = $coupon_det[0]['type'];
                    $coupon_value = $coupon_det[0]['value'];
                    $copon_tax = $coupon_det[0]['tax'];
                    if ($coupon_type == "percentage") {
                        $coupon_label = $coupon_value . " %";
                        $tax_label = $copon_tax . " %";
                    } else {
                        $coupon_label = "";
                        $tax_label = "";
                    }
                    $currency_symbol = Setting::get_admin_default_currency();
                    $array = [
                        'id' => $coupon_id,
                        'coupon_type' => $coupon_type,
                        'coupon_label' => $coupon_label,
                        'coupon_amount' => number_format((float) $coupon_value, 2, '.', ''),
                        'tax_label' => $tax_label,
                        'tax_amount' => number_format((float) $copon_tax, 2, '.', ''),
                        'currency_symbol' => !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""
                    ];
                    //individual person coupon usage check
                    if ($limit_type == "individual") {
                        $coupon_used_count = TblPayment::where('user_id', $user_id)->where('active', '1')->where('coupon_id', $coupon_id)->get()->count();
                        if ($limit_value == $coupon_used_count) {
                            $response = [
                                'success' => false,
                                'code' => 0,
                                'message' => "Limit reached. Already Coupen used " . $limit_value . " time."
                            ];
                        } else {
                            $response = [
                                'success' => true,
                                'code' => 200,
                                'data' => $array
                            ];
                        }
                    }
                    //overall person coupon usage check
                    if ($limit_type == "all") {
                        $coupon_used_count = TblPayment::where('coupon_id', $coupon_id)->where('active', '1')->get()->count();
                        if ($limit_value == $coupon_used_count) {
                            $response = [
                                'success' => false,
                                'code' => 0,
                                'message' => "Coupen Expired"
                            ];
                        } else {
                            $response = [
                                'success' => true,
                                'code' => 200,
                                'data' => $array
                            ];
                        }
                    }
                } else {
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => "Invalid Coupen Given"
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    public function choose_multiple_package()
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $data = array();
                $data['list_top_ads'] = array();
                $data['list_of_fea_ads'] = array();
                $data["currency_symbol"] = array();
                $payment_methods = TblPaymentsMethod::where('active', '1')->get();
                $data['payment_method'] = array();
                foreach ($payment_methods as $payment_method) {
                    $data['payment_method'][] = array(
                        'payment_method' => strtolower($payment_method->display_name)
                    );
                }
                $currency_symbol = Setting::get_admin_default_currency();
                $data["currency_symbol"] = !empty($currency_symbol) ? $currency_symbol['currency_hex'] : "";
                $list_of_packs = Package::where('active', '1')->where('lft', '!=', '1')
                    ->where('bulk_ads', '1')
                    ->where('ad_type', 'top_ad')
                    ->orderBy('lft', 'asc')
                    ->get();
                foreach ($list_of_packs as $list_of_pack) {
                    $data['list_top_ads'][] = array(
                        'package_id' => $list_of_pack->id,
                        'amount' => $list_of_pack->price,
                        'name' => $list_of_pack->name,
                        'duration' => $list_of_pack->duration,
                        "currency_symbol" => !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""
                    );
                }
                $list_of_fea_ads = Package::where('active', '1')->where('lft', '!=', '1')
                    ->where('bulk_ads', '1')
                    ->where('ad_type', 'feature_ad')
                    ->orderBy('lft', 'asc')
                    ->get();
                foreach ($list_of_fea_ads as $list_of_fea_ad) {
                    $data['list_of_fea_ads'][] = array(
                        'package_id' => $list_of_fea_ad->id,
                        'amount' => $list_of_fea_ad->price,
                        'name' => $list_of_fea_ad->name,
                        'duration' => $list_of_fea_ad->duration,
                        "currency_symbol" => !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function pay_for_bulk_package(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $currency_symbol = Setting::get_admin_default_currency();
                if (!empty($currency_symbol)) {
                    if ($request->payment_type == "paypal") {
                        $url = URL::to('/paypal-payment-bulk-package?pack_amt=' . $request->package_amount . '&cid=' . $currency_symbol->id . '&package_id=' . $request->package_id . '&payment_type=' . $request->payment_type . '&uid=' . $user);
                    } else if ($request->payment_type == "stripe") {
                        $url = URL::to('/stripe-payment?pack_amt=' . $request->package_amount . '&cid=' . $currency_symbol->id . '&package_id=' . $request->package_id . '&payment_type=' . $request->payment_type . '&uid=' . $user . '&paid_for=package');
                    }
                    $response = [
                        'success' => true,
                        'code' => 200,
                        'data' => $url
                    ];
                } else {
                    $response = $this->sendError("Please try again later!");
                }
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function pay_for_single_package(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $pack_info = Package::where('id', $request->package_id)->first();
                $coupon = (!empty($request->coupon_id) ? $request->coupon_id : '');
                $currency_symbol = Setting::get_admin_default_currency();
                if (!empty($currency_symbol)) {
                    $cid = $currency_symbol->id;
                    if ($request->payment_type == "paypal") {
                        $url = URL::to('/paypal-payment-process?pack_amt=' . $request->package_amount . '&cid=' . $cid . '&post_id=' . $request->post_id . '&live_days=' . $pack_info->duration . '&package_id=' . $request->package_id . '&payment_type=' . $request->payment_type . '&coupon_id=' . $coupon . '&uid=' . $user);
                    } else if ($request->payment_type == "stripe") {
                        $url = URL::to('/stripe-payment?pack_amt=' . $request->package_amount . '&cid=' . $cid . '&post_id=' . $request->post_id . '&live_days=' . $pack_info->duration . '&package_id=' . $request->package_id . '&payment_type=' . $request->payment_type . '&coupon_id=' . $coupon . '&uid=' . $user . '&paid_for=package');
                    }
                    $response = [
                        'success' => true,
                        'code' => 200,
                        'data' => $url
                    ];
                } else {
                    $response = $this->sendError("Please try again later!");
                }
            } else {
                $response = $this->sendError("Invalid user!");
            }
        } else {
            $response = $this->sendError("Invalid token!");
        }
        return response()->json($response);
    }
    /* list of my bulk packages */
    public function my_bulk_pakages(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $data = array();
                $datas = TblBulkPackPayment::join('packages', 'packages.id', '=', 'tbl_bulk_pack_payments.package_id')
                    ->where('tbl_bulk_pack_payments.user_id', $user)
                    ->where('tbl_bulk_pack_payments.active', '1')
                    ->orderBy('tbl_bulk_pack_payments.created_at', 'desc')
                    ->get(['tbl_bulk_pack_payments.s_payment_id', 'tbl_bulk_pack_payments.package_amount', 'tbl_bulk_pack_payments.created_at', 'tbl_bulk_pack_payments.start_date', 'tbl_bulk_pack_payments.end_date', 'tbl_bulk_pack_payments.id', 'packages.name as pack_name', 'packages.bulk_limit']);
                foreach ($datas as $bulk) {
                    $remaining_cnt = $this->get_remaining_ads($bulk->id, $user);
                    $p_end_date = date('Y-m-d', strtotime($bulk->end_date));
                    $used_cnt = ($bulk->bulk_limit - $remaining_cnt);
                    if (($remaining_cnt != 0) && ($p_end_date >= date('Y-m-d'))) {
                        $view_ads = 1; // assign ads
                    } else if ($bulk->bulk_limit - $remaining_cnt != 0) {
                        $view_ads = 2; // view assigned ads
                    } else if ($p_end_date <= date('Y-m-d')) {
                        $view_ads = 0; // package expired
                    }
                    $data[] = array(
                        'id' => $bulk->id,
                        'package_name' => $bulk->pack_name,
                        'start_date' => date('d-m-Y', strtotime($bulk->start_date)),
                        'end_date' => date('d-m-Y', strtotime($bulk->end_date)),
                        'created_at' => date('d-m-Y', strtotime($bulk->created_at)),
                        'total_count' => $bulk->bulk_limit,
                        'used_count' => $used_cnt,
                        'remaining_count' => $remaining_cnt,
                        'assign_ads' => $view_ads,
                        'package_amount' => $bulk->package_amount
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User!"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!"
            ];
        }
        return response()->json($response);
    }
    public function mybulk_package_addpost(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $data = array();
                $data['post'] = array();
                $bulkids = TblPayment::where('user_id', $user)
                    ->where('active', '1')
                    ->whereDate('end_date', '>=', date('Y-m-d'))
                    ->pluck('post_id')->toArray();
                $list = TblPost::where('user_id', $user)
                    ->whereNull('deleted_at')
                    ->where('active', '1')
                    ->where('tbl_posts.sold_status', 0)
                    ->whereNotIn('id', $bulkids)
                    ->orderBy('title', 'asc')
                    ->get();
                $remining_ads = $this->get_remaining_ads($request->pack_id, $user);
                foreach ($list as $bulk) {
                    $data['post'][] = array(
                        'post_id' => $bulk->id,
                        'title' => $bulk->title
                    );
                }
                $data['remaining_count'] = $remining_ads;
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User!"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!"
            ];
        }
        return response()->json($response);
    }
    public function get_remaining_ads($id, $user_id)
    {
        /* get data from bulkpayment */
        $get_pack_id = TblBulkPackPayment::where('id', $id)->pluck('package_id')->first();
        /* get package info bulkpayment */
        $get_bulklimit = Package::where('id', $get_pack_id)->pluck('bulk_limit')->first();
        /* get already added payment post count for bulck */
        $ads_count = TblPayment::where('user_id', $user_id)
            ->where('package_id', $get_pack_id)
            ->where('active', '1')
            ->where('is_bulk', $id)
            ->count('post_id');
        $remaing_ads = $get_bulklimit - $ads_count;
        return $remaing_ads;
    }
    public function get_assigned_ads($id, $user_id)
    {
        /* get data from bulkpayment */
        $get_pack_id = TblBulkPackPayment::where('id', $id)->pluck('package_id')->first();
        /* get already added payment post ids for bulck */
        $ads_ids = TblPayment::where('user_id', $user_id)
            ->where('package_id', $get_pack_id)
            ->where('active', '1')
            ->where('is_bulk', $id)
            ->pluck('post_id')
            ->toArray();
        /*get bulck payment post list*/
        $ads_list = TblPost::whereIn('id', $ads_ids)->get()->toArray();
        return $ads_list;
    }
    public function mybulk_package_savepost(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $user_id = $user;
                $post_ids = explode(',', $request->post_id);
                $remaining_cnt = $this->get_remaining_ads($request->package_id, $user);
                /* get package id */
                $get_package_id = TblBulkPackPayment::where('id', $request->package_id)->pluck('package_id')->first();
                /* get package validity type */
                $get_package_type = Package::where('id', $get_package_id)->pluck('bulk_type')->first();
                // if ((count($post_ids) == $remaining_cnt) || (count($post_ids) <= $remaining_cnt)) {
                foreach ($post_ids as $value) {
                    $package = TblBulkPackPayment::where('id', $request->package_id)
                        ->first();
                    $check_post = TblPayment::where('post_id', $value)
                        ->where('active', '1')
                        ->whereDate('end_date', '>=', date("Y-m-d"))->first();
                    if (empty($check_post)) {
                        $curr_date = date('Y-m-d H:i:s');
                        $free_package = Package::where('name', "Free")->where('lft', 1)->pluck('duration')->first();
                        $end_date = date('Y-m-d H:i:s', strtotime($curr_date . "+" . $free_package . " days"));
                        TblPayment::create([
                            's_payment_id' => $package->s_payment_id,
                            'package_id' => $package->package_id,
                            'payment_type' => $package->payment_type,
                            'payment_loc_ref_id' => $package->payment_loc_ref_id,
                            'user_id' => $user_id,
                            'post_id' => $value,
                            'start_date' => ($get_package_type == 1) ? $package->start_date : $curr_date,
                            'end_date' => ($get_package_type == 1) ? $package->end_date : $end_date,
                            'live_days' => $package->live_days,
                            'package_amount' => $package->package_amount,
                            'payment_status' => $package->payment_status,
                            'coupon_id' => $package->coupon_id,
                            'is_bulk' => $request->package_id,
                        ]);
                        $release_from_type_free = TblPostedAdPackageInfo::where('post_id', $value);
                        $release_from_type_free->update([
                            "active" => "0"
                        ]);
                    }
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => "Post added to the package!"
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    public function mybulk_ads_view_assignedads(Request $request, $id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $assigned_ads = $this->get_assigned_ads($id, $user);
                $data = array();
                foreach ($assigned_ads as $assigned_ad) {
                    $data[] = array(
                        'id' => $assigned_ad['id'],
                        'title' => $assigned_ad['title']
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    public function block_user(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $check_block = TblBlockeduser::where('post_id', $request->post_id)
                    ->where('blocked_id', $request->to)
                    ->where('blocked_by', $user)
                    ->first();
                if (!empty($check_block)) {
                    if ($check_block->block_status == 1) {
                        $block = 0;
                        $message = 'Unblocked successfully';
                    } else {
                        $block = 1;
                        $message = 'Blocked successfully';
                    }
                    TblBlockeduser::where('id', $check_block->id)->update(array('block_status' => $block));
                } else {
                    $block = 1;
                    $message = 'Blocked successfully';
                    TblBlockeduser::create([
                        'post_id' => $request->post_id,
                        'blocked_by' => $user,
                        'blocked_id' => $request->to,
                        'block_status' => $block,
                    ]);
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => $message
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invaid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invaid Token"
            ];
        }
        return response()->json($response);
    }
    public function filterbranddata($post_id, $cat_id)
    {
        $payment_ads_ids = array();
        $free_ads_ids = array();
        /* get unexpired payment post */
        $payment_ids_array = TblPost::get_unexpired_payment_post_ids();
        /* get unexpired free post */
        $free_ids_array = TblPost::get_unexpired_free_post_ids();
        foreach ($payment_ids_array as $payment_ids_array) {
            // dd($payment_ids_array);
            if (in_array($payment_ids_array, $post_id)) {
                $payment_ads_ids[] = $payment_ids_array;
            }
        }
        // dd($payment_ads_ids);
        foreach ($free_ids_array as $free_ids_array) {
            if (in_array($free_ids_array, $post_id)) {
                ;
                $free_ads_ids[] = $free_ids_array;
            }
        }
        $pay_id = $payment_ads_ids;
        $free_id = $free_ads_ids;
        $all_post = array_merge($pay_id, $free_id);
        $unique_all_post = array_unique($all_post, SORT_REGULAR);
        $final_result_id = $all_post;
        // $brand_field_id = TblFieldsDetail::where('form_field_name', '=', 'brandwithmodel')->where('cat_id', '=', $cat_id)->value('id');
        $brand_field_id = TblFieldsDetail::where('cat_id', '=', $cat_id)->pluck('id')->toArray();
        $get_field_id = TblPostValue::whereIn('post_id', $final_result_id)->where('field_id', $brand_field_id)->pluck('field_id')->toArray();
        $get_brand_filter_value = TblPostValue::whereIn('post_id', $final_result_id)->whereIn('field_id', $brand_field_id)
            ->pluck('value')->toArray();
        // dd($get_field_id);
        $desired_format = [];
        // Loop through the array to dynamically split values containing commas
        foreach ($get_brand_filter_value as $value) {
            if (strpos($value, ',') !== false) {
                $split_values = explode(',', $value);
                $desired_format = array_merge($desired_format, $split_values);
            } else {
                $desired_format[] = $value;
            }
        }
        $post_count_value = array_count_values($desired_format);
        $filter_data = array(
            'get_value' => $desired_format,
            'get_id' => $get_field_id,
            'post_count' => $post_count_value
        );
        return $filter_data;
    }
    public function filterdata($post_id)
    {
        $get_field_id = TblPostValue::whereIn('post_id', $post_id)->pluck('field_id')->toArray();
        $get_filter_value = TblPostValue::whereIn('post_id', $post_id)->pluck('value')->toArray();
        // Assuming $get_filter_value contains the array returned by the pluck method
        // Create a new array to hold the converted values dynamically
        $desired_format = [];
        // Loop through the array to dynamically split values containing commas
        foreach ($get_filter_value as $value) {
            if (strpos($value, ',') !== false) {
                $split_values = explode(',', $value);
                $desired_format = array_merge($desired_format, $split_values);
            } else {
                $desired_format[] = $value;
            }
        }
        // Now $desired_format will have the array in the desired format, with comma-separated values split into separate element
        $post_count_value = array_count_values($desired_format);
        $filter_result = [];
        foreach ($get_field_id as $index => $fieldId) {
            if (!isset($filter_result[$fieldId])) {
                $filter_result[$fieldId] = [];
            }
            $filter_result[$fieldId][] = $get_filter_value[$index];
            $filter_result[$fieldId] = array_unique($filter_result[$fieldId]);
        }
        $filter_data = array('get_value' => $desired_format, 'get_id' => $get_field_id, 'post_count' => $post_count_value);
        //     array_push($this->fildata, $filter_data);
        //     dd($this->fildata);
        return $filter_data;
        // return [];
    }
    /* get custome fields based on category */
    public function get_custome_fields(Request $request)
    {
        $id = $request->category_id;
        // search post id start
        $data = array();
        $addressids = array();
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 10;
        $cat_id = $request->category_id;
        $lat = $request->lat;
        $lng = $request->lon;
        $distance = $request->distance;
        $search = $request->s;
        $city = $request->city;
        $state = $request->state;
        $country = $request->country;
        $price_sort_by = $request->sort_by;
        $posted_within = $request->posted_within;
        /* no need to show the blocked user posts */
        $blockedUsers = User::blocked_users();
        $postDetail = TblPost::whereNotIn('tbl_posts.user_id', $blockedUsers)->where('tbl_posts.sold_status', 0);
        /* Text box search */
        if ($search != "") {
            $postDetail->where('tbl_posts.title', 'like', '%' . $search . '%');
        }
        /* Category id filter */
        if ($cat_id != "") {
            $cat = TblCategory::where('id', $cat_id)->where('enable_disable', 1)->first();
            $subcategory = TblCategory::where('enable_disable', 1)->descendantsAndSelf($cat->id);
            foreach ($subcategory as $subcat) {
                $sids[] = $subcat->id;
            }
            $postDetail->whereIn('tbl_posts.category_id', $sids);
        }
        if (!empty($lat)) {
            $cities_lat_lng = TblCity::where('latitude', 'like', '%' . bcdiv($lat, 1, 3) . '%')->where('logitude', 'like', '%' . bcdiv($lng, 1, 3) . '%')->pluck('id')->first();
            $current_address_ids = array();
            $addressidss = array();
            if (!empty($cities_lat_lng)) {
                $current_address_ids[] = $cities_lat_lng;
            }
            if (!empty($distance)) {
                $addressidss = $this->get_city_ids($lat, $lng, $distance);
            } else {
                $addressidss = $this->get_city_ids($lat, $lng, 20);
            }
            $addressids = array_merge($current_address_ids, $addressidss);
        }
        if (!empty($addressids)) {
            $postDetail->whereIn('tbl_posts.city', $addressids);
        }
        $get_all_posts = $postDetail->pluck('id')->toArray();
        $branddata = $this->filterbranddata($get_all_posts, $id);
        if (!empty($posted_within)) {
            if ($posted_within == "today") {
                $postDetail->whereDate('tbl_posts.created_at', Carbon::today());
            } else if ($posted_within == "weekly") {
                $previous_week = strtotime("-1 week +1 day");
                $start_week = strtotime("last sunday midnight", $previous_week);
                $end_week = strtotime("next saturday", $start_week);
                $start_week = date("Y-m-d", $start_week);
                $end_week = date("Y-m-d", $end_week);
                $postDetail->whereBetween('tbl_posts.created_at', [$start_week, $end_week]);
            } else if ($posted_within == "monthly") {
                $postDetail->whereMonth('tbl_posts.created_at', '=', Carbon::now()->subMonth()->month);
            }
        }
        /* custom fields fillter start */
        $array = array();
        $filter = $request->fullUrl();
        $postids = array();
        $numberids = array();
        if (!empty($get_all_posts)) {
            $selectpostids = $get_all_posts;
        } else {
            $selectpostids = array();
        }
        parse_str(parse_url($filter, PHP_URL_QUERY), $array);
        foreach ($array as $key => $value) {
            if (preg_match('/field_/', $key)) {
                Session::put(['is_custome_fields' => 1]);
                break;
            } else if ($key == 'price_range') {
                Session::put(['is_custome_fields' => 1]);
                break;
            } else {
                Session::forget('is_custome_fields');
            }
        }
        $is_custome_checkbox = 0;
        $is_custome_select = 0;
        $is_custome_number = 0;
        if (!empty($get_all_posts)) {
            $selectpostids = $get_all_posts;
        } else {
            $selectpostids = array();
        }
        /* type number filter */
        foreach ($array as $key => $value) {
            $cate = TblCategory::where('id', $request->category_id)->first();
            if ($key == "price_range") {
                // dd('price');
                $is_custome_number = 1;
                $values = explode(',', $value);
                $res1 = $values[0];
                $res2 = $values[1];
                if ((int) ($res1) && (int) ($res2)) {
                    $res11 = preg_replace(
                        "/[^0-9]/",
                        "",
                        $res1
                    );
                    $res22 = preg_replace(
                        "/[^0-9]/",
                        "",
                        $res2
                    );
                    $range1 = (int) round($res11);
                    $range2 = (int) round($res22);
                    // DB::enableQueryLog();
                    $postvalues = TblPost::where('active', '1')
                        ->where('category_id', $cate->id)->whereIn('id', $selectpostids)->whereBetween('price', [$range1, $range2])->get();
                    //         $query = DB::getQueryLog();
                    //    dd($query);
                    // dd($postvalues);
                    $selectpostids = array();
                    $numberids = array();
                    foreach ($postvalues as $postvalue) {
                        $numberids[] = $postvalue->id;
                        $selectpostids[] = $postvalue->id;
                    }
                }
            }
        }
        // dd($numberids);
        foreach ($array as $key => $value) {
            if (preg_match('/field_/', $key)) {
                $cate = TblCategory::where('id', $request->category_id)->first();
                $field_name = explode('_', $key)[1];
                if (!empty($field_name)) {
                    $first = (str_replace('*', ' ', ucfirst($field_name)));
                    if (($first != "Brand and model") && ($first != "Modelswithbrand")) {
                        $field_detail = TblFieldsDetail::where('name', $first)
                            ->where('active', '1')
                            ->where('cat_id', $cate->id)
                            ->first();
                        if (!empty($field_detail)) {
                            if ($field_detail->type == "number") {
                                $is_custome_number = 1;
                                $values = explode(',', $value);
                                $range1 = (int) round($values[0]);
                                $range2 = (int) round($values[1]);
                                $postvalues = TblPostValue::where('field_id', $field_detail->id)
                                    ->whereIn('post_id', $selectpostids)->whereBetween('value', [$range1, $range2])->get();
                                $selectpostids = array();
                                $numberids = array();
                                foreach ($postvalues as $postvalue) {
                                    $numberids[] = $postvalue->post_id;
                                    $selectpostids[] = $postvalue->post_id;
                                }
                            }
                        }
                    }
                }
            }
        }
        // dd($numberids);
        /* type select filter */
        $check_allposts = array();
        foreach ($array as $key => $value) {
            if (preg_match('/field_/', $key)) {
                $cate = TblCategory::where('id', $request->category_id)->first();
                $field_name = explode('_', $key)[1];
                if (!empty($field_name)) {
                    $first = (str_replace('*', ' ', ucfirst($field_name)));
                    if ($first == "Modelswithbrand") {
                        $field_detail = TblFieldsDetail::where('name', "Brand and model")
                            ->where('active', '1')
                            ->where('cat_id', $cate->id)
                            ->first();
                        // dd($field_detail,$cate->id,$first);
                        $is_custome_select = 1;
                        if (!empty($selectpostids)) {
                            //    dd('model',$selectpostids);
                            DB::enableQueryLog();
                            $postvalues = TblPostValue::where('field_id', $field_detail->id)
                                ->where('active', '1')
                                ->whereIn('post_id', $selectpostids)
                                ->whereRaw("find_in_set('$value',value)")
                                ->get();
                            // dd($selectpostids, $postvalues);
                            //  $query = DB::getQueryLog();
                            //  echo "<pre>";
                            //  echo 'Model'; 
                            //    dd($query);
                        } else {
                            $postvalues = TblPostValue::where('field_id', $field_detail->id)
                                ->where('active', '1')
                                ->whereRaw("find_in_set('$value',value)")
                                ->get();
                        }
                        if ($postvalues->count() > 0) {
                            $selectpostids = array();
                            foreach ($postvalues as $postvalue) {
                                $selectpostids[] = $postvalue->post_id;
                            }
                        } else {
                            $selectpostids = [];
                        }
                    } else {
                        $field_detail = TblFieldsDetail::where('name', $first)
                            ->where('active', '1')
                            ->where('cat_id', $cate->id)
                            ->first();
                        if (!empty($field_detail)) {
                            if ($field_detail->type == "select") {
                                $is_custome_select = 1;
                                if ($first == "Brand and model") {
                                    DB::enableQueryLog();
                                    //  $get_brand_id = TblFieldsOption::where('key', 'like','%' . $value .'%')->pluck('id')->first();
                                    $get_brand_id = TblFieldsOption::where('key', $value)->where('active', '1')->pluck('id')->first();
                                    $postvalues = TblPostValue::where('field_id', $field_detail->id)
                                        ->where('active', '1')
                                        ->whereIn('post_id', $selectpostids)
                                        ->whereRaw("find_in_set('$get_brand_id',value)")
                                        ->get();
                                    //  dd($postvalues);
                                } else {
                                    $postvalues = TblPostValue::where('field_id', $field_detail->id)
                                        ->where('active', '1')
                                        ->whereIn('post_id', $selectpostids)
                                        ->whereRaw("find_in_set('$value',value)")
                                        ->get();
                                }
                                if (isset($postvalues)) {
                                    if ($postvalues->count() > 0) {
                                        $selectpostids = array();
                                        foreach ($postvalues as $postvalue) {
                                            $selectpostids[] = $postvalue->post_id;
                                        }
                                        //  dd($selectpostids); 
                                    } else {
                                        $selectpostids = [];
                                    }
                                } else {
                                    //  $selectpostids = [];
                                }
                            }
                        }
                    }
                }
            }
        }
        //  dd($selectpostids,$get_all_posts);
        //   dd(array_unique($selectpostids));
        /* type text box and check box filter */
        $radio_postids = array();
        $chk_postids = array();
        $check_allposts = array();
        /* type text box and check box filter */
        foreach ($array as $key => $value) {
            if (preg_match('/field_/', $key)) {
                $cate = TblCategory::where('id', $request->category_id)->first();
                $field_name = explode('_', $key)[1];
                if (!empty($field_name)) {
                    $first = (str_replace('*', ' ', ucfirst($field_name)));
                    if (($first != "Brand and model") && ($first != "Modelswithbrand")) {
                        // $field_detail = TblFieldsDetail::where('name', $first)
                        //     ->where('active', '1')
                        //     ->where('cat_id', $cate->id)
                        //     ->first();
                        //     if(!empty($field_detail)){
                        //         if ($field_detail->type == "radio-group"  && $field_detail->is_multiple == "0") {
                        //             $is_custome_checkbox = 1;
                        //             $postvalues = TblPostValue::where('field_id', $field_detail->id)
                        //                 ->where('active', '1')
                        //                 ->whereIn('post_id', $selectpostids)
                        //                 ->whereRaw("find_in_set('" . $value . "',value)")
                        //                 ->get();
                        //             // foreach ($postvalues as $postvalue) {
                        //             //     $postids[] = $postvalue->post_id;
                        //             // }
                        //             $radio_postids = array();
                        //             foreach ($postvalues as $postvalue) {
                        //                 $radio_postids[] = $postvalue->post_id;
                        //             }
                        //         }
                        //     }
                        // if(!empty($field_detail)){
                        //     if ($field_detail->type == "checkbox-group" || $field_detail->is_multiple == "1") {
                        //         $is_custome_checkbox = 1;
                        //         $postvalues = TblPostValue::where('field_id', $field_detail->id)
                        //             ->where('active', '1')
                        //             ->whereIn('post_id', $selectpostids)
                        //             ->whereRaw("find_in_set('" . $value . "',value)")
                        //             ->get();
                        //         // foreach ($postvalues as $postvalue) {
                        //         //     $postids[] = $postvalue->post_id;
                        //         // }
                        //         $chk_postids = array();
                        //         foreach ($postvalues as $postvalue) {
                        //             $chk_postids[] = $postvalue->post_id;
                        //         }
                        //         $check_allposts = array_merge($check_allposts, $chk_postids);
                        //     } 
                        // }
                        $field_detail = TblFieldsDetail::where('name', $first)
                            ->where('active', '1')
                            ->where('cat_id', $cate->id)
                            ->first();
                        if (!empty($field_detail)) {
                            if ($field_detail->type == "radio-group" && $field_detail->is_multiple == "0") {
                                $is_custome_checkbox = 1;
                                DB::enableQueryLog();
                                $postvalues = TblPostValue::where('field_id', $field_detail->id)
                                    ->where('active', '1')
                                    ->whereIn('post_id', $selectpostids)
                                    ->whereRaw("find_in_set('" . $value . "',value)")
                                    ->get();
                                $query = DB::getQueryLog();
                                if (isset($postvalues)) {
                                    if (!empty($postvalues)) {
                                        $radio_postids = array();
                                        $selectpostids = array();
                                        foreach ($postvalues as $postvalue) {
                                            $radio_postids[] = $postvalue->post_id;
                                            $selectpostids[] = $postvalue->post_id;
                                        }
                                    } else {
                                        $radio_postids = array();
                                    }
                                } else {
                                    $radio_postids = array();
                                }
                            }
                            if ($field_detail->type == "checkbox-group" || $field_detail->is_multiple == "1") {
                                $is_custome_checkbox = 1;
                                DB::enableQueryLog();
                                $postvalues = TblPostValue::where('field_id', $field_detail->id)
                                    ->where('active', '1')
                                    ->whereIn('post_id', $selectpostids)
                                    ->whereRaw("find_in_set('" . $value . "',value)")
                                    ->get();
                                if (isset($postvalues)) {
                                    if (!empty($postvalues)) {
                                        $chk_postids = array();
                                        $selectpostids = array();
                                        foreach ($postvalues as $postvalue) {
                                            $chk_postids[] = $postvalue->post_id;
                                            $selectpostids[] = $postvalue->post_id;
                                        }
                                    } else {
                                        $radio_postids = array();
                                    }
                                } else {
                                    $chk_postids = array();
                                }
                                $check_allposts = array_merge($check_allposts, $chk_postids);
                            }
                        }
                    }
                }
            }
        }
        // dd($chk_postids,$check_allposts);
        if (!empty($radio_postids) && !empty($chk_postids)) {
            $postids = array_intersect($radio_postids, $chk_postids);
        } elseif (!empty($radio_postids)) {
            $postids = $radio_postids;
        } elseif (!empty($chk_postids)) {
            // if u need both checkbox value pass post id as $check_allposts
            $postids = $chk_postids;
        }
        $value_session = session::get('is_custome_fields');
        if ($value_session == 1) {
            if (($is_custome_select == 1) && ($is_custome_number == 0) && ($is_custome_checkbox == 0)) {
                if (!empty($postids)) {
                    $postDetail->whereIn('tbl_posts.id', $postids);
                }
                if (!empty($numberids)) {
                    $postDetail->whereIn('tbl_posts.id', $numberids);
                }
                $postDetail->whereIn('tbl_posts.id', $selectpostids);
            } else if (($is_custome_number == 1) && ($is_custome_select == 0) && ($is_custome_checkbox == 0)) {
                if (!empty($postids)) {
                    $postDetail->whereIn('tbl_posts.id', $postids);
                }
                $postDetail->whereIn('tbl_posts.id', $numberids);
                if (!empty($selectpostids)) {
                    $postDetail->whereIn('tbl_posts.id', $selectpostids);
                }
            } else if ($is_custome_checkbox == 1) {
                if ($is_custome_select == 1) {
                    $postDetail->whereIn('tbl_posts.id', $selectpostids);
                }
                if ($is_custome_number == 1) {
                    $postDetail->whereIn('tbl_posts.id', $numberids);
                }
                $postDetail->whereIn('tbl_posts.id', $postids);
            } else {
                if ($is_custome_select == 1) {
                    $postDetail->whereIn('tbl_posts.id', $selectpostids);
                }
                if ($is_custome_number == 1) {
                    $postDetail->whereIn('tbl_posts.id', $numberids);
                }
                if (!empty($postids)) {
                    $postDetail->whereIn('tbl_posts.id', $postids);
                }
            }
        }
        /* custome filter end */
        $final_postdetail = $postDetail->pluck('id')->toArray();
        $payment_ads_ids = array();
        $free_ads_ids = array();
        /* get unexpired payment post */
        $payment_ids_array = TblPost::get_unexpired_payment_post_ids();
        /* get unexpired free post */
        $free_ids_array = TblPost::get_unexpired_free_post_ids();
        foreach ($payment_ids_array as $payment_ids_array) {
            if (in_array($payment_ids_array, $final_postdetail)) {
                $payment_ads_ids[] = $payment_ids_array;
            }
        }
        foreach ($free_ids_array as $free_ids_array) {
            if (in_array($free_ids_array, $final_postdetail)) {
                $free_ads_ids[] = $free_ids_array;
            }
        }
        $get_ads_big_cnt = array(count($free_ads_ids), count($payment_ads_ids));
        $maxs = array_keys($get_ads_big_cnt, max($get_ads_big_cnt));
        if ($maxs[0] == 0) {
            $big = $free_ads_ids;
            $small = $payment_ads_ids;
        } else {
            $big = $payment_ads_ids;
            $small = $free_ads_ids;
        }
        $all_post = array_merge($big, $small);
        $unique_all_post = array_unique($all_post, SORT_REGULAR);
        $final_result_array = $all_post;
        $fildata = $this->filterdata($unique_all_post);
        // dd($branddata);
        // search post id end
        $cfld = TblCustomField::where('cat_id', $id)->get();
        $cat = TblCategory::where('id', $id)->where('enable_disable', 1)->first();
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                // dd($user);
                $lan_code = User::where('id', $user)->value('preferred_language');
            } else {
                $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
        }
        $cat_filter = array();
        $categoryName = "";
        if ($cat->parent_id == null) {
            $cat_filter[] = TblCategory::where('enable_disable', 1)->descendantsAndSelf($cat->id);
            // $cat_filter['title'] = Languages::where('lang_code',$lan_code)->where('lang_org_text',$cat_filter['title'])->value('lang_text');
            // dd($cat->id);
        } else {
            $cate = TblCategory::where('id', $cat->parent_id)->where('enable_disable', 1)->first();
            // dd($cate->id);
            $cat_filter[] = TblCategory::where('enable_disable', 1)->descendantsAndSelf($cate->id);
            // $cate_title =Languages::where('lang_code',$lan_code)->where('lang_org_text',$cate->title)->value('lang_text');
            $categoryName = $cate->title;
        }
        foreach ($cat_filter as $filter) {
            foreach ($filter as $value) {
                // dd($value->title);
                $cate_title = Languages::where('lang_code', $lan_code)->where('lang_org_text', $value->title)->value('lang_text');
                $value->title = !empty($cate_title) ? $cate_title : $value->title;
            }
        }
        // dd($cat_filter);
        //start
        $allnameId = array();
        $allnameIds = array();
        foreach ($cfld as $cfName) {
            $gethtml = $cfName->html;
            if (!empty($gethtml)) {
                $gthtmldata = json_decode($gethtml);
                foreach ($gthtmldata as $hmldata) {
                    $allnameIds[] = $hmldata->name;
                }
            }
        }
        $allnameId = array_unique($allnameIds);
        $finalarrayData = array();
        foreach ($allnameId as $fields_gets) {
            $finalarrayData[] = TblFieldsDetail::where('cat_id', $id)
                ->where('active', '1')
                ->where('filter', '1')
                ->where('form_field_name', $fields_gets)
                ->first();
        }
        $finalarrayData = array_filter($finalarrayData);
        //  dd($finalarrayData);
        //end custom
        $data = array();
        if (!empty($cfld[0])) {
            if ($cfld[0]->field_count > 0) {
                // $arrayData = TblFieldsDetail::where('cat_id', $id)->where('active', '1')->get();
                // if ($arrayData->count() > 0) {
                if (count($finalarrayData) > 0) {
                    $loopmain = 0;
                    $type_text = $type_number = $type_textarea = $type_select = $type_checkbox = $type_radio = 0;
                    foreach ($finalarrayData as $r) {
                        $field_id = $r["id"];
                        $type = $r["type"];
                        $name = $field_id . '_' . $r["form_field_name"];
                        $label = $r['name'];
                        $token = $this->getBearerToken();
                        if (!empty($token['code']) && ($token['code'] == 200)) {
                            $user = $this->getLoggedUser($token['token']);
                            if (!empty($user)) {
                                // dd($user);
                                $lan_code = User::where('id', $user)->value('preferred_language');
                            } else {
                                $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                                $response = [
                                    'success' => false,
                                    'code' => 0,
                                    'message' => "Invalid User"
                                ];
                            }
                        } else {
                            $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                        }
                        $get_lang = Languages::where('lang_code', $lan_code)->where('lang_org_text', $r['name'])->value('lang_text');
                        $label_text = !empty($get_lang) ? $get_lang : $label;
                        $required = $r['required'];
                        $show_count = $r['count'];
                        $show_lable = $r['icon'];
                        if ($type == "text") {
                            $type_text = 1;
                            $data['text'][] = array(
                                'label' => $label_text,
                                'passing_label' => str_replace(" ", '*', strtolower($label)),
                                'name' => $name,
                                'required' => $required
                            );
                        }
                        if ($type == "number") {
                            $type_number = 1;
                            // dd($all_post);
                            $price_value = TblPostValue::where('field_id', $field_id)->whereIn('post_id', $all_post)->where('active', '1')->pluck('value')->toArray();
                            // dd($price_value);
                            if (!empty($price_value)) {
                                $maxPrice = max($price_value);
                            }
                            if (count($all_post) >= 1) {
                                $minPrice = TblPostValue::where('field_id', $field_id)->whereIn('post_id', $all_post)->where('active', '1')->min('value');
                            }
                            //    dd($minPrice,$maxPrice);
                            $data['number'][] = array(
                                'label' => $label_text,
                                'passing_label' => str_replace(" ", '*', strtolower($label)),
                                'name' => $name,
                                'required' => $required,
                                'max_value' => !empty($maxPrice) ? $maxPrice : "",
                                'min_value' => !empty($minPrice) ? $minPrice : "0"
                            );
                        }
                        // if ($type == "textarea") {
                        //     $type_textarea = 1;
                        //     $data['textarea'][] = array(
                        //         'name' => $name,
                        //         'label' => $label,
                        //         'passing_label' => str_replace(" ", '*', strtolower($label)),
                        //         'required' => $required
                        //     );
                        // }
                        if ($type == "select") {
                            // $type_select = 1;
                            $selectarrayData = TblFieldsOption::where('cat_id', $id)->where('form_field_name', $r["form_field_name"])->where('active', '1')->orderby('value', 'asc')->get();
                            // echo "<pre>";
                            // print_r($selectarrayData);
                            if ($r['form_field_name'] == "brandwithmodel") {
                                $type_radio = 1;
                                $select_options = array();
                                foreach ($selectarrayData as $k) {
                                    if ((in_array($k["value"], $branddata['get_value']) || (in_array($k["id"], $branddata['get_value'])))) {
                                        if (array_key_exists($k['value'], $branddata['post_count'])) {
                                            $b = $k['value'];
                                            $a = $branddata['post_count'][$b];
                                        } elseif (array_key_exists($k['id'], $branddata['post_count'])) {
                                            $b = $k['id'];
                                            $a = $branddata['post_count'][$b];
                                        }
                                        $dis = '';
                                        $count = $a;
                                    } else {
                                        $dis = 'disabled';
                                        $count = "0";
                                    }
                                    $kid = explode('-', $k["id"])[0];
                                    $key = Languages::where('lang_code', $lan_code)->where('lang_org_text', $k["key"])->value('lang_text');
                                    $select_options[] = array(
                                        'value' => $k["id"],
                                        'key' => !empty($key) ? $key : $k['key'],
                                        'logo' => Url::to('storage/customfields/filters/' . $k["logo"]),
                                        'count' => $count
                                    );
                                }
                                $brand_name = Languages::where('lang_code', $lan_code)->where('lang_org_text', "Brand and model")->value('lang_text');
                                $data['radio'][] = array(
                                    'label' => $brand_name,
                                    'passing_label' => str_replace(" ", '*', strtolower($label)),
                                    'name' => $name,
                                    'required' => $required,
                                    'options' => $select_options
                                );
                                // model select options
                                // $modelarrayData = TblFieldsOption::where('id', 'like', '%' . $brand_kid . '%')->where('active', '1')->first();
                                // dd($request->get('brand'),$request->get('brand*and*model'));
                                $brand_value = !empty($request->get('brand*and*model')) ? $request->get('brand*and*model') : "";
                                $brand_id = TblFieldsOption::where('key', 'like', '%' . $brand_value . '%')->where('active', '1')->value('id');
                                if (!empty($brand_id)) {
                                    $arrayData = TblFieldsOption::where('id', 'like', '%' . $brand_id . '%')
                                        ->where('cat_id', $id)
                                        ->where('active', '1')->first();
                                    if (!empty($arrayData)) {
                                        $modelarrayData = explode(',', $arrayData['value']);
                                        $model_select_options = array();
                                        foreach ($modelarrayData as $k) {
                                            $modelVal = Str::slug($k, "-");
                                            if ((in_array($modelVal, $branddata['get_value']))) {
                                                if (array_key_exists($modelVal, $branddata['post_count'])) {
                                                    // dd($modelVal,$branddata['get_value'],$branddata['post_count']);
                                                    $b = $modelVal;
                                                    $a = $branddata['post_count'][$b];
                                                }
                                                $dis = '';
                                                $count = $a;
                                            } else {
                                                $dis = 'disabled';
                                                $count = "0";
                                            }
                                            $model_select_options[] = array(
                                                'value' => $modelVal,
                                                'key' => $k,
                                                'count' => $count
                                            );
                                        }
                                        $model = Languages::where('lang_code', $lan_code)->where('lang_org_text', "Models")->value('lang_text');
                                        $data['radio'][] = array(
                                            'label' => $model,
                                            'passing_label' => "modelswithbrand",
                                            'name' => $field_id . "_modelswithbrand",
                                            'required' => 1,
                                            'options' => $model_select_options
                                        );
                                    }
                                }
                            } elseif ($r['name'] == "Type") {
                                // dd('dbfh');
                                $type_radio = 1;
                                $select_options = array();
                                foreach ($selectarrayData as $k) {
                                    if ((in_array($k["value"], $branddata['get_value']) || (in_array($k["id"], $branddata['get_value'])))) {
                                        $type_radio = 1;
                                        // $type_checkbox = 1;
                                        if (array_key_exists($k['value'], $branddata['post_count'])) {
                                            $b = $k['value'];
                                            $a = $branddata['post_count'][$b];
                                        } elseif (array_key_exists($k['id'], $branddata['post_count'])) {
                                            $b = $k['id'];
                                            $a = $branddata['post_count'][$b];
                                        }
                                        $dis = '';
                                        $count = $a;
                                    } else {
                                        $dis = 'disabled';
                                        $count = "0";
                                    }
                                    $key = Languages::where('lang_code', $lan_code)->where('lang_org_text', $k["key"])->value('lang_text');
                                    $select_options[] = array(
                                        'value' => $k["value"],
                                        'key' => !empty($key) ? $key : $k['key'],
                                        'logo' => Url::to('storage/customfields/filters/' . $k["logo"]),
                                        'name' => "body*type",
                                        'count' => $count
                                    );
                                }
                                $data['radio'][] = array(
                                    'label' => $label_text,
                                    'passing_label' => str_replace(" ", '*', strtolower($label)),
                                    'name' => $name,
                                    'required' => $required,
                                    'show_count' => $show_count,
                                    'show_lable' => $show_lable,
                                    'options' => $select_options
                                );
                            } else {
                                $type_radio = 1;
                                $type_checkbox = 1;
                                if ($r['form_field_name'] != "attribute" && $r['form_field_name'] != "option_list") {
                                    $select_options = array();
                                    foreach ($selectarrayData as $k) {
                                        if ((in_array($k["value"], $branddata['get_value']) || (in_array($k["id"], $branddata['get_value'])))) {
                                            $type_radio = 1;
                                            if (array_key_exists($k['value'], $branddata['post_count'])) {
                                                $b = $k['value'];
                                                $a = $branddata['post_count'][$b];
                                            } elseif (array_key_exists($k['id'], $branddata['post_count'])) {
                                                $b = $k['id'];
                                                $a = $branddata['post_count'][$b];
                                            }
                                            $dis = '';
                                            $count = $a;
                                        } else {
                                            $dis = 'disabled';
                                            $count = "0";
                                        }
                                        $key = Languages::where('lang_code', $lan_code)->where('lang_org_text', $k["key"])->value('lang_text');
                                        $select_options[] = array(
                                            'value' => $k["value"],
                                            'key' => !empty($key) ? $key : $k['key'],
                                            'logo' => Url::to('storage/customfields/filters/' . $k["logo"]),
                                            'count' => $count
                                        );
                                    }
                                    $data['checkbox'][] = array(
                                        'label' => $label_text,
                                        'passing_label' => str_replace(" ", '*', strtolower($label)),
                                        'name' => $name,
                                        'required' => $required,
                                        'show_count' => $show_count,
                                        'show_lable' => $show_lable,
                                        'options' => $select_options
                                    );
                                    // dd($data);
                                } else {
                                    $select_options = array();
                                    if ($r['form_field_name'] == "attribute") {
                                        foreach ($selectarrayData as $k) {
                                            if ((in_array($k["value"], $branddata['get_value']) || (in_array($k["id"], $branddata['get_value'])))) {
                                                $type_checkbox = 1;
                                                if (array_key_exists($k['value'], $branddata['post_count'])) {
                                                    $b = $k['value'];
                                                    $a = $branddata['post_count'][$b];
                                                } elseif (array_key_exists($k['id'], $branddata['post_count'])) {
                                                    $b = $k['id'];
                                                    $a = $branddata['post_count'][$b];
                                                }
                                                $dis = '';
                                                $count = $a;
                                            } else {
                                                $dis = 'disabled';
                                                $count = "0";
                                            }
                                            $key = Languages::where('lang_code', $lan_code)->where('lang_org_text', $k["key"])->value('lang_text');
                                            $select_options[] = array(
                                                'value' => $k["value"],
                                                'key' => !empty($key) ? $key : $k['key'],
                                                'logo' => Url::to('storage/customfields/filters/' . $k["logo"]),
                                                'name' => "body*type",
                                                'count' => $count
                                            );
                                        }
                                        $data['checkbox'][] = array(
                                            'label' => $label_text,
                                            'passing_label' => str_replace(" ", '*', strtolower($label)),
                                            'name' => $name,
                                            'required' => $required,
                                            'show_count' => $show_count,
                                            'show_lable' => $show_lable,
                                            'options' => $select_options
                                        );
                                    }
                                    // if ($r['form_field_name'] == "option_list") {
                                    //     foreach ($selectarrayData as $k) {
                                    //         $select_options[] = array(
                                    //             'value' => $k["value"],
                                    //             'key' => Url::to('storage/customfields/filters/' . $k["key"]),
                                    //             'name' => "brand_name"
                                    //         );
                                    //     }
                                    //     $data['select'][] = array(
                                    //         'label' => $label,
                                    //         'passing_label' => 'brand_name',
                                    //         'name' => $name,
                                    //         'required' => $required,
                                    //         'options' => $select_options
                                    //     );
                                    // }
                                }
                            }
                        }
                        if ($type == "checkbox-group") {
                            $type_checkbox = 1;
                            $checkbox_arrayData = TblFieldsOption::where('cat_id', $id)->where('form_field_name', $r["form_field_name"])->where('active', '1')->orderby('value', 'asc')->get();
                            $checkbox_options = array();
                            foreach ($checkbox_arrayData as $k) {
                                if ((in_array($k["value"], $branddata['get_value']) || (in_array($k["id"], $branddata['get_value'])))) {
                                    if (array_key_exists($k['value'], $branddata['post_count'])) {
                                        $b = $k['value'];
                                        $a = $branddata['post_count'][$b];
                                    } elseif (array_key_exists($k['id'], $branddata['post_count'])) {
                                        $b = $k['id'];
                                        $a = $branddata['post_count'][$b];
                                    }
                                    $dis = '';
                                    $count = $a;
                                } else {
                                    $dis = 'disabled';
                                    $count = "0";
                                }
                                $key = Languages::where('lang_code', $lan_code)->where('lang_org_text', $k["key"])->value('lang_text');
                                $checkbox_options[] = array(
                                    'value' => $k["value"],
                                    'key' => !empty($key) ? $key : $k['key'],
                                    'logo' => Url::to('storage/customfields/filters/' . $k["logo"]),
                                    'count' => $count
                                );
                            }
                            $data['checkbox'][] = array(
                                'name' => $name,
                                'passing_label' => str_replace(" ", '*', strtolower($label)),
                                'label' => $label_text,
                                'required' => $required,
                                'show_count' => $show_count,
                                'show_lable' => $show_lable,
                                'options' => $checkbox_options
                            );
                        }
                        if ($type == "radio-group") {
                            $type_radio = 1;
                            $type_checkbox = 1;
                            $radioarrayData = TblFieldsOption::where('cat_id', $id)->where('form_field_name', $r["form_field_name"])->where('active', '1')->orderby('value', 'asc')->get();
                            $radio_options = array();
                            foreach ($radioarrayData as $k) {
                                if ((in_array($k["value"], $branddata['get_value']) || (in_array($k["id"], $branddata['get_value'])))) {
                                    if (array_key_exists($k['value'], $branddata['post_count'])) {
                                        $b = $k['value'];
                                        $a = $branddata['post_count'][$b];
                                    } elseif (array_key_exists($k['id'], $branddata['post_count'])) {
                                        $b = $k['id'];
                                        $a = $branddata['post_count'][$b];
                                    }
                                    $dis = '';
                                    $count = $a;
                                } else {
                                    $dis = 'disabled';
                                    $count = "0";
                                }
                                $key = Languages::where('lang_code', $lan_code)->where('lang_org_text', $k["key"])->value('lang_text');
                                $radio_options[] = array(
                                    'value' => $k["value"],
                                    'key' => !empty($key) ? $key : $k['key'],
                                    'logo' => Url::to('storage/customfields/filters/' . $k["logo"]),
                                    'count' => $count
                                );
                            }
                            $data['checkbox'][] = array(
                                'name' => $name,
                                'passing_label' => str_replace(" ", '*', strtolower($label)),
                                'label' => $label_text,
                                'required' => $required,
                                'show_count' => $show_count,
                                'show_lable' => $show_lable,
                                'options' => $radio_options
                            );
                        }
                        $loopmain++;
                    }
                    $get_max_price = TblPost::whereNotNull('price')->where('active', 1)->whereIn('id', $final_result_array)->max("price");
                    if (count($final_result_array) > 1) {
                        $get_min_price = TblPost::whereNotNull('price')->where('active', 1)->whereIn('id', $final_result_array)->min("price");
                    }
                    if (!empty($get_max_price)) {
                        $type_number = 1;
                        $max_price = $get_max_price;
                        $min_price = !empty($get_min_price) ? $get_min_price : "";
                        $brand_name = Languages::where('lang_code', $lan_code)->where('lang_org_text', "Price")->value('lang_text');
                        $data['number'][] = array(
                            'label' => $brand_name,
                            'passing_label' => 'price_range',
                            'name' => '',
                            'required' => $required,
                            'max_value' => !empty($max_price) ? $max_price : "",
                            'min_value' => !empty($min_price) ? $min_price : "0"
                        );
                    }
                    // dd($data);
                    // dd($type_radio);
                    if (!empty($cat_filter)) {
                        $data['category'][] = array(
                            'label' => $categoryName,
                            'options' => $cat_filter
                        );
                    }
                    if ($type_text == 0) {
                        $data['text'] = array();
                    }
                    if ($type_number == 0) {
                        $data['number'] = array();
                    }
                    if ($type_textarea == 0) {
                        $data['textarea'] = array();
                    }
                    if ($type_select == 0) {
                        $data['select'] = array();
                    }
                    if ($type_checkbox == 0) {
                        $data['checkbox'] = array();
                    }
                    if ($type_radio == 0) {
                        $data['radio'] = array();
                    }
                    if (empty($cat_filter)) {
                        $data['category'] = array();
                    }
                }
            }
        }
        $getstates = array();
        $getcountries = array();
        $getcities = array();
        if (!empty($city)) {
            $state_name = TblState::where('name', $state)->first();
            if (!empty($state_name)) {
                $getcountries = TblCountry::where('id', $state_name->country_id)->first();
                $getstates = TblState::where('id', $state_name->id)->get();
            }
        }
        if (!empty($state)) {
            //   dd($state);
            $states = TblState::where('name', $state)->first();
            //    dd($states);
            $getcountries = TblCountry::where('id', $states->country_id)->first();
            $getstates = TblState::where('id', $states->id)->get();
        }
        if (!empty($country)) {
            $countries = TblCountry::where('name', $country)->first();
            $getcountries = TblCountry::where('id', $countries->id)->first();
            // $getstates = TblState::where('name', $countries->id)->get();
        }
        // dd($getstates,$getcountries);
        $location = array();
        if (!empty($getcountries)) {
            $location['country'] = $getcountries->name;
        }
        if (!empty($getstates)) {
            foreach ($getstates as $staterow) {
                $location['state'] = array(
                    'state_name' => $staterow->name,
                    'state_id' => $staterow->id
                );
                $getcities = TblPost::getCities($staterow->id);
                if (!empty($getcities)) {
                    foreach ($getcities as $row) {
                        $city_product_cnt = TblPost::city_product_cnt($id, $row->name);
                        if ($city_product_cnt > 0) {
                            $location['city'] = array(
                                [
                                    'city_name' => $row->name,
                                    'city_lat' => $row->latitude,
                                    'city_lon' => $row->logitude,
                                    'city_state' => $staterow->name
                                ]
                            );
                        }
                    }
                }
            }
        }
        // dd($data);
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $data,
            'location' => $location
        ];
        return response()->json($response);
    }
    /* get custome fields based on category to add post*/
    public function get_custome_fields_addpost(Request $request, $id)
    {
        $cfld = TblCustomField::where('cat_id', $id)->get();
        $cat = TblCategory::where('id', $id)->first();
        $cat_filter = array();
        $categoryName = "";
        if ($cat->parent_id == null) {
            $cat_filter[] = TblCategory::descendantsAndSelf($cat->id);
            // dd($cat->id);
        } else {
            $cate = TblCategory::where('id', $cat->parent_id)->first();
            // dd($cate->id);
            $cat_filter[] = TblCategory::descendantsAndSelf($cate->id);
            $categoryName = $cate->title;
        }
        $data = array();
        if (!empty($cfld[0])) {
            if ($cfld[0]->field_count > 0) {
                $arrayData = TblFieldsDetail::where('cat_id', $id)->where('active', '1')->get();
                if ($arrayData->count() > 0) {
                    $loopmain = 0;
                    $type_text = $type_number = $type_textarea = $type_select = $type_checkbox = $type_radio = 0;
                    foreach ($arrayData as $r) {
                        $field_id = $r["id"];
                        $type = $r["type"];
                        $name = $field_id . '_' . $r["form_field_name"];
                        $label = $r['name'];
                        $token = $this->getBearerToken();
                        if (!empty($token['code']) && ($token['code'] == 200)) {
                            $user = $this->getLoggedUser($token['token']);
                            if (!empty($user)) {
                                // dd($user);
                                $lan_code = User::where('id', $user)->value('preferred_language');
                            } else {
                                $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                                $response = [
                                    'success' => false,
                                    'code' => 0,
                                    'message' => "Invalid User"
                                ];
                            }
                        } else {
                            $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                        }
                        $get_lang = Languages::where('lang_code', $lan_code)->where('lang_org_text', $r['name'])->value('lang_text');
                        $label_text = !empty($get_lang) ? $get_lang : $label;
                        $required = $r['required'];
                        if ($type == "text") {
                            $type_text = 1;
                            $data['text'][] = array(
                                'label' => $label_text,
                                'passing_label' => str_replace(" ", '*', strtolower($label)),
                                'name' => $name,
                                'required' => $required
                            );
                        }
                        if ($type == "number") {
                            $type_number = 1;
                            $maxPrice = TblPostValue::where('field_id', $field_id)->where('active', '1')->max('value');
                            $data['number'][] = array(
                                'label' => $label_text,
                                'passing_label' => str_replace(" ", '*', strtolower($label)),
                                'name' => $name,
                                'required' => $required,
                                'max_value' => !empty($maxPrice) ? $maxPrice : ""
                            );
                        }
                        // if ($type == "textarea") {
                        //     $type_textarea = 1;
                        //     $data['textarea'][] = array(
                        //         'name' => $name,
                        //         'label' => $label,
                        //         'passing_label' => str_replace(" ", '*', strtolower($label)),
                        //         'required' => $required
                        //     );
                        // }
                        if ($type == "select") {
                            $type_select = 1;
                            $selectarrayData = TblFieldsOption::where('cat_id', $id)->where('form_field_name', $r["form_field_name"])->where('active', '1')->get();
                            $catData = TblCustomField::where('cat_id', $id)->first();
                            $jsonData = $catData['html'];
                            $decodedData = json_decode($jsonData, true);
                            if ($r['form_field_name'] == "brandwithmodel") {
                                $select_options = array();
                                foreach ($selectarrayData as $k) {
                                    // $kid = explode('-', $k["id"])[0]; changed this into full id on(1.11.2023)for filter issue
                                    $image = null;
                                    if (isset($decodedData[0]['values'])) {
                                        foreach ($decodedData[0]['values'] as $value) {
                                            if ($value['label'] === $k["key"]) {
                                                if (!empty($value['image']) && $value['image'] != 'noimage50.png') {
                                                    $image = 'https://www.justreused.com/storage/customfields/filters/' . $value['image'];
                                                } else if (!empty($value['logo']) && $value['logo'] != 'noimage50.png') {
                                                    $image = 'https://www.justreused.com/storage/customfields/filters/' . $value['logo'];
                                                } else if ($value['image'] == 'noimage50.png' || $value['logo'] != 'noimage50.png') {
                                                    $image = 'https://www.justreused.com/storage/noimage50.png';
                                                }
                                            }
                                        }
                                    }
                                    $kid = $k["id"];
                                    $select_options[] = array(
                                        'label' => $k['key'],
                                        'value' => $kid,
                                        'key' => $k["key"],
                                        'icon' => $image,
                                        'models' => $k['value']  // Models list for brand
                                    );
                                }
                                $get_lang = Languages::where('lang_code', $lan_code)->where('lang_org_text', 'Brands')->value('lang_text');
                                $label_text = !empty($get_lang) ? $get_lang : $label;
                                $data['select'][] = array(
                                    'label' => $label_text,
                                    'passing_label' => str_replace(" ", '*', strtolower($label)),
                                    'name' => $name,
                                    'required' => $required,
                                    'options' => $select_options
                                );
                                // model select options
                                // $modelarrayData = TblFieldsOption::where('id', 'like', '%' . $brand_kid . '%')->where('active', '1')->first();
                                $brand_id = !empty($request->get('brand')) ? $request->get('brand') : "";
                                if (!empty($brand_id)) {
                                    $arrayData = TblFieldsOption::where('id', 'like', '%' . $brand_id . '%')->where('active', '1')->first();
                                    if (!empty($arrayData)) {
                                        $modelarrayData = explode(',', $arrayData['value']);
                                        $model_select_options = array();
                                        foreach ($modelarrayData as $k) {
                                            $modelVal = Str::slug($k, "-");
                                            $model_select_options[] = array(
                                                'label' => $k,
                                                'value' => $modelVal,
                                                'key' => $k
                                            );
                                        }
                                        $get_lang = Languages::where('lang_code', $lan_code)->where('lang_org_text', 'Models')->value('lang_text');
                                        $label_text = !empty($get_lang) ? $get_lang : $label;
                                        $data['select'][] = array(
                                            'label' => $label_text,
                                            'passing_label' => "modelswithbrand",
                                            'name' => $field_id . "_modelswithbrand",
                                            'required' => 1,
                                            'options' => $model_select_options
                                        );
                                    }
                                }
                            } else {
                                if ($r['form_field_name'] != "attribute") {
                                    $select_options = array();
                                    foreach ($selectarrayData as $k) {
                                        $select_options[] = array(
                                            'label' => $k['key'],
                                            'value' => $k["value"],
                                            'key' => $k["key"]
                                        );
                                    }
                                    $data['select'][] = array(
                                        'label' => $label_text,
                                        'passing_label' => str_replace(" ", '*', strtolower($label)),
                                        'name' => $name,
                                        'required' => $required,
                                        'options' => $select_options
                                    );
                                } else {
                                    $select_options = array();
                                    if ($r['form_field_name'] == "attribute") {
                                        foreach ($selectarrayData as $k) {
                                            $select_options[] = array(
                                                'value' => $k["value"],
                                                'key' => Url::to('storage/customfields/filters/' . $k["key"]),
                                                'name' => "body*type",
                                                'label' => $k['key']
                                            );
                                        }
                                        $data['select'][] = array(
                                            'label' => $label_text,
                                            'passing_label' => str_replace(" ", '*', strtolower($label)),
                                            'name' => $name,
                                            'required' => $required,
                                            'options' => $select_options
                                        );
                                    }
                                    //   if ($r['form_field_name'] == "option_list") {
                                    //       foreach ($selectarrayData as $k) {
                                    //           $select_options[] = array(
                                    //               'value' => $k["value"],
                                    //               'key' => Url::to('storage/customfields/filters/' . $k["key"]),
                                    //               'name' => "brand_name"
                                    //           );
                                    //       }
                                    //       $data['select'][] = array(
                                    //           'label' => $label,
                                    //           'passing_label' => 'brand_name',
                                    //           'name' => $name,
                                    //           'required' => $required,
                                    //           'options' => $select_options
                                    //       );
                                    //   }
                                }
                            }
                        }
                        if ($type == "checkbox-group") {
                            $type_checkbox = 1;
                            $checkbox_arrayData = TblFieldsOption::where('cat_id', $id)->where('form_field_name', $r["form_field_name"])->where('active', '1')->get();
                            $checkbox_options = array();
                            foreach ($checkbox_arrayData as $k) {
                                $checkbox_options[] = array(
                                    'label' => $k['key'],
                                    'value' => $k["value"],
                                    'key' => $k["key"]
                                );
                            }
                            $data['checkbox'][] = array(
                                'name' => $name,
                                'passing_label' => str_replace(" ", '*', strtolower($label)),
                                'label' => $label_text,
                                'required' => $required,
                                'options' => $checkbox_options
                            );
                        }
                        if ($type == "radio-group") {
                            $type_radio = 1;
                            $radioarrayData = TblFieldsOption::where('cat_id', $id)->where('form_field_name', $r["form_field_name"])->where('active', '1')->get();
                            $radio_options = array();
                            foreach ($radioarrayData as $k) {
                                $radio_options[] = array(
                                    'label' => $k['key'],
                                    'value' => $k["value"],
                                    'key' => $k["key"]
                                );
                            }
                            $data['radio'][] = array(
                                'name' => $name,
                                'passing_label' => str_replace(" ", '*', strtolower($label)),
                                'label' => $label_text,
                                'required' => $required,
                                'options' => $radio_options
                            );
                        }
                        $loopmain++;
                    }
                    if (!empty($cat_filter)) {
                        $data['category'][] = array(
                            'label' => $categoryName,
                            'options' => $cat_filter
                        );
                    }
                    if ($type_text == 0) {
                        $data['text'] = array();
                    }
                    if ($type_number == 0) {
                        $data['number'] = array();
                    }
                    if ($type_textarea == 0) {
                        $data['textarea'] = array();
                    }
                    if ($type_select == 0) {
                        $data['select'] = array();
                    }
                    if ($type_checkbox == 0) {
                        $data['checkbox'] = array();
                    }
                    if ($type_radio == 0) {
                        $data['radio'] = array();
                    }
                    if (empty($cat_filter)) {
                        $data['category'] = array();
                    }
                }
            }
        }
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $data
        ];
        return response()->json($response);
    }
    /* get custome fields based on category */
    // public function get_custome_fields(Request $request, $id)
    // {
    //     $cfld  = TblCustomField::where('cat_id', $id)->get();
    //     $data = array();
    //     if (!empty($cfld[0])) {
    //         if ($cfld[0]->field_count > 0) {
    //             $arrayData = TblFieldsDetail::where('cat_id', $id)->where('active', '1')->get();
    //             if ($arrayData->count() > 0) {
    //                 $loopmain = 0;
    //                 $type_text = $type_number = $type_textarea = $type_select = $type_checkbox = $type_radio = 0;
    //                 foreach ($arrayData as $r) {
    //                     $field_id = $r["id"];
    //                     $type = $r["type"];
    //                     $name = $field_id . '_' . $r["form_field_name"];
    //                     $label = $r['name'];
    //                     $required = $r['required'];
    //                     if ($type == "text") {
    //                         $type_text = 1;
    //                         $data['text'][] = array(
    //                             'label' => $label,
    //                             'passing_label' => str_replace(" ", '*', strtolower($label)),
    //                             'name' => $name,
    //                             'required' => $required
    //                         );
    //                     }
    //                     if ($type == "number") {
    //                         $type_number = 1;
    //                         $maxPrice = TblPostValue::where('field_id', $field_id)->where('active', '1')->max('value');
    //                         $data['number'][] = array(
    //                             'label' => $label,
    //                             'passing_label' => str_replace(" ", '*', strtolower($label)),
    //                             'name' => $name,
    //                             'required' => $required,
    //                             'max_value' => !empty($maxPrice) ? $maxPrice : ""
    //                         );
    //                     }
    //                     // if ($type == "textarea") {
    //                     //     $type_textarea = 1;
    //                     //     $data['textarea'][] = array(
    //                     //         'name' => $name,
    //                     //         'label' => $label,
    //                     //         'passing_label' => str_replace(" ", '*', strtolower($label)),
    //                     //         'required' => $required
    //                     //     );
    //                     // }
    //                     if ($type == "select") {
    //                         $type_select = 1;
    //                         $selectarrayData = TblFieldsOption::where('cat_id', $id)->where('form_field_name', $r["form_field_name"])->where('active', '1')->get();
    //                         if ($r['form_field_name'] == "brandwithmodel") {
    //                             $select_options = array();
    //                             foreach ($selectarrayData as $k) {
    //                                 $kid = explode('-', $k["id"])[0];
    //                                 $select_options[] = array(
    //                                     'label' => $k["key"],
    //                                     'value' =>  $k["key"],
    //                                     'key' => $k["key"]
    //                                 );
    //                             }
    //                             $data['select'][] = array(
    //                                 'label' => "Brands",
    //                                 'passing_label' => str_replace(" ", '*', strtolower($label)),
    //                                 'name' => $name,
    //                                 'required' => $required,
    //                                 'options' => $select_options
    //                             );
    //                             // model select options
    //                             // $modelarrayData = TblFieldsOption::where('id', 'like', '%' . $brand_kid . '%')->where('active', '1')->first();
    //                             $brand_id = !empty($request->get('brand')) ? $request->get('brand') : "";
    //                             if (!empty($brand_id)) {
    //                                 $arrayData = TblFieldsOption::where('id', 'like', '%' . $brand_id . '%')->where('active', '1')->first();
    //                                 if (!empty($arrayData)) {
    //                                     $modelarrayData = explode(',', $arrayData['value']);
    //                                     $model_select_options = array();
    //                                     foreach ($modelarrayData as $k) {
    //                                         $modelVal = Str::slug($k, "-");
    //                                         $model_select_options[] = array(
    //                                             'value' => $modelVal,
    //                                             'key' => $k
    //                                         );
    //                                     }
    //                                     $data['select'][] = array(
    //                                         'label' => "Models",
    //                                         'passing_label' => "modelswithbrand",
    //                                         'name' => $field_id . "_modelswithbrand",
    //                                         'required' => 1,
    //                                         'options' => $model_select_options
    //                                     );
    //                                 }
    //                             }
    //                         } else {
    //                             $select_options = array();
    //                             foreach ($selectarrayData as $k) {
    //                                 $select_options[] = array(
    //                                     'value' => $k["value"],
    //                                     'key' => $k["key"]
    //                                 );
    //                             }
    //                             $data['select'][] = array(
    //                                 'label' => $label,
    //                                 'passing_label' => str_replace(" ", '*', strtolower($label)),
    //                                 'name' => $name,
    //                                 'required' => $required,
    //                                 'options' => $select_options
    //                             );
    //                         }
    //                     }
    //                     if ($type == "checkbox-group") {
    //                         $type_checkbox = 1;
    //                         $checkbox_arrayData = TblFieldsOption::where('cat_id', $id)->where('form_field_name', $r["form_field_name"])->where('active', '1')->get();
    //                         $checkbox_options = array();
    //                         foreach ($checkbox_arrayData as $k) {
    //                             $checkbox_options[] = array(
    //                                 'value' => $k["value"],
    //                                 'key' => $k["key"]
    //                             );
    //                         }
    //                         $data['checkbox'][] = array(
    //                             'name' => $name,
    //                             'passing_label' => str_replace(" ", '*', strtolower($label)),
    //                             'label' => $label,
    //                             'required' => $required,
    //                             'options' => $checkbox_options
    //                         );
    //                     }
    //                     if ($type == "radio-group") {
    //                         $type_radio = 1;
    //                         $radioarrayData = TblFieldsOption::where('cat_id', $id)->where('form_field_name', $r["form_field_name"])->where('active', '1')->get();
    //                         $radio_options = array();
    //                         foreach ($radioarrayData as $k) {
    //                             $radio_options[] = array(
    //                                 'label'=>$k["key"],
    //                                 'value' => $k["value"],
    //                                 'key' => $k["key"]
    //                             );
    //                         }
    //                         $data['radio'][] = array(
    //                             'name' => $name,
    //                             'passing_label' => str_replace(" ", '*', strtolower($label)),
    //                             'label' => $label,
    //                             'required' => $required,
    //                             'options' => $radio_options
    //                         );
    //                     }
    //                     $loopmain++;
    //                 }
    //                 if ($type_text == 0) {
    //                     $data['text'] = array();
    //                 }
    //                 if ($type_number == 0) {
    //                     $data['number'] = array();
    //                 }
    //                 if ($type_textarea == 0) {
    //                     $data['textarea'] = array();
    //                 }
    //                 if ($type_select == 0) {
    //                     $data['select'] = array();
    //                 }
    //                 if ($type_checkbox == 0) {
    //                     $data['checkbox'] = array();
    //                 }
    //                 if ($type_radio == 0) {
    //                     $data['radio'] = array();
    //                 }
    //             }
    //         }
    //     }
    //     $response = [
    //         'success' => true,
    //         'code' => 200,
    //         'data' => $data
    //     ];
    //     return response()->json($response);
    // }
    public function seller_profile(Request $request, $id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $page = $request->has('page') ? $request->get('page') : 1;
                $limit = $request->has('limit') ? $request->get('limit') : 50;
                $price_sort_by = $request->sort_by;
                $data = array();
                $data['post'] = array();
                $data['seller_info'] = array();
                $data['category'] = array();
                /* get unexpired payment post */
                $payment_ids_array = TblPost::get_unexpired_payment_post_ids();
                /* get unexpired free post */
                $free_ids_array = TblPost::get_unexpired_free_post_ids();
                $final_result_ids = array_merge($payment_ids_array, $free_ids_array);
                $seller_posts_cnt = TblPost::whereNull('tbl_posts.deleted_at')
                    ->where('tbl_posts.user_id', $id)
                    ->where('tbl_posts.active', 1)
                    ->where('tbl_posts.sold_status', 0)
                    ->whereIn('tbl_posts.id', $final_result_ids)
                    ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')->get();
                // filter by price low to high - asc and high to low - desc   
                if (!empty($price_sort_by) && ($price_sort_by == "asc")) {
                    // $final_result_array = TblPost::whereIn('id', $all_post)->pluck('id');
                    $seller_posts = TblPost::whereNull('tbl_posts.deleted_at')
                        ->where('tbl_posts.user_id', $id)
                        ->where('tbl_posts.active', 1)
                        ->whereIn('tbl_posts.id', $final_result_ids)
                        ->where('tbl_posts.sold_status', 0)
                        ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                        ->orderBy(DB::raw("CAST(`tbl_posts`.`price` AS DECIMAL(18,2))"), 'asc')
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get(['tbl_posts.id as id', 'tbl_posts.giving_away as giving_away', 'tbl_posts.category_id as category_id', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.currency_id as currency_id', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_posts.description as description', 'tbl_cities.name as city_name', 'tbl_cities.latitude as c_lat', 'tbl_cities.logitude as c_lng']);
                } else if (!empty($price_sort_by) && ($price_sort_by == "desc")) {
                    // $final_result_array = TblPost::whereIn('id', $all_post)->orderBy(DB::raw("CAST(`price` AS DECIMAL(18,2))"), 'desc')->pluck('id');
                    $seller_posts = TblPost::whereNull('tbl_posts.deleted_at')
                        ->where('tbl_posts.user_id', $id)
                        ->where('tbl_posts.active', 1)
                        ->whereIn('tbl_posts.id', $final_result_ids)
                        ->where('tbl_posts.sold_status', 0)
                        ->orderBy(DB::raw("CAST(`tbl_posts`.`price` AS DECIMAL(18,2))"), 'desc')
                        ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get(['tbl_posts.id as id', 'tbl_posts.giving_away as giving_away', 'tbl_posts.category_id as category_id', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.currency_id as currency_id', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_posts.description as description', 'tbl_cities.name as city_name', 'tbl_cities.latitude as c_lat', 'tbl_cities.logitude as c_lng']);
                } else if (!empty($price_sort_by) && ($price_sort_by == "popular")) {
                    // based on views 
                    // $final_result_array = TblPost::whereIn('id', $all_post)->orderBy('views_count', 'desc')->pluck('id');
                    $seller_posts = TblPost::whereNull('tbl_posts.deleted_at')
                        ->where('tbl_posts.user_id', $id)
                        ->where('tbl_posts.active', 1)
                        ->whereIn('tbl_posts.id', $final_result_ids)
                        ->where('tbl_posts.sold_status', 0)
                        ->orderBy('tbl_posts.views_count', 'desc')
                        ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get(['tbl_posts.id as id', 'tbl_posts.giving_away as giving_away', 'tbl_posts.category_id as category_id', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.currency_id as currency_id', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_posts.description as description', 'tbl_cities.name as city_name', 'tbl_cities.latitude as c_lat', 'tbl_cities.logitude as c_lng']);
                } else if (!empty($price_sort_by) && ($price_sort_by == "post-desc")) {
                    $seller_posts = TblPost::whereNull('tbl_posts.deleted_at')
                        ->where('tbl_posts.user_id', $id)
                        ->where('tbl_posts.active', 1)
                        ->whereIn('tbl_posts.id', $final_result_ids)
                        ->where('tbl_posts.sold_status', 0)
                        ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                        ->orderBy('tbl_posts.created_at', 'desc')
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get(['tbl_posts.id as id', 'tbl_posts.giving_away as giving_away', 'tbl_posts.category_id as category_id', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.currency_id as currency_id', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_posts.description as description', 'tbl_cities.name as city_name', 'tbl_cities.latitude as c_lat', 'tbl_cities.logitude as c_lng']);
                } else {
                    $seller_posts = TblPost::whereNull('tbl_posts.deleted_at')
                        ->where('tbl_posts.user_id', $id)
                        ->where('tbl_posts.active', 1)
                        ->whereIn('tbl_posts.id', $final_result_ids)
                        ->where('tbl_posts.sold_status', 0)
                        ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get(['tbl_posts.id as id', 'tbl_posts.giving_away as giving_away', 'tbl_posts.category_id as category_id', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.currency_id as currency_id', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_posts.description as description', 'tbl_cities.name as city_name', 'tbl_cities.latitude as c_lat', 'tbl_cities.logitude as c_lng']);
                }
                // category info in seller
                $token = $this->getBearerToken();
                if (!empty($token['code']) && ($token['code'] == 200)) {
                    $user = $this->getLoggedUser($token['token']);
                    if (!empty($user)) {
                        // dd($user);
                        $lan_code = User::where('id', $user)->value('preferred_language');
                    } else {
                        $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                        $response = [
                            'success' => false,
                            'code' => 0,
                            'message' => "Invalid User"
                        ];
                    }
                } else {
                    $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                }
                $data['category'] = TblPost::cat_info($id, $final_result_ids);
                foreach ($seller_posts as $newdata) {
                    $fav = "";
                    if (!empty($user)) {
                        $fav = TblSavedPosts::where('user_id', $user)->where('post_id', $newdata['id'])->get(['id']);
                    }
                    $cat = TblCategory::where('id', $newdata['category_id'])->first();
                    $cat_name = "";
                    if ($cat->parent_id == null) {
                        $cat_name = $cat->title;
                    } else {
                        $cate = TblCategory::where('id', $cat->parent_id)->first();
                        $cat_name = $cate->title;
                    }
                    $currency = $this->post_currency($newdata['id']);
                    $adtype = TblPost::getAddtype($newdata['id']); //
                    $images = TblPost::get_single_post_information($newdata['id']);
                    $additional_data = $this->getAdditionalInfo($newdata['id']);
                    $token = $this->getBearerToken();
                    if (!empty($token['code']) && ($token['code'] == 200)) {
                        $user = $this->getLoggedUser($token['token']);
                        if (!empty($user)) {
                            // dd($user);
                            $lan_code = User::where('id', $user)->value('preferred_language');
                        } else {
                            $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                            $response = [
                                'success' => false,
                                'code' => 0,
                                'message' => "Invalid User"
                            ];
                        }
                    } else {
                        $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                    }
                    $additional_info = array();
                    foreach ($additional_data as $additional_data) {
                        $label = Languages::where('lang_code', $lan_code)->where('lang_org_text', $additional_data['label'])->value('lang_text');
                        $additional_info[] = array(
                            'lable' => !empty($label) ? $label : $additional_data['label'],
                            'value' => $additional_data['value']
                        );
                    }
                    //seller logo  start
                    $seller_logo = '';
                    $seller_brand = '';
                    $seller = TblPost::where('id', $newdata['id'])->value('user_id');
                    $seller_check = Verificationrequest::where('user_id', $seller)->first();
                    if (!empty($seller_check)) {
                        if ($seller_check->is_company == 'yes') {
                            $seller_logo = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_logo');
                            $seller_brand = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_name');
                        }
                    }
                    $brand_logo = !empty($seller_logo) ? URL::to('storage/business-profile/thumb50/' . $seller_logo) : URL::asset('storage/profile-avatar.jpg');
                    //seller logo end
                    Carbon::setLocale($lan_code);
                    $city_name = !empty($newdata['locality']) ? $newdata['locality'] : $newdata['city_name'];
                    $data['post'][] = array(
                        'id' => $newdata['id'],
                        'title' => $newdata['title'],
                        'price' => $newdata['price'],
                        'city_name' => $city_name,
                        'latitude' => $newdata['c_lat'],
                        'longitude' => $newdata['c_lng'],
                        'custom_fields' => $additional_info,
                        'created_at' => \Carbon\Carbon::parse($newdata['created_at'])->isoFormat('DD MMM YYYY'), //date('d M Y', strtotime($newdata['created_at'])),
                        'images' => $images["images"],
                        'is_fav' => !empty($fav) && ($fav->count() > 0) ? true : false,
                        'ad_type' => !empty($adtype) ? Str_replace('_', ' ', strtoupper($adtype->ad_type)) : "",
                        'currency_symbol' => $currency,
                        'category_name' => $cat_name,
                        'sellerId' => $seller,
                        'brand_logo' => $brand_logo,
                        'brand_name' => $seller_brand,
                        'description' => $newdata['description'],
                        'giving_away' => $newdata['giving_away'],
                    );
                }
                $seller_info = User::where('id', $id)->whereNull('deleted_at')->get(['profile_photo_path', 'name', 'id', 'created_at', 'is_blocked'])->first();
                // $seller_mobile = User_profile::where('user_id', $id)->where('show_mobile', 1)->value('phone');
                $seller_mobile = User_profile::where('user_id', $id)->value('phone');
                $user_profile = User_profile::where('user_id', $id)->first();
                $country_code = !empty($user_profile) ? $user_profile->country_code : '';
                $user_ids = TblFollowers::where('seller_id', $id)->where('is_followed', 1)->pluck('user_id');
                $seller_ids = TblFollowers::where('user_id', $id)->where('is_followed', 1)->pluck('seller_id');
                //$followers = User::select("users.*")->whereIn('users.id', $user_ids)->whereNull('users.deleted_at')->get();
                $followers = User::whereIn('id', $user_ids)->get(['id'])->count();
                //$followings = User::select("users.*")->whereIn('users.id', $seller_ids)->whereNull('users.deleted_at')->get();
                $followings = User::whereIn('id', $seller_ids)->get(['id'])->count();
                if (empty($seller_info)) {
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => 'Invalid User'
                    ];
                } else {
                    $profile_url = (!empty($seller_info->profile_photo_path)) ? URL::to('storage/' . $seller_info->profile_photo_path) : URL::asset('storage/profile-avatar.jpg');
                    if ($user == $id) {
                        $enable = 0;
                        $is_followed = 0;
                    } else {
                        $enable = 1;
                        $check = TblFollowers::where('user_id', $user)->where('seller_id', $id)->first();
                        $is_followed = 0;
                        if (!empty($check)) {
                            $is_followed = ($check->is_followed == 1) ? 1 : 0;
                        }
                    }
                    $isbuy = TblBuynowOrder::where('user_id', $user)
                        ->where('seller_id', $id)
                        ->exists();
                    $is_buy = 0;
                    // dd($isbuy,$currentUserId,$profile_id);
                    if ($isbuy == 'true') {
                        $is_buy = 1;
                    }
                    $seller_review_count = TblSellerReviews::where('seller_id', $id)->count();
                    $is_verify_there = Verificationrequest::where('user_id', $seller_info->id)->exists();
                    $verify_id = Verificationrequest::where('user_id', $seller_info->id)->value('id');
                    $has_shop = BusinessProfile::where('verifcation_id', $verify_id)->exists();
                    if ($has_shop == true) {
                        $shop_profile = 'yes';
                    } else {
                        $shop_profile = 'no';
                    }
                    $seller_checking = Verificationrequest::where('user_id', $seller_info->id)->where('is_company', 'yes')->where('is_approved', 1)->exists();
                    $seller_verified = Verificationrequest::where('user_id', $seller_info->id)->where('is_approved', 1)->value('is_approved');
                    $is_decline = Verificationrequest::where('user_id', $seller_info->id)->whereNotNull('decline_reason')->exists();
                    if ($is_verify_there == true) {
                        if ($seller_verified == 1) {
                            $verified_user = 'Approved';
                        } else if ($is_decline == true) {
                            $verified_user = 'Decline';
                        } else {
                            $verified_user = 'Pending';
                        }
                    } else if ($is_decline == true) {
                        $verified_user = 'Decline';
                    } else {
                        $verified_user = 'initiate';
                    }
                    $is_company = 'no';
                    if ($seller_checking == true) {
                        $verification = Verificationrequest::where('user_id', $seller_info->id)->first();
                        $is_company = $verification->is_company;
                        $verication_id = $verification->id;
                        $datas = BusinessProfile::where('verifcation_id', $verication_id)->first();
                        if (!empty($datas)) {
                            $brand_opening = TblChat::timeAgo($datas->created_at, $lan_code);
                            $op_hours = json_decode($datas->hours, true);
                            $shop_id = $datas->id;
                            $latitude = TblCity::where('id', $datas->city)->value('latitude');
                            $longitude = TblCity::where('id', $datas->city)->value('logitude');
                            $logo_url = (!empty($datas->brand_logo)) ? URL::to('storage/business-profile/' . $datas->brand_logo) : URL::asset('storage/profile-avatar.jpg');
                            $data['seller_info'] = array(
                                'followers_count' => $followers,
                                'followings_count' => $followings,
                                'seller_name' => $datas->brand_name,
                                'profile' => $profile_url,
                                'contact_number' => !empty($datas->contact_number) ? $datas->contact_number : "",
                                'country_code' => $country_code,
                                'enable_follow_btn' => $enable,
                                'is_followed' => $is_followed,
                                'total_count' => count($seller_posts_cnt),
                                'seller_id' => $seller_info->id,
                                'is_blocked' => $seller_info->is_blocked,
                                'seller_review_count' => $seller_review_count,
                                'is_company' => $is_company,
                                'shop_profile' => $shop_profile,
                                'shop_id' => $shop_id,
                                'brand_name' => $datas->brand_name,
                                'status' => $seller_info->current_chat_status,
                                'brand_open_on' => $brand_opening,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'brand_about_us' => $datas->about_us,
                                'brand_address' => $datas->address,
                                'brand_logo' => $logo_url,
                                'shop_timing' => $op_hours,
                                'is_user_verify' => $verified_user,
                                'seller_joins' => "Member Since " . \Carbon\Carbon::parse($seller_info->created_at)->isoFormat('MMM YYYY'),
                                'is_buy' => $is_buy,
                            );
                        } else {
                            $data['seller_info'] = array(
                                'followers_count' => $followers,
                                'followings_count' => $followings,
                                'seller_name' => $seller_info->name,
                                'profile' => $profile_url,
                                'contact_number' => !empty($seller_mobile) ? $seller_mobile : "",
                                'country_code' => $country_code,
                                'enable_follow_btn' => $enable,
                                'is_followed' => $is_followed,
                                'is_blocked' => $seller_info->is_blocked,
                                'is_company' => $is_company,
                                'shop_profile' => $shop_profile,
                                'total_count' => count($seller_posts_cnt),
                                'seller_id' => $seller_info->id,
                                'seller_review_count' => $seller_review_count,
                                'is_user_verify' => $verified_user,
                                'seller_joins' => "Member Since " . \Carbon\Carbon::parse($seller_info->created_at)->isoFormat('MMM YYYY'),
                                'is_buy' => $is_buy,
                            );
                        }
                    } else {
                        $data['seller_info'] = array(
                            'followers_count' => $followers,
                            'followings_count' => $followings,
                            'seller_name' => $seller_info->name,
                            'profile' => $profile_url,
                            'contact_number' => !empty($seller_mobile) ? $seller_mobile : "",
                            'country_code' => $country_code,
                            'enable_follow_btn' => $enable,
                            'is_followed' => $is_followed,
                            'is_company' => $is_company,
                            'is_blocked' => $seller_info->is_blocked,
                            'shop_profile' => $shop_profile,
                            'total_count' => count($seller_posts_cnt),
                            'seller_id' => $seller_info->id,
                            'seller_review_count' => $seller_review_count,
                            'is_user_verify' => $verified_user,
                            'seller_joins' => "Member Since " . \Carbon\Carbon::parse($seller_info->created_at)->isoFormat('MMM YYYY'),
                            'is_buy' => $is_buy,
                        );
                    }
                    $response = [
                        'success' => true,
                        'code' => 200,
                        'data' => $data
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            // $response = [
            //     'success' => false,
            //     'code' => 0,
            //     'message' => "Invalid Token"
            // ];
            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('limit') ? $request->get('limit') : 50;
            $price_sort_by = $request->sort_by;
            // dd($price_sort_by);
            $data = array();
            $data['post'] = array();
            $data['seller_info'] = array();
            $data['category'] = array();
            /* get unexpired payment post */
            $payment_ids_array = TblPost::get_unexpired_payment_post_ids();
            /* get unexpired free post */
            $free_ids_array = TblPost::get_unexpired_free_post_ids();
            $final_result_ids = array_merge($payment_ids_array, $free_ids_array);
            $seller_posts_cnt = TblPost::whereNull('tbl_posts.deleted_at')
                ->where('tbl_posts.user_id', $id)
                ->where('tbl_posts.active', 1)
                ->where('tbl_posts.sold_status', 0)
                ->whereIn('tbl_posts.id', $final_result_ids)
                ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')->get();
            // filter by price low to high - asc and high to low - desc   
            if (!empty($price_sort_by) && ($price_sort_by == "asc")) {
                // $final_result_array = TblPost::whereIn('id', $all_post)->pluck('id');
                $seller_posts = TblPost::whereNull('tbl_posts.deleted_at')
                    ->where('tbl_posts.user_id', $id)
                    ->where('tbl_posts.active', 1)
                    ->whereIn('tbl_posts.id', $final_result_ids)
                    ->where('tbl_posts.sold_status', 0)
                    ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                    ->orderBy(DB::raw("CAST(`tbl_posts`.`price` AS DECIMAL(18,2))"), 'asc')
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get(['tbl_posts.id as id', 'tbl_posts.giving_away as giving_away', 'tbl_posts.category_id as category_id', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.currency_id as currency_id', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_posts.description as description', 'tbl_cities.name as city_name', 'tbl_cities.latitude as c_lat', 'tbl_cities.logitude as c_lng']);
            } else if (!empty($price_sort_by) && ($price_sort_by == "desc")) {
                // $final_result_array = TblPost::whereIn('id', $all_post)->->pluck('id');
                $seller_posts = TblPost::whereNull('tbl_posts.deleted_at')
                    ->where('tbl_posts.user_id', $id)
                    ->where('tbl_posts.active', 1)
                    ->whereIn('tbl_posts.id', $final_result_ids)
                    ->where('tbl_posts.sold_status', 0)
                    ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                    ->orderBy(DB::raw("CAST(`tbl_posts`.`price` AS DECIMAL(18,2))"), 'desc')
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get(['tbl_posts.id as id', 'tbl_posts.giving_away as giving_away', 'tbl_posts.category_id as category_id', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.currency_id as currency_id', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_posts.description as description', 'tbl_cities.name as city_name', 'tbl_cities.latitude as c_lat', 'tbl_cities.logitude as c_lng']);
            } else if (!empty($price_sort_by) && ($price_sort_by == "popular")) {
                // based on views
                // $final_result_array = TblPost::whereIn('id', $all_post)->orderBy('views_count', 'desc')->pluck('id');
                $seller_posts = TblPost::whereNull('tbl_posts.deleted_at')
                    ->where('tbl_posts.user_id', $id)
                    ->where('tbl_posts.active', 1)
                    ->whereIn('tbl_posts.id', $final_result_ids)
                    ->where('tbl_posts.sold_status', 0)
                    ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                    ->orderBy('tbl_posts.views_count', 'desc')
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get(['tbl_posts.id as id', 'tbl_posts.giving_away as giving_away', 'tbl_posts.category_id as category_id', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.currency_id as currency_id', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_posts.description as description', 'tbl_cities.name as city_name', 'tbl_cities.latitude as c_lat', 'tbl_cities.logitude as c_lng']);
            } else if (!empty($price_sort_by) && ($price_sort_by == "post-desc")) {
                $seller_posts = TblPost::whereNull('tbl_posts.deleted_at')
                    ->where('tbl_posts.user_id', $id)
                    ->where('tbl_posts.active', 1)
                    ->whereIn('tbl_posts.id', $final_result_ids)
                    ->where('tbl_posts.sold_status', 0)
                    ->orderBy('tbl_posts.created_at', 'desc')
                    ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get(['tbl_posts.id as id', 'tbl_posts.giving_away as giving_away', 'tbl_posts.category_id as category_id', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.currency_id as currency_id', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_posts.description as description', 'tbl_cities.name as city_name', 'tbl_cities.latitude as c_lat', 'tbl_cities.logitude as c_lng']);
            } else {
                $seller_posts = TblPost::whereNull('tbl_posts.deleted_at')
                    ->where('tbl_posts.user_id', $id)
                    ->where('tbl_posts.active', 1)
                    ->whereIn('tbl_posts.id', $final_result_ids)
                    ->where('tbl_posts.sold_status', 0)
                    ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get(['tbl_posts.id as id', 'tbl_posts.giving_away as giving_away', 'tbl_posts.category_id as category_id', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.currency_id as currency_id', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_posts.description as description', 'tbl_cities.name as city_name', 'tbl_cities.latitude as c_lat', 'tbl_cities.logitude as c_lng']);
            }
            // category info in seller
            $data['category'] = TblPost::cat_info($id, $final_result_ids);
            $token = $this->getBearerToken();
            if (!empty($token['code']) && ($token['code'] == 200)) {
                $user = $this->getLoggedUser($token['token']);
                if (!empty($user)) {
                    // dd($user);
                    $lan_code = User::where('id', $user)->value('preferred_language');
                } else {
                    $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => "Invalid User"
                    ];
                }
            } else {
                $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
            }
            foreach ($seller_posts as $newdata) {
                $fav = "";
                if (!empty($user)) {
                    $fav = TblSavedPosts::where('user_id', $user)->where('post_id', $newdata['id'])->get(['id']);
                }
                $cat = TblCategory::where('id', $newdata['category_id'])->first();
                $cat_name = "";
                if ($cat->parent_id == null) {
                    $cat_name = $cat->title;
                } else {
                    $cate = TblCategory::where('id', $cat->parent_id)->first();
                    $cat_name = $cate->title;
                }
                $currency = $this->post_currency($newdata['id']);
                $adtype = TblPost::getAddtype($newdata['id']); //
                $images = TblPost::get_single_post_information($newdata['id']);
                $additional_data = $this->getAdditionalInfo($newdata['id']);
                $token = $this->getBearerToken();
                if (!empty($token['code']) && ($token['code'] == 200)) {
                    $user = $this->getLoggedUser($token['token']);
                    if (!empty($user)) {
                        // dd($user);
                        $lan_code = User::where('id', $user)->value('preferred_language');
                    } else {
                        $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                        $response = [
                            'success' => false,
                            'code' => 0,
                            'message' => "Invalid User"
                        ];
                    }
                } else {
                    $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                }
                $additional_info = array();
                foreach ($additional_data as $additional_data) {
                    $label = Languages::where('lang_code', $lan_code)->where('lang_org_text', $additional_data['label'])->value('lang_text');
                    $additional_info[] = array(
                        'lable' => !empty($label) ? $label : $additional_data['label'],
                        'value' => $additional_data['value']
                    );
                }
                //seller logo  start
                $seller_logo = '';
                $seller_brand = '';
                $seller = TblPost::where('id', $newdata['id'])->value('user_id');
                $seller_check = Verificationrequest::where('user_id', $seller)->first();
                if (!empty($seller_check)) {
                    if ($seller_check->is_company == 'yes') {
                        $seller_logo = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_logo');
                        $seller_brand = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_name');
                    }
                }
                $brand_logo = !empty($seller_logo) ? URL::to('storage/business-profile/thumb50/' . $seller_logo) : URL::asset('storage/profile-avatar.jpg');
                //seller logo end
                $city_name = !empty($newdata['locality']) ? $newdata['locality'] : $newdata['city_name'];
                $data['post'][] = array(
                    'id' => $newdata['id'],
                    'title' => $newdata['title'],
                    'price' => $newdata['price'],
                    'city_name' => $city_name,
                    'latitude' => $newdata['c_lat'],
                    'longitude' => $newdata['c_lng'],
                    'custom_fields' => $additional_info,
                    'created_at' => date('d M Y', strtotime($newdata['created_at'])),
                    'images' => $images["images"],
                    'is_fav' => !empty($fav) && ($fav->count() > 0) ? true : false,
                    'ad_type' => !empty($adtype) ? Str_replace('_', ' ', strtoupper($adtype->ad_type)) : "",
                    'currency_symbol' => $currency,
                    'category_name' => $cat_name,
                    'brand_logo' => $brand_logo,
                    'brand_name' => $seller_brand,
                    'description' => $newdata['description'],
                    'giving_away' => $newdata['giving_away']
                );
            }
            $seller_info = User::where('id', $id)->whereNull('deleted_at')->get(['profile_photo_path', 'name', 'id', 'created_at', 'is_blocked'])->first();
            // $seller_mobile = User_profile::where('user_id', $id)->where('show_mobile', 1)->value('phone');
            $seller_mobile = User_profile::where('user_id', $id)->value('phone');
            $user_ids = TblFollowers::where('seller_id', $id)->where('is_followed', 1)->pluck('user_id');
            $user_profile = User_profile::where('user_id', $id)->first();
            $country_code = !empty($user_profile) ? $user_profile->country_code : '';
            $seller_ids = TblFollowers::where('user_id', $id)->where('is_followed', 1)->pluck('seller_id');
            //$followers = User::select("users.*")->whereIn('users.id', $user_ids)->whereNull('users.deleted_at')->get();
            $followers = User::whereIn('id', $user_ids)->get(['id'])->count();
            //$followings = User::select("users.*")->whereIn('users.id', $seller_ids)->whereNull('users.deleted_at')->get();
            $followings = User::whereIn('id', $seller_ids)->get(['id'])->count();
            if (empty($seller_info)) {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => 'Invalid User'
                ];
            } else {
                $profile_url = (!empty($seller_info->profile_photo_path)) ? URL::to('storage/' . $seller_info->profile_photo_path) : URL::asset('storage/profile-avatar.jpg');
                $is_followed = "";
                $enable = "";
                // if ($user == $id) {
                //     $enable = 0;
                //     $is_followed = 0;
                // } else {
                //     $enable = 1;
                //     $check = TblFollowers::where('user_id', $user)->where('seller_id', $id)->first();
                //     $is_followed = 0;
                //     if (!empty($check)) {
                //         $is_followed = ($check->is_followed == 1) ? 1 : 0;
                //     }
                // }
                $seller_review_count = TblSellerReviews::where('seller_id', $id)->count();
                $is_verify_there = Verificationrequest::where('user_id', $seller_info->id)->exists();
                $verify_id = Verificationrequest::where('user_id', $seller_info->id)->value('id');
                $has_shop = BusinessProfile::where('verifcation_id', $verify_id)->exists();
                if ($has_shop == true) {
                    $shop_profile = 'yes';
                } else {
                    $shop_profile = 'no';
                }
                $seller_checking = Verificationrequest::where('user_id', $seller_info->id)->where('is_company', 'yes')->where('is_approved', 1)->exists();
                $seller_verified = Verificationrequest::where('user_id', $seller_info->id)->where('is_approved', 1)->value('is_approved');
                $is_decline = Verificationrequest::where('user_id', $seller_info->id)->whereNotNull('decline_reason')->exists();
                if ($is_verify_there == true) {
                    if ($seller_verified == 1) {
                        $verified_user = 'Approved';
                    } else if ($is_decline == true) {
                        $verified_user = 'Decline';
                    } else {
                        $verified_user = 'Pending';
                    }
                } else if ($is_decline == true) {
                    $verified_user = 'Decline';
                } else {
                    $verified_user = 'initiate';
                }
                $is_company = 'no';
                if ($seller_checking == true) {
                    $verification = Verificationrequest::where('user_id', $seller_info->id)->first();
                    $is_company = $verification->is_company;
                    $verication_id = $verification->id;
                    $datas = BusinessProfile::where('verifcation_id', $verication_id)->first();
                    if (!empty($datas)) {
                        $brand_opening = TblChat::timeAgo($datas->created_at, $lan_code);
                        $op_hours = json_decode($datas->hours, true);
                        $shop_id = $datas->id;
                        $latitude = TblCity::where('id', $datas->city)->value('latitude');
                        $longitude = TblCity::where('id', $datas->city)->value('logitude');
                        $logo_url = (!empty($datas->brand_logo)) ? URL::to('storage/business-profile/' . $datas->brand_logo) : URL::asset('storage/profile-avatar.jpg');
                        $data['seller_info'] = array(
                            'followers_count' => $followers,
                            'followings_count' => $followings,
                            'seller_name' => $datas->brand_name,
                            'profile' => $profile_url,
                            'contact_number' => !empty($datas->contact_number) ? $datas->contact_number : "",
                            'country_code' => $country_code,
                            'enable_follow_btn' => $enable,
                            'is_followed' => $is_followed,
                            'is_blocked' => $seller_info->is_blocked,
                            'total_count' => count($seller_posts_cnt),
                            'seller_id' => $seller_info->id,
                            'seller_review_count' => $seller_review_count,
                            'is_company' => $is_company,
                            'shop_profile' => $shop_profile,
                            'shop_id' => $shop_id,
                            'brand_name' => $datas->brand_name,
                            'status' => $seller_info->current_chat_status,
                            'brand_open_on' => $brand_opening,
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'brand_about_us' => $datas->about_us,
                            'brand_address' => $datas->address,
                            'brand_logo' => $logo_url,
                            'shop_timing' => $op_hours,
                            'is_user_verify' => $verified_user,
                            'seller_joins' => "Member Since " . \Carbon\Carbon::parse($seller_info->created_at)->isoFormat('MMM YYYY'),
                            'is_buy' => 0,
                        );
                    } else {
                        $data['seller_info'] = array(
                            'followers_count' => $followers,
                            'followings_count' => $followings,
                            'seller_name' => $seller_info->name,
                            'profile' => $profile_url,
                            'contact_number' => !empty($seller_mobile) ? $seller_mobile : "",
                            'country_code' => $country_code,
                            'enable_follow_btn' => $enable,
                            'is_followed' => $is_followed,
                            'is_blocked' => $seller_info->is_blocked,
                            'total_count' => count($seller_posts_cnt),
                            'seller_id' => $seller_info->id,
                            'is_company' => $is_company,
                            'shop_profile' => $shop_profile,
                            'seller_review_count' => $seller_review_count,
                            'is_user_verify' => $verified_user,
                            'seller_joins' => "Member Since " . \Carbon\Carbon::parse($seller_info->created_at)->isoFormat('MMM YYYY'),
                            'is_buy' => 0,
                        );
                    }
                } else {
                    $data['seller_info'] = array(
                        'followers_count' => $followers,
                        'followings_count' => $followings,
                        'seller_name' => $seller_info->name,
                        'profile' => $profile_url,
                        'contact_number' => !empty($seller_mobile) ? $seller_mobile : "",
                        'country_code' => $country_code,
                        'enable_follow_btn' => $enable,
                        'is_followed' => !empty($is_followed) ? $is_followed : 0,
                        'is_blocked' => $seller_info->is_blocked,
                        'total_count' => count($seller_posts_cnt),
                        'seller_id' => $seller_info->id,
                        'is_company' => $is_company,
                        'shop_profile' => $shop_profile,
                        'seller_review_count' => $seller_review_count,
                        'is_user_verify' => $verified_user,
                        'seller_joins' => "Member Since " . \Carbon\Carbon::parse($seller_info->created_at)->isoFormat('MMM YYYY'),
                        'is_buy' => 0,
                    );
                }
            }
            $response = [
                'success' => true,
                'code' => 200,
                'data' => $data
            ];
        }
        return response()->json($response);
    }
    //             $data['post'] = array();
    //             $data['seller_info'] = array();
    //             /* get unexpired payment post */
    //             $payment_ids_array = TblPost::get_unexpired_payment_post_ids();
    //             /* get unexpired free post */
    //             $free_ids_array = TblPost::get_unexpired_free_post_ids();
    //             $final_result_ids = array_merge($payment_ids_array, $free_ids_array);
    //             $seller_posts_cnt = TblPost::whereNull('tbl_posts.deleted_at')
    //                 ->where('tbl_posts.user_id', $id)
    //                 ->where('tbl_posts.active', 1)
    //                 ->where('tbl_posts.sold_status', 0)
    //                 ->whereIn('tbl_posts.id', $final_result_ids)
    //                 ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')->get();
    //             $seller_posts = TblPost::whereNull('tbl_posts.deleted_at')
    //                 ->where('tbl_posts.user_id', $id)
    //                 ->where('tbl_posts.active', 1)
    //                 ->whereIn('tbl_posts.id', $final_result_ids)
    //                 ->where('tbl_posts.sold_status', 0)
    //                 ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
    //                 ->limit($limit)->offset(($page - 1) * $limit)
    //                 ->get(['tbl_posts.id as id', 'tbl_posts.giving_away as giving_away', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.currency_id as currency_id', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_cities.name as city_name', 'tbl_cities.latitude as c_lat', 'tbl_cities.logitude as c_lng']);
    //             foreach ($seller_posts as $newdata) {
    //                 $fav = "";
    //                 if (!empty($user)) {
    //                     $fav = TblSavedPosts::where('user_id', $user)->where('post_id', $newdata['id'])->get(['id']);
    //                 }
    //                 $currency = $this->post_currency($newdata['id']);
    //                 $adtype = TblPost::getAddtype($newdata['id']); //
    //                 $images = TblPost::get_single_post_information($newdata['id']);
    //                 $city_name = !empty($newdata['locality']) ? $newdata['locality'] : $newdata['city_name'];
    //                 $data['post'][] = array(
    //                     'id' => $newdata['id'],
    //                     'title' => $newdata['title'],
    //                     'price' => $newdata['price'],
    //                     'city_name' => $city_name,
    //                     'latitude' => $newdata['c_lat'],
    //                     'longitude' => $newdata['c_lng'],
    //                     'created_at' => date('d M Y', strtotime($newdata['created_at'])),
    //                     'images' => $images["images"],
    //                     'is_fav' => !empty($fav) && ($fav->count() > 0) ? true : false,
    //                     'ad_type' => !empty($adtype) ? Str_replace('_', ' ', strtoupper($adtype->ad_type)) : "",
    //                     'currency_symbol' => $currency,
    //                     'giving_away' => $newdata['giving_away']
    //                 );
    //             }
    //             $seller_info = User::where('id', $id)->whereNull('deleted_at')->get(['profile_photo_path', 'name', 'id'])->first();
    //             $user_ids = TblFollowers::where('seller_id', $id)->where('is_followed', 1)->pluck('user_id');
    //             $seller_ids = TblFollowers::where('user_id', $id)->where('is_followed', 1)->pluck('seller_id');
    //             //$followers = User::select("users.*")->whereIn('users.id', $user_ids)->whereNull('users.deleted_at')->get();
    //             $followers = User::whereIn('id', $user_ids)->get(['id'])->count();
    //             //$followings = User::select("users.*")->whereIn('users.id', $seller_ids)->whereNull('users.deleted_at')->get();
    //             $followings = User::whereIn('id', $seller_ids)->get(['id'])->count();
    //             if (empty($seller_info)) {
    //                 $response = [
    //                     'success' => false,
    //                     'code' => 0,
    //                     'message' => 'Invalid User'
    //                 ];
    //             } else {
    //                 $profile_url = (!empty($seller_info->profile_photo_path)) ? URL::to('storage/' . $seller_info->profile_photo_path) : URL::asset('storage/profile-avatar.jpg');
    //                 if ($user == $id) {
    //                     $enable = 0;
    //                     $is_followed = 0;
    //                 } else {
    //                     $enable = 1;
    //                     $check = TblFollowers::where('user_id', $user)->where('seller_id', $id)->first();
    //                     $is_followed = 0;
    //                     if (!empty($check)) {
    //                         $is_followed = ($check->is_followed == 1) ? 1 : 0;
    //                     }
    //                 }
    //                 $seller_review_count = TblSellerReviews::where('seller_id', $id)->count();
    //                 $data['seller_info'] = array(
    //                     'followers_count' => $followers,
    //                     'followings_count' => $followings,
    //                     'seller_name' => $seller_info->name,
    //                     'profile' => $profile_url,
    //                     'enable_follow_btn' => $enable,
    //                     'is_followed' => $is_followed,
    //                     'total_count' => count($seller_posts_cnt),
    //                     'seller_id' => $seller_info->id,
    //                     'seller_review_count' => $seller_review_count
    //                 );
    //                 $response = [
    //                     'success' => true,
    //                     'code' => 200,
    //                     'data' => $data
    //                 ];
    //             }
    //         } else {
    //             $response = [
    //                 'success' => false,
    //                 'code' => 0,
    //                 'message' => "Invalid User"
    //             ];
    //         }
    //     } else {
    //         $response = [
    //             'success' => false,
    //             'code' => 0,
    //             'message' => "Invalid Token"
    //         ];
    //     }
    //     return response()->json($response);
    // }
    public function add_to_follower(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $request->validate([
                    'seller_id' => 'required',
                ]);
                $check = TblFollowers::where('user_id', $user)->where('seller_id', $request->seller_id)->first();
                if ($user == $request->seller_id) {
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => "You can't follow yourself!"
                    ];
                } else {
                    if (!empty($check)) {
                        if ($check->is_followed == 1) {
                            TblFollowers::where('id', $check->id)->update(array('is_followed' => 0));
                            $response = [
                                'success' => true,
                                'code' => 200,
                                'message' => "Unfollowed successfully!",
                                'is_followed' => 0
                            ];
                        } else {
                            TblFollowers::where('id', $check->id)->update(array('is_followed' => 1));
                            $response = [
                                'success' => true,
                                'code' => 200,
                                'message' => "Now you are following the seller!!",
                                'is_followed' => 1
                            ];
                            try {
                                $settings = Setting::get_logos();
                                $site_name = $settings['name'];
                                $get_user_info = User::where('id', $user)->first();
                                $get_seller_info = User::where('id', $request->seller_id)->first();
                                //$get_post_info = TblPost::where('id', $post_id)->first();
                                $headers = array(
                                    'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                                    'Content-Type: application/json',
                                );
                                $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                                $message1 = array("notifydata" => array('to_id' => $request->seller_id, 'from_id' => $user, 'message' => $get_user_info->name . " Following you.!", 'notify_from' => 'following', 'notify_title' => "New Following In " . $site_name . " !..", 'post_id' => "", 'slug' => ""));
                                TblPost::send_push_notification($fcmid, $message1);
                                $slug = URL::to('seller-profile/' . $user);
                                $mail_data = array("send_maildata" => array('to_id' => $request->seller_id, 'message' => $get_user_info->name . " Following you.!", 'subject' => "New Following In " . $site_name . " !..", 'ad_url' => $slug));
                                $mail_key = "new_follower";
                                Setting::notification_mail($mail_data, $mail_key);
                            } catch (\Exception $e) {
                                \Log::error("Follow notification failed: " . $e->getMessage());
                            }
                            // notification end
                        }
                    } else {
                        TblFollowers::create([
                            'user_id' => $user,
                            'seller_id' => $request->seller_id,
                            'is_followed' => 1
                        ]);
                        try {
                            // notification start
                            $settings = Setting::get_logos();
                            $site_name = $settings['name'];
                            $get_user_info = User::where('id', $user)->first();
                            $get_seller_info = User::where('id', $request->seller_id)->first();
                            //$get_post_info = TblPost::where('id', $post_id)->first();
                            $headers = array(
                                'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                                'Content-Type: application/json',
                            );
                            $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                            $message1 = array("notifydata" => array('to_id' => $request->seller_id, 'from_id' => $user, 'message' => $get_user_info->name . " Following you.!", 'notify_from' => 'following', 'notify_title' => "New Following In " . $site_name . " !..", 'post_id' => "", 'slug' => ""));
                            TblPost::send_push_notification($fcmid, $message1);
                            $slug = URL::to('seller-profile/' . $user);
                            $mail_data = array("send_maildata" => array('to_id' => $request->seller_id, 'message' => $get_user_info->name . " Following you.!", 'subject' => "New Following In " . $site_name . " !..", 'ad_url' => $slug));
                            $mail_key = "new_follower";
                            Setting::notification_mail($mail_data, $mail_key);
                            // notification end
                        } catch (\Exception $e) {
                            \Log::error("Follow notification failed: " . $e->getMessage());
                        }
                        $response = [
                            'success' => true,
                            'code' => 200,
                            'message' => "Now you are following the seller!!",
                            'is_followed' => 1
                        ];
                    }
                }
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    /* logged in user friends*/
    public function my_friends(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $data = array();
                $user_ids = TblFollowers::where('seller_id', $user)->where('is_followed', 1)->pluck('user_id');
                $followers = User::select("users.*")
                    ->whereIn('users.id', $user_ids)
                    ->whereNull('users.deleted_at')->get();
                $seller_ids = TblFollowers::where('user_id', $user)->where('is_followed', 1)->pluck('seller_id');
                $followings = User::select("users.*")
                    ->whereIn('users.id', $seller_ids)
                    ->whereNull('users.deleted_at')->get();
                $data['followers_count'] = count($followers);
                $data['followings_count'] = count($followings);
                $data['followers'] = array();
                foreach ($followers as $follower) {
                    if (!empty($follower->profile_photo_path)) {
                        $follwer_profile = URL::to('storage/' . $follower->profile_photo_path);
                    } else {
                        $follwer_profile = URL::asset('storage/profile-avatar.jpg');
                    }
                    $data['followers'][] = array(
                        'id' => $follower->id,
                        'name' => $follower->name,
                        'profile' => $follwer_profile
                    );
                }
                $data['followings'] = array();
                foreach ($followings as $following) {
                    if (!empty($following->profile_photo_path)) {
                        $following_profile = URL::to('storage/' . $following->profile_photo_path);
                    } else {
                        $following_profile = URL::asset('storage/profile-avatar.jpg');
                    }
                    $data['followings'][] = array(
                        'id' => $following->id,
                        'name' => $following->name,
                        'profile' => $following_profile
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    /* Seller Friends */
    public function seller_friends(Request $request, $id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $data = array();
                $user_ids = TblFollowers::where('seller_id', $id)->where('is_followed', 1)->pluck('user_id');
                $followers = User::select("users.*")
                    ->whereIn('users.id', $user_ids)
                    ->whereNull('users.deleted_at')->get();
                $seller_ids = TblFollowers::where('user_id', $id)->where('is_followed', 1)->pluck('seller_id');
                $followings = User::select("users.*")
                    ->whereIn('users.id', $seller_ids)
                    ->whereNull('users.deleted_at')->get();
                $data['followers_count'] = count($followers);
                $data['followings_count'] = count($followings);
                $data['followers'] = array();
                foreach ($followers as $follower) {
                    if (!empty($follower->profile_photo_path)) {
                        $follwer_profile = URL::to('storage/' . $follower->profile_photo_path);
                    } else {
                        $follwer_profile = URL::asset('storage/profile-avatar.jpg');
                    }
                    $data['followers'][] = array(
                        'id' => $follower->id,
                        'name' => $follower->name,
                        'profile' => $follwer_profile
                    );
                }
                $data['followings'] = array();
                foreach ($followings as $following) {
                    if (!empty($following->profile_photo_path)) {
                        $following_profile = URL::to('storage/' . $following->profile_photo_path);
                    } else {
                        $following_profile = URL::asset('storage/profile-avatar.jpg');
                    }
                    $data['followings'][] = array(
                        'id' => $following->id,
                        'name' => $following->name,
                        'profile' => $following_profile
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    public function invite_friends(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $user_id = $user;
                $check = User::where('email', $request->email_id)->whereNull('deleted_at')->first();
                $get_user = User::where('id', $user_id)->whereNull('deleted_at')->first();
                if (!empty($check)) {
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => "This email ID already registered!"
                    ];
                } else {
                    $check_invite = TblInvitedFriends::where('email', $request->email_id)->where('user_id', $user_id)->whereNull('deleted_at')->first();
                    if (empty($check_invite)) {
                        //send mail start
                        $settings = Setting::get_logos();
                        $site_name = $settings['name'];
                        $slug = URL::to('/');
                        $mail_data = array("send_maildata" => array('to_id' => $request->email_id, 'message' => $get_user->name . ' Invite to you. Click below link and register now.!', 'subject' => "New Friend Invitation In " . $site_name . " !..", 'ad_url' => $slug));
                        $mail_key = "invite_friend";
                        Setting::notification_mail($mail_data, $mail_key);
                        // send mail end
                        TblInvitedFriends::create([
                            'user_id' => $user_id,
                            'email' => $request->email_id,
                        ]);
                        $response = [
                            'success' => true,
                            'code' => 200,
                            'message' => "Invitation mail sent successfully!"
                        ];
                    } else {
                        $response = [
                            'success' => false,
                            'code' => 0,
                            'message' => "Already you are invited this email ID!"
                        ];
                    }
                }
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    /* Notification list */
    public function notification_list(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $data = array();
                $notifications = TblNotifications::Where('to_id', $user)->orderBy('created_at', 'desc')->whereNull('deleted_at')->get();
                $data['notifications_count'] = $notifications->where('read_status', 0)->count();
                $data['notifications'] = array();
                foreach ($notifications as $notification) {
                    $images = TblPost::where('id', $notification->post_id)->pluck('images')->first();
                    if (!empty($images)) {
                        $imgUrl = explode(',', $images)[0];
                        $imgName = str_replace("adpost/predefined/", '', $imgUrl);
                        $is_file = base_path() . '/storage/app/public/adpost/applist/' . $imgName;
                        $post_img = (is_file($is_file)) ? URL::to('storage/adpost/applist/' . $imgName) : URL::to('storage/' . $imgUrl);
                    } else {
                        $post_img = URL::to('storage/noimage150.png');
                    }
                    $seller_name = "";
                    $seller_profile = "";
                    if ($notification->notify_from == 'chat') {
                        $seller_detail = User::where('id', $notification->from_id)->get();
                        foreach ($seller_detail as $seller) {
                            $seller_name = $seller->name;
                            // dd($seller_name);
                            $seller_profile = !empty($seller->profile_photo_path) ? URL::to('/public/storage/' . $seller->profile_photo_path) : URL::to('storage/profile-avatar.jpg');
                        }
                    } else {
                        $seller_name = "";
                        $seller_profile = "";
                    }
                    if ((str_contains($notification->msg, 'post name'))) {
                        $message = explode('post name', $notification->msg);
                        $msg_title = $message[0];
                        $msg_description = str_replace(' -', 'post name -', $message[1]);
                    } else if ((str_contains($notification->msg, 'Post Name'))) {
                        $message = explode('Post Name', $notification->msg);
                        $msg_title = $message[0];
                        $msg_description = str_replace(' -', 'Post Name -', $message[1]);
                    } else if ((str_contains($notification->msg, '-'))) {
                        $message = explode('-', $notification->msg);
                        $msg_title = $message[0];
                        $msg_description = str_replace(' -', '', $message[1]);
                    } else {
                        $msg_title = "";
                        $msg_description = $notification->msg;
                    }
                    $data['notifications'][] = array(
                        'id' => $notification->id,
                        'msg' => $notification->msg,
                        'msg_title' => $msg_title,
                        'msg_description' => $msg_description,
                        'post_id' => $notification->post_id,
                        'from_id' => $notification->from_id,
                        'to_id' => $notification->to_id,
                        'post_image' => $post_img,
                        'notify_from' => $notification->notify_from,
                        'created_at' => date('d-m-y h:i a', strtotime($notification->created_at)),
                        'read_status' => $notification->read_status,
                        'seller_name' => $seller_name,
                        'seller_image' => $seller_profile,
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    public function chat_read_count()
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                /* Notifiations count and list */
                $notifications = TblNotifications::where('to_id', $user)->where('read_status', 0)->get();
                // chat count
                $userid = $user;
                $chatlists = TblChat::where('tbl_chats.from_id', $userid)
                    ->join('tbl_posts', function ($join) {
                        $join->on('tbl_chats.post_id', '=', 'tbl_posts.id')
                            ->whereNull('tbl_posts.deleted_at')
                            ->where('tbl_posts.sold_status', 0);
                    })
                    ->orWhere('tbl_chats.to_id', $userid)
                    ->whereNotNull('tbl_chats.msg')
                    ->whereNull('tbl_chats.deleted_at')
                    ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
                    ->orderBy('tbl_chats.created_at', 'desc')
                    ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);
                $total_unread_count = 0;
                foreach ($chatlists as $chatlist) {
                    $visible_posts = TblPost::check_payment_pack_expired($chatlist->post_id);
                    if (!empty($visible_posts)) {
                        $sender = (($userid == $chatlist->from_id) ? $chatlist->to_id : $chatlist->from_id);
                        $unread_count = TblChat::getUnreadCount($userid, $sender, $chatlist->post_id);
                        $total_unread_count += $unread_count;
                    }
                }
                $data = $total_unread_count;
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    public function fcmid_update(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                if (!empty($request->fcmid)) {
                    User::where('id', $user)->update(array('fcmid' => $request->fcmid));
                    $response = [
                        'success' => true,
                        'code' => 200,
                        'message' => "FCMID updated successfully!"
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => "Can't get fcmid"
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    public function notification_read_status(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                if (!empty($request->notification_id)) {
                    TblNotifications::where('id', $request->notification_id)->update(array('read_status' => 1));
                    $response = [
                        'success' => true,
                        'code' => 200,
                        'message' => "Updated successfully!"
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => "Can't get notification id"
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    public function google_login(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'google_id' => 'required'
        ]);
        if (empty($request->email)) {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Email Id missing"
            ];
        } else {
            $finduser = User::where('email', $request->email)->first();
            if (!empty($finduser)) {
                $token = $finduser->createToken('Classified')->plainTextToken;
                User::where('id', $finduser->id)->update(array('api_token' => Str::substr($token, 2), 'google_id' => $request->google_id, ));
                $success['token'] = Str::substr($token, 2);
                $success['name'] = $finduser->name;
                $success['is_blocked'] = $finduser->is_blocked;
                $success['user_id'] = $finduser->id;
                if ($finduser->is_blocked == 1) {
                    $message = "Your account has beed deactived. If you want to activate please contact admin!";
                } else {
                    $message = "Loggedin successfully!";
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => $message,
                    'data' => $success
                ];
            } else {
                // $check_user = User::Where('email', $request->email)->whereNull('deleted_at')->first();
                // if (empty($check_user)) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'google_id' => $request->google_id,
                    'password' => encrypt('123456789')
                ]);
                $role = "User";
                $user->assignRole([$role]);
                User_profile::create([
                    'user_id' => $user->id,
                ]);
                $token = $user->createToken('Classified')->plainTextToken;
                User::where('id', $user->id)->update(array('api_token' => Str::substr($token, 2)));
                $success['token'] = Str::substr($token, 2);
                $success['name'] = $user->name;
                $success['is_blocked'] = $user->is_blocked;
                $success['user_id'] = $user->id;
                if ($user->is_blocked == 1) {
                    $message = "Your account has beed deactived. If you want to activate please contact admin!";
                } else {
                    $message = "Loggedin successfully!";
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => $message,
                    'data' => $success
                ];
                // } else {
                // $response = [
                // 'success' => false,
                // 'code' => 0,
                // 'message' => "Email id already exist!"
                // ];
                // }
            }
        }
        return response()->json($response);
    }
    public function facebook_login(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'facebook_id' => 'required'
        ]);
        if (empty($request->email)) {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Please update Email address to your facebook account, otherwise you cannot login.."
            ];
        } else {
            $finduser = User::where('email', $request->email)->first();
            if (!empty($finduser)) {
                $token = $finduser->createToken('Classified')->plainTextToken;
                User::where('id', $finduser->id)->update(array('api_token' => Str::substr($token, 2)));
                User::where('id', $finduser->id)->update(array('is_fb_login' => 1, 'facebook_id' => $request->facebook_id, ));
                $success['token'] = Str::substr($token, 2);
                $success['name'] = $finduser->name;
                $success['is_blocked'] = $finduser->is_blocked;
                $success['user_id'] = $finduser->id;
                if ($finduser->is_blocked == 1) {
                    $message = "Your account has beed deactived. If you want to activate please contact admin!";
                } else {
                    $message = "Loggedin successfully!";
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => $message,
                    'data' => $success
                ];
            } else {
                // $check_user = User::Where('email', $request->email)->whereNull('deleted_at')->count();
                // if ($check_user == 0) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'facebook_id' => $request->facebook_id,
                    'is_fb_login' => 1,
                    'password' => encrypt('123456789')
                ]);
                $role = "User";
                $user->assignRole([$role]);
                User_profile::create([
                    'user_id' => $user->id,
                ]);
                $token = $user->createToken('Classified')->plainTextToken;
                User::where('id', $user->id)->update(array('api_token' => Str::substr($token, 2)));
                $success['token'] = Str::substr($token, 2);
                $success['name'] = $user->name;
                $success['is_blocked'] = $user->is_blocked;
                $success['user_id'] = $user->id;
                if ($user->is_blocked == 1) {
                    $message = "Your account has beed deactived. If you want to activate please contact admin!";
                } else {
                    $message = "Loggedin successfully!";
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => $message,
                    'data' => $success
                ];
                // } else {
                // $response = [
                // 'success' => false,
                // 'code' => 0,
                // 'message' => "Facebook email already used in this app. Kindly check and login"
                // ];
                // }
            }
        }
        return response()->json($response);
    }
    public function user_blocked(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $block_user = $request->blocked;
                $user_id = $user;
                $user1 = User::find($user_id);
                $user1->update([
                    'is_blocked' => $block_user,
                ]);
                if ($block_user == 1) {
                    $data = array(
                        'message' => 'User Blocked successfully..'
                    );
                    $slug = url('/');
                    $mail_data = array("send_maildata" => array('to_id' => $user_id, 'message' => "Thank you for join with us. Your account deleted successfully. Contact us if you want to  activate your account.", 'subject' => "Account Deleted", 'ad_url' => $slug));
                    $mail_key = "account_deleted";
                    Setting::notification_mail($mail_data, $mail_key);
                } else {
                    $data = array(
                        'message' => 'User Un-Blocked successfully..'
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
                // code here
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }

    public function get_package_list(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $packagesList = Package::get_active_packages();
                if (!empty($packagesList)) {
                    $data = array();
                    $data['package_list'] = array();
                    foreach ($packagesList as $packagesList) {
                        $pack_id = $packagesList->id;
                        $pack_name = $packagesList->name;
                        $pack_price = $packagesList->price;
                        if ($packagesList->lft == 1) {
                            $pack_duration = $packagesList->duration;
                            $pack_single_pack_limit = $packagesList->single_pack_limit;
                        } else {
                            $pack_duration = $packagesList->duration;
                            $pack_single_pack_limit = 1;
                        }
                        $data['package_list'][] = array(
                            'package_id' => $pack_id,
                            'package_name' => $pack_name,
                            'package_price' => $pack_price,
                            'package_duration' => $pack_duration,
                            'package_single_pack_limit' => $pack_single_pack_limit,
                        );
                    }
                }
                // end
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
                // code here
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    public function contact_us(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'description' => 'required'
        ]);
        $imageName = "";
        if ($request->attachment != null) {
            $extension = explode('/', explode(':', substr($request->attachment, 0, strpos($request->attachment, ';')))[1])[1];
            $imageName = 'contact-us/' . Str::random(15) . '.' . $extension;
            $replace = substr($request->attachment, 0, strpos($request->attachment, ',') + 1);
            $img = str_replace($replace, '', $request->attachment);
            $k = str_replace(' ', '+', $img);
            Storage::disk('public')->put($imageName, base64_decode($k));
        }
        $title = ($request->title == null) ? "" : $request->title;
        $phonenum = ($request->phone == null) ? "" : $request->phone;
        $last_id = TblContactUs::create([
            'name' => $request->name,
            'email' => $request->email,
            'description' => $request->description,
            'phone' => $phonenum,
            'ad_link' => "",
            'title' => $title,
            'attachment' => $imageName
        ]);
        // Mail::send(
        //     'livewire.contact-us.mail-content',
        //     array(
        //         'name' => $request->name,
        //         'email' => $request->email,
        //         'phone' => $phonenum,
        //         'description' => $request->description,
        //     ),
        //     function ($message) use ($request) {
        //         $settings = Setting::where('key', 'app')->get()[0];
        //         $admin_mail = json_decode($settings->value);
        //         $message->from($admin_mail->smtp_mail_username, 'Admin');
        //         $message->to($admin_mail->email)->subject('Your Site Contect Form');
        //     }
        // );
        $mail_data = array("send_maildata" => array('name' => $request->name, 'email' => $request->email, 'phone' => $phonenum, 'description' => $request->description, 'ad_link' => $title));
        $mail_key = "contact_us";
        Setting::notification_mail($mail_data, $mail_key);
        $response = [
            'success' => true,
            'code' => 200,
            'message' => "Form submitted Successfully.!"
        ];
        return response()->json($response);
    }
    public function all_package_list(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                /* packages list for filter */
                $packages_list = Package::where('active', '1')
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'asc')
                    ->get();
                if (!empty($packages_list)) {
                    $data = array();
                    $data['package_list'] = array();
                    foreach ($packages_list as $packages_list) {
                        $package_id = $packages_list->id;
                        $package_name = $packages_list->name;
                        $data['package_list'][] = array(
                            'package_id' => $package_id,
                            'package_name' => $package_name,
                            'label' => $package_name,
                            'value' => $package_id
                        );
                    }
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    public function get_base_url()
    {
        $response = [
            'success' => true,
            'code' => 200,
            'data' => URL::to('/')
        ];
        return response()->json($response);
    }
    public function fb_connect(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $finduser = User::where('id', $user)->first();
                $user_profile = User_profile::where('user_id', $user)->first();
                if (!empty($request->facebook_id)) {
                    $finduser->update([
                        'facebook_id' => $request->facebook_id
                    ]);
                }
                if (!empty($request->profile)) {
                    $extension = explode('/', explode(':', substr($request->profile, 0, strpos($request->profile, ';')))[1])[1];
                    $imageName = 'profile-photos/' . Str::random(15) . '.' . $extension;
                    $replace = substr($request->profile, 0, strpos($request->profile, ',') + 1);
                    $img = str_replace($replace, '', $request->profile);
                    $k = str_replace(' ', '+', $img);
                    Storage::disk('public')->put($imageName, base64_decode($k));
                    $finduser->update([
                        'profile_photo_path' => $imageName
                    ]);
                }
                if (!empty($request->phone)) {
                    $check_phone = User_profile::where('phone', $request->phone)->where('user_id', '!=', $user)->pluck('user_id')->first();
                    if (!empty($check_phone)) {
                        $get_ph_user = User::where('id', $check_phone)->whereNull('deleted_at')->first();
                        if (empty($get_ph_user)) {
                            $user_profile->update([
                                'phone' => $request->phone
                            ]);
                            $finduser->update([
                                'phone' => $request->phone
                            ]);
                        }
                    } else {
                        $user_profile->update([
                            'phone' => $request->phone
                        ]);
                        $finduser->update([
                            'phone' => $request->phone
                        ]);
                    }
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => "Connected successfully"
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    public function fb_disconnect(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $finduser = User::where('id', $user)->first();
                if ($finduser->is_fb_login == 1) {
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => "You are now logged in with facebook. so you can't disconnect your account!"
                    ];
                } else {
                    $finduser->update([
                        'facebook_id' => null
                    ]);
                    $response = [
                        'success' => true,
                        'code' => 200,
                        'message' => "Disconnected successfully"
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    public function home_banners()
    {
        $map_products = [];
        $settings = Setting::where('key', 'home_banner_map')->get()->toArray();
        $json_data = json_decode($settings[0]['value']);
        $cover_distance = $json_data->cover_max_distance_km;
        //SHOW BANNER MAP START
        $enable_banner_map = "0";
        $settings = Setting::where('key', 'home_banner_map')->get()->toArray();
        if (count($settings) > 0) {
            $json_data = json_decode($settings[0]['value']);
            $enable_banner_map = $json_data->enable_map;
        }
        //SHOW BANNER MAP END
        //enable / disable banners start
        $banner_map_is_enable = false;
        $settings = Setting::where('key', 'homepage_banner_type')->get()->toArray();
        if (count($settings) > 0) {
            $json_data = json_decode($settings[0]['value']);
            // $banner_map_is_enable = ($json_data->banner_type=="1" && $enable_banner_map=="1")?true:false;
            $banner_map_is_enable = (($json_data->banner_type == "1" || $json_data->banner_type == "3") && $enable_banner_map == "1") ? true : false;
            //get country detail begin
            $lat = (isset(request()->lat) && strlen(request()->lat) > 0) ? request()->lat : "";
            $lon = (isset(request()->lon) && strlen(request()->lon) > 0) ? request()->lon : "";
            $dist = $cover_distance;
            $state = (isset(request()->current_state) && strlen(request()->current_state) > 0) ? request()->current_state : "";
            $city = (isset(request()->current_cityname) && strlen(request()->current_cityname) > 0) ? request()->current_cityname : "";
            //get country detail end
            $map_products = [];
            if ($lat != "" && $lon != "" && $state != "" && $city != "") {
                $map_products = $this->km_ads_from_cur_dist($lat, $lon, $dist, $state, $city);
            }
            // else{
            //     $lat    = -33.8688197;//munichalai
            //     $lon    = 151.2092955;
            //     $dist = 100; // Km
            //     $state = 'New South Wales';
            //     $city = 'Sydney NSW Australia';
            //     $map_products = $this->km_ads_from_cur_dist($lat, $lon, $dist, $state, $city);
            // }
        }
        //enable / disable banners end
        $banners = TblBanners::whereNull('deleted_at')->orderBy('id', 'desc')->limit(5)->get(['banner_url', 'images', 'content']);
        $current_date = date('Y-m-d');
        $paid_banners = TblBannerAdvertisement::where('page', 'home')->where('status', 'approved')->where('active', 1)->where('end_date', '>=', $current_date)->whereNull('deleted_at')->orderBy('created_at', 'desc')->get(['app_link', 'app_banner']);
        // dd($paid_banners/)
        $data = array();
        $admin_banner = array();
        $pay_banner = array();
        if (!empty($banners)) {
            foreach ($banners as $banner) {
                $admin_banner[] = array(
                    'url' => $banner->banner_url,
                    'image' => URL::to('storage/' . $banner->images),
                    'content' => $banner->content
                );
            }
        }
        if (!empty($paid_banners)) {
            foreach ($paid_banners as $paid_banner) {
                $pay_banner[] = array(
                    'url' => $paid_banner->app_link,
                    'image' => URL::to('storage/' . $paid_banner->app_banner)
                );
            }
        }
        $banner_map[] = array(
            'banner_is_enable' => $banner_map_is_enable
        );
        $visible_banners = array_merge($admin_banner, $pay_banner);
        if (!empty($visible_banners)) {
            $response = [
                'success' => true,
                'code' => 200,
                'data' => $visible_banners,
                'banner_map' => $banner_map_is_enable,
                'banner_map_info' => $map_products
            ];
        } else {
            $response = [
                'success' => true,
                'code' => 200,
                'data' => $data,
            ];
        }
        return response()->json($response);
    }
    //banner map begin
    public function km_ads_from_cur_dist($lat, $lon, $dist, $state, $city)
    {
        // $lat    = 9.9202912;//munichalai
        // $lon    = 78.1294314;
        // $dist = 100; // Km
        if (!empty($lat)) {
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
        } else {
            $results = "";
        }
        if (empty($results)) {
            $get_city_based = TblCity::where('name', $city)->first();
            if (empty($get_city_based)) {
                $get_state_based = TblState::where('name', $state)->pluck('id')->first();
                if (!empty($get_state_based)) {
                    $results = TblCity::where('state_id', $get_state_based)->get();
                } else {
                    $results = array();
                }
            } else {
                $results = $get_city_based;
            }
        }
        $paid_ads = $this->get_paid_ads_for_map();
        $final_result = [];
        foreach ($results as $r) {
            $getArrDet = TblPost::where('city', $r->id)->whereIn('id', $paid_ads)->limit(10)
                ->inRandomOrder()->get()->toArray();
            $lc = (is_null($r->locality)) ? $r->name : $r->locality;
            $html = "";
            if (count($getArrDet) > 0) {
                $html .= "<h2 class='bg-green-500 text-center text-white p-1'>$lc</h2><ul>";
                foreach ($getArrDet as $k) {
                    $ad_title = $k['title'];
                    $ad_slug = URL::to('/' . $k["slug"]);
                    $html .= "<li class='mt-2 underline'><a href='$ad_slug' target='_blank'>$ad_title</a></li>";
                }
                $html .= "</ul>";
                $final_result[] = array(
                    "latitude" => $r->latitude,
                    "logitude" => $r->logitude,
                    "icon" => URL::to('images/loc-mark.png'),
                    "description" => $html
                );
            }
        }
        return $final_result;
    }
    public function get_paid_ads_for_map()
    {
        $curr_date = date('Y-m-d H:i:s');
        $blockedUsers = User::blocked_users();
        $payment_ids_array = TblPayment::whereNotIn('tbl_payments.user_id', $blockedUsers)
            ->where('tbl_payments.active', '1')
            ->where('tbl_payments.start_date', '<=', $curr_date)
            ->where('tbl_payments.end_date', '>=', $curr_date)
            ->pluck('tbl_payments.post_id')->toArray();
        return $payment_ids_array;
    }
    //banner map end
    public function home_banners_old()
    {
        $banners = TblBanners::whereNull('deleted_at')->orderBy('id', 'desc')->limit(3)->get(['banner_url', 'images']);
        $current_date = date('Y-m-d');
        $paid_banners = TblBannerAdvertisement::where('page', 'home')->where('status', 'approved')->where('active', 1)->where('end_date', '>=', $current_date)->whereNull('deleted_at')->orderBy('created_at', 'desc')->get(['app_link', 'app_banner']);
        $data = array();
        $admin_banner = array();
        $pay_banner = array();
        if (!empty($banners)) {
            foreach ($banners as $banner) {
                $admin_banner[] = array(
                    'url' => $banner->banner_url,
                    'image' => URL::to('storage/' . $banner->images),
                );
            }
        }
        if (!empty($paid_banners)) {
            foreach ($paid_banners as $paid_banner) {
                $pay_banner[] = array(
                    'url' => $paid_banner->app_link,
                    'image' => URL::to('storage/' . $paid_banner->app_banner)
                );
            }
        }
        $visible_banners = array_merge($admin_banner, $pay_banner);
        if (!empty($visible_banners)) {
            $response = [
                'success' => true,
                'code' => 200,
                'data' => $visible_banners
            ];
        } else {
            $response = [
                'success' => true,
                'code' => 200,
                'data' => $data,
            ];
        }
        return response()->json($response);
    }
    // Post sold , sale status updation
    public function update_sold_status(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $post_id = request()->post_id;
                $ex_post_sold = TblPost::find($post_id);
                if ($ex_post_sold->sold_status == 0) {
                    $ex_post_sold->update([
                        'sold_status' => 1 // update to sold
                    ]);
                    $response = [
                        'success' => true,
                        'code' => 200,
                        'message' => "Your post has been updated to sold!"
                    ];
                } else {
                    $ex_post_sold->update([
                        'sold_status' => 0 // back to sale
                    ]);
                    $response = [
                        'success' => true,
                        'code' => 200,
                        'message' => "Your post has been updated to sale!"
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    // mypost list for exchange to another post
    public function get_available_to_exchange()
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $visible_posts = TblPost::getMyPost($user);
                $data = array();
                foreach ($visible_posts as $visible_post) {
                    $single_info = TblPost::get_single_post_information($visible_post->id);
                    $data[] = array(
                        'id' => $visible_post->id,
                        'title' => $visible_post->title,
                        'images' => $single_info['images']
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Token"
            ];
        }
        return response()->json($response);
    }
    public function create_exchange(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $request->validate([
                    'exchange_id' => 'required',
                    'post_id' => 'required',
                    'post_owner' => 'required',
                ]);
                $check_exchange_post = TblPost::where('id', $request->exchange_id)->where('user_id', '=', $user)->whereNull('deleted_at')->pluck('id')->first();
                $check_own_post = TblPost::where('id', $request->post_id)->where('user_id', '!=', $user)->whereNull('deleted_at')->pluck('id')->first();
                if (empty($check_exchange_post) || empty($check_own_post)) {
                    $response = $this->sendError("Invalid Post.");
                    return response()->json($response);
                }
                $exchange_id = $request->exchange_id;
                $post_id = $request->post_id;
                $user_id = $user;
                $post_owner = $request->post_owner;
                $get_user_info = User::where('id', $user_id)->first();
                $get_post_info = TblPost::where('id', $post_id)->first();
                $get_seller_info = User::where('id', $get_post_info->user_id)->first();
                $settings = Setting::get_logos();
                $site_name = $settings['name'];
                $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                $check_exchange = TblExchangedPost::where('user_id', $user_id)->where('post_id', $post_id)->where('exchanged_post_id', $exchange_id)->first();
                if (empty($check_exchange)) {
                    TblExchangedPost::create([
                        'user_id' => $user_id,
                        'post_owner_id' => $post_owner,
                        'exchanged_post_id' => $exchange_id,
                        'post_id' => $post_id,
                        'block_exchange' => 0
                    ]);
                    // notification start  
                    $headers = array(
                        'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                        'Content-Type: application/json',
                    );
                    $message1 = array("notifydata" => array('to_id' => $get_post_info->user_id, 'from_id' => $user_id, 'message' => " sent exchange request to your product. Post Name - " . $get_post_info->title, 'notify_from' => 'post_exchange', 'notify_title' => "New post exchange In " . $site_name . " !..", 'post_id' => $post_id, 'slug' => $get_post_info->slug));
                    TblPost::send_push_notification($fcmid, $message1);
                    //send email
                    $slug = URL::to('my-exchange/incoming');
                    $mail_data = array("send_maildata" => array('to_id' => $get_post_info->user_id, 'message' => $get_user_info->name . " sent exchange request to your product. Post Name - " . $get_post_info->title, 'subject' => "New post exchange In " . $site_name . " !..", 'ad_url' => $slug));
                    $mail_key = "post_exchange_request";
                    Setting::notification_mail($mail_data, $mail_key);
                    // notification end
                    $response = $this->sendSuccess("Exchange created successfully!.");
                } else if ($check_exchange->block_exchange == 1) {
                    $response = $this->sendError("You can't exchange this product, this product already blocked by the seller!");
                } else if (($check_exchange->status == "success") || ($check_exchange->status == "pending") || ($check_exchange->status == "approved")) {
                    $response = $this->sendError("You already created exchange using this product!, please choose some another product.");
                } else {
                    // notification start  
                    $headers = array(
                        'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                        'Content-Type: application/json',
                    );
                    $message2 = array("notifydata" => array('to_id' => $get_post_info->user_id, 'from_id' => $user_id, 'message' => " sent exchange request to your product. Post Name - " . $get_post_info->title, 'notify_from' => 'post_exchange', 'notify_title' => "New post exchange In " . $site_name . " !..", 'post_id' => $post_id, 'slug' => $get_post_info->slug));
                    TblPost::send_push_notification($fcmid, $message2);
                    //send email
                    $slug = URL::to('my-exchange/incoming');
                    $mail_data = array("send_maildata" => array('to_id' => $get_post_info->user_id, 'message' => $get_user_info->name . " sent exchange request to your product. Post Name - " . $get_post_info->title, 'subject' => "New post exchange In " . $site_name . " !..", 'ad_url' => $slug));
                    $mail_key = "post_exchange_request";
                    Setting::notification_mail($mail_data, $mail_key);
                    // notification end
                    TblExchangedPost::create([
                        'user_id' => $user_id,
                        'post_owner_id' => $post_owner,
                        'exchanged_post_id' => $exchange_id,
                        'post_id' => $post_id,
                        'block_exchange' => 0
                    ]);
                    $response = $this->sendSuccess("Exchange created successfully!.");
                }
            } else {
                $response = $this->sendError('Invalid User!');
            }
        } else {
            $response = $this->sendError('Invalid Authorization Bearer Token!');
        }
        return response()->json($response);
    }
    public function exchange_detail_page($id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $get_exchange = TblExchangedPost::where('id', $id)->first();
                $sold_status = TblPost::where('id', $get_exchange->post_id)->pluck('sold_status')->first();
                $data = array();
                if (!empty($get_exchange)) {
                    $data = array(
                        'id' => $get_exchange->id,
                        'status' => $get_exchange->status,
                        'sold_status' => $sold_status
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = $this->sendError('Invalid User!');
            }
        } else {
            $response = $this->sendError('Invalid Authorization Bearer Token!');
        }
        return response()->json($response);
    }
    // Post exchange status updation
    public function update_exchange_status(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $request->validate([
                    'exchange_id' => 'required',
                    'status' => 'required',
                ]);
                $exchange_id = $request->exchange_id;
                $status = $request->status;
                $check_exchange = TblExchangedPost::find($exchange_id);
                if ($status == "block") {
                    // block the failed exchange post
                    $block = TblPost::find($check_exchange->exchanged_post_id);
                    $check_exchange->update(['block_exchange' => 1,]);
                    $block->update(['block_exchange' => 1,]);
                } else if ($status == "unblock") {
                    // unblock the failed exchange post
                    $unblock = TblPost::find($check_exchange->exchanged_post_id);
                    $check_exchange->update(['block_exchange' => 0,]);
                    $unblock->update(['block_exchange' => 0,]);
                } else {
                    $check_exchange->update(['status' => $status,]);
                    //status notification start
                    $settings = Setting::get_logos();
                    $site_name = $settings['name'];
                    $user_id = $user;
                    $get_user_info = User::where('id', $user_id)->first();
                    $get_post_info = TblPost::where('id', $check_exchange->post_id)->first();
                    if ($user_id == $check_exchange->user_id) {
                        $to_id = $check_exchange->post_owner_id;
                    } else {
                        $to_id = $check_exchange->user_id;
                    }
                    $get_seller_info = User::where('id', $to_id)->first();
                    $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                    $headers = array(
                        'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                        'Content-Type: application/json',
                    );
                    $message1 = array("notifydata" => array('to_id' => $to_id, 'from_id' => $user_id, 'message' => $status . " your exchange request on " . $get_post_info->title, 'notify_from' => 'post_exchange_status', 'notify_title' => "Post exchange status In " . $site_name . " !..", 'post_id' => $check_exchange->post_id, 'slug' => $get_post_info->slug));
                    TblPost::send_push_notification($fcmid, $message1);
                    $slug = URL::to('my-exchange/incoming');
                    $mail_data = array("send_maildata" => array('to_id' => $to_id, 'message' => $status . " your exchange request on " . $get_post_info->title, 'subject' => "Post exchange status In " . $site_name . " !..", 'ad_url' => $slug));
                    $mail_key = "post_exchange_status";
                    Setting::notification_mail($mail_data, $mail_key);
                    //status notification end
                }
                if ($status == "success") {
                    // update the post status to sold for exchanged post
                    $ex_post_sold = TblPost::find($check_exchange->exchanged_post_id);
                    $ex_post_sold->update([
                        'sold_status' => 1
                    ]);
                    // update the post status to sold for orginal post
                    $post_sold = TblPost::find($check_exchange->post_id);
                    $post_sold->update([
                        'sold_status' => 1
                    ]);
                    $message = "Exchanged successfully!";
                    //success notification start
                    $settings = Setting::get_logos();
                    $site_name = $settings['name'];
                    $user_id = $user;
                    $get_user_info = User::where('id', $user_id)->first();
                    $get_post_info = TblPost::where('id', $check_exchange->post_id)->first();
                    if ($user_id == $check_exchange->user_id) {
                        $to_id = $check_exchange->post_owner_id;
                    } else {
                        $to_id = $check_exchange->user_id;
                    }
                    $get_seller_info = User::where('id', $to_id)->first();
                    $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                    $headers = array(
                        'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                        'Content-Type: application/json',
                    );
                    $message1 = array("notifydata" => array('to_id' => $to_id, 'from_id' => $user_id, 'message' => " completed your exchange request on " . $get_post_info->title, 'notify_from' => 'post_exchange_complete', 'notify_title' => "Post exchange complete In " . $site_name . " !..", 'post_id' => $check_exchange->post_id, 'slug' => $get_post_info->slug));
                    TblPost::send_push_notification($fcmid, $message1);
                    $slug = URL::to('my-exchange/successful');
                    $mail_data = array("send_maildata" => array('to_id' => $to_id, 'message' => "Completed your exchange request on " . $get_post_info->title, 'subject' => "Post exchange complete In " . $site_name . " !..", 'ad_url' => $slug));
                    $mail_key = "post_exchange_success";
                    Setting::notification_mail($mail_data, $mail_key);
                    //success notification end
                } else {
                    $message = $status . " successfully!";
                }
                $response = $this->sendSuccess($message);
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    // Post exchange list
    public function my_exchanges_list(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $page = $request->has('page') ? $request->get('page') : 1;
                $limit = $request->has('limit') ? $request->get('limit') : 10;
                $request_from = $request->get('request_from');
                if ($request_from == "incoming") {
                    $exchanges_cnt = TblExchangedPost::where('post_owner_id', $user)->where(function ($q) {
                        $q->where('status', 'pending')->orWhere('status', 'accepted');
                    })->orderBy('created_at', 'desc')->count();
                    $exchanges = TblExchangedPost::where('post_owner_id', $user)->where(function ($q) {
                        $q->where('status', 'pending')->orWhere('status', 'accepted');
                    })->orderBy('created_at', 'desc')->limit($limit)->offset(($page - 1) * $limit)->get();
                } else if ($request_from == "outgoing") {
                    $exchanges_cnt = TblExchangedPost::where('user_id', $user)->where(function ($q) {
                        $q->where('status', 'pending')->orWhere('status', 'accepted');
                    })->orderBy('created_at', 'desc')->count();
                    $exchanges = TblExchangedPost::where('user_id', $user)->where(function ($q) {
                        $q->where('status', 'pending')->orWhere('status', 'accepted');
                    })->orderBy('created_at', 'desc')->limit($limit)->offset(($page - 1) * $limit)->get();
                } else if ($request_from == "success") {
                    $exchanges_cnt = TblExchangedPost::where('status', 'success')->where(function ($q) use ($user) {
                        $q->where('user_id', $user)->orWhere('post_owner_id', $user);
                    })->orderBy('created_at', 'desc')->count();
                    $exchanges = TblExchangedPost::where('status', 'success')->where(function ($q) use ($user) {
                        $q->where('user_id', $user)->orWhere('post_owner_id', $user);
                    })->orderBy('created_at', 'desc')->limit($limit)->offset(($page - 1) * $limit)->get();
                } else if ($request_from == "failed") {
                    $exchanges_cnt = TblExchangedPost::where(function ($q) {
                        $q->where('status', 'cancelled')
                            ->orWhere('status', 'declined')
                            ->orWhere('status', 'failed');
                    })->where(function ($q) use ($user) {
                        $q->where('user_id', $user)
                            ->orWhere('post_owner_id', $user);
                    })->orderBy('created_at', 'desc')->count();
                    $exchanges = TblExchangedPost::where(function ($q) {
                        $q->where('status', 'cancelled')
                            ->orWhere('status', 'declined')
                            ->orWhere('status', 'failed');
                    })->where(function ($q) use ($user) {
                        $q->where('user_id', $user)
                            ->orWhere('post_owner_id', $user);
                    })->orderBy('created_at', 'desc')->limit($limit)->offset(($page - 1) * $limit)->get();
                }
                $data = array();
                foreach ($exchanges as $exchange) {
                    $user_profile = User::where('id', $exchange->user_id)->withTrashed()->first();
                    $seller_profile = User::where('id', $exchange->post_owner_id)->withTrashed()->first();
                    $left = TblPost::get_single_post_information_with_delete($exchange->post_id);
                    $right = TblPost::get_single_post_information_with_delete($exchange->exchanged_post_id);
                    $left_city = TblCity::where('id', $left['city'])->first();
                    $left_state = TblState::where('id', $left_city->state_id)->pluck('name')->first();
                    $left_country = TblCountry::where('id', $left_city->country_id)->pluck('name')->first();
                    if (!empty($left_city->locality)) {
                        $left_location = $left_city->locality . " ," . $left_state . " ," . $left_country;
                    } else {
                        $left_location = $left_state . " ," . $left_country;
                    }
                    $right_city = TblCity::where('id', $right['city'])->first();
                    $right_state = TblState::where('id', $right_city->state_id)->pluck('name')->first();
                    $right_country = TblCountry::where('id', $right_city->country_id)->pluck('name')->first();
                    if (!empty($right_city->locality)) {
                        $right_location = $right_city->locality . " ," . $right_state . " ," . $right_country;
                    } else {
                        $right_location = $right_state . " ," . $right_country;
                    }
                    $right_user_deleted = !empty($user_profile->deleted_at) ? 1 : 0;
                    $right_user_blocked = $user_profile->is_blocked;
                    $left_user_deleted = !empty($seller_profile->deleted_at) ? 1 : 0;
                    $left_user_blocked = $seller_profile->is_blocked;
                    $data[] = array(
                        'id' => $exchange->id,
                        'left_img' => $left['images'],
                        'right_image' => $right['images'],
                        'left_id' => $exchange->post_id,
                        'right_id' => $exchange->exchanged_post_id,
                        'status' => $exchange->status,
                        'user_profile' => !empty($user_profile->profile_photo_path) ? URL::to('/public/storage/' . $user_profile->profile_photo_path) : URL::to('/storage/noimage150.png'),
                        'right_name' => $user_profile->name,
                        'left_name' => $seller_profile->name,
                        'right_user_deleted' => $right_user_deleted,
                        'right_user_blocked' => $right_user_blocked,
                        'left_user_deleted' => $left_user_deleted,
                        'left_user_blocked' => $left_user_blocked,
                        'left_post_title' => $left['post_title'],
                        'right_post_title' => $right['post_title'],
                        'left_location' => $left_location,
                        'right_location' => $right_location,
                        'created_at' => date('d-m-Y', strtotime($exchange->created_at)),
                        'is_deleted_left' => $left['is_deleted'],
                        'is_expired_left' => $left['is_expired'],
                        'is_deleted_right' => $right['is_deleted'],
                        'is_expired_right' => $right['is_expired'],
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data,
                    'total_count' => $exchanges_cnt
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    // Add banner advertisement
    public function add_banner_advertisement(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $request->validate([
                    'web_banner' => 'required',
                    'app_banner' => 'required',
                    // 'web_link' => 'required',
                    // 'app_link' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'payment_method' => 'required',
                    'live_days' => 'required',
                    'banner_display_page' => 'required',
                    'total_amount' => 'required'
                ]);
                $req_web_banner = $request->web_banner;
                $req_app_banner = $request->app_banner;
                $web_link = !empty($request->web_link) && isset($request->web_link) ? $request->web_link : "";
                $app_link = $request->app_link;
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $payment_method = $request->payment_method;
                $final_total_amount = $request->total_amount;
                $live_days = $request->live_days;
                $banner_display_page = $request->banner_display_page;
                $category_id = !empty($request->category_id) ? $request->category_id : "";
                // insert web banner
                $web_extension = explode('/', explode(':', substr($req_web_banner, 0, strpos($req_web_banner, ';')))[1])[1];
                $web_imageName = 'web_banner_ads/' . Str::random(15) . '.' . $web_extension;
                $web_replace = substr($req_web_banner, 0, strpos($req_web_banner, ',') + 1);
                $web_img = str_replace($web_replace, '', $req_web_banner);
                $web_k = str_replace(' ', '+', $web_img);
                Storage::disk('public')->put($web_imageName, base64_decode($web_k));
                //insert app banner
                $app_extension = explode('/', explode(':', substr($req_app_banner, 0, strpos($req_app_banner, ';')))[1])[1];
                $app_imageName = 'app_banner_ads/' . Str::random(15) . '.' . $app_extension;
                $app_replace = substr($req_app_banner, 0, strpos($req_app_banner, ',') + 1);
                $app_img = str_replace($app_replace, '', $req_app_banner);
                $app_k = str_replace(' ', '+', $app_img);
                Storage::disk('public')->put($app_imageName, base64_decode($app_k));
                if (!empty($app_imageName) && !empty($web_imageName)) {
                    $settings = Setting::get_logos();
                    $currency_symbol1 = Setting::get_admin_default_currency();
                    $currency_id = $currency_symbol1['id'];
                    //$currency_id = TblCurrency::where('id', $settings['default_currency'])->pluck('default_currency_id')->first();
                    $last_inset_id = TblBannerAdvertisement::create([
                        "payment_id" => "1",
                        "user_id" => $user,
                        "web_banner" => $web_imageName,
                        "app_banner" => $app_imageName,
                        "web_link" => $web_link,
                        "app_link" => $app_link,
                        "start_date" => date('Y-m-d', strtotime($start_date)),
                        "end_date" => date('Y-m-d', strtotime($end_date)),
                        "payment_type" => $payment_method,
                        "live_days" => $live_days,
                        "page" => $banner_display_page,
                        "category_id" => !empty($category_id) ? $category_id : "",
                        "total_amount" => $final_total_amount,
                        "payment_status" => "fail",
                        'active' => 0,
                        'status' => "pending",
                        'currency_id' => $currency_id
                    ])->id;
                    if ($payment_method == "stripe") {
                        $url = URL::to('/stripe-payment?pack_amt=' . $final_total_amount . '&uid=' . $user . '&lid=' . $last_inset_id . '&cid=' . $currency_id . '&paid_for=bannerads');
                    } else {
                        $url = URL::to('/bannerad-paypal-payment-process?total_amount=' . $final_total_amount . '&id=' . $last_inset_id . '&cid=' . $currency_id . '&uid=' . $user . '&paid_for=bannerads');
                    }
                    $response = [
                        'success' => true,
                        'code' => 200,
                        'url' => $url
                    ];
                } else {
                    $response = $this->sendError("Invalid Banners");
                }
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    // get advertisement page price
    public function get_banner_price(Request $request)
    {
        $request->validate([
            'page' => 'required',
        ]);
        $page = $request->page;
        $category_id = $request->category_id;
        if (!empty($category_id)) {
            $price = TblBannerAdvertisement::get_banner_ads_price($page, $category_id);
        } else {
            $price = TblBannerAdvertisement::get_banner_ads_price($page);
        }
        if (!empty($price)) {
            $response = [
                'success' => true,
                'code' => 200,
                'price' => $price
            ];
        } else {
            $response = $this->sendError("Invalid Pagse selected");
        }
        return response()->json($response);
    }
    //get all available payment_methods
    public function get_available_payment_methods()
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $data = array();
                $payment_methods = TblPaymentsMethod::where('active', '1')->get()->toArray();
                $payment_logo = "";
                foreach ($payment_methods as $payment_method) {
                    if (strtolower($payment_method['display_name']) == "paypal") {
                        $payment_logo = URL::to('/images/app_paypal.png');
                    } else if (strtolower($payment_method['display_name']) == "stripe") {
                        $payment_logo = URL::to('/images/app_stripe.png');
                    }
                    $data[] = array(
                        'payment_method' => strtolower($payment_method['display_name']),
                        'payment_logo' => $payment_logo
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = $this->sendError('Invalid User!');
            }
        } else {
            $response = $this->sendError('Invalid Authorization Bearer Token!');
        }
        return response()->json($response);
    }
    // Get Single Post Packages (including Free) for Add Post
    public function get_single_post_packages(Request $request)
    {
        $packages = Package::where('active', '1')->where('bulk_ads', '0')->orderBy('lft', 'asc')->get();

        $list_of_plans = [];
        foreach ($packages as $pkg) {
            $list_of_plans[] = [
                'package_id' => $pkg->id,
                'package_plan' => [['package_id' => $pkg->id]], // Frontend expects this structure
                'plan_name' => $pkg->name,
                'name' => $pkg->name,
                'short_name' => $pkg->short_name,
                'amount' => $pkg->price,
                'currency_symbol' => $pkg->currency_code
            ];
        }

        $response = [
            'success' => true,
            'code' => 200,
            'data' => [
                'list_of_plans' => $list_of_plans,
                'list_of_fea_ads' => [], // Add feature ads here if needed
                'currency_symbol' => '$' // Default or fetch from settings
            ]
        ];

        return response()->json($response);
    }

    // banner ads list
    public function my_banner_ads_histroy(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $page = $request->has('page') ? $request->get('page') : 1;
                $limit = $request->has('limit') ? $request->get('limit') : 10;
                $posted_within = $request->has('posted_within') ? $request->get('posted_within') : "";
                if ($posted_within == "today") {
                    $ads_cnt = TblBannerAdvertisement::where('user_id', $user)->where('active', 1)->whereNull('deleted_at')->whereDate('created_at', Carbon::today())->count();
                    $ads = TblBannerAdvertisement::where('user_id', $user)->where('active', 1)->whereNull('deleted_at')->whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->limit($limit)->offset(($page - 1) * $limit)->get();
                } else if ($posted_within == "weekly") {
                    $previous_week = strtotime("-1 week +1 day");
                    $start_week = strtotime("last sunday midnight", $previous_week);
                    $end_week = strtotime("next saturday", $start_week);
                    $start_week = date("Y-m-d", $start_week);
                    $end_week = date("Y-m-d", $end_week);
                    $ads_cnt = TblBannerAdvertisement::where('user_id', $user)->where('active', 1)->whereNull('deleted_at')->whereBetween('created_at', [$start_week, $end_week])->count();
                    $ads = TblBannerAdvertisement::where('user_id', $user)->where('active', 1)->whereNull('deleted_at')->whereBetween('created_at', [$start_week, $end_week])->orderBy('created_at', 'desc')->limit($limit)->offset(($page - 1) * $limit)->get();
                } else if ($posted_within == "monthly") {
                    $ads_cnt = TblBannerAdvertisement::where('user_id', $user)->where('active', 1)->whereNull('deleted_at')->whereMonth('created_at', '=', Carbon::now()->subMonth()->month)->count();
                    $ads = TblBannerAdvertisement::where('user_id', $user)->where('active', 1)->whereNull('deleted_at')->whereMonth('created_at', '=', Carbon::now()->subMonth()->month)->orderBy('created_at', 'desc')->limit($limit)->offset(($page - 1) * $limit)->get();
                } else {
                    $ads_cnt = TblBannerAdvertisement::where('user_id', $user)->where('active', 1)->whereNull('deleted_at')->count();
                    $ads = TblBannerAdvertisement::where('user_id', $user)->where('active', 1)->whereNull('deleted_at')->orderBy('created_at', 'desc')->limit($limit)->offset(($page - 1) * $limit)->get();
                }
                $data = array();
                foreach ($ads as $ad) {
                    $data[] = array(
                        'id' => $ad->id,
                        'status' => $ad->status,
                        'start_date' => date('d-m-Y', strtotime($ad->start_date)),
                        'end_date' => date('d-m-Y', strtotime($ad->end_date)),
                        'created_at' => date('d-m-Y', strtotime($ad->created_at)),
                        'image' => URL::to('/storage/' . $ad->app_banner)
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data,
                    'total_count' => $ads_cnt
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    //banner ads detail
    public function banner_ads_detail($id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $ad_info = TblBannerAdvertisement::where('id', $id)->first();
                $images = array();
                $images = array(
                    !empty($ad_info->web_banner) ? URL::to('/storage/' . $ad_info->web_banner) : '',
                    !empty($ad_info->app_banner) ? URL::to('/storage/' . $ad_info->app_banner) : '',
                );
                $data = array();
                $currency_symbol = TblDefaultCurrency::where('id', $ad_info->currency_id)->pluck('currency_hex')->first();
                $data = array(
                    'currency_symbol' => $currency_symbol,
                    'amount' => $ad_info->total_amount,
                    'payment_method' => $ad_info->payment_type,
                    'transaction_id' => $ad_info->payment_id,
                    'start_date' => $ad_info->start_date,
                    'end_date' => $ad_info->end_date,
                    'status' => $ad_info->status,
                    'images' => $images
                );
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data,
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    // Add shipping address
    public function add_address(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $request->validate([
                    'name' => 'required',
                    'address_1' => 'required',
                    'city' => 'required',
                    'state' => 'required',
                    'country' => 'required',
                    'phone_number' => 'required',
                    'zipcode' => 'required'
                ]);
                $check_address = TblShippingAddress::where('user_id', $user)->whereNull('deleted_at')->get();
                if ($check_address->count() > 0) {
                    if ($request->default_address == 1) {
                        foreach ($check_address as $check_address) {
                            TblShippingAddress::where('id', $check_address->id)->update(array('default_address' => 0));
                        }
                    }
                }
                $last_address_id = TblShippingAddress::create([
                    "user_id" => $user,
                    "name" => $request->name,
                    "address_1" => $request->address_1,
                    "address_2" => !empty($request->address_2) ? $request->address_2 : "",
                    "country" => $request->country,
                    "city" => $request->city,
                    "state" => $request->state,
                    "zipcode" => $request->zipcode,
                    'phone_number' => $request->phone_number,
                    'default_address' => !empty($request->default_address) ? $request->default_address : 0
                ])->id;
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => "New address added successfully!",
                    'inserted_id' => $last_address_id
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    // Edit shipping address
    public function edit_address(Request $request, $id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $def_add_flag = strval($request->default_address);
                $check_address = TblShippingAddress::where('user_id', $user);
                if ($check_address->count() > 0 && $def_add_flag == "1") {
                    $check_address->update(["default_address" => "0"]);
                }
                $update_node = TblShippingAddress::where('id', $id);
                $update_node->update([
                    "user_id" => $user,
                    "name" => $request->name,
                    "address_1" => $request->address_1,
                    "address_2" => !empty($request->address_2) ? $request->address_2 : "",
                    "country" => $request->country,
                    "city" => $request->city,
                    "state" => $request->state,
                    "zipcode" => $request->zipcode,
                    'phone_number' => $request->phone_number,
                    'default_address' => $def_add_flag
                ]);
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => "Address updated successfully!"
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function edit_address_old(Request $request, $id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $update_node = TblShippingAddress::find($id);
                $check_address = TblShippingAddress::where('user_id', $user)->whereNull('deleted_at')->get();
                if ($check_address->count() > 0) {
                    if ($request->default_address == 1) {
                        foreach ($check_address as $check_address) {
                            TblShippingAddress::where('id', $check_address->id)->update(array('default_address' => 0));
                        }
                    }
                }
                $update_node->update([
                    "user_id" => $user,
                    "name" => $request->name,
                    "address_1" => $request->address_1,
                    "address_2" => !empty($request->address_2) ? $request->address_2 : "",
                    "country" => $request->country,
                    "city" => $request->city,
                    "state" => $request->state,
                    "zipcode" => $request->zipcode,
                    'phone_number' => $request->phone_number,
                    'default_address' => !empty($request->default_address) ? $request->default_address : $update_node->default_address
                ]);
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => "Address updated successfully!"
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    //get all the my shoipping address list
    public function my_address_list()
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $addresses = TblShippingAddress::where('user_id', $user)->whereNull('deleted_at')->get();
                $data = array();
                foreach ($addresses as $address) {
                    $data[] = array(
                        'id' => $address->id,
                        'name' => $address->name,
                        'address_1' => $address->address_1,
                        'address_2' => $address->address_2,
                        'city' => $address->city,
                        'state' => $address->state,
                        'country' => $address->country,
                        'zipcode' => $address->zipcode,
                        'phone_number' => $address->phone_number,
                        'default_address' => $address->default_address // 0 no 1 yes
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = $this->sendError('Invalid User!');
            }
        } else {
            $response = $this->sendError('Invalid Authorization Bearer Token!');
        }
        return response()->json($response);
    }
    // set default shipping address
    public function set_default_address(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $update_node = TblShippingAddress::find($request->id);
                $update_node->update([
                    "default_address" => 1,
                ]);
                $other_address = TblShippingAddress::where('id', '!=', $request->id)->where('user_id', $user)->whereNull('deleted_at')->get();
                if (!empty($other_address)) {
                    foreach ($other_address as $other_address) {
                        TblShippingAddress::where('id', $other_address->id)->update(array('default_address' => 0));
                    }
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => "Default address updated successfully!"
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function get_address($id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $get_node = TblShippingAddress::find($id);
                $data = array(
                    "name" => $get_node->name,
                    "address_1" => $get_node->address_1,
                    "address_2" => $get_node->address_2,
                    "country" => $get_node->country,
                    "city" => $get_node->city,
                    "state" => $get_node->state,
                    "zipcode" => $get_node->zipcode,
                    'phone_number' => $get_node->phone_number,
                    'default_address' => $get_node->default_address
                );
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    // delete shipping address
    public function delete_address(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $record = TblShippingAddress::find($request->id);
                if ($record->default_address == 1) {
                    $set_default = TblShippingAddress::where('user_id', $user)->where('id', '!=', $request->id)->whereNull('deleted_at')->first();
                    $set_default->update([
                        'default_address' => 1
                    ]);
                }
                $record->delete();
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => "Deleted successfully!"
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function move_to_checkout(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $request->validate([
                    'post_id' => 'required',
                ]);
                $check_cart = TblPostCheckout::where('user_id', $user)->where('post_id', $request->post_id)->whereNull('deleted_at')->first();
                if (empty($request->address_id)) {
                    $get_address = TblShippingAddress::where('user_id', $user)->whereNull('deleted_at')->first();
                    $address_id = $get_address->id;
                } else {
                    $address_id = $request->address_id;
                }
                $get_post = TblPost::where('id', $request->post_id)->first();
                if ($get_post->instant_buy == 1) {
                    $price = round($get_post->price);
                    $s_rate = round($get_post->shipping_rate);
                    $total = $price + $s_rate;
                    if (empty($check_cart)) {
                        $last_cart_id = TblPostCheckout::create([
                            "user_id" => $user,
                            "post_id" => $request->post_id,
                            "seller_id" => $get_post->user_id,
                            "shipping_address" => $address_id,
                            "price" => $price,
                            "shipping_fee" => $s_rate,
                            "order_total" => $total,
                            "currency_id" => $get_post->currency_id
                        ])->id;
                        $last_id = $last_cart_id;
                    } else {
                        $check_cart->update([
                            "user_id" => $user,
                            "post_id" => $request->post_id,
                            "seller_id" => $get_post->user_id,
                            "shipping_address" => $address_id,
                            "price" => $price,
                            "shipping_fee" => $s_rate,
                            "order_total" => $total,
                            "currency_id" => $get_post->currency_id
                        ]);
                        $last_id = $check_cart->id;
                    }
                    $get_cart = TblPostCheckout::where('id', $last_id)->first();
                    $images = TblPost::get_single_post_information($request->post_id);
                    $seller_info = User::where('id', $get_post->user_id)->first();
                    $seller_profile = User_profile::where('user_id', $get_post->user_id)->first();
                    $selected_address = TblShippingAddress::where('id', $get_cart->shipping_address)->first();
                    $product_condition = TblPost::get_product_condition($request->post_id);
                    $currency_symbol = $this->post_currency($request->post_id);
                    $post_city = TblCity::where('id', $get_post->city)->first();
                    $post_state = TblState::where('id', $post_city->state_id)->first();
                    $post_country = TblCountry::where('id', $post_city->country_id)->first();
                    $address = array(
                        'id' => $selected_address->id,
                        'name' => $selected_address->name,
                        'address_1' => $selected_address->address_1,
                        'address_2' => $selected_address->address_2,
                        'city' => $selected_address->city,
                        'state' => $selected_address->state,
                        'country' => $selected_address->country,
                        'phone_number' => $selected_address->phone_number,
                        'zip_code' => $selected_address->zipcode,
                    );
                    $data = array(
                        'id' => $get_cart->id,
                        'image' => $images['images'],
                        'title' => $get_post->title,
                        'price' => $get_post->price,
                        'currency' => $currency_symbol,
                        'seller_name' => $seller_info->name,
                        'address' => $address,
                        'price' => $get_post->price,
                        'shipping_fee' => $get_post->shipping_rate,
                        'total' => $get_cart->order_total,
                        'post_id' => $get_cart->post_id,
                        'phone' => $seller_profile->phone,
                        'product_condition' => !empty($product_condition) ? $product_condition : "",
                        'post_address' => $post_city->locality . $post_city->name . "," . $post_state->name . "," . $post_country->name,
                        'profile' => !empty($seller_info->profile_photo_path) ? URL::to('/storage/' . $seller_info->profile_photo_path) : URL::to('storage/profile-avatar.jpg'),
                    );
                    $payment_methods = TblPaymentsMethod::where('active', '1')->where('name', 'stripe')->first();
                    if (!empty($payment_methods)) {
                        $url = URL::to('/stripe-payment?pack_amt=' . $get_cart->order_total . '&sid=' . $get_post->user_id . '&cid=' . $get_post->currency_id . '&lid=' . $get_cart->id . '&uid=' . $user . '&paid_for=buynow');
                    } else {
                        $url = "";
                    }
                    $response = [
                        'success' => true,
                        'code' => 200,
                        'data' => $data,
                        'url' => $url
                    ];
                } else {
                    $response = $this->sendError("Invalid Post");
                }
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function change_address(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $update_cart = TblPostCheckout::where('id', $request->id)->first();
                $update_cart->update([
                    'shipping_address' => $request->address_id
                ]);
                $response = $this->sendSuccess("Address changed successfully!");
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    //get all the my orders
    public function my_buynow_orders(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $data = array();
                $page = $request->has('page') ? $request->get('page') : 1;
                $limit = $request->has('limit') ? $request->get('limit') : 10;
                $get_from = $request->get('request_from');
                if ($get_from == "orders") {
                    $orders = TblBuynowOrder::where('user_id', $user)->limit($limit)->offset(($page - 1) * $limit)->get();
                    $order_count = TblBuynowOrder::where('user_id', $user)->count();
                } else {
                    $orders = TblBuynowOrder::where('seller_id', $user)->limit($limit)->offset(($page - 1) * $limit)->get();
                    $order_count = TblBuynowOrder::where('seller_id', $user)->count();
                }
                foreach ($orders as $order) {
                    // print_r($order);
                    // get deleted post also
                    $post_info = TblPost::get_single_post_information_with_delete($order->post_id);
                    $post_city = TblCity::where('id', $post_info['city'])->first();
                    $post_state = TblState::where('id', $post_city->state_id)->pluck('name')->first();
                    $post_country = TblCountry::where('id', $post_city->country_id)->pluck('name')->first();
                    $post_location = !empty($post_city->locality) ? $post_city->locality . "," : "" . $post_city->name . "," . $post_state . "," . $post_country;
                    $product_condition = TblPost::get_product_condition($order->post_id);
                    if ($get_from == "orders") {
                        // $name = User::where('id', $post_info['seller_id'])->pluck('name')->first();
                        $get_user = User::where('id', $post_info['seller_id'])->withTrashed()->first();
                        $name = $get_user->name;
                        $user_deleted = !empty($get_user->deleted_at) ? 1 : 0;
                        $user_blocked = $get_user->is_blocked;
                    } else {
                        // $name = User::where('id', $order->user_id)->pluck('name')->first();
                        $get_user = User::where('id', $order->user_id)->withTrashed()->first();
                        $name = $get_user->name;
                        $user_deleted = !empty($get_user->deleted_at) ? 1 : 0;
                        $user_blocked = $get_user->is_blocked;
                    }
                    $data[] = array(
                        'id' => $order->id,
                        'image' => $post_info["images"],
                        'name' => $name,
                        'created_at' => date("M d,Y", strtotime($order->created_at)),
                        'post_title' => $post_info['post_title'],
                        'status' => $order->order_status,
                        'post_location' => $post_location,
                        'product_condition' => $product_condition,
                        'user_deleted' => $user_deleted,
                        'user_blocked' => $user_blocked,
                        'is_deleted_post' => $post_info['is_deleted'],
                        'is_expired_post' => $post_info['is_expired'],
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data,
                    'total_count' => $order_count
                ];
            } else {
                $response = $this->sendError('Invalid User!');
            }
        } else {
            $response = $this->sendError('Invalid Authorization Bearer Token!');
        }
        return response()->json($response);
    }
    //get all the my orders
    public function buynow_order_detail(Request $request, $id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $order = TblBuynowOrder::where('id', $id)->first();
                $post_info = TblPost::get_single_post_information($order->post_id);
                $get_post = TblPost::where('id', $order->post_id)->first();
                $post_owner = User::where('id', $order->seller_id)->pluck("name")->first();
                $seller_mobile_no = User_profile::where('user_id', $order->seller_id)->pluck("phone")->first();
                $check_courier = TblCourierInfo::where('order_id', $id)->first();
                $user_info = User::where('id', $order->user_id)->first();
                $seller_info = User::where('id', $get_post->user_id)->first();
                if (!empty($check_courier)) {
                    $courier_info = array(
                        "id" => $check_courier->id,
                        "shippment_date" => $check_courier->shipping_date,
                        "courier_name" => $check_courier->courier_name,
                        "courier_service" => $check_courier->courier_service,
                        "tracking_id" => $check_courier->tracking_id,
                        "more_info" => $check_courier->more_info,
                    );
                } else {
                    $courier_info = array();
                }
                $delivery_address = array(
                    'name' => $order->shipping_add_name,
                    'address_1' => $order->shipping_add_address1,
                    'address_2' => $order->shipping_add_address2,
                    'city' => $order->shipping_add_city,
                    'state' => $order->shipping_add_state,
                    'country' => $order->shipping_add_country,
                    'zipcode' => $order->shipping_add_zipcode,
                    'phone_number' => $order->shipping_add_phone_number
                );
                $delivered_date = "";
                if ($order->order_status == "delivered") {
                    $delivered_date = date("M d,Y", strtotime($order->updated_at));
                }
                $currency_symbol = TblDefaultCurrency::where('id', $order->currency_id)->pluck('currency_hex')->first();
                $product_condition = TblPost::get_product_condition($order->post_id);
                $post_city = TblCity::where('id', $get_post->city)->first();
                $post_state = TblState::where('id', $post_city->state_id)->first();
                $post_country = TblCountry::where('id', $post_city->country_id)->first();
                $data = array(
                    'id' => $order->id,
                    'image' => $post_info["images"],
                    'created_at' => date("M d,Y", strtotime($order->created_at)),
                    'post_title' => $post_info['post_title'],
                    'status' => $order->order_status,
                    'price' => $order->price,
                    'currency_symbol' => $currency_symbol,
                    'seller_name' => $post_owner,
                    'buyer_name' => $user_info->name,
                    'payment_type' => "stripe",
                    'order_id' => $order->id,
                    'transaction_id' => $order->payment_id,
                    'created_at' => date('M d,Y', strtotime($order->created_at)),
                    'address' => $delivery_address,
                    'shipping_fee' => $order->shipping_fee,
                    'order_total' => $order->total,
                    'courier_address' => !empty($check_courier) ? 1 : 0, // 0 - no 1 - yes
                    'courier_info' => $courier_info,
                    "deliverd_confirmed_date" => !empty($delivered_date) ? $delivered_date : "",
                    'from_id' => $user,
                    "to_order_id" => $order->seller_id,
                    "to_sale_id" => $order->user_id,
                    "post_id" => $order->post_id,
                    "post_phone" => $seller_mobile_no,
                    'product_condition' => !empty($product_condition) ? $product_condition : "",
                    'post_address' => !empty($post_city->locality) ? $post_city->locality . " " : "" . $post_city->name . "," . $post_state->name . "," . $post_country->name,
                    'profile' => !empty($seller_info->profile_photo_path) ? URL::to('/storage/' . $seller_info->profile_photo_path) : URL::to('storage/profile-avatar.jpg'),
                    'buyer_profile' => !empty($user_info->profile_photo_path) ? URL::to('/storage/' . $user_info->profile_photo_path) : URL::to('storage/profile-avatar.jpg'),
                    'buyer_address' => !empty($user_info->address_line1) ? $user_info->address_line1 . "," : "" . $user_info->address_line2
                );
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data,
                ];
            } else {
                $response = $this->sendError('Invalid User!');
            }
        } else {
            $response = $this->sendError('Invalid Authorization Bearer Token!');
        }
        return response()->json($response);
    }
    // update the order status
    public function update_order_status(Request $request, $id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $update_node = TblBuynowOrder::find($id);
                $user_id = $update_node->user_id;
                $seller_id = $update_node->seller_id;
                $post_id = $update_node->post_id;
                $get_user_info = User::where('id', $user_id)->first();
                $get_post_info = TblPost::where('id', $post_id)->first();
                $get_seller_info = User::where('id', $seller_id)->first();
                $order_status = $update_node->order_status;
                if ($update_node->seller_id == $user) {
                    if ($update_node->order_status == "pending") {
                        $order_status = "processing";
                    } else if ($update_node->order_status == "processing") {
                        $order_status = "shipped";
                    }
                } else if ($update_node->user_id == $user) {
                    if ($update_node->order_status == "pending") {
                        $order_status = "cancelled";
                        $post_sold_status = TblPost::where('id', $update_node->post_id)->first();
                        $post_sold_status->update([
                            "sold_status" => 0,
                        ]);
                    } else if ($update_node->order_status == "shipped") {
                        $order_status = "delivered";
                        // send notification start
                        $settings = Setting::get_logos();
                        $site_name = $settings['name'];
                        $slug = URL::to('my-buynow/sales');
                        $headers = array(
                            'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                            'Content-Type: application/json',
                        );
                        $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                        $message1 = array("notifydata" => array('to_id' => $seller_id, 'from_id' => $user_id, 'message' => "Delivered your Buy-now request on " . $get_post_info->title, 'notify_from' => 'buynow_status', 'notify_title' => "Post BuyNow Status On " . $site_name . " !..", 'post_id' => $post_id, 'slug' => $get_post_info->slug, 'order_id' => $id));
                            TblPost::send_push_notification($fcmid, $message1);
                        $mail_data = array("send_maildata" => array('to_id' => $seller_id, 'message' => "Delivered your Buy-now request on " . $get_post_info->title, 'subject' => "Delivered BuyNow Request In " . $site_name . " !..", 'ad_url' => $slug));
                        $mail_key = "post_buy_now_success";
                        Setting::notification_mail($mail_data, $mail_key);
                        // send notification start
                    }
                }
                $update_node->update([
                    "order_status" => $order_status,
                    "updated_at" => date("Y-m-d H:i:s"),
                ]);
                if ($order_status != "delivered") {
                    $settings = Setting::get_logos();
                    $site_name = $settings['name'];
                    $headers = array(
                        'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                        'Content-Type: application/json',
                    );
                    $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
                    $message1 = array("notifydata" => array('to_id' => $user_id, 'from_id' => $seller_id, 'message' => $order_status . " your Buy-now request on " . $get_post_info->title, 'notify_from' => 'buynow_status', 'notify_title' => "Post BuyNow Status On " . $site_name . " !..", 'post_id' => $post_id, 'slug' => $get_post_info->slug, 'order_id' => $id));
                    TblPost::send_push_notification($fcmid, $message1);
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => "status successfully!"
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function update_courier_info(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $request->validate([
                    'order_id' => 'required',
                    'shipping_date' => 'required',
                    'courier_name' => 'required',
                    'courier_service' => 'required',
                    'tracking_id' => 'required',
                ]);
                $check_courier = TblCourierInfo::where('order_id', $request->order_id)->first();
                if (!empty($check_courier)) {
                    $check_courier->update([
                        "order_id" => $request->order_id,
                        "shipping_date" => !empty($request->shipping_date) ? date('Y-m-d', strtotime($request->shipping_date)) : $check_courier->shipping_date,
                        "courier_name" => !empty($request->courier_name) ? $request->courier_name : $check_courier->courier_name,
                        "courier_service" => !empty($request->courier_service) ? $request->courier_service : $check_courier->courier_service,
                        "tracking_id" => !empty($request->tracking_id) ? $request->tracking_id : $check_courier->tracking_id,
                        "more_info" => !empty($request->more_info) ? $request->more_info : $check_courier->more_info,
                    ]);
                } else {
                    TblCourierInfo::create([
                        "order_id" => $request->order_id,
                        "shipping_date" => date('Y-m-d', strtotime($request->shipping_date)),
                        "courier_name" => $request->courier_name,
                        "courier_service" => $request->courier_service,
                        "tracking_id" => $request->tracking_id,
                        "more_info" => !empty($request->more_info) ? $request->more_info : "",
                    ]);
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => "Updated successfully!"
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function get_courier_info($id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $check_courier = TblCourierInfo::where('order_id', $id)->first();
                if (!empty($check_courier)) {
                    $order = TblBuynowOrder::where('id', $id)->pluck('post_id')->first();
                    $post_info = TblPost::get_single_post_information($order);
                    $data = array(
                        "order_id" => $id,
                        "shipping_date" => date('d-m-Y', strtotime($check_courier->shipping_date)),
                        "courier_name" => $check_courier->courier_name,
                        "courier_service" => $check_courier->courier_service,
                        "tracking_id" => $check_courier->tracking_id,
                        "more_info" => $check_courier->more_info,
                        "image" => $post_info["images"]
                    );
                } else {
                    $data = array();
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function store_insight(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $chk_post = TblPost::where('id', $request->post_id)->first();
                if ($chk_post->user_id != $user) {
                    $client_ip = !empty($request->ip_address) ? $request->ip_address : "";
                    $post_id = $request->post_id;
                    $curr_date = date('Y-m-d');
                    $curr_city = !empty($request->city) ? $request->city : "";
                    $curr_lat = !empty($request->latitude) ? $request->latitude : "";
                    $curr_lng = !empty($request->longitude) ? $request->longitude : "";
                    // Unique visitor check: har user sirf 1 baar count hoga (kisi bhi date pe)
                    // Date check hata di gai taake true unique view count ho
                    $chk_record = TblPostInsight::where('post_id', $post_id)->where("user_id", $user)->first();
                    if (empty($chk_record)) {
                        TblPostInsight::create([
                            'user_id' => $user,
                            'post_id' => $post_id,
                            'ip_address' => $client_ip,
                            'visited_date' => $curr_date,
                            'views' => 1,
                            'city' => $curr_city,
                            'latitude' => $curr_lat,
                            'logitude' => $curr_lng,
                        ]);
                    }
                    // Agar record pehle se hai to kuch nahi karna - view count nahi badhega
                    $response = [
                        'success' => true,
                        'code' => 200,
                        'message' => "created successfully!"
                    ];
                } else {
                    $response = $this->sendError("user and posted-user same");
                }
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function insight_list(Request $request, $post_id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $type = $request->type;
                $user_views = TblPostInsight::where('post_id', $post_id)->get();
                $tot = 0;
                foreach ($user_views as $get_view) {
                    $tot += $get_view->views;
                }
                $total_user_views = $tot;
                $unique_user = TblPostInsight::where('post_id', $post_id)->groupBy('user_id')->get();
                $unique_user_views = count($unique_user);
                $total_likes = TblSavedPosts::where('post_id', $post_id)->count();
                $total_comments = TblReview::where('post_id', $post_id)->WhereNull('deleted_at')->count();
                $unique_city = TblPostInsight::where('post_id', $post_id)->whereRaw('city <> ""')->groupBy('city')->get();
                $total_city = $unique_city;
                $offer_request = TblChat::where('post_id', $post_id)->where('make_offer', 1)->WhereNull('deleted_at')->get();
                $total_offer_request = count($offer_request);
                $exchange_request = TblExchangedPost::where('post_owner_id', $user)->where('post_id', $post_id)->where('status', 'pending')->orWhere('status', 'accepted')->get();
                $total_exchange_request = count($exchange_request);
                $city_data = array();
                if (!empty($total_city)) {
                    $city_name = array();
                    $city_count = array();
                    foreach ($total_city as $row) {
                        $c_count = TblPostInsight::where('city', $row->city)->where('post_id', $post_id)->groupBy('user_id')->get();
                        $city_data[] = array(
                            "cityname" => $row->city,
                            "citycount" => count($c_count)
                        );
                    }
                }
                // chart data
                $weekdata = array();
                if ($type == "weekly") {
                    /* SET 1 DATA */
                    $monday = strtotime(date("Y-m-d"));
                    $sunday = strtotime(date("Y-m-d", $monday) . " -6 days");
                    $set2_sd = date("Y-m-d", $monday);
                    $set2_ed = date("Y-m-d", $sunday);
                    $set2datas = $this->displayDates($set2_ed, $set2_sd);
                    foreach ($set2datas as $set2data) {
                        $total_views = TblPostInsight::where('post_id', $post_id)->whereDate('created_at', date('Y-m-d', strtotime($set2data)))->get();
                        $weekdata[] = array(
                            "dates" => date('d M', strtotime($set2data)),
                            "counts" => count($total_views)
                        );
                    }
                } else if ($type == "monthly") {
                    $d1 = date("Y-m-d");
                    $newdate = date("Y-m-d", strtotime("-6 months"));
                    $result = CarbonPeriod::create($newdate, '1 month', $d1);
                    $newmonth = array();
                    foreach ($result as $dt) {
                        $newmonth[] = $dt->format("m");
                    }
                    $setmonths = $newmonth;
                    foreach ($setmonths as $setmonth) {
                        $total_views1 = TblPostInsight::where('post_id', $post_id)->whereMonth('created_at', $setmonth)->get();
                        $monthNum = $setmonth;
                        $monthName = date('F', mktime(0, 0, 0, $monthNum, 10)); // March
                        $weekdata[] = array(
                            "dates" => $monthName,
                            "counts" => count($total_views1)
                        );
                    }
                } else if ($type == "yearly") {
                    $d2 = date("Y-m-d");
                    $newyear = date("Y-m-d", strtotime("-6 years"));
                    $result1 = CarbonPeriod::create($newyear, '1 year', $d2);
                    $yearnew = array();
                    foreach ($result1 as $dt) {
                        $yearnew[] = $dt->format("Y");
                    }
                    $setyears = $yearnew;
                    foreach ($setyears as $setyear) {
                        $total_views2 = TblPostInsight::where('post_id', $post_id)->whereYear('created_at', $setyear)->get();
                        $weekdata[] = array(
                            "dates" => $setyear,
                            "counts" => count($total_views2)
                        );
                    }
                }
                $check_post_package = TblPost::check_post_expired($post_id);
                $sell_fast = 0;
                if ($check_post_package['ads_type'] != "free") {
                    if ($check_post_package['expired'] == "Expired") {
                        $sell_fast = 1;
                    }
                } else {
                    $sell_fast = 1;
                }
                $data = array(
                    "total_user_views" => $total_user_views,
                    "unique_user_views" => $unique_user_views,
                    "total_likes" => $total_likes,
                    "total_comments" => $total_comments,
                    "total_offer_request" => $total_offer_request,
                    "total_exchange_request" => $total_exchange_request,
                    "total_visited_city" => count($total_city),
                    "city_data" => $city_data,
                    "chart_data" => $weekdata,
                    'sell_fast' => $sell_fast
                );
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function displayDates($date1, $date2, $format = 'd-m-Y')
    {
        $dates = array();
        $current = strtotime($date1);
        $date2 = strtotime($date2);
        $stepVal = '+1 day';
        while ($current <= $date2) {
            $dates[] = date($format, $current);
            $current = strtotime($stepVal, $current);
        }
        return $dates;
    }
    public function currency_list()
    {
        $currencies = TblCurrency::where('active', 0)->whereNull('deleted_at')->get();
        $data = array();
        foreach ($currencies as $currency) {
            $data[] = array(
                'id' => $currency->id,
                'hex_code' => $currency->currency_hex,
                'short_code' => $currency->short_code,
                'value' => $currency->id,
                'label' => $currency->short_code
            );
        }
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $data
        ];
        return response()->json($response);
    }
    public function get_active_ads_methods(Request $request, $id)
    {
        $post_methods = TblPostMethod::get_active_post_methods();
        $data = array();
        $check_buynow = TblCategory::where('id', $id)->value('buynow');
        $check_exchange = TblCategory::where('id', $id)->value('exchange');
        if (!empty($post_methods)) {
            foreach ($post_methods as $post_method) {
                //  dd($post_method->id);
                if ($post_method->name == 'buynow' && $check_buynow == '1') {
                    $data[] = array(
                        'key' => $post_method->name,
                    );
                }
                // Check if the method is 'exchange' and 'exchange' is active
                if ($post_method->name == 'exchange' && $check_exchange == '1') {
                    $data[] = array(
                        'key' => $post_method->name,
                    );
                }
                // $data[] = array(
                //     'key' => $post_method->name,
                // );
            }
        }
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $data
        ];
        return response()->json($response);
    }
    // resend verify mail
    public function resend_verify_email(Request $request)
    {
        if ($request->has('id')) {
            $user = User::find($request->id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'code' => 404,
                    'message' => "User not found."
                ]);
            }

            if ($user->hasVerifiedEmail()) {
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => "Email is already verified."
                ];
            } else {
                // 1. Generate New OTP
                $otp = rand(100000, 999999);

                // 2. Update User Record with new OTP
                $user->otp = $otp;
                $user->otp_expires_at = now()->addMinutes(10); // Reset expiry
                $user->save();

                // 3. Send OTP Email (Link wala code hata diya)
                try {
                    $user->notify(new OtpNotification($otp));

                    $response = [
                        'success' => true,
                        'code' => 200,
                        'message' => "Verification code sent to your email."
                    ];
                } catch (\Exception $e) {
                    \Log::error("Resend OTP Failed: " . $e->getMessage());

                    $response = [
                        'success' => false,
                        'code' => 500,
                        'message' => "Failed to send email. Please try again later."
                    ];
                }
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "User ID cannot be null!"
            ];
        }

        return response()->json($response);
    }
    public function email_verify(Request $request)
    {
        if ($request->has('id') && $request->id != null) {
            $user = User::find($request->id);
            if (!empty($user)) {
                if (!$user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                    $message = "Email verified successfully.!";
                } else {
                    $message = "This email already verified.!";
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => $message
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Enter valid user id.!"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "user id cannot be null.!"
            ];
        }
        return response()->json($response);
    }
    public function get_active_map_homepage()
    {
        // get banner type value
        $getbanner_type = Setting::where('key', 'homepage_banner_type')->first();
        $ban_type_value = json_decode($getbanner_type->value);
        $banner_type = $ban_type_value->banner_type;
        //get google apikey form app 
        $getapp_key = Setting::where('key', 'app')->first();
        $google_api_value = json_decode($getapp_key->value);
        $google_api_key = $google_api_value->google_api_key;
        $mapbox_api_key = $google_api_value->mapbox_api_key;
        if ($banner_type == 1) {
            $data = array(
                'map_value' => "google_map",
                'api_key' => $google_api_key,
            );
        } else if ($banner_type == 3) {
            $data = array(
                'map_value' => "map_box",
                'api_key' => $mapbox_api_key,
            );
        } else if ($banner_type == 2) {
            $data = array(
                'map_value' => "image_slider"
            );
        } else {
            $data = array(
                'map_value' => "no data"
            );
        }
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $data
        ];
        return response()->json($response);
    }
    public function add_seller_review(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $request->validate([
                    'ratings' => 'required',
                    'comment' => 'required',
                    'user_id' => 'required',
                    'seller_id' => 'required',
                ]);
                $user_id = $request->user_id;
                $seller_id = $request->seller_id;
                $ratings = $request->ratings;
                $comment = $request->comment;
                $create = TblSellerReviews::create([
                    "user_id" => $user_id,
                    "seller_id" => $seller_id,
                    "ratings" => $ratings,
                    "comment" => $comment,
                    "approved" => '1',
                ]);
                $response = [
                    'success' => true,
                    'code' => 200,
                    'message' => "Review added successfully.!",
                ];
                return response()->json($response);
            } else {
                $response = [
                    'success' => false,
                    'code' => 0,
                    'message' => "Invalid User",
                ];
                return response()->json($response);
            }
        } else {
            $response = [
                'success' => false,
                'code' => 0,
                'message' => "Invalid Authorization Bearer Token!",
            ];
            return response()->json($response);
        }
    }
    public function seller_review_list($seller_id)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $get_reviews = TblSellerReviews::join('users', 'tbl_seller_reviews.user_id', '=', 'users.id')->get(['tbl_seller_reviews.*', 'users.name', 'users.profile_photo_path'])->where('seller_id', $seller_id);
                $avg_rating = TblSellerReviews::rate_avg($seller_id);
                $token = $this->getBearerToken();
                if (!empty($token['code']) && ($token['code'] == 200)) {
                    $user = $this->getLoggedUser($token['token']);
                    if (!empty($user)) {
                        // dd($user);
                        $lan_code = User::where('id', $user)->value('preferred_language');
                    } else {
                        $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                        $response = [
                            'success' => false,
                            'code' => 0,
                            'message' => "Invalid User"
                        ];
                    }
                } else {
                    $lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
                }
                $data = [];
                foreach ($get_reviews as $get_review) {
                    $data[] = array(
                        'posted_by' => $get_review->name,
                        "ratings" => $get_review->ratings,
                        "comment" => $get_review->comment,
                        "approved" => '1',
                        "created_at" => TblChat::timeAgo($get_review->created_at, $lan_code),
                        "profile_image" => !empty($get_review->profile_photo_path) ? URL::to('storage/' . $get_review->profile_photo_path) : URL::to('storage/profile-avatar.jpg'),
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data,
                    'avg_star_rate' => round($avg_rating),
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function default_language()
    {
        $default_lan_name = TblLanguage::where('default', '=', '1')->value('name');
        $default_lan_id = TblLanguage::where('default', '=', '1')->value('id');
        $default_lan_code = TblLanguage::where('default', '=', '1')->value('abbr');
        $default_val = TblLanguage::where('default', '=', '1')->value('default');
        // print_r($default_val);
        $data = ([
            'id' => $default_lan_id,
            'name' => $default_lan_name,
            'default' => $default_val,
            'code' => $default_lan_code
        ]);
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $data,
        ];
        return response()->json($response);
    }
    public function active_language()
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $active_lan_name = TblLanguage::where('active', '=', '1')->get();
                // $active_lan_id = TblLanguage::where('active','=','1')->get('id');
                // $active_lan_default = TblLanguage::where('active','=','1')->get('default');
                // $active_lan_active = TblLanguage::where('active','=','1')->get('active');
                // print_r($active_language);
                // $data= ([
                //     'id' => $active_lan_id,
                //     'name'=> $active_lan_name,
                //     'default'=>$active_lan_default,
                //     'active' => $active_lan_active
                // ]);
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $active_lan_name,
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function language($lang_code)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $language = Languages::where('lang_code', $lang_code)
                    ->where('type', 'mobile')
                    ->get();
                $data = [];
                foreach ($language as $languages) {
                    $data[] = array(
                        "lang_text" => $languages->lang_text,
                        "lang_org_text" => $languages->lang_org_text
                    );
                }
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $data,
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function lang_code_name()
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                $lang = TblLanguage::all('abbr', 'name');
                $response = [
                    'success' => true,
                    'code' => 200,
                    'data' => $lang,
                ];
            } else {
                $response = $this->sendError("Invalid User");
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function getLanguage($lang_code)
    {
        $language = Languages::where('lang_code', $lang_code)
            ->where('type', 'mobile')
            ->get();
        if ($language->isEmpty()) {
            // If translation for provided language and type is not found,
            // retrieve English translation as fallback.
            $language = Languages::where('lang_code', 'en')
                ->where('type', 'mobile')
                ->get();
        }
        $data = [];
        foreach ($language as $lang) {
            $org_text = $lang->lang_org_text;
            $translated_text = $lang->lang_text;
            // Check if the translated text is empty, if so, use English translation.
            if (empty($translated_text)) {
                $english_translation = Languages::where('lang_code', 'en')
                    ->where('lang_org_text', $org_text)
                    ->where('type', 'mobile')
                    ->first();
                if ($english_translation) {
                    $translated_text = $english_translation->lang_text;
                } else {
                    // If English translation is also not found, use the original text.
                    $translated_text = $org_text;
                }
            }
            $data[$org_text] = $translated_text;
        }
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $data,
        ];
        return response()->json($response);
    }
    // public function getLanguage($lang_code)
    // {
    //     // $token = $this->getBearerToken();
    //     // if (!empty($token['code']) && ($token['code'] == 200)) {
    //     //     $user = $this->getLoggedUser($token['token']);
    //     //     if (!empty($user)) {
    //     $language =  Languages::where('lang_code', $lang_code)
    //         ->where('type', 'mobile')
    //         ->get();
    //     $data = [];
    //     foreach ($language as $languages) {
    //         $data[] = array(
    //             $languages->lang_org_text => $languages->lang_text,
    //         );
    //     }
    //     $object_array = (json_encode($data));
    //     $mergedArray = array_reduce($data, function ($carry, $item) {
    //         return array_merge($carry, $item);
    //     }, []);
    //     $object_array = json_encode($mergedArray);
    //     $response = [
    //         'success' => true,
    //         'code' => 200,
    //         'data' => $object_array,
    //     ];
    //     //     } else {
    //     //         $response = $this->sendError("Invalid User");
    //     //     }
    //     // } else {
    //     //     $response = $this->sendError("Invalid Token");
    //     // }
    //     return response()->json($response);
    // }
    public function verification_request(Request $request)
    {
        $token = $this->getBearerToken();
        if (!empty($token['code']) && ($token['code'] == 200)) {
            $user = $this->getLoggedUser($token['token']);
            if (!empty($user)) {
                // try {
                $loguser = User::where('id', $user)->first();
                $user_id = $user;
                $email = $loguser->email;
                $name = request()->name;
                $governmentproof = request()->document;
                $certificate = request()->certificate;
                $address_proof = request()->address_proof;
                $iscompany = request()->iscompany;
                $shopData = array(
                    'brand_name' => request()->brand_name,
                    'brand_title' => request()->brand_title,
                    'description' => request()->description,
                    'contact_number' => request()->contact_number,
                    'office_hours' => request()->office_hours,
                    'city_name' => request()->city_name,            // this is local area
                    'main_city_name' => request()->main_city_name,  // this is city name like chennai, madurai
                    'city_lat' => request()->city_lat,
                    'city_lag' => request()->city_lag,
                    'country_long' => request()->country_long,
                    'country_short' => request()->country_short,
                    'state_long' => request()->state_long,
                    'state_short' => request()->state_short,
                    'brand_logo' => request()->brand_logo,
                    'about_us' => request()->about_us,
                );
                $destinationPath = '/verification_request';
                if (Verificationrequest::where('user_id', '=', $user_id)->where('is_approved', 1)->exists()) {
                    $response = [
                        'success' => true,
                        'code' => 200,
                        'data' => 'You have already verified',
                    ];
                } else {
                    $data = Verificationrequest::create([
                        'name' => $name,
                        'user_id' => $user_id,
                        'email' => $email,
                        'is_company' => $iscompany,
                    ]);
                    $document_certificate = [$governmentproof, $address_proof];
                    $document = [$governmentproof, $certificate, $address_proof];
                    if ($document[1] == null) {
                        foreach ($document_certificate as $key => $file) {
                            $fileName = $file->getClientOriginalName();
                            $extension = time() . '.' . $file->getClientOriginalExtension();
                            $filepath = $file->storeAs($destinationPath, $fileName);
                            $fileUpload = new VerificationAttachment();
                            $fileUpload->verify_id = $data->id;
                            $fileUpload->attachments = $fileName;
                            $fileUpload->save();
                        }
                    } else {
                        foreach ($document as $key => $file) {
                            $image = $file;  // your base64 encoded
                            $image = str_replace('data:file/pdf;base64,', '', $image);
                            $image = str_replace(' ', '+', $image);
                            $imageName = Str::random(10) . '.png';
                            // Storage::disk('local')->put($imageName, base64_decode($image));
                            $fileName = $file->getClientOriginalName();
                            $extension = time() . '.' . $file->getClientOriginalExtension();
                            $filepath = $file->storeAs($destinationPath, $fileName);
                            $fileUpload = new VerificationAttachment();
                            $fileUpload->verify_id = $data->id;
                            $fileUpload->attachments = $fileName;
                            $fileUpload->save();
                        }
                    }
                    $verify_id = $data->id;
                    $is_company = $data->is_company;
                    $to_shop = $this->openShop($user, $verify_id, $shopData, $is_company);
                    $response = [
                        'success' => true,
                        'code' => 200,
                        'data' => 'Verified Successfully',
                    ];
                }
                // } catch (Exception $e) {
                //     $response = [
                //         'success' => false,
                //         'code' => 500,
                //         'message' => $e,
                //         'request' => $request->all(),
                //         'file' => $request->file()
                //     ];
                // }
            } else {
                // $response = $this->sendError("Invalid User");
                $response = [
                    'success' => false,
                    'code' => 401,
                    'message' => 'Invalid User!'
                ];
            }
        } else {
            $response = $this->sendError("Invalid Token");
        }
        return response()->json($response);
    }
    public function openShop($user, $verify_id, $shopData, $is_company)
    {
        // dd($user,$verify_id,$shopData);
        $get_verify_id = Verificationrequest::where('user_id', '=', $user)->value('id');
        $is_shop = BusinessProfile::where('verifcation_id', $get_verify_id)->exists();
        $response = [];
        if ($is_shop == false) {
            // try {
            // $request->validate([
            //     'brand_name' => 'required',
            //     'brand_title' => 'required|email',
            //     'description' => 'required',
            //     'contact_number' => 'required',
            //     'office_hours' => 'required',
            //     'about_us'=> 'required',
            //     'city_name' =>'required',
            //     'main_city_name'=>'required',
            //     'city_lat' =>'required',
            //     'country_long'=>'required',
            //     'city_lag'=>'required',
            //     'country_short'=>'required',
            //     'state_long'=>'required',
            //     'state_short'=>'required'
            //  ]);
            // dd($shopData);
            $brand_name = $shopData['brand_name'];
            $brand_title = $shopData['brand_title'];
            $description = $shopData['description'];
            $contact_number = $shopData['contact_number'];
            $office_hours = $shopData['office_hours'];
            $city_name = $shopData['city_name'];            // this is local area
            $main_city_name = $shopData['main_city_name'];  // this is city name like chennai, madurai
            $city_lat = $shopData['city_lat'];
            $city_lag = $shopData['city_lag'];
            $country_long = $shopData['country_long'];
            $country_short = $shopData['country_short'];
            $state_long = $shopData['state_long'];
            $state_short = $shopData['state_short'];
            $about_us = $shopData['about_us'];
            $destinationPath = public_path('storage/business-profile');
            // dd($destinationPath);
            $country_id = "";
            $state_id = "";
            $city_id = "";
            $nameimage = "";
            $address = $main_city_name . ',' . $state_long . ',' . $country_long;
            $tbl_country = TblCountry::where('code', $country_short)->where('name', $country_long)->get();
            if ($tbl_country->count() == 0) {
                $country_id = TblCountry::create([
                    'code' => $country_short,
                    'name' => $country_long
                ])->id;
            } else {
                $country_id = $tbl_country[0]->id;
            }
            //get state info
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
            //get city info
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
            $value = Verificationrequest::where('user_id', '=', $user)->first();
            $imageName = "";
            if ($shopData['brand_logo'] != null) {
                // $image = $shopData['brand_logo'];  // your base64 encoded
                // $image = str_replace('data:image/png;base64,', '', $image);
                // $image = str_replace(' ', '+', $image);
                // $imageName = Str::random(15) . '.png';
                // $result = Storage::disk('public')->put('business-profile/', base64_decode($image));
                // dd($shopData['brand_logo']); 
                // $extension = explode('/', explode(':', substr($shopData['brand_logo'], 0, strpos($shopData['brand_logo'], ';')))[1])[1];
                // dd($extension);
                $nameimage = Str::random(15) . '.' . 'jpeg';
                $imageName = "/business-profile/" . $nameimage;
                $replace = substr($shopData['brand_logo'], 0, strpos($shopData['brand_logo'], ',') + 1);
                $img = str_replace($replace, '', $shopData['brand_logo']);
                $k = str_replace(' ', '+', $img);
                $decoded_image = base64_decode($k);
                Storage::put($imageName, $decoded_image);
                // dd($decoded_image);
                // $path_web_list = "/business-profile/thumb15/";
                $path_web_list = "/business-profile/thumb15/" . $nameimage;
                $web_list = Image::make($decoded_image)->resize(15, 15, function ($constraint) {
                    $constraint->aspectRatio();
                });
                // dd($web_list);
                // Storage::disk('public')->put($path_web_list, (string) $web_list->encode());
                try {
                    Storage::put($path_web_list, (string) $web_list->encode());
                } catch (\Exception $e) {
                    dd($e->getMessage());
                }
                $path = "/business-profile/thumb50/" . $nameimage;
                $image = Image::make($decoded_image)->resize(30, 30, function ($constraint) {
                    $constraint->aspectRatio();
                });
                try {
                    Storage::put($path, (string) $image->encode());
                } catch (\Exception $e) {
                    dd($e->getMessage());
                }
                $path_app_detail = "/business-profile/listsize/" . $nameimage;
                $app_detail = Image::make($decoded_image)->resize(400, 400, function ($constraint) {
                    $constraint->aspectRatio();
                });
                try {
                    Storage::put($path_app_detail, (string) $app_detail->encode());
                } catch (\Exception $e) {
                    dd($e->getMessage());
                }
            }
            $phonenum = ($contact_number == null) ? "-" : $contact_number;
            $insertData = array(
                'brand_name' => $brand_name,
                'brand_title' => $brand_title,
                'description' => $description,
                'contact_number' => $phonenum,
                'brand_logo' => $nameimage,
                'city' => $city_id,
                'is_company' => $is_company,
                'hours' => $office_hours,
                'verifcation_id' => $verify_id,
                'about_us' => $about_us
            );
            // dd($insertData);
            $last_id = BusinessProfile::create([
                'brand_name' => $brand_name,
                'brand_title' => $brand_title,
                'description' => $description,
                'contact_number' => $phonenum,
                'brand_logo' => $nameimage,
                'city' => $city_id,
                'is_company' => $is_company,
                'hours' => $office_hours,
                'verifcation_id' => $verify_id,
                'about_us' => $about_us,
                'address' => $address
            ]);
            $response = [
                'success' => true,
                'code' => 200,
                'data' => 'Shop Created Successfully',
            ];
            // }catch (\Exception $e) {
            //     $response = [
            //         'success' => true,
            //         'code' => 500,
            //         'data' => "Something went wrong"
            //     ];
            // }
        } else {
            $response = [
                'success' => true,
                'code' => 200,
                'data' => 'Shop already exist in this account',
            ];
        }
    }
    public function home_search(Request $request)
    {
        $lat = $request->lat;
        $lng = $request->lon;
        $distance = 20;
        $blockedUsers = User::blocked_users();
        $postDetail = TblPost::whereNotIn('tbl_posts.user_id', $blockedUsers);
        if (!empty($lat)) {
            $cities_lat_lng = TblCity::where('latitude', 'like', '%' . bcdiv($lat, 1, 3) . '%')->where('logitude', 'like', '%' . bcdiv($lng, 1, 3) . '%')->pluck('id')->first();
            $current_address_ids = array();
            $addressidss = array();
            if (!empty($cities_lat_lng)) {
                $current_address_ids[] = $cities_lat_lng;
            }
            if (!empty($distance)) {
                $addressidss = $this->get_city_ids($lat, $lng, $distance);
            } else {
                $addressidss = $this->get_city_ids($lat, $lng, 20);
            }
            $addressids = array_merge($current_address_ids, $addressidss);
        }
        if (!empty($addressids)) {
            $postDetail->whereIn('tbl_posts.city', $addressids);
        }

        // Category Filter
        if ($request->has('category_id') && $request->category_id != "") {
            $cats = TblCategory::descendantsAndSelf($request->category_id)->pluck('id');
            $postDetail->whereIn('tbl_posts.category_id', $cats);
        }

        // Custom Filters - Accept both formats for compatibility
        $customFilters = $request->input('customFilters') ?? $request->input('custom_filters');
        if (!empty($customFilters) && is_array($customFilters)) {
            $postDetail->applyCustomFilters($customFilters);
        }

        // Price Filter
        if ($request->has('min_price') && $request->min_price != "") {
            $postDetail->where('tbl_posts.price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price != "") {
            $postDetail->where('tbl_posts.price', '<=', $request->max_price);
        }
        $search = $request->search;
        $search_id = array();
        $resut_post_ids = array();
        if ($search != "") {
            $check_in_custom = TblPostValue::where('value', 'like', '%' . $search . '%')->pluck('post_id')->toArray();
            $check_in_post = TblPost::where('title', 'like', '%' . $search . '%')->pluck('id')->toArray();
            if (!empty($check_in_custom) && !empty($check_in_post)) {
                $resut_post_ids = array_merge($check_in_custom, $check_in_post);
            } elseif (!empty($check_in_custom)) {
                $resut_post_ids = $check_in_custom;
            } elseif (!empty($check_in_post)) {
                $resut_post_ids = $check_in_post;
            }
            $search_id = array_unique($resut_post_ids);
            // dd(array_unique($resut_post_ids));
            $postDetail->whereIn('tbl_posts.id', $search_id);
        }
        // $postDetail = TblPost::where('title', 'like', '%' . $search . '%')->whereNull('deleted_at')->get();
        $data = [];
        $get_all_posts = $postDetail->pluck('id')->toArray();
        $payment_ads_ids = array();
        $free_ads_ids = array();
        /* get unexpired payment post */
        $payment_ids_array = TblPost::get_unexpired_payment_post_ids();
        /* get unexpired free post */
        $free_ids_array = TblPost::get_unexpired_free_post_ids();
        foreach ($payment_ids_array as $payment_ids_array) {
            // dd($payment_ids_array);
            if (in_array($payment_ids_array, $get_all_posts)) {
                $payment_ads_ids[] = $payment_ids_array;
            }
        }
        // dd($payment_ads_ids);
        foreach ($free_ids_array as $free_ids_array) {
            if (in_array($free_ids_array, $get_all_posts)) {
                ;
                $free_ads_ids[] = $free_ids_array;
            }
        }
        $pay_id = $payment_ads_ids;
        $free_id = $free_ads_ids;
        $post_ids = array_merge($pay_id, $free_id);
        $location_post = array_merge($pay_id, $free_id);
        $post_data = $postDetail->whereIn('id', $post_ids)
            ->whereNull('deleted_at')
            ->where('active', 1)
            ->where('sold_status', 0)
            ->get();
        foreach ($post_data as $post) {
            // dd($post->id);
            $adtype = TblPost::getAddtype($post->id);
            $currency = $this->post_currency($post->id);
            $single_info = TblPost::get_single_post_information($post->id);
            $additional_data = $this->getAdditionalInfo($post->id);
            $additional_info = array();
            foreach ($additional_data as $additional_data) {
                $additional_info[] = array(
                    'lable' => $additional_data['label'],
                    'value' => $additional_data['value']
                );
            }
            $token = $this->getBearerToken();
            if (!empty($token['code']) && ($token['code'] == 200)) {
                $user = $this->getLoggedUser($token['token']);
                if (!empty($user)) {
                    // dd($user);
                    $fav = "";
                    if (!empty($user)) {
                        $fav = TblSavedPosts::where('user_id', $user)->where('post_id', $post->id)->get(['id']);
                    }
                } else {
                    $fav = "";
                    if (!empty($user)) {
                        $fav = TblSavedPosts::where('user_id', $user)->where('post_id', $post->id)->get(['id']);
                    }
                    $response = [
                        'success' => false,
                        'code' => 0,
                        'message' => "Invalid User"
                    ];
                }
            } else {
                $fav = "";
                if (!empty($user)) {
                    $fav = TblSavedPosts::where('user_id', $user)->where('post_id', $post->id)->get(['id']);
                }
            }
            //seller logo  start
            $seller_logo = '';
            $seller_brand = '';
            $seller = TblPost::where('id', $post->id)->value('user_id');
            $seller_check = Verificationrequest::where('user_id', $seller)->first();
            if (!empty($seller_check)) {
                if ($seller_check->is_company == 'yes') {
                    $seller_logo = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_logo');
                    $seller_brand = BusinessProfile::where('verifcation_id', $seller_check->id)->value('brand_name');
                }
            }
            $brand_logo = !empty($seller_logo) ? URL::to('storage/business-profile/thumb50/' . $seller_logo) : URL::asset('storage/profile-avatar.jpg');
            //seller logo end
            $data[] = array(
                'id' => $post->id,
                'title' => $post->title,
                'price' => $post->price,
                'city_name' => !empty($post->locality) ? $post->locality : $post->city_name,
                'created_at' => date('d M Y', strtotime($post->created_at)),
                'ad_type' => !empty($adtype) ? Str_replace('_', ' ', strtoupper($adtype->ad_type)) : "",
                'images' => $single_info["images"],
                'description' => $post["description"],
                'custom_fields' => $additional_info,
                'brand_logo' => $brand_logo,
                'brand_name' => $seller_brand,
                'sellerId' => $seller,
                'is_fav' => !empty($fav) && ($fav->count() > 0) ? true : false,
                'currency_symbol' => $currency,
                'giving_away' => $post->giving_away
            );
        }
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $data,
        ];
        return response()->json($response);
    }
    public function readStatusUpdate(Request $request)
    {
        $read_id = $request->read_id;
        $chat = TblChat::where('read_id', $read_id)->first();
        if (!empty($chat)) {
            $chat->update(['read_status' => 1]);
            $response = [
                'success' => true,
                'code' => 200,
                'data' => "updated",
            ];
        } else {
            $response = [
                'success' => false,
                'code' => 404,
                'data' => "Chat not found",
            ];
        }
        return response()->json($response);
    }
    public function unreadChat(Request $request)
    {
        $toId = $request->query('to_id');
        $postId = $request->query('post_id');
        $readId = $request->query('read_id');
        $token = $this->getBearerToken();
        if (empty($token['code']) || $token['code'] != 200) {
            return response()->json([
                'success' => false,
                'code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }
        $user = $this->getLoggedUser($token['token']);
        if (empty($user)) {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'User not found',
            ], 404);
        }
        $unreadChats = TblChat::where('to_id', $toId)
            ->where('post_id', $postId)
            ->where('read_status', 0)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'from_id', 'to_id', 'msg', 'created_at']);
        return response()->json([
            'success' => true,
            'code' => 200,
            'unread_chats' => $unreadChats
        ]);
    }

    public function recently_viewed_posts($id)
    {
        $token = $this->getBearerToken();
        if (empty($token['code']) || $token['code'] != 200) {
            return response()->json([
                'success' => false,
                'code' => 401,
                'message' => 'Invalid Token',
            ]);
        }

        $user = $this->getLoggedUser($token['token']);
        if (empty($user)) {
            return response()->json([
                'success' => false,
                'code' => 401,
                'message' => 'Invalid User',
            ]);
        }

        // TODO: Implement recently viewed posts tracking
        // For now, return empty array to prevent 404 error
        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => []
        ]);
    }

    private function getFilterGroup($filterName, $categoryName = null)
    {
        $categoryName = strtolower($categoryName ?? '');
        $filterName = strtolower($filterName);

        if (in_array($filterName, ['categories', 'sub categories'])) {
            return 'Category';
        }

        if (in_array($filterName, ['price range', 'distance'])) {
            return 'General';
        }

        return match ($categoryName) {
            'automobiles' => match ($filterName) {
                    'make', 'body type', 'transmission', 'fuel type', 'cylinders', 'drive type', 'seats', 'doors' => 'Vehicle Specifics',
                    'features', 'colors', 'safety rating' => 'Features & Options',
                    'vehicle identification', 'engine', 'engine power', 'towing capacity', 'gross vehicle mass' => 'Technical Details',
                    default => 'Other Details',
                },
            'phones & tablets' => match ($filterName) {
                    'brand', 'storage', 'ram', 'screen size', 'color' => 'Device Specs',
                    'condition', 'warranty' => 'Purchase Details',
                    default => 'Other Features',
                },
            'fashion' => match ($filterName) {
                    'size', 'color', 'brand', 'gender' => 'Item Details',
                    'condition' => 'Purchase Details',
                    default => 'Other',
                },
            default => 'General Details',
        };
    }

    public function get_filter_options(Request $request)
    {
        $cat_id = $request->cat_id;

        if (empty($cat_id)) {
            return response()->json([
                'success' => true,
                'code' => 200,
                'data' => []
            ]);
        }

        $category = TblCategory::find($cat_id);
        $categoryName = $category ? $category->title : '';

        // Fetch active fields for this category
        $fields = TblFieldsDetail::where('cat_id', $cat_id)
            ->where('active', 1)
            ->with('options')
            ->get();

        $data = [];
        foreach ($fields as $field) {
            $options = [];
            foreach ($field->options as $option) {
                $options[] = [
                    'id' => $option->id,
                    'value' => $option->value
                ];
            }

            $data[] = [
                'id' => $field->id,
                'name' => $field->name,
                'type' => $field->type,
                'options' => $options,
                'group' => $this->getFilterGroup($field->name, $categoryName)
            ];
        }

        return response()->json([
            'success' => true,
            'code' => 200,
            'data' => $data
        ]);
    }
}
