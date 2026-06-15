
<?php $__env->startSection('content'); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('choose-package-step-one')->html();
} elseif ($_instance->childHasBeenRendered('001uKAE')) {
    $componentId = $_instance->getRenderedChildComponentId('001uKAE');
    $componentTag = $_instance->getRenderedChildComponentTagName('001uKAE');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('001uKAE');
} else {
    $response = \Livewire\Livewire::mount('choose-package-step-one');
    $html = $response->html();
    $_instance->logRenderedChild('001uKAE', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontendother', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/choose_package_step_one.blade.php ENDPATH**/ ?>