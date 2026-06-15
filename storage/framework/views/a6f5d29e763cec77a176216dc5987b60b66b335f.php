<?php $__env->startSection('content'); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('myprofile-component')->html();
} elseif ($_instance->childHasBeenRendered('CyppQRm')) {
    $componentId = $_instance->getRenderedChildComponentId('CyppQRm');
    $componentTag = $_instance->getRenderedChildComponentTagName('CyppQRm');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('CyppQRm');
} else {
    $response = \Livewire\Livewire::mount('myprofile-component');
    $html = $response->html();
    $_instance->logRenderedChild('CyppQRm', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontnewhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/myprofile.blade.php ENDPATH**/ ?>