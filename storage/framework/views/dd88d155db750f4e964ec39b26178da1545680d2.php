<?php $__env->startSection('content'); ?>
    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('favourite-ads')->html();
} elseif ($_instance->childHasBeenRendered('08B8oZX')) {
    $componentId = $_instance->getRenderedChildComponentId('08B8oZX');
    $componentTag = $_instance->getRenderedChildComponentTagName('08B8oZX');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('08B8oZX');
} else {
    $response = \Livewire\Livewire::mount('favourite-ads');
    $html = $response->html();
    $_instance->logRenderedChild('08B8oZX', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontnewhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/my-favourites.blade.php ENDPATH**/ ?>