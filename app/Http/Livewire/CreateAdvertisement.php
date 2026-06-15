<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\AdZone;
use App\Models\AdTemplate;
use App\Models\UserAdvertisement;
use App\Models\Setting;
use App\Models\TblPaymentsMethod;
use Livewire\WithFileUploads;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;

class CreateAdvertisement extends Component
{
    use WithFileUploads;

    public $currentStep = 1;
    public $ad_zone_id;
    public $start_date;
    public $end_date;
    public $ad_template_id;
    public $selectedPaymentMethod;
    public $headline, $subtitle, $link, $cta_text = 'Learn More', $image, $logo;
    public $totalAmount = 0, $pricePerDay = 0, $liveDays = 0;
    public $clientSecret;
    public $previewHtml = '';
    public $currencySymbol = '$';
    public $paymentAmountDisplay = '0.00';
    public $templatePlaceholders = []; // Yeh template ke placeholders save karega

    public function mount()
    {
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addDays(7)->format('Y-m-d');
        $currency = Setting::get_admin_default_currency();
        $this->currencySymbol = !empty($currency) ? $currency['currency_hex'] : "$";
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'ad_template_id') {
            $this->updateTemplatePlaceholders();
        }

        if(in_array($propertyName, ['headline', 'link', 'cta_text', 'image', 'logo', 'subtitle'])) {
            $this->validateOnly($propertyName);
        }

        if(in_array($propertyName, ['ad_zone_id', 'start_date', 'end_date'])) {
            $this->calculateTotals();
        }
        $this->generatePreview();
    }
    
    private function updateTemplatePlaceholders()
    {
        if (!$this->ad_template_id) {
            $this->templatePlaceholders = [];
            return;
        }
        $template = AdTemplate::find($this->ad_template_id);
        if ($template) {
            preg_match_all('/__([A-Z_]+)__/', $template->html_content, $matches);
            $this->templatePlaceholders = $matches[0] ?? [];
        } else {
            $this->templatePlaceholders = [];
        }
    }

    protected function rules()
    {
        $rules = [
            'ad_zone_id' => 'required|exists:ad_zones,id',
            'ad_template_id' => 'required|exists:ad_templates,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'selectedPaymentMethod' => 'required',
        ];

        // Rules ko dynamically add karein
        if (in_array('__HEADLINE__', $this->templatePlaceholders)) $rules['headline'] = 'required|string|max:100';
        if (in_array('__SUBTITLE__', $this->templatePlaceholders)) $rules['subtitle'] = 'nullable|string|max:150';
        if (in_array('__LINK__', $this->templatePlaceholders)) $rules['link'] = 'required|url';
        if (in_array('__CTA_TEXT__', $this->templatePlaceholders)) $rules['cta_text'] = 'required|string|max:25';
        if (in_array('__IMAGE_URL__', $this->templatePlaceholders)) $rules['image'] = 'required|image|max:2048';
        if (in_array('__LOGO_URL__', $this->templatePlaceholders)) $rules['logo'] = 'nullable|image|max:1024';

        return $rules;
    }

    public function calculateTotals()
    {
        $zone = AdZone::find($this->ad_zone_id);
        if ($zone && $this->start_date && $this->end_date) {
            $this->pricePerDay = $zone->price_per_day;
            try {
                $start = Carbon::parse($this->start_date);
                $end = Carbon::parse($this->end_date);
                $this->liveDays = $start->diffInDays($end);
                $this->liveDays = $this->liveDays > 0 ? $this->liveDays : 0;
            } catch (\Exception $e) {
                $this->liveDays = 0;
            }
            $this->totalAmount = $this->liveDays * $this->pricePerDay;
            $this->paymentAmountDisplay = number_format($this->totalAmount, 2);
        } else {
            $this->totalAmount = 0;
            $this->pricePerDay = 0;
            $this->liveDays = 0;
            $this->paymentAmountDisplay = '0.00';
        }
    }

    public function generatePreview()
    {
        if (!$this->ad_template_id) {
            $this->previewHtml = '<div style="text-align: center; padding: 20px; color: #9ca3af;">Select a template to see preview</div>';
            return;
        }

        $template = AdTemplate::find($this->ad_template_id);
        $zone = AdZone::find($this->ad_zone_id);

        if (!$template || !$zone) return;

        $html = $template->html_content;
        $specs = $zone->specifications;
        $width = (isset($specs['width']) && is_numeric($specs['width'])) ? $specs['width'] . 'px' : 'auto';
        $height = (isset($specs['height']) && is_numeric($specs['height'])) ? $specs['height'] . 'px' : 'auto';

        $imageUrl = ($this->image instanceof \Livewire\TemporaryUploadedFile)
            ? $this->image->temporaryUrl()
            : 'https://placehold.co/600x400/e2e8f0/e2e8f0?text=Image';

        $logoUrl = ($this->logo instanceof \Livewire\TemporaryUploadedFile)
            ? $this->logo->temporaryUrl()
            : 'https://placehold.co/40x40/e2e8f0/e2e8f0?text=Logo';

        $replacements = [
            '__IMAGE_URL__' => $imageUrl,
            '__HEADLINE__' => e($this->headline ?: 'Your Ad Headline'),
            '__SUBTITLE__' => e($this->subtitle ?: 'A catchy subtitle for your ad.'),
            '__LINK__' => e($this->link ?: '#'),
            '__CTA_TEXT__' => e($this->cta_text ?: 'Learn More'),
            '__LOGO_URL__' => $logoUrl,
            '__WIDTH__' => $width,
            '__HEIGHT__' => $height,
        ];

        $this->previewHtml = str_replace(array_keys($replacements), array_values($replacements), $html);
    }
    
    public function goToStep($step)
    {
        if ($step == 2) {
             $this->validate(['ad_zone_id' => 'required', 'ad_template_id' => 'required']);
        }
        $this->currentStep = $step;
    }

    // In your Livewire component - Update the createAdvertisementAndProceedToPayment method
    public function createAdvertisementAndProceedToPayment()
    {
        $this->validate(); // Dynamic rules yahan istemal honge
        $this->calculateTotals();

        if ($this->totalAmount <= 0) {
            $this->dispatchBrowserEvent('show-toast', ['message' => 'Total amount must be greater than zero.']);
            return;
        }

        try {
            $content = [];
            if ($this->image && in_array('__IMAGE_URL__', $this->templatePlaceholders)) $content['image'] = $this->image->store('user_ads', 'public');
            if ($this->logo && in_array('__LOGO_URL__', $this->templatePlaceholders)) $content['logo'] = $this->logo->store('user_ad_logos', 'public');
            if ($this->headline && in_array('__HEADLINE__', $this->templatePlaceholders)) $content['headline'] = $this->headline;
            if ($this->subtitle && in_array('__SUBTITLE__', $this->templatePlaceholders)) $content['subtitle'] = $this->subtitle;
            if ($this->link && in_array('__LINK__', $this->templatePlaceholders)) $content['link'] = $this->link;
            if ($this->cta_text && in_array('__CTA_TEXT__', $this->templatePlaceholders)) $content['cta_text'] = $this->cta_text;

            $advertisement = UserAdvertisement::create([
                'user_id' => auth()->id(),
                'ad_zone_id' => $this->ad_zone_id,
                'ad_template_id' => $this->ad_template_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'total_amount' => $this->totalAmount,
                'status' => 'pending_payment',
                'content' => $content,
                'payment_status' => 'pending',
                'payment_type' => $this->selectedPaymentMethod,
            ]);

            Session::put('payment_ad_id', $advertisement->id);

            if (strtolower($this->selectedPaymentMethod) === 'stripe') {
                Stripe::setApiKey(config('services.stripe.secret'));
                $paymentIntent = PaymentIntent::create([
                    'amount' => $this->totalAmount * 100, 
                    'currency' => 'usd',
                    'metadata' => ['advertisement_id' => $advertisement->id]
                ]);
                $this->clientSecret = $paymentIntent->client_secret;
                $this->currentStep = 3;

                // Fix: Pass the correct data to the browser event
                $this->dispatchBrowserEvent('stripe-init', [
                    'clientSecret' => $this->clientSecret,
                    'amountDisplay' => $this->paymentAmountDisplay,
                    'currencySymbol' => $this->currencySymbol
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Ad Creation Failed: ' . $e->getMessage());
            $this->dispatchBrowserEvent('show-toast', ['message' => 'An error occurred. Please try again.']);
        }
    }
	public function handlePaymentSuccess($paymentIntentId)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $intent = PaymentIntent::retrieve($paymentIntentId);

            if ($intent->status === 'succeeded') {
                $adId = $intent->metadata['advertisement_id'] ?? null;
                $advertisement = UserAdvertisement::find($adId);

                if ($advertisement && $advertisement->payment_status === 'pending') {
                    $advertisement->update([
                        'status'            => 'approved',
                        'payment_status'    => 'paid',
                        'payment_intent_id' => $intent->id,
                        'paid_at'           => now(),
                    ]);
                    
                    $this->successfulAdvertisement = $advertisement;
                    $this->currentStep = 4;
                    Session::forget('payment_ad_id');
                }
            }
        } catch(\Exception $e) {
            Log::error('Payment Success Handling Failed: ' . $e->getMessage());
            $this->stripeError = 'There was an issue confirming your payment. Please contact support.';
        }
    }
    public function render()
    {
        $adZones = AdZone::where('is_active', true)->get();
        $templates = $this->ad_zone_id ? AdTemplate::where('ad_zone_id', $this->ad_zone_id)->where('is_active', true)->get() : [];
        $paymentMethods = TblPaymentsMethod::where('active', '1')->get()->toArray();
        
        if(!empty($paymentMethods) && !$this->selectedPaymentMethod) {
            $this->selectedPaymentMethod = $paymentMethods[0]['name'];
        }
        
        return view('livewire.create-advertisement', [
            'adZones' => $adZones, 
            'templates' => $templates, 
            'paymentMethods' => $paymentMethods
        ])->layout('layouts.packagebuy');
    }
}

