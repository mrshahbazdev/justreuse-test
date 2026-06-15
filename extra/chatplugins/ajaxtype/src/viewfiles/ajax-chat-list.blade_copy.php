@extends('layouts.frontendother')
	@section('content')
	<div class="root-element-div">
		<link href="{{ URL::to('css/chat.css') }}" rel="stylesheet"/>

	<?php



		$get_meta = App\Models\TblOtherpage::get_meta('user-chats');
		$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
		$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
		$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
		
		// only chat list blade
		$dir_rtl =  App\Models\Setting::is_dir_rtl();
		$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
		
		$class_dir_cl_padd_lr = ($dir_rtl=="true")?'pr-0 md:pr-3 lg:pr-6':'pl-0 md:pl-3 lg:pl-6';
		$class_dir_user_conv_img = ($dir_rtl=="true")?'float-right ml-2 lg:ml-4':'float-left mr-2 lg:mr-4';
		$class_dir_pl_lr = ($dir_rtl=="true")?'pr-6':'pl-6';
		
		
		// chat list blade with reload chat list blade
		
		$class_dir_user_cl_img = ($dir_rtl=="true")?'float-right ml-4 md:ml-2 lg:ml-4':'float-left mr-4 md:mr-2 lg:mr-4';
		$class_dir_ud_padd_lr = ($dir_rtl=="true")?'pl-8 md:pl-14 lg:pl-8':'pr-8 md:pr-14 lg:pr-8';
		$class_dir_rl = ($dir_rtl=="true")?'left':'right';
		
		
		
		// chat list blade with reload conversation list blade
		$class_dir_float_lr = ($dir_rtl=="true")?'float-right':'float-left';
		$class_dir_float_rl = ($dir_rtl=="true")?'float-left':'float-right';
		$class_dir_text_rl = ($dir_rtl=="true")?'text-left':'text-right';
		
		
		
		// Common
		$class_dir_lr = ($dir_rtl=="true")?'right':'left';
		$class_dir_mr_lr = ($dir_rtl=="true")?'mr-2':'ml-2';
		$class_dir_mr_rl = ($dir_rtl=="true")?'ml-2':'mr-2';
		
		
		
	?>

	@if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description))
        @section('meta_title', $meta_title)
        @section('meta_keywords', $meta_keywords)
        @section('meta_description', $meta_description)
	@endif
		
		<div class="w-full float-left" {{$class_dir}}>
			<div class="w-full float-left">
				<div class="container mx-auto px-4">
					<h4 class="text-xl md:text-2xl font-bold text-black my-4 sm:my-6 lg:my-8 uppercase">{{__('messages.chats')}}</h4>
				</div>
			</div>
		
			<div class="w-full float-left">
				<div class="container mx-auto px-4">
					<div class="w-full block bg-gray-100 float-left border rounded-lg p-0 md:p-4 mb-5 md:flex justify-center">
					
						<!--left-chat-->
						<div class="user_chat_button md:hidden mb-4 bg-white">
							<button class="bg-green-500 rounded-lg text-white border-2 border-green-500 px-6 py-2 pb-3">{{__('messages.user chats')}}</button>
						</div>
						
						<div class="user_chat_block hidden md:block w-full md:w-5/12 lg:w-2/5 bg-gray-50 float-left py-4 pb-0 rounded-lg overflow-auto mb-8 md:mb-0">
						
							@if($dir_rtl =="false")
							<div class="relative max-w-md mx-auto mb-2 px-2">
								<input class="search_text w-full bg-gray-50 pl-12 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 p-3 " type="text" placeholder="{{__('messages.search by product name')}}" name="search2">
								<label class=" absolute top-3 left-5 text-gray-500 font-medium"><i class="fa fa-search" aria-hidden="true"></i></span></label>
							</div>
							@else
							<div class="relative max-w-md mx-auto mb-2 px-2">
								<input class="search_text w-full bg-gray-50 pr-12 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 p-3 " type="text" placeholder="{{__('messages.search by product name')}}" name="search2">
								<label class=" absolute top-3 right-5 text-gray-500 font-medium"><i class="fa fa-search" aria-hidden="true"></i></span></label>
							</div>
							@endif
							
							<div class="w-full chats rounded-lg float-left cursor-pointer " id="chat_list_area">

							
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
												<span class="font-bold text-base md:text-sm lg:text-lg pb-1 inline-block capitalize align-middle chats_user_name truncate">
												<?php echo $sendername; ?>
													<?php $unread_count = App\Models\TblChat::getUnreadCount(auth()->user()->id, $sender, $chatlist->post_id); ?>
													<input type="hidden" class="<?php echo $chatlist->receiver; ?>_unread" value="<?php echo $unread_count; ?>">
													<input type="hidden" class="<?php echo $chatlist->receiver; ?>_status" value="<?php echo !empty($selected) ? "online" : "offline"; ?>">
												</span>
												<span class="rounded-full bg-green-500 text-white text-sm font-semibold h-5 w-5 inline-block text-center" data-ids="<?php echo $chatlist->post_id . '.' . $chatlist->receiver; ?>">
													<?php echo $unread_count; ?>
												</span>
											</p>
											<?php 
											$getLastTime = App\Models\TblChat::getTimeZoneUser($lastchat['created_at']);
											
											$last_time = date('h:i a', strtotime($getLastTime['converted_datetime']));
											// \Carbon\Carbon::parse($getCreateTime['converted_datetime'])->format('h:i a');
											$last_time = \Carbon\Carbon::parse($getLastTime['converted_datetime'])->format('h:i a'); //date('h:i a', strtotime($getLastTime['converted_datetime']));
											?>
											
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

							</div>
						</div>
						
						
						<!--right-chat-->   
						<div class="md:block w-full {{$class_dir_cl_padd_lr}} md:w-7/12 lg:w-3/5 float-right chat_detail cursor-pointer" style="background-image:url('<?php echo URL::to('/images/chat-bg.png'); ?>');">

							<?php if (!empty($details)) {
								$senders= "";
							?>
							@foreach ($details as $day => $detail)
							<?php $reciv = $detail[0]->receiver; 
								$senders = ((auth()->user()->id == $detail[0]->from_id) ? $detail[0]->to_id : $detail[0]->from_id);

								$check_user_blocked = count($post_deleted) > 0 ? App\Models\TblChat::checkUserBlocked($detail[0]->from_id, request()->p,request()->to) : "";
							
							
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

							<?php ?>
							<?php //$get_user_last_seen = count($post_deleted) > 0 ? App\Models\TblChat::GetUserLastSeen(request()->to) : ""; ?>


							<!--<span class="py-3 px-4 block text-right close_btn block md:hidden ">
							<svg fill="currentColor" viewBox="0 0 20 20" class="inline-block w-6 h-6">
							  
							  <path x-show="open" fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
							</svg>
							</span>-->
							<div class="w-full float-left h-full relative">
								<div class="w-full bg-green-500 rounded-lg p-4 float-left">
									<div class="user_image w-10 lg:w-20 mt-3 {{$class_dir_user_conv_img}} ">
										<img class="w-10 lg:w-20 h-10 {{$class_dir_float_lr}} lg:h-20 rounded-full object-cover" src="<?php echo !empty($detail_post_img) ? URL::to('storage/' . $detail_post_img) : URL::to('storage/profile-avatar.jpg') ?>" alt="user.jpg">
									</div>
									<div class="user_detail pt-1 {{$class_dir_float_lr}} lg:pt-4 w-1/2 xl:w-auto">
										<span class="font-bold text-lg text-white pb-1 inline-block capitalize">
											<?php echo $sendername; ?>
										</span>

										<p class="text-sm"><a href="<?php echo URL::to('/'.$get_post->slug);?>" target="_blank" class="font-semibold text-sm lg:text-base text-white underline pb-1 inline-block">{{$get_post->title}}</a></p>
										<?php if (empty($check_blocked_user)) { ?>
												<span class="text-xs lastseen text-white"></span>
										<?php } ?>
									</div>
									<div class="w-3/12 {{$class_dir_float_rl}} pb-0 pt-2 lg:p-4 lg:pt-6 lg:w-3/12">
										<span class="text-xs text-white {{$class_dir_float_rl}} inline-block align-middle lg:pt-1">
											<?php 
                                                if (!isset($lang_code)) {
                                                 $lang_code = \Config::get('app.locale');
                                                 }
                                                 $trans_block = App\Models\Languages::where('lang_code', $lang_code)->where('lang_org_text', 'block')->value('lang_text');
                                                 $block = !empty($trans_block)?$trans_block:"Block";
                                                 $trans_unblock = App\Models\Languages::where('lang_code', $lang_code)->where('lang_org_text', 'unblock')->value('lang_text');
                                                 $unblock = !empty($trans_unblock)?$trans_unblock:"Unblock";
												$blk = ($check_blocked_user == 1)?$unblock:$block;
												$blk_state = (empty($check_blocked_user) || $check_blocked_user=="0")?"0":"1";
											?>
											<span class="{{$class_dir_float_rl}}">
												<button class="block focus:outline-none rounded-md">
												<p class="focus:outline-none block_user rounded-md text-base cursor-pointer p-1" data-block-status="{{$blk_state}}" data-id="<?php echo request()->to . '@' . request()->p; ?>">{{$blk}}</p>
												</button>
											</span>
										</span>
										<span class="{{$class_dir_float_lr}} inline-block align-middle text-white text-xl lg:text-3xl"><a href="#"><i class="fa fa-map-marker" aria-hidden="true"></i></a>
										</span>   
									</div>
								</div>
							
							
								<div class="w-full bg-white float-left">

									<!--converstion-->
									<div class="w-full chat_converstion p-4 pb-36 md:pb-4" id="messages_area">
										<div class="show_bottom h-full overflow-y-scroll">
											@foreach ($details as $date => $detail)                     
											<?php $grouped_date = App\Models\TblChat::checkChatDate($date);
												$print_day = empty($grouped_date) ? date('d-m-Y', strtotime($date)) : $grouped_date;
												$todayClass = (strtolower($print_day)=="today")?"today":"previous_day";
											?>
											<div class="w-full mesage float-left {{$todayClass}}">
												<div class="w-full text-gray-500 my-3 text-center"><?php echo $print_day; ?></div>
													
												<!--begin chat loop-->
									
												@foreach ($detail as $detail)

												

												<?php
													$direction ="";$div_flow = "";$txt_clr = "";$mar_r = "";
													if($detail->from_id == auth()->user()->id){
														// $div_flow = "float-right";
														$div_flow = ($dir_rtl=="true")?"float-left":"float-right";
														$bg = "bg-gray-100";         
														$corner= "rounded-br-3xl rounded-tr-none";
														// $mar_r = "mr-2";
														$mar_r = ($dir_rtl=="true")?"ml-2":"mr-2";
														$direction = "sender";
														// $text_class = "text-left";
														$text_class = ($dir_rtl=="true")?"text-right":"text-left";
													}
													else{
														// $div_flow = "float-left";
														$div_flow = ($dir_rtl=="true")?"float-right":"float-left";
														$bg = "bg-green-500";            
														$txt_clr = "text-white";
														$corner= "rounded-tr-3xl rounded-br-none";
														$direction = "receiver";
														// $text_class = "text-right";
														$text_class = ($dir_rtl=="true")?"text-left":"text-right";
													}

													if ($detail->accept_offer==1 || $detail->denied_offer==1) 
													{
														echo '<script>$(document).ready(function(){ $("#make-offer-chat").empty(); });</script>';
													}
												?>


												<?php
													$make_offer_msg="";
													if ($detail->make_offer == 1) {
														if ($detail->from_id == auth()->user()->id) {
															$make_offer_msg = "Your Offer " . $currency_symbol[0];
														}
														else{
															if ($detail->to_id == $detail->receiver) {
																$make_offer_msg = "Seller Offer " . $currency_symbol[0];
															}
															else{
																$make_offer_msg = "Buyer Offer " . $currency_symbol[0];
															}
														}
													}

													$read_state = "";
													if ($detail->read_status == 0) {
														$read_state = $direction."-unread";
														$tick = '<img class="w-3 h-3 inline '.$class_dir_mr_lr.'" id="tick_mark_'.$detail->id.'" src="' . URL::to("storage/tick.png") . '"/>';
													} else {
														$tick = '<img class="w-3 h-3 inline '.$class_dir_mr_lr.'" id="tick_mark_'.$detail->id.'" src="' . URL::to("storage/double-tick.png") . '"/>';
													}
                                                    $getTime = App\Models\TblChat::getTimeZoneUser($detail->created_at);
                                                   
													$time = date('h:i a', strtotime($getTime['converted_datetime']));
												?>

					  
												<!--begin message area-->
												<?php if ($detail->msg != "") { ?>
												<?php if($detail->from_id == auth()->user()->id){
													$colorclass = "bg-gray-100";            
												?>
												<div class="w-4/5 {{$class_dir_float_rl}} {{$read_state}}" id="{{$detail->id}}">
													<div class="{{$colorclass}} chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-br-3xl rounded-tr-none {{$class_dir_float_rl}} {{$class_dir_mr_rl}}">
														<p class="text-base text-gray-800"><?php echo $make_offer_msg.$detail->msg; ?></p>
														<time class="text-xs text-gray-500 {{$class_dir_float_rl}} block {{$class_dir_text_rl}} w-36 "><?php echo $time; ?><?php echo $tick; ?></time>

														<?php if (!empty($get_make_offer->id) && ($get_make_offer->id == $detail->id)) { ?>
														<?php if (($get_make_offer->from_id == auth()->user()->id) || ($get_make_offer->from_id == $detail->receiver)) { 
															$actClss = ($detail->accept_offer==0 && $detail->denied_offer==0)?"edit_offer_btn":"";
															// $actMsg = ($detail->accept_offer==0 && $detail->denied_offer==0)?"Edit Offer":"Accepted";

															if($detail->accept_offer==0 && $detail->denied_offer==0)
															{
																$actMsg = "Edit Offer";
																$checkToRemove = "edit-off-clean";
															}elseif($detail->accept_offer==1 && $detail->denied_offer==0){
																$actMsg = "Accepted";
																$checkToRemove = "accept-off-clean";
															}elseif($detail->accept_offer==0 && $detail->denied_offer==1){
																$actMsg = "Denied";
																$checkToRemove = "denied-off-clean";
															}
														?>
									
														<div class="{{$actClss}}" p-id="{{$detail->id}}" o-state="{{$detail->accept_offer}}" d-state="{{$detail->denied_offer}}" data-defcurency="<?php echo $currency_symbol[0]; ?>" data-youroffer="<?php echo $detail->msg; ?>">
															<p class="bg-green-500 text-white {{$checkToRemove}} {{$div_flow}} hover:bg-white hover:text-black hover:border-green-500 shadow-lg border px-2 py-2 text-xs edit-offer mt-3 rounded-lg transition-all ease-linear duration-500" >{{$actMsg}}</p>
														</div>
									
														<?php } } ?>
													</div>
												</div>
												<?php } else {
													$colorclass = "bg-green-500";
													$textcolor = "text-white";
												?>
												<div class="w-4/5 {{$class_dir_float_lr}} {{$read_state}}" id="{{$detail->id}}">
													<div class="{{$colorclass}} chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-tr-3xl rounded-br-none">
														<div class="block">
															<p class="text-base {{$textcolor}}"><?php echo $make_offer_msg.$detail->msg; ?></p>
															<time class="text-xs text-white {{$class_dir_float_rl}} block {{$class_dir_text_rl}} w-36 "><?php echo $time; ?></time>
														</div>
														<?php if (!empty($get_make_offer->id) && ($get_make_offer->id == $detail->id)) { ?>
														<?php if (($get_make_offer->from_id == auth()->user()->id) || ($get_make_offer->from_id == $detail->receiver)) { 
															$actClss = ($detail->accept_offer==0)?"accept_offer_btn":"";
															$actMsg = ($detail->accept_offer==0)?"Accept Offer":"Accepted";
															$deniedClss = ($detail->denied_offer==0)?"denied_offer_btn":"";
															$deniedMsg = ($detail->denied_offer==0)?"Deny Offer":"Denied";
															?>
														<div class="inline-block mt-3 ">
														@if($detail->denied_offer!=1)
														
															<div class="float-left mr-2 <?php echo $actClss;?>" p-id="{{$detail->id}}" a-state="{{$detail->accept_offer}}" data-defcurency="<?php echo $currency_symbol[0]; ?>" data-youroffer="<?php echo $detail->msg; ?>">
															<p class="accept-offer {{$div_flow}} bg-gray-300 hover:bg-transparent hover:text-white cursor-pointer shadow-lg border-2 border-gray-300 px-2 py-2 text-xs font-semibold rounded-lg transition-all ease-linear duration-500">{{$actMsg}}</p>
															</div>
															@endif
															<!-- denied offer button -->
															@if($detail->accept_offer!=1)
															<div class="float-left <?php echo $deniedClss;?>" p-id="{{$detail->id}}" a-state="{{$detail->denied_offer}}" data-defcurency="<?php echo $currency_symbol[0]; ?>" data-youroffer="<?php echo $detail->msg; ?>">
															<p class="denied-offer {{$div_flow}} bg-gray-300 hover:bg-transparent hover:text-white cursor-pointer shadow-lg border-2 border-gray-300 px-2 py-2 text-xs font-semibold rounded-lg transition-all ease-linear duration-500">{{$deniedMsg}}</p>
															</div>
														
														@endif
														</div>
														<?php } } ?>
													</div>
												</div>
												<?php } ?>
											<?php } ?>
											<!--end message area-->




												<?php if ($detail->location != "") { ?>
												<?php
												$lat = $detail->latitude;
												$long = $detail->longitude;
												?>
												<div class="mb-2 w-full {{$div_flow}} {{$read_state}}" id="{{$detail->id}}">
													<div class="inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl {{$mar_r}} {{$corner}} {{$bg}} {{$div_flow}}">
														<p class="{{$div_flow}}">
															<a class="viewShared w-36 h-36 lg:w-44 lg:h-44 xl:w-56 xl:h-56 inline-block" href="https://www.google.com/maps?daddr=<?php echo $lat ?>,<?php echo $long ?>" target="_blank">
																<img class="h-full w-full object-cover object-center" src="https://maps.googleapis.com/maps/api/staticmap?center=<?php echo $lat; ?>,<?php echo $long; ?>&amp;zoom=16&amp;size=400x200&amp;sensor=false&amp;maptype=roadmap&amp;markers=color:red%7Clabel:S%7C<?php echo $lat; ?>,<?php echo $long; ?>&amp;key=<?php echo $google_api_key;?>" class="rounded-lg border-2 border-gray-200"></a>
														</p> 
														<p><time class="text-xs float-right mt-2 block text-right w-36 {{$txt_clr}}"><?php echo $time; ?><?php if($direction=='sender') { echo $tick;} ?></time></p>
													</div>
												</div>
												<?php } ?>


												<?php if ($detail->attachment != "") { ?>
												<div class="mb-2 w-full {{$div_flow}} {{$read_state}}" id="{{$detail->id}}">
													<div class="inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl {{$mar_r}} {{$corner}} {{$bg}} {{$div_flow}}">
														<?php $image_url = URL::to('storage/app/public/' . $detail->attachment); ?>
														
														<div class="" id="chat_conv_image" data-img="<?php echo $image_url; ?>">
															<div class="w-36 h-36 lg:w-44 lg:h-44 xl:w-56 xl:h-56 flex items-center justify-center">
																<img class="max-h-full max-w-full rounded m-auto <?php echo $text_class; ?>" src="<?php echo $image_url; ?>" />
															</div>
														</div>
														<p><time class="text-xs float-right mt-2 block text-right w-36 {{$txt_clr}}"><?php echo $time; ?><?php if($direction=='sender') { echo $tick;} ?></time></p>
													</div>
												</div>
												<?php } ?>


											   @endforeach
											   <!--end chat loop-->

											</div>
											@endforeach
										</div>	
									</div>

								 
                                                    
									<?php if ($check_blocked_user == 0 && count($post_deleted) > 0 && $check_user_blocked == 0) { ?>
									<div id="type-message" class="w-full float-left px-4 xl:px-6 py-4 pb-0 question_block absolute bottom-0 left-0 right-0 bg-white">
										<div class="rounded-full text-2xl rounded-lg chat-area-adjust show-hide-redymade-chat text-center m-auto cursor-pointer absolute top-0 inset-x-1/3 w-11"><i class="fa fa-angle-down bg-green-500 text-white px-3 py-2 rounded-full" aria-hidden="true"></i></div>
										
										<div class="rounded w-full bg-white border-t border-gray-100">
											<!-- Tabs -->
											<ul id="tabs" class="flex items-center w-full border border-b-0 border-t-2 border-gray-100 shadow-lg">
												<li class="w-full sm:w-1/2 text-center border-b border-transparent rounded-lg chat-area-adjust-left ">
													<a class="block mx-1 py-3 text-sm lg:text-base text-black font-medium lg:font-semibold border-b-3 lg:border-b-4 border-transparent" id="default-tab" href="#first">{{__('messages.questions')}}</a>
												</li>
												<li class="w-full sm:w-1/2 text-center border-b border-transparent rounded-lg chat-area-adjust-right">
													<a class="block mx-1 py-3 text-sm lg:text-base text-black font-medium lg:font-semibold border-b-3 lg:border-b-4 border-transparent" href="#make-offer-chat">{{__('messages.make offers')}}</a>
												</li>
											</ul>
											<!-- Tab Contents -->
											<div id="tab-contents" class="">
												<div id="first" class="my-3 px-2 rounded">
													<div class="w-full lg:w-3/4 xl:w-3/5 inline-block px-4">
														<div class="bg-green-500 p-3 rounded-xl rounded-tl-none relative mb-3">
															<div class="cnt-design absolute right-full top-0"></div>
															
															<div class="mb-0">
																<h4 class="text-base lg:text-lg font-medium text-white mb-2"><span>{{__('messages.chat to know more')}}</span></h4>
																<p class="mt-1 mb-2 font-medium text-xs lg:text-sm text-white">
																	{{__('messages.close the deal faster by asking more about the product or person')}}
																</p>
															</div>
															<!--<div class="items-center flex">
																<img src="<?php //echo URL::to("storage/auto_answer_onboarding.webp"); ?>" class="w-12 h-12" />
															</div>-->
														</div>
													</div>
													<ul class="list-unstyled inline-flex defalut-chat flex-wrap px-2">
														<li class="questions bg-white text-green-500 font-medium text-sm lg:text-base px-1 py-1 pb-2 lg:px-1 lg:pt-2.5 cursor-pointer m-1 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-2xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500">Hello</li>
														<li class="questions bg-white text-green-500 font-medium text-sm lg:text-base px-1 py-1 pb-2 lg:px-1 lg:pt-2.5 cursor-pointer m-1 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-2xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500">Is it available?</li>
														<li class="questions bg-white text-green-500 font-medium text-sm lg:text-base px-1 py-1 pb-2 lg:px-1 lg:pt-2.5 cursor-pointer m-1 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-2xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500">Okay</li>
														<li class="questions bg-white text-green-500 font-medium text-sm lg:text-base px-1 py-1 pb-2 lg:px-1 lg:pt-2.5 cursor-pointer m-1 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-2xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500">No Problem</li>
														<li class="questions bg-white text-green-500 font-medium text-sm lg:text-base px-1 py-1 pb-2 lg:px-1 lg:pt-2.5 cursor-pointer m-1 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-2xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500">Please reply</li>
														<li class="questions bg-white text-green-500 font-medium text-sm lg:text-base px-1 py-1 pb-2 lg:px-1 lg:pt-2.5	 cursor-pointer m-1 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-2xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500">Not interested</li>
													</ul>
												</div>
												<div id="make-offer-chat" class="hidden my-3">
													@if($get_post->user_id != auth()->user()->id && $detail->accept_offer != 1)
													<div class="w-full inline-block rounded  ">
															
														<?php $perc1 = 0;
														for($j=1;$j<=4;$j++){
															 
														if($post_price>100){
															$perc1 = $perc1+5;
															$offer = $post_price - round(($post_price * $perc1) / 100);                         $lastPrice1 = ($j==4) ? $offer : "";
														}
														else{ $offer = $post_price - $j;
															$lastPrice1 = ($j==4) ? $offer : "";
														}
														}
														?>
														
														<div class="make-offer-design">
															<input type="hidden" class="offer-price" value="<?php echo floor($post_price); ?>" />
															<div class="w-full inline-block mb-4">
																<div class="w-full sm:w-1/2 relative mb-4 sm:mb-0 {{$class_dir_float_lr}}">
																	<label class="text-black text-2xl font-bold absolute {{$class_dir_lr}}-1 top-1.5"><?php echo $currency_symbol[0]; ?></label>
																	<input type="number" currency="<?php echo $currency_symbol[0]; ?>" price="<?php echo floor($post_price); ?>" min="<?php echo $lastPrice1; ?>" max="<?php echo floor($post_price); ?>" value="<?php echo floor($post_price); ?>" class="offer_input appearance:none px-4 py-2 pb-3 text-gray-800 text-xl font-medium bg-white rounded outline-none focus:outline-none md:36 w-full shadow {{$class_dir_pl_lr}}" />
																</div>
																
																<button name="send" id="send_make_offer" class="send bg-green-500 text-white font-semibold text-base border-2 border-green-500 px-8 py-2 pb-3 rounded-xl shadow hover:bg-white hover:text-green-500 outline-none focus:outline-none ease-linear transition-all duration-500 inline-block sm:w-auto w-full flaot-right">{{__('messages.send')}}</button>											
															</div>	
															
															<div class="w-full inline-block mb-4">
																<ul class="list-unstyled inline-flex flex-wrap">
																	
																	
																	<li row-val="5" class="bg-white text-green-500 font-medium text-sm lg:text-base px-2 py-1 lg:px-4 lg:py-2 cursor-pointer m-1 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-2xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500 price-active" data-defcurncy="<?php echo $currency_symbol[0]; ?>" data-defmsg="<?php echo floor($post_price); ?>"><?php echo $currency_symbol[0] . floor($post_price); ?></li>
																	<?php
																	$perc = 0;
																	for($j=1;$j<=4;$j++){
																		 
																	if($post_price>100){
																		$perc = $perc+5;
																		$offer = $post_price - round(($post_price * $perc) / 100);                         $lastPrice = ($j==4) ? $offer : "";
																	}
																	else{ $offer = $post_price - $j;
																		$lastPrice = ($j==4) ? $offer : "";
																	}
																	?>


																	<li row-val="{{$j}}" class="bg-white text-green-500 font-medium text-sm lg:text-base px-2 py-1 lg:px-4 lg:py-2 cursor-pointer m-1 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-2xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500" data-defcurncy="<?php echo $currency_symbol[0]; ?>" data-defmsg="<?php echo $offer; ?>"><?php echo $currency_symbol[0] . $offer; ?></li>
																	<?php } ?>

																</ul>
															</div>
															
															<div class="w-full inline-block">
																<div class="w-full lg:w-3/4 xl:w-3/5 inline-block pl-4 pr-2 lg:px-4">
																	<div class="bg-green-500 p-3 rounded-xl rounded-tl-none relative">
																		<div class="cnt-design absolute right-full top-0"></div>
																		<div class="w-full inline-block mb-4 sm:mb-0">
																			<h4 id="offer-title" class="text-base lg:text-lg font-medium text-white mb-2">{{__('messages.offer_title_1')}}</h4>
																			<p id="offer-desc" class="mt-1 mb-2 font-medium text-xs lg:text-sm text-white">{{__('messages.offer_desc_1')}}</p>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														@endif
													</div>
												</div>
											</div>
											
											<!--typemessage-->
											<div class="w-full float-left bg-white pb-4" onclick="texting_area_click()">
												<div class="chat-box w-full float-left bg-gray-100 rounded-lg relative">
												
													@if($dir_rtl =="false")
													<textarea class="w-full bg-gray-100 outline-none px-5 py-4 pr-36 lg:pr-48 h-14" type="text" id="message_text" name="message" placeholder="{{__('messages.type message')}}"></textarea>
													<div class="absolute right-2 sm:right-4 bottom-3 w-auto lg:w-44">
														<span class="send_message text-base bg-green-500  text-center  w-8 h-8  ml-2 leading-7  lg:w-10 lg:h-10 lg:ml-3 lg:leading-9 text-white float-right inline-block rounded-full">
														<img class="inline-block" src="{{ URL::to('images/frontend/send.png') }}" alt="send.png"></span>

														<span class="text-lg bg-green-500  text-center w-8 h-8  ml-2 leading-8  lg:w-10 lg:h-10 lg:ml-3 lg:leading-9 text-white float-right inline-block rounded-full ">
															<span class="cursor-pointer block chat_share_btn" id="share_loc_btn">
																<i class="fa fa-map-marker text-2xl" aria-hidden="true"></i>
															</span>
														</span>

														<span class="text-xs bg-green-500  text-center w-8 h-8  ml-2 leading-7  lg:w-10 lg:h-10 lg:ml-3 lg:leading-9 text-white float-right inline-block rounded-full ">
															<label class="attachments">
															<i class="fa fa-picture-o" aria-hidden="true"></i>
															<input type="file" accept="image/*" name="image" id="attachments" class="attachment">
															<input type="hidden" id="file" name="filename">
															</label>
														</span>
													</div>
													
													
													
													@else
													<textarea class="w-full bg-gray-100 outline-none px-5 py-4 pl-28 sm:pl-48 h-14" type="text" id="message_text" name="message" placeholder="{{__('messages.type message')}}"></textarea>
													<div class="absolute left-2 sm:left-4 bottom-3 w-auto sm:w-44">
														<span class="send_message text-base bg-green-500  text-center  w-8 h-8  mr-2 leading-7  lg:w-10 lg:h-10 lg:mr-3 lg:leading-9 text-white float-left inline-block rounded-full">
														<img class="inline-block" src="{{ URL::to('images/frontend/send.png') }}" alt="send.png"></span>

														<span class="text-lg bg-green-500  text-center w-8 h-8  mr-2 leading-8  lg:w-10 lg:h-10 lg:mr-3 lg:leading-9 text-white float-left inline-block rounded-full "><span class="cursor-pointer block chat_share_btn" id="share_loc_btn">
															<i class="fa fa-map-marker text-2xl" aria-hidden="true"></i>
														</span>
														</span>



														<span class="text-xs bg-green-500  text-center w-8 h-8  mr-2 leading-7  lg:w-10 lg:h-10 lg:mr-3 lg:leading-9 text-white float-left inline-block rounded-full ">
															<label class="attachments">
															<i class="fa fa-picture-o" aria-hidden="true"></i>
															<input type="file" accept="image/*" name="image" id="attachments" class="attachment">
															<input type="hidden" id="file" name="filename">
															</label>
														</span>
													</div>
													@endif
													
												</div>
											</div>
											<div id="deteleted-post-warning" style="display:none">
											<span class='p-4 text-red-500'>{{__('messages.delete_post_chat_warning')}}</span>
											</div>
										</div>
									<?php } ?>
									</div>

									<!-- end popup -->
									<?php } else {?>
									<?php $update_online_status = App\Models\TblChat::UpdateUserStatus(auth()->user()->id, "offline"); ?>
										<img class="m-auto w-40 mt-6 md:mt-20" src="<?php echo URL::to('storage/emptyChat.png'); ?>" />
										<p class="text-center mt-2 mb-6 md:mb-0 sm:mt-4 text-base md:text-lg text-black"><?php echo __('messages.Select a chat to view conversation'); ?></p>
									<?php } ?>
								</div>
						  
								<!--right-chatend-->   

								<!-- share location start -->
								<div class="fixed z-50 inset-0 overflow-y-auto" id="share_loc_popup" style="display:none">
									<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
										<div class="fixed inset-0 transition-opacity" aria-hidden="true">
											<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
										</div>
										<!-- This element is to trick the browser into centering the modal contents. -->
										<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
										<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
											<div class="bg-white px-6 py-4 sm:px-12 sm:py-8">
												<div class="mb-6">
													<div class="text-left">
														<h3 class="block text-xl text-black font-semibold mb-2 sm:mb-4" id="modal-headline">
															{{__('messages.share location')}}
														</h3>
														<div class="mt-2">
															<div class="py-2">
																<div class="relative">
																	<input id="pac-input" class="controls px-2 py-3 bg-gray-100 border-l-2 border-gray-400 rounded text-gray-700 w-full" type="text" placeholder="{{__('messages.enter a location')}}">
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
												
												<div class="sm:flex sm:flex-row-reverse">
													<button type="button" class="chat_share_send_btn w-full inline-flex justify-center rounded-md border-2 border-transparent shadow-sm px-4 py-2 pb-3 bg-green-500 text-base font-semibold text-white hover:border-green-500 hover:text-green-500 hover:bg-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all ease-linear duration-500" id="submit_shared_loc">{{__('messages.share')}}</button>
													
													<button type="button" id="share_loc_cancel" class="chat_share_close mt-3 w-full inline-flex justify-center rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 pb-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-200 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all ease-linear duration-500">{{__('messages.cancel')}}</button>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- share location end -->
								
								
								<!-- Chat Conversation Image Popup -->
								<div class="fixed z-50 inset-0 overflow-y-auto" id="chat_conv_image_popup" style="display:none">
									<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
										<div class="fixed inset-0 transition-opacity" aria-hidden="true">
											<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
										</div>
										<!-- This element is to trick the browser into centering the modal contents. -->
										<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
										<div class="inline-block align-bottom bg-transparent text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
											<div class="py-9 h-96 w-full">
												<div class="bg-white flex h-full items-center">
													<img class="max-h-full max-w-full mx-auto" src="" id="image_pop_up"/>
												</div>
												<button type="button" id="chat_conv_image_close" class="border-2 border-gray-300 shadow-sm px-2 py-1 bg-white text-base font-normal text-gray-700 hover:bg-gray-200 focus:outline-none sm:w-auto transition-all ease-linear duration-500 absolute top-0 right-0"><i class="fa fa-times" aria-hidden="true"></i></button>
												
											</div>
										</div>
									</div>
								</div>
								<!-- Chat Conversation Image Popup -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	<?php
		//for map
		if (Session::has("CurrLoggedLat")) {
			$current_lat = Session::get("CurrLoggedLat");
			$currenct_long = Session::get("CurrLoggedLng");
		} else {
			$current_lat = "-33.8688";
			$currenct_long = "151.2195";
		}
	?>



	<script>
		jQuery(document).ready(function(){
			$(".user_chat_button button").click(function() {
				$(".user_chat_block").toggleClass("hidden")
			});
		});


		//offer input 
		
		$(".offer_input").on("keyup", function(e) {
			var inputVal = $(this).val();
			var price = $(this).attr('price');
			var min = $(this).attr('min');
			var max = $(this).attr('max');
			var get_currency = $(this).attr('currency');
			
			if(inputVal == "" ) {
				$(".show_offer_price").text(get_currency + price);
				$(".offer-price").val(price);
			}
			else{
				$(".make-offer-design .show_offer_price").text(get_currency + inputVal); // curency symbol need to change
			$(".make-offer-design .offer-price").val(inputVal);
			}

		});
		
	</script>
		

	<script>
	
	
	
	
	$(".show-hide-redymade-chat").click(function() {
		
	});


	var tabcheck = document.getElementById("tabs");
	if(tabcheck)
	{
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
					$("#tab-contents").show();
					
				}
				e.target.parentElement.classList.add("tab-active");
			});
		});
		document.getElementById("default-tab").click();
	}


	$("body").delegate('.edit_offer_btn','click', function(e) {

		e.preventDefault();
		var offered_price = $(this).attr('data-youroffer');
		var get_curency = $(this).attr("data-defcurency");
		//$(".chat-box").slideToggle("slow");
		
		// $("#tab-contents").slideUp("slow");
		//$("#tab-contents").css("display","block");
		
		$("#tabs li").removeClass("tab-active");
		$("#tabs li:nth-child(2)").addClass("tab-active");
		$("#tab-contents #first").removeClass('hidden');
		$("#tab-contents #make-offer-chat").removeClass('hidden');
		$("#tab-contents #first").addClass('hidden');
		$("#make-offer-chat ul li").removeClass('price-active');
		var check_price_val;
		$('#make-offer-chat ul li').each(function() {
			check_price_val = $(this).attr('data-defmsg');
			if (check_price_val === offered_price) {
				$(this).addClass('price-active');
				$(".make-offer-design p.offer-price").text(get_curency + offered_price);
				$(".make-offer-design input.offer-price").val(offered_price);
				$(".offer_input").val(offered_price);
				$(".show_offer_price").text(get_curency + offered_price);
			}
		});
		
		// tab_operation('right');
	});
	
	
	</script>

	<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
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
			infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address + '</div>');
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


	$("#make-offer-chat ul li").on('click', function(e) {
			e.preventDefault();
			var get_val = $(this).attr('data-defmsg');
			var get_currency = $(this).attr("data-defcurncy");
			var rowVal = $(this).attr("row-val");
			// alert(rowVal);
			$("#offer-title").text("demo Very good offer!");
			$("#make-offer-chat ul li").removeClass("price-active");
			$(".make-offer-design .show_offer_price").text(get_currency + get_val); // curency symbol need to change
			$(".make-offer-design .offer-price").val(get_val);
			$(this).addClass("price-active");
			var inputVal = $(".offer_input").val(get_val);
			// languages
			var offer_title_1 = "<?php echo __('messages.offer_title_1') ?>";
			var offer_desc_1 = "<?php echo __('messages.offer_desc_1') ?>";
			var offer_title_2 = "<?php echo __('messages.offer_title_2') ?>";
			var offer_desc_2 = "<?php echo __('messages.offer_desc_2') ?>";
			var offer_title_3 = "<?php echo __('messages.offer_title_3') ?>";
			var offer_desc_3 = "<?php echo __('messages.offer_desc_3') ?>";
			var offer_title_4 = "<?php echo __('messages.offer_title_4') ?>";
			var offer_desc_4 = "<?php echo __('messages.offer_desc_4') ?>";
			var offer_title_5 = "<?php echo __('messages.offer_title_5') ?>";
			var offer_desc_5 = "<?php echo __('messages.offer_desc_5') ?>";

				// rowVal == 5 means post price (1st price). rowVal == 1,2,3,4 means nxt four price.
			if(rowVal == 5)
			{
				$("#offer-title").text(offer_title_1);
				$("#offer-desc").text(offer_desc_1);
			}else if(rowVal == 1){
				$("#offer-title").text(offer_title_2);
				$("#offer-desc").text(offer_desc_2);
			}else if(rowVal == 2){
				$("#offer-title").text(offer_title_3);
				$("#offer-desc").text(offer_desc_3);
			}else if(rowVal == 3){
				$("#offer-title").text(offer_title_4);
				$("#offer-desc").text(offer_desc_4);
			}else if(rowVal == 4){
				$("#offer-title").text(offer_title_5);
				$("#offer-desc").text(offer_desc_5);
			}


		});


	</script>


	<script>
	
	
	$(document).ready(function(){
		$(".chat_share_btn").click(function(){
			$("body").addClass("scroll_stop");
		});
		
		$(".chat_share_close").click(function(){
			$("body").removeClass("scroll_stop");
		});
		
		$(".chat_share_send_btn").click(function(){
			$("body").removeClass("scroll_stop");
		});




		/*var attr = $(this).attr('a-state');

		if (typeof attr !== typeof undefined && attr !== false) {
			console.log("Exist: "+attr);
		}
		else{
			console.log("Not Exist: "+attr);
		}*/

	});
	



	// share location start
	$('#share_loc_btn').on('click', function(e) {
		document.querySelector("#share_loc_popup").style.display = "block";
	});

	$('#share_loc_cancel').on('click', function(e) { closeLocPopUp(); });
	function closeLocPopUp(){ document.querySelector("#share_loc_popup").style.display = "none"; }
	// share location end
	
	
	
					
					
	// $('#chat_conv_image').on('click', function(e) {
		// var imgSrc = $(this).attr("data-img");
		// document.querySelector("#chat_conv_image_popup").style.display = "block";
		// document.querySelector("#image_pop_up").src = imgSrc;
		
	// });
	
	$("body").delegate("#chat_conv_image","click",function(){
		var imgSrc = $(this).attr("data-img");
		document.querySelector("#chat_conv_image_popup").style.display = "block";
		document.querySelector("#image_pop_up").src = imgSrc;
	});
	

	$('#chat_conv_image_close').on('click', function(e) { closeConvimagePopUp(); });
	function closeConvimagePopUp(){ document.querySelector("#chat_conv_image_popup").style.display = "none"; }





	//scroll to bottom
	$(window).on('load', function() { CallScrollToBottom(); });

	function CallScrollToBottom()
	{
		var _receiver_id = "{{request()->to}}";
		var _post_id = "{{request()->p}}";
		if(_receiver_id.length>0 && _post_id.length>0)
		{
			var elem = document.getElementById('messages_area');
			if(elem!=null){ elem.scrollTop = elem.scrollHeight; getLastSeen(); }  
		}
  
	}
	//begin- sending message, location, etc




	$("#send_make_offer").click(function(){
	$('#messages_area .today').find('.edit_offer_btn').remove();


	var msg = parseInt($(".offer-price").val());
	var min = parseInt($(".offer_input").attr("min"));
	var max = parseInt($(".offer_input").attr("max"));

	if(msg >= min && msg <= max)
	{
	   //console.log(p);
	$(".show-hide-redymade-chat").trigger('click');//close the shutter
	$("#message_text").val(msg);
	sendmessage('1'); 

	}else{
		toastr.success("Enter price between "+ min + "-" + max);
	}


	//console.log(p);
	// $("#message_text").val(msg);
	// sendmessage('1');
	});

	$(".questions").click(function(){
		//$(".show-hide-redymade-chat").trigger('click');//close the shutter
		var msg = $(this).html();
		$("#message_text").val(msg);
		//sendmessage('0');
	});

	$("body").delegate('.accept_offer_btn','click', function(e) {
		var accept_state =  $(this).attr('a-state');
		var curr = $(this).attr('data-defcurency');
		var pri = $(this).attr('data-youroffer');    
		var msg = "Offer "+curr+pri+" Accepted. \n Let's move towards the final deal.";
		
		if(accept_state=="0")
		{
			var p = $(this).attr('p-id');
			//console.log("post: "+p);

			$.ajax({
				type:"post",
				dataType:"json",
				url:"{{URL::to('ajax_send_chat_accept_offer') }}",
				data:{
					id:p
				},
				success:function(data){
					if(data.result==true){
						$('.accept_offer_btn').attr('a-state','1');
						$('div.accept_offer_btn > p').html("Accepted");
						$('#messages_area').find(".accept_offer_btn").removeClass('accept_offer_btn');
						$('.denied_offer_btn').remove();                    
						$("#message_text").val(msg);
						sendmessage('0');
					}
				}
			});
		}

	});

	// for denied offer 
	$("body").delegate('.denied_offer_btn','click', function(e) {
		var denied_state =  $(this).attr('a-state');
		var curr = $(this).attr('data-defcurency');
		var pri = $(this).attr('data-youroffer');    
		var msg = "Offer "+curr+pri+" Denied.";
		
		if(denied_state=="0")
		{
			var p = $(this).attr('p-id');
			//console.log("post: "+p);

			$.ajax({
				type:"post",
				dataType:"json",
				url:"{{URL::to('ajax_send_chat_denied_offer') }}",
				data:{
					id:p
				},
				success:function(data){
					if(data.result==true){
						$('.denied_offer_btn').attr('a-state','1');
						$('div.denied_offer_btn > p').html("Denied");
						$('#messages_area').find(".denied_offer_btn").removeClass('denied_offer_btn');
						$('.accept_offer_btn').remove();
						$("#message_text").val(msg);
						sendmessage('0');
					}
				}
			});
		}

	});

	//check if exist editoffer button
	var acceptOff = setInterval(function(){
		if ($('#messages_area .edit_offer_btn').length>0)
		{
			var accept_state = $('#messages_area .edit_offer_btn').attr('o-state');
			var deny_state = $('#messages_area .edit_offer_btn').attr('d-state');

			if(accept_state=="0" && deny_state=="0")
			{
				var p = $('#messages_area .edit_offer_btn').attr('p-id');
				$.ajax({
					type:"post",
					dataType:"json",
					cache:false,
					url:"{{URL::to('ajax_send_chat_offer_state_check') }}",
					data:{
						id:p
					},
					success:function(data){
						if(data.result==true){
							if(data.denied_value=="1"){
								$('.edit_offer_btn').attr('d-state','1');
								$('div.edit_offer_btn > p').html("Denied");
								$('#messages_area').find(".edit_offer_btn").removeClass('edit_offer_btn');
								$("#make-offer-chat").empty();
								clearInterval(acceptOff);
							}else if(data.accept_value=="1"){
								$('.edit_offer_btn').attr('o-state','1');
								$('div.edit_offer_btn > p').html("Accepted");
								$('#messages_area').find(".edit_offer_btn").removeClass('edit_offer_btn');
								$("#make-offer-chat").empty();
								clearInterval(acceptOff);
							}
						}
					}
				});
			}
		}
	}, 2000);


	$(window).on('load', function() {
		var accept_state = $('#messages_area .edit_offer_btn').attr('o-state');
		if(accept_state!=null && accept_state=="1"){
			$("#make-offer-chat").empty();
			console.log("onload called it");
		}

	 });


	$(".send_message").click(function(){ sendmessage('0'); });
	$("#message_text").on('keyup', function (e) {
		if (e.key === 'Enter' || e.keyCode === 13) { sendmessage('0'); }
	});

	function sendmessage(_make_offer){
		var _sender_id = "{{auth()->user()->id}}";
		var _receiver_id = "{{request()->to}}";
		var _post_id = "{{request()->p}}";

		if(_sender_id==null || _sender_id==""){
			reload(true);
			return false;
		}
		var _msg = $("#message_text").val();
		if(_msg==""){ $("#message_text").focus(); return false; }
	  

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: "{{ URL::to('ajax_send_chat_msg') }}",
			data: {
				msg : _msg,
				sender_id:_sender_id,
				receiver_id:_receiver_id,
				post_id:_post_id,
				make_offer:_make_offer
			},
			success: function(data) {
				$("#message_text").val('');
				var reloadedhtml = reloadConversationArea();

				//remove if already - edit offer button paced
				if(reloadedhtml.length>0 && reloadedhtml.includes("edit_offer_btn"))
				{
					$('#messages_area').find('.edit_offer_btn').remove();
				}

				if(reloadedhtml.length>0){
					$('#messages_area .today').append(reloadedhtml);
					CallScrollToBottom();
				}                          

				
			}
			});
	}

	$("#submit_shared_loc").click(function() {
			var address_loc = $("#user_address").val();
			var address_lat = $("#user_latitude").val();
			var address_long = $("#user_longitude").val();
			var to = "{{request()->to}}";//$(".to").val();
			var post_id = "{{request()->p}}";// $(".post_id").val();
			if (address_loc == "" || address_loc == null) {
				$("#pac-input").focus();
				toastr.warning("Please enter valid address to share with the user!");
				return false;
			} else {
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: "{{ URL::to('ajax_send_chat_location') }}",
					data: {
						to: to,
						loc: address_loc,
						lat: address_lat,
						long: address_long,
						post_id: post_id,
					},
					success: function(data) {
						var reloadedhtml = reloadConversationArea();

						if(reloadedhtml.length>0){
							$('#messages_area .today').append(reloadedhtml);
							CallScrollToBottom();
							console.log("location shared successfully");
						} 
						closeLocPopUp();
						
					}
				});
			}

		});

	$('#attachments').change(function() {
		var _imgage = $('#attachments').prop('files')[0]; 
		var to = "{{request()->to}}";
		var post_id = "{{request()->p}}";

		var postData=new FormData();
		postData.append('image_file',_imgage);
		postData.append('to',to);
		postData.append('post_id',post_id);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: "{{ URL::to('ajax_send_chat_image') }}",
			data: postData,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data) {
				var reloadedhtml = reloadConversationArea();
				if(reloadedhtml.length>0){
					$('#messages_area .today').append(reloadedhtml);
					CallScrollToBottom();
					
				}                          
			}
			});

			return false;
	});


	//end - sending message, location, etc




	function reloadConversationArea()
	{
		var _last_recid ="";
		var _curr_day_last_recid = $('#messages_area .today').children().last().attr('id');//current day last msg id
		var _prev_day_last_recid = $('#messages_area .previous_day').children().last().attr('id');//previous day last msg id - if current day not avail
		if(_curr_day_last_recid!=null) {
			_last_recid = _curr_day_last_recid;
		}
		if(_curr_day_last_recid==null){
			_last_recid = _prev_day_last_recid;
		}
		//console.log("Previous last:- "+_last_recid);
		var htmldata = "";
		if(_last_recid){
		var _param = "{{ URL::to('ajax_reload_conversation_area') }}?to={{request()->to}}&p={{request()->p}}&type=old&last_recid="+_last_recid;
		//console.log(_param);
			$.ajax({
				type: 'get',
				url: _param,
				async: false,
				data: {
					msg: "dum"
				},
				success: function(data) {
					htmldata = data.html;
				//remove if already - accept offer button paced
				if(htmldata.length>0 && htmldata.includes("accept_offer_btn"))
				{
					$('#messages_area').find('.accept_offer_btn').remove();
					$('#messages_area').find('.denied_offer_btn').remove();
				}
			
				$('#type-message').css( "pointer-events", "auto" );
				document.querySelector("#deteleted-post-warning").style.display = "none";

				// var total_msg_count = data.total_msg_count;
				// if(total_msg_count == 0)
				// {
				// 	$("#messages_area > .show_bottom").html('');
				// 	//window.location.href = "<?php //echo url('chatting'); ?>";
				// }

				},
				error: function (data) {

					$('#type-message').css( "pointer-events", "none" );
					document.querySelector("#deteleted-post-warning").style.display = "block";
					
    			}
			});
		}

		return htmldata;
	}



	//check & update - if any new message received/sent
	setInterval(function(){
			var reloadedhtml = reloadConversationArea();
			
			if(reloadedhtml.length>0){
				if ($("#messages_area .today").length>0) {
					$('#messages_area .today').append(reloadedhtml);
					console.log("printing it...");
				}
				else{
					//while first time insert
					var _h = '<div class="w-full mesage float-left today"><div class="w-full text-gray-500 my-3 text-center">Today</div>';
					var _f = '</div>';
					$('#messages_area > .show_bottom').append(_h+reloadedhtml+_f);
				}
			}
	}, 3000);


	function isOnScreen(element)
	{
		var curPos = element.offset();
		var curTop = curPos.top;
		
		var screenHeight = $(".chat_converstion").height();//$(window).height();
		return (curTop > screenHeight) ? false : true;
	}


	//update if receiver read messages
	$(".chat_converstion").scroll(function(){
		//while scroll reached bottom - read status , update will begin
	if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
		updateReadStatus();
	}
	});

	function updateReadStatus()
	{
		var last = $('#messages_area .today').find('.receiver-unread').attr('id');
		if(last!=null){
			var i = 0;
			var ids = [];
			$(".receiver-unread").each(function(){
			ids[i++] =  $(this).attr("id"); //this.id
			});
			var _msg_ids = ids;//"'"+ids.join("','")+"'";

			var _param = "{{ URL::to('ajax_chat_update_message_read') }}";
			var htmldata = "";
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: _param,
				data: {
					msg_ids : _msg_ids
				},
				success: function(data) {
					if(data.success==true){
						$('#messages_area .today').find('.receiver-unread').removeClass('receiver-unread');
						//console.log("readout:-"+data.message);
					}
					
				}
			});

		}
	}
	//update if receiver read messages

	//*<<<<<<< begin - update tick >>>>>> *//
	setInterval(function(){
		var ticked = "<?php echo URL::to("storage/double-tick.png");?>";
		var first = $('#messages_area .today').find('.sender-unread').first().attr('id');
		if(first){

			var i = 0;
			var ids = [];
			$(".sender-unread").each(function(){
				ids[i++] =  $(this).attr("id"); //this.id
			});
			var _msg_ids = ids;//"'"+ids.join("','")+"'";

			

			var _param = "{{ URL::to('ajax_chat_fetch_readed_ids') }}";
			var htmldata = "";
			$.ajax({
				type: 'post',
				dataType: 'json',
				url: _param,
				async: false,
				data: {
					msg_ids : _msg_ids
				},
				success: function(data) {
					if((data.ids).length>0){
						$.each(data.ids, function(index,value){
							$('#tick_mark_'+value).attr('src', ticked);
							$('#messages_area .today #'+value).removeClass('sender-unread');
						});
						//console.log("idss: "+data.ids);
					}
					
				}
			});
		}
	}, 3000);
	//*<<<<<<< end - update tick >>>>>> *//


	//********>>>> begin chat list reload <<<<<<********//
	function reloadChatlistArea(que)
	{
		//console.log("query="+que);
		var _param = "{{ URL::to('ajax_reload_chatlist_area') }}?to={{request()->to}}&p={{request()->p}}&type=old&q="+que;
		//console.log("search: "+_param);
		var htmldata = "";
			$.ajax({
				type: 'get',
				url: _param,
				async: false,
				data: {
					q : que
				},
				success: function(data) {
					htmldata = data.html;
				}
			});
			$("#chat_list_area").html(htmldata);
			//return htmldata;
	}

	setInterval(function(){
			var q=$(".search_text").val();
			reloadChatlistArea(q);
	}, 3000);


	//********>>>> end chat list reload <<<<<<********//




	/*<<<<<<<<<<<<<<<<< Begin - Block/Delete Chat >>>>>>>>>>>>>>>>>>>>>> */
	$(".block_user").click(function(event) {
			var ids = $(this).attr('data-id');
			var msg = ($(this).attr('data-block-status') == 0) ? " to Block the user?" : " to Unblock the user?";
			if (confirm('Are you sure you want' + msg)) {
				delete_chat(ids, 'block');
			} else {
				event.preventDefault();
			}
	});
	$(".delete_all").click(function(event) {
			var ids = $(this).attr('data-id');
			if (confirm('Are you sure to delete?')) {
				delete_chat(ids, 'delete');
			} else {
				event.preventDefault();
			}
	});

	function delete_chat(id, type) {
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: "{{ URL::to('ajax_send_chat_block_delete_chat') }}",
			data: {
				id: id,
				type: type
			},
			cache: false,
			success: function(data) {
				toastr.success(data.message);
				if (data.type == "delete") {
					window.location = "<?php echo URL::to('/chatting'); ?>";
				} else {
					console.log("hi");
					$(".question_block").css("style","display:none");
					//$(".chat-box").css("style","display:none");
					location.reload();
				}

			}
		});
	}
	/*<<<<<<<<<<<<<<<<< End - Block/Delete Chat >>>>>>>>>>>>>>>>>>>>>> */



	/* Begin - Check User State */
	var inactiveTime;
	var i=0;
	$('*').bind('mousemove click mouseup mousedown keydown keypress keyup submit change mouseenter scroll resize dblclick', function () {
		if(i==0)
		{
		console.log("State: Active");
		updateLastSeen();
		i++;
		}   
		function callIdleState() {
			// do your task here
			updateLastSeen();
			console.log("State: InActive");
			i=0;
		}
		clearTimeout(inactiveTime);
		inactiveTime = setTimeout(callIdleState, 1000 * 5); // 5 seconds
	});
	$("body").trigger("mousemove");

	function updateLastSeen()
	{
		$.ajax({
			type:'post',
			dataType:'json',
			data:{id:"dum"},
			url:"{{ URL::to('ajax_send_chat_update_last_seen') }}",
			success:function(data){
				//console.log(data.result);
			}
		});
	}

	setInterval(function(){ 
		var elem = document.getElementById('messages_area');
		if(elem!=null){ getLastSeen(); }
	},1000 * 5);//5 second
	function getLastSeen()
	{
		var _receiver_id = "{{request()->to}}";
		var _post_id = "{{request()->p}}";
		if(_receiver_id.length>0 && _post_id.length>0)
		{
		var _uri = "{{ URL::to('ajax_send_chat_fetch_last_seen') }}?id={{request()->to}}";
		$.ajax({
			type:'get',
			dataType:'json',
			data:{ r:"dum"},
			cache: false,
			url:_uri,
			success:function(data){
				$(".lastseen").html(data.last_seen);
			}
		});
		}
	}

	
	</script>




	</div>
	@endsection	