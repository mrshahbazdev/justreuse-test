<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\AdminLayout::class, []); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.bulk-email-component')->html();
} elseif ($_instance->childHasBeenRendered('C3J8v0l')) {
    $componentId = $_instance->getRenderedChildComponentId('C3J8v0l');
    $componentTag = $_instance->getRenderedChildComponentTagName('C3J8v0l');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('C3J8v0l');
} else {
    $response = \Livewire\Livewire::mount('admin.bulk-email-component');
    $html = $response->html();
    $_instance->logRenderedChild('C3J8v0l', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
 <?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/admin/bulk-email.blade.php ENDPATH**/ ?>