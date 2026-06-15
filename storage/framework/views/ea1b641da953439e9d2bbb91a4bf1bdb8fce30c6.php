<?php $__env->startSection('content'); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('front-notification-component')->html();
} elseif ($_instance->childHasBeenRendered('RuFuQwK')) {
    $componentId = $_instance->getRenderedChildComponentId('RuFuQwK');
    $componentTag = $_instance->getRenderedChildComponentTagName('RuFuQwK');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('RuFuQwK');
} else {
    $response = \Livewire\Livewire::mount('front-notification-component');
    $html = $response->html();
    $_instance->logRenderedChild('RuFuQwK', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontnewhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/notifications.blade.php ENDPATH**/ ?>