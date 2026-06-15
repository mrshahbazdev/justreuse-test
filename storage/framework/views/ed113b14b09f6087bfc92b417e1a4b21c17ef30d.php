<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Complete Your Payment</h1>
        <p class="text-gray-600 mb-6">Total Amount: <span class="font-bold text-green-600">$<?php echo e(number_format($totalAmount, 2)); ?></span></p>

        <form id="payment-form">
            <div id="payment-element" class="mb-6">
                <!-- Stripe.js will inject the Payment Element here -->
            </div>
            <button id="submit" class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-700 transition">
                <div class="spinner hidden" id="spinner"></div>
                <span id="button-text">Pay Now</span>
            </button>
            <div id="payment-message" class="hidden text-red-500 text-sm mt-2"></div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe("<?php echo e(env('STRIPE_KEY')); ?>");
    const clientSecret = "<?php echo e($clientSecret); ?>";

    const options = {
        clientSecret: clientSecret,
        appearance: { theme: 'stripe' },
    };

    const elements = stripe.elements(options);
    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');

    const form = document.getElementById('payment-form');
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        setLoading(true);

        const { error } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: "<?php echo e(route('payment.success')); ?>",
            },
        });

        if (error) {
            const messageContainer = document.querySelector('#payment-message');
            messageContainer.textContent = error.message;
            messageContainer.classList.remove('hidden');
            setLoading(false);
        }
    });

    function setLoading(isLoading) {
        if (isLoading) {
            document.querySelector("#submit").disabled = true;
            document.querySelector("#spinner").classList.remove("hidden");
            document.querySelector("#button-text").classList.add("hidden");
        } else {
            document.querySelector("#submit").disabled = false;
            document.querySelector("#spinner").classList.add("hidden");
            document.querySelector("#button-text").classList.remove("hidden");
        }
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.packagebuy', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/stripe-checkout.blade.php ENDPATH**/ ?>