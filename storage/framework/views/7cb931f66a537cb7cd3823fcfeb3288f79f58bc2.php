<?php $__env->startSection('content'); ?>
    <div>
        <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('insights-component', ['postId' => request()->segment(2)])->html();
} elseif ($_instance->childHasBeenRendered('JaagUaE')) {
    $componentId = $_instance->getRenderedChildComponentId('JaagUaE');
    $componentTag = $_instance->getRenderedChildComponentTagName('JaagUaE');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('JaagUaE');
} else {
    $response = \Livewire\Livewire::mount('insights-component', ['postId' => request()->segment(2)]);
    $html = $response->html();
    $_instance->logRenderedChild('JaagUaE', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontnewhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/insights.blade.php ENDPATH**/ ?>