<div>
   <div class="py-2">
        <div class="px-4 md:px-10 mx-auto w-full">
		<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
		<div class="w-full  bg-gray-100  pl-2 pr-2 mb-2 rounded-sm">
		<button wire:click="back()" class="bg-green-500 hover:bg-orange-500 text-white  py-2 px-4 rounded-sm my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150">Back</button>
		</div>
	 <form wire:submit.prevent="store(Object.fromEntries(new FormData($event.target)))">
        <div class="bg-gray-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left border rounded shadow mt-4">
          <div class="flex flex-col mb-4">
            <h1 class="text-2xl font-medium text-gray-900 mb-3">Create New Coupon</h1>
            <label class="block text-gray-700 text-sm font-bold mb-2">Coupon Title <span class="text-red-500">*</span></label>
            <input type="text" required="required" name="coupon_title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter Coupon Title" autocomplete="off">
            @error('coupon_title') <span class="text-red-500">{{ $message }}</span>@enderror
          </div>
          <div class="flex flex-col mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">
              Coupon Code <span class="text-red-500">*</span></label>
            <input type="text" required="required" name="coupon_code" class="uppercase shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ex : AWE012!" autocomplete="off">
            @error('coupon_code')<span class="text-red-500">{{ $message }}</span>@enderror
          </div>
          <div class="flex flex-col mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">
              Coupon Type <span class="text-red-500">*</span></label>
            <select required="required" name="type" class="type shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              <option value="">[--Select Coupon type--]</option>
              <option value="fixed">Fixed</option>
              <option value="percentage">Percentage</option>
            </select>
            @error('type')<span class="text-red-500">{{ $message }}</span>@enderror
          </div>
          <?php
          $currency_symbol = App\Models\Setting::get_admin_default_currency();
          ?>
          <div class="flex flex-col mb-4 fixed_discount">
            <label class="block text-gray-700 text-sm font-bold mb-2">Fixed Discount <span class="text-red-500">*</span></label>
            <?php if (!empty($currency_symbol)) { ?>
              <div class="inline-flex">
                <span class="p-2 border shadow mr-2"><?php echo $currency_symbol['currency_hex']; ?></span>
                <input type="text" name="fixed_discount" class="allow_decimal shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter fixed offer amount" autocomplete="off">
              </div>
              <span class="text-sm text-gray-500">Enter fixed discount amount in numbers only!</span>
            <?php } else { ?>
              <input type="text" disabled="true" readonly="true" class="allow_decimal shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter fixed offer amount" autocomplete="off">
              <p class="bg-red-300 text-xs p-1 rounded mt-4 mb-4">Note :: Before update the price please set the default currency in the application settings.</p>
            <?php } ?>
            @error('fixed_discount')<span class="text-red-500">{{ $message }}</span>@enderror
          </div>
          <div class="flex flex-col mb-4 percentage_discount">
            <label class="block text-gray-700 text-sm font-bold mb-2">
              Discount in % <span class="text-red-500">*</span></label>
            <input type="text" name="percentage_discount" class="allow_decimal shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter % discount" autocomplete="off">
            <span class="text-sm text-gray-500">Enter discount in numbers only!</span>
            @error('offer_percentage')<span class="text-red-500">{{ $message }}</span>@enderror
          </div>
          <div class="flex flex-col mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">
              Coupon Limit Type <span class="text-red-500">*</span></label>
            <select required="required" name="limit_type" class="limit_type shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              <option value="">[--Select coupon limit type--]</option>
              <option value="individual">Individual person</option>
              <option value="all">Overall</option>
            </select>
            @error('limit_type')<span class="text-red-500">{{ $message }}</span>@enderror
          </div>
          <div class="flex flex-col mb-4 individual">
            <label class="block text-gray-700 text-sm font-bold mb-2">
              No of times per person <span class="text-red-500">*</span></label>
            <input type="text" name="individual_value" class="allow_decimal shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter coupon limited times in numbers" autocomplete="off">
            <span class="text-sm text-gray-500">Enter coupon limited times in numbers only!</span>
          </div>
          <div class="flex flex-col mb-4 all">
            <label class="block text-gray-700 text-sm font-bold mb-2">
              Total no of times <span class="text-red-500">*</span></label>
            <input type="text" name="all_value" class="allow_decimal shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter coupon limited times in numbers" autocomplete="off">
            <span class="text-sm text-gray-500">Enter coupon limited times in numbers only!</span>
          </div>
          <div class="flex flex-col mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">
              Tax</label>
            <input type="text" name="tax" class="allow_decimal shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter Tax" autocomplete="off">
          </div>
          <div class="flex flex-col mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">
              Coupon Validate From <span class="text-red-500">*</span></label>
            <input type="date" required="required" name="start_date" class="date shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Validate From" autocomplete="off">
            @error('start_date')<span class="text-red-500">{{ $message }}</span>@enderror
          </div>
          <div class="flex flex-col mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">
              Coupon Validate To <span class="text-red-500">*</span></label>
            <input type="date" required="required" name="end_date" class="date shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Validate To" autocomplete="off">
            @error('end_date')<span class="text-red-500">{{ $message }}</span>@enderror
          </div>
          <div class="flex flex-col mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">
              Coupon Description <span class="text-red-500">*</span></label>
            <textarea id="editor1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Coupon Description"></textarea>
            @error('description')<span class="text-red-500">{{ $message }}</span>@enderror
          </div>
          <textarea class="mycontent" name="description" style="display:none"></textarea>

        </div>
        <div class="justify-center bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
            <button type="submit" class="getfullcontent inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
              Save
            </button>
          </span>
        </div>
      </form>
</div>
    </div>
    <script>
      //*Allow decimals only
      $(".allow_decimal").on("input", function(evt) {
        var self = $(this);
        self.val(self.val().replace(/[^0-9\.]/g, ''));
        if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
          evt.preventDefault();
        }
      });
      //*
      $('.date').val(new Date().toJSON().slice(0, 10));
      $(function() {
        var dtToday = new Date();
        var month = dtToday.getMonth() + 1;
        var day = dtToday.getDate();
        var year = dtToday.getFullYear();
        if (month < 10)
          month = '0' + month.toString();
        if (day < 10)
          day = '0' + day.toString();
        var minDate = year + '-' + month + '-' + day;
        $('.date').attr('min', minDate);
      });
      $('.percentage_discount').hide();
      $('.fixed_discount').hide();
      $('.type').on('change', function() {
        if (this.value == "fixed") {
          $('.fixed_discount').show();
          $(".fixed_discount input").attr("required", true);
          $('.percentage_discount').hide();
          $(".percentage_discount input").attr("required", false);
        } else if (this.value == "percentage") {
          $('.percentage_discount').show();
          $(".percentage_discount input").attr("required", true);
          $('.fixed_discount').hide();
          $(".fixed_discount input").attr("required", false);
        } else {
          $('.percentage_discount').hide();
          $('.fixed_discount').hide();
        }
      });
      $('.all').hide();
      $('.individual').hide();
      $('.limit_type').on('change', function() {
        if (this.value == "individual") {
          $('.individual').show();
          $(".individual input").attr("required", true);
          $('.all').hide();
          $(".all input").attr("required", false);
        } else if (this.value == "all") {
          $('.all').show();
          $(".all input").attr("required", true);
          $('.individual').hide();
          $(".individual input").attr("required", false);
        } else {
          $('.all').hide();
          $('.individual').hide();
        }
      });
      $("document").ready(function() {
        tinymce.init({
          selector: '#editor1',
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
          /* enable title field in the Image dialog*/
          image_title: true,
          /* enable automatic uploads of images represented by blob or data URIs*/
          automatic_uploads: true,
          /*
            URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url)
            images_upload_url: 'postAcceptor.php',
            here we add custom filepicker only to Image dialog
          */
          file_picker_types: 'image',
          /* and here's our custom image picker*/
          file_picker_callback: function(cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            /*
              Note: In modern browsers input[type="file"] is functional without
              even adding it to the DOM, but that might not be the case in some older
              or quirky browsers like IE, so you might want to add it to the DOM
              just in case, and visually hide it. And do not forget do remove it
              once you do not need it anymore.
            */

            input.onchange = function() {
              var file = this.files[0];

              var reader = new FileReader();
              reader.onload = function() {
                /*
                  Note: Now we need to register the blob in TinyMCEs image blob
                  registry. In the next release this part hopefully won't be
                  necessary, as we are looking to handle it internally.
                */
                var id = 'blobid' + (new Date()).getTime();
                var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                var base64 = reader.result.split(',')[1];
                var blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);

                /* call the callback and populate the Title field with the file name */
                cb(blobInfo.blobUri(), {
                  title: file.name
                });
              };
              reader.readAsDataURL(file);
            };

            input.click();
          },
          content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
        });
      });
      $(".getfullcontent").click(function() {
        var myContent = tinymce.get("editor1").getContent();
        $(".mycontent").text(myContent);
      });
    </script>

  </div>




</div>