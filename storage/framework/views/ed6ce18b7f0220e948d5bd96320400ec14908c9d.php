<?php 
$get_meta = App\Models\TblOtherpage::get_meta('notifications');
$meta_title = (!empty($get_meta->meta_title) ? $get_meta->meta_title : "");
$meta_keywords = (!empty($get_meta->meta_key) ? $get_meta->meta_key : "");
$meta_description = (!empty($get_meta->meta_description) ? $get_meta->meta_description : "");

$dir_rtl = App\Models\Setting::is_dir_rtl();
$class_dir = ($dir_rtl == "true") ? 'dir=rtl' : "";
?>
<?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
    <?php $__env->startSection('meta_title', $meta_title); ?>
    <?php $__env->startSection('meta_keywords', $meta_keywords); ?>
    <?php $__env->startSection('meta_description', $meta_description); ?>
<?php endif; ?>

<?php 
$user = auth()->user();
$result = $user->roles[0]->name;
if($result == "User"){
?>

<div class="min-h-screen " style="background:#fffbfa;"  <?php echo e($class_dir); ?>>
    <div class="max-w-3xl mx-auto py-6 px-4">
        <!-- Simple Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-gray-900 mb-2"><?php echo e(__('messages.read notification')); ?></h1>
            <p class="text-gray-600"><?php echo e($notifications->count()); ?> notifications</p>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            <?php if($notifications->count() > 0): ?>
                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $from_user_profile = App\Models\User::where('id', $row->from_id)->first();
                    if($from_user_profile != null) {
                        $profile_image = $from_user_profile->profile_photo_path;
                        $imgUrlfinal = (!empty($profile_image) && ($profile_image != null)) ? URL::to('storage/app/public/' . $profile_image) : URL::to('storage/noimage150.png');
                    ?>
                    
                    <div class=" border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors">
                        <div class="flex items-start gap-4">
                            <!-- Avatar -->
                            <img class="w-12 h-12 rounded-full object-cover" src="<?php echo e($imgUrlfinal); ?>" alt="<?php echo e($from_user_profile->name); ?>">
                            
                            <!-- Content -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-gray-900">
                                            <a href="<?php echo e(URL::to('seller-profile/' . $row->from_id)); ?>" target="_blank" class="font-medium text-gray-900 hover:text-blue-600">
                                                <?php echo e($from_user_profile->name); ?>

                                            </a>
                                            <span class="text-gray-700"><?php echo e($row->msg); ?></span>
                                        </p>
                                        <p class="text-gray-500 text-sm mt-1">
                                            <?php echo e(\Carbon\Carbon::parse($row['created_at'])->format('M d, Y')); ?>

                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php } ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <!-- Simple Empty State -->
                <div class="text-center py-12">
                    <div class="text-gray-400 text-4xl mb-4">🔔</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
                    <p class="text-gray-600">You don't have any notifications yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Simple Pagination -->
        <?php if($notifications->count() > 0): ?>
            <div class="mt-8 flex justify-center">
                <?php echo e($notifications->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<?php } else { ?>

<div class="min-h-screen bg-white flex items-center justify-center" <?php echo e($class_dir); ?>>
    <div class="text-center">
        <div class="text-gray-400 text-4xl mb-4">⚠️</div>
        <h3 class="text-xl font-medium text-gray-900 mb-2">Notification not available!</h3>
        <p class="text-gray-600">Please contact administrator for access.</p>
    </div>
</div>

<?php } ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/front-notifications.blade.php ENDPATH**/ ?>