<?php $__env->startSection('content'); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('post-component-add')->html();
} elseif ($_instance->childHasBeenRendered('iwpX2TR')) {
    $componentId = $_instance->getRenderedChildComponentId('iwpX2TR');
    $componentTag = $_instance->getRenderedChildComponentTagName('iwpX2TR');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('iwpX2TR');
} else {
    $response = \Livewire\Livewire::mount('post-component-add');
    $html = $response->html();
    $_instance->logRenderedChild('iwpX2TR', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontnewhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/post_add.blade.php ENDPATH**/ ?>