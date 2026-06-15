
<?php $__env->startSection('content'); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('contact-us-component')->html();
} elseif ($_instance->childHasBeenRendered('mquBsqn')) {
    $componentId = $_instance->getRenderedChildComponentId('mquBsqn');
    $componentTag = $_instance->getRenderedChildComponentTagName('mquBsqn');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('mquBsqn');
} else {
    $response = \Livewire\Livewire::mount('contact-us-component');
    $html = $response->html();
    $_instance->logRenderedChild('mquBsqn', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontendother', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/contact-us.blade.php ENDPATH**/ ?>