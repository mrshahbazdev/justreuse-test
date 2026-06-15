@extends('layouts.frontendother')
@section('content')
<?php 
		$get_meta = App\Models\TblOtherpage::get_meta('banner-advertise');
		$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
		$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
		$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
	
	?>
	
	@if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description))
        @section('meta_title', $meta_title)
        @section('meta_keywords', $meta_keywords)
        @section('meta_description', $meta_description)
	@endif

<div class="py-0">
   @if ($message = Session::get('message'))
   <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-yellow-500 z-50">
      <span class="text-xl inline-block mr-5 align-middle"><i class="fas fa-bell"></i></span>
      <span class="inline-block align-middle mr-8"><b class="capitalize"></b> {{ $message }}</span>
      <button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none" onclick="closeAlert(event)"><span>×</span></button>
   </div>
   @endif
   
	<div class="w-full float-left">
		<div class="container mx-auto px-4">
			<h1 class="text-xl md:text-2xl font-bold text-black my-4 sm:my-6 lg:my-8 uppercase">{{__('messages.my banner ads')}}</h1>
		</div>
	</div>
	
   <div class="w-full bg-gray-100 h-48 float-left">
      
   </div>
		
		<div class="w-full float-left">
			<div class="container mx-auto px-4">
			   <div class="p-5 w-full bg-white shadow-lg float-left mb-8 sm:mb-12 md:mb-16 lg:mb-20 lg:p-12 relative -mt-36">
				  @if(!empty($payment_methods))
				  <form id="BannerAdsForm" method="post" action="#" enctype="multipart/form-data">
					 @csrf
					 <div class="mb-4 w-full float-left lg:mb-14">
						<!--<div class="m-4 md:w-6/12">-->
						   <label class="w-full m-0 text-lg text-gray-800 font-semibold pb-4 inline-block">{{__('p_my_banner_ads.banner image for website')}} (1200 X 400) <span class="text-red-500">*</span></label>

							<div class="w-full lg:w-2/5 rounded-lg float-left">
							   <div class="w-full bg-gray-100 border-l-2 border-gray-400 my-2 lg:w-2/4 rounded-lg choose-file float-left">
								  <!-- <input type="file" id="web_banner" required accept="image/*" name="web_banner" class="opacity-0 w-3/12 hidden"/> -->

                          <input type="file" id="web_banner" required accept="image/*" name="web_banner" class="appearance-none border-l-2 border-gray-400 bg-gray-100 rounded-md w-full py-4 px-6 placeholder-gray-700 text-green-500 font-semibold focus:outline-none text-base italic text-left cursor-pointer ">

								  <!-- <label class="text-gray-500 text-base font-medium cursor-pointer w-full inline-block px-6 py-4" for="web_banner">Choose file</label> -->
							   </div>
							   <!-- <span class="hidden w-full text-base  inline-block text-center text-gray-500 text-base font-medium italic px-6 pt-3 pb-3 my-2 lg:w-2/4 lg:inline-block">No Choosen File</span> -->
							</div>

						


						<!--</div>-->
						<!--<div class="m-4 md:w-6/12">-->
						<div class="my-2 w-full lg:w-2/4 rounded-lg lg:-my-4 float-right">
						   <label class="text-gray-500 text-base font-medium">{{__('p_my_banner_ads.web link')}} <span class="text-red-500">*</span></label>
						   <input type="text" required class="appearance-none my-2 border-l-2 border-gray-400 bg-gray-100 rounded w-full py-4 px-3 text-gray-700 leading-tight focus:outline-none" name="web_link" />
						</div>
						<!--</div>-->
					 </div>

					 <div class="mb-4 w-full float-left lg:mb-14">
						<!--<div class="m-4 md:w-6/12">-->
						   <label class="w-full m-0 text-lg text-gray-800 font-semibold pb-4 inline-block">{{__('p_my_banner_ads.banner image for mobile app')}} (1024 X 500)<span class="text-red-500">*</span></label>
							<div class="w-full lg:w-2/5 rounded-lg float-left">
							   <div class="w-full bg-gray-100 border-l-2 border-gray-400 my-2 lg:w-2/4 rounded-lg choose-file float-left">
								  <!-- <input type="file" id="app_banner" accept="image/*" required name="app_banner"  class="opacity-0 w-3/12 hidden"/> -->

                          <input type="file" id="app_banner" required accept="image/*" name="app_banner" class="appearance-none border-l-2 border-gray-400 bg-gray-100 rounded-md w-full py-4 px-6 placeholder-gray-700 text-green-500 font-semibold focus:outline-none text-base italic text-left cursor-pointer ">

								  <!-- <label class="cursor-pointer w-full inline-block px-6 py-4 text-gray-500 text-base font-medium" for="app_banner">Choose file</label> -->
							   </div>
								<!-- <span class="hidden w-full text-base  inline-block text-center text-gray-500 text-base font-medium rounded-lg italic px-6 pt-3 pb-3 my-2 lg:w-2/4 lg:inline-block ">No Choosen File</span> -->
							</div>
						<!--</div>-->
						<!--<div class="m-4 md:w-6/12">-->
							<div class="my-2 w-full lg:w-2/4 rounded-lg lg:-my-4 float-right">
							   <label class="text-gray-500 text-base font-medium">{{__('p_my_banner_ads.app link')}} <span class="text-red-500">*</span></label>
							   <input type="text" required class="my-2 appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-4 px-3 text-gray-700 leading-tight focus:outline-none" name="app_link" />
							</div>
						<!--</div>-->
					 </div>


					 <div class="w-full float-left">
						<label class="m-0 text-lg text-gray-800 font-semibold pb-4">{{__('p_my_banner_ads.where your banner display in live')}}? <span class="text-red-500">*</span></label>
						<div class="w-full float-left">
							<div class="w-full my-2 float-left">
								<select required name="banner_display_page" id="banner_display_page" class="my-2 banner_display_page border-l-4 border-gray-400 bg-gray-100 rounded-lg w-full py-4 px-3 text-gray-700 leading-tight focus:outline-none">
								   <option value="">[--- {{__('p_my_banner_ads.select page')}} ---]</option>
								   <option value="home">{{__('p_my_banner_ads.in home page')}}</option>
								   <option value="search">{{__('p_my_banner_ads.in search list page')}}</option>
								</select>
							</div>
						</div>
					 </div>
					 
					 
					 
					 
					 

					 <div class="m-4 select_banner_category">
						<label class="text-gray-700 text-sm font-bold">{{__('p_my_banner_ads.for which category you are going to add banner')}}? <span class="text-red-500">*</span></label>
						<select name="banner_category" class="my-2 banner_category appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-4 px-3 text-gray-700 leading-tight focus:outline-none">
						   <option hidden="" disabled="disabled" selected="selected" value="">{{__('p_myads.please select')}}</option>
						   <?php
						   $traverse = function ($categorylist, $prefix = '') use (&$traverse) {
							  foreach ($categorylist as $category) {
								 $catname = $prefix . '| ' . $category->title;
								 $catid = $category->id;
								 echo '<option value="' . $catid . '">' . $catname . '</option>';
								 $traverse($category->children, $prefix . '--');
							  }
						   };
						   $traverse($categorylist);
						   ?>
						</select>
					 </div>

					 <div class="based_on_selected_page">
						<div class="pt-5 default_btn text-center m-auto">
						   <button class="w-2/2 p-4 bg-yellow-500 text-xl font-bold opacity-50 rounded shadow hover:shadow-md outline-none focus:outline-none cursor-not-allowed">Save</button>
						</div>
						<div class="m-4">
						   <label class="text-gray-700 text-sm font-bold">{{__('p_my_banner_ads.when your banner display in live')}}? <span class="bg-yellow-500 p-1 text-white rounded mt-2 md:mt-0 md:float-right per_day_amt_txt"> $100 Per day</span></label>
						   <input type="hidden" name="per_day_amt" id="per_day_amt" value="100" />
						   <input type="hidden" id="live_days" name="live_days" value="1" />
						</div>

						<div class="md:flex">
						   <div class="m-4 md:w-6/12">
							  <label class="text-gray-700 text-sm font-bold">{{__('p_my_banner_ads.start date')}} <span class="text-red-500">*</span></label>
							  <input type="date" required id="start_date" class="date my-2 appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-4 px-3 text-gray-700 leading-tight focus:outline-none" name="start_date" />
						   </div>
						   <div class="m-4 md:w-6/12">
							  <label class="text-gray-700 text-sm font-bold">{{__('p_my_banner_ads.end date')}} <span class="text-red-500">*</span></label>
							  <input type="date" required id="end_date" class="date my-2 appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-4 px-3 text-gray-700 leading-tight focus:outline-none" name="end_date" />
						   </div>
						</div>
						<div class="p-2">
						   <p id="days_desc"></p>
						</div>
						<div class="block p-2 border-b border-solid border-white">
						   <span class="text-gray-700 text-sm font-bold">{{__('p_my_banner_ads.payment methods')}} <span class="text-red-500">*</span></span>
						   <div class="mt-2 md:flex">
							  <?php
							  $i = 0;
							  foreach ($payment_methods as $p) {
								 $checkString = ($i == 0) ? "checked=checked" : "";
								 $class =  ($i == 0) ? "mr-3" : "";
								 $i++;
							  ?>
								 <label class="inline-flex items-center mb-2 {{$class}}">
									<input type="radio" class="form-radio" name="payment_type" value="{{$p['name']}}" {{$checkString}}>
									@if(strtolower($p['display_name']) == "paypal")
									<span><img src="{{ URL::to('/images/web_paypal.png') }}" class="h-10 md:h-12 ml-3 -mt-1" /></span>
									@elseif(strtolower($p['display_name']) == "stripe")
									<span><img src="{{ URL::to('/images/stripe_logo.png') }}" class="h-10 md:h-12 ml-2" /></span>
									@else
									<span class="ml-2">{{$p['display_name']}}</span>
									@endif
								 </label>
							  <?php } ?>
							  <div>
							  </div>
						   </div>
						</div>
						<input type="hidden" name="final_total_amount" id="final_total_amount" value="100" />
						<?php
						$settings = App\Models\Setting::get_logos();
						//$currency_symbol = App\Models\TblCurrency::where('id', $settings['default_currency'])->pluck('currency_hex')->first();

						$currency_symbol1 = App\Models\Setting::get_admin_default_currency();
						$currency_symbol = $currency_symbol1['currency_hex'];
						$currency_id = $currency_symbol1['id'];
						?>
						<input type="hidden" class="banner-currency" value="<?php echo $currency_symbol; ?>" />
						<input type="hidden" name="currency_id" value="<?php echo $currency_id; ?>" />
						<div class="pt-5">
						   <?php if (!empty($currency_symbol)) { ?>
							  <button id="pay_proceed" type="submit" class="w-full bg-green-500 text-white font-bold text-xl p-2 uppercase text-center rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 border-2 border-green-500 hover:bg-white hover:text-green-500">{{__('p_choose_package.pay')}}
								 <span class="text-2xl font-bold">
									<?php echo $currency_symbol; ?> <span id="show_price_2">100</span>
								 </span>
							  </button>
						   <?php } else { ?>
							  <button class="not-allowed cursor-not-allowed w-full bg-green-500 text-white font-bold text-xl p-2 uppercase text-center rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 border-2 border-green-500 hover:bg-white hover:text-green-500">{{__('p_choose_package.pay')}}
								 <span class="text-2xl font-bold"></span>
							  </button>
						   <?php } ?>
						</div>

					 </div>
				  </form>
				  @else
				  <div class="text-center">
					 <p>{{__('p_my_banner_ads.payment methods are not activated, please contact admin')}}!</p>
				  </div>
				  @endif
			   </div>
			</div>
		</div>
<div id="overlay"></div>
<?php
if (!empty($payment_methods)) {
   foreach ($payment_methods as $p) {
      $base_path = base_path() . '/extra/plugins/' . $p['name'] . '/src/banner_ads_view.php';
      if (is_file($base_path)) {
         include_once($base_path);
      }
   }
}
?>
<script>
   $(document).ready(function() {
      var _URL = window.URL || window.webkitURL;

      $('#web_banner').change(function() {
         var imgwidth1 = 0;
         var imgheight1 = 0;
         var wb_file = $(this)[0].files[0];
         var x_img = new Image();
         x_img.src = _URL.createObjectURL(wb_file);
         x_img.onload = function() {
            imgwidth1 = this.width;
            imgheight1 = this.height;
            if (parseFloat(imgwidth1) != 1200) {
               alert("Improper image size uploaded.\nBanner Image For Website (1200px X 400px)");
               $("#web_banner").val("");
               return false;
            } else if (parseFloat(imgheight1) != 400) {
               alert("Improper image size uploaded.\nBanner Image For Website (1200px X 400px)");
               $("#web_banner").val("");
               return false;
            }
         };
      });

      $('#app_banner').change(function() {
         var imgwidth1 = 0;
         var imgheight1 = 0;
         var wb_file = $(this)[0].files[0];
         var x_img = new Image();
         x_img.src = _URL.createObjectURL(wb_file);
         x_img.onload = function() {
            imgwidth1 = this.width;
            imgheight1 = this.height;
            if (parseFloat(imgwidth1) != 1024) {
               alert("Improper image size uploaded.\nBanner Image For App (1024px X 500px)");
               $("#app_banner").val("");
               return false;
            } else if (parseFloat(imgheight1) != 500) {
               alert("Improper image size uploaded.\nBanner Image For App (1024px X 500px)");
               $("#app_banner").val("");
               return false;
            }
         };
      });


   });


   $(".based_on_selected_page").hide();
   $(".select_banner_category").hide();
   $(".banner_display_page").on('change', function() {
      var selected_val = $(this).val();
      var cat_id = "";
      get_banner_price(selected_val, cat_id);
      if (selected_val == "search") {
         $(".select_banner_category").show();
         $(".banner_category").prop('required', true);
         $(".based_on_selected_page").hide();
         $(".default_btn").show();
      } else {
         $(".select_banner_category").hide();
         $(".banner_category").prop('required', false);
         $(".based_on_selected_page").hide();
         $(".default_btn").show();
      }
   });

   $(".banner_category").on('change', function() {
      var page = $(".banner_display_page").val();
      var cat_id = $(this).val();
      get_banner_price(page, cat_id);
   });

   // get price based on selecd page and category
   function get_banner_price(page, cat_id) {
      $.ajax({
         type: 'POST',
         dataType: 'json',
         url: "{{ URL::to('get_banner_price') }}",
         data: {
            page: page,
            cat_id: cat_id
         },
         cache: false,
         success: function(data) {
            var currency = $(".banner-currency").val();
            var txt_price = currency + data + " Per day";
            $("#per_day_amt").val(data);
            $(".per_day_amt_txt").text(txt_price);
            $("#show_price_2").text(data);
            $("#final_total_amount").val(data);
            $(".based_on_selected_page").show();
            $(".default_btn").hide();
            var total_amnt = data;
            var days = 1;
            if (($("#live_days").val() != "") && ($("#live_days").val() > 0)) {
               days = $("#live_days").val();
               total_amnt = $("#live_days").val() * data;
            }
            $("#days_desc").text("Your ads will run for " + days + " days. You have to spend " + currency + Math.round(total_amnt));
            $("#days_desc").addClass("bg-red-300 p-2 mt-3 rounded");
         }
      });
   }
   $('#start_date').on('change', function() {
      var date1 = new Date($("#start_date").val());
      var date2 = new Date($("#end_date").val());

      if(date1>date2)
      {
         $("#start_date").val($("#end_date").val());
         toastr.error("Start date must be less than (or) equal to End date");
         return;
      }

      var currency = $(".banner-currency").val();
      // To calculate the time difference of two dates
      var Difference_In_Time = date2.getTime() - date1.getTime();
      // To calculate the no. of days between two dates
      var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24) + 1;
      $("#live_days").val(Difference_In_Days);
      if (Difference_In_Days > 0) {
         var per_day = $("#per_day_amt").val();
         var total_amnt = Difference_In_Days * per_day;
         $("#final_total_amount").val(Math.round(total_amnt));
         $("#show_price_2").text(Math.round(total_amnt));
         $("#days_desc").text("Your ads will run for " + Difference_In_Days + " days. You have to spend " + currency + Math.round(total_amnt));
         $("#days_desc").addClass("bg-red-300 p-2 mt-3 rounded");
      }
   });
   $('#end_date').on('change', function() {
      var date1 = new Date($("#start_date").val());
      var date2 = new Date($("#end_date").val());
      var currency = $(".banner-currency").val();
      // To calculate the time difference of two dates
      var Difference_In_Time = date2.getTime() - date1.getTime();
      // To calculate the no. of days between two dates
      var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24) + 1;
      $("#live_days").val(Difference_In_Days);
      if (Difference_In_Days > 0) {
         var per_day = $("#per_day_amt").val();
         var total_amnt = Difference_In_Days * per_day;
         $("#final_total_amount").val(Math.round(total_amnt));
         $("#show_price_2").text(Math.round(total_amnt));
         $("#days_desc").text("Your ads will run for " + Difference_In_Days + " days. You have to spend " + currency + Math.round(total_amnt));
         $("#days_desc").addClass("bg-red-300 p-2 mt-3 rounded");
      }
   });
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
   $('.not-allowed').on('click', function() {
      toastr.warning("Oops something went wrong!");
      return false;
   });
</script>
@endsection