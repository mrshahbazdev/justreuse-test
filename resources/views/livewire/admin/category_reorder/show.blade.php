@if(auth()->user()->can('category-reorder'))
<div class="relative md:pt-28 pb-32 pt-12">
<div class="px-4 md:px-10 mx-auto w-full">

<div>
<div class="flex flex-wrap items-center bg-white p-2 mb-2 rounded-md">
<div class="relative">
@if($ancestors!="")
<?php
echo '<ul class="flex text-orange-400 text-sm lg:text-base">';
echo "<li><a href='".URL::to('admin/category/')."'>Category <i class='fa fa-angle-double-right'></i></a></li>";
$i=1;
$addnew_end_title = "";
//echo $de = $ancestors->depth(request()->segment(2));
  foreach ($ancestors as $r) {
    $totcount = $ancestors->count();
    $catname = "&nbsp;".$r->title;
    $addnew_end_title = $r->title;

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


<div class="relative w-full max-w-full flex-grow flex-1 text-right">

<?php 
$reqId = request()->segment(3);
if($reqId=="")
{
  $addnew_title = "Back To Category";
  $addnew_url = "admin/category";
}
else{
  $subcattitle = end($ancestors)[0]['title'];
  $addnew_title = "Back to subcategory → ".$addnew_end_title;
  $addnew_url = "admin/category/".$reqId."/subcategories";
}
?>
@if(auth()->user()->can('category-create'))
<button class="bg-green-500 text-white text-sm px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none hover:bg-orange-500"><a href="{{URL::to($addnew_url)}}"><i class="fa fa-arrow-circle-left"></i>
 {{ $addnew_title }}</a></button>
 @endif
</div>
</div>


<div class="bg-white p-5 rounded-md">
<!--sort start-->
<ul id="sortable1" class="connectedSortable">
@foreach($list as $row)
  <li class="bg-green-100 p-2 mb-1 rounded cursor-pointer list_values" data-row-id="{{$row->id}}">{{$row->title}}</li>
@endforeach
</ul>
<!--sort end-->

</div>




</div>


<div class="grid grid-cols-12 bg-white p-2 mt-2 rounded-md">
<div class="col-span-9 inline-block align-text-middle">
</div>
<div class="col-span-3 flex flex-row-reverse">
<button class="w-3/4 bg-green-500 text-white text-sm px-4 py-3 rounded shadow hover:shadow-md outline-none focus:outline-none hover:bg-orange-500 save_click">Save</button>
 </div>
</div>
</div>


<script src="{{ URL::to('js/reorder-jquery-ui.js') }}"></script>
<script>
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
       });


$( function() {

  $( "#sortable1, #sortable2" ).sortable({
    connectWith: ".connectedSortable"
  }).disableSelection();


  $(".save_click").click(function(){

    var dataArr= [];
    // $.each(".list_values",function(e){
      var i=0;
      $(".list_values").each(function(){
        i++;
        var rowid = $(this).attr('data-row-id');
        var idarr = {};
        idarr["list_order"]=i; idarr["row_id"]=rowid;
        dataArr.push(idarr);
      // console.log('hi'+rowid);
      });

    if(dataArr.length==0)
    {
      location.href="{{URL::to($addnew_url)}}";
      return false;
    }


//post begin
   $.ajax({
	  type:'POST',
	  dataType: 'json',
	  url:"{{URL::to('update-category-order')}}",
	  data:{data_array:dataArr},
	  success:function(data){
      if(data.result=="success")
      {
        location.href="{{URL::to($addnew_url)}}";
      }
      else {
        location.href="{{URL::to($addnew_url)}}";
      }
    }
   });
//post end
});


  } );
</script>





</div>
@endif