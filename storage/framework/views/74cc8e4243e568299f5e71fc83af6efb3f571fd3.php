<div>
    <div class="pb-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5 top-shadow">
            <!-- <button wire:click="back()" class="bg-green-500 hover:bg-orange-500 text-white py-2 px-4 rounded my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150">Back</button> -->
            <div class="bg-white px-0 xl:px-4 pt-5 pb-4 sm:p-2 sm:pb-4 text-left">
                <h1 class="text-2xl font-medium text-gray-900">Banner Advertisement</h1>
                <?php
                //$settings = App\Models\Setting::get_logos();
                //$currency_symbol = App\Models\TblPost::get_post_currency($settings['default_currency']);

                $currency_symbol = App\Models\Setting::get_admin_default_currency();
                ?>
                <?php if(!empty($currency_symbol)): ?>
                <div>
                    <div class="inline-block w-3/6">
                        <p class="bg-red-300 text-xs p-1 rounded mt-4 mb-4">Note :: Banner ads price for per day!</p>
                        <div class="flex flex-col mb-4 md:w-2/2">
                            <label class="block text-gray-700 text-sm font-bold mt-2 mb-2">Default Price :</label>
                            <div class="inline-flex">
                                <span class="p-2 border shadow mr-2"><?php echo $currency_symbol['currency_hex']; ?></span>
                                <input type="text" wire:model="default_amount" class="allow_decimal shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <?php $__errorArgs = ['default_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    
                    <div class="inline-block w-3/6">
                        <div class="flex flex-col mb-4 md:w-2/2">
                            <label class="block text-gray-700 text-sm font-bold mt-2 mb-2">Price for showing advertisement in home page</label>
                            <div class="inline-flex">
                                <span class="p-2 border shadow mr-2"><?php echo $currency_symbol['currency_hex']; ?></span>
                                <input type="text" wire:model="home_page" class="allow_decimal shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>
                    </div>
                    <div style="display:none">
                    <div class="inline-block w-3/6">
                        <div class="flex flex-col mb-4 md:w-2/2">
                            <label class="block text-gray-700 text-sm font-bold mt-2 mb-2">Price for Level 1 Category ( main category )</label>
                            <div class="inline-flex">
                                <span class="p-2 border shadow mr-2"><?php echo $currency_symbol['currency_hex']; ?></span>
                                <input type="text" wire:model="level_1" class="allow_decimal shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>
                    </div>
                    <div class="inline-block w-3/6">
                        <div class="flex flex-col mb-4 md:w-2/2">
                            <label class="block text-gray-700 text-sm font-bold mt-2 mb-2">Price for Level 2 Category ( sub category )</label>
                            <div class="inline-flex">
                                <span class="p-2 border shadow mr-2"><?php echo $currency_symbol['currency_hex']; ?></span>
                                <input type="text" wire:model="level_2" class="allow_decimal shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>
                    </div>
                    <div class="inline-block w-3/6">
                        <div class="flex flex-col mb-4 md:w-2/2">
                            <label class="block text-gray-700 text-sm font-bold mt-2 mb-2">Price for Level 3 Category ( child category )</label>
                            <div class="inline-flex">
                                <span class="p-2 border shadow mr-2"><?php echo $currency_symbol['currency_hex']; ?></span>
                                <input type="text" wire:model="level_3" class="allow_decimal shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>
                    </div>
</div>
                    <div class="inline-block w-3/6">
                        <div class="flex flex-col mb-4 md:w-2/2">
                            <p class="bg-red-300 p-1 text-xs rounded">Note :: If you not added price for home page or category means, default price will be added automatically!</p>
                        </div>
                    </div>
                </div>
                <div class="pb-2">
                    <span class="flex w-full rounded-md sm:w-auto pb-2 border-0">
                        <button wire:click.prevent="update()" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                            Update
                        </button>
                    </span>
                </div>
                <?php else: ?>
                <p class="bg-red-300 text-xs p-1 rounded mt-4 mb-4">Note :: Please update the default currency in the application settings.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        // allow decimal values only
        $(".allow_decimal").on("input", function(evt) {
            var self = $(this);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
                evt.preventDefault();
            }
        });
    </script>
</div><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/settings/banner-advertisement.blade.php ENDPATH**/ ?>