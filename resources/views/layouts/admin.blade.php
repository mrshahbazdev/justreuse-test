<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#000000" />
  <?php
  $settings = App\Models\Setting::get_logos();
  ?>
  <!--meta data title and description start-->
  <title>{{$settings['name']}}</title>
  <meta name="title" content="{{$settings['meta_title']}}">
  <meta name="description" content="{{$settings['meta_desc']}}">
  <!--meta data title and description end-->
  <!-- css start -->
  <link rel="stylesheet" href="{{ URL::to('fontawesome-free/css/all.min.css') }}">
  <link rel="shortcut icon" href="{{ URL::to('/storage/'.$settings['fav_icon']) }}">
  <!-- <link rel="stylesheet" href="{{ URL::to('css/all.min.css') }}"> -->
  <link href="{{ URL::to('css/toastr.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ URL::to('css/image-uploader.min.css') }}">
  <link rel="stylesheet" href="{{ URL::to('css/app.css') }}">
  <link rel="stylesheet" href="{{ URL::to('css/admin-custom.css') }}">
  <!-- css end -->
  <!-- js start -->
  <!--script src="{{ URL::to('js/tinymce.min.js') }}"></script-->
    <script src="{{ URL::to('ckeditor_4_15_1/ckeditor.js') }}"></script>
  <script src="{{ URL::to('js/jquery.min.js') }}"></script> 
  <script src="{{ URL::to('js/jquery-ui.js') }}"></script>
  <script src="{{ URL::to('js/toastr.min.js') }}"></script>  
  <script src="{{ URL::to('js/image-uploader2.min.js') }}"></script>
  <script src="{{ URL::to('js/jquery.doubleScroll.js') }}"></script>
  <script src="{{ URL::to('js/app.js') }}" defer></script>
  @livewireStyles  
</head>

<body class="font-sans text-gray-800 antialiased">
  <div id="root">
    <!-- Page Content -->
    @include('livewire.common.sidenav')

    <div class="relative md:ml-64 bg-gray-100 admin_right">
      @include('livewire.admin.dashboard.admin-dropdown')
	  <div class="test">
	  
	  <?php  $seg2 = request()->segment(2);
          $open_title =($seg2 == "dashboard") ? 'false' : 'true';
		  
          $dashboard = route('dashboard');
          

switch ($seg2) {
  case "posts":
     $title = 'Posts';
     $url = route('admin/post');
    break;
  case "category":
   $title = 'Category';
   $url = route('admin/category');
    break;
  case "report-ad":
     $title = 'Report Ads';
     $url = route('admin/report-ad') ;
    break;
	 case "blocked-posts":
    $title = 'Blocked Posts';
    $url = route('admin/blocked-post');
    break;
	 case "user":
    $title ='User';
    $url = route('admin/user');
    break;
	 case "role":
    $title = 'Role';
    $url = route('admin/role');
    break;
	case "permissions":
    $title = 'Permissions';
    $url = route('admin/permissions');
    break;
	 case "settings":
    $title = 'Settings';
    $url = route('admin/settings');
    break;
	 case "packages":
    $title = 'Packages';
    $url = route('admin/package');
    break;
	case "report-type":
    $title = 'Report Type';
    $url = route('admin/report-type');
    break;
	 case "payment-methods":
    $title = 'Payments Methods';
    $url = route('admin/payment-methods');
    break;
	 case "payments":
    $title = 'Payments';
    $url = route('admin/payment');
    break;
	 case "reviews":
    $title = 'Reviews';
    $url =  route('admin/review');
    break;
	 case "staticpage":
    $title = 'Static Page';
    $url = route('admin/staticpage');
    break;
	 case "coupon":
    $title = 'Coupon';
    $url =  route('admin/coupon') ;
    break;
	 case "languages":
    $title = 'Languages';
    $url = route('admin/language');
    break;
	 case "country":
    $title = 'Country';
    $url =  route('admin/country');
    break;
	 case "advertising":
    $title = 'Advertisements';
    $url = route('admin/advertising');
    break;
	 case "blacklist":
    $title = "Blacklist";
    $url = route('admin/blacklist');
    break;
	 case "contact-us":
    $title = 'Contact Us';
    $url = route('admin-contact-us');
    break;
	 case "report-user":
    $title = 'Report Users';
    $url  = route('admin/report-user');
    break;
	 case "home-banner":
    $title = 'Home Banner ';
    $url = route('admin/home-banner');
    break;
	 case "banner-advertisements":
    $title = 'Banner Advertisements';
    $url = route('admin/banner-advertisements');
    break;
	 case "currency":
    $title = 'Currency';
    $url = route('admin/currency');
    break;
	 case "email-template":
    $title = 'Email Template';
    $url = route('admin/email-template');
    break;
	 case "buynow-orders":
    $title = 'Buy Now Orders';
    $url =  route('admin/buynow-orders');
    break;
	 case "bulk-payments":
    $title = 'Bulk Payments';
    $url = route('admin/bulk-payments');
    break;
	 case "post-methods":
    $title = "Post Methods";
    $url =route('admin/post-methods');
    break;
	 case "chat-methods":
    $title = 'Chat Methods';
    $url =route('admin/chat-methods');
    break;
	 case "otherpage":
    $title = 'Other Pages';
    $url = route('admin/otherpages');
    break;
	 case "bulk-email":
    $title = "Bulk Email";
    $url = route('admin/bulk-email'); 	
    break;
    case "keys-update":
     $title = 'Payment Methods';
    $url = route('admin/payment-methods'); 	
  break;
    case "view-order-invoice":
     $title = 'Order Invoice';
    $url = ""; 	

  break;
    case "features-map-show":
     $title = 'Features Mapping';
    $url = ""; 	
  break;
    case "features-map-edit":
     $title = 'Features Mapping Edit';
    $url = ""; 	
  break;
  case "features-map":
    $title = 'Features Mapping Create';
   $url = ""; 
   break;
  case "features":
    $title = 'Features Import';
   $url = ""; 
   break;
   break;
  case "features-order-list":
    $title = 'Features Reorder List';
   $url = ""; 
   break;
  default:
  $title = 'Justreused';
  $url = "route('admin/dashboard')";
}
if($open_title == 'true'){
	
	  ?>
	  <div class="md:flex items-center justify-between p-5 pt-7 pb-3">
	  <div class="mb-3 md:mb-0 text-xl leading-relaxed inline-block  font-semibold  whitespace-no-wrap text-gray-600" >{{$title}}</div>
    <ul class="breadcrumb text-blue-500 text-sm py-2 flex gap-3 bg-gray-100 px-6   rounded-md w-full md:w-auto">
	<li class="breadcrumb-item"><a class="text-black " href="{{ URL::to('admin/dashboard') }}">Dashboard <span class="fa fa-angle-double-right pl-2 text-orange-500"></span></a></li>
	<li class="breadcrumb-item "><a class="text-orange-500  " href="{{$url}}">{{$title}}</a></li>

</ul>
</div>
<?php } ?>
      {{ $slot }}
	  
		</div>
      @include('livewire.common.footer')
      @stack('modals')
      @livewireScripts
    </div>


  </div>
  <script src="{{ URL::to('js/tailwind_popper.js') }}" defer></script>
  <script>
    /* Make dynamic date appear */
    (function() { 
      if (document.getElementById("get-current-year")) {
        document.getElementById(
          "get-current-year"
        ).innerHTML = new Date().getFullYear();
      }
    })();
    /* Function for opning navbar on mobile */
    function toggleNavbar(collapseID) {
      document.getElementById(collapseID).classList.toggle("hidden");
      document.getElementById(collapseID).classList.toggle("block");
    }
    /* Function for dropdowns */
    function openDropdown(event, dropdownID) {
      let element = event.target;
      while (element.nodeName !== "A") {
        element = element.parentNode;
      }
      Popper.createPopper(element, document.getElementById(dropdownID), {
        placement: "bottom-start",
      });
      document.getElementById(dropdownID).classList.toggle("hidden");
      document.getElementById(dropdownID).classList.toggle("block");
    }
  </script>

</body>


</html>