<div>
<style>
    /* ===== SELECT PACKAGE SCOPED CSS (spk- prefix) ===== */
    :root {
        --spk-primary: #16a34a;
        --spk-primary-dark: #15803d;
        --spk-accent: #f59e0b;
        --spk-bg: #f8fafc;
        --spk-card: #ffffff;
        --spk-text: #1e293b;
        --spk-text-muted: #64748b;
        --spk-border: #e2e8f0;
        --spk-radius: 16px;
    }

    body { background: var(--spk-bg) !important; }
    footer { background: var(--spk-bg) !important; }

    .spk-shell {
        max-width: 1000px;
        margin: 0 auto;
        padding: 24px 16px 60px;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* --- Back Link --- */
    .spk-back {
        font-size: 13px; color: var(--spk-primary);
        text-decoration: none; font-weight: 600;
        display: inline-flex; align-items: center; gap: 4px;
        margin-bottom: 20px;
    }
    .spk-back:hover { text-decoration: underline; }

    /* --- Page Title --- */
    .spk-page-title {
        font-size: 24px; font-weight: 800;
        color: var(--spk-text);
        margin: 0 0 24px;
    }

    /* --- Ad Info Card --- */
    .spk-ad-card {
        background: var(--spk-card);
        border: 1px solid var(--spk-border);
        border-radius: var(--spk-radius);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
    }
    .spk-ad-img {
        width: 72px; height: 72px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #f1f5f9;
        flex-shrink: 0;
    }
    .spk-ad-title {
        font-size: 16px; font-weight: 700;
        color: var(--spk-text); margin: 0 0 4px;
    }
    .spk-ad-price {
        font-size: 18px; font-weight: 800;
        color: var(--spk-primary); margin: 0;
    }

    /* --- Layout Grid --- */
    .spk-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 24px;
        align-items: start;
    }

    /* --- Section Card --- */
    .spk-section {
        background: var(--spk-card);
        border: 1px solid var(--spk-border);
        border-radius: var(--spk-radius);
        padding: 24px;
        margin-bottom: 20px;
    }
    .spk-section-title {
        font-size: 15px; font-weight: 700;
        color: var(--spk-text);
        margin: 0 0 16px;
        display: flex; align-items: center; gap: 8px;
    }
    .spk-section-title i {
        color: var(--spk-text-muted); font-size: 14px;
    }

    /* --- Package Cards --- */
    .spk-packages {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 12px;
    }
    .spk-pkg {
        border: 2px solid var(--spk-border);
        border-radius: 14px;
        padding: 20px 16px;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s ease;
        position: relative;
        background: var(--spk-card);
    }
    .spk-pkg:hover {
        border-color: #a7f3d0;
        background: #f0fdf4;
    }
    .spk-pkg.selected {
        border-color: var(--spk-primary);
        background: #f0fdf4;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
    }
    .spk-pkg-check {
        position: absolute;
        top: 10px; right: 10px;
        width: 24px; height: 24px;
        border-radius: 50%;
        background: var(--spk-primary);
        color: #fff;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 11px;
    }
    .spk-pkg.selected .spk-pkg-check { display: flex; }
    .spk-pkg-name {
        font-size: 14px; font-weight: 700;
        color: var(--spk-text); margin: 0 0 4px;
    }
    .spk-pkg-duration {
        font-size: 12px; color: var(--spk-text-muted);
        margin: 0 0 12px;
    }
    .spk-pkg-price {
        font-size: 24px; font-weight: 800;
        color: var(--spk-primary); margin: 0;
        line-height: 1;
    }
    .spk-pkg-free {
        display: inline-block;
        background: #d1fae5;
        color: #065f46;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 10px;
        border-radius: 20px;
        margin-top: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .spk-pkg input[type="radio"] { display: none; }

    /* --- Payment Methods --- */
    .spk-pay-methods { display: flex; flex-direction: column; gap: 8px; }
    .spk-pay-method {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border: 2px solid var(--spk-border);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        background: var(--spk-card);
    }
    .spk-pay-method:hover { border-color: #a7f3d0; }
    .spk-pay-method.selected {
        border-color: var(--spk-primary);
        background: #f0fdf4;
    }
    .spk-pay-radio {
        width: 18px; height: 18px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .spk-pay-method.selected .spk-pay-radio {
        border-color: var(--spk-primary);
    }
    .spk-pay-radio-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--spk-primary);
        display: none;
    }
    .spk-pay-method.selected .spk-pay-radio-dot { display: block; }
    .spk-pay-name {
        font-size: 14px; font-weight: 600;
        color: var(--spk-text);
    }
    .spk-pay-method input[type="radio"] { display: none; }

    /* --- Coupon --- */
    .spk-coupon-row {
        display: flex;
        gap: 8px;
        margin-top: 8px;
    }
    .spk-coupon-input {
        flex: 1;
        padding: 10px 14px;
        border: 1.5px solid var(--spk-border);
        border-radius: 10px;
        font-size: 13px;
        outline: none;
        transition: border-color 0.2s;
        background: #f8fafc;
    }
    .spk-coupon-input:focus { border-color: var(--spk-primary); }
    .spk-coupon-btn {
        padding: 10px 18px;
        background: #f1f5f9;
        border: 1.5px solid var(--spk-border);
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        color: var(--spk-text);
        cursor: pointer;
        transition: all 0.2s;
    }
    .spk-coupon-btn:hover { background: #e2e8f0; }

    /* --- Summary Sidebar --- */
    .spk-summary {
        background: var(--spk-card);
        border: 1px solid var(--spk-border);
        border-radius: var(--spk-radius);
        padding: 24px;
        position: sticky;
        top: 100px;
    }
    .spk-summary-title {
        font-size: 16px; font-weight: 700;
        color: var(--spk-text);
        margin: 0 0 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--spk-border);
    }
    .spk-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 14px;
    }
    .spk-summary-label { color: var(--spk-text-muted); }
    .spk-summary-value { font-weight: 600; color: var(--spk-text); }
    .spk-summary-discount { color: #dc2626; }
    .spk-summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0 0;
        margin-top: 12px;
        border-top: 2px solid var(--spk-border);
    }
    .spk-summary-total-label {
        font-size: 16px; font-weight: 700;
        color: var(--spk-text);
    }
    .spk-summary-total-value {
        font-size: 24px; font-weight: 800;
        color: var(--spk-primary);
    }

    /* --- Pay Button --- */
    .spk-pay-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 14px 24px;
        margin-top: 20px;
        background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .spk-pay-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(22, 163, 74, 0.25); }
    .spk-pay-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }

    /* --- Secure Badge --- */
    .spk-secure {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 14px;
        font-size: 12px;
        color: var(--spk-text-muted);
    }
    .spk-secure i { color: #16a34a; }

    /* --- Toast --- */
    .spk-toast {
        position: fixed;
        bottom: 24px; right: 24px;
        background: #1e293b;
        color: #fff;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        z-index: 9999;
        display: none;
        opacity: 0;
        transform: translateY(8px);
        transition: all 0.3s ease;
    }
    .spk-toast.show {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    /* --- Responsive --- */
    @media (max-width: 768px) {
        .spk-shell { padding: 16px 12px 40px; }
        .spk-grid { grid-template-columns: 1fr; }
        .spk-summary { position: static; }
        .spk-packages { grid-template-columns: repeat(2, 1fr); }
        .spk-page-title { font-size: 20px; }
        .spk-ad-card { flex-direction: column; text-align: center; }
    }
    @media (max-width: 480px) {
        .spk-packages { grid-template-columns: 1fr; }
    }

    .page-wrapper { min-height: 100vh; display: flex; flex-direction: column; }
    .spk-shell { flex-grow: 1; }
</style>

<div class="spk-shell">

    {{-- Back Link --}}
    <a href="{{ url('/ad/' . $post->slug) }}" class="spk-back">
        <i class="fa fa-arrow-left"></i> Back to Ad
    </a>

    <h1 class="spk-page-title">Promote Your Ad</h1>

    {{-- Ad Info Card --}}
    <div class="spk-ad-card">
        <img src="{{ \App\Models\TblChat::getPostImgForList($post->id) }}" alt="{{ $post->title }}" class="spk-ad-img">
        <div>
            <p class="spk-ad-title">{{ $post->title }}</p>
            <p class="spk-ad-price">{!! $currencySymbol !!}{{ number_format($post->price, 2) }}</p>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="spk-grid">

        {{-- Left: Packages + Payment --}}
        <div>
            {{-- Package Selection --}}
            <div class="spk-section">
                <h3 class="spk-section-title"><i class="fa fa-box-open"></i> Choose a Package</h3>
                <div class="spk-packages">
                    @foreach($packages as $pack)
                        <label wire:key="pack-{{ $pack->id }}" class="spk-pkg {{ $selectedPackageId == $pack->id ? 'selected' : '' }}">
                            <input type="radio" wire:model="selectedPackageId" value="{{ $pack->id }}">
                            <div class="spk-pkg-check"><i class="fa fa-check"></i></div>
                            <p class="spk-pkg-name">{{ $pack->name }}</p>
                            <p class="spk-pkg-duration"><i class="fa fa-clock" style="margin-right: 3px;"></i> {{ $pack->duration }} Days</p>
                            <p class="spk-pkg-price">{!! $currencySymbol !!}{{ number_format($pack->price, 2) }}</p>
                            @if($pack->price == 0)
                                <span class="spk-pkg-free">Free</span>
                            @endif
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="spk-section">
                <h3 class="spk-section-title"><i class="fa fa-credit-card"></i> Payment Method</h3>
                <div class="spk-pay-methods">
                    @foreach($paymentMethods as $method)
                        <label class="spk-pay-method {{ $selectedPaymentMethod == $method['name'] ? 'selected' : '' }}">
                            <input type="radio" wire:model="selectedPaymentMethod" name="payment_type" value="{{ $method['name'] }}">
                            <div class="spk-pay-radio"><div class="spk-pay-radio-dot"></div></div>
                            <span class="spk-pay-name">{{ $method['display_name'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: Summary Sidebar --}}
        <div class="spk-summary">
            <h3 class="spk-summary-title">Order Summary</h3>

            {{-- Coupon --}}
            <p style="font-size: 13px; font-weight: 600; color: var(--spk-text); margin: 0 0 4px;">
                <i class="fa fa-tag" style="color: var(--spk-text-muted); margin-right: 4px;"></i> Have a coupon?
            </p>
            <div class="spk-coupon-row">
                <input type="text" wire:model.lazy="couponCode" class="spk-coupon-input" placeholder="Enter code">
                <button type="button" wire:click="applyCoupon" class="spk-coupon-btn">Apply</button>
            </div>

            <div style="margin-top: 20px;">
                <div class="spk-summary-row">
                    <span class="spk-summary-label">Subtotal</span>
                    <span class="spk-summary-value">{!! $currencySymbol !!}{{ number_format($packageAmount, 2) }}</span>
                </div>
                @if($discountAmount > 0)
                <div class="spk-summary-row">
                    <span class="spk-summary-label">Discount</span>
                    <span class="spk-summary-value spk-summary-discount">- {!! $currencySymbol !!}{{ number_format($discountAmount, 2) }}</span>
                </div>
                @endif
                @if($taxAmount > 0)
                <div class="spk-summary-row">
                    <span class="spk-summary-label">Tax</span>
                    <span class="spk-summary-value">{!! $currencySymbol !!}{{ number_format($taxAmount, 2) }}</span>
                </div>
                @endif
            </div>

            <div class="spk-summary-total">
                <span class="spk-summary-total-label">Total</span>
                <span class="spk-summary-total-value">{!! $currencySymbol !!}{{ number_format($totalAmount, 2) }}</span>
            </div>

            <button wire:click="proceedToPayment" wire:loading.attr="disabled" class="spk-pay-btn">
                <span wire:loading.remove><i class="fa fa-lock"></i> Proceed to Pay</span>
                <span wire:loading><i class="fa fa-spinner fa-spin"></i> Processing...</span>
            </button>

            <div class="spk-secure">
                <i class="fa fa-shield-alt"></i>
                Secured Payment · SSL Encrypted
            </div>
        </div>

    </div>
</div>

{{-- Toast Notification --}}
<div id="spk-toast" class="spk-toast"></div>
<script>
document.addEventListener('livewire:load', function () {
    Livewire.on('show-toast', function (data) {
        var toast = document.getElementById('spk-toast');
        if (toast) {
            toast.textContent = data.message || data;
            toast.classList.add('show');
            setTimeout(function() { toast.classList.remove('show'); }, 3000);
        }
    });
});
</script>

</div>
