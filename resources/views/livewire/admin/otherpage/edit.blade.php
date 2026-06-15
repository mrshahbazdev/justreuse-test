<div>
     <div class="px-4 md:px-10 mx-auto w-full">
		<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
		<div class="w-full  bg-gray-100  pl-2 pr-2 mb-2 rounded-sm">
        <button wire:click="back()" class="bg-green-500 hover:bg-orange-500 text-white py-2 px-4 rounded my-3">Back</button>
		</div>
        <form wire:submit.prevent="update(Object.fromEntries(new FormData($event.target)))">
            <div class="bg-gray-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left border rounded shadow mt-4">
                <div class="flex flex-col mb-4">
                    <h1 class="text-2xl font-medium text-gray-900 mb-3">Update Page</h1>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
                    <input type="text" required="required" name="title" class="form-input rounded-md shadow-sm block mt-1 w-full" value="<?php echo $editdata['title']; ?>" placeholder="Enter Title" autocomplete="off">
                    @error('title') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>     
                
                <div class="flex flex-col mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" placeholder="Meta Title" value="<?php echo $editdata['meta_title']; ?>"  class="form-input rounded-md shadow-sm block mt-1 w-full">
                </div>

                <div class="flex flex-col mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Meta Key</label>
                        <textarea placeholder="Meta key" name="meta_key" value="<?php echo $editdata['meta_key']; ?>" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo $editdata['meta_key']; ?></textarea>
                </div>

                <div class="flex flex-col mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Meta Description</label>
                        <textarea placeholder="Meta Description" name="meta_description" value="<?php echo $editdata['meta_description']; ?>" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo $editdata['meta_description']; ?></textarea>
                </div>

                <!-- <div class="flex flex-col mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Content</label>
                    <textarea placeholder="Content" readonly rows="6" name="content" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                      <?php //echo $editdata['content']; ?>                        
                    </textarea>
                </div> -->
                <!-- <textarea class="hide mycontent" name="content" style="display:none"></textarea> -->
            </div>
            <input type="hidden" value="<?php echo $editdata['slug']; ?>" name="slug"/>
            <input type="hidden" value="<?php echo $editdata['id']; ?>" name="page_id"/>
            <div class="justify-center bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button  type="submit" class="tesing inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                        Update
                    </button>
                </span>
            </div>
        </form>

        </div>
<script>

</script>

    </div>




</div>