<div>
    <div class=py-24>
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-16">
        <button wire:click="back()" class="bg-green-500 hover:bg-orange-700 text-white py-2 px-4 rounded my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150">Back</button>
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left">
                <h1 class="text-2xl font-medium text-gray-900">Homepage Banner Type</h1>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Banner with Map or Images</label>
                    <select wire:model="banner_type" class="banner_map form-select mt-1 block w-full" wire:ignore>
                        <option value="0" <?php echo ($banner_type=="0")?"selected='selected'":""; ?> >--select option--</option>
                        <option value="1" <?php echo ($banner_type=="1")?"selected='selected'":""; ?> >Banner with Map - enable</option>
                        <option value="2" <?php echo ($banner_type=="2")?"selected='selected'":""; ?>>Banner with images - enable</option>
                    </select>
                    @error('banner_type') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>


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
</div>