<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAdvertisement;
use App\Models\TblPayments;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function success(Request $request)
    {
        Log::info('=== PAYMENT SUCCESS CALLBACK START ===');
        Log::info('Payment success callback received', [
            'payment_intent' => $request->payment_intent,
            'redirect_status' => $request->redirect_status,
            'all_params' => $request->all(),
            'session_data' => Session::all()
        ]);

        try {
            // Get the advertisement ID from session
            $advertisementId = Session::get('payment_ad_id');
            
            Log::info('Looking for advertisement ID:', ['advertisement_id' => $advertisementId]);
            
            if (!$advertisementId) {
                Log::error('No advertisement ID found in session');
                return redirect()->route('advertisements.index')->with('error', 'Advertisement not found.');
            }

            // Find the advertisement with detailed logging
            $advertisement = UserAdvertisement::find($advertisementId);
            
            Log::info('Advertisement found:', [
                'exists' => !is_null($advertisement),
                'current_status' => $advertisement ? $advertisement->status : 'NOT FOUND',
                'current_payment_status' => $advertisement ? $advertisement->payment_status : 'NOT FOUND'
            ]);
            
            if (!$advertisement) {
                Log::error('Advertisement not found in database', ['id' => $advertisementId]);
                return redirect()->route('advertisements.index')->with('error', 'Advertisement not found.');
            }

            // Update advertisement status using DB transaction for safety
            DB::beginTransaction();
            
            try {
                $updateData = [
                    'payment_status' => 'completed',
                    'status' => 'active',
                    'payment_intent_id' => $request->payment_intent,
                    'paid_at' => now(),
                ];
                
                Log::info('Attempting to update advertisement with data:', $updateData);
                
                $updated = $advertisement->update($updateData);
                
                Log::info('Update result:', ['updated' => $updated]);
                
                // Refresh the model to get updated data
                $advertisement->refresh();
                
                Log::info('After update - advertisement status:', [
                    'status' => $advertisement->status,
                    'payment_status' => $advertisement->payment_status,
                    'payment_intent_id' => $advertisement->payment_intent_id,
                    'paid_at' => $advertisement->paid_at
                ]);
                
                DB::commit();
                Log::info('Database transaction committed successfully');
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Database update failed: ' . $e->getMessage());
                throw $e;
            }

            // Create payment record
            $this->createPaymentRecord($advertisement, $request);

            // Clear the session
            Session::forget('payment_ad_id');
            Log::info('Session cleared');

            Log::info('=== PAYMENT SUCCESS CALLBACK END ===');
            
            return view('payment.success', [
                'advertisement' => $advertisement,
                'payment_intent' => $request->payment_intent
            ]);

        } catch (\Exception $e) {
            Log::error('Payment success error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('advertisements.index')->with('error', 'Error processing payment success.');
        }
    }

    protected function createPaymentRecord($advertisement, $request)
    {
        try {
            // If you have a payments table, create record here
            if (class_exists('App\Models\TblPayments')) {
                TblPayments::create([
                    'user_id' => $advertisement->user_id,
                    'advertisement_id' => $advertisement->id,
                    'amount' => $advertisement->total_amount,
                    'payment_method' => 'stripe',
                    'payment_intent_id' => $request->payment_intent,
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);
                Log::info('Payment record created successfully');
            }
        } catch (\Exception $e) {
            Log::error('Error creating payment record: ' . $e->getMessage());
        }
    }
}