<?php $__env->startSection('content'); ?>

<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('post-component')->html();
} elseif ($_instance->childHasBeenRendered('NzedcJw')) {
    $componentId = $_instance->getRenderedChildComponentId('NzedcJw');
    $componentTag = $_instance->getRenderedChildComponentTagName('NzedcJw');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('NzedcJw');
} else {
    $response = \Livewire\Livewire::mount('post-component');
    $html = $response->html();
    $_instance->logRenderedChild('NzedcJw', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontnewhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/post.blade.php ENDPATH**/ ?>