<div class="w-full float-left my-12">
    <div class="container m-auto px-4">
		<div class="w-full float-left ">
			<div class="w-full float-left md:w-4/12 lg:w-3/12 relative">
				<div class="w-full float-left shadow-md rounded-xl">
					<div class="w-full float-left py-10 px-6">
						<div class="w-full float-left text-center mb-10">
							<div class="w-full mb-3">
								<?php
								$profile_url = !empty($seller_info->profile_photo_path) ? URL::to('storage/' . $seller_info->profile_photo_path) : URL::asset('storage/profile-avatar.jpg');
								$currentUserId = !empty(auth()->user()->id) ? auth()->user()->id : "";
								$adPostedUserId = $seller_info->id;
								if (!empty($currentUserId) && ($currentUserId != $adPostedUserId)) {
									$enable = 1;
									$check_is_follow = App\Models\TblFollowers::check_is_follow($currentUserId, $adPostedUserId);
									$follow_text = ($check_is_follow == true) ? "Unfollow" : "Follow";
								} else {
									$enable = 0;
								}
								?>
								<img class="w-24 h-24 mx-auto object-cover object-center rounded-full" src="{{ $profile_url }}" />
							</div>
							<h3 class="text-black font-bold text-xl mb-6">{{$seller_info->name}}</h3>
							<?php if ($enable == 1) { ?>
								<button data-seller-id="{{ $adPostedUserId }}" id="seller_{{ $adPostedUserId }}" class="save_to_followers bg-green-500 w-full p-2 pb-3 text-white text-xl font-normal text-center rounded-md mb-6 border-2 border-green-500 hover:bg-white hover:text-green-500 ease-linear transition-all duration-150">
									<?php echo $follow_text; ?>
								</button>
							<?php } ?>
							<?php if ($enable == 0 && !empty($currentUserId)) { ?>
								<button class="invite_friends bg-green-500 w-full text-lg p-1 text-white font-semibold text-center rounded-md">
									{{__('messages.invite friends')}}
								</button>
							<?php } ?>
							<p class="text-center">
								<?php if ($enable == 1) { ?>
									<a href="#" class="block text-green-500 underline text-xl font-normal" id="report_user">{{__('messages.report user')}}</a>
								<?php } ?>
							</p>
						</div>	
							
						<div class="w-full float-left">
							<p class="cursor-pointer mb-2 text-xl text-gray-800" id="followers_list_click">
								{{__('messages.followers')}}<span class="float-right font-bold text-gray-500" id="seller_followers"><?php echo count($followers); ?></span>
							</p>
							<p class="cursor-pointer mb-2 text-xl text-gray-800" id="followings_list_click">
								{{__('messages.following')}} <span class="float-right font-bold text-gray-500"><?php echo count($followings); ?></span>
							</p>								
							<input type="hidden" value="<?php echo count($followers); ?>" class="seller_followers" />
						</div>
						
						<!--<div class="w-full float-left mt-6">
							<h2 class="text-black font-bold text-xl mb-6">Linked Accounts</h2>
							<ul>
								<li class="mb-4"><a href="#" class="text-xl text-gray-800">Phone Number <span class="float-right font-normal text-gray-500"><i class="fa fa-check bg-green-500 text-white p-1 rounded-full" aria-hidden="true"></i></span></a></li>
								<li class="mb-4"><a href="#" class="text-xl text-gray-800">Email <span class="float-right font-normal text-gray-500"><i class="fa fa-check bg-green-500 text-white p-1 rounded-full" aria-hidden="true"></i></span></a></li>
							</ul>
						</div>-->
						
					</div>
				</div>
			</div>
			
			<div class="w-full lg:w-9/12 pl-0 lg:pl-6 md:w-8/12 ml-auto mr-auto float-left ">
				<div class="float-left w-full">
					<div class="w-full float-left">
						<h1 class="text-black font-bold text-xl mb-10">Published Ads</h1>
					</div>
				</div>

				
                                <?php $i = 0; ?>
                                @foreach($seller_posts as $ads)
                                <?php
                                $i++;
                                $imgUrlfinal = App\Models\TblChat::getPostImg($ads->id);
                                $posted_on = date('d M Y', strtotime($ads['created_at']));
                                $slug = App\Models\TblPost::get_post_slug($ads["slug"]);
                                $ad_type = App\Models\TblPost::getAddtype($ads->id);
                                $ad_type = str_replace('_', ' ', strtoupper($ad_type->ad_type));


                                $fav_style = App\Models\TblSavedPosts::check_fav($ads->id);
                                $hearty = ($fav_style==true)?'fa-heart':'fa-heart-o';
                                $final_city_name = !empty($ads->locality) ? $ads->locality : $ads->city_name; // get locality & city
                                $currency_symbol = App\Models\TblPost::get_post_currency($ads->currency_id);
                                $get_categoryname = App\Models\TblCategory::getCategoryName($ads['category_id']);
                                
/* show the curreny symbol */
$settings = App\Models\Setting::get_logos();
$slected_currency = !empty($d['currency_id']) ? $d['currency_id'] : $settings['default_currency'];
$currency_symbol = App\Models\TblPost::get_post_currency($slected_currency);
                                ?>

                    <?php
                
                //echo $ss = App\Models\Setting::htmlAdBlock($adtype,$fav_style,$imgUrlfinal,$ads['title'],"$",$ads['price'],"category",$posted_on,$final_city_name);
                
                
                ?>



				<div class="w-1/2 md:w-1/3 sm:w-1/3 lg:w-1/3 xl:w-1/3 pb-3 lg:pb-6 float-left">
					<div class="w-full float-left p-1 relative">


                    

						<div class="w-full float-left overflow-hidden relative border-2 border-b-0">
                        <div class="items-center">
                            <div class="absolute left-0 top-0 z-10">
                                    <h3 class="text-xs text-white font-semibold relative">
                                        <span class="bg-yellow-500 px-2 py-1 rounded-tr-lg rounded-br-lg uppercase inline-block">{{$ad_type}}</span>
                                    </h3>
                            
                            </div>
                            <div class="absolute right-0 top-0 z-10">
                                <button type="button" id="favourate_post_id_{{ $ads['id'] }}" data-fav-post-id="{{ $ads['id'] }}" value="{{ $ads['title'] }}" class="bg-green-500 text-white w-8 h-8 save_favourate rounded-tl-lg rounded-bl-lg  text-center focus:outline-none border-0 save_favourate">
                                    <i class="fa {{$hearty}}" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <a href="{{$slug}}">
                        <img alt="{{$ads['title']}}" src="{{ $imgUrlfinal }}" class="transform hover:scale-125 transition duration-500 ease-in-out m-auto h-72"></a>
                        <!-- <img alt="{{$ads['title']}}" src="{{ $imgUrlfinal }}" class="w-full transform hover:scale-125 transition duration-500 ease-in-out h-80 w-80 object-cover object-center"> -->
						</div>
                        <div class="w-full float-left p-2 px-4 border-2 border-gray-200">
                        <a href="{{$slug}}">
                           <h4 class="font-sans text-gray-600 font-semibold hover:text-green-500 transition duration-500 ease-in-out"><?php echo mb_strimwidth($ads['title'], 0, 18, "..."); ?></h4>
                           <p class="my-2"><?php echo $currency_symbol[0]; ?><span class="text-black font-bold">{{ $ads['price'] }}</span> <span class="float-right text-gray-600"><?php echo mb_strimwidth($get_categoryname, 0, 15, ".."); ?></span></p>
                           <p class="inline-block"><span><img src="{{ URL::to('images/frontend/Group111.png') }}" class="w-4 align-middle inline-block mr-2"></span> <span class="inline-block text-gray-600"><?php echo mb_strimwidth($final_city_name, 0, 20, "..."); ?></span></p>
                                </a>
                        </div>
                    </div>
				</div>
			@endforeach

					
			</div>
			
			{{ $seller_posts->links() }}
		</div>
    </div>
</div>


<!-- Followers list -->
<div class="fixed z-10 inset-0 overflow-y-auto" id="followers_list" style="display:none">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="bg-white p-4">
                <div class="">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-xl leading-6 font-medium text-black" id="modal-headline">
                            {{__('messages.followers')}}
                        </h3>
                        <div class="mt-2">
                            <div class="py-2">
                                <?php if (count($followers) > 0) { ?>
                                    @foreach($followers as $follow)
                                    <?php
                                    if (!empty($follow->profile_photo_path)) {
                                        $follower_dp = URL::to('storage/' . $follow->profile_photo_path);
                                    } else {
                                        $follower_dp = URL::asset('storage/profile-avatar.jpg');
                                    }
                                    ?>
                                    <a href="<?php echo URL::to('seller-profile/' . $follow->id) ?>">
                                        <div class="flex items-center m-1 p-2 border-b-2 border-gray-200">
                                            <img class="w-10 h-10 md:w-16 sm:h-16 mr-4 rounded-full " src="<?php echo $follower_dp; ?>" />
                                            <div>
                                                <h3 class="text-md text-gray-900">{{$follow->name}}</h3>
                                            </div>
                                        </div>
                                    </a>
                                    @endforeach
                                <?php } else { ?>
                                    <div class="text-left">
                                        <h4 class="text-black text-lg">0 {{__('messages.followers')}}</h4>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="py-2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="close_followers_list" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-lg font-semibold text-black hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    {{__('messages.close')}}
                </button>
            </div>
        </div>
    </div>
</div>
<!-- followers end -->


<!-- Followings list -->
<div class="fixed z-10 inset-0 overflow-y-auto" id="followings_list" style="display:none">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="bg-white p-4">
                <div class="">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left">
                        <h3 class="text-xl leading-6 font-medium text-black" id="modal-headline">
                            {{__('messages.following')}}
                        </h3>
                        <div class="mt-2">
                            <div class="py-2">
                                <?php if (count($followings) > 0) { ?>
                                    @foreach($followings as $following)
                                    <?php
                                    if (!empty($following->profile_photo_path)) {
                                        $following_dp = URL::to('storage/' . $following->profile_photo_path);
                                    } else {
                                        $following_dp = URL::asset('storage/profile-avatar.jpg');
                                    }
                                    ?>
                                    <a href="<?php echo URL::to('seller-profile/' . $following->id) ?>">
                                        <div class="flex items-center m-1 p-2 border-b-2 border-gray-200">
                                            <img class="w-10 h-10 mr-4 rounded-full " src="<?php echo $following_dp; ?>" />
                                            <div>
                                                <h3 class="text-lg text-black">{{$following->name}}</h3>
                                            </div>
                                        </div>
                                    </a>
                                    @endforeach
                                <?php } else { ?>
                                    <div class="text-left">
                                        <h4>0 {{__('messages.following')}}</h4>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="py-2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">

                <button type="button" id="close_following_list" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    {{__('messages.close')}}
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Followings end -->
<!-- Invite friends start --->
<div class="fixed z-10 inset-0 overflow-y-auto" id="invite_friends" style="display:none">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="bg-white p-4">
                <div class="">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                            {{__('messages.invite friends')}}
                        </h3>
                        <div class="mt-2">
                            <div class="py-2">
                                <h3>{{__('messages.hi you can invite your friends through email')}}</h3>
                                <p class="text-sm mt-2 mb-1">{{__('messages.please enter email id here')}}</p>
                                <input type="text" name="email_ids" id="email_ids" class="px-3 py-3 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150 border border-2" />
                            </div>
                            <div class="py-2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" id="invite_frd_submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    {{__('messages.submit')}}
                </button>
                <button type="button" id="invite_frd_list" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    {{__('messages.close')}}
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Invite friends end --->

<!-- report this user start --->

<div class="fixed z-10 inset-0 overflow-y-auto" id="report" style="display:none">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-xl leading-6 font-medium text-gray-900" id="modal-headline">
                            {{__('messages.report user')}}
                        </h3>
                        <div class="mt-2">
                            <div class="py-2">
                                @foreach($report_types as $report)
                                <label class="text-base text-black"><input type="radio" value="{{$report->id}}" name="re_type" class="re_type ml-1 mr-1" required>{{$report->name}}</label><br>
                                @endforeach
                            </div>
                            <div class="py-2">
                                <label class="block text-gray-700 text-base font-semibold mb-2">{{__('messages.comment')}}:</label>
                                <textarea id="comment" maxlength="500" placeholder="Type here" rows="3" required class="text-sm shadow appearance-none border rounded w-full py-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                                <p class="text-xs font-semibold item-center text-gray-800">{{__('messages.character limit')}}: 500</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm" id="submit">
                    {{__('messages.send complaint')}}
                </button>
                <button type="button" id="cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-black hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    {{__('messages.cancel')}}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- report this user end --->
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    /* Followers list popup start */
    $('#followers_list_click').on('click', function(e) {
        document.querySelector("#followers_list").style.display = "block";
    });
    $('#close_followers_list').on('click', function(e) {
        document.querySelector("#followers_list").style.display = "none";
    });
    /* Followers list popup end */
    /* Following list popup start */
    $('#close_following_list').on('click', function(e) {
        document.querySelector("#followings_list").style.display = "none";
    });
    $('#followings_list_click').on('click', function(e) {
        document.querySelector("#followings_list").style.display = "block";
    });
    /* Following list popup end */

    /* Invite friends start*/

    $('.invite_friends').on('click', function(e) {
        var user = "{{auth()->user()}}";
        if (user == "") {
            window.location.href = "/login";
        } else {
            document.querySelector("#invite_friends").style.display = "block";
        }
        $('#invite_frd_submit').on('click', function(e) {
            var email_ids = $('#email_ids').val();
            if (email_ids == "") {
                toastr.warning('Email Id field is required..');
            } else {
                var mailformat = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
                if (email_ids.match(mailformat)) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "{{ route('invite_friend') }}",
                        data: {
                            email_ids: email_ids
                        },
                        success: function(data) {

                            $('#email_ids').val('');
                            if (data.result == "error") {
                                toastr.warning(data.message);
                            } else {
                                document.querySelector("#invite_friends").style.display = "none";
                                toastr.success(data.message);
                            }

                        }
                    });
                } else {
                    toastr.warning('Invalid Email Id');
                }
            }
        });
        $('#invite_frd_list').on('click', function(e) {
            document.querySelector("#invite_friends").style.display = "none";
        });
    });
    /* Invite friends end */
    $(".save_to_followers").click(function(e) {
        var val = $(this).attr("data-seller-id");
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "{{ route('savefollowers') }}",
            data: {
                seller_id: val
            },
            success: function(data) {
                if (data.result == "failed" && data.flag == "0") {
                    toastr.warning(data.message);
                } else if (data.result == "success" && data.flag == "1") {
                    var s_cnt = parseInt($(".seller_followers").val()) + 1;
                    $(".seller_followers").val(s_cnt);
                    $("#seller_followers").text(s_cnt);
                    $("#seller_" + val).text("Unfollow");
                    toastr.success(data.message);
                    location.reload();
                } else {
                    var s_cnt = parseInt($(".seller_followers").val()) - 1;
                    $(".seller_followers").val(s_cnt);
                    $("#seller_followers").text(s_cnt);
                    $("#seller_" + val).text("Follow");
                    toastr.success(data.message);
                    location.reload();
                }

            }
        });
    });


    /* report user start */
    $('#report_user').on('click', function(e) {
        var user = "{{$currentUserId}}";
        var reportUser = "{{$adPostedUserId}}";
        if (user == "") {
            window.location.href = "/login";
        } else {
            document.querySelector("#report").style.display = "block";
        }
        $('#submit').on('click', function(e) {
            var comment = $('#comment').val();
            var retype = $('input[name="re_type"]:checked').val();
            if (retype == null) {
                toastr.warning('report type field is required..');
                return;
            }
            if (comment == "") //change if condition
            {
                toastr.warning('Comment field is required..');
                return;
            }
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "{{ route('report_user') }}",
                data: {
                    comment: comment,
                    retype: retype,
                    user: user,
                    reportUser: reportUser
                },
                success: function(data) {
                    window.location.reload();
                    document.querySelector("#report").style.display = "none";
                    $('#comment').val('');
                    $('input[name="re_type"]').attr('checked', false);
                    toastr.success(data.message);
                }
            });
        });
        $('#cancel').on('click', function(e) {
            document.querySelector("#report").style.display = "none";
        });
    });


    /* report user end */
</script>
</div>