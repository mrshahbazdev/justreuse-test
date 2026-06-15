

<?php

$seg2 = request()->segment(2);
$post_methods = App\Models\TblPostMethod::get_active_post_methods();
$settings = App\Models\Setting::get_logos();
$check_post_methods = "";
if (!empty($post_methods)) {
  $check_post_methods = $post_methods->pluck('name')->toArray();
}

$open_dashboard = ($seg2 == "dashboard") ? 'true' : 'false';

$open_posts = ($seg2 == "posts") ? 'true' : 'false';
$open_category = ($seg2 == "category") ? 'true' : 'false';
$open_report_ad = ($seg2 == "report-ad") ? 'true' : 'false';
$open_blocked_post = ($seg2 == "blocked-posts") ? 'true' : 'false';

$open_user = ($seg2 == "user") ? 'true' : 'false';
$open_role = ($seg2 == "role") ? 'true' : 'false';
$open_permission = ($seg2 == "permissions") ? 'true' : 'false';

$open_settings = ($seg2 == "settings") ? 'true' : 'false';
$open_packages = ($seg2 == "packages") ? 'true' : 'false';
$open_report_type = ($seg2 == "report-type") ? 'true' : 'false';
$open_payment_methods = ($seg2 == "payment-methods") ? 'true' : 'false';
$open_payments = ($seg2 == "payments") ? 'true' : 'false';

$open_reviews = ($seg2 == "reviews") ? 'true' : 'false';
$open_staticpage = ($seg2 == "staticpage") ? 'true' : 'false';
$open_coupon = ($seg2 == "coupon") ? 'true' : 'false';
$open_language = ($seg2 == "languages") ? 'true' : 'false';
$open_country = ($seg2 == "country") ? 'true' : 'false';

$open_advertising = ($seg2 == "advertising") ? 'true' : 'false';
$open_blacklist = ($seg2 == "blacklist") ? 'true' : 'false';
$open_contact_us = ($seg2 == "contact-us") ? 'true' : 'false';
$open_report_user = ($seg2 == "report-user") ? 'true' : 'false';
$open_home_banner = ($seg2 == "home-banner") ? 'true' : 'false';
$open_banner_ads = ($seg2 == "banner-advertisements") ? 'true' : 'false';
$open_currency_mgnt = ($seg2 == "currency") ? 'true' : 'false';
$open_email_template = ($seg2 == "email-template") ? 'true' : 'false';
$open_buynow_orders = ($seg2 == "buynow-orders") ? 'true' : 'false';
$open_bluk_payments = ($seg2 == "bulk-payments") ? 'true' : 'false';
$open_post_methods = ($seg2 == 'post-methods') ? 'true' : 'false';
$open_chat_methods = ($seg2 == 'chat-methods') ? 'true' : 'false';
$open_features = ($seg2 == 'features') ? 'true' : 'false';

$open_features_map = ($seg2 == 'features-map-show' || $seg2 == 'features-map-edit' || $seg2 == 'features-map') ? 'true' : 'false';

$open_otherpage = ($seg2 == "otherpage") ? 'true' : 'false';

$open_bulk_email = ($seg2 == "bulk-email") ? 'true' : 'false';

?>
<?php 
$userImg = auth()->user()->profile_photo_path;

if (!empty($userImg)) {
	$userImgUrl = URL::asset('storage/' . $userImg);
} else {
	$userImgUrl = URL::asset('storage/profile-avatar.jpg');
}
?>
<nav class="bg-side-nav md:left-0 md:block md:fixed md:top-0 md:bottom-0 md:overflow-y-auto md:flex-row md:flex-no-wrap md:overflow-hidden shadow-xl bg-white flex flex-wrap items-center justify-between relative md:w-64 z-10 py-4  px-4 lg:pt-0 lg:px-0">
  <div class="md:flex-col md:items-stretch md:min-h-full md:flex-no-wrap px-0 flex items-center justify-between w-full mx-auto">
    <button class="cursor-pointer text-white opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent  border border-solid border-transparent" type="button" onclick="toggleNavbar('example-collapse-sidebar')">
      <i class="fas fa-bars"></i>
    </button>
    <a class="lg:pt-4 md:block text-center text-gray-700 mr-0 inline-block whitespace-no-wrap text-sm uppercase font-bold" href="<?php echo e(URL::to('/admin/dashboard')); ?>">
      <?php
      // echo !empty($settings) ? $settings['name'] : "Admin Panel";
      ?>
      <img class="max-w-full" src="<?php echo e(URL::to('storage/'.$settings['admin_logo'])); ?>">
    </a>

    <a class="lg:text-gray-800 lg:hover:text-yellow-300 text-gray-800 pl-3 py-4 lg:py-2 flex items-center text-sm xl:hidden" href="#pablo" onclick="openDropdown(event,'demo-pages-dropdown1')">
          <img src="<?php echo e($userImgUrl); ?>" class="bg-white rounded-full mr-1" width="30" height="30" alt="your profile"/></a>
          <div class="bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg hidden" id="demo-pages-dropdown1" data-popper-placement="bottom-start" style="width:150px;position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 38px);">
          <a href="<?php echo e(URL::to('admin/my-profile')); ?>" class="text-sm py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800">My Profile</a>
          <form method="POST" action="<?php echo e(route('logout')); ?>">
          <?php echo csrf_field(); ?>
          <a href="<?php echo e(route('logout')); ?>" class="lg:text-gray-800 lg:hover:text-yellow-300 text-gray-800 px-3 py-4 lg:py-2 flex items-center text-sm" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fa fa-sign-out mr-1" fa-lg=""></i> Logout</a>
          </form>    
        </div>

    <div class="md:flex md:flex-col md:items-stretch md:opacity-100 md:relative md:mt-4 md:shadow-none shadow absolute top-0 left-0 right-0 z-40 overflow-y-auto overflow-x-hidden h-auto items-center flex-1 hidden" id="example-collapse-sidebar">
      <div class="md:min-w-full md:hidden block pb-0 mb-4 border-b border-solid border-gray-300 fixed inset-x-0" style="background-color:#313a46">
        <div class="w-full text-center flex justify-center align-center">
          <div class="w-full flex justify-center align-center">
            <a class="md:block text-center md:pb-2 text-gray-700 mr-0 inline-block whitespace-no-wrap text-sm uppercase font-bold p-4 px-0" href="<?php echo e(URL::to('/admin/dashboard')); ?>">
              <?php
              // echo !empty($settings) ? $settings['name'] : "Admin Panel";
              ?>
              <img class="max-w-full" src="<?php echo e(URL::to('storage/'.$settings['admin_logo'])); ?>">
            </a>
          </div>
          <div class="w-auto flex justify-end">
            <button type="button" class="cursor-pointer text-black opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent" onclick="toggleNavbar('example-collapse-sidebar')">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
      </div>
      <!-- Divider -->
      
      <!-- Heading -->

      <!-- Navigation -->

      <ul class="md:flex-col md:min-w-full flex flex-col list-none mt-32 xl:mt-0">

        <div class="font-medium">
          <?php if(auth()->user()->can('dashboard-list')): ?>
          <a class="w-full flex items-center py-3 px-2 <?php echo ($open_dashboard == "true") ? "bg-green-500  text-white " : "text-gray-400"; ?> cursor-pointer  transition hover:text-white focus:outline-none" href="<?php echo e(route('dashboard')); ?>"><i class="fas fa-home mr-3 hover:text-white text-sm <?php echo ($open_dashboard == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>"></i>&nbsp;Dashboard</a>
          <?php endif; ?>
        </div>


        <!-- ads -->

        <?php

        $selected_ads = ($open_posts == 'true' || $open_category == 'true' || $open_report_ad == 'true' || $open_blocked_post == 'true' ||$open_features == "true" || $open_features_map == 'true' ) ? 'true' : 'false';

        ?>

        <div x-data="{ open:<?php echo e($selected_ads); ?> }" class="border-b border-gray-600">
          <button @click="open = !open" class="w-full flex justify-between items-center py-3 px-3 text-gray-400 cursor-pointer h hover:text-white transition focus:outline-none">
            <span class="flex items-center transition hover:text-white">


              <span class="font-medium"><i class="fas fa-ad text-sm mr-3 "></i>&nbsp;Ads</span>
            </span>

            <span>
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path x-show="! open" d="M9 5L16 12L9 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"></path>
                <path x-show="open" d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
              </svg>
            </span>
          </button>


          <div x-show="open" class="bg-gray-500 pb-2 transition" >
            <?php if(auth()->user()->can('postlist-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_posts == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white " href="<?php echo e(route('admin/post')); ?>">Post lists</a>
            <?php endif; ?>
            <?php if(auth()->user()->can('category-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_category == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white" href="<?php echo e(route('admin/category')); ?>">Category</a>
            <?php endif; ?>
            <?php if(auth()->user()->can('category-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm  <?php echo ($open_features == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white" href="<?php echo e(route('features')); ?>">Features</a>
            <?php endif; ?>
            <?php if(auth()->user()->can('category-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm  <?php echo ($open_features_map == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white" href="<?php echo e(route('features-map-show')); ?>">Features Mapping</a>
            <?php endif; ?>
            <?php if(auth()->user()->can('reportad-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_report_ad == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white" href="<?php echo e(route('admin/report-ad')); ?>">Report Ads</a>
            <?php endif; ?>
            <?php if(auth()->user()->can('blocked-post-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_blocked_post == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white" href="<?php echo e(route('admin/blocked-post')); ?>">Blocked Posts</a>
            <?php endif; ?>
          </div>
        </div>


        <!-- users -->

        <?php

        $selected_users = ($open_user == 'true' || $open_role == 'true' || $open_permission == 'true') ? 'true' : 'false';

        ?>

        <div x-data="{ open: <?php echo e($selected_users); ?> }" class="border-b border-gray-600">
          <button @click="open = !open" class="w-full flex justify-between items-center py-3 px-3 text-gray-400 cursor-pointer  hover:text-white focus:outline-none">
            <span class="flex items-center">


              <span class="font-medium"><i class="fas fa-users text-sm mr-3"></i>&nbsp;Users</span>
            </span>

            <span>
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path x-show="! open" d="M9 5L16 12L9 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"></path>
                <path x-show="open" d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
              </svg>
            </span>
          </button>

          <div x-show="open" class="bg-gray-500 pb-2">
            <?php if(auth()->user()->can('user-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_user == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white" href="<?php echo e(route('admin/user')); ?>">Users</a>
            <?php endif; ?>
            <?php if(auth()->user()->can('role-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_role == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white" href="<?php echo e(route('admin/role')); ?>">Roles</a>
            <?php endif; ?>
            <?php if(auth()->user()->can('permission-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_permission == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white" href="<?php echo e(route('admin/permissions')); ?>">Permissions</a>
            <?php endif; ?>
            <?php if(auth()->user()->can('verificationrequest')): ?>
          <a class="py-3 px-6 pl-12 block transition text-sm hidden <?php echo ($open_bulk_email == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white" href="<?php echo e(route('admin/verification-request')); ?>">Verification Request</a>
          <?php endif; ?>
          </div>
        </div>

        <!-- setup -->


        <?php
        $selected_setup = ($open_coupon == 'true' || $open_post_methods == 'true' || $open_email_template == 'true' || $open_language == 'true' || $open_currency_mgnt == 'true' || $open_country == 'true' || $open_advertising == 'true' || $open_blacklist == 'true' || $open_settings == 'true' || $open_packages == 'true' || $open_report_type == 'true' || $open_home_banner == 'true' || $open_payment_methods == 'true' || $open_chat_methods=='true') ? 'true' : 'false';
        ?>
        <div x-data="{ open: <?php echo e($selected_setup); ?> }" class="border-b border-gray-600">
          <button @click="open = !open" class="w-full flex justify-between items-center py-3 px-3 text-gray-400 cursor-pointer 0 hover:text-white focus:outline-none">
            <span class="flex items-center">
              <span class="font-medium"><i class="fas fa-cog mr-3 text-gray-400"></i>&nbsp;Setup</span>
            </span>
            <span>
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path x-show="! open" d="M9 5L16 12L9 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"></path>
                <path x-show="open" d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
              </svg>
            </span>
          </button>


          <div x-show="open" class="bg-gray-500">
            <?php if(auth()->user()->can('settings-list')): ?>
            <a class="py-3 px-6 pl-12 block transition  text-sm  hover:text-white <?php echo ($open_settings == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white " href="<?php echo e(route('admin/settings')); ?>">Settings</a>
            <?php endif; ?>

            <?php if(auth()->user()->can('post-method-list')): ?>
            <a class="py-3 px-6 pl-12 block transition  text-sm  hover:text-white <?php echo ($open_post_methods == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white" href="<?php echo e(route('admin/post-methods')); ?>">Post Methods</a>
            <?php endif; ?>

            <?php if(auth()->user()->can('currency-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_currency_mgnt == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>  hover:text-white" href="<?php echo e(route('admin/currency')); ?>">Currency</a>
            <?php endif; ?>

            <?php if(auth()->user()->can('package-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_packages == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white" href="<?php echo e(route('admin/package')); ?>">Package</a>
            <?php endif; ?>
            <?php if(auth()->user()->can('reporttype-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_report_type == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>  hover:text-white" href="<?php echo e(route('admin/report-type')); ?>">Report Types</a>
            <?php endif; ?>
            <?php if(auth()->user()->can('payment-methods-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_payment_methods == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>  hover:text-white" href="<?php echo e(route('admin/payment-methods')); ?>">Payment Methods</a>
            <?php endif; ?>
            <?php if(auth()->user()->can('chat-method-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_chat_methods == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>  hover:text-white" href="<?php echo e(route('admin/chat-methods')); ?>">Chat Methods</a>
            <?php endif; ?>

            <?php if(auth()->user()->can('coupon-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_coupon == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>  hover:text-white" href="<?php echo e(route('admin/coupon')); ?>">Coupons</a>
            <?php endif; ?>

            <?php if(auth()->user()->can('language-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_language == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>  hover:text-white" href="<?php echo e(route('admin/language')); ?>">Languages</a>
            <?php endif; ?>

            <?php if(auth()->user()->can('country-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm  <?php echo ($open_country == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>  hover:text-white" href="<?php echo e(route('admin/country')); ?>">Country</a>
            <?php endif; ?>

            <?php if(auth()->user()->can('advertising-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_advertising == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>  hover:text-white" href="<?php echo e(route('admin/advertising')); ?>">Advertising</a>
            <?php endif; ?>

            <?php if(auth()->user()->can('blacklist-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_blacklist == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>  hover:text-white" href="<?php echo e(route('admin/blacklist')); ?>">Blacklist</a>
            <?php endif; ?>

            <?php if(auth()->user()->can('home-banner-list')): ?>
            <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_home_banner == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>  hover:text-white" href="<?php echo e(route('admin/home-banner')); ?>">Home Banner</a>
            <?php endif; ?>

            <?php if(auth()->user()->can('email-template-list')): ?>
            <a class="py-3 px-6 pl-12 block transition  text-sm <?php echo ($open_email_template == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>  hover:text-white " href="<?php echo e(route('admin/email-template')); ?>">Email Template</a>
            <?php endif; ?>

          </div>
        </div>
        <?php
        $selected_sales = ($open_payments == 'true' || $open_bluk_payments == 'true' || $open_banner_ads == 'true' ||$open_buynow_orders=='true') ? 'true' : 'false';
        ?>
        <div x-data="{ open: <?php echo e($selected_sales); ?> }" class="border-b border-gray-600">
          <button @click="open = !open" class="w-full flex justify-between items-center py-3 px-3 text-gray-400 cursor-pointer  hover:text-white focus:outline-none">
            <span class="flex items-center">


              <span class="font-medium"><i class="fas fa-users text-sm mr-3"></i>Sales</span>
            </span>

            <span>
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path x-show="! open" d="M9 5L16 12L9 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"></path>
                <path x-show="open" d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
              </svg>
            </span>
          </button>

          <div x-show="open" class="bg-gray-500 pb-2">
           <?php if(auth()->user()->can('payment-list')): ?>
          <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_payments == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white" href="<?php echo e(route('admin/payment')); ?>">Service Orders</a>
          <?php endif; ?>
          <!-- <?php if(auth()->user()->can('bulk-payment-list')): ?>
          <a class="py-3 px-6 pl-12 block transition text-sm <//?php echo ($open_bluk_payments == "true") ? "bg-gray-500 text-white" : "text-gray-400 "; ?> hover:text-white" href="<?php echo e(route('admin/bulk-payments')); ?>">Bulk Orders</a>
          <?php endif; ?> -->


            <?php if(auth()->user()->can('buynow-orders-list')): ?>
          <?php if(!empty($check_post_methods)): ?>
          <?php if(in_array("buynow", $check_post_methods)): ?>
          <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_buynow_orders == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white" href="<?php echo e(route('admin/buynow-orders')); ?>">Buynow Orders</a>
          <?php endif; ?>
          <?php endif; ?>
          <?php endif; ?>

          <?php if(auth()->user()->can('banner-ads-list')): ?>
          <?php if(!empty($check_post_methods)): ?>
          <?php if(in_array("bannerads", $check_post_methods)): ?>
          <a class="py-3 px-6 pl-12 block transition text-sm <?php echo ($open_banner_ads == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> hover:text-white" href="<?php echo e(route('admin/banner-advertisements')); ?>">Banner Ads</a>
          <?php endif; ?>
          <?php endif; ?>
          <?php endif; ?>


        
          </div>
        </div>

        <div class="font-medium">
          <!-- <?php if(auth()->user()->can('payment-list')): ?>
          <a class="border-b border-gray-600 w-full flex items-center py-3 px-3 <//?php echo ($open_payments == "true") ? "bg-gray-500 text-white" : "text-gray-400"; ?> cursor-pointer  hover:text-white focus:outline-none" href="<?php echo e(route('admin/payment')); ?>"><i class="fas fa-dollar-sign mr-3 <//?php echo ($open_payments == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>"></i>&nbsp;Orders</a>
          <?php endif; ?>
          <?php if(auth()->user()->can('bulk-payment-list')): ?>
          <a class="border-b border-gray-600 w-full flex items-center py-3 px-3 <//?php echo ($open_bluk_payments == "true") ? "bg-gray-500 text-white" : "text-gray-400 "; ?> cursor-pointer  hover:text-white focus:outline-none" href="<?php echo e(route('admin/bulk-payments')); ?>"><i class="fas fa-dollar-sign mr-3 <//?php echo ($open_bluk_payments == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>"></i>&nbsp;Bulk Orders</a>
          <?php endif; ?>

          <?php if(auth()->user()->can('banner-ads-list')): ?>
          <?php if(!empty($check_post_methods)): ?>
          <?php if(in_array("bannerads", $check_post_methods)): ?>
          <a class="border-b border-gray-600 w-full flex items-center py-3 px-3 <//?php echo ($open_banner_ads == "true") ? "bg-gray-500 text-white" : "text-gray-400 "; ?>cursor-pointer  hover:text-white focus:outline-none" href="<?php echo e(route('admin/banner-advertisements')); ?>"><i class="fas fa-dollar-sign mr-3 <//?php echo ($open_banner_ads == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>"></i>&nbsp;Banner Ads</a>
          <?php endif; ?>
          <?php endif; ?>
          <?php endif; ?>
          <?php if(auth()->user()->can('buynow-orders-list')): ?>
          <?php if(!empty($check_post_methods)): ?>
          <?php if(in_array("buynow", $check_post_methods)): ?>
          <a class="border-b border-gray-600 w-full flex items-center py-3 px-3 <//?php echo ($open_buynow_orders == "true") ? "bg-gray-500 text-white" : "text-gray-400 "; ?> cursor-pointer  hover:text-white focus:outline-none" href="<?php echo e(route('admin/buynow-orders')); ?>"><i class="fas fa-dollar-sign mr-3 <//?php echo ($open_buynow_orders == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>"></i>&nbsp;Buynow Orders</a>
          <?php endif; ?>
          <?php endif; ?>
          <?php endif; ?> -->


          <?php if(auth()->user()->can('review-list')): ?>
          <a class="border-b border-gray-600 w-full flex items-center py-3 px-3 <?php echo ($open_reviews == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> cursor-pointer  hover:text-white focus:outline-none" href="<?php echo e(route('admin/review')); ?>"><i class="fas fa-newspaper mr-3 <?php echo ($open_reviews == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>"></i>&nbsp;Reviews</a>
          <?php endif; ?>

          <?php if(auth()->user()->can('pages-list')): ?>
          <a class="border-b border-gray-600 w-full flex items-center py-3 px-3 <?php echo ($open_staticpage == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> cursor-pointer hover:text-white focus:outline-none" href="<?php echo e(route('admin/staticpage')); ?>"><i class="fa fa-file mr-3 <?php echo ($open_staticpage == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>"></i>&nbsp;Pages</a>
          <?php endif; ?>
          <?php if(auth()->user()->can('otherpages-list')): ?>
          <a class="border-b border-gray-600 w-full flex items-center py-3 px-3 <?php echo ($open_otherpage == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> cursor-pointer  hover:text-white focus:outline-none" href="<?php echo e(route('admin/otherpages')); ?>"><i class="fa fa-file mr-3 <?php echo ($open_otherpage == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>"></i>&nbsp;Other Pages</a>
          <?php endif; ?>
          <?php if(auth()->user()->can('contact-us-list')): ?>
          <a class="border-b border-gray-600 w-full flex items-center py-3 px-3 <?php echo ($open_contact_us == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> cursor-pointer  hover:text-white focus:outline-none" href="<?php echo e(route('admin-contact-us')); ?>"><i class="fa fa-file mr-3 <?php echo ($open_contact_us == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>"></i>&nbsp;Contact-us</a>
          <?php endif; ?>
          <?php if(auth()->user()->can('reportuser-list')): ?>
          <a class="border-b border-gray-600 w-full flex items-center py-3 px-3 <?php echo ($open_report_user == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?> cursor-pointer hover:text-white focus:outline-none" href="<?php echo e(route('admin/report-user')); ?>"><i class="fas fa-users mr-3 <?php echo ($open_report_user == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>"></i>&nbsp;Report User</a>
          <?php endif; ?>
          <?php if(auth()->user()->can('bulk-email-list')): ?>
          <a class="border-b border-gray-600 w-full flex items-center py-3 px-3 <?php echo ($open_bulk_email == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>  cursor-pointer  hover:text-white focus:outline-none" href="<?php echo e(route('admin/bulk-email')); ?>"><i class="fas fa-envelope mr-3 <?php echo ($open_bulk_email == "true") ? "bg-green-500 text-white cursor-pointer  transition hover:text-white focus:outline-none" : "text-gray-400"; ?>"></i>&nbsp;Bulk Email</a>
          <?php endif; ?>
        </div>

        



    </div>
  </div>
</nav><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/common/sidenav.blade.php ENDPATH**/ ?>