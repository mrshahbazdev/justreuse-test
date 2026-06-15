<?php $__env->startSection('content'); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('post-detail')->html();
} elseif ($_instance->childHasBeenRendered('E4nz0b8')) {
    $componentId = $_instance->getRenderedChildComponentId('E4nz0b8');
    $componentTag = $_instance->getRenderedChildComponentTagName('E4nz0b8');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('E4nz0b8');
} else {
    $response = \Livewire\Livewire::mount('post-detail');
    $html = $response->html();
    $_instance->logRenderedChild('E4nz0b8', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.post-detail-new', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/front_post_detail.blade.php ENDPATH**/ ?>