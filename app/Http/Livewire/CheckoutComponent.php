<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Setting;
use App\Models\TblPaymentsMethod;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;

class CheckoutComponent extends Component
{
    public $totalAmount;
    public $clientSecret;
    public $currencySymbol = '$';
    public $paymentDescription = 'Payment';

    public function mount(Request $request)
    {
        // Total amount aur payment ki wajah URL se hasil karein
        $this->totalAmount = $request->query('pack_amt');
        $paid_for = $request->query('paid_for');

        if (!$this->totalAmount || !is_numeric($this->totalAmount) || $this->totalAmount <= 0) {
            session()->flash('error', 'Invalid payment amount.');
            return redirect('/');
        }

        // --- YEH SAB SE ZAROORI HISSA HAI ---
        // Zaroori data (jaise post_id, package_id, uid) ko session mein save karein
        // taake payment ke baad istemal ho sake.
        Session::put('payment_data', $request->query());

        // Page par dikhane ke liye description set karein
        switch ($paid_for) {
            case 'package': $this->paymentDescription = 'Ad Promotion'; break;
            case 'buynow': $this->paymentDescription = 'Product Purchase'; break;
            case 'bannerads': $this->paymentDescription = 'Banner Advertisement'; break;
        }

        try {
            // Stripe keys database se hasil karein
            $keys = TblPaymentsMethod::where('name', "stripe")->value('keys_value');
            $stripeKeys = json_decode($keys, true);
            
            Stripe::setApiKey($stripeKeys[0]['STRIPE_SECRET_KEY'] ?? env('STRIPE_SECRET'));

            // Stripe se payment process shuru karein
            $paymentIntent = PaymentIntent::create([
                'amount' => $this->totalAmount * 100, // Amount in cents
                'currency' => 'usd', // Isay aap dynamic bana sakte hain
                'description' => $this->paymentDescription,
            ]);

            $this->clientSecret = $paymentIntent->client_secret;
            
            // Currency symbol hasil karein
            $currency = Setting::get_admin_default_currency();
            $this->currencySymbol = !empty($currency) ? $currency['currency_hex'] : "$";

        } catch (\Exception $e) {
            session()->flash('error', 'Could not initialize payment. Please check your API keys.');
            return redirect('/');
        }
    }

    public function render()
    {
        return view('livewire.checkout-component')
            ->layout('layouts.packagebuy'); // Guest layout istemal karein
    }
}

