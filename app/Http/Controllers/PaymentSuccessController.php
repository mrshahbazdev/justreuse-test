<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAdvertisement;
use App\Models\TblPayment;
use App\Models\AdZone;
use App\Models\TblPost;
use App\Models\Package;
use App\Models\TblPostedAdPackageInfo;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class PaymentSuccessController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        Log::info('=== PAYMENT SUCCESS CALLBACK START ===', ['session' => Session::all(), 'request' => $request->all()]);

        try {
            $paymentData = Session::get('payment_data');
            $adId = Session::get('payment_ad_id');

            // Handle Banner Advertisement Payment
            if ($adId) {
                return $this->handleAdvertisementPayment($adId, $request);
            }

            // Handle Post Promotion (Package) Payment
            if (isset($paymentData['paid_for']) && $paymentData['paid_for'] === 'package') {
                return $this->handlePackagePayment($paymentData, $request);
            }

            Log::warning('Payment success called but no relevant session data found.');
            return redirect('/')->with('success', 'Payment successful!');

        } catch (\Exception $e) {
            Log::error('Payment success error: ' . $e->getMessage());
            return redirect('/')->with('error', 'An error occurred while processing your payment.');
        }
    }

    private function handleAdvertisementPayment($adId, Request $request)
    {
        $advertisement = UserAdvertisement::find($adId);
        if (!$advertisement) {
            Log::error('Advertisement not found in database', ['id' => $adId]);
            return redirect()->route('my-advertisements')->with('error', 'Advertisement record not found.');
        }

        DB::transaction(function () use ($advertisement, $request) {
            $adZone = AdZone::find($advertisement->ad_zone_id);
            $status = ($adZone && $adZone->auto_approve) ? 'approved' : 'pending_approval';

            $advertisement->update([
                'payment_status' => 'paid',
                'status' => $status,
                'payment_intent_id' => $request->payment_intent,
                'paid_at' => now(),
            ]);

            if (class_exists('App\Models\TblPayment')) {
                TblPayment::create([
                    'user_id' => $advertisement->user_id,
                    'advertisement_id' => $advertisement->id,
                    'amount' => $advertisement->total_amount,
                    'payment_type' => 'stripe',
                    'transaction_id' => $request->payment_intent,
                    'status' => 'completed',
                    'active' => 1,
                ]);
            }
        });

        Session::forget(['payment_ad_id', 'payment_total_amount']);
        Log::info('Advertisement payment processed successfully.', ['ad_id' => $adId]);
        
        return view('payment.success', ['advertisement' => $advertisement]);
    }

    private function handlePackagePayment($paymentData, Request $request)
{
    DB::transaction(function() use ($paymentData, $request) {
        $post = TblPost::find($paymentData['post_id']);
        if ($post) {
            $post->active = 1;
            $post->save();
        }

        if (class_exists('App\Models\TblPayment')) {
            TblPayment::create([
                'user_id' => Auth::id(),
                'post_id' => $paymentData['post_id'],
                'package_id' => $paymentData['package_id'],
                'payment_type' => $paymentData['payment_type'],
                'payment_loc_ref_id' => $request->payment_intent, // Use payment_loc_ref_id instead of transaction_id
                'package_amount' => $paymentData['pack_amt'], // Use package_amount instead of amount
                'currency_id' => $paymentData['cid'], // Add currency_id
                'start_date' => now(), // Add start_date
                'end_date' => now()->addDays($paymentData['live_days']), // Add end_date
                'live_days' => $paymentData['live_days'], // Add live_days
                'active' => 1,
                'payment_status' => 'completed', // Add payment_status
            ]);
        }

        $package = Package::find($paymentData['package_id']);
        if ($package) {
            TblPostedAdPackageInfo::create([
                'user_id' => Auth::id(),
                'post_id' => $paymentData['post_id'],
                'ad_type' => $package->ad_type,
                'start_date' => now(),
                'end_date' => now()->addDays($package->duration),
                'active' => '1'
            ]);
        }
    });

    Session::forget('payment_data');
    Log::info('Package payment processed successfully.', ['post_id' => $paymentData['post_id']]);

    return redirect()->route('post')->with('message', 'Payment successful! Your ad has been promoted.');
}
}
