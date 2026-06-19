<!DOCTYPE html>
<html>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @yield('whatsapp_meta')
    <?php
    $settings = App\Models\Setting::get_logos();
    ?>
    <!--  favicon -->
    <link rel="shortcut icon" href="{{ URL::to('/storage/'.$settings['fav_icon']) }}">
    @include('partials.favicon')
    <link rel="stylesheet" href="{{ URL::to('css/tailwind.css') }}">
    <link rel="stylesheet" href="{{ URL::to('css/slick-theme.css') }}">
    <link rel="stylesheet" href="{{ URL::to('css/tailwind_all.min.css') }}">
    <link rel="stylesheet" href="{{ URL::to('public/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link href="{{ URL::to('css/tailwind.min.css') }}" rel="stylesheet">
    <script src="{{ URL::to('js/stimulus.umd.js') }}"></script>
    <link href="{{ URL::to('css/jquery-ui.css') }}" rel="stylesheet">
    <link href="https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet">
    <script src="{{ URL::to('js/tailwindcss-stimulus-components.umd.js') }}"></script>
    <script src="{{ URL::to('js/jquery-1.10.2.js') }}"></script>
    <script src="{{ URL::to('js/jquery.min.js') }}"></script>

    <script src="{{ URL::to('js/jquery-ui.js') }}"></script>
    <script src="{{ URL::to('js/slick.min.js') }}"></script>
    <script src="{{ URL::to('js/app.js') }}" defer></script>
    <link href="{{ URL::to('css/toastr.css') }}" rel="stylesheet">
    <script src="{{ URL::to('js/toastr.min.js') }}"></script>
    @include('livewire.common.location_default')
    <title>Classified</title>
    @livewireScripts
</head>

<body class="antialiased overflow-x-hidden">
    <?php
    /* default language set from admin start */
    $select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
    $locale = config('app.locale');
    if (!Session::has('locale')) {
        $get_default = App\Models\TblLanguage::where('active', '1')->where('default', '1')->first()->toArray();
        $locale = $get_default['locale'];
        App::setLocale($locale);
    }
    /* default language set from admin end */
    /* Top position tracking code start */
    $advertising = App\Models\TblAdvertising::where('position', 'top')->where('active', '1')->first();
    if (!empty($advertising)) {
        echo $advertising->tracking_code;
    }
    /* Top position tracking code end */
    ?>
    <div class="bg-indigo-500 top-0 z-50 w-full flex flex-wrap items-center justify-between px-2 py-3 navbar-expand-lg">
        <div class="fixed z-10 inset-0 overflow-y-auto inset-0 bg-gray-500 opacity-75" style="display:none" id="overlay">
            <div class="text-white text-center mt-auto mb-auto">
                <p>Loading...</p>
            </div>
        </div>
        <div class="container px-4 mx-auto flex flex-wrap items-center justify-between">
            <div class="w-full relative flex justify-between lg:w-auto lg:static lg:block lg:justify-start">
                <a class="text-xl leading-relaxed inline-block whitespace-no-wrap text-white" href="{{ URL::to('/') }}">
                    <img width="50px" height="50px" src="{{URL::to('public/storage/'.$settings['logo'])}}">
                </a>
                <button class="cursor-pointer text-xl leading-none px-3 py-1 border border-solid border-transparent rounded bg-transparent block lg:hidden outline-none  focus:outline-none" type="button" onclick="toggleNavbar('example-collapse-navbar')">
                    <i class="text-white fas fa-bars"></i>
                </button>
            </div>
            <div class="md:w-11/12 lg:flex flex-grow items-center bg-white lg:bg-transparent lg:shadow-none hidden absolute mt-20 lg:mt-0 inset-x-0 z-50 mx-auto top-0 lg:relative lg:rounded-none rounded-lg border-gray border-2 lg:border-0" id="example-collapse-navbar">
                @include('layouts.search-bar')
                <ul class="md:w-full lg:w-auto md:justify-center justify-center flex flex-col-3 md:flex-col-3 lg:flex-col-1 lg:flex-row list-none lg:ml-auto">
                    @if(auth()->user())
                    <?php
                    /* Notifiations count and list */
                    $notifications = App\Models\TblNotifications::where('to_id', auth()->user()->id)->where('read_status', 0)->get();
                    $userImg = auth()->user()->profile_photo_path;
                    if (!empty($userImg)) {
                        $userImgUrl = URL::asset('storage/' . $userImg);
                    } else {
                        $userImgUrl = URL::asset('storage/profile-avatar.jpg');
                    }
                    ?>
                    <li class="flex items-center">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" id="logout_trig" class="lg:text-white lg:hover:text-yellow-300 text-gray-800 px-3 py-4 lg:py-2 flex items-center text-sm" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fa fa-sign-out mr-1" fa-lg></i> {{__('messages.logout')}}</a>
                        </form>
                    </li>
                    <li class="inline-block relative"><a class="lg:text-white lg:hover:text-yellow-300 text-gray-800 px-3 py-4 lg:py-2 flex items-center text-sm" href="<?php echo url::to('notifications'); ?>" onclick="openDropdown(event, 'demo-notifications-dropdown')"><i class="fa fa-bell mt-4" aria-hidden="true"></i><span class="absolute top-0 text-xs bg-red-600 px-2 py-1 rounded-full font-black" style="left: 18px;top: 8px;padding: 2px 6px;"><?php echo count($notifications); ?></span></a>

                    </li>
                    <?php
                    $post_methods = App\Models\TblPostMethod::get_active_post_methods();
                    ?>
                    <li class="inline-block relative"><a class="lg:text-white lg:hover:text-yellow-300 text-gray-800 px-3 py-4 lg:py-2 flex items-center text-sm" href="#pablo" onclick="openDropdown(event, 'demo-pages-dropdown')"><img src="{{$userImgUrl}}" class="bg-white rounded-full mr-1 h-8 w-8" width="30" height="30" alt="your profile" />{{ auth()->user()->name }} <i class="fa fa-angle-down ml-2" fa-lg></i></a>
                        <div class="hidden bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48" id="demo-pages-dropdown">
                            <a href="{{ URL::to('post') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800">{{__('messages.my ads')}}</a>
                            @foreach($post_methods as $post_method)
                            @if($post_method->name == "exchange")
                            <a href="{{ URL::to('/my-exchange/incoming') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800">{{__('messages.my exchanges')}}</a>
                            @endif
                            @endforeach
                            <a href="{{ URL::to('/my-banner-ads') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800">{{__('messages.my banner ads')}}</a>
                            <a href="{{ URL::to('/my-buynow/orders') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800">{{__('messages.my orders & sales')}}</a>
                            <a href="{{ URL::to('favourite') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800">{{__('messages.favourite ads')}}</a>
                            <a href="{{ URL::to('/my-profile') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800">{{__('messages.my profile')}}</a>
                            <a href="{{ URL::to('/my-followers') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800">{{__('messages.my followers & followings')}}</a>
                            <a href="{{ URL::to('/selectPackageMultiple') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800">{{__('messages.buy business packs')}}</a>
                            <a href="{{ URL::to('/mypackage') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800">{{__('messages.my business packs')}}</a>
                            <a href="{{ URL::to('/chat') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800">{{__('messages.My Chat')}}</a>
                        </div>
                    </li>
                    <!-- language -->
                    <li class="inline-block relative "><a class="text-sm z-50 sm:text-white sm:hover:text-yellow-300 text-gray-800 px-3 py-4 sm:py-3 flex items-center" href="#pablo" onclick="openDropdown(event, 'demo-language-dropdown')">{{ strtoupper($locale) }}<i class="fa fa-angle-down ml-2" fa-lg></i></a>
                        <div class="hidden bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48" id="demo-language-dropdown">
                            @foreach($select_language as $row)
                            <?php $lang_url = URL::to('locale') . '/' . $row->locale ?>
                            <a href="{{ $lang_url }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800"><i class="fa fa-language"></i> {{$row->native}}</a>
                            @endforeach
                        </div>
                    </li>
                    <!-- end language -->
                    <li class="flex items-center">
                        <a class="inline-block text-black bg-yellow-500 text-xs font-bold uppercase px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none lg:mr-1 lg:mb-0 ml-3 mb-3 ease-linear transition-all duration-150" href="{{route('post-add')}}"><i class="fa fa-plus-circle"></i> {{__('messages.post add')}}</a>
                    </li>
                    @else
                    <li class="flex items-center">
                        <a class="lg:text-white lg:hover:text-yellow-300 text-gray-800 px-3 py-4 lg:py-2 flex items-center text-sm" href="{{route('login')}}"><i class="fa fa-user mr-1" fa-lg></i> {{__('messages.login')}}</a>
                    </li>
                    <li class="flex items-center">
                        <a class="lg:text-white lg:hover:text-yellow-300 text-gray-800 px-3 py-4 lg:py-2 flex items-center text-sm" href="{{route('register')}}"><i class="fa fa-user-plus mr-1" fa-lg></i>{{__('messages.register')}}</a>
                    </li>
                    <!-- language -->
                    <?php
                    $select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
                    ?>
                    <li class="inline-block relative"><a class="lg:text-white lg:hover:text-yellow-300 text-gray-800 px-3 py-4 lg:py-2 flex items-center text-sm" href="#pablo" onclick="openDropdown(event, 'demo-language-dropdown')">{{ strtoupper(config('app.locale')) }}<i class="fa fa-angle-down ml-2" fa-lg></i></a>
                        <div class="hidden bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48" id="demo-language-dropdown">
                            @foreach($select_language as $row)
                            <?php $lang_url = URL::to('locale') . '/' . $row->locale ?>
                            <a href="{{ $lang_url }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800"><i class="fa fa-language"></i> {{$row->native}}</a>
                            @endforeach
                        </div>
                    </li>
                    <!-- end language -->
                    <li class="flex items-center">
                        <a class="inline-block text-black bg-yellow-500 text-xs font-bold uppercase px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none lg:mr-1 lg:mb-0 ml-3 mb-3 ease-linear transition-all duration-150" href="{{route('login')}}"><i class="fa fa-plus-circle"></i> {{__('messages.post add')}}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="relative top-0 z-40 w-full flex flex-wrap items-center justify-between shadow-md">
        <!--Top menu -->
        <?php
        $main_categories = App\Models\TblCategory::withDepth()->having('depth', '=', 0)->whereNull('deleted_at')->orderBy('list_order', 'asc')->get();
        ?>
        <header x-data="{ mobileMenuOpen : false }" class="flex px-4 container mx-auto flex-wrap flex-row justify-between lg:items-center lg:space-x-4 bg-white py-3 relative">

            <div class="block">
                <button class="focus:outline-none leading-none	font-bold text-base" onclick="showAllcategory()">{{__('messages.all category')}}
                    <span class="text-2xl" style="position: relative;top: 4px;"> <i class="fa fa-angle-down" aria-hidden="true"></i> </span>
                </button>
            </div>
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-block lg:hidden w-8 h-8 bg-gray-200 text-gray-600 p-1">
                <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                </svg>
            </button>
            <nav class="absolute mb-0 md:mt-16 sm:mt-16 mt-16 lg:mt-0 lg:relative md:absolute top-0 left-0 lg:top-0 z-20 lg:flex flex-col lg:flex-row lg:space-x-5 font-semibold w-full lg:w-auto bg-white shadow-md rounded-lg md:shadow-none md:bg-white p-0 pt-0 border-gray border-2 lg:border-0" :class="{ 'flex' : mobileMenuOpen , 'hidden' : !mobileMenuOpen}" @click.away="mobileMenuOpen = false">
                @foreach($main_categories as $main_category)
                <a class="inline text-sm mx-3 sm:mx-2 md:my-1 my-1 lg:my-1 sm:my-1 md:mx-2 lg:mx-2 font-medium" href="<?php echo Session::get('Searchedurl') . "&c=$main_category->slug"; ?>">{{ $main_category->title }}</a>
                @endforeach
            </nav>
            <div id="Allcategorylist" class="shadow-md bg-gray-200 left-0 top-0 absolute z-10 w-full mt-0" style="display:none;top:100%;">
                <div class="flex flex-wrap mt-2 mb-2">
                    @foreach($main_categories as $main_category)
                    <?php
                    $sub_categories = App\Models\TblCategory::orderBy('list_order', 'asc')->descendantsAndSelf($main_category->id);
                    ?>
                    <div class="w-full md:w-6/12 lg:w-3/12 lg:mb-0 mb-12 px-4 py-2">
                        <a class="font-bold text-base hover:text-indigo-700 text-opacity-100" href="<?php echo Session::get('Searchedurl') . "&c=$main_category->slug"; ?>">
                            {{ $main_category->title}}
                        </a>
                        @foreach($sub_categories as $sub_category)
                        <?php
                        if ($sub_category->parent_id != "") {
                            $child = App\Models\TblCategory::where('id', $sub_category->parent_id)->pluck('parent_id')->first();
                            if (empty($child)) {
                        ?>
                                <div class="text-sm">
                                    <a class="hover:text-indigo-700 text-opacity-100" href="<?php echo Session::get('Searchedurl') . "&c=$sub_category->slug"; ?>">{{$sub_category->title}}
                                    </a>
                                </div>
                        <?php
                            }
                        }
                        ?>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
        </header>
        <!-- Top menu -->
    </div>
    <main>
        <!--content start-->
        @yield('content')
        <!--content end-->
    </main>
    <?php
    $pop_locations = App\Models\TblPost::get_populor_loc();
    $trending_locations = App\Models\TblPost::get_trending_loc();
    ?>
    <footer class="relative bg-gray-300 pt-8 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                <div class="md:w-full md:text-left">
                    <h4 class="font-bold text-base text-opacity-100 mb-3">{{__('messages.popular locations')}}</h4>
                    @if(!empty($pop_locations))
                    <ul>
                        @foreach($pop_locations as $pop_location)
                        <?php
                        $pop_state_url = URL::to('/' . str_replace(' ', '_', strtolower($pop_location)) . "?loc=" . $pop_location . "&country=&state=" . $pop_location . "&city=");
                        ?>
                        <li>
                            <a class="hover:text-indigo-700 text-opacity-100 text-sm" href="<?php echo $pop_state_url; ?>">
                                <?php echo $pop_location; ?>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                <div class="md:w-full md:text-left">
                    <h4 class="font-bold text-base text-opacity-100 mb-3">{{__('messages.trending locations')}}</h4>
                    @if(!empty($trending_locations))
                    <ul>
                        @foreach($trending_locations as $trending_location)
                        <?php
                        $pop_state_url = URL::to('/' . str_replace(' ', '_', strtolower($trending_location)) . "?loc=" . $trending_location . "&country=&state=" . $trending_location . "&city=");
                        ?>
                        <li>
                            <a class="hover:text-indigo-700 text-opacity-100 text-sm" href="<?php echo $pop_state_url; ?>">
                                <?php echo $trending_location; ?>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                <div class="md:w-full md:text-left">
                    <h4 class="font-bold text-base text-opacity-100 mb-3">{{__('messages.about us')}}</h4>
                    <ul>
                        <li><a class="hover:text-indigo-700 text-opacity-100 text-sm" href="{{ URL::to('/contact-us') }}">{{__('messages.contact us')}}</a></li>
                        <li><a class="hover:text-indigo-700 text-opacity-100 text-sm" href="{{ URL::to('/pages/help') }}">{{__('messages.help')}}</a>
                        </li>
                    </ul>
                </div>
                <div class="md:w-full md:text-left">
                    <h4 class="font-bold text-base text-opacity-100 mb-3">{{__('messages.classified')}}</h4>
                    <ul>
                        <li><a class="hover:text-indigo-700 text-opacity-100 text-sm" href="{{ URL::to('/packages') }}">{{__('messages.packages')}}</a></li>
                        <li><a class="hover:text-indigo-700 text-opacity-100 text-sm" href="{{ URL::to('/pages/terms-conditions') }}">{{__('messages.terms & conditions')}}</a></li>
                        <li><a class="hover:text-indigo-700 text-opacity-100 text-sm" href="{{ URL::to('/pages/privacy-policy') }}">{{__('messages.privacy policy')}}</a>
                        </li>
                    </ul>
                </div>
                <div class="md:w-full md:text-center">
                    <h4 class="font-bold text-base text-opacity-100 mb-3">{{__('messages.follow us')}}</h4>
                    <ul class="inline-block">
                        <li class="inline">
                            <a class="hover:text-indigo-700 text-opacity-100 mr-3" href="#">
                                <i class="fa fa-facebook" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li class="inline">
                            <a class="hover:text-indigo-700 text-opacity-100 mr-3" href="#">
                                <i class="fa fa-twitter" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li class="inline">
                            <a class="hover:text-indigo-700 text-opacity-100 mr-3" href="#">
                                <i class="fa fa-instagram" aria-hidden="true"></i>
                            </a>
                        </li>
                    </ul>
                    <br>
                    <button class="mt-2 text-white px-2 bg-red-200 border focus:outline-none rounded py-1 bg-indigo-500 border border-gray-600 rounded-sm"><a href="{{ URL::to('/pages/banner-ads') }}">{{__('messages.banner advertise')}}</a></button>
                </div>
            </div>
        </div>
    </footer>
    <!-- Bottom script -->
    <?php
    /* Bottom position tracking code start */
    $advertising = App\Models\TblAdvertising::where('position', 'bottom')->where('active', '1')->first();
    if (!empty($advertising)) {
        echo $advertising->tracking_code;
    }
    /* Bottom position tracking code end */
    ?>
    <div class="bg-indigo-500 p-1">
        <div class="flex flex-wrap items-center md:justify-between justify-center">
            <div class="w-full md:w-4/12 px-4 mx-auto text-center">
                <div class="text-sm text-white font-semibold py-1">
                    {{__('messages.copywrite')}} © <span id="get-current-year"></span>
                </div>
            </div>
        </div>
    </div>
    @stack('modals')
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
    <!--for tab design, dropdown, tabs, popover-->
    <script>
        const application = Stimulus.Application.start();
        application.register('dropdown', TailwindcssStimulusComponents.Dropdown);
        application.register('modal', TailwindcssStimulusComponents.Modal);
        application.register('tabs', TailwindcssStimulusComponents.Tabs);
        application.register('popover', TailwindcssStimulusComponents.Popover);
        application.register('alert', TailwindcssStimulusComponents.Alert);
        application.register('slideover', TailwindcssStimulusComponents.Slideover);
    </script>
    <!-- Favourate product process begin -->
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".save_favourate").click(function(e) {
            var val = $(this).attr("data-fav-post-id");
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "{{ route('savepost') }}",
                data: {
                    post_id: val
                },
                success: function(data) {
                    if (data.result == "failed" && data.flag == "0") {
                        toastr.warning(data.message);
                    } else if (data.result == "success" && data.flag == "1") {
                        //$("#favourate_post_id_"+rowid).css('background','gray');
                        $("#favourate_post_id_" + val).removeClass("bg-gray-800");
                        $("#favourate_post_id_" + val).addClass("bg-indigo-500");
                        toastr.success(data.message);
                    } else {
                        $("#favourate_post_id_" + val).removeClass("bg-indigo-500");
                        $("#favourate_post_id_" + val).addClass("bg-gray-800");
                        toastr.success(data.message);
                    }

                }
            });
        });
    </script>
    <!-- Favourate product process end -->
    <!-- Show all categories script -->
    <script>
        function showAllcategory() {
            var x = document.getElementById("Allcategorylist");
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }
        $(window).scroll(function() {
            var bodyheight = $(document).scrollTop() + 100;
            if (bodyheight >= 250) {
                $(".navbar-expand-lg").addClass("fixed");
            } else {
                $(".navbar-expand-lg").removeClass("fixed");
            }
        });
    </script>
    <!-- begin - force logout if user if user blocked -->
    <?php
    $isBlocked = "0";
    if (!empty(auth()->user())) {
        $isBlocked = auth()->user()->is_blocked;
    }
    ?>
    <script>
        (function() {
            var blocked = '{{$isBlocked}}';
            if (blocked == "1") {
                document.getElementById('logout_trig').click();
            }
        })();
    </script>
    <!-- end - force logout if user if user blocked -->
    <?php
    //from paypal success(/)failure
    $payment_nofy = Session('payment_nofy');
    Session::forget('payment_nofy');
    ?>
    <script type="text/javascript">
        var payment_nofy = "{{$payment_nofy}}";
        if (payment_nofy != "") {
            toastr.info(payment_nofy);
        }
    </script>
    <style>
        .pac-logo:after {
            display: none;
        }
    </style>
</body>

</html>