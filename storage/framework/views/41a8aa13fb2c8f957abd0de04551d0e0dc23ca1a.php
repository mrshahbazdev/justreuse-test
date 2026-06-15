<?php $__env->startSection('content'); ?>
    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('my-orders-sales')->html();
} elseif ($_instance->childHasBeenRendered('H0S4iPd')) {
    $componentId = $_instance->getRenderedChildComponentId('H0S4iPd');
    $componentTag = $_instance->getRenderedChildComponentTagName('H0S4iPd');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('H0S4iPd');
} else {
    $response = \Livewire\Livewire::mount('my-orders-sales');
    $html = $response->html();
    $_instance->logRenderedChild('H0S4iPd', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontnewhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/my-orders-sales.blade.php ENDPATH**/ ?>