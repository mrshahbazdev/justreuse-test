@php
    $settings = App\Models\Setting::get_logos();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout — Stripe Payment</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <style>
        html, body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f9fafb;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }
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
</head>
<body>
    <div class="flex items-center justify-center min-h-screen p-4">
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

            <a href="{{ url('/') }}" class="block text-center text-sm text-gray-400 mt-6 hover:text-gray-600 transition">Cancel and go back</a>
            @else
            <div class="text-center">
                <p class="text-red-500 font-semibold">Could not initialize payment. Please go back and try again.</p>
                <a href="{{ url('/') }}" class="inline-block mt-4 text-green-600 hover:text-green-700 font-medium">Go to Homepage</a>
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
</body>
</html>
