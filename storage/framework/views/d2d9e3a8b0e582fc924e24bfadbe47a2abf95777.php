<?php $__env->startSection('content'); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('thankyou-component')->html();
} elseif ($_instance->childHasBeenRendered('EOVnbDe')) {
    $componentId = $_instance->getRenderedChildComponentId('EOVnbDe');
    $componentTag = $_instance->getRenderedChildComponentTagName('EOVnbDe');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('EOVnbDe');
} else {
    $response = \Livewire\Livewire::mount('thankyou-component');
    $html = $response->html();
    $_instance->logRenderedChild('EOVnbDe', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.payment', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/thankyou_webview.blade.php ENDPATH**/ ?>