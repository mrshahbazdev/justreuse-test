<?php
namespace App\Http\Livewire;
use Livewire\Component;
use App\Models\User_profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use App\Models\TblLanguage;
use App\Models\TblCity;
use App\Models\TblCountry;
use App\Models\TblState;
use App\Models\TblCurrency;
use Illuminate\Support\Facades\Session;

class MyprofileComponent extends Component
{
    use WithFileUploads;

    // Basic Information
    public $first_name, $last_name, $phone, $address_line1, $address_line2, $date_of_birth, $gender, $user_id, $show_mobile;
    public $name, $email, $profile_photo_path, $new_profile_photo_path, $mobile_verified;
    
    // Stripe Keys
    public $stripe_public_key, $stripe_private_key;
    
    // Preferences
    public $p_lang, $p_curr;
    
    // Location
    public $city_name, $city_lat, $main_city_name, $city_lag, $country_long, $country_short, $state_long, $state_short;
    
    // OTP Verification
    public $otp, $showOtpField = false, $otpSent = false;
    
    // UI States
    public $isOpen = 0;
    public $cnfopen = 0;
    public $deleteConfirmation = false;
    public $currentLocation = '';
    
    // Gender Options
    public $gendArr = ["male" => "Male", "female" => "Female", "other" => "Other"];

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'first_name' => 'required|min:2',
        'last_name' => 'required|min:2',
        'phone' => 'sometimes|nullable',
        'address_line1' => 'sometimes|nullable',
        'address_line2' => 'sometimes|nullable',
        'date_of_birth' => 'sometimes|nullable|date',
        'gender' => 'sometimes|nullable',
        'new_profile_photo_path' => 'sometimes|nullable|image|max:2048',
        'p_lang' => 'sometimes|nullable',
        'p_curr' => 'sometimes|nullable',
        'stripe_public_key' => 'sometimes|nullable',
        'stripe_private_key' => 'sometimes|nullable',
    ];

    public function mount()
    {
        $this->loadUserData();
    }

    public function loadUserData()
    {
        $user = Auth::user();
        $user_pro = $user->user_profile;

        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->profile_photo_path = $user->profile_photo_path;
        
        if ($user_pro) {
            // Basic Information
            $this->first_name = $user_pro->first_name ?? '';
            $this->last_name = $user_pro->last_name ?? '';
            $this->phone = $user_pro->phone ?? '';
            $this->address_line1 = $user_pro->address_line1 ?? '';
            $this->address_line2 = $user_pro->address_line2 ?? '';
            $this->date_of_birth = $user_pro->date_of_birth ?? '';
            $this->gender = $user_pro->gender ?? '';
            $this->show_mobile = $user_pro->show_mobile ?? false;
            $this->mobile_verified = $user_pro->mobile_verified ?? false;
            
            // Stripe Keys
            $this->stripe_public_key = $user_pro->stripe_public_key ?? '';
            $this->stripe_private_key = $user_pro->stripe_private_key ?? '';

            // Load current location
            if ($user_pro->city_id) {
                $location = TblCity::where('id', $user_pro->city_id)->first();
                
                if ($location) {
                    // Get country and state
                    $country = TblCountry::where('id', $location->country_id)->first();
                    $state = TblState::where('id', $location->state_id)->first();
                    
                    $this->currentLocation = $location->name;
                    if ($state) {
                        $this->currentLocation .= ', ' . $state->name;
                    }
                    if ($country) {
                        $this->currentLocation .= ', ' . $country->name;
                    }
                    
                    $this->city_name = $location->locality ?? '';
                    $this->main_city_name = $location->name ?? '';
                    $this->city_lat = $location->latitude ?? '';
                    $this->city_lag = $location->logitude ?? '';
                    $this->country_long = $country ? $country->name : '';
                    $this->country_short = $country ? $country->code : '';
                    $this->state_long = $state ? $state->name : '';
                    $this->state_short = $state ? $state->code : '';
                }
            }
        }

        // Preferences
        $this->p_lang = $user->preferred_language ?? '';
        $this->p_curr = $user->preferred_currency ?? '';
    }

    public function render()
    {
        $user_pro = Auth::user()->user_profile;
        $info_location = collect();
        
        if ($user_pro && $user_pro->city_id) {
            $info_location = TblCity::join('tbl_countries', 'tbl_cities.country_id', '=', 'tbl_countries.id')
                ->join('tbl_states', 'tbl_cities.state_id', '=', 'tbl_states.id')
                ->where('tbl_cities.id', $user_pro->city_id)
                ->select('tbl_cities.*', 
                         'tbl_countries.code as country_short', 
                         'tbl_countries.name as country_long', 
                         'tbl_states.code as state_short', 
                         'tbl_states.name as state_long')
                ->get();
        }

        $select_language = TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
        $currencies = TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
        $settings = Setting::get_logos();
        $default_curr = $settings['default_currency'] ?? null;
        return view('livewire.myprofile.show', compact('info_location', 'select_language', 'currencies', 'default_curr'));
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function saveProfile()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $user = User::find(Auth::id());
                
                // Update profile photo
                if ($this->new_profile_photo_path) {
                    // Delete old photo if exists
                    if ($user->profile_photo_path) {
                        Storage::disk('public')->delete($user->profile_photo_path);
                    }
                    
                    $imagename = $this->new_profile_photo_path->store('profile-photos', 'public');
                    $user->update(['profile_photo_path' => $imagename]);
                    $this->profile_photo_path = $imagename;
                }

                // Update user basic info and preferences
                $user->update([
                    'name' => $this->name,
                    'preferred_language' => $this->p_lang,
                    'preferred_currency' => $this->p_curr,
                ]);

                // Get or create location
                $city_id = $this->getOrCreateLocation();

                // Update or create user profile with all fields
                $user_profile_data = [
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'phone' => $this->phone,
                    'address_line1' => $this->address_line1,
                    'address_line2' => $this->address_line2,
                    'date_of_birth' => $this->date_of_birth,
                    'gender' => $this->gender,
                    'show_mobile' => $this->show_mobile,
                    'stripe_private_key' => $this->stripe_private_key ?? "",
                    'stripe_public_key' => $this->stripe_public_key ?? "",
                ];

                if ($city_id) {
                    $user_profile_data['city_id'] = $city_id;
                }

                User_profile::updateOrCreate(
                    ['user_id' => Auth::id()],
                    $user_profile_data
                );
            });

            Session::flash('message', 'Profile updated successfully!');
            Session::flash('class', 'success');
            
            // Redirect based on language preference
            if ($this->p_lang) {
                return redirect(URL::to('locale') . '/' . $this->p_lang);
            }

        } catch (\Exception $e) {
            Session::flash('message', 'Error updating profile: ' . $e->getMessage());
            Session::flash('class', 'error');
        }
    }

    private function getOrCreateLocation()
    {
        if (empty($this->main_city_name) || empty($this->country_short)) {
            return null;
        }

        try {
            // Get or create country
            $country = TblCountry::firstOrCreate(
                ['code' => $this->country_short],
                [
                    'code' => $this->country_short,
                    'name' => $this->country_long ?? $this->country_short
                ]
            );

            // Get or create state
            $state = TblState::firstOrCreate(
                [
                    'country_id' => $country->id,
                    'code' => $this->state_short ?? substr($this->state_long ?? 'UNK', 0, 3)
                ],
                [
                    'country_id' => $country->id,
                    'code' => $this->state_short ?? substr($this->state_long ?? 'UNK', 0, 3),
                    'name' => $this->state_long ?? 'Unknown'
                ]
            );

            // Get or create city
            $city = TblCity::firstOrCreate(
                [
                    'country_id' => $country->id,
                    'state_id' => $state->id,
                    'name' => $this->main_city_name
                ],
                [
                    'country_id' => $country->id,
                    'state_id' => $state->id,
                    'locality' => $this->city_name ?? $this->main_city_name,
                    'name' => $this->main_city_name,
                    'latitude' => $this->city_lat ?? 0,
                    'logitude' => $this->city_lag ?? 0,
                    'active' => 1
                ]
            );

            return $city->id;

        } catch (\Exception $e) {
            \Log::error('Location creation error: ' . $e->getMessage());
            return null;
        }
    }

    public function setLocation($placeData)
    {
        $this->city_name = $placeData['city_name'] ?? '';
        $this->main_city_name = $placeData['main_city_name'] ?? '';
        $this->city_lat = $placeData['city_lat'] ?? '';
        $this->city_lag = $placeData['city_lag'] ?? '';
        $this->country_long = $placeData['country_long'] ?? '';
        $this->country_short = $placeData['country_short'] ?? '';
        $this->state_long = $placeData['state_long'] ?? '';
        $this->state_short = $placeData['state_short'] ?? '';

        // Update current location display
        $this->currentLocation = $this->main_city_name;
        if ($this->state_long) {
            $this->currentLocation .= ', ' . $this->state_long;
        }
        if ($this->country_long) {
            $this->currentLocation .= ', ' . $this->country_long;
        }

        $this->dispatchBrowserEvent('location-updated', ['location' => $this->currentLocation]);
    }

    public function clearLocation()
    {
        $this->city_name = '';
        $this->main_city_name = '';
        $this->city_lat = '';
        $this->city_lag = '';
        $this->country_long = '';
        $this->country_short = '';
        $this->state_long = '';
        $this->state_short = '';
        $this->currentLocation = '';
    }

    public function sendOtp()
    {
        $this->validate(['phone' => 'required']);

        // Check if phone already exists
        $existingPhone = User_profile::where('phone', $this->phone)
            ->where('user_id', '!=', Auth::id())
            ->exists();

        if ($existingPhone) {
            $this->addError('phone', 'Phone number already exists!');
            return;
        }

        // In production, use actual OTP service
        $code = 1234; // For demo purposes
        
        User_profile::where('user_id', Auth::id())->update(['otp' => $code]);
        
        $this->showOtpField = true;
        $this->otpSent = true;
        Session::flash('otp_message', 'OTP sent to your mobile number!');
    }

    public function verifyOtp()
    {
        $this->validate(['otp' => 'required|digits:4']);

        $isValid = User_profile::where('user_id', Auth::id())
            ->where('otp', $this->otp)
            ->exists();

        if ($isValid) {
            User_profile::where('user_id', Auth::id())->update([
                'mobile_verified' => 1,
                'otp' => null
            ]);
            
            $this->mobile_verified = 1;
            $this->showOtpField = false;
            Session::flash('message', 'Mobile number verified successfully!');
        } else {
            $this->addError('otp', 'Invalid OTP');
        }
    }

    public function confirmDelete()
    {
        $this->deleteConfirmation = true;
    }

    public function cancelDelete()
    {
        $this->deleteConfirmation = false;
    }

    public function deleteAccount()
    {
        $user = User::find(Auth::id());
        
        if ($user) {
            $user->update([
                'is_blocked' => '1',
                'deleted_at' => now()
            ]);

            // Send notification email
            $mail_data = [
                'send_maildata' => [
                    'to_id' => $user->id,
                    'message' => "Thank you for joining us. Your account has been deleted successfully.",
                    'subject' => "Account Deleted",
                    'ad_url' => url('/')
                ]
            ];
            
            Setting::notification_mail($mail_data, "account_deleted");

            Auth::logout();
            session()->flush();
            
            return redirect('/');
        }
    }

    public function removeProfilePhoto()
    {
        $user = User::find(Auth::id());
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);
            $this->profile_photo_path = null;
            Session::flash('message', 'Profile photo removed successfully!');
        }
    }

    /**
     * Calculate age from date of birth
     */
    public function getAgeAttribute()
    {
        if ($this->date_of_birth) {
            return \Carbon\Carbon::parse($this->date_of_birth)->age;
        }
        return null;
    }
}