<div>
    <div class=py-24>
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-16">
        <button wire:click="back()" class="bg-green-500 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded my-3">Back</button>
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left">
            <h1 class="text-2xl font-medium text-gray-900">Free Ads</h1>
                

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-ssm font-bold mb-2">Free Ads Limit (maximum):</label>
                    <input type="number" min="1" wire:model="free_ads" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('free_ads') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>

                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Free Ads Duration: (in days)</label>
                    <input type="number" min="1" wire:model="free_ads_duration" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('free_ads_duration') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>
                

            </div>

            <div class="justify-center bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button wire:click.prevent="update()" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                        Update
                    </button>
                </span>
            </div>

        </div>


    </div>




</div>