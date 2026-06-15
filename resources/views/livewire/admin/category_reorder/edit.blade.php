<div class="relative md:pt-28 pb-32 pt-12">
<div class="px-4 md:px-10 mx-auto w-full">

<div class="bg-white p-2 mb-2 rounded-md">
@if($ancestors!="")
<?php 
echo '<ul class="flex text-orange-600 text-sm lg:text-base">';
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
@endif
</div>

<div class="mb-4 border-b-2 border-gray-800 bg-white rounded-md p-4">
<form wire:submit.prevent="update(Object.fromEntries(new FormData($event.target)))">
<div>
<div class="inline-block mr-2">Parent
</div>
<div class="inline-block w-2/4">
  <select name="cat_parent" id="id_catparent" class="form-select mt-1 block w-full">
  <option value="0" <?php if($data->parent_id==null){ echo "selected='selected'";} ?>>Root</option>
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
$traverse($categories,'',$data->parent_id);


?>
</select>
</div>
</div>
<div class="mb-3">
<div class="inline-block mr-6">Title</div>
<div class="inline-block w-2/4">
<input type="text" name="cattitle" id="id_cattitle" value="{{ $data->title }}" class="form-input rounded-md shadow-sm block mt-1 w-full">
<input type="hidden" name="catid" id="id_catid" value="{{ $data->id }}" class="form-input rounded-md shadow-sm block mt-1 w-full">
<input type="hidden" name="cathtml" id="id_cathtml" value="{{ $data->html }}"/>
</div>
<div class="inline-block"><button id="post_json_insert" type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 ml-4">Update</button>
<div id="post_cancel" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 ml-4">Cancel</div>
</div>
</div>
</form>
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
$("#post_cancel").click(function(){ window.location.href=document.referrer; });
//cancel end
});

</script>
</div>

</div>