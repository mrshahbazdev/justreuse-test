<div>
    <?php $__env->startPush('styles'); ?>
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
        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }
        .StripeElement--invalid {
            border-color: #fa755a;
        }
    </style>
    <?php $__env->stopPush(); ?>

    <div class="flex items-center justify-center min-h-screen p-4 bg-gray-50">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 sm:p-12">
            
            <?php if($clientSecret): ?>
            <div wire:ignore>
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Complete Your Payment</h2>
                    <p class="text-gray-500 mt-2"><?php echo e($paymentDescription); ?></p>
                </div>
                <div class="my-6 p-4 bg-green-50 border border-green-200 rounded-lg text-center">
                    <p class="text-sm text-green-700">Total Amount</p>
                    <p class="text-4xl font-bold text-green-600"><?php echo $currencySymbol; ?><?php echo e(number_format($totalAmount, 2)); ?></p>
                </div>
                
                
                <form id="payment-form">
                    <div id="payment-element" class="mb-6">
                        <!-- Stripe ka secure payment form yahan aayega -->
                    </div>
                    <button type="submit" id="submit-payment" class="w-full flex justify-center items-center py-3 text-lg font-bold text-white rounded-lg bg-green-600 hover:bg-green-700 transition">
                        <span id="button-text">Pay Now</span>
                        <span id="spinner" class="hidden"><i class="fa fa-spinner fa-spin"></i> Processing...</span>
                    </button>
                    <div id="payment-message" class="hidden text-red-500 text-sm mt-2 text-center"></div>
                </form>
            </div>
            <?php else: ?>
            <div class="text-center">
                 <p class="text-red-500 font-semibold">Could not initialize payment. Please go back and try again.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            const clientSecret = <?php echo json_encode($clientSecret, 15, 512) ?>;
            if (!clientSecret) return;

            const stripe = Stripe('<?php echo e(config("services.stripe.key")); ?>');
            const elements = stripe.elements({ clientSecret });
            const paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');
            
            const form = document.getElementById('payment-form');
            const submitButton = document.getElementById('submit-payment');
            const buttonText = document.getElementById('button-text');
            const spinner = document.getElementById('spinner');

            // Form ke 'submit' event par listen karein, na ke button ke 'click' par
            form.addEventListener('submit', async (e) => {
                e.preventDefault(); // Default form submission ko rokein
                
                submitButton.disabled = true;
                buttonText.classList.add('hidden');
                spinner.classList.remove('hidden');

                const { error } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: "<?php echo e(route('payment.success')); ?>",
                    },
                });

                if (error) {
                    document.getElementById('payment-message').textContent = error.message;
                    document.getElementById('payment-message').classList.remove('hidden');
                    submitButton.disabled = false;
                    buttonText.classList.remove('hidden');
                    spinner.classList.add('hidden');
                }
            });
        });
    </script>
</div>

<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/checkout-component.blade.php ENDPATH**/ ?>