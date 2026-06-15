<?php $__env->startSection('content'); ?>
    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('my-followers')->html();
} elseif ($_instance->childHasBeenRendered('q4SrjSP')) {
    $componentId = $_instance->getRenderedChildComponentId('q4SrjSP');
    $componentTag = $_instance->getRenderedChildComponentTagName('q4SrjSP');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('q4SrjSP');
} else {
    $response = \Livewire\Livewire::mount('my-followers');
    $html = $response->html();
    $_instance->logRenderedChild('q4SrjSP', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontnewhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/my-connections.blade.php ENDPATH**/ ?>