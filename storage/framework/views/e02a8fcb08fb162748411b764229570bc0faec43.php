<?php $__env->startSection('content'); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('user-landing-component')->html();
} elseif ($_instance->childHasBeenRendered('eXvDqSu')) {
    $componentId = $_instance->getRenderedChildComponentId('eXvDqSu');
    $componentTag = $_instance->getRenderedChildComponentTagName('eXvDqSu');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('eXvDqSu');
} else {
    $response = \Livewire\Livewire::mount('user-landing-component');
    $html = $response->html();
    $_instance->logRenderedChild('eXvDqSu', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontnewhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/landing.blade.php ENDPATH**/ ?>