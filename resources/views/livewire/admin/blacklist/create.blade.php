<div>
    <div class=py-24>
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-16">
        <button wire:click="back()" class="bg-green-500 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded my-3">Back</button>
        <form wire:submit.prevent="store(Object.fromEntries(new FormData($event.target)))">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left">
                <div class="flex flex-col mb-4">
                    <h1 class="text-2xl font-medium text-gray-900">Add New Blacklist</h1>      
                </div>
                <div class="flex flex-col mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                    Blacklist Type *</label>
                    <select required="required" name="type" class="type shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                      <option value="">[--Select Blacklist type--]</option>
                      <option value="word">Word</option>
                      <option value="email">Email</option>
                      <option value="domain">Domain</option>
                    </select>
                </div>
                <div class="flex flex-col mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                    Entry Text *</label>
                    <input type="text" name="entry" required="required" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter blacklist word" autocomplete="off">
                </div>
            </div>
            <div class="justify-center bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                        Save
                    </button>
                </span>
            </div>
        </form>

        </div>

    </div>




</div>