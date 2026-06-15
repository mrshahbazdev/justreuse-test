<div>
    <div class="py-4">
        <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white overflow-hidden top-shadow rounded-md px-6 py-5">
			<div class="w-full  bg-gray-100  pl-2 pr-2 mb-2 rounded-sm">
            <button wire:click="back()" class="bg-green-500 hover:bg-orange-500 text-white  py-2 px-4 rounded-sm my-3">Back</button>
			</div>
            <div class="bg-gray-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left border rounded shadow mt-4">
                <div class="flex flex-col mb-4 md:w-1/2">
                    <h1 class="text-2xl font-medium text-gray-900 mb-3">Create Package</h1>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                    <input type="text" wire:model="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter Name" wire:model="name">
                    @error('name') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Short Name:</label>
                    <input type="text" wire:model="short_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter short-Name" wire:model="short_name">
                    <small>Short name for ribbon label.</small>
                    @error('short_name') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>


                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Ribbon</label>
                    <select wire:model="ribbon" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                        <option value="">-</option>

                        <option value="red">red</option>
                        <option value="orange">orange</option>
                        <option value="green">green</option>
                    </select>


                    <small class="form-control-feedback">Show ads with ribbon when viewing ads in search results list.</small>
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label>
                        <input type="checkbox" wire:model="has_badge" value="{{$badge_val}}"> Show ads with a badge (in addition)
                    </label>

                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Price</label>
                    <?php
                    //$settings = App\Models\Setting::get_logos();
                    //$currency_symbol = App\Models\TblPost::get_post_currency($settings['default_currency']);
                    $currency_symbol = App\Models\Setting::get_admin_default_currency();
                    if (!empty($currency_symbol)) {
                    ?>
                        <div class="inline-flex">
                            <span class="p-2 border shadow mr-2"><?php echo $currency_symbol['currency_hex']; ?></span>
                            <input type="text" wire:model="price" placeholder="Price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    <?php } else { ?>
                        <input type="number" min="1" placeholder="Price" readonly="true" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <p class="bg-red-300 text-xs p-1 rounded mt-4 mb-4">Note :: Before add the price please set the default currency in the application settings.</p>
                    <?php } ?>
                    @error('price') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Ad Type</label>
                    <ul class="flex flex-wrap content-start">
                        @foreach($ad_typeArr as $key=>$value)
                        <label class="inline-flex items-center">
                            <input wire:model="ad_type" type="radio" class="form-radio" value="{{$key}}">
                            <span class="ml-2 mr-1">{{$value}}</span>
                        </label>
                        @endforeach
                    </ul>
                    @error('ad_type') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>

                <!-- bulk ads -->
                <div class="flex flex-col mb-4 md:w-1/2">
                    <label>
                        <input type="checkbox" wire:model="bulk_ads" id="bulk" value="{{$bulk_ad_val}}"> Bulk Ads
                    </label>
                    @error('bulk_ads') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Bulk Ads Validity Type</label>
                    <ul class="flex flex-wrap content-start">
                        <label class="inline-flex items-center">
                            <input wire:model="bulk_type" type="radio" class="form-radio" value="1">
                            <span class="ml-2 mr-1">Based on Package</span>
                            <input wire:model="bulk_type" type="radio" class="form-radio" value="2">
                            <span class="ml-2 mr-1">Based on Item</span>
                        </label>
                    </ul>
                    @error('bulk_type') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>


                <div class="bulk-limit flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Bulk Ad Limit</label>
                    <input type="number" wire:model="bulk_limit" placeholder="Bulk Limit" min="1" step="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('bulk_limit') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>

                <!-- end bulk ads -->


                <!-- <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Currency</label>

                    <select wire:model="currency_code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                        <option value="">-</option>
                        <option value="USD">USD</option>
                    </select>
                    @error('currency_code') <span class="text-red-500">{{ $message }}</span>@enderror
                </div> -->

                <!-- <div class="flex flex-col mb-4 md:w-1/2">

                    <label class="block text-gray-700 text-sm font-bold mb-2">Promotion Duration</label>

                    <input type="number" wire:model="promo_duration" placeholder="Duration (in days)" min="0" step="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                    <small>Duration to promote posts (in days). You need to schedule the AdsCleaner command.</small>

                </div> -->

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Publication Duration</label>

                    <input type="number" wire:model="duration" placeholder="Duration (in days)" min="0" step="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                    <small>Duration to promote posts (in days). You need to schedule the AdsCleaner command.</small>

                </div>

                <!-- <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pictures Limit</label>

                    <input type="number" wire:model="pictures_limit" placeholder="Pictures Limit" min="0" step="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                    <small>Maximum number of pictures allowed per ad. Requires to force ads posting from the Pricing Page.</small>

                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Facebook Ads (Duration)</label>

                    <input type="number" wire:model="facebook_ads_duration" min="0" step="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                    <small>Enter a number (in days) greater than 0 to enable this option. Enter 0 to disable it. NOTE: By enabling this option, you accept to sponsor (manually) all the posts that will be belong to this package on <strong>Facebook</strong>.</small>

                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Google Ads (Duration)</label>

                    <input type="number" wire:model="google_ads_duration" min="0" step="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                    <small>Enter a number (in days) greater than 0 to enable this option. Enter 0 to disable it. NOTE: By enabling this option, you accept to sponsor (manually) all the posts that will be belong to this package on <strong>Google</strong>.</small>

                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Twitter Ads (Duration)</label>

                    <input type="number" wire:model="twitter_ads_duration" min="0" step="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                    <small>Enter a number (in days) greater than 0 to enable this option. Enter 0 to disable it. NOTE: By enabling this option, you accept to sponsor (manually) all the posts that will be belong to this package on <strong>Twitter</strong>.</small>

                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">LinkedIn Ads (Duration)</label>

                    <input type="number" wire:model="linkedin_ads_duration" min="0" step="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                    <small>Enter a number (in days) greater than 0 to enable this option. Enter 0 to disable it. NOTE: By enabling this option, you accept to sponsor (manually) all the posts that will be belong to this package on <strong>LinkedIn</strong>.</small>

                </div> -->

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea wire:model="description" placeholder="Description" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    <small>This will appear on the "Pricing" page. Enter one option (advantage) per line or separate them by comma, semicolon or point.</small>

                </div>

                <!-- <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Position</label>

                    <input type="number" wire:model="lft" min="0" step="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                    <small>Quick Reorder: Enter a position number. NOTE: High number will allow to show ads in top in ads listing. Low number will allow to show ads in bottom in ads listing.</small>

                </div> -->

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label>
                        <input type="checkbox" wire:model="recommended" value="{{$recommend_val}}">
                        Recommended</label>
                    <small>By marking this package as recommended, the primary color will be applied to it on the "Pricing" page.</small>

                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label>
                        <input type="checkbox" wire:model="active" value="{{$active_val}}">
                        Active</label>
                    <small class="form-control-feedback"><br><br></small>

                </div>



            </div>

            <div class="justify-center bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button wire:click.prevent="store()" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                        Save
                    </button>
                </span>
            </div>

        </div>
		</div>	

    </div>



</div>