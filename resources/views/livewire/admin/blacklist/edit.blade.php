<div>
   <div class="py-2 ">
        <div class="px-4 md:px-10 mx-auto w-full">
		<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
		<div class="w-full  bg-gray-100  pl-2 pr-2 mb-2 rounded-sm">
        <button wire:click="back()" class="bg-green-500 hover:bg-orange-500 text-white  py-2 px-4 rounded-sm my-3">Back</button>
		</div>
        <form wire:submit.prevent="update(Object.fromEntries(new FormData($event.target)))">
            <div class="bg-gray-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left border rounded shadow mt-4">
                <div class="flex flex-col mb-4">
                    <h1 class="text-2xl font-medium text-gray-900">Update</h1>          
                </div>
                <div class="flex flex-col mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                    Blacklist Type *</label>
                    <select required="required" name="type" disabled="" class="type shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                      <option value="word" <?php echo ($editdata['type']=="word" ? "selected" : "");?>>Word</option>
                      <option value="email" <?php echo ($editdata['type']=="email" ? "selected" : "");?>>Email</option>
                      <option value="domain" <?php echo ($editdata['type']=="domain" ? "selected" : "");?>>Domain</option>
                    </select>
                </div>
                <div class="flex flex-col mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                    Entry Text *</label>
                    <input type="text" required="required" name="entry" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter blacklist word" autocomplete="off" value="<?php echo $editdata['entry'];?>">
                </div>                                         
                <input type="hidden" name="list_id" value="<?php echo $editdata['id'];?>"/>
            </div>
            <div class="justify-center bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                <button  type="submit" class="tesing inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                        Update
                </button>
            </span>
          </div>
        </form>
      </div>
	  </div>
    </div>
</div>