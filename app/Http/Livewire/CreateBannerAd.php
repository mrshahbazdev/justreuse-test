<?php

namespace App\Http\Livewire;

use App\Models\Package;
use App\Models\TblBannerAdvertisement;
use App\Models\TblCategory;
use App\Models\TblPaymentsMethod;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;
use Intervention\Image\Facades\Image; // Correct Facade
use Illuminate\Support\Facades\Storage;

class CreateBannerAd extends Component
{
    use WithFileUploads;

    // Form Properties
    public $page = 'home';
    public $category_id = '';
    public $start_date;
    public $end_date;
    public $web_link;
    public $app_link;
    public $web_banner;
    public $app_banner;
    public $selectedPaymentMethod;

    // Calculated Properties
    public $pricePerDay = 0;
    public $live_days = 0;
    public $totalAmount = 0;

    // Data lists
    public $paymentMethods = [];
    public $currencySymbol;

    // --- VALIDATION CHANGE IS HERE ---
    // URL rules ko update kar diya gaya hai taake sirf internal links hi accept hon.
    protected function rules()
    {
        return [
            'page' => 'required|string',
            'category_id' => 'required_if:page,search|nullable|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'web_link' => ['required', 'url', 'starts_with:' . url('/')],
            'app_link' => ['required', 'url', 'starts_with:' . url('/')],
            'web_banner' => 'required|image|max:1024', // 1MB Max
            'app_banner' => 'required|image|max:1024', // 1MB Max
        ];
    }

    // Custom validation messages for the new rule
    protected $messages = [
        'web_link.starts_with' => 'The Web Link must be an internal URL from this site.',
        'app_link.starts_with' => 'The App Link must be an internal URL from this site.',
    ];


    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->paymentMethods = TblPaymentsMethod::where('active', '1')->get()->toArray();
        if (!empty($this->paymentMethods)) {
            $this->selectedPaymentMethod = $this->paymentMethods[0]['name'];
        }
        $currency = Setting::get_admin_default_currency();
        $this->currencySymbol = !empty($currency) ? $currency['currency_hex'] : "$";
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addDay()->format('Y-m-d');
        $this->calculatePrice();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['page', 'category_id', 'start_date', 'end_date'])) {
            $this->calculatePrice();
        }
    }

    public function calculatePrice()
    {
        if ($this->page === 'home') {
            $this->pricePerDay = TblBannerAdvertisement::get_banner_ads_price('home', null);
        } else {
            if ($this->category_id) {
                $this->pricePerDay = TblBannerAdvertisement::get_banner_ads_price('search', $this->category_id);
            } else {
                $this->pricePerDay = 0;
            }
        }

        try {
            $startDate = Carbon::parse($this->start_date);
            $endDate = Carbon::parse($this->end_date);
            $this->live_days = $startDate->diffInDays($endDate);
            if ($this->live_days < 0) $this->live_days = 0;
        } catch (\Exception $e) {
            $this->live_days = 0;
        }

        $this->totalAmount = $this->live_days * $this->pricePerDay;
    }

    public function saveBannerAd()
    {
        $this->validate();

        try {
            // --- Store images directly without watermark ---
            $webBannerPath = $this->web_banner->store('web_banner_ads', 'public');
            $appBannerPath = $this->app_banner->store('app_banner_ads', 'public');

            $settings = Setting::get_logos();

            $bannerAd = TblBannerAdvertisement::create([
                "user_id" => Auth::id(),
                "web_banner" => $webBannerPath,
                "app_banner" => $appBannerPath,
                "web_link" => $this->web_link,
                "app_link" => $this->app_link,
                "start_date" => $this->start_date,
                "end_date" => $this->end_date,
                "payment_type" => $this->selectedPaymentMethod,
                "live_days" => $this->live_days,
                "page" => $this->page,
                "category_id" => $this->page === 'search' ? $this->category_id : null,
                "total_amount" => $this->totalAmount,
                "currency_id" => $settings['default_currency'],
                'payment_status' => "pending",
                'active' => 0
            ]);

            // Use toast notification for better UX
            $this->dispatchBrowserEvent('show-toast', ['message' => 'Banner Ad created successfully! Proceeding to payment...']);
            
            // Yahan se aap payment gateway par redirect kar sakte hain
            return redirect()->route('my-banner-ads');

        } catch (\Exception $e) {
            // Show an error toast if anything fails
            $this->dispatchBrowserEvent('show-toast', ['message' => 'An error occurred while creating the ad. Please try again.']);
            return;
        }
    }

    public function render()
    {
        $categorylist = TblCategory::orderBy('list_order', "asc")->get()->toTree();

        return view('livewire.create-banner-ad', [
            'categorylist' => $categorylist
        ])->layout('layouts.packagebuy');
    }
}

