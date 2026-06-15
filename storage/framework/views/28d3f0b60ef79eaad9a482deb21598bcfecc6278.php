<?php $dir_rtl =  App\Models\Setting::is_dir_rtl();
$class_dir = ($dir_rtl=="true")?'dir=rtl':""; ?>
<div class="js-cookie-consent cookie-consent fixed bottom-0 inset-x-0 z-40" <?php echo e($class_dir); ?>>
    <div class="bg-yellow-100 w-full py-2">
        <div class="container mx-auto px-4">
            <div class="md:flex md:items-center md:justify-between flex-wrap">
                <div class="">
                    <p class="ml-3 text-black cookie-consent__message md:text-left text-center">
                        <?php echo trans('cookie-consent::texts.message'); ?>

                    </p>
                </div>
                <div class="mt-2 flex-shrink-0 w-full sm:mt-0 sm:w-auto">
                    <a href="javascript:void()" class="js-cookie-consent-agree cookie-consent__agree cursor-pointer flex items-center justify-center px-4 lg:px-6 py-2 pb-3 rounded-md text-sm font-medium text-black bg-yellow-400 hover:bg-yellow-300 transition-all ease-linear duration-500 border border-2 border-transparent">
                        <?php echo e(trans('cookie-consent::texts.agree')); ?>

                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/vendor/cookie-consent/dialogContents.blade.php ENDPATH**/ ?>