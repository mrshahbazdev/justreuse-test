<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.site-layout','data' => []]); ?>
<?php $component->withName('site-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
    
     <?php $__env->slot('header'); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e($seller->name); ?>'s Profile
        </h2>
     <?php $__env->endSlot(); ?>

    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                
                <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('seller-profile', ['seller' => $seller])->html();
} elseif ($_instance->childHasBeenRendered('n40xnlk')) {
    $componentId = $_instance->getRenderedChildComponentId('n40xnlk');
    $componentTag = $_instance->getRenderedChildComponentTagName('n40xnlk');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('n40xnlk');
} else {
    $response = \Livewire\Livewire::mount('seller-profile', ['seller' => $seller]);
    $html = $response->html();
    $_instance->logRenderedChild('n40xnlk', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
            </div>
        </div>
    </div>
 <?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/seller-page.blade.php ENDPATH**/ ?>