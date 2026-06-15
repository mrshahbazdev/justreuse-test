<?php $__env->startSection('content'); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('packages-list')->html();
} elseif ($_instance->childHasBeenRendered('mt7HC3X')) {
    $componentId = $_instance->getRenderedChildComponentId('mt7HC3X');
    $componentTag = $_instance->getRenderedChildComponentTagName('mt7HC3X');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('mt7HC3X');
} else {
    $response = \Livewire\Livewire::mount('packages-list');
    $html = $response->html();
    $_instance->logRenderedChild('mt7HC3X', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontendother', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/front_packages_detail.blade.php ENDPATH**/ ?>