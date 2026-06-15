<div>
    <div class="pb-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5 top-shadow">
            <!-- <button wire:click="back()" class="bg-green-500 hover:bg-orange-700 text-white py-2 px-4 rounded my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150">Back</button> -->
            <div class="bg-white px-0 xl:px-4 pt-5 pb-4 sm:p-2 sm:pb-4 text-left">
                <h1 class="text-2xl font-medium text-gray-900">Twilio SMS Keys</h1>
                <div class="inline-block w-2/4">
                    <select wire:model="enable_sms" class="form-select mt-1 block w-full" wire:ignore>
                        <option value="0" <?php echo ($enable_sms=="0")?"selected='selected'":""; ?> >Disable</option>
                        <option value="1" <?php echo ($enable_sms=="1")?"selected='selected'":""; ?>>Enable</option>
                    </select>
                </div>
                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mt-2 mb-2">Twilio SID</label>
                    <input type="text" wire:model="twilio_sid" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('twilio_sid') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>
                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mt-2 mb-2">Twilio Token</label>
                    <input type="text" wire:model="twilio_token" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('twilio_token') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>
                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mt-2 mb-2">Twilio From</label>
                    <input type="text" wire:model="twilio_from" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('twilio_from') <span class="text-red-500">{{ $message }}</span>@enderror
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