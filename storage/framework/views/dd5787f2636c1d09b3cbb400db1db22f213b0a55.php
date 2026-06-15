<div>
    <div class="pb-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5 top-shadow">
            <!-- <button wire:click="back()" class="bg-green-500 hover:bg-orange-500 text-white py-2 px-4 rounded my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150">Back</button> -->
            <div class="bg-white px-0 xl:px-4 pt-5 pb-4 sm:p-2 sm:pb-4 text-left">
                <div class="flex flex-col mb-4 md:w-1/2">
                    <h1 class="text-2xl font-medium text-gray-900">Application Setup</h1>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Purchase Code :</label>
                    <input type="text" wire:model="purchase_code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <small>Get your Purchase Code here.</small>
                    <?php $__errorArgs = ['purchase_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Site title : <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="site_title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Meta Title : <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="meta_title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php $__errorArgs = ['meta_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Meta description : <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="meta_desc" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php $__errorArgs = ['meta_desc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Meta Keywords : <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="meta_keywords" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php $__errorArgs = ['meta_keywords'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email : <span class="text-red-500">*</span></label>
                    <input type="email" wire:model="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">SMTP mail username : <span class="text-red-500">*</span></label>
                    <input type="email" wire:model="smtp_mail_username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php $__errorArgs = ['smtp_mail_username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">SMTP mail password : <span class="text-red-500">*</span></label>
                    <input type="password" wire:model="smtp_mail_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php $__errorArgs = ['smtp_mail_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Google Api Key : <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="google_api_key" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php $__errorArgs = ['google_api_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Google Recaptcha SiteKey : <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="google_recaptcha_sitekey" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php $__errorArgs = ['google_recaptcha_sitekey'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Google Recaptcha SecretKey : <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="google_recaptcha_secret" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php $__errorArgs = ['google_recaptcha_secret'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">phone : <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Logo :</label>
                    <img width="60px" class="" src="<?php echo e(URL::to('/storage/'.$this->app_logo_view)); ?>">
                    <input type="file" wire:model="app_logo" id="app_logo">
                    <p>Image Size (265px X 90px)</p>
                    <?php $__errorArgs = ['app_logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Admin Logo :</label>
                    <img width="60px" class="" src="<?php echo e(URL::to('/storage/'.$admin_logo_view)); ?>">
                    <input type="file" wire:model="admin_logo" id="admin_logo">
                    <p>Image Size (158px X 130px)</p>
                    <?php $__errorArgs = ['admin_logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Favicon :</label>
                    <img width="30px" class="" src="<?php echo e(URL::to('/storage/'.$this->favicon_view)); ?>"><br>
                    <input type="file" wire:model="favicon" id="favicon">
                    <p>Image Size (16px X 16px)</p>
                    <?php $__errorArgs = ['favicon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Watermark Image :</label>
                    <img width="60px" class="" src="<?php echo e(URL::to('/storage/'.$this->app_watermark_view)); ?>">
                    <input type="file" wire:model="app_watermark" id="app_watermark">
                    <p>Image Size (32px X 32px)</p>
                    <?php $__errorArgs = ['app_watermark'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Default Currency: ( Currency list comming from currency management )</label>
                    <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" wire:model="default_currency" required="">
                        <option value="">[-- Select Default currency --]</option>
                        <?php
                        $currency = App\Models\TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
                        foreach ($currency as $currency) {
                        ?>
                            <option value="<?php echo $currency->id ?>"><?php echo $currency->currency_hex; ?> - <?php echo $currency->short_code; ?></option>
                        <?php } ?>
                    </select>
                    <p class="bg-red-300 text-xs p-1 rounded mt-4 mb-4">Note :: Based on this currency package payment & banner advertisement payment will be done!</p>
                    <?php $__errorArgs = ['default_currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    
                </div>
				
                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Grid Setup: ( Landing Page )</label>
                    <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" wire:model="grid_setup">
                    <option value="4">Four Ads / Per Row</option>
                    <option value="5">Five Ads / Per Row</option>
                    <option value="6">Six Ads / Per Row</option>
                    </select>
                </div>
				
				
                <div class="p-2">
                    <span class="flex w-full rounded-md sm:w-auto pb-2 border-0">
                        <button wire:click.prevent="update()" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                            Update
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <script>
$(document).ready(function(){
    var _URL = window.URL || window.webkitURL;

    $('#app_logos').change(function () {
        var imgwidth1 = 0;
        var imgheight1 = 0;
        var wb_file  =  $(this)[0].files[0];
        var x_img = new Image();
        x_img.src = _URL.createObjectURL(wb_file);
        x_img.onload = function() {
             imgwidth1 = this.width;
             imgheight1 = this.height;
			 if(parseFloat(imgwidth1)!=265)
			 {
				 alert("Improper image size uploaded.\nApplogo Image For Website (265px X 90px)");
				 $("#app_logo").val("");
                 return false;
			 }
             else if(parseFloat(imgheight1)!=90)
             {
                alert("Improper image size uploaded.\nApplogo Image For Website (265px X 90px)");
				 $("#app_logo").val("");
                 return false;
             }
        };
    });

   //admin logo

   $('#admin_logo').change(function () {
        var imgwidth1 = 0;
        var imgheight1 = 0;
        var wb_file  =  $(this)[0].files[0];
        var x_img = new Image();
        x_img.src = _URL.createObjectURL(wb_file);
        x_img.onload = function() {
             imgwidth1 = this.width;
             imgheight1 = this.height;
			 if(parseFloat(imgwidth1)!=158)
			 {
				 alert("Improper image size uploaded.\nAdmin logo Image For Website (158px X 130px)");
				 $("#admin_logo").val("");
                 return false;
			 }
             else if(parseFloat(imgheight1)!=130)
             {
                alert("Improper image size uploaded.\nAdmin logo Image For Website (158px X 130px)");
				 $("#admin_logo").val("");
                 return false;
             }
        };
    });


// favicon

    $('#favicon').change(function () {
        var imgwidth1 = 0;
        var imgheight1 = 0;
        var wb_file  =  $(this)[0].files[0];
        var x_img = new Image();
        x_img.src = _URL.createObjectURL(wb_file);
        x_img.onload = function() {
             imgwidth1 = this.width;
             imgheight1 = this.height;
			 if(parseFloat(imgwidth1)!=16)
			 {
				 alert("Improper image size uploaded.\nFavicon Image For Website (16px X 16px)");
				 $("#favicon").val("");
                 return false;
			 }
             else if(parseFloat(imgheight1)!=16)
             {
                alert("Improper image size uploaded.\nFavicon Image For Website (16px X 16px)");
				 $("#favicon").val("");
                 return false;
             }
        };
    });


//watermark
$('#app_watermarkss').change(function () {
        var imgwidth1 = 0;
        var imgheight1 = 0;
        var wb_file  =  $(this)[0].files[0];
        var x_img = new Image();
        x_img.src = _URL.createObjectURL(wb_file);
        x_img.onload = function() {
             imgwidth1 = this.width;
             imgheight1 = this.height;
			 if(parseFloat(imgwidth1)!=32)
			 {
				 alert("Improper image size uploaded.\nWatermark Image For Website (32px X 32px)");
				 $("#app_watermark").val("");
                 return false;
			 }
             else if(parseFloat(imgheight1)!=32)
             {
                alert("Improper image size uploaded.\nWatermark Image For Website (32px X 32px)");
				 $("#app_watermark").val("");
                 return false;
             }
        };
    });

});
</script>


</div><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/settings/app.blade.php ENDPATH**/ ?>