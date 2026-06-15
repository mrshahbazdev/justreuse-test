<div>
    <div class="px-4 md:px-10 mx-auto w-full">
		<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
		 <div class="w-full  bg-gray-100  pl-2 pr-2 mb-2 rounded-sm">
        <button wire:click="back()" class="bg-green-500 hover:bg-orange-700 text-white  py-2 px-4 rounded-sm my-3">Back</button>
		</div>
        <form wire:submit.prevent="update(Object.fromEntries(new FormData($event.target)))">
            <div class="bg-gray-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left border rounded shadow mt-4">
                <div class="flex flex-col mb-4">
                    <h1 class="text-2xl font-medium text-gray-900 mb-3">Update Page Content</h1>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
                    <input type="text" required="required" name="title" class="form-input rounded-md shadow-sm block mt-1 w-full" value="<?php echo $editdata['title']; ?>" placeholder="Enter Title" autocomplete="off">
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>     
                
                <div class="flex flex-col mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" value="<?php echo $editdata['meta_title']; ?>"  class="form-input rounded-md shadow-sm block mt-1 w-full">
                </div>

                <div class="flex flex-col mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Meta Key</label>
                        <textarea placeholder="Meta key" name="meta_key" value="<?php echo $editdata['meta_key']; ?>" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo $editdata['meta_key']; ?></textarea>
                </div>

                <div class="flex flex-col mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Meta Description</label>
                        <textarea placeholder="Meta Description" name="meta_description" value="<?php echo $editdata['meta_key']; ?>" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo $editdata['meta_description']; ?></textarea>
                </div>

                <div class="flex flex-col mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Content</label>
                    <textarea id="editor" placeholder="Content"  rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                      <?php echo $editdata['content']; ?>                        
                    </textarea>
                </div>
            </div>
            <textarea class="hidden" id="mycontent" name="content"><?php echo $editdata['content']; ?></textarea>
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

//CKEDITOR.replace('editor');
const editor = CKEDITOR.replace('editor');
editor.on('change', function(event){
    $("#mycontent").text(event.editor.getData());
});
     


/*$("document").ready(function(){   
tinymce.init({
  selector: '#editor',
  plugins: 'image code link table textcolor media',
  toolbar: 'undo redo | link image | code | forecolor backcolor | media',
  contextmenu: 'link image table',
  media_live_embeds: true,
  textcolor_map: [
    "000000", "Black",
    "993300", "Burnt orange",
    "333300", "Dark olive",
    "003300", "Dark green",
    "003366", "Dark azure",
    "000080", "Navy Blue",
    "333399", "Indigo",
    "333333", "Very dark gray",
    "800000", "Maroon",
    "FF6600", "Orange",
    "808000", "Olive",
    "008000", "Green",
    "008080", "Teal",
    "0000FF", "Blue",
    "666699", "Grayish blue",
    "808080", "Gray",
    "FF0000", "Red",
    "FF9900", "Amber",
    "99CC00", "Yellow green",
    "339966", "Sea green",
    "33CCCC", "Turquoise",
    "3366FF", "Royal blue",
    "800080", "Purple",
    "999999", "Medium gray",
    "FF00FF", "Magenta",
    "FFCC00", "Gold",
    "FFFF00", "Yellow",
    "00FF00", "Lime",
    "00FFFF", "Aqua",
    "00CCFF", "Sky blue",
    "993366", "Red violet",
    "FFFFFF", "White",
    "FF99CC", "Pink",
    "FFCC99", "Peach",
    "FFFF99", "Light yellow",
    "CCFFCC", "Pale green",
    "CCFFFF", "Pale cyan",
    "99CCFF", "Light sky blue",
    "CC99FF", "Plum"
  ],
  image_title: true,
  automatic_uploads: true,

  file_picker_types: 'image',
  file_picker_callback: function (cb, value, meta) {
    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');

    input.onchange = function () {
      var file = this.files[0];

      var reader = new FileReader();
      reader.onload = function () {

        var id = 'blobid' + (new Date()).getTime();
        var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
        var base64 = reader.result.split(',')[1];
        var blobInfo = blobCache.create(id, file, base64);
        blobCache.add(blobInfo);

        cb(blobInfo.blobUri(), { title: file.name });
      };
      reader.readAsDataURL(file);
    };

    input.click();
  },
  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
});

});
$(".tesing").click(function(){
  var myContent = tinymce.get("editor").getContent();
 $(".mycontent").text(myContent);
});*/

           </script>

    </div>




</div><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/staticpage/edit.blade.php ENDPATH**/ ?>