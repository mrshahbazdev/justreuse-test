<div>
    <div class="pb-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5 top-shadow">
            <!-- <button wire:click="back()" class="bg-green-500 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded my-3">Back</button> -->
            <div class="bg-white px-0 xl:px-4 pt-5 pb-4 sm:p-2 sm:pb-4 text-left">
                <h1 class="text-2xl font-medium text-gray-900">Home Banner - Map</h1>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Banner with Map or Images</label>
                    <!-- wire:model="banner_type" -->
                    <select id="banner_type_val" class="banner_map form-select mt-1 block w-full" wire:click="changeBannerType($event.target.value)">
                        <option value="0" <?php echo ($banner_type=="0")?"selected='selected'":""; ?> >--select option--</option>
                        <option value="1" <?php echo ($banner_type=="1")?"selected='selected'":""; ?> >Banner with Map - enable</option>
                        <option value="2" <?php echo ($banner_type=="2")?"selected='selected'":""; ?>>Banner with images - enable</option>
                        <option value="3" <?php echo ($banner_type=="3")?"selected='selected'":""; ?>>Banner with Mapbox - enable</option>
                    </select>
                    @error('banner_type') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>

                @if($banner_type !=2)
                <div class="inline-block w-2/4 div_enable_map">
                    <!-- wire:model="enable_map" -->
                    <select wire:click="changeEnableMap($event.target.value)" class="form-select mt-1 block w-full enable_map">
                        <option value="0" <?php echo ($enable_map=="0")?"selected='selected'":""; ?> >Disable</option>
                        <option value="1" <?php echo ($enable_map=="1")?"selected='selected'":""; ?>>Enable</option>
                    </select>
                    <p class="mt-2 mb-2 bg-red-300 text-xs rounded p-1">Note: Banner will be shown only for paid users.</p>
                </div>    
                @endif
                @if($banner_type !=2)
                <div class="flex flex-col mb-4 md:w-1/2 div_cover_max_distance_km">
                    <label class="block text-gray-700 text-sm font-bold mt-2 mb-2">Cover max distance(around) in kilometer:</label>
                    <input type="number" min="1" wire:model="cover_max_distance_km" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p class="mt-2 mb-2 bg-red-300 text-xs rounded p-1">Note: It will show paid Ads location based on from current city location</p>
                </div>
                @endif
                @if($banner_type !=3 && $banner_type !=2)
                <div class="flex flex-col mb-4 md:w-1/2 div_google_api_key">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Google Api Key : <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="google_api_key" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('google_api_key') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>
                @endif
                @if($banner_type !=1 && $banner_type !=2)
                <div class="flex flex-col mb-4 md:w-1/2 div_mapbox_api_key">
                    <label class="block text-gray-700 text-sm font-bold mb-2">MapBox Api Key : <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="mapbox_api_key" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('mapbox_api_key') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>
                @endif
                <div class="p-2">
                    <span class="flex w-full rounded-md pb-2 sm:w-auto">
                        <button wire:click.prevent="update()" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                            Update
                        </button>
                    </span>
                </div>
            </div>
        </div> 
    </div>
    <script>

        $(document).ready(function() {

            var banner_type = "<?php echo $banner_type; ?>";
            
            if(banner_type == "1")
            {
                $('.div_enable_map').show();
                $('.div_cover_max_distance_km').show();
                $('.div_google_api_key').show();
                $('.div_mapbox_api_key').hide();
            }else if(banner_type == "3"){
                $('.div_enable_map').show();
                $('.div_cover_max_distance_km').show();
                $('.div_google_api_key').hide();
                $('.div_mapbox_api_key').show();
            }else{
                $('.div_enable_map').hide();
                $('.div_cover_max_distance_km').hide();
                $('.div_google_api_key').hide();
                $('.div_mapbox_api_key').hide();
            }

        
            $('#banner_type_val').on('change', function (e) {
                var optionSelected = $("option:selected", this);
                var banner_type = this.value;
                
                if(banner_type == "1")
                {
                    $('.div_enable_map').show();
                    $('.div_cover_max_distance_km').show();
                    $('.div_google_api_key').show();
                    $('.div_mapbox_api_key').hide();
                }else if(banner_type == "3"){
                    $('.div_enable_map').show();
                    $('.div_cover_max_distance_km').show();
                    $('.div_google_api_key').hide();
                    $('.div_mapbox_api_key').show();
                }else{
                    $('.div_enable_map').hide();
                    $('.div_cover_max_distance_km').hide();
                    $('.div_google_api_key').hide();
                    $('.div_mapbox_api_key').hide();
                }

            });

        });

    </script>
</div>