<section class="relative py-20">

<div class="container mx-auto">
<div class="flex flex-wrap">
<!--left portion-->
<div class="w-full md:w-3/12 ml-auto mr-auto px-2  text-gray-800">
<div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 border border-gray-300 bg-gray-100">
<div class="p-2 text-center border-b border-solid border-gray-400"><a class="inline-block px-4 py-0  lg:mr-1 lg:mb-0 ml-3 mb-3 ease-linear transition-all duration-150 uppercase font-bold" href="{{ URL::to('/search')}}">All Categories</a>
</div>
<div class="p-2">
@if($left_tree->count()==0)
<div>No Subcategories Found</div>
@endif

@foreach($left_tree as $t)
<?php $boldclass =($t['title']==$cat_detail[0]['title'])?'font-bold':''; ?>
<div class="py-1 {{ $boldclass }}"><a href="{{URL::to('/category/'.$t['slug'])}}">{{$t['title']}}</a></div>
@endforeach
</div>

<div class="p-2 text-center border-b border-t border-solid border-gray-400 text-md font-bold uppercase">Date Posted</div>
<div class="p-2">
<?php $df = request()->days_last;?>
<div><input <?php echo ($df=="1")?"checked":""; ?> type="radio" id="day_filter_1" name="day_filter" value="1" class="mr-1" />24 hours</div>
<div><input <?php echo ($df=="3")?"checked":""; ?> type="radio" id="day_filter_3" name="day_filter" value="3" class="mr-1" />3 days</div>
<div><input <?php echo ($df=="7")?"checked":""; ?> type="radio" id="day_filter_7" name="day_filter" value="7" class="mr-1"  />7 days</div>
<div><input <?php echo ($df=="30")?"checked":""; ?> type="radio" id="day_filter_30" name="day_filter" value="30" class="mr-1" />30 days</div>
</div>

<div class="p-2 text-center border-b border-t border-solid border-gray-400 text-md font-bold uppercase">Price Range</div>
<?php 
$min_p = request()->min_price;
$max_p = request()->max_price;
?>
<div class="p-2">
<div class="flex flex-wrap">
<div class="w-full lg:w-4/12 px-1">
<div class="relative w-full mb-3">
<input type="number" class="p-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" value="{{ $min_p }}" min=0 placeholder="from" id="price_from" >
</div>
</div>
<div class="w-full lg:w-4/12 px-1">
<div class="relative w-full mb-3">
<input type="number" class="p-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" value="{{ $max_p }}" min=0 placeholder="to" id="price_to" >
</div>
</div>
<div class="w-full lg:w-4/12 px-1">
<div class="relative w-full mb-3">
<input type="button" class="p-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" id="price_range" value="Go">
</div>
</div>
</div>


</div>

</div>
</div>

<!--right portion-->
<div class="w-full md:w-9/12 ml-auto mr-auto px-4 bg-gray-100">
<div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-1 border border-gray-300 rounded bg-gray-100 p-2">

<div class="flex flex-wrap bg-gray-100 mt-1 border-0 rounded items-center">
<div class="flex flex-col lg:flex-row list-none mr-auto text-sm">
<label class="mr-1">Ads in: <a class="inline-block text-white bg-indigo-600 text-xs px-2 py-1 rounded shadow hover:shadow-md outline-none focus:outline-none lg:mr-1 lg:mb-0 mb-3 ease-linear transition-all duration-150" href="{{ URL::to('search') }}">{{$cat_detail[0]['title'] }} <i class="fa fa-times"></i> </a></label>
<?php $dfl = request()->days_last;
if($dfl!=""){
$dfl = ($dfl=="1")?"24 hours":$dfl." days";
$dflurl = URL::to('/'.request()->path());
?>
<lable>last <a class="inline-block text-white bg-indigo-600 text-xs px-2 py-1 rounded shadow hover:shadow-md outline-none focus:outline-none lg:mr-1 lg:mb-0 mb-3 ease-linear transition-all duration-150" href="{{ $dflurl }}">{{$dfl }} <i class="fa fa-times"></i> </a></lable>
<?php } ?>


</div>

<div class="flex flex-col lg:flex-row list-none lg:ml-auto">
<?php $ordby = request()->order_by;?>
<select id="order_by" class="px-3 py-1 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150">
<option value="" <?php echo ($ordby=="")?"selected='selected'":"" ?>>Sort By</option>
<option value="priceAsc" <?php echo ($ordby=="priceAsc")?"selected='selected'":"" ?>>Price: Low to High</option>
<option value="priceDesc" <?php echo ($ordby=="priceDesc")?"selected='selected'":"" ?>>Price: High to Low</option>
<option value="date" <?php echo ($ordby=="date")?"selected='selected'":"" ?>>Date</option>
</select>


</div>
</div>

</div>

<div class="flex flex-wrap mt-2 mb-2">
@if($post_detail!="")
<?php $i=0;?>
@foreach($post_detail as $d)
<?php 
$i++;
$imgUrlfinal = URL::to('storage/noimage50.png');
if($d["images"]!="")
{
  $imgUrl = explode(',',$d["images"])[0];
  $imgUrl = URL::to('storage/'.$imgUrl);
  $imgUrlfinal = $imgUrl;
}
else{
  $imgUrlfinal = URL::to('storage/noimage150.png');
}
$posted_on = date('d M y',strtotime($d['created_at']));
$prd_url = URL::to('post/'.$d['slug']);

$reviewcount = App\Models\TblReview::review_count($d["id"]);
$avg_rating = App\Models\TblReview::rate_avg($d["id"]);

?>

<div class="w-full md:w-6/12 lg:w-3/12 lg:mb-0 mb-12 px-4 py-2 border border-black-100">
<div class="px-4">
<a href="{{ $prd_url }}">
<img style="width:200px;height:150px" alt="..." src="{{ $imgUrlfinal }}" class="shadow-lg mx-auto">
<div class="pt-3"><h5 class="text-md font-bold">{{ $d['title'] }}</h5></div>
</a>
<div class="mt-1">
<span class="inline-flex items-center mr-2 text-xs">{{ $posted_on }}</span>
<span class="inline-flex items-center mr-2 text-xs"><i class="fa fa-map-marker fa-xs">&nbsp;</i> {{ $d['city_name' ]}}</span></div>


<!-- common star rating begin-->
<div class="mt-1">
<?php 
$starwhite = "fa fa-star-o";
$starFill = "fa fa-star";
$starbg = "text-yellow-500";
?>
<div class="mt-2 items-center text-xs">
<label class="inline-flex items-center mr-1 {{ ($avg_rating>=1)?$starbg:'' }}"><i class="{{ ($avg_rating>=1)?$starFill:$starwhite }}"></i></label>
<label class="inline-flex items-center mr-1 {{ ($avg_rating>=2)?$starbg:'' }}"><i class="{{ ($avg_rating>=2)?$starFill:$starwhite }}"></i></label>
<label class="inline-flex items-center mr-1 {{ ($avg_rating>=3)?$starbg:'' }}"><i class="{{ ($avg_rating>=3)?$starFill:$starwhite }}"></i></label>
<label class="inline-flex items-center mr-1 {{ ($avg_rating>=4)?$starbg:'' }}"><i class="{{ ($avg_rating>=4)?$starFill:$starwhite }}"></i></label>
<label class="inline-flex items-center mr-1 {{ ($avg_rating>=5)?$starbg:'' }}"><i class="{{ ($avg_rating>=5)?$starFill:$starwhite }}"></i></label>
<label class="inline-flex items-center mr-1">{{ $reviewcount }} review</label>
</div>
</div>
<!-- common start rating end-->


<div class="mt-1 text-right text-sm font-bold">{{ $d['price'] }}</div>
<?php 
$fav_style="bg-gray-800";
if(auth()->user())
{
	$userid = auth()->user()->id;
	$getRows = App\Models\TblSavedPosts::where('user_id',$userid)->where('post_id',$d->id)->get();
	if($getRows->count()>0){ $fav_style = "bg-indigo-500"; }
}
?>
<div class="mt-1 text-right text-xs text-white"><button type="button" id="favourate_post_id_{{$i}}" data-fav-post-row-id="{{$i}}" data-fav-post-id="{{ $d->id }}" value="{{$d->title}}" class="save_favourate px-1 border rounded border border-white  <?php echo $fav_style;?> "><i class="fa fa-heart" aria-hidden="true"></i></button></div>

</div>

</div>
@endforeach
@else
<div>No records Found</div>
@endif

</div>



</div>
</div>
</div>
</section>



<script>
$(document).ready(function ()
{
  var baseurl = "{{ URL::to('/'.request()->path()) }}";
  var currurl = document.location.href;

  $('input[type=radio][name=day_filter]').click(function() {
    var url = "";
    var qV =$(this).val();
    //check not exist  parameter
    if(currurl.indexOf('?')==-1) { url = currurl+"?days_last="+qV; }
    else{ url = currurl+"&days_last="+qV; }
    //alert(url);
    location.href = url;
		});


    $("#order_by").change(function(){
      var qV =$(this).val();
      var url = "";
    //check not exist  parameter
    if(currurl.indexOf('?')==-1) { url = currurl+"?order_by="+qV; }
    else{ url = currurl+"&order_by="+qV; }
    //alert(url);
    location.href = url;

    });


    $("#price_range").click(function(){
      var from = $("#price_from").val();
      var to = $("#price_to").val();
      
      //do nothing if both empty
      if(from=="" && to==""){ return false; }
      if(to!="" && to<from){alert('invalid input'); return false;}
      
      if(currurl.indexOf('?')==-1) { 
        url = currurl+"?min_price="+from+"&max_price="+to; 
      }
      else{
        url = currurl+"&min_price="+from+"&max_price="+to;
      }

      location.href = url;
      


    });
    

});

</script>