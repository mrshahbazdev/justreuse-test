<footer class=" border-t border-slate-200">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-6">
        
        
        <div>
            <a href="<?php echo e(URL::to('/')); ?>">
                <img src="<?php echo e(asset('storage/'.$settings['logo'])); ?>" alt="Site Logo" class="h-9 w-auto">
            </a>
        </div>
        
        
        <div class="text-sm text-slate-500 text-center">
            <p>&copy; <?php echo e(date('Y')); ?> JustReused. All Rights Reserved.</p>
        </div>

        
        <div class="flex items-center justify-center sm:justify-end gap-x-6 text-sm">
            <a href="#" class="font-medium text-slate-600 hover:text-primary transition-colors">About</a>
            <a href="#" class="font-medium text-slate-600 hover:text-primary transition-colors">Privacy</a>
            <a href="#" class="font-medium text-slate-600 hover:text-primary transition-colors">Terms</a>
        </div>

    </div>
</footer><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/layouts/footernewhome.blade.php ENDPATH**/ ?>