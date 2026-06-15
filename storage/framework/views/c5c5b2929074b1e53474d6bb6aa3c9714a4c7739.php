<?php $__env->startSection('content'); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('search-detail')->html();
} elseif ($_instance->childHasBeenRendered('01Ae90f')) {
    $componentId = $_instance->getRenderedChildComponentId('01Ae90f');
    $componentTag = $_instance->getRenderedChildComponentTagName('01Ae90f');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('01Ae90f');
} else {
    $response = \Livewire\Livewire::mount('search-detail');
    $html = $response->html();
    $_instance->logRenderedChild('01Ae90f', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontfilter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/front_search_detail.blade.php ENDPATH**/ ?>