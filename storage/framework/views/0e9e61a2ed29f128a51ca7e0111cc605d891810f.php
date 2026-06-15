<?php $__env->startSection('content'); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('front-favourite-component')->html();
} elseif ($_instance->childHasBeenRendered('N2p2fEG')) {
    $componentId = $_instance->getRenderedChildComponentId('N2p2fEG');
    $componentTag = $_instance->getRenderedChildComponentTagName('N2p2fEG');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('N2p2fEG');
} else {
    $response = \Livewire\Livewire::mount('front-favourite-component');
    $html = $response->html();
    $_instance->logRenderedChild('N2p2fEG', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontnewhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/favourite.blade.php ENDPATH**/ ?>