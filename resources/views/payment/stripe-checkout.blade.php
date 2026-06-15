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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #f9fafb 50%, #ecfdf5 100%);
            color: #1f2937;
            min-height: 100vh;
        }
        .sc-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px 16px;
        }
        .sc-card {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08), 0 1px 3px rgba(0,0,0,0.04);
            padding: 40px 32px;
        }
        @media (max-width: 480px) {
            .sc-card { padding: 32px 20px; border-radius: 16px; }
        }

        /* Header */
        .sc-logo { text-align: center; margin-bottom: 8px; }
        .sc-logo img { height: 36px; }
        .sc-title {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }
        .sc-subtitle {
            text-align: center;
            font-size: 14px;
            color: #9ca3af;
            margin-bottom: 28px;
        }

        /* Amount box */
        .sc-amount-box {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 1px solid #bbf7d0;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            margin-bottom: 28px;
        }
        .sc-amount-label {
            font-size: 12px;
            font-weight: 600;
            color: #16a34a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .sc-amount-value {
            font-size: 36px;
            font-weight: 800;
            color: #15803d;
            line-height: 1.2;
        }

        /* Divider */
        .sc-divider {
            height: 1px;
            background: #f3f4f6;
            margin: 0 0 24px 0;
        }

        /* Payment element container */
        .sc-payment-wrap {
            margin-bottom: 24px;
            min-height: 200px;
        }
        .sc-payment-label {
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .sc-payment-label i { color: #16a34a; }

        /* Pay button */
        .sc-pay-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 14px 24px;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #16a34a, #15803d);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(22, 163, 74, 0.3);
            letter-spacing: 0.3px;
        }
        .sc-pay-btn:hover { background: linear-gradient(135deg, #15803d, #166534); box-shadow: 0 6px 20px rgba(22,163,74,0.35); transform: translateY(-1px); }
        .sc-pay-btn:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
        .sc-pay-btn .sc-spinner { display: none; }
        .sc-pay-btn.loading .sc-btn-text { display: none; }
        .sc-pay-btn.loading .sc-spinner { display: inline-flex; align-items: center; gap: 8px; }

        /* Error message */
        .sc-error {
            display: none;
            color: #dc2626;
            font-size: 13px;
            text-align: center;
            margin-top: 12px;
            padding: 10px;
            background: #fef2f2;
            border-radius: 8px;
            border: 1px solid #fecaca;
        }
        .sc-error.visible { display: block; }

        /* Cancel link */
        .sc-cancel {
            display: block;
            text-align: center;
            font-size: 13px;
            color: #9ca3af;
            text-decoration: none;
            margin-top: 20px;
            transition: color 0.2s;
        }
        .sc-cancel:hover { color: #6b7280; }

        /* Secure badge */
        .sc-secure {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 11px;
            color: #9ca3af;
            margin-top: 16px;
        }
        .sc-secure i { color: #16a34a; }

        /* Error state page */
        .sc-error-page { text-align: center; padding: 20px 0; }
        .sc-error-page i { font-size: 48px; color: #fca5a5; margin-bottom: 16px; }
        .sc-error-page p { color: #dc2626; font-weight: 600; font-size: 15px; margin-bottom: 16px; }
        .sc-error-page a { color: #16a34a; font-weight: 600; text-decoration: none; }
        .sc-error-page a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="sc-page">
        <div class="sc-card">

            @if(!empty($clientSecret))
                @if(!empty($settings['logo']))
                <div class="sc-logo">
                    <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Logo">
                </div>
                @endif
                <h1 class="sc-title">Complete Your Payment</h1>
                <p class="sc-subtitle">Business Package Purchase</p>

                <div class="sc-amount-box">
                    <div class="sc-amount-label">Total Amount</div>
                    <div class="sc-amount-value">{!! $currencySymbol !!}{{ number_format($totalAmount, 2) }}</div>
                </div>

                <div class="sc-divider"></div>

                <form id="payment-form">
                    <div class="sc-payment-label"><i class="fa-solid fa-lock"></i> Payment Details</div>
                    <div class="sc-payment-wrap">
                        <div id="payment-element"></div>
                    </div>
                    <button type="submit" id="submit-payment" class="sc-pay-btn">
                        <span class="sc-btn-text"><i class="fa-solid fa-shield-halved" style="margin-right:8px;"></i> Pay {!! $currencySymbol !!}{{ number_format($totalAmount, 2) }}</span>
                        <span class="sc-spinner"><i class="fa-solid fa-spinner fa-spin"></i> Processing...</span>
                    </button>
                    <div id="payment-message" class="sc-error"></div>
                </form>

                <a href="{{ url('/') }}" class="sc-cancel">Cancel and go back</a>

                <div class="sc-secure">
                    <i class="fa-solid fa-lock"></i>
                    <span>Secured by Stripe — Your payment info is encrypted</span>
                </div>
            @else
                <div class="sc-error-page">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <p>Could not initialize payment. Please go back and try again.</p>
                    <a href="{{ url('/') }}"><i class="fa-solid fa-arrow-left" style="margin-right:4px;"></i> Go to Homepage</a>
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
        var elements = stripe.elements({
            clientSecret: clientSecret,
            appearance: {
                theme: 'stripe',
                variables: {
                    colorPrimary: '#16a34a',
                    colorBackground: '#ffffff',
                    colorText: '#1f2937',
                    colorDanger: '#dc2626',
                    fontFamily: 'Inter, system-ui, sans-serif',
                    spacingUnit: '4px',
                    borderRadius: '10px'
                },
                rules: {
                    '.Input': {
                        border: '1px solid #e5e7eb',
                        boxShadow: 'none',
                        padding: '12px'
                    },
                    '.Input:focus': {
                        border: '1px solid #16a34a',
                        boxShadow: '0 0 0 3px rgba(22,163,74,0.1)'
                    },
                    '.Label': {
                        fontWeight: '500',
                        fontSize: '13px',
                        color: '#374151'
                    }
                }
            }
        });
        var paymentElement = elements.create('payment', {
            layout: 'tabs'
        });
        paymentElement.mount('#payment-element');

        var form = document.getElementById('payment-form');
        var submitButton = document.getElementById('submit-payment');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            submitButton.disabled = true;
            submitButton.classList.add('loading');

            stripe.confirmPayment({
                elements: elements,
                confirmParams: {
                    return_url: "{{ route('payment.success') }}",
                },
            }).then(function (result) {
                if (result.error) {
                    var msg = document.getElementById('payment-message');
                    msg.textContent = result.error.message;
                    msg.classList.add('visible');
                    submitButton.disabled = false;
                    submitButton.classList.remove('loading');
                }
            });
        });
    });
    </script>
</body>
</html>
