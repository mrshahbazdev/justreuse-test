@extends('layouts.packagebuy')

@section('meta_title', 'Checkout — Stripe Payment')

@section('content')
<div class="flex items-center justify-center min-h-[60vh] p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 sm:p-12">

        @if(!empty($clientSecret))
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Complete Your Payment</h2>
            <p class="text-gray-500 mt-2">Business Package Purchase</p>
        </div>

        <div class="my-6 p-4 bg-green-50 border border-green-200 rounded-lg text-center">
            <p class="text-sm text-green-700">Total Amount</p>
            <p class="text-4xl font-bold text-green-600">{!! $currencySymbol !!}{{ number_format($totalAmount, 2) }}</p>
        </div>

        <form id="payment-form">
            <div id="payment-element" class="mb-6"></div>
            <button type="submit" id="submit-payment" class="w-full flex justify-center items-center py-3 text-lg font-bold text-white rounded-lg bg-green-600 hover:bg-green-700 transition">
                <span id="button-text">Pay Now</span>
                <span id="spinner" class="hidden"><i class="fa fa-spinner fa-spin ml-2"></i> Processing...</span>
            </button>
            <div id="payment-message" class="hidden text-red-500 text-sm mt-3 text-center"></div>
        </form>
        @else
        <div class="text-center">
            <p class="text-red-500 font-semibold">Could not initialize payment. Please go back and try again.</p>
        </div>
        @endif
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var clientSecret = @json($clientSecret ?? '');
    if (!clientSecret) return;

    var stripe = Stripe(@json($stripeKey ?? ''));
    var elements = stripe.elements({ clientSecret: clientSecret });
    var paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');

    var form = document.getElementById('payment-form');
    var submitButton = document.getElementById('submit-payment');
    var buttonText = document.getElementById('button-text');
    var spinner = document.getElementById('spinner');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        submitButton.disabled = true;
        buttonText.classList.add('hidden');
        spinner.classList.remove('hidden');

        stripe.confirmPayment({
            elements: elements,
            confirmParams: {
                return_url: "{{ route('payment.success') }}",
            },
        }).then(function (result) {
            if (result.error) {
                document.getElementById('payment-message').textContent = result.error.message;
                document.getElementById('payment-message').classList.remove('hidden');
                submitButton.disabled = false;
                buttonText.classList.remove('hidden');
                spinner.classList.add('hidden');
            }
        });
    });
});
</script>

<style>
    .StripeElement {
        box-sizing: border-box;
        height: 40px;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background-color: white;
        transition: box-shadow 150ms ease;
    }
    .StripeElement--focus { box-shadow: 0 1px 3px 0 #cfd7df; }
    .StripeElement--invalid { border-color: #fa755a; }
</style>
@endsection
