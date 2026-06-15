
<?php $__env->startSection('content'); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('staticpages-list')->html();
} elseif ($_instance->childHasBeenRendered('gEMyvYr')) {
    $componentId = $_instance->getRenderedChildComponentId('gEMyvYr');
    $componentTag = $_instance->getRenderedChildComponentTagName('gEMyvYr');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('gEMyvYr');
} else {
    $response = \Livewire\Livewire::mount('staticpages-list');
    $html = $response->html();
    $_instance->logRenderedChild('gEMyvYr', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontendother', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/front_static_pages.blade.php ENDPATH**/ ?>