<?php

namespace App\Http\Livewire;

use App\Models\Package;
use App\Models\TblCoupon;
use App\Models\TblPaymentsMethod;
use App\Models\TblPost;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SelectSinglePackage extends Component
{
    public TblPost $post;
    public $packages = [];
    public $paymentMethods = [];
    public $currencySymbol;

    // Form State
    public $selectedPackageId;
    public $selectedPaymentMethod;
    public $couponCode;

    // Calculated Values
    public $packageAmount = 0;
    public $discountAmount = 0;
    public $taxAmount = 0;
    public $totalAmount = 0;
    public $couponId = null;

    public function mount()
    {
        $postId = request()->query('post');
        if (!$postId) {
            abort(404, 'Post not found.');
        }

        $this->post = TblPost::findOrFail($postId);
        
        if ($this->post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $this->paymentMethods = TblPaymentsMethod::where('active', '1')->get()->toArray();
        if (!empty($this->paymentMethods)) {
            $this->selectedPaymentMethod = $this->paymentMethods[0]['name'];
        }

        $this->packages = Package::where('active', '1')->where('bulk_ads', '0')->orderBy('lft', 'asc')->get();
        if ($this->packages->isNotEmpty()) {
            $this->selectedPackageId = $this->packages->first()->id;
        }

        $currency = Setting::get_admin_default_currency();
        $this->currencySymbol = !empty($currency) ? $currency['currency_hex'] : "$";

        $this->calculateTotals();
    }

    public function updatedSelectedPackageId()
    {
        $this->applyCoupon(); // Recalculate discount for the new package
    }
    
    public function applyCoupon()
    {
        if (empty($this->couponCode)) {
            $this->resetCoupon();
            return;
        }

        $coupon = TblCoupon::where('coupon_code', $this->couponCode)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if ($coupon) {
             // You can add coupon limit logic here if needed
            $this->couponId = $coupon->id;
        } else {
            $this->resetCoupon();
            $this->dispatchBrowserEvent('show-toast', ['message' => 'Invalid or expired coupon code.']);
        }
        
        $this->calculateTotals();
    }

    private function resetCoupon()
    {
        $this->couponId = null;
        $this->discountAmount = 0;
        $this->taxAmount = 0;
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $selectedPackage = $this->packages->find($this->selectedPackageId);
        $this->packageAmount = $selectedPackage ? $selectedPackage->price : 0;

        $subTotal = $this->packageAmount;

        if ($this->couponId) {
            $coupon = TblCoupon::find($this->couponId);
            if ($coupon->type == 'fixed') {
                $this->discountAmount = $coupon->value;
            } elseif ($coupon->type == 'percentage') {
                $this->discountAmount = ($this->packageAmount * $coupon->value) / 100;
            }
            $subTotal -= $this->discountAmount;
            
            if ($coupon->tax > 0) {
                $this->taxAmount = ($subTotal * $coupon->tax) / 100;
            } else {
                $this->taxAmount = 0;
            }
        } else {
            $this->discountAmount = 0;
            $this->taxAmount = 0;
        }

        $this->totalAmount = $subTotal + $this->taxAmount;
    }
    
    public function proceedToPayment()
    {
        // Redirect to the correct payment gateway based on selection
        $package = $this->packages->find($this->selectedPackageId);
        
        $paymentUrl = '';
        if (strtolower($this->selectedPaymentMethod) == 'paypal') {
            $paymentUrl = '/paypal-payment-process';
        } elseif (strtolower($this->selectedPaymentMethod) == 'stripe') {
            $paymentUrl = '/stripe-payment';
        }
        
        if (empty($paymentUrl)) {
             $this->dispatchBrowserEvent('show-toast', ['message' => 'Please select a valid payment method.']);
             return;
        }
        
        $queryParams = http_build_query([
            'pack_amt' => $this->totalAmount,
            'cid' => $this->post->currency_id,
            'post_id' => $this->post->id,
            'live_days' => $package->duration,
            'package_id' => $package->id,
            'payment_type' => $this->selectedPaymentMethod,
            'coupon_id' => $this->couponId,
            'uid' => Auth::id(),
            'paid_for' => 'package',
        ]);
        
        return redirect()->to($paymentUrl . '?' . $queryParams);
    }


    public function render()
    {
        return view('livewire.select-single-package')
            ->layout('layouts.packagebuy');
    }
}
