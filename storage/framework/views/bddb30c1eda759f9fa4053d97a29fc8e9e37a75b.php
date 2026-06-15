<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\AdminLayout::class, []); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.otherpages')->html();
} elseif ($_instance->childHasBeenRendered('zow7mGd')) {
    $componentId = $_instance->getRenderedChildComponentId('zow7mGd');
    $componentTag = $_instance->getRenderedChildComponentTagName('zow7mGd');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('zow7mGd');
} else {
    $response = \Livewire\Livewire::mount('admin.otherpages');
    $html = $response->html();
    $_instance->logRenderedChild('zow7mGd', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
 <?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/admin/otherpages.blade.php ENDPATH**/ ?>