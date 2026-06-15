<div class="pt-12">
    <div class="container m-auto px-5 mb-12">
        <div class="md:flex">
            <div class="md:w-3/12 md:pr-4">
                <div class="px-2 py-2 rounded shadow-md">
                    <div class="m-1 p-1">
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
                        <img class="w-24 m-auto rounded-full h-24 " src="{{ $profile_url }}" />
                        <div>
                            <h3 class="text-2xl text-center font-bold mb-3">{{$seller_info->name}}</h3>
                            <?php if ($enable == 1) { ?>
                                <button data-seller-id="{{ $adPostedUserId }}" id="seller_{{ $adPostedUserId }}" class="save_to_followers bg-green-500 w-full text-lg p-1 text-white font-semibold text-center rounded-md">
                                    <?php echo $follow_text; ?>
                                </button>
                            <?php } ?>
                            <?php if ($enable == 0 && !empty($currentUserId)) { ?>
                                <button class="invite_friends bg-green-500 w-full text-lg p-1 text-white font-semibold text-center rounded-md">
                                    {{__('messages.invite friends')}}
                                </button>
                            <?php } ?>
                            <p class="text-lg p-1 text-green-500 font-semibold text-center">
                                <?php if ($enable == 1) { ?>
                                    <a href="#" class="text-sm underline mr-4" id="report_user">{{__('messages.report user')}}</a>
                                <?php } ?>
                            </p>
                            <p class="cursor-pointer p-1" id="followers_list_click">
                                {{__('messages.followers')}}<span class="float-right" id="seller_followers"><?php echo count($followers); ?></span>
                            </p>
                            <p class="cursor-pointer p-1" id="followings_list_click">
                                {{__('messages.following')}} <span class="float-right"><?php echo count($followings); ?></span>
                            </p>
                            <input type="hidden" value="<?php echo count($followers); ?>" class="seller_followers" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="md:w-9/12">
                <div class="flex flex-wrap">
                    <!-- seller posts -->
                    <div class="w-full">
                        <h1 class="text-lg font-bold font-light px-2">
                            {{__('messages.Published Ads')}}
                        </h1>
                        <div class="py-2 px-2">
                            <div class="flex flex-wrap">
                                <?php $i = 0; ?>
                                @foreach($seller_posts as $ads)
                                <?php
                                $i++;
                                $imgUrlfinal = App\Models\TblChat::getPostImg($ads->id);
                                $posted_on = date('d M Y', strtotime($ads['created_at']));
                                $slug = App\Models\TblPost::get_post_slug($ads["slug"]);
                                $adtype = App\Models\TblPost::getAddtype($ads->id);
                                $fav_style = App\Models\TblSavedPosts::check_fav($ads->id);
                                $final_city_name = !empty($ads->locality) ? $ads->locality : $ads->city_name; // get locality & city
                                $currency_symbol = App\Models\TblPost::get_post_currency($ads->currency_id);

                                ?>
                                <div class="pb-3 w-full md:w-2/6 sm:w-2/4 lg:w-2/6 xl:w-2/6">
                                    <div class="border border-gray-200 shadow m-2">
                                        <div class="flex flex-wrap items-center absolute">
                                            <div class="mt-2">
                                                <?php
                                                if (!empty($adtype)) {
                                                ?>
                                                    <h3 class="text-xs text-white font-bold">
                                                        <span class="bg-green-500 px-2 py-2 rounded-tr-lg rounded-br-lg uppercase">
                                                            <?php echo str_replace('_', ' ', strtoupper($adtype->ad_type)); ?>
                                                        </span>
                                                    </h3>
                                                <?php } ?>
                                            </div>
                                            <div class="absolute left-64 top-0 w-full">
                                                <button type="button" id="favourate_post_id_{{ $ads->id }}" data-fav-post-id="{{ $ads->id }}" value="{{$ads->title}}" class="text-white leading-8 w-8 h-8 save_favourate rounded-tl-lg rounded-bl-lg focus:outline-none text-center border-0 bg-green-500">
                                                    @if(!empty($fav_style))
                                                    <i class="fa fa-heart" aria-hidden="true"></i>
                                                    @else
                                                    <i class="fa fa-heart-o" aria-hidden="true"></i>
                                                    @endif
                                                </button>
                                            </div>
                                        </div>
                                        <a href="{{$slug}}">
                                            <img alt="{{$ads['title']}}" src="{{ $imgUrlfinal }}" class="m-auto h-48">
                                            <p class="4xl text-gray-600 px-4 pt-4"><?php echo mb_strimwidth($ads['title'], 0, 18, "..."); ?></p>
                                            <p class="4xl font-bold text-gray-600 px-4">
                                                <?php echo $currency_symbol[0]; ?>{{ $ads['price'] }}
                                                <span class="pr-1 text-xs mb-1 md:float-right">
                                                    &nbsp;{{$posted_on}}
                                                </span>
                                            </p>
                                            <p class="4xl text-gray-600 px-4 pb-2">
                                                <span class="inline-block align-middle">
                                                    <img class="w-4" src="{{ URL::to('images/frontend/Group111.png') }}" alt="location"></span>
                                                <span class="inline-block align-middle pl-1"><?php echo mb_strimwidth($final_city_name, 0, 20, "..."); ?></span>
                                            </p>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            {{ $seller_posts->links() }}
                        </div>

                    </div>


                </div>
            </div>
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
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
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
                                            <img class="w-10 h-10 mr-4 rounded-full " src="<?php echo $follower_dp; ?>" />
                                            <div>
                                                <h3 class="text-md text-gray-900">{{$follow->name}}</h3>
                                            </div>
                                        </div>
                                    </a>
                                    @endforeach
                                <?php } else { ?>
                                    <div class="text-left">
                                        <h4>0 {{__('messages.followers')}}</h4>
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
                <button type="button" id="close_followers_list" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
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
                                                <h3 class="text-md text-gray-900">{{$following->name}}</h3>
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
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                            {{__('messages.report user')}}
                        </h3>
                        <div class="mt-2">
                            <div class="py-2">
                                @foreach($report_types as $report)
                                <label class="text-sm"><input type="radio" value="{{$report->id}}" name="re_type" class="re_type ml-1 mr-1" required>{{$report->name}}</label><br>
                                @endforeach
                            </div>
                            <div class="py-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">{{__('messages.comment')}}:</label>
                                <textarea id="comment" maxlength="500" placeholder="Type here" rows="3" required class="text-sm shadow appearance-none border rounded w-full py-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                                <p class="text-xs font-bold item-center text-gray-800">{{__('messages.character limit')}}: 500</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm" id="submit">
                    {{__('messages.send complaint')}}
                </button>
                <button type="button" id="cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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