<?php if (isset($component)) { $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\GuestLayout::class, []); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
<?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('login-with-otp')->html();
} elseif ($_instance->childHasBeenRendered('G9g5mpp')) {
    $componentId = $_instance->getRenderedChildComponentId('G9g5mpp');
    $componentTag = $_instance->getRenderedChildComponentTagName('G9g5mpp');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('G9g5mpp');
} else {
    $response = \Livewire\Livewire::mount('login-with-otp');
    $html = $response->html();
    $_instance->logRenderedChild('G9g5mpp', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
 <?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/front_login_otp.blade.php ENDPATH**/ ?>