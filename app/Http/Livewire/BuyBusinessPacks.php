<?php

namespace App\Http\Livewire;

use App\Models\Package;
use App\Models\TblPaymentsMethod;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class BuyBusinessPacks extends Component
{
    public $topAdPacks = [];
    public $featureAdPacks = [];
    public $paymentMethods = [];
    public $currencySymbol;

    public $selectedPacks = [];
    public $totalAmount = 0;
    public $selectedPaymentMethod;

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->paymentMethods = TblPaymentsMethod::where('active', '1')->get()->toArray();
        if (!empty($this->paymentMethods)) {
            $this->selectedPaymentMethod = $this->paymentMethods[0]['name'];
        }

        $this->topAdPacks = Package::where('active', '1')->where('bulk_ads', '1')
            ->where('ad_type', 'top_ad')->orderBy('lft', 'asc')->get()->toArray();

        $this->featureAdPacks = Package::where('active', '1')->where('bulk_ads', '1')
            ->where('ad_type', 'feature_ad')->orderBy('lft', 'asc')->get()->toArray();

        // Load initial state from session
        $this->selectedPacks = array_keys(Session::get('cart-selected-bulk-packs', []));
        $this->calculateTotal();
        
        $currency = Setting::get_admin_default_currency();
        $this->currencySymbol = !empty($currency) ? $currency['currency_hex'] : "$";

    }

    public function updatedSelectedPacks()
    {
        $this->calculateTotal();
        $this->updateSessionCart();
    }

    public function calculateTotal()
    {
        $allPacks = collect($this->topAdPacks)->concat($this->featureAdPacks);
        $this->totalAmount = $allPacks->whereIn('id', $this->selectedPacks)->sum('price');
    }

    public function updateSessionCart()
    {
        $allPacks = collect($this->topAdPacks)->concat($this->featureAdPacks);
        $selectedPackDetails = $allPacks->whereIn('id', $this->selectedPacks)->keyBy('id')->toArray();
        Session::put('cart-selected-bulk-packs', $selectedPackDetails);
    }
    
    public function proceedToPayment()
    {
        if ($this->totalAmount <= 0) {
            $this->dispatchBrowserEvent('show-toast', ['message' => 'Please select at least one package.']);
            return;
        }

        // Save total amount to session for payment controller
        Session::put('payment_total_amount', $this->totalAmount);

        switch (strtolower($this->selectedPaymentMethod)) {
            case 'stripe':
                return redirect()->route('stripe.checkout');
                break;
            
            case 'paypal':
                // return redirect()->route('paypal.checkout'); // PayPal ke liye route yahan aayega
                $this->dispatchBrowserEvent('show-toast', ['message' => 'PayPal is not yet implemented.']);
                break;

            default:
                $this->dispatchBrowserEvent('show-toast', ['message' => 'Please select a valid payment method.']);
                break;
        }
    }


    public function render()
    {
        return view('livewire.buy-business-packs')
               ->layout('layouts.packagebuy');
    }
}

