 

<?php $__env->startSection('content'); ?>
    
        <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('post-component-edit', ['postId' => request()->query('id')])->html();
} elseif ($_instance->childHasBeenRendered('aTf1Drd')) {
    $componentId = $_instance->getRenderedChildComponentId('aTf1Drd');
    $componentTag = $_instance->getRenderedChildComponentTagName('aTf1Drd');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('aTf1Drd');
} else {
    $response = \Livewire\Livewire::mount('post-component-edit', ['postId' => request()->query('id')]);
    $html = $response->html();
    $_instance->logRenderedChild('aTf1Drd', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
    
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontnewhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/post_edit.blade.php ENDPATH**/ ?>