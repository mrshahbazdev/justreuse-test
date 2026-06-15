<?php
	$dir_rtl =  App\Models\Setting::is_dir_rtl();
	$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
		
	$class_dir_user_cl_img = ($dir_rtl=="true")?'float-right ml-4 md:ml-2 lg:ml-4':'float-left mr-4 md:mr-2 lg:mr-4';
	$class_dir_ud_padd_lr = ($dir_rtl=="true")?'pl-8 md:pl-14 lg:pl-8':'pr-8 md:pr-14 lg:pr-8';
	$class_dir_mr_lr = ($dir_rtl=="true")?'mr-2':'ml-2';
	$class_dir_mr_rl = ($dir_rtl=="true")?'ml-2':'mr-2';
	$class_dir_rl = ($dir_rtl=="true")?'left':'right';

?>

@foreach($chatlists as $chatlist)
        <?php
         $visible_posts = App\Models\TblPost::check_payment_pack_expired($chatlist->post_id);
         if(!empty($visible_posts)) {

        $sender = ((auth()->user()->id == $chatlist->from_id) ? $chatlist->to_id : $chatlist->from_id);
        $chaturl = URL::to('/chatting') . '?to=' . $sender . '&p=' . $chatlist->post_id . '&type=old';
		
		
        ?>
        <?php $lastchat = App\Models\TblChat::getLastChat($sender, $chatlist->post_id); ?>
        <?php
        $post_deleted = App\Models\TblPost::where('id', $chatlist->post_id)->whereNull('deleted_at')->pluck('id');
        $deleted_class = count($post_deleted) > 0 ? "" : "bg-red-300";
        ?>
        <?php
        if ((request()->to == $sender) && (request()->p == $chatlist->post_id)) {
            $selected = "bg-gray-100";
        } else {
            $selected = "bg-white";
        }

        $post_img = App\Models\User::where('id', $sender)->pluck('profile_photo_path')->first();
        $sendername = App\Models\TblChat::getSender($sender);
        ?>
						
            <div class="w-full px-3 bg-white float-left user_chats lg:px-5 xl:px-8 py-4 border-b relative <?php echo !empty($deleted_class) ? $deleted_class : $selected; ?>">
            <a href="<?php echo $chaturl; ?>" class="chat_lists w-full inline-block">
            <div class="user_image w-16 sm:w-20 md:w-14 xl:w-20 {{$class_dir_user_cl_img}} mt-4 lg:mt-4 xl:mt-3">
                <img class="w-16 h-16 sm:w-20 sm:h-20 md:w-14 md:h-14 xl:w-20 xl:h-20 float-left rounded-full object-cover" src="<?php echo !empty($post_img) ? URL::to('storage/' . $post_img) : URL::to('storage/profile-avatar.jpg') ?>" alt="user.jpg">
            </div>
            <div class="user_detail pt-3 {{$class_dir_ud_padd_lr}}">
			<p class="">
                <span class="font-bold text-base md:text-sm lg:text-lg pb-1 inline-block capitalize align-middle chats_user_name truncate"><?php echo $sendername; ?>
                <?php $unread_count = App\Models\TblChat::getUnreadCount(auth()->user()->id, $sender, $chatlist->post_id); ?>
                    <input type="hidden" class="<?php echo $chatlist->receiver; ?>_unread" value="<?php echo $unread_count; ?>">
                    <input type="hidden" class="<?php echo $chatlist->receiver; ?>_status" value="<?php echo !empty($selected) ? "online" : "offline"; ?>">
				</span>
				<span class="rounded-full bg-green-500 text-white text-sm font-semibold h-5 w-5 inline-block text-center" data-ids="<?php echo $chatlist->post_id . '.' . $chatlist->receiver; ?>">
						<?php echo $unread_count; ?>
                    </span>
			</p>
            <?php  $getLastTime = App\Models\TblChat::getTimeZoneUser($lastchat['created_at']);
           //dd($getLastTime);
           	$last_time = date('h:i a', strtotime($getLastTime['converted_datetime']));?>
			<time class="text-xs text-gray-500 absolute top-8 {{$class_dir_rl}}-5">{{$last_time}}</time>
            <button class="delete_all focus:outline-none bg-transparent absolute top-1/2 {{$class_dir_rl}}-3 px-2.5 py-2 text-red-500 text-xl font-bold" title="Delete Chat" data-id="<?php echo $sender . '@' . $chatlist->post_id; ?>"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                <p class="text-sm text-gray-500"><?php echo mb_strimwidth($chatlist->post_name, 0, 12, "...");?></p>
                <p class="text-sm text-black" data-id="<?php echo $chatlist->post_id . '.' . $chatlist->receiver ?>">

                <?php
                    $tick ="";
                    if($lastchat['from_id']== auth()->user()->id){
                        if ($lastchat['read_status'] == 0) {
                        
						$tick = '<span class="mt-2 '.$class_dir_mr_rl.'"><img class="w-3 h-3 inline" src="' . URL::to("storage/tick.png") . '"/></span>';
                        } else {
                        $tick = '<span class="mt-2 '.$class_dir_mr_rl.'"><img class="w-3 h-3 inline" src="' . URL::to("storage/double-tick.png") . '"/></span>';
                        }
                    }
                    echo $tick;
                    ?>
                    <span class="mt-2 {{$class_dir_mr_rl}} inline-block">
                    <?php
                    
                    if (!empty($lastchat['msg'])) {
                        echo mb_strimwidth($lastchat['msg'], 0, 12, "...");//$lastchat['msg'];
                    } else if (!empty($lastchat['attachment'])) {
                        echo "Image";
                    }else{ echo "Location";}
                    ?>
                    </span>
                </p>


                
            </div>
            </a>
            </div>
            <?php } ?>
    
@endforeach