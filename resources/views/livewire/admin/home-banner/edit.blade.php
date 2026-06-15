<div>
  <div class="py-2 px-4">
       

        <div class="px-4 md:px-10 mx-auto w-full">
		<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
		<div class="w-full  bg-gray-100  pl-2 pr-2 mb-2 rounded-sm">
        <button wire:click="back()" class="bg-green-500 hover:bg-orange-700 text-white py-2 px-4 rounded-sm my-3">Back</button>
		</div>
        <form>
            <div class="bg-gray-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left border rounded shadow mt-4">
                <div class="flex flex-col mb-4">
                    <h1 class="text-2xl font-medium text-gray-900 mb-3">Edit New Banner</h1>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Banner Url:</label>
                    <input type="text" wire:model="banner_url" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    
                    @error('banner_url') <span class="text-red-500">{{ $message }}</span>@enderror

                </div>

				<div class="flex flex-col mb-4">
				                    <?php
                        if (!empty($this->stored_images)) {
                            $imageUrl = URL::to('storage/' . $this->stored_images);
                        } else {
                            $imageUrl = URL::to('storage/profile-avatar.jpg');
                        }
                     ?>
                        <img src="{{$imageUrl}}" width="70" height="70" alt="your profile"/>

                    <label class="block text-gray-700 text-sm font-bold mb-2">Image:</label>
                    <input type="file" wire:model="images" name="images" id="images"  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p>Image Size (1200px X 400px)</p>
                    @error('images') <span class="text-red-500">{{ $message }}</span>@enderror
				
				</div>
                <div class="flex flex-col mb-4" wire:ignore>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
                        <textarea id="description" wire:model="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        @error('description') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>

            </div>
            <div class="justify-center bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button wire:click.prevent="update()" class="tesing inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                        Update
                    </button>
                </span>
				</div>
            </div>
        </form>

        <script>
$(document).ready(function(){
    var _URL = window.URL || window.webkitURL;
    
    $('#images').change(function () {
        var imgwidth1 = 0;
        var imgheight1 = 0;
        var wb_file  =  $(this)[0].files[0];
        var x_img = new Image();
        x_img.src = _URL.createObjectURL(wb_file);
        x_img.onload = function() {
             imgwidth1 = this.width;
             imgheight1 = this.height;
			 if(parseFloat(imgwidth1)!=1200)
			 {
				 alert("Improper image size uploaded.\nBanner Image For Website (1200px X 400px)");
				 $("#images").val("");
                 return false;
			 }
             else if(parseFloat(imgheight1)!=400)
             {
                alert("Improper image size uploaded.\nBanner Image For Website (1200px X 400px)");
				 $("#images").val("");
                 return false;
             }
        };
    });




});
</script>

<script>
  $(document).ready(function() {
        var editor = CKEDITOR.replace('description');

        editor.on('change', function(event){
            window.livewire.emit('ckeditorUpdated', event.editor.getData());
        });

        window.livewire.on('ckeditorSetData', function(data) {
            editor.setData(data);
        });
    });
</script>

        </div>


    </div>

</div>