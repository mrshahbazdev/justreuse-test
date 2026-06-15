<div class="relative md:pt-28 pb-32 pt-12">
<div class="px-4 md:px-10 mx-auto w-full">

@if($ancestors!="")
<div class="bg-white p-2 mb-2 rounded-md">
<?php 
echo '<ul class="flex text-orange-400 text-sm lg:text-base">';
echo "<li><a href='".URL::to('admin/category/')."'>Category <i class='fa fa-angle-double-right'></i></a></li>";
$i=1;
//echo $de = $ancestors->depth(request()->segment(2));
  foreach ($ancestors as $r) {
    $totcount = $ancestors->count();
    $catname = "&nbsp;".$r->title;

    if($i!=$totcount)
    {
        $url = URL::to('admin/category/'.$r->uuid.'/subcategories');
        echo '<li><a href="'.$url.'">'.$catname.' <i class="fa fa-angle-double-right"></i> </a></li>';
    }
    else{
      echo '<li class="text-black">'.$catname.'</li>';
    }
    $i++;
  }
echo "</ul>";
?>
</div>
@endif

<div class="mb-4 border-b-2 border-gray-800 bg-white rounded-md p-4">
<form wire:submit.prevent="store(Object.fromEntries(new FormData($event.target)))">
<div>
<div class="inline-block mr-2">Parent</div>
<div class="inline-block w-2/4">
  <select name="cat_parent" id="id_catparent" class="form-select mt-1 block w-full">

  <option value="0">Root</option>
<?php

  $traverse = function ($categories, $prefix = '', $parent='') use (&$traverse) {
    foreach ($categories as $category) {
        $catname = $prefix.'| '.$category->title;
        $catid = $category->id;
        $selected = ($parent==$catid)?"selected='selected'":"";
        echo '<option value="'.$catid.'" '.$selected.'>'.$catname.'</option>';
        $traverse($category->children, $prefix.'--', $parent);
    }
};
$traverse($categories,'',$current_parent);
?>
</select>
</div>
</div>

<div class="mb-3">
<div class="inline-block mr-6">Title</div>
<div class="inline-block w-2/4">
<input type="text" name="cat_title" id="id_cattitle" class="form-input rounded-md shadow-sm block mt-1 w-full">
<input type="hidden" name="cat_html" id="id_cathtml" value=""/>
</div>
<div class="inline-block"><button id="post_json_insert" type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 ml-4">Save</button>
<div id="post_cancel" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 ml-4">Cancel</div>
</div>
</div>
</form>
</div>

<div class="flex flex-wrap mt-3" style="display:none;">
<div class="w-full lg:w-12/12 px-4 mb-3">
<label class="block text-gray-700 text-xs font-bold mb-2" htmlfor="grid-password">Add Title *</label>
<input type="text" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" value="" name="text-title-sst" id="text-title-sst" required="required">
</div>

<div class="w-full lg:w-12/12 px-4 mb-3">
<label class="block text-gray-700 text-xs font-bold mb-2" htmlfor="grid-password">Price *</label>
<input type="number" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" value="" name="number-price-sst" id="number-price-sst" min="0" required="required">
</div>
                     

<div class="w-full lg:w-12/12 px-4 mb-3">
<label class="block text-gray-700 text-xs font-bold mb-2">Description *</label>
<textarea type="text" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" rows="4" name="textarea-desc-sst" id="textarea-desc-sst" required="required"></textarea>
</div>



<div class="w-full lg:w-12/12 px-4 mb-3">
<label class="block text-gray-700 text-xs font-bold mb-2">Image</label> <input class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" type="file" name="file-images-sst[]" id="predefined_images" multiple="" accept="image/*"></div>


<div class="w-full lg:w-12/12 px-4 mb-3">
<label class="block text-gray-700 text-xs font-bold mb-2">City</label>
<input type="text" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150 pac-target-input" value="" id="searchTextField" name="text-city-sst" required="required" placeholder="Enter a location" autocomplete="off">
<input type="hidden" id="cityName" name="city_name" size="50">
<input type="hidden" id="cityLat" name="city_lat" size="50">
<input type="hidden" id="cityLag" name="city_lag" size="50">
<input type="hidden" id="country_long" name="country_long" size="50">
<input type="hidden" id="country_short" name="country_short" size="50">
<input type="hidden" id="state_long" name="state_long" size="50">
<input type="hidden" id="state_short" name="state_short" size="50">
<input type="hidden" name="post-catid-sst" id="id_post_catid">
</div>



</div>


@include('livewire.admin.category.js_css_category')

<script>
$(document).ready(function(){
	$("#post_json_insert").click(function(){
    var title = $('#id_cattitle').val();
		if(title==""){
      $('#id_cattitle').focus();
			return false;
    }

  });

  	//cancel begin
    $("#post_cancel").click(function(){ 

      window.location.href=document.referrer; });
   //cancel end
});


</script>

</div>




</div>