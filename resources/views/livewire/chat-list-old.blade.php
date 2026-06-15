<div class="root-element-div">
    <!-- section start --->
    <link href="{{ URL::to('css/chat.css') }}" rel="stylesheet">
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBrwbVt6abHnoZjzNWYWhYhQLwL45mO2hA&callback=initMap&libraries=places&v=weekly" defer></script>
    <div class="w-full">
        <div class="container mx-auto px-4 lg:px-0">
            <h4 class="my-4 text-xl font-bold lg:my-8">CHATS</h4>
            <div class="w-full block bg-gray-100 border rounded-lg  p-0 md:p-4 mb-5 lg:flex justify-center">
                <!--left-chat-->
                <div class="w-full md:w-5/12 lg:w-2/5 bg-gray-50 float-left py-4 rounded-lg overflow-auto" style="height:700px;">
                    <!-- search box -->
                    <div class="search_icon relative max-w-md mx-auto mb-4">
                        <h1 class="text-3xl heading_div" wire:ignore><?php echo __('messages.inbox'); ?> <span class="float-right text-xl mt-2"><button class="search focus:outline-none"><i class="fa fa-search" aria-hidden="true"></i></button></span></h1>
                        <div class="md:flex flex-row flex-wrap items-center lg:ml-auto search_div" wire:ignore style="display:none">
                            <input type="text" wire:model="search" class="px-3 py-4 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-11/12 pl-10 mr-1" placeholder="Search by post name">
                            <button class="close focus:outline-none"><i class="fa fa-times" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    <!-- search box end -->
                    <div class="w-full chats rounded-lg float-left">
                        <!-- chat list start -->
                        @foreach($chatlists as $chatlist)
                        <?php
                        $selected = "bg-white";
                        $sender = ((auth()->user()->id == $chatlist->from_id) ? $chatlist->to_id : $chatlist->from_id);
                        $chaturl = URL::to('/chat') . '?to=' . $sender . '&p=' . $chatlist->post_id . '&type=old';
                        $sendername = App\Models\TblChat::getSender($sender);
                        $unread_count = App\Models\TblChat::getUnreadCount(auth()->user()->id, $sender, $chatlist->post_id);
                        if ((request()->to == $sender) && (request()->p == $chatlist->post_id)) {
                            $selected = "bg-gray-100";
                        }
                        $lastchat = App\Models\TblChat::getLastChat($sender, $chatlist->post_id);
                        ?>
                        <div class="flex {{ $selected }}">
                            <a href="{{$chaturl}}" class="chat_lists w-full">
                                <?php $user_profle = App\Models\User::where('id', $sender)->pluck('profile_photo_path')->first(); ?>
                                <div class="w-full px-3 float-left user_chats lg:px-8 py-4 border-b">
                                    <div class="user_image w-18 md:mt-4 lg:w-14 lg:w-20 float-left mr-4">
                                        <img class="w-18 md:w-14 md:h-14 w-20 lg:w-20 float-left h-20 rounded-full  object-cover" src="<?php echo !empty($user_profle) ? URL::to('storage/' . $user_profle) : URL::to('storage/profile-avatar.jpg') ?>" alt="user.jpg">
                                    </div>
                                    <div class="user_detail w-4/6 float-right lg:w-3/4 pt-3">
                                        <span class="font-bold text-lg pb-1 inline-block">{{$sendername}}</span>
                                        <span class="bg-green-400 px-2 py-1 ml-2 rounded-full text-sm" data-ids="{{ $chatlist->post_id . '.' . $chatlist->receiver }}">
                                            {{$unread_count}}
                                        </span>
                                        <input type="hidden" class="{{ $chatlist->receiver.'_unread' }}" value="{{ $unread_count }}">
                                        <input type="hidden" class="{{ $chatlist->receiver.'_status' }}" value="<?php echo !empty($selected) ? "online" : "offline"; ?>">
                                        <time class="text-xs text-gray-500 float-right"></time>
                                        <p class="text-xs text-gray-500">{{$chatlist->post_name}}</p>
                                        <p class="flex text-xs text-gray-500" data-id="{{ $chatlist->post_id . '.' . $chatlist->receiver }}">
                                            <span class="mt-2 mr-2">
                                                @if(!empty($lastchat['msg']))
                                                @if($lastchat['make_offer'] == 1)
                                                {{ "Offer request sent "}}
                                                @elseif($lastchat['accept_offer'] == 1)
                                                {{ "Offer request accepted" }}
                                                @else
                                                {{ $lastchat['msg'] }}
                                                @endif
                                                @elseif(!empty($lastchat['attachment']))
                                                {{ "Image" }}
                                                @else
                                                {{ "location" }}
                                                @endif
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </a>
                            <button class="focus:outline-none delete_all pr-5 {{ $selected }} border-b" title="Delete Chat" data-id="{{ $sender . '@' . $chatlist->post_id }}"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                        </div>
                        @endforeach
                        <!-- chat list end -->
                    </div>
                </div>
                <!--right-chat-->
                <div class=" hidden md:block w-full pl-0 md:w-7/12 lg:w-3/5 lg:pl-4 float-right chat_detail" style="background-image:url('<?php echo URL::to('/images/chat-bg.png'); ?>');">
                    @if(empty($details))
                    <img class="m-auto w-40 mt-20" src="<?php echo URL::to('storage/emptyChat.png'); ?>" />
                    <p class="text-center mt-2"><?php echo __('messages.Select a chat to view conversation'); ?></p>
                    @else
                    <?php $get_post = App\Models\TblPost::where('id', request()->p)->first(); ?>
                    <?php $post_price = $get_post->price; ?>
                    <?php $currency_symbol = App\Models\TblPost::get_post_currency($get_post->currency_id); ?>
                    @foreach ($details as $day => $detail)
                    <?php
                    $receiver_detail = $detail[0]->receiver;
                    $sender_detail = ((auth()->user()->id == $detail[0]->from_id) ? $detail[0]->to_id : $detail[0]->from_id);
                    ?>
                    @endforeach
                    <?php $sendername = App\Models\TblChat::getSender(request()->to); ?>
                    <?php $check_blocked_user = App\Models\TblChat::checkBlocked(request()->to, request()->p); ?>
                    <?php $detail_user_info = App\Models\User::where('id', $sender_detail)->first(); ?>
                    <?php $detail_seller_profile = !empty($detail_user_info->profile_photo_path) ? URL::to('storage/' . $detail_user_info->profile_photo_path) : URL::to('storage/profile-avatar.jpg') ?>
                    <span class="py-3 px-4 block text-right close_btn block md:hidden ">
                        <svg fill="currentColor" viewBox="0 0 20 20" class="inline-block w-6 h-6">
                            <path x-show="open" fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                    <div class="w-full bg-green-500 rounded-lg p-4 float-left">
                        <div class="user_image w-10 lg:w-20 mr-2 float-left lg:mr-4">
                            <img class="w-10 lg:w-20 h-10 float-left lg:h-20 rounded-full  object-cover" src="{{ $detail_seller_profile }}" alt="user.jpg">
                        </div>
                        <div class="user_detail w-6/12 pt-1 float-left lg:w-8/12 lg:pt-3">
                            <p class="font-bold text-lg text-white pb-1 w-full">{{$sendername}}</p>
                            <a target="_blank" href="{{ URL::to('/'.$get_post->slug) }}" class="font-bold text-lg text-white underline pb-1 inline-block">{{$get_post->title}}</a>
                        </div>
                        <div class="w-3/12 float-right pb-0 pt-2 lg:w-1/6 lg:p-4 lg:pt-6">
                            <span class="block_user cursor-pointer text-xs text-white float-right inline-block pt-1" data-block-status="<?php echo !empty($check_blocked_user) ? $check_blocked_user : 0 ?>" data-id="<?php echo request()->to . '@' . request()->p; ?>">
                                @if($check_blocked_user == 0)
                                Block
                                @else
                                Unblock
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="w-full bg-white float-left">
                        <!--converstion-->
                        <div class="w-full chat_converstion p-4 overflow-auto h-96 float-left" data-selected-ids="<?php echo ($check_blocked_user == 0) ? $receiver_detail . "." . request()->p : "" ?>" id="messages_area" style="height:500px;">
                            @foreach ($details as $date => $detail)
                            <?php $grouped_date = App\Models\TblChat::checkChatDate($date); ?>
                            <div class="w-full mesage float-left">
                                <div class="w-full text-gray-500 my-3 text-center">{{ empty($grouped_date) ? date('d M y', strtotime($date)) : $grouped_date }}</div>
                                @foreach ($detail as $detail)
                                <?php ($detail->from_id == auth()->user()->id) ? $div_class = "float-right" : $div_class = "float-left"; ?>
                                <?php $time = date('h:i a', strtotime($detail->created_at)); ?>
                                <?php $tick = '<img class="w-3 h-3 inline ml-1" src="' . URL::to("storage/double-tick.png") . '"/>' ?>
                                @if($detail->read_status == 0)
                                <?php $tick = '<img class="w-3 h-3 inline ml-1" src="' . URL::to("storage/tick.png") . '"/>' ?>
                                @endif
                                @if(!empty($detail->msg) || (!empty($detail->attachment)))
                                <?php $make_offer_msg = ""; ?>
                                @if($detail->accept_offer == 1)
                                @if(auth()->user()->id == $get_post->user_id)
                                <?php
                                $make_offer_msg = "Buyer Offer " . $currency_symbol[0] . $detail->msg . "<span class='text-green-500 float-right font-bold'> Accepted </span>";
                                $meet_msg = "Let's move towards the final deal";
                                ?>
                                @else
                                <?php
                                $make_offer_msg = "Buyer Offer " . $currency_symbol[0] . $detail->msg . "<span class='text-white float-right font-bold'> Accepted </span>";
                                $meet_msg = "I accpet your offer, Let's move towards the final deal";
                                ?>
                                @endif
                                @endif
                                @if($detail->from_id == auth()->user()->id)
                                @if($detail->make_offer == 1)
                                <?php $make_offer_msg = "Your Offer " . $currency_symbol[0]; ?>
                                @endif
                                <div class="w-full float-right">
                                    <div class="bg-gray-100 chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-br-3xl rounded-tr-none float-right">
                                        @if(!empty($detail->attachment))
                                        <a href="{{ URL::to('/storage/'.$detail->attachment) }}" target="_blank">
                                            <img class="object-contain h-48 w-48 m-auto" src="{{ URL::to('/storage/'.$detail->attachment) }}" />
                                        </a>
                                        <time class="text-xs text-gray-500 float-right block text-right w-36 ">{{ $time }} <span><?php echo $tick ?></span></time>
                                        @else
                                        @if($detail->accept_offer == 1)
                                        <p class="text-base text-gray-800"><?php echo $make_offer_msg; ?></p>
                                        <p class="text-base text-gray-800"><?php echo $meet_msg; ?></p>
                                        @else
                                        <p class="text-base text-gray-800"><?php echo $make_offer_msg; ?>{{$detail->msg }}</p>
                                        @endif
                                        <time class="text-xs text-gray-500 float-right block text-right w-36 ">{{ $time }} <span><?php echo $tick ?></span></time>
                                        @if(empty($get_accept_offer))
                                        <?php if (!empty($get_make_offer->id) && ($get_make_offer->id == $detail->id)) { ?>
                                            <?php if (($get_make_offer->from_id == auth()->user()->id) || ($get_make_offer->from_id == $detail->receiver)) { ?>
                                                <?php $offer_price = $detail->msg; ?>
                                                <div class="edit_offer_btn">
                                                    <p class="cursor-pointer bg-green-500 text-white float-right hover:bg-white hover:text-black hover:border-green-500 shadow-lg border px-2 py-1 text-xs edit-offer mt-3 rounded-lg" data-defcurency="<?php echo $currency_symbol[0]; ?>" data-youroffer="<?php echo $offer_price; ?>">Edit Offer</p>
                                                </div>
                                        <?php }
                                        } ?>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                                @else
                                @if($detail->make_offer == 1)
                                @if($detail->to_id == $detail->receiver)
                                <?php $make_offer_msg = "Seller Offer " . $currency_symbol[0]; ?>
                                @else
                                <?php $make_offer_msg = "Buyer Offer " . $currency_symbol[0]; ?>
                                @endif
                                @endif
                                <div class="w-full float-left">
                                    <div class="bg-green-500 chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-tr-3xl rounded-br-none">
                                        @if(!empty($detail->attachment))
                                        <a href="{{ URL::to('/storage/app/public/'.$detail->attachment) }}" target="_blank">
                                            <img class="object-contain h-48 w-48 m-auto" src="{{ URL::to('/storage/app/public/'.$detail->attachment) }}" />
                                        </a>
                                        <time class="text-xs text-gray-500 float-right block text-right w-36 ">{{ $time }} <span><?php echo $tick ?></span></time>
                                        @else
                                        @if($detail->accept_offer == 1)
                                        <p class="text-base text-white"><?php echo $make_offer_msg; ?></p>
                                        <p class="text-base text-white"><?php echo $meet_msg; ?></p>
                                        @else
                                        <p class="text-base text-white"><?php echo $make_offer_msg; ?>{{$detail->msg }}</p>
                                        @endif
                                        <time class="text-xs text-white float-right block text-right w-36 ">{{ $time }} <span><?php echo $tick ?></span></time>
                                        @if(empty($get_accept_offer))
                                        <?php if (!empty($get_make_offer->id) && ($get_make_offer->id == $detail->id)) { ?>
                                            <?php if (($get_make_offer->from_id == auth()->user()->id) || ($get_make_offer->from_id == $detail->receiver)) { ?>
                                                <?php $offer_price = $detail->msg; ?>
                                                <div class="edit_offer_btn">
                                                    <p class="cursor-pointer hover:bg-green-500 hover:text-white float-right bg-white text-black border-green-500 hover:border-white shadow-lg border px-2 py-1 text-xs accept-offer mt-3 rounded-lg" data-defcurency="<?php echo $currency_symbol[0]; ?>" data-youroffer="<?php echo $offer_price; ?>">Accept</p>
                                                </div>
                                        <?php }
                                        } ?>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                                @endif
                                @endif
                                @if(!empty($detail->location))
                                <?php
                                $lat = $detail->latitude;
                                $long = $detail->longitude;
                                ?>
                                <div class="mb-2 w-full">
                                    <div class="w-full {{ $div_class }}">
                                        <div class="p-4 mb-4">
                                            <a class="viewShared {{ $div_class }}" href="https://www.google.com/maps?daddr={{$lat}},{{$long}}" target="_blank">
                                                <img src="https://maps.googleapis.com/maps/api/staticmap?center={{$lat}},{{$long}} ?>&amp;zoom=16&amp;size=400x200&amp;sensor=false&amp;maptype=roadmap&amp;markers=color:red%7Clabel:S%7C<?php echo $lat; ?>,<?php echo $long; ?>&amp;key=AIzaSyA_gSzPNC40ioDPHIb7kMkYFClDihdhhx4" style="width:400px;height:200px;">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                        @if($check_blocked_user == 0)
                        <!--question-->
                        <div class="w-full float-left p-4 pb-0">
                            <div class="text-center">
                                <h1 class="bg-gray-100 pl-4 pr-4 text-2xl rounded-t-lg show-hide-redymade-chat w-16 text-center m-auto cursor-pointer"><i class="fa fa-angle-down" aria-hidden="true"></i></h1>
                            </div>
                            <div class="rounded w-full bg-white">
                                <!-- Tabs -->
                                <ul id="tabs" class="inline-flex w-full border-b">
                                    <li class="px-4 w-1/2 text-center text-gray-800 font-semibold py-2 border-b -mb-px bg-gray-50 rounded-t-lg"><a id="default-tab" href="#first">QUESTIONS</a></li>
                                    <li class="px-4 w-1/2 text-center text-gray-800 font-semibold py-2 border-b -mb-px bg-gray-50 rounded-t-lg"><a href="#make-offer-chat">MAKE OFFERS</a></li>
                                </ul>
                                <!-- Tab Contents -->
                                <div id="tab-contents">
                                    <div id="first" class="p-4">
                                        <div class="max-w-3xl mx-auto">
                                            <div class="sm:flex items-center rounded p-2 bg-gray-100">
                                                <div class="mb-4 sm:mb-0 sm:mr-4 flex-1 self-start">
                                                    <h4 class="text-md font-bold"><span><b>Chat to know more!</b></span></h4>
                                                    <p class="mt-1 text-sm">
                                                        Close the deal faster by asking more about the product or person.
                                                    </p>
                                                </div>
                                                <div class="items-center flex">
                                                    <img src="<?php echo URL::to("storage/auto_answer_onboarding.webp"); ?>" class="w-12 h-12" />
                                                </div>
                                            </div>
                                        </div>
                                        @if($detail->accept_offer == 0)
                                        <ul class="list-unstyled inline-flex defalut-chat">
                                            <li data-defmsg="hello">Hello</li>
                                            @if($get_post->user_id == auth()->user()->id)
                                            <li data-defmsg="it's available">It's available</li>
                                            @else
                                            <li data-defmsg="is it available?">Is it available?</li>
                                            @endif
                                            <li data-defmsg="okay">Okay</li>
                                            <li data-defmsg="no problem">No Problem</li>
                                            @if($get_post->user_id != auth()->user()->id)
                                            <li data-defmsg="please reply">Please reply</li>
                                            <li data-defmsg="not interested">Not interested</li>
                                            @endif
                                        </ul>
                                        @endif
                                    </div>
                                    <div id="make-offer-chat" class="hidden p-4">
                                        @if($get_post->user_id != auth()->user()->id)
                                        <ul class="list-unstyled inline-flex">
                                            <li class="rounded-full border pl-4 pr-4 pt-2 pb-2 price-active" data-defcurncy="<?php echo $currency_symbol[0]; ?>" data-defmsg="<?php echo floor($post_price); ?>"><?php echo $currency_symbol[0] . floor($post_price); ?></li>
                                            <?php
                                            $perc = 0;
                                            $offer = "";
                                            for ($j = 1; $j <= 4; $j++) {
                                                if ($post_price > 100) {
                                                    $perc = $perc + 5;
                                                    $offer = $post_price - round(($post_price * $perc) / 100);
                                                } else {
                                                    $offer = $post_price - $j;
                                                }
                                            ?>
                                                <li class="rounded-full border pl-4 pr-4 pt-2 pb-2" data-defcurncy="<?php echo $currency_symbol[0]; ?>" data-defmsg="<?php echo $offer; ?>"><?php echo $currency_symbol[0] . $offer; ?></li>
                                            <?php } ?>
                                        </ul>
                                        <div class="make-offer-design">
                                            <input type="hidden" class="offer-price" value="<?php echo floor($post_price); ?>" />
                                            <input type="hidden" class="default_currency" value="<?php echo $currency_symbol[0]; ?>" />
                                            <p class="offer-price"><?php echo $currency_symbol[0] . floor($post_price); ?></p>
                                            <i class="fa fa-caret-up" aria-hidden="true"></i>
                                            <div class="flex items-center bg-gray-100 p-3 rounded">
                                                <i class="fa fa-hand-o-up ml-4 mr-4" aria-hidden="true"></i>
                                                <div clas="inline-flex items-center">
                                                    <h3 class="text-md text-gray-900">Very good offer!</h3>
                                                    <p class="text-sm text-gray-700">High chances of seller's reply.</p>
                                                </div>
                                                <button type="submit" name="send" id="send" class="send bg-green-500 text-white font-bold text-xs px-6 py-3 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 ml-10">Send</button>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--typemessage-->
                        <div class="w-full bg-white p-4 pt-0 float-left rounded-t-lg relative">
                            <form method="post" id="chat_form" name="chatform" onsubmit="return validateForm()" class="w-full" enctype="multipart/form-data">
                                <input type="hidden" name="current_user_id" class="current_user" value="{{ auth()->user()->id }}" />
                                <input type="hidden" name="post_id" class="post_id" value="{{ request()->p }}" />
                                <input type="hidden" name="to" class="to" value="{{ request()->to }}" />
                                <input type="hidden" class="receiver_id" value="{{ $receiver_detail }}" />
                                <div class="bg-gray-100 p-5 rounded-lg chat-box">
                                    <input class="msg w-3/5 md:w-3/4 bg-gray-100 outline-none" autocomplete="off" type="text" id="chat_message" name="chat_message" placeholder="Type message...">
                                    <div class="w-2/11 md:w-2/6 absolute right-16 top-4">
                                        <input type="hidden" id="file" name="filename">
                                        <span class="text-xs bg-green-500  text-center  w-8 h-8  ml-2 leading-8  md:w-10 md:h-10 md:ml-3 md:leading-9 text-white float-right inline-block rounded-full"><button type="submit" name="send" id="send" class="send"><img class="inline-block" src="{{ URL::to('images/frontend/send.png') }}" alt="send.png"></button></span>
                                        <span class="text-xs bg-green-500  text-center w-8 h-8  ml-2 leading-8  md:w-10 md:h-10 md:ml-3 md:leading-9 text-white float-right inline-block rounded-full ">
                                            <label class="attachments">
                                                <i class="fa fa-picture-o" aria-hidden="true"></i>
                                                <input type="file" accept="image/*" name="image" id="image" class="attachment" />
                                                <input type="hidden" id="file" name="filename">
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </form>
                            <div class="absolute right-4 top-4 chat-box">
                                <span class="text-xs bg-green-500  text-center  w-8 h-8  ml-2 leading-8  md:w-10 md:h-10 md:ml-3 md:leading-9 text-white float-right inline-block rounded-full">
                                    <button class="inline-block text-xl mt-1" id="share_loc_btn"><i class="fa fa-map-marker text-2xl" aria-hidden="true"></i></button>
                                </span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                <!--right-chatend-->
                <!-- share location start -->
                <div class="fixed z-10 inset-0 overflow-y-auto" id="share_loc_popup" style="display:none">
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
                                            Share Location
                                        </h3>
                                        <div class="mt-2">
                                            <div class="py-2">
                                                <input id="pac-input" class="controls" type="text" placeholder="Enter a location">
                                                <div id="type-selector" class="controls">
                                                    <input type="radio" name="type" id="changetype-all" checked="checked">
                                                    <label for="changetype-all">All</label>

                                                    <input type="radio" name="type" id="changetype-establishment">
                                                    <label for="changetype-establishment">Establishments</label>

                                                    <input type="radio" name="type" id="changetype-address">
                                                    <label for="changetype-address">Addresses</label>

                                                    <input type="radio" name="type" id="changetype-geocode">
                                                    <label for="changetype-geocode">Geocodes</label>
                                                </div>
                                                <div id="shared_location_map"></div>

                                                <input type="hidden" id="user_latitude" value="">
                                                <input type="hidden" id="user_longitude" value="">
                                                <input type="hidden" id="user_address" value="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm" id="submit_shared_loc">
                                    Share
                                </button>
                                <button type="button" id="share_loc_cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- share location end -->
            </div>
        </div>
    </div>
    <!-- section end -->
    <?php
    if (Session::has("CurrLoggedLat")) {
        $current_lat = Session::get("CurrLoggedLat");
        $currenct_long = Session::get("CurrLoggedLng");
    } else {
        $current_lat = "-33.8688";
        $currenct_long = "151.2195";
    }
    ?>
    <!-- share locatin map -->
    <script type="text/javascript">
        function initMap() {
            var map = new google.maps.Map(document.getElementById('shared_location_map'), {
                center: {
                    lat: <?php echo $current_lat; ?>,
                    lng: <?php echo $currenct_long; ?>
                },
                zoom: 13
            });
            var input = /** @type {!HTMLInputElement} */ (
                document.getElementById('pac-input'));
            document.getElementById('user_latitude').value = <?php echo $current_lat; ?>;
            document.getElementById('user_longitude').value = <?php echo $currenct_long; ?>;
            var types = document.getElementById('type-selector');
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);

            var infowindow = new google.maps.InfoWindow();
            var marker = new google.maps.Marker({
                position: {
                    lat: <?php echo $current_lat; ?>,
                    lng: <?php echo $currenct_long; ?>
                },
                title: 'Marker',
                map: map,
                anchorPoint: new google.maps.Point(0, -29),
                draggable: true,
                animation: google.maps.Animation.DROP,
            });

            autocomplete.addListener('place_changed', function() {
                infowindow.close();
                marker.setVisible(false);

                var place = autocomplete.getPlace();

                document.getElementById('user_address').value = $("#pac-input").val();
                if (!place.geometry) {
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                    window.alert("No details available for input: '" + place.name + "'");
                    return;
                }

                // If the place has a geometry, then present it on a map.
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17); // Why 17? Because it looks good.
                }
                marker.setIcon( /** @type {google.maps.Icon} */ ({
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(35, 35)
                }));

                var lat = place.geometry.location.lat();
                var long = place.geometry.location.lng();

                document.getElementById('user_latitude').value = lat;
                document.getElementById('user_longitude').value = long;

                marker.setPosition(place.geometry.location);
                marker.setVisible(true);
                var address = '';
                if (place.address_components) {
                    address = [
                        (place.address_components[0] && place.address_components[0].short_name || ''),
                        (place.address_components[1] && place.address_components[1].short_name || ''),
                        (place.address_components[2] && place.address_components[2].short_name || '')
                    ].join(' ');
                }
                infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
                infowindow.open(map, marker);
            });

            // Sets a listener on a radio button to change the filter type on Places
            // Autocomplete.
            function setupClickListener(id, types) {
                var radioButton = document.getElementById(id);
                radioButton.addEventListener('click', function() {
                    autocomplete.setTypes(types);
                });
            }

            setupClickListener('changetype-all', []);
            setupClickListener('changetype-address', ['address']);
            setupClickListener('changetype-establishment', ['establishment']);
            setupClickListener('changetype-geocode', ['geocode']);

            marker.addListener('drag', entreXY);
            map.addListener('click', deplacerClick);

            function entreXY(event) {
                var lat = event.latLng.lat();
                var long = event.latLng.lng();
                document.getElementById('user_latitude').value = lat;
                document.getElementById('user_longitude').value = long;
                var ss = 'X: ' + lat + '<br/> Y: ' + long;
            }

            function deplacerClick(event) {
                var lat = event.latLng.lat();
                var long = event.latLng.lng();
                marker.setPosition(new google.maps.LatLng(lat, long));
                document.getElementById('user_latitude').value = lat;
                document.getElementById('user_longitude').value = long;
                var ss = 'X: ' + lat + '<br/> Y: ' + long;
            }
        }

        $(document).ready(function() {
            $(document).on("click", "#submit_shared_loc", function() {
                var address_loc = $("#user_address").val();
                var address_lat = $("#user_latitude").val();
                var address_long = $("#user_longitude").val();
                var to = $(".to").val();
                var post_id = $(".post_id").val();
                if (address_loc == "" || address_loc == null) {
                    $("#pac-input").focus();
                    toastr.warning("Please enter valid address to share with the user!");
                    return false;
                } else {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "{{ URL::to('share_location') }}",
                        data: {
                            to: to,
                            loc: address_loc,
                            lat: address_lat,
                            long: address_long,
                            post_id: post_id,
                        },
                        success: function(data) {
                            var elem = document.getElementById('messages_area');
                            elem.scrollTop = elem.scrollHeight;
                            window.location.reload();
                        }
                    });
                }
            });
        });
    </script>
    <!-- share location map end -->
    <script>
        function delete_chat(id, type) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "{{ URL::to('delete_chat') }}",
                data: {
                    id: id,
                    type: type
                },
                cache: false,
                success: function(data) {
                    toastr.success(data.message);
                    if (data.type == "delete") {
                        window.location = "<?php echo URL::to('/chat'); ?>";
                    } else {
                        location.reload();
                    }

                }
            });
        }
    </script>

    <!-- websocket connection start --->
    <script>
        $(document).ready(function() {
            var conn = new WebSocket("ws://localhost:8090");
            conn.onopen = function(e) {
                console.log("WebSocket Connection established!");
            };
            conn.onmessage = function(e) {
                var data = JSON.parse(e.data);
                if (data != "") {
                    var user_status = $("." + data.receiveid + "_status").val();
                    var read_status = 0;
                    var html_data = "";
                    var time = "<?php echo date('h:i a'); ?>";
                    var tick = "<span class='mt-3 ml-1 mr-1'><img class='w-3 h-3 inline' src='<?php echo URL::to('storage/'); ?>/tick.png'></span>";
                    if (data.status_type == 'Online') {
                        var tick = "<span class='mt-3 ml-1 mr-1'><img class='w-3 h-3 inline' src='<?php echo URL::to('storage/'); ?>/double-tick.png'></span>";
                    }
                    // offer msg appended msg
                    var offer_msg = "";
                    var edit_offer = "";
                    var accept_offer = "";
                    if (data.make_offer != "") {
                        if (data.from == "Me") {
                            offer_msg = "Your offer " + data.currency;
                            edit_offer = '<div class="edit_offer_btn"><p class="cursor-pointer bg-green-500 text-white float-right hover:bg-white hover:text-black hover:border-green-500 shadow-lg border px-2 py-1 text-xs edit-offer mt-3 rounded-lg" data-defcurency="' + data.currency + '" data-youroffer="' + data.msg + '">Edit Offer</p></div>';
                        } else {
                            if (data.to == data.receiveid) {
                                offer_msg = "Seller Offer " + data.currency;
                            } else {
                                offer_msg = "Buyer Offer " + data.currency;
                            }
                        }
                    }

                    if (data.from == "Me") {
                        if (data.msg != "") {
                            if (edit_offer != "") {
                                $('.edit_offer_btn').hide();
                            }
                            if (data.accept_offer != "") {
                                $(".accept-offer").hide();
                                $('.edit_offer_btn').hide();
                                if ("<?php echo auth()->user()->id; ?>" == data.post_user_id) {
                                    var show_offer_msg = "Buyer Offer " + data.currency + data.msg + "<span class='text-green-500 float-right font-bold'> Accepted </span>";
                                    var meet_msg = "Let's move towards the final deal";
                                } else {
                                    var show_offer_msg = "Buyer Offer " + data.currency + data.msg + "<span class='text-green-500 float-right font-bold'> Accepted </span>";
                                    var meet_msg = "I accpet your offer, Let's move towards the final deal";
                                }
                                html_data = '<div class="w-full float-right"><div class="bg-gray-100 chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-br-3xl rounded-tr-none float-right"><p class="text-base text-gray-800"><p class="text-base text-gray-800">' + show_offer_msg + '</p><p class="text-base text-gray-800"> ' + meet_msg + '</p><time class="text-xs text-gray-500 float-right block text-right w-36 ">' + time + '<span>' + tick + '</span></time></div>';
                            } else {
                                html_data = '<div class="w-full message float-right "><div class="bg-gray-100 chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-br-3xl rounded-tr-none float-right"><p class="text-base text-gray-800"> ' + offer_msg + data.msg + '</p><time class="text-xs text-gray-500 float-right block text-right w-36">' + time + tick + '</time> ' + edit_offer + '</div> </div>';
                            }
                        }
                        if (data.image != "") {
                            html_data = '<div class="w-full float-right"><div class="bg-gray-100 chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-br-3xl rounded-tr-none float-right"><img class="w-24 h-24 m-auto object-none base64-img cursor-pointer" src="' + data.image + '"/><time class="text-xs text-gray-500 float-right block text-right w-36 ">' + time + '<span>' + tick + '</span></time></div></div>';
                        }
                    } else {
                        if (data.msg != "") {
                            if (data.accept_offer != "") {
                                if ("<?php echo auth()->user()->id; ?>" == data.post_user_id) {
                                    var show_offer_msg = "Buyer Offer " + data.currency + data.msg + "<span class='text-green-500 float-right font-bold'> Accepted </span>";
                                    var meet_msg = "Let's move towards the final deal";
                                } else {
                                    var show_offer_msg = "Buyer Offer " + data.currency + data.msg + "<span class='text-green-500 float-right font-bold'> Accepted </span>";
                                    var meet_msg = "I accpet your offer, Let's move towards the final deal";
                                }
                                html_data = '<div class="w-full float-left"><div class="bg-gray-100 chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-br-3xl rounded-tr-none float-right"><p class="text-base text-gray-800"><p class="text-base text-gray-800">' + show_offer_msg + '</p><p class="text-base text-gray-800"> ' + meet_msg + '</p><time class="text-xs text-gray-500 float-right block text-right w-36 ">' + time + '<span>' + tick + '</span></time></div>';
                            } else {
                                html_data = '<div class="w-full message float-left"><div class="bg-green-500 chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-tr-3xl rounded-br-none"><p class="text-base text-white">' + offer_msg + data.msg + '</p><time class="text-xs text-white float-right block text-right w-36 ">' + time + tick + '</time></div></div>';
                            }
                        }
                        if (data.image != "") {
                            html_data = '<div class="w-full message float-left"><div class="bg-green-500 chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-tr-3xl rounded-br-none"><img class="w-24 h-24 m-auto object-none base64-img cursor-pointer" src="' + data.image + '"/><time class="text-xs text-white float-right block text-right w-36 ">' + time + tick + '</time></div></div>';
                        }
                    }

                    var selected_ids = data.receiveid + '.' + data.post_id;
                    $('[data-selected-ids="' + selected_ids + '"]').append(html_data);
                    $("#chat_message").val("");
                    if (data.msg != "") {
                        if (data.make_offer != "") {
                            var last_chat_html = '<span class="mt-2 mr-2">Offer request</span>';
                        } else {
                            var last_chat_html = '<span class="mt-2 mr-2">' + data.msg + '</span>';
                        }

                    } else if (data.image != "") {
                        var last_chat_html = '<span class="mt-2 mr-2">Image</span>';
                    } else {
                        var last_chat_html = '<span class="mt-2 mr-2">location</span>';
                    }
                    var lastchat = data.post_id + '.' + data.receiveid;
                    $('[data-id="' + lastchat + '"]').html(last_chat_html);
                    var elem = document.getElementById('messages_area');
                    elem.scrollTop = elem.scrollHeight;
                }
                return false;
            };
            conn.onclose = function(e) {
                //keepAlive();
                console.log("WebSocket Connection Closed!", e);
            }
            conn.onerror = function(e) {
                //cancelKeepAlive();
                console.log("WS GOT ERROR!", e);
            }
            $('#chat_form').on('submit', function(event) {
                event.preventDefault();
                var to = $(".to").val();
                var post_id = $(".post_id").val();
                var current_user = $(".current_user").val();
                var message = $('#chat_message').val();
                var imgnaam = "";
                var reciv_id = $(".receiver_id").val();
                if ((message != "") || (imgnaam != "")) {
                    var formData = new FormData(this);
                    var data = {
                        from: current_user,
                        to: to,
                        msg: message,
                        post_id: post_id,
                        image: imgnaam,
                        receiveid: reciv_id,
                        make_offer: "",
                        accept_offer: "",
                        post_user_id: "",
                        currency: ""
                    };
                    send_chat(formData);
                    conn.send(JSON.stringify(data));
                }
            });

            // sending image attachement
            $('#image').change(function(e) {
                var _imgage = $('#image').prop('files')[0];
                var to = $(".to").val();
                var post_id = $(".post_id").val();
                var current_user = $(".current_user").val();
                var message = $('#chat_message').val();
                var reciv_id = $(".receiver_id").val();

                let file = e.target.files[0];
                let reader = new FileReader();
                reader.onloadend = function() {
                    var data = {
                        from: current_user,
                        to: to,
                        msg: "",
                        post_id: post_id,
                        image: reader.result,
                        receiveid: reciv_id,
                        make_offer: "",
                        accept_offer: "",
                        post_user_id: "",
                        currency: ""
                    };
                    conn.send(JSON.stringify(data));
                }
                reader.readAsDataURL(file);
                if (_imgage != "") {
                    var postData = new FormData();
                    postData.append('from', current_user);
                    postData.append('to', to);
                    postData.append('msg', "");
                    postData.append('post_id', post_id);
                    postData.append('image', _imgage);
                    postData.append('receiveid', reciv_id);
                    send_chat(postData);
                }
            });

            $(".defalut-chat li").on('click', function(e) {
                e.preventDefault();
                var message = $(this).attr('data-defmsg');
                var to = $(".to").val();
                var current_user = $(".current_user").val();
                var post_id = $(".post_id").val();
                var reciv_id = $(".receiver_id").val();
                var make_offer = "";
                var accept_offer = "";
                var data = {
                    from: current_user,
                    to: to,
                    msg: message,
                    post_id: post_id,
                    receiveid: reciv_id,
                    make_offer: "",
                    accept_offer: "",
                    post_user_id: "",
                    currency: ""
                };
                send_default_chat(message, to, post_id, make_offer, accept_offer);
                conn.send(JSON.stringify(data));
            });
            $(".make-offer-design #send").on('click', function(e) {
                e.preventDefault();
                var message = $(".make-offer-design input.offer-price").val();
                var currency = $(".default_currency").val();
                var to = $(".to").val();
                var post_id = $(".post_id").val();
                var reciv_id = $(".receiver_id").val();
                var current_user = $(".current_user").val();
                var make_offer = "yes";
                var accept_offer = "";
                var data = {
                    from: current_user,
                    to: to,
                    msg: message,
                    post_id: post_id,
                    receiveid: reciv_id,
                    make_offer: "yes",
                    accept_offer: "",
                    post_user_id: "",
                    currency: currency
                };
                send_default_chat(message, to, post_id, make_offer, accept_offer);
                conn.send(JSON.stringify(data));
            });
            $("body").delegate(".accept-offer", 'click', function(e) {
                e.preventDefault();
                var message = $(this).attr('data-youroffer');
                var currency = $(this).attr('data-defcurency');
                var to = $(".to").val();
                var post_id = $(".post_id").val();
                var reciv_id = $(".receiver_id").val();
                var current_user = $(".current_user").val();
                var accept_offer = "yes";
                var post_user_id = $(this).attr('p_uid');;
                var data = {
                    from: current_user,
                    to: to,
                    msg: message,
                    post_id: post_id,
                    receiveid: reciv_id,
                    make_offer: "",
                    accept_offer: "yes",
                    post_user_id: post_user_id,
                    currency: currency
                };
                send_default_chat(message, to, post_id, "", accept_offer);
                conn.send(JSON.stringify(data));
            });
        });

        function send_default_chat(message, to, post_id, make_offer, accept_offer) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "{{ URL::to('send_chat') }}",
                data: {
                    chat_message: message,
                    to: to,
                    post_id: post_id,
                    image: "",
                    accept_offer: accept_offer,
                    make_offer: make_offer
                },
                success: function(data) {
                    var elem = document.getElementById('messages_area');
                    elem.scrollTop = elem.scrollHeight;
                }
            });
        }
    </script>
    <!-- websocket connection end -->
    <!-- open the base64 image in new tab start --->
    <script>
        $("body").delegate(".base64-img", 'click', function(e) {
            var src = $(this).attr('src');
            var newTab = window.open();
            newTab.document.body.innerHTML = '<img src="' + src + '" width="100px" height="100px">';
        });
    </script>
    <!-- open the base64 image in new tab end -->

    <!--- send chat --->
    <script>
        function send_chat(formData) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "{{ URL::to('send_chat') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    var elem = document.getElementById('messages_area');
                    elem.scrollTop = elem.scrollHeight;
                }
            });
        }
    </script>
    <script src="{{ URL::to('js/chat.js') }}"></script>
</div>