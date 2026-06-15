	<?php
		
		$dir_rtl =  App\Models\Setting::is_dir_rtl();
		$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
		$class_dir_mar_rl = ($dir_rtl=="true")?'ml-2':'mr-2';
		$class_dir_text_pad_lr = ($dir_rtl == "true") ? 'lg:pr-12 text-right' : 'lg:pl-12 text-left';
	
	?>
	
	<div class="w-full float-left" {{$class_dir}}>

		<?php 
			$get_meta = App\Models\TblOtherpage::get_meta('mypackage-add');
			$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
			$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
			$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");

		?>

		@if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description))
			@section('meta_title', $meta_title)
			@section('meta_keywords', $meta_keywords)
			@section('meta_description', $meta_description)
		@endif

		<div class="w-full float-left bg-gray-100 h-52 mt-6">
			<div class="container mx-auto px-4">
				<h1 class="text-xl md:text-2xl font-bold text-black my-4 sm:my-6 lg:my-8 uppercase pt-6 text-center">{{__('p_choose_package.select post for package')}}</h1>
			</div>
		</div>
			
		<div class="w-full float-left">
			<div class="container px-4 mx-auto">
				<div class="p-5 w-full bg-white shadow-lg float-left mb-8 sm:mb-12 md:mb-16 lg:mb-20 lg:p-12 relative -mt-20">
					@if(count($list) > 0)
					<div class="w-full float-left mb-4 sm:mb-6">
						<button class="bg-green-500 text-white font-bold text-sm px-4 py-2 sm:px-6 sm:py-3 uppercase w-full sm:w-auto text-center rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-500 border-2 border-green-500 hover:bg-white hover:text-green-500"><a href="{{URL::to('/mypackage')}}">{{__('p_choose_package.go back')}}</a>
						</button>
					</div>
					<form action="{{ URL::to('mypackage-add-save') }}" method="POST">
					  @csrf
					  <div class="w-full float-left overflow-auto h-72">
						<table class="ml-4 sm:ml-8 lg:ml-10">
						  <tbody>
						  
							<?php foreach($list as $row)
							{
							$imgUrlfinal = App\Models\TblChat::getPostImg($row->id);
							$final_city_name = !empty($row->locality) ? $row->locality : $row->city_name;


							//get currency
							$settings = App\Models\Setting::get_logos();
							$slected_currency = !empty($post_det[0]->currency_id) ? $post_det[0]->currency_id : $settings['default_currency'];
							$currency_symbol = App\Models\TblPost::get_post_currency($slected_currency);
							$currency = $currency_symbol[0];

							$post_price = $row->price;
							$slug = App\Models\TblPost::get_post_slug($row->slug);
								 ?>
							<tr>
									
							<td class="p-2 py-7 border border-gray-300 md:border-0 text-center text-xs md:text-sm text-gray-500 font-medium">
										<div class="sm:px-2">
											<input type="checkbox" class="del_check w-4 h-4" name="postid[]" value="<?php echo $row->id; ?>" />
										</div>
									</td>

									<td class="py-2 lg:py-7 border border-gray-300 md:border-0 " align="center">
										<a href="{{$slug}}" target="_blank">
											<img class="w-16 h-16 lg:w-18 lg:h-18 object-cover " src="{{$imgUrlfinal}}">
										</a>
									</td>

									<td class="border border-gray-300 md:border-0 py-2 lg:py-7 p-2 {{$class_dir_text_pad_lr}}">
										<p><strong class=" inline-block pb-1 text-sm md:text-lg font-semibold"><a class="text-gray-900" href="{{$slug}}" target="_blank">{{$row["title"]}}</a></strong></p>
										<p><strong class="inline-block pb-1 text-xs md:text-sm text-gray-500 font-medium"><span class="text-green-500"></span> <?php echo $currency." ".$post_price;?> </strong></p>
										<p><strong class="inline-block pb-1 text-xs md:text-sm text-gray-500 font-medium"><span class="text-green-500"><i class="fa fa-map-marker" aria-hidden="true"></i> </span>{{$final_city_name}}</strong></p>
									</td>

								</tr>

							<?php } ?>
						  
						  
							<!--@foreach($list as $row)
							<tr class="mb-2 block">
							  <td>
								<input class="{{$class_dir_mar_rl}} postid text-base text-black" type="checkbox" name="postid[]" value="<//?php echo $row->id; ?>" />
							  </td>
							  <td class="text-base text-black">{{$row->title}}</td>
							</tr>
							@endforeach-->
						  </tbody>
						</table>
					  </div>
					  <?php
					  $remining_ads =  App\Models\TblBulkPackPayment::get_remaining_ads(request()->p);
					  ?>
					  <input type="hidden" name="package_id" value="<?php echo request()->p; ?>">
					  <input type="hidden" class="maxAllowed" value="<?php echo $remining_ads; ?>">
					  <div class="w-full float-left mt-4 sm:mt-6">
					  <button class="bg-green-500 text-white font-bold text-sm px-4 py-2 sm:px-6 sm:py-3 uppercase w-full sm:w-auto text-center rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-500 border-2 border-green-500 hover:bg-white hover:text-green-500 submitclass">{{__('p_choose_package.submit')}}</button>
					  </div>
					</form>

					@else
					<div class="w-full text-center">
					  <img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto" />
					  <p class="text-2xl pl-2 pb-1 font-bold mb-8 sm:mb-12">Can't find any ads posted by you!</p>
					</div>
					@endif
				</div>
			</div>
		</div>
	</div>
<script>
  $(document).ready(function() {
    $(".postid").change(function() {
      var maxAllowed = $(".maxAllowed").val();
      var cnt = $("input:checkbox[name='postid[]']:checked").length;
      if (cnt > maxAllowed) {
        $(this).prop("checked", "");
        alert('You can select maximum ' + maxAllowed + ' posts!');
      }
    });

    $(".submitclass").click(function() {
      var cnt = $("input:checkbox[name='postid[]']:checked").length;
      if (cnt == 0) {
        toastr.warning("Choose any post");
        return false;
      }

      if (!confirm("Once Ads Assign To this pack, Can't able to modify.\nAre you sure to save?")) {
        return false;
      }
    });

  });
</script>