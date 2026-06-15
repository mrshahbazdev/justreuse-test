<?php $__env->startSection('content'); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('stripe-payment-component')->html();
} elseif ($_instance->childHasBeenRendered('bbK9reP')) {
    $componentId = $_instance->getRenderedChildComponentId('bbK9reP');
    $componentTag = $_instance->getRenderedChildComponentTagName('bbK9reP');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('bbK9reP');
} else {
    $response = \Livewire\Livewire::mount('stripe-payment-component');
    $html = $response->html();
    $_instance->logRenderedChild('bbK9reP', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.payment', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/stripe_payment.blade.php ENDPATH**/ ?>