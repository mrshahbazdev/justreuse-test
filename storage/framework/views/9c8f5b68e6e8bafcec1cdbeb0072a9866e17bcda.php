
<?php $__env->startSection('content'); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('choose-multiple-package')->html();
} elseif ($_instance->childHasBeenRendered('wvemrrX')) {
    $componentId = $_instance->getRenderedChildComponentId('wvemrrX');
    $componentTag = $_instance->getRenderedChildComponentTagName('wvemrrX');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('wvemrrX');
} else {
    $response = \Livewire\Livewire::mount('choose-multiple-package');
    $html = $response->html();
    $_instance->logRenderedChild('wvemrrX', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontendother', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/choose_multiple_package.blade.php ENDPATH**/ ?>