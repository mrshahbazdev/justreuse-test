<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\AdminLayout::class, []); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
                <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.settings')->html();
} elseif ($_instance->childHasBeenRendered('ZbH2zQp')) {
    $componentId = $_instance->getRenderedChildComponentId('ZbH2zQp');
    $componentTag = $_instance->getRenderedChildComponentTagName('ZbH2zQp');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('ZbH2zQp');
} else {
    $response = \Livewire\Livewire::mount('admin.settings');
    $html = $response->html();
    $_instance->logRenderedChild('ZbH2zQp', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>       
 <?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/admin/settings.blade.php ENDPATH**/ ?>