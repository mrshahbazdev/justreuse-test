<div class="root-element-div">
    <link href="{{ URL::to('css/chat.css') }}" rel="stylesheet">
    <section class="relative w-full h-full py-10 min-h-screen">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap border-solid border-1 border-gray-500">
                <!--LEFT PORTION -->
                <div class="w-full md:w-4/12 ml-auto mr-auto border-solid border-4 border-light-blue-500 overflow-auto" style="height:600px;">
                    <h1 class="bg-gray-200 text-3xl p-2 pl-4 heading_div border-solid border-b-4 border-light-blue-500" wire:ignore><?php echo __('messages.inbox'); ?> <span class="float-right text-xl mt-2"><button class="search focus:outline-none"><i class="fa fa-search" aria-hidden="true"></i></button></span></h1>
                    <div class="md:flex flex-row flex-wrap items-center lg:ml-auto search_div bg-gray-300 p-2" wire:ignore style="display:none">
                        <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-11/12 pl-10 mr-1" placeholder="Search by post name">
                        <button class="close focus:outline-none"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                    <!-- Chat list group by post start -->
                    <div>
                        @foreach($chatlists as $chatlist)
                        <?php
                        $sender = ((auth()->user()->id == $chatlist->from_id) ? $chatlist->to_id : $chatlist->from_id);
                        $chaturl = URL::to('/chat') . '?to=' . $sender . '&p=' . $chatlist->post_id . '&type=old';
                        ?>
                        <?php $lastchat = App\Models\TblChat::getLastChat($sender, $chatlist->post_id); ?>
                        <?php
                        $post_deleted = App\Models\TblPost::where('id', $chatlist->post_id)->whereNull('deleted_at')->pluck('id');
                        $deleted_class = count($post_deleted) > 0 ? "" : "bg-red-300";
                        ?>
                        <?php
                        if ((request()->to == $sender) && (request()->p == $chatlist->post_id)) {
                            $selected = "active";
                        } else {
                            $selected = "";
                        }
                        ?>
                         @if(empty($deleted_class))  
                        <div class="flex border-b-2 border-gray-300 flex-grow <?php echo !empty($deleted_class) ? $deleted_class : $selected; ?>">
                       
                        <a href="<?php echo $chaturl; ?>" class="chat_lists w-full">
                                <div class="flex items-center p-3 w-full two-div">
                                    <?php //$post_img = App\Models\TblChat::getPostImg($chatlist->post_id); 
                                    ?>
                                    <?php $post_img = App\Models\User::where('id', $sender)->pluck('profile_photo_path')->first(); ?>
                                    <img style="border-radius:10px;border:1px solid #e1e1e1;padding:5px;" class="w-16 h-16 mr-3" src="<?php echo !empty($post_img) ? URL::to('storage/' . $post_img) : URL::to('storage/profile-avatar.jpg') ?>" />
                                    <div class="w-full">
                                        <?php $sendername = App\Models\TblChat::getSender($sender); ?>
                                        <h3 class="text-base"><b><?php echo $sendername; ?></b>
                                            <?php $unread_count = App\Models\TblChat::getUnreadCount(auth()->user()->id, $sender, $chatlist->post_id); ?>
                                            <span class="bg-green-400 px-2 py-1 ml-2 rounded-full text-sm" data-ids="<?php echo $chatlist->post_id . '.' . $chatlist->receiver; ?>">
                                                <?php echo $unread_count; ?>
                                            </span>
                                            <input type="hidden" class="<?php echo $chatlist->receiver; ?>_unread" value="<?php echo $unread_count; ?>">
                                            <input type="hidden" class="<?php echo $chatlist->receiver; ?>_status" value="<?php echo !empty($selected) ? "online" : "offline"; ?>">
                                        </h3>
                                        <p class="text-sm">{{$chatlist->post_name}}</p>
                                        <p class="flex text-xs" data-id="<?php echo $chatlist->post_id . '.' . $chatlist->receiver ?>">
                                            <?php if ($lastchat['read_status'] == 0) { ?>
                                                <span class="mt-2 mr-2"><img class="w-3" src="<?php echo URL::to('storage/tick.png'); ?>" /></span>
                                            <?php } else { ?>
                                                <span class="mt-2 mr-2"><img class="w-3" src="<?php echo URL::to('storage/double-tick.png'); ?>" /></span>
                                            <?php } ?>
                                            <?php
                                            if (!empty($lastchat['msg'])) {
                                                echo $lastchat['msg'];
                                            } else if (!empty($lastchat['attachment'])) {
                                                echo "Image";
                                            };
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                            <button class="focus:outline-none delete_all pr-5" title="Delete Chat" data-id="<?php echo $sender . '@' . $chatlist->post_id; ?>"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    <!--Chat list group by post end -->
                </div>
                <!--RIGHT PORTION -->
                <div class="w-full md:w-8/12 ml-auto mr-auto border-solid border-4 border-light-blue-500" style="background-image:url('<?php echo URL::to('/images/chat-bg.png'); ?>');">
                    <?php if (!empty($details)) {
                        $senders= "";
                    ?>
                        @foreach ($details as $day => $detail)
                        <?php $reciv = $detail[0]->receiver; 
                        $senders = ((auth()->user()->id == $detail[0]->from_id) ? $detail[0]->to_id : $detail[0]->from_id);
                        ?>                        
                        @endforeach
                        <?php
                        $post_deleted = App\Models\TblPost::where('id', request()->p)->whereNull('deleted_at')->pluck('id');
                        
                        ?>
                        
                        <?php $sendername = App\Models\TblChat::getSender(request()->to); ?>
                        <?php $get_post = App\Models\TblPost::where('id', request()->p)->first(); ?>
                        <?php $currency_symbol = App\Models\TblPost::get_post_currency($get_post->currency_id); ?>
                        <?php $post_price = App\Models\TblPost::where('id', request()->p)->pluck('price')->first(); ?>
                        <?php //$post_img = App\Models\TblChat::getPostImg(request()->p); ?>
                        <?php $detail_post_img = App\Models\User::where('id', $senders)->pluck('profile_photo_path')->first(); ?>
                        <?php $check_blocked_user = count($post_deleted) > 0 ? App\Models\TblChat::checkBlocked(request()->to, request()->p) : ""; ?>
                        <?php $get_user_last_seen = count($post_deleted) > 0 ? App\Models\TblChat::GetUserLastSeen(request()->to) : ""; ?>
                        <div class="flex items-center bg-white p-2  mb-2">
                            <img style="border-radius:10px;border:1px solid #e1e1e1;padding:5px;" class="w-16 h-16 mr-3" src="<?php echo !empty($detail_post_img) ? URL::to('storage/' . $detail_post_img) : URL::to('storage/profile-avatar.jpg') ?>" />

                            <div class="w-full">
                                <h1 class="text-xl"><?php echo $sendername; ?>
                                    <?php if (empty($check_blocked_user)) { ?>
                                        <span class="float-right">
                                            <button class="block focus:outline-none">
                                                <p class="focus:outline-none block_user text-base cursor-pointer p-1" data-block-status="<?php echo !empty($check_blocked_user) ? $check_blocked_user : 0 ?>" data-id="<?php echo request()->to . '@' . request()->p; ?>">
                                                    <?php
                                                    if ($check_blocked_user == 0) { ?>
                                                        Block
                                                    <?php } else { ?>
                                                        Unblock
                                                    <?php } ?>
                                                </p>
                                            </button>
                                        </span>
                                    <?php } ?>
                                </h1>
                                <p class="text-sm"><a href="<?php echo URL::to('/'.$get_post->slug);?>" target="_blank" class="text-sm text-blue-700 font-bold underline">{{$get_post->title}}</a></p>
                                <?php if (empty($check_blocked_user)) { ?>
                                    <span class="text-xs lastseen"><?php echo $get_user_last_seen; ?></span>
                                <?php } else { ?>
                                    <span class="text-xs text-red-600">Post has been deleted by seller.!</span>
                                <?php } ?>
                            </div>
                        </div>
                        <div id="messages_area" class="overflow-auto" style="height:358px;" data-selected-ids="<?php echo ($check_blocked_user == 0) ? $reciv . "." . request()->p : "" ?>">
                            <?php $update_online_status = App\Models\TblChat::UpdateUserStatus(auth()->user()->id, "online"); ?>
                            @foreach ($details as $date => $detail)
                            <?php $grouped_date = App\Models\TblChat::checkChatDate($date); ?>
                            <h5 class="text-center text-xs text-gray-600 w-full float-left mt-4 mb-4"><b><?php echo empty($grouped_date) ? date('d-m-Y', strtotime($date)) : $grouped_date; ?></b></h5>

                            @foreach ($detail as $detail)
                            <?php
                            ($detail->from_id == auth()->user()->id) ? $class = "text-right mr-3" : $class = "text-left ml-3";
                            ($detail->from_id == auth()->user()->id) ? $text_class = "float-right" : $text_class = "float-left";
                            if ($detail->make_offer == 1) {
                                ($detail->from_id == auth()->user()->id) ? $bg_class = "bg-yellow-500" : $bg_class = "bg-yellow-500";
                            } else {
                                ($detail->from_id == auth()->user()->id) ? $bg_class = "bg-indigo-400" : $bg_class = "bg-white";
                            }

                            if ($detail->read_status == 0) {
                                $tick = '<img class="w-3 h-3 inline" src="' . URL::to("storage/tick.png") . '"/>';
                            } else {
                                $tick = '<img class="w-3 h-3 inline" src="' . URL::to("storage/double-tick.png") . '"/>';
                            }
                            $time = date('h:i a', strtotime($detail->created_at));
                            ?>

                            <?php if ($detail->attachment != "") { ?>
                                <div class="mb-2 w-full">
                                    <div class="w-full <?php echo $text_class; ?>">
                                        <img class="w-24 h-24 rounded-md <?php echo $text_class; ?>" src="<?php echo URL::to('storage/' . $detail->attachment); ?>" />
                                    </div>
                                    <p class="mb-2 <?php echo $text_class; ?>"><span class="inline"><span class="time"><?php echo $time; ?></span><?php echo $tick; ?></span></p>
                                </div>
                            <?php } ?>
                            <?php if ($detail->msg != "") { ?>
                                <div class="mb-2 w-full <?php echo $text_class; ?>">
                                    <?php if ($detail->make_offer == 1) { ?>
                                        <?php
                                        if ($detail->from_id == auth()->user()->id) {
                                            $make_offer_msg = "Your Offer " . $currency_symbol[0];
                                            $maker_off_class = "float-right mr-3";
                                            $maker_icon_class = "float-right mr-4 text-right";
                                        } else {
                                            if ($detail->to_id == $detail->receiver) {
                                                $make_offer_msg = "Seller Offer " . $currency_symbol[0];
                                                $maker_off_class = "float-right mr-3";
                                                $maker_icon_class = "float-right mr-4 text-right";
                                            } else {
                                                $make_offer_msg = "Buyer Offer " . $currency_symbol[0];
                                                $maker_off_class = "float-left ml-3";
                                                $maker_icon_class = "float-left ml-4 text-left";
                                            }
                                        }
                                        ?>
                                        <p class="mb-3 <?php echo $class; ?>"><span class="p-2 <?php echo $bg_class; ?>"><?php echo $make_offer_msg . $detail->msg; ?></span><span class="inline p-2 <?php echo $bg_class ?>"><span class="time"><?php echo $time; ?></span><?php echo $tick; ?></span></p>
                                    <?php } else { ?>
                                        <p class="mb-3 <?php echo $class; ?>"><span class="p-2 <?php echo $bg_class; ?>"><?php echo $detail->msg; ?></span><span class="inline p-2 <?php echo $bg_class ?>"><span class="time"><?php echo $time; ?></span><?php echo $tick; ?></span></p>
                                    <?php } ?>

                                    <?php if (!empty($get_make_offer->id) && ($get_make_offer->id >= $last_chat_id) && ($get_make_offer->id == $detail->id)) { ?>
                                        <?php if (($get_make_offer->from_id == auth()->user()->id) || ($get_make_offer->from_id == $detail->receiver)) { ?>
                                            <i class="fa fa-sort-desc w-full <?php echo $maker_icon_class; ?> -mt-6 z-40 relative text-3xl text-yellow-500" aria-hidden="true"></i>
                                            <?php $offer_price = $detail->msg; ?>
                                            <a class="<?php echo $maker_off_class; ?> border-gray-600 border rounded-full p-1 text-xs -mt-0 edit-offer" href="#" data-defcurency="<?php echo $currency_symbol[0]; ?>" data-youroffer="<?php echo $offer_price; ?>">Edit Offer</a>
                                    <?php }
                                    } ?>
                                </div>
                            <?php } ?>
                            <?php if ($detail->location != "") { ?>
                                <?php
                                $lat = $detail->latitude;
                                $long = $detail->longitude;
                                ?>
                                <div class="mb-2 w-full">
                                    <div class="w-full <?php echo $text_class; ?>">
                                        <div class="p-4 mb-4">
                                            <a class="viewShared <?php echo $text_class; ?>" href="https://www.google.com/maps?daddr=<?php echo $lat ?>,<?php echo $long ?>" target="_blank">
                                                <img src="https://maps.googleapis.com/maps/api/staticmap?center=<?php echo $lat; ?>,<?php echo $long; ?>&amp;zoom=16&amp;size=400x200&amp;sensor=false&amp;maptype=roadmap&amp;markers=color:red%7Clabel:S%7C<?php echo $lat; ?>,<?php echo $long; ?>&amp;key=AIzaSyA_gSzPNC40ioDPHIb7kMkYFClDihdhhx4" style="width:400px;height:200px;"></a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            @endforeach
                            @endforeach
                        </div>
                        <?php if ($check_blocked_user == 0 && count($post_deleted) > 0) { ?>
                            <div class="image_preview" style="display: none;">
                                <img src="" id="preview" />
                                <button class="text-red-700 remove-img">
                                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="w-full p-2">
                                <div class="text-center">
                                    <h1 class="bg-white pl-4 pr-4 text-2xl rounded-t-lg show-hide-redymade-chat w-16 text-center m-auto cursor-pointer"><i class="fa fa-angle-down" aria-hidden="true"></i></h1>
                                </div>
                                <div class="rounded w-full bg-white">
                                    <!-- Tabs -->
                                    <ul id="tabs" class="inline-flex w-full border-b">
                                        <li class="px-4 w-1/2 text-center text-gray-800 font-semibold py-2 border-b -mb-px bg-white rounded-t-lg"><a id="default-tab" href="#first">QUESTIONS</a></li>
                                        <li class="px-4 w-1/2 text-center text-gray-800 font-semibold py-2 border-b -mb-px bg-white rounded-t-lg"><a href="#make-offer-chat">MAKE OFFERS</a></li>
                                    </ul>
                                    <!-- Tab Contents -->
                                    <div id="tab-contents">
                                        <div id="first" class="p-4">
                                            <div class="max-w-3xl mx-auto">
                                                <div class="sm:flex items-center rounded p-2 bg-gray-200">
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
                                            <ul class="list-unstyled inline-flex defalut-chat">
                                                <li data-defmsg="hello">Hello</li>
                                                <li data-defmsg="is it available?">Is it available?</li>
                                                <li data-defmsg="okay">Okay</li>
                                                <li data-defmsg="no problem">No Problem</li>
                                                <li data-defmsg="please reply">Please reply</li>
                                                <li data-defmsg="not interested">Not interested</li>
                                            </ul>
                                        </div>
                                        <div id="make-offer-chat" class="hidden p-4">
                                            <ul class="list-unstyled inline-flex">
                                                <li class="rounded-full border pl-4 pr-4 pt-2 pb-2 price-active" data-defcurncy="<?php echo $currency_symbol[0]; ?>" data-defmsg="<?php echo floor($post_price); ?>"><?php echo $currency_symbol[0] . floor($post_price); ?></li>
                                                <li class="rounded-full border pl-4 pr-4 pt-2 pb-2" data-defcurncy="<?php echo $currency_symbol[0]; ?>" data-defmsg="<?php echo $post_price - round($post_price * 5 / 100); ?>"><?php echo $currency_symbol[0] . $post_price - round($post_price * 5 / 100); ?></li>
                                                <li class="rounded-full border pl-4 pr-4 pt-2 pb-2" data-defcurncy="<?php echo $currency_symbol[0]; ?>" data-defmsg="<?php echo $post_price - round($post_price * 10 / 100); ?>"><?php echo $currency_symbol[0] . $post_price - round($post_price * 10 / 100); ?></li>
                                                <li class="rounded-full border pl-4 pr-4 pt-2 pb-2" data-defcurncy="<?php echo $currency_symbol[0]; ?>" data-defmsg="<?php echo $post_price - round($post_price * 15 / 100); ?>"><?php echo $currency_symbol[0] . $post_price - round($post_price * 15 / 100); ?></li>
                                                <li class="rounded-full border pl-4 pr-4 pt-2 pb-2" data-defcurncy="<?php echo $currency_symbol[0]; ?>" data-defmsg="<?php echo $post_price - round($post_price * 20 / 100); ?>"><?php echo $currency_symbol[0] . $post_price - round($post_price * 20 / 100); ?></li>
                                            </ul>
                                            <div class="make-offer-design">
                                                <input type="hidden" class="offer-price" value="<?php echo floor($post_price); ?>" />
                                                <p class="offer-price"><?php echo $currency_symbol[0] . floor($post_price); ?></p>
                                                <i class="fa fa-caret-up" aria-hidden="true"></i>
                                                <div class="flex items-center bg-indigo-200 w-3/6 p-3 rounded">
                                                    <i class="fa fa-hand-o-up ml-4 mr-4" aria-hidden="true"></i>
                                                    <div clas="inline-flex items-center">
                                                        <h3 class="text-md text-gray-900">Very good offer!</h3>
                                                        <p class="text-sm text-gray-700">High chances of seller's reply.</p>
                                                    </div>
                                                    <button type="submit" name="send" id="send" class="send bg-indigo-500 text-white active:bg-indigo-500 font-bold text-xs px-6 py-3 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 ml-10">Send</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <form method="post" id="chat_form" name="chatform" onsubmit="return validateForm()" class="w-full" enctype="multipart/form-data">
                                    <input type="hidden" name="post_id" class="post_id" value="<?php echo request()->p; ?>" />
                                    <input type="hidden" name="to" class="to" value="<?php echo request()->to; ?>" />
                                    <input type="hidden" class="receiver_id" value="<?php echo $reciv; ?>" />
                                    <div class="chat-box flex">
                                        <label class="attachments">
                                            <i class="fa fa-picture-o" aria-hidden="true"></i>
                                            <input type="file" accept="image/*" name="image" id="image" class="attachment" />
                                        </label>
                                        <input type="hidden" id="file" name="filename">
                                        <input type="text" id="chat_message" name="chat_message" placeholder="Type Message Here" class="msg w-full px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline ease-linear transition-all duration-150 w-4/5" />
                                        <button type="submit" name="send" id="send" class="send bg-indigo-500 text-white active:bg-indigo-500 font-bold text-xs px-6 py-3 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                                        <button type="button" id="share_loc_btn" class="send bg-indigo-500 text-white active:bg-indigo-500 font-bold text-lg px-6 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 ml-2"><i class="fa fa-map-marker" aria-hidden="true"></i></button>
                                    </div>
                                </form>
                            </div>
                        <?php } ?>

                    <?php } else { ?>
                        <?php $update_online_status = App\Models\TblChat::UpdateUserStatus(auth()->user()->id, "offline"); ?>
                        <img class="m-auto w-40 mt-20" src="<?php echo URL::to('storage/emptyChat.png'); ?>" />
                        <p class="text-center mt-2"><?php echo __('messages.Select a chat to view conversation'); ?></p>
                    <?php } ?>
                </div>
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
    </section>
</div>
<?php
if (Session::has("CurrLoggedLat")) {
    $current_lat = Session::get("CurrLoggedLat");
    $currenct_long = Session::get("CurrLoggedLng");
} else {
    $current_lat = "-33.8688";
    $currenct_long = "151.2195";
}
?>
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

    $(window).on('load', function() {
        var elem = document.getElementById('messages_area');
        elem.scrollTop = elem.scrollHeight;
    });

    // share location start
    $('#share_loc_btn').on('click', function(e) {
        document.querySelector("#share_loc_popup").style.display = "block";
    });

    $('#share_loc_cancel').on('click', function(e) {
        document.querySelector("#share_loc_popup").style.display = "none";
    });
    // share location end

    function validateForm() {
        var chat_message = document.forms["chatform"]["chat_message"].value;
        var imgname = document.forms["chatform"]["filename"].value;
        if ((chat_message == "") && (imgname == "")) {
            toastr.warning("Please type any message!");
            return false;
        }
    }


    $(document).ready(function() {
        var rand_str = "<?php echo date("Hms"); ?>";
        //  var conn = new WebSocket("ws://139.162.207.47:8090");
        //var conn = new WebSocket("wss://157.51.62.196:8443");
        //var conn = new WebSocket("wss://139.162.207.47:8443");
        //var conn = new WebSocket("wss://139.162.207.47:8443");
        //var conn = new WebSocket("wss://products.sharesoft.in/letgo/wss/NNN");
        var conn = new WebSocket('wss://products.sharesoft.in/wss/NNN');





        //call in specific interval
        var sayHi = function() {
            conn.send('hi');
            console.log("call me");
        }
        //sayHi();

        var timerID = 0;
        var keepAlive = function() {
            conn = new WebSocket('wss://products.sharesoft.in/wss2');
            var timeout = 10000;
            if (conn.readyState == conn.OPEN) {
                conn.send('');

            }
            console.log("Re-connect");
            timerId = setTimeout(keepAlive, timeout);
        }
        var cancelKeepAlive = function() {
            if (timerId) {
                console.log("close conn")
                clearTimeout(timerId);
            }
        }
        //call in specific interval

        //console.log('hi'+conn.OPEN);
        //var conn = new WebSocket("wss://products.sharesoft.in/letgo/", "443");
        conn.onopen = function(e) {
            //keepAlive(conn);
            //setInterval(sayHi, 5000);
            //conn.send('Ping');
            console.log("WebSocket Connection established!");
        };

        conn.onmessage = function(e) {
            //console.log('Server: ' + e.data);
            var data = JSON.parse(e.data);
            //console.log('message received\n'+data);
            var row_class = '';
            var text_class = '';
            var bg_class = '';
            var user_status = $("." + data.receiveid + "_status").val();
            var read_status = 0;
            if (data.from == "Me") {
                if (data.make_offer != "") {
                    bg_class = 'bg-yellow-500';
                } else {
                    bg_class = 'bg-indigo-400';
                }
                row_class = 'text-right mr-3';
                text_class = 'float-right';
            } else {
                row_class = 'text-left ml-3';
                text_class = 'float-left';
                bg_class = 'bg-white';
                if (user_status == "offline") {
                    var unread_cnt = 1 + parseInt($("." + data.receiveid + "_unread").val());
                    var find_unread_update = data.post_id + '.' + data.receiveid;
                    $('[data-ids="' + find_unread_update + '"]').html(unread_cnt);
                    $("." + data.receiveid + "_unread").val(unread_cnt);
                }
            }
            if (user_status == "offline") {
                read_status = 0;
            } else {
                read_status = 1;
            }
            if (read_status == 1) {
                var tick = "<img class='w-3 h-3 inline' src='<?php echo URL::to('storage/'); ?>/double-tick.png'>";
            } else {
                var tick = "<img class='w-3 h-3 inline' src='<?php echo URL::to('storage/'); ?>/tick.png'>";
            }
            var current_date = "<?php echo date('h:i a'); ?>";
            if (data.image != "") {
                var html_data = "<div class='mb-2 w-full'><div class='w-full " + text_class + "'><img class='w-24 h-24 rounded-md " + text_class + "' src='" + data.image + "'/></div><p class='mb-2 " + text_class + "'><span class='inline'><span class='time'>" + current_date + "</span>" + tick + "</span></p></div>";
            } else {
                var html_data = "";
            }
            if (data.msg != "") {
                if (data.make_offer != "") {
                    var html_datas = "<div class='mb-2 w-full " + text_class + "'><p class='mb-3 " + row_class + "'><span class='p-2 " + bg_class + "'>" + data.msg + "</span><span class='inline p-2 " + bg_class + "'><span class='time'>" + current_date + "</span>" + tick + "</span></p></div><a href='#'>Edit Offer</a>";
                } else {
                    var html_datas = "<div class='mb-2 w-full " + text_class + "'><p class='mb-3 " + row_class + "'><span class='p-2 " + bg_class + "'>" + data.msg + "</span><span class='inline p-2 " + bg_class + "'><span class='time'>" + current_date + "</span>" + tick + "</span></p></div>";
                }
            } else {
                var html_datas = "";
            }
            var selected_ids = data.receiveid + '.' + data.post_id;
            $('[data-selected-ids="' + selected_ids + '"]').append(html_data + html_datas);
            $("#chat_message").val("");
            if (data.msg != "") {
                var last_chat_html = '<span class="mt-2 mr-2">' + tick + '</span>' + data.msg;
            } else if (data.image != "") {
                var last_chat_html = '<span class="mt-2 mr-2">' + tick + '</span> Image';
            }
            var lastchat = data.post_id + '.' + data.receiveid;
            $('[data-id="' + lastchat + '"]').html(last_chat_html);
            var elem = document.getElementById('messages_area');
            elem.scrollTop = elem.scrollHeight;
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


        $('#image').change(function(e) {
            var filename = e.target.files[0].name;
            $("#file").val(filename);
            var reader = new FileReader();
            $(".image_preview").css("display", "block");
            reader.onload = function(e) {
                document.getElementById("preview").src = e.target.result;
            };
            // read the image file as a data URL.
            reader.readAsDataURL(this.files[0]);
        });
        $('#chat_form').on('submit', function(event) {
            event.preventDefault();
            var to = $(".to").val();
            var post_id = $(".post_id").val();
            var message = $('#chat_message').val();
            var imgnaam = $('#file').val();
            var reciv_id = $(".receiver_id").val();
            if (imgnaam != "") {
                var img_base64 = document.getElementById("preview").src;
            } else {
                var img_base64 = "";
            }
            if ((message != "") || (imgnaam != "")) {
                var formData = new FormData(this);
                var data = {
                    from: "<?php echo auth()->user()->id; ?>",
                    to: to,
                    msg: message,
                    post_id: post_id,
                    image: img_base64,
                    receiveid: reciv_id,
                    make_offer: "",
                };
                send_chat(formData);
                conn.send(JSON.stringify(data));
            }
        });
        $(".defalut-chat li").on('click', function(e) {
            e.preventDefault();
            var message = $(this).attr('data-defmsg');
            var to = $(".to").val();
            var post_id = $(".post_id").val();
            var reciv_id = $(".receiver_id").val();
            var make_offer = "";
            var data = {
                from: "<?php echo auth()->user()->id; ?>",
                to: to,
                msg: message,
                post_id: post_id,
                receiveid: reciv_id,
                make_offer: ""
            };
            send_default_chat(message, to, post_id, make_offer);
            conn.send(JSON.stringify(data));
        });
        $(".make-offer-design #send").on('click', function(e) {
            e.preventDefault();
            var message = $(".make-offer-design input.offer-price").val();
            var to = $(".to").val();
            var post_id = $(".post_id").val();
            var reciv_id = $(".receiver_id").val();
            var make_offer = "yes";
            var data = {
                from: "<?php echo auth()->user()->id; ?>",
                to: to,
                msg: message,
                post_id: post_id,
                receiveid: reciv_id,
                make_offer: "yes"
            };
            send_default_chat(message, to, post_id, make_offer);
            conn.send(JSON.stringify(data));
        });
    });

    $("#make-offer-chat ul li").on('click', function(e) {
        e.preventDefault();
        var get_val = $(this).attr('data-defmsg');
        var get_currency = $(this).attr("data-defcurncy");
        $("#make-offer-chat ul li").removeClass("price-active");
        $(".make-offer-design p.offer-price").text(get_currency + get_val); // curency symbol need to change
        $(".make-offer-design input.offer-price").val(get_val);
        $(this).addClass("price-active");
    });

    $("#tab-contents").hide();
    $(".chat-box").show();
    $(document).ready(function() {
        $(".show-hide-redymade-chat").click(function() {
            $(".chat-box").slideToggle("slow");
            $("#tab-contents").slideToggle("slow");
        });
    });

    $(".edit-offer").on('click', function(e) {
        e.preventDefault();
        var offered_price = $(this).attr('data-youroffer');
        var get_curency = $(this).attr("data-defcurency");
        $(".chat-box").slideToggle("slow");
        $("#tab-contents").slideToggle("slow");
        $("#tabs li").removeClass("tab-active");
        $("#tabs li:nth-child(2)").addClass("tab-active");
        $("#tab-contents #first").removeClass('hidden');
        $("#tab-contents #make-offer-chat").removeClass('hidden');
        $("#tab-contents #first").addClass('hidden');
        $("#make-offer-chat ul li").removeClass('price-active');
        $('#make-offer-chat ul li').each(function() {
            var check_price_val = $(this).attr('data-defmsg');
            if (check_price_val === offered_price) {
                $(this).addClass('price-active');
                $(".make-offer-design p.offer-price").text(get_curency + offered_price);
                $(".make-offer-design input.offer-price").val(offered_price);
            }
        });
    });


    //Remove the old image 
    $('.remove-img').on('click', function() {
        $(".image_preview").css('display', 'none');
        document.getElementById("preview").src = "";
        $("#image").val("");
        $("#file").val("");
    });


    function send_default_chat(message, to, post_id, make_offer) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "{{ URL::to('send_chat') }}",
            data: {
                chat_message: message,
                to: to,
                post_id: post_id,
                image: "",
                make_offer: make_offer
            },
            success: function(data) {
                var elem = document.getElementById('messages_area');
                elem.scrollTop = elem.scrollHeight;
                $(".image_preview").css('display', 'none');
                document.getElementById("preview").src = "";
                $("#image").val("");
                $("#file").val("");
            }
        });
    }

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
                $(".image_preview").css('display', 'none');
                document.getElementById("preview").src = "";
                $("#image").val("");
                $("#file").val("");
            }
        });
    }
    /* delete all chat for particular user*/
    $(document).ready(function() {
        $(".delete_all").click(function(event) {
            var ids = $(this).attr('data-id');
            if (confirm('Are you sure to delete?')) {
                delete_chat(ids, 'delete');
            } else {
                event.preventDefault();
            }
        });
    });
    /* Block User*/
    $(document).ready(function() {
        $(".block_user").click(function(event) {
            alert("sdsd");
            var ids = $(this).attr('data-id');
            var msg = ($(this).attr('data-block-status') == 0) ? " to Block the user?" : " to Unblock the user?";
            if (confirm('Are you sure you want' + msg)) {
                delete_chat(ids, 'block');
            } else {
                event.preventDefault();
            }
        });
    });

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
    $(document).ready(function() {
        var is_param = searchParams('type');
        if (is_param == true) {
            // setInterval(fetch_user_status, 10000);
        }
    });

    function searchParams(sParam) {
        var field = sParam;
        var url = window.location.href;
        if (url.indexOf('&' + field + '=') != -1)
            return true;
        return false
    }


    function fetch_user_status() {
        var classname = '.' + $(".receiver_id").val() + '_status';
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "{{ URL::to('fetch_user_status') }}",
            data: {
                id: $(".to").val()
            },
            cache: false,
            async: false,
            success: function(data) {
                $(classname).val(data.value);
                if (data.value == "offline") {
                    $('.lastseen').text(data.message);
                } else {
                    $('.lastseen').text("Online");
                }
            }
        });
    }

    /* search */
    $(".search").click(function() {
        $(".search_div").css("display", "block");
        $(".heading_div").css("display", "none");
    });
    $(".close").click(function() {
        $(".search_div").css("display", "none");
        $(".heading_div").css("display", "block");
    });
    /* block user */
    $(".block").click(function() {
        $(".block_div").toggle();
    });


    let tabsContainer = document.querySelector("#tabs");
    let tabTogglers = tabsContainer.querySelectorAll("a");
    tabTogglers.forEach(function(toggler) {
        toggler.addEventListener("click", function(e) {
            e.preventDefault();
            let tabName = this.getAttribute("href");
            let tabContents = document.querySelector("#tab-contents");
            for (let i = 0; i < tabContents.children.length; i++) {
                tabTogglers[i].parentElement.classList.remove("tab-active");
                tabContents.children[i].classList.remove("hidden");
                if ("#" + tabContents.children[i].id === tabName) {
                    continue;
                }
                tabContents.children[i].classList.add("hidden");
            }
            e.target.parentElement.classList.add("tab-active");
        });
    });
    document.getElementById("default-tab").click();
</script>