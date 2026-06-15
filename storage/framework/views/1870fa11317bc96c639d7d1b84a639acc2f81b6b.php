
<?php $__env->startSection('content'); ?>
<div class="w-full float-left mb-3 sm:mb-6 py-8">
<div class="container mx-auto px-4">
    <h1 class="text-xl md:text-2xl font-bold text-black text-center uppercase">Sitemap</h1>

    <?php if(!empty($pages)): ?>
    <h4 class="text-lg sm:text-xl lg:text-xl font-bold text-black uppercase mt-6 mb-4">Pages</h4>
   <ul class="marker:text-sky-400 list-disc pl-5 space-y-3 text-base md:text-lg font-normal text-slate-500">
       
        <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $pageurl = URL::to($page->slug)?>
        <li><a href="<?php echo e($pageurl); ?>"><?php echo e($page->title); ?></a></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        
    </ul>
    <?php endif; ?>
    <?php if(!empty($staticpages)): ?>
        <h4 class="text-lg sm:text-xl lg:text-xl font-bold text-black uppercase mt-6 mb-4">Staticpages</h4>
    <ul class="marker:text-sky-400 list-disc pl-5 space-y-3 text-base md:text-lg font-normal text-slate-500">
       
        <?php $__currentLoopData = $staticpages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $staticpageurl = URL::to('pages/'.$page->slug)?>
        <li><a href="<?php echo e($staticpageurl); ?>"><?php echo e($page->title); ?></a></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      
        </ul>
        <?php endif; ?>
        <?php if(!empty($categories)): ?>
        <h4 class="text-lg sm:text-xl lg:text-xl font-bold text-black uppercase mt-6 mb-4">Category</h4>
        <ul class="marker:text-sky-400 list-disc pl-5 space-y-3 text-base md:text-lg font-normal text-slate-500">
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mainSlug => $mainData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
            <?php $catUrl =  URL::to($mainSlug);?>
                <a href="<?php echo e($catUrl); ?>"><?php echo e($mainData['title']); ?></a>
                <?php if(!empty($mainData['subcategories'])): ?>
                <ul class="marker:text-sky-400 list-disc pl-5 space-y-3 text-base md:text-lg font-normal text-slate-500">
                        <?php $__currentLoopData = $mainData['subcategories']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $subcatUrl =  URL::to($subData['slug']);?>
                            <li><a href="<?php echo e($subcatUrl); ?>"><?php echo e($subData['title']); ?></a></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <?php endif; ?>

   </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/sitemap.blade.php ENDPATH**/ ?>