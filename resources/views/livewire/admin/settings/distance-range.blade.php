<div>
    <div class="pb-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5 top-shadow">
        <!-- <button wire:click="back()" class="bg-green-500 hover:bg-orange-500 text-white py-2 px-4 rounded my-3 focus:outline-none focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150">Back</button> -->
            <div class="bg-white px-0 xl:px-4 pt-5 pb-4 sm:p-2 sm:pb-4 text-left">
                <h1 class="text-2xl font-medium text-gray-900">Distance Range</h1>
                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Max distance range in km</label>
                    <input type="number" min="1" wire:model="max_distance" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('max_distance') <span class="text-red-500">{{ $message }}</span>@enderror
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