	<div class="root-element-div">
		<!-- section start --->
		<link href="{{ URL::to('css/chat.css') }}" rel="stylesheet">
		
	<?php 
		$get_meta = App\Models\TblOtherpage::get_meta('user-chats');
		$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
		$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
		$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
	?>

	@if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description))
        @section('meta_title', $meta_title)
        @section('meta_keywords', $meta_keywords)
        @section('meta_description', $meta_description)
	@endif

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
						<!-- search box -->
						
						<div class="search_icon relative max-w-md mx-auto mb-2 px-2">
							<input type="text" wire:model="search" class="w-full bg-gray-50 pl-12 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 p-3" type="text" placeholder="{{__('messages.search by product name')}}" />
							<label class=" absolute top-3 left-5 text-gray-500 font-medium"><i class="fa fa-search" aria-hidden="true"></i></span></label>
						</div>
						<!-- search box end -->
						
						<div class="w-full chats rounded-lg float-left cursor-pointer ">
							<!-- chat list start -->
							@foreach($chatlists as $chatlist)
							<?php
							$visible_posts = App\Models\TblPost::check_payment_pack_expired($chatlist->post_id);
							if(!empty($visible_posts)) {

							$selected = "bg-white";
							$sender = ((auth()->user()->id == $chatlist->from_id) ? $chatlist->to_id : $chatlist->from_id);
							$chaturl = URL::to('/chat') . '?to=' . $sender . '&p=' . $chatlist->post_id . '&type=old';
							$sendername = App\Models\TblChat::getSender($sender);
							$unread_count = App\Models\TblChat::getUnreadCount(auth()->user()->id, $sender, $chatlist->post_id);
							if ((request()->to == $sender) && (request()->p == $chatlist->post_id)) {
								$selected = "bg-gray-100";
							}
							$lastchat = App\Models\TblChat::getLastChat($sender, $chatlist->post_id);

							if ($chatlist->accept_offer==1 || $chatlist->denied_offer==1) 
							{
								echo '<script>$(document).ready(function(){ $("#make-offer-chat").empty(); });</script>';
							}
							?>
							
							<div class="w-full px-3 bg-white float-left user_chats lg:px-5 xl:px-8 py-4 border-b relative {{ $selected }}">
								<a href="{{$chaturl}}" class="chat_lists w-full inline-block">
									<?php $user_profle = App\Models\User::where('id', $sender)->pluck('profile_photo_path')->first(); ?>
									<div class="user_image w-16 sm:w-20 md:w-14 xl:w-20 mt-4 float-left mr-4 md:mt-2 lg:mr-4 lg:mt-4 xl:mt-3">
										<img class="w-16 h-16 sm:w-20 sm:h-20 md:w-14 md:h-14 xl:w-20 xl:h-20 float-left rounded-full object-cover" src="<?php echo !empty($user_profle) ? URL::to('storage/' . $user_profle) : URL::to('storage/profile-avatar.jpg') ?>" alt="user.jpg">
									</div>
									<div class="user_detail pt-3 pr-8 md:pr-14 lg:pr-8">
									<p class="">
										<span class="font-bold text-base md:text-sm lg:text-lg pb-1 inline-block capitalize align-middle truncate">{{$sendername}}</span>
										<span class="bg-green-400 px-1.5 py-0.5 pb-1 lg:px-1.5 lg:py-0.5 lg:pb-1 ml-2 leading-tight rounded-full text-sm text-white" data-ids="{{ $chatlist->post_id . '.' . $chatlist->receiver }}">
											{{$unread_count}}
										</span>
									
										<input type="hidden" class="{{ $chatlist->receiver.'_unread' }}" value="{{ $unread_count }}">
										<input type="hidden" class="{{ $chatlist->receiver.'_status' }}" value="<?php echo !empty($selected) ? "online" : "offline"; ?>">
									</p>
										<?php $last_time = date('h:i a', strtotime($lastchat['created_at']));?>
										<time class="text-xs text-gray-500 absolute top-8 right-5">{{$last_time}}</time>
										<button class="delete_all {{ $selected }} focus:outline-none bg-transparent absolute top-1/2 right-3 px-2.5 py-1 text-red-500 text-xl font-bold" title="Delete Chat" data-id="{{ $sender . '@' . $chatlist->post_id }}"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
										<p class="text-sm text-gray-500"><?php echo mb_strimwidth($chatlist->post_name, 0, 12, "...");?></p>
										<p class="text-sm text-black" data-id="{{ $chatlist->post_id . '.' . $chatlist->receiver }}">
										<?php
									$tick ="";
									if($lastchat['from_id']== auth()->user()->id){
										if ($lastchat['read_status'] == 0) {
										
											$tick = '<span class="mt-2 mr-2"><img class="w-3 h-3 inline ml-1" src="' . URL::to("storage/tick.png") . '"/></span>';
										} else {
											$tick = '<span class="mt-2 mr-2"><img class="w-3 h-3 inline ml-1" src="' . URL::to("storage/double-tick.png") . '"/></span>';
										}
									}
									echo $tick;
									?>
											<span class="mt-2 mr-2 inline-block">
												@if(!empty($lastchat['msg']))
												@if($lastchat['make_offer'] == 1)
												<?php echo mb_strimwidth("Offer request sent", 0, 12, "...");?>
												@elseif($lastchat['accept_offer'] == 1)
												<?php echo mb_strimwidth("Offer request accepted", 0, 12, "...");?>
												@elseif($lastchat['denied_offer'] == 1)
												<?php echo mb_strimwidth("Offer request denied", 0, 12, "...");?>
												@else
												<?php echo mb_strimwidth($lastchat['msg'], 0, 12, "...");?>
												@endif
												@elseif(!empty($lastchat['attachment']))
												{{ "Image" }}
												@else
												{{ "location" }}
												@endif
											</span>
										</p>
									</div>
								</a>
								
							</div>
							<?php } ?>
							@endforeach
							<!-- chat list end -->
						</div>
					</div>
					
					
					<!--right-chat-->
					<div class="md:block w-full pl-0 md:w-7/12 lg:w-3/5 lg:pl-4 float-right chat_detail cursor-pointer" style="background-image:url('<?php echo URL::to('/images/chat-bg.png'); ?>');">
						@if(empty($details))
						<img class="m-auto w-40 mt-6 md:mt-20" src="<?php echo URL::to('storage/emptyChat.png'); ?>" />
						<p class="text-center mt-2 mb-6 md:mb-0 sm:mt-4 text-base md:text-lg text-black"><?php echo __('messages.Select a chat to view conversation'); ?></p>
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
						<!--<span class="py-3 px-4 block text-right close_btn block md:hidden ">
							<svg fill="currentColor" viewBox="0 0 20 20" class="inline-block w-6 h-6">
								<path x-show="open" fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
							</svg>
						</span>-->
						
						<div class="w-full float-left h-full relative">
							<div class="w-full bg-green-500 rounded-lg p-4 float-left">
								<div class="user_image w-10 lg:w-20 mr-2 float-left lg:mr-4 mt-3">
									<img class="w-10 lg:w-20 h-10 float-left lg:h-20 rounded-full object-cover" src="{{ $detail_seller_profile }}" alt="user.jpg">
								</div>
								<div class="user_detail pt-1 float-left lg:pt-4 md:w-6/12 xl:w-auto">
									<span class="font-bold text-lg text-white pb-1 inline-block capitalize">{{$sendername}}</span>
									<p class="text-sm"><a target="_blank" href="{{ URL::to('/'.$get_post->slug) }}" class="font-semibold text-sm lg:text-base text-white underline pb-1 inline-block">{{$get_post->title}}</a></p>
									<?php if (empty($check_blocked_user)) { ?>
										<span class="text-xs lastseen text-white"></span>
										
									<?php } ?>
								</div>
								<div class="w-3/12 float-right pb-0 pt-2 lg:p-4 lg:pt-6 lg:w-3/12">
									<span class="text-xs text-white float-right inline-block align-middle lg:pt-1">
										
										<span class="float-right">
											<button class="block focus:outline-none focus:outline-none ">
											<p class="focus:outline-none block_user text-base text-white cursor-pointer p-1"  data-block-status="<?php echo !empty($check_blocked_user) ? $check_blocked_user : 0 ?>" data-id="<?php echo request()->to . '@' . request()->p; ?>">
												@if($check_blocked_user == 0)
												Block
												@else
												Unblock
												@endif
											</p>
											</button>
										</span>
									</span>
									<span class="float-left inline-block align-middle text-white text-xl lg:text-3xl"><a href="#"><i class="fa fa-map-marker" aria-hidden="true"></i></a></span>
								</div>
							</div>
							
							
							<div class="w-full bg-white float-left">
								<!--converstion-->
								<div class="w-full chat_converstion p-4 pb-36 md:pb-4" data-selected-ids="<?php echo ($check_blocked_user == 0) ? $receiver_detail . "." . request()->p : "" ?>" id="messages_area">
									<div class="show_bottom h-full overflow-y-scroll">
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
											//$make_offer_msg = "Buyer Offer " . $currency_symbol[0] . $detail->msg . "<span class='text-green-500 float-right font-bold'> Accepted </span>";
											
											$make_offer_msg = $detail->msg;
											//$make_offer_msg = "Buyer Offer " . $currency_symbol[0] . $detail->msg . " Accepted ";
											
											//$meet_msg = "Let's move towards the final deal";
											?>
											@else
											<?php
											$make_offer_msg = $detail->msg;
											//$make_offer_msg = "Buyer Offer " . $currency_symbol[0] . $detail->msg . " Accepted ";
											//$meet_msg = "I accpet your offer, Let's move towards the final deal";
											?>
											@endif
											@endif
											<!-- denied offer -->
											@if($detail->denied_offer == 1)
											@if(auth()->user()->id == $get_post->user_id)
											<?php
											$make_offer_msg = "Buyer Offer " . $currency_symbol[0] . $detail->msg . " Denied ";
											$meet_msg = "offer";
											?>
											@else
											<?php
											$make_offer_msg = "Buyer Offer " . $currency_symbol[0] . $detail->msg . " Denied ";
											$meet_msg = "I denied your offer.!";
											?>
											@endif
											@endif
											<!-- denied offer -->
											@if($detail->from_id == auth()->user()->id)
											@if($detail->make_offer == 1)
											<?php $make_offer_msg = "Your Offer " . $currency_symbol[0].$detail->msg; ?>
											@endif
											<div class="w-full float-right">
												<div class="bg-gray-100 chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-br-3xl rounded-tr-none float-right mr-2">
													@if(!empty($detail->attachment))
													<div id="chat_conv_image" data-img="{{ URL::to('/storage/app/public/'.$detail->attachment) }}" target="_blank">
														<div class="w-36 h-36 lg:w-44 lg:h-44 xl:w-56 xl:h-56 flex items-center justify-center"><img class="max-w-full max-h-full" src="{{ URL::to('/storage/app/public/'.$detail->attachment) }}" /></div>
													</div>
													<p><time class="text-xs text-gray-500 float-right block text-right w-36 ">{{ $time }} <span><?php echo $tick ?></span></time></p>
													@else
													@if($detail->accept_offer == 1)
													<p class="text-base text-gray-800"><?php echo $make_offer_msg; ?></p>
													<p class="text-base text-gray-800"></p>
													@elseif($detail->denied_offer == 1)
													<p class="text-base text-gray-800"><?php echo $make_offer_msg; ?> </p>
													<p class="text-base text-gray-800"></p>
													@else
													<p class="text-base text-gray-800"><?php echo $make_offer_msg; ?>{{$detail->msg }}</p>
													@endif
													<time class="text-xs text-gray-500 float-right block text-right w-36 ">{{ $time }} <span><?php echo $tick ?></span></time>
													<div class="inline-block mt-3">
													@if(empty($get_accept_offer) && empty($get_denied_offer))
													<?php if (!empty($get_make_offer->id) && ($get_make_offer->id == $detail->id)) { ?>
														<?php if (($get_make_offer->from_id == auth()->user()->id) || ($get_make_offer->from_id == $detail->receiver)) { ?>
															<?php $offer_price = $detail->msg; ?>
															<div class="edit_offer_btn">
																<p class="cursor-pointer bg-green-500 text-white float-right hover:bg-white hover:text-black hover:border-green-500 shadow-lg border px-2 py-2 text-xs edit-offer mt-3 rounded-lg transition-all ease-linear duration-500" data-defcurency="<?php echo $currency_symbol[0]; ?>" data-youroffer="<?php echo $offer_price; ?>">Edit Offer</p>
															</div>
													<?php }
													} ?>
													@endif
													@if($detail->accept_offer == 1)
														<div class="float-left mr-2">
														<p class="bg-green-500 text-white denied-off-clean float-right hover:bg-white hover:text-black hover:border-green-500 shadow-lg border px-2 py-2 text-xs mt-3 rounded-lg transition-all ease-linear duration-500">Accepted</p>
														</div>
														@endif
														@if($detail->denied_offer == 1)
														<div class="float-left mr-2">
														<p class="bg-green-500 text-white denied-off-clean float-right hover:bg-white hover:text-black hover:border-green-500 shadow-lg border px-2 py-2 text-xs mt-3 rounded-lg transition-all ease-linear duration-500">Denied</p>
														</div>
														@endif

													</div>
													@endif
												</div>
											</div>
											@else
											@if($detail->make_offer == 1)
											@if($detail->to_id == $detail->receiver)
											<?php $make_offer_msg = "Seller Offer " . $currency_symbol[0].$detail->msg; ?>
											@else
											<?php $make_offer_msg = "Buyer Offer " . $currency_symbol[0].$detail->msg; ?>
											@endif
											@endif
											<div class="w-full float-left">
												<div class="chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl  rounded-tr-3xl rounded-br-none bg-green-500 float-left">
													<div class="block">
														@if(!empty($detail->attachment))
														<div id="chat_conv_image" data-img="{{ URL::to('/storage/app/public/'.$detail->attachment) }}" target="_blank">
															<div class="w-36 h-36 lg:w-44 lg:h-44 xl:w-56 xl:h-56 flex items-center justify-center">
																<img class="max-w-full max-h-full rounded m-auto" src="{{ URL::to('/storage/app/public/'.$detail->attachment) }}" />
															</div>
														</div>
														<p><time class="text-xs text-white float-right block text-right w-36 ">{{ $time }}</time></p>
														@else
														@if($detail->accept_offer == 1)
														<p class="text-base text-white"><?php echo $make_offer_msg; ?></p>
														<p class="text-base text-white"></p>
														@elseif($detail->denied_offer == 1)

														<p class="text-base text-white"><?php echo $make_offer_msg; ?></p>
														<p class="text-base text-white"></p>

														@else
														<p class="text-base text-white"><?php echo $make_offer_msg; ?>{{$detail->msg }}</p>
														@endif
														<time class="text-xs text-white float-right block text-right w-36 ">{{ $time }}</time>
													</div>
													
													<div class="inline-block mt-3 ">
														@if(empty($get_accept_offer) && empty($get_denied_offer))
														<?php if (!empty($get_make_offer->id) && ($get_make_offer->id == $detail->id)) { ?>
															<?php if (($get_make_offer->from_id == auth()->user()->id) || ($get_make_offer->from_id == $detail->receiver)) { ?>
																<?php $offer_price = $detail->msg; ?>
																
															<div class="float-left mr-2 edit_offer_btn">
																<p class="accept-offer bg-gray-300 hover:bg-transparent hover:text-white shadow-lg cursor-pointer border-2 border-gray-300 px-2 py-2 text-xs font-semibold rounded-lg transition-all ease-linear duration-500" a-state="{{$detail->accept_offer}}" p-id="{{$detail->id}}" data-defcurency="<?php echo $currency_symbol[0]; ?>" data-youroffer="<?php echo $offer_price; ?>">Accept Offer</p>
															</div>
															
																<!-- denied offer button -->
																
															<div class="float-left denied_offer_btn">
																		  
																<p class="denied-offer cursor-pointer bg-gray-300 hover:bg-transparent hover:text-white shadow-lg border-2 border-gray-300 px-2 py-2 text-xs font-semibold rounded-lg transition-all ease-linear duration-500" a-state="{{$detail->denied_offer}}" p-id="{{$detail->id}}" data-defcurency="<?php echo $currency_symbol[0]; ?>" data-youroffer="<?php echo $offer_price; ?>">Deny Offer</p>
															</div>
																
														<?php }
														} ?>
														@endif
														@if($detail->accept_offer == 1)
														<div class="float-left mr-2 " >
														<p class="float-left bg-gray-300 hover:bg-transparent hover:text-white cursor-pointer shadow-lg border-2 border-gray-300 px-2 py-2 text-xs font-semibold rounded-lg transition-all ease-linear duration-500">Accepted</p>
														</div>
														@endif
														@if($detail->denied_offer == 1)
														<div class="float-left mr-2 " >
														<p class="float-left bg-gray-300 hover:bg-transparent hover:text-white cursor-pointer shadow-lg border-2 border-gray-300 px-2 py-2 text-xs font-semibold rounded-lg transition-all ease-linear duration-500">Denied</p>
														</div>
														@endif
													</div>
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
												<div class="mb-2 w-full {{ $div_class }}">
												<div class="inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl mr-2 rounded-br-3xl rounded-tr-none bg-gray-100 {{ $div_class }}">
												<p class="{{ $div_class }}">
													<a class="viewShared w-36 h-36 lg:w-44 lg:h-44 xl:w-56 xl:h-56 inline-block" href="https://www.google.com/maps?daddr={{$lat}},{{$long}}" target="_blank">
														<img class="h-full w-full" src="https://maps.googleapis.com/maps/api/staticmap?center={{$lat}},{{$long}} ?>&amp;zoom=16&amp;size=400x200&amp;sensor=false&amp;maptype=roadmap&amp;markers=color:red%7Clabel:S%7C<?php echo $lat; ?>,<?php echo $long; ?>&amp;key=<?php echo $google_api_key;?>"></a>
												</p> 
												<p><time class="text-xs text-gray-500 float-right block text-right w-36 ">{{ $time }} 
													<?php if($detail->from_id == auth()->user()->id){?>
														<span><?php echo $tick ?></span>
													<?php } ?>
											
												</time></p>
												</div>
												</div>
											@endif
											@endforeach
										</div>
										@endforeach
									</div>
								</div>
								@if($check_blocked_user == 0)
								<!--question-->
								<div class="w-full float-left px-4 xl:px-6 py-4 pb-0 absolute bottom-0 left-0 right-0 bg-white">
								
									<div class="rounded-full text-2xl rounded-lg chat-area-adjust show-hide-redymade-chat text-center m-auto cursor-pointer absolute top-0 inset-x-1/3 w-11"><i class="fa fa-angle-down bg-green-500 text-white px-3 py-2 rounded-full" aria-hidden="true"></i></div>
									
											
									<div class="rounded w-full bg-white border-t border-gray-100">
										<!-- Tabs -->
										<ul id="tabs" class="flex items-center w-full border border-b-0 border-t-2 border-gray-100 shadow-lg">
											<li class="w-full sm:w-1/2 text-center border-b border-transparent rounded-lg chat-area-adjust-left"><a id="default-tab" class="block mx-1 py-3 text-sm lg:text-base text-black font-medium lg:font-semibold border-b-3 lg:border-b-4 border-transparent" href="#first">{{__('messages.questions')}}</a></li>
											<li class="w-full sm:w-1/2 text-center border-b border-transparent rounded-lg chat-area-adjust-right">
											<a class="block mx-1 py-3 text-sm lg:text-base text-black font-medium lg:font-semibold border-b-3 lg:border-b-4 border-transparent" href="#make-offer-chat">{{__('messages.make offers')}}</a></li>
										</ul>
										<!-- Tab Contents -->
										<div id="tab-contents" class="h-44 md:h-auto overflow-y-scroll md:overflow-auto">
											<div id="first" class="my-4 lg:my-6 px-2 rounded h-auto md:h-60 lg:h-72 md:overflow-y-scroll">
												<div class="w-full lg:w-3/4 xl:w-3/5 inline-block px-4">
													<div class="bg-green-500 p-3 rounded-xl rounded-tl-none relative mb-2 md:mb-4 xl:mb-6">
														
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
												
												@if($detail->accept_offer == 0)
												<ul class="list-unstyled inline-flex defalut-chat flex-wrap px-2">
													<li class="bg-white text-green-500 font-medium text-sm lg:text-base px-2 py-1 pb-2 lg:px-6 xl:px-8 lg:py-2 lg:pb-3 cursor-pointer mx-1 my-2 lg:m-2 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-3xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500" data-defmsg="hello">Hello</li>
													@if($get_post->user_id == auth()->user()->id)
													<li class="bg-white text-green-500 font-medium text-sm lg:text-base px-2 py-1 pb-2 lg:px-6 xl:px-8 lg:py-2 lg:pb-3 cursor-pointer mx-1 my-2 lg:m-2 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-3xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500" data-defmsg="it's available">It's available</li>
													@else
													<li class="bg-white text-green-500 font-medium text-sm lg:text-base px-2 py-1 pb-2 lg:px-6 xl:px-8 lg:py-2 lg:pb-3 cursor-pointer mx-1 my-2 lg:m-2 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-3xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500" data-defmsg="is it available?">Is it available?</li>
													@endif
													<li class="bg-white text-green-500 font-medium text-sm lg:text-base px-2 py-1 pb-2 lg:px-6 xl:px-8 lg:py-2 lg:pb-3 cursor-pointer mx-1 my-2 lg:m-2 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-3xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500" data-defmsg="okay">Okay</li>
													<li class="bg-white text-green-500 font-medium text-sm lg:text-base px-2 py-1 pb-2 lg:px-6 xl:px-8 lg:py-2 lg:pb-3 cursor-pointer mx-1 my-2 lg:m-2 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-3xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500" data-defmsg="no problem">No Problem</li>
													@if($get_post->user_id != auth()->user()->id)
													<li class="bg-white text-green-500 font-medium text-sm lg:text-base px-2 py-1 pb-2 lg:px-6 xl:px-8 lg:py-2 lg:pb-3 cursor-pointer mx-1 my-2 lg:m-2 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-3xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500" data-defmsg="please reply">Please reply</li>
													<li class="bg-white text-green-500 font-medium text-sm lg:text-base px-2 py-1 pb-2 lg:px-6 xl:px-8 lg:py-2 lg:pb-3 cursor-pointer mx-1 my-2 lg:m-2 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-3xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500" data-defmsg="not interested">Not interested</li>
													@endif
												</ul>
												@endif
											</div>
											<div id="make-offer-chat" class="hidden h-auto md:h-60 lg:h-72 my-4 lg:my-6 md:overflow-y-scroll">
												@if($get_post->user_id != auth()->user()->id && $detail->accept_offer != 1)
												<div class="w-full inline-block rounded  ">
													
													<?php
														$perc = 0;
														$offer = "";
														for ($j = 1; $j <= 4; $j++) {
															if ($post_price > 100) {
																$perc = $perc + 5;
																$offer = $post_price - round(($post_price * $perc) / 100);
																$lastPrice = ($j==4) ? $offer : "";
															} else {
																$offer = $post_price - $j;
																$lastPrice = ($j==4) ? $offer : "";
															}
														}
													?> 
															
													<div class="make-offer-design">
														<input type="hidden" class="offer-price" value="<?php echo floor($post_price); ?>" />
														<input type="hidden" class="default_currency" value="<?php echo $currency_symbol[0]; ?>" />
														<div class="w-full inline-block mb-4">
															<div class="w-full sm:w-1/2 float-left relative mb-4 sm:mb-0">
																<label class="text-black text-2xl font-bold absolute left-1 top-1.5"><?php echo $currency_symbol[0]; ?></label>
																<input type="number" currency="<?php echo $currency_symbol[0]; ?>" price="<?php echo floor($post_price); ?>" min="<?php echo $lastPrice; ?>" max="<?php echo floor($post_price); ?>" value="<?php echo floor($post_price); ?>" class="offer_input appearance:none px-4 py-2 pb-3 text-gray-800 text-xl font-medium bg-white rounded bg-white w-full shadow pl-6 outline-none focus:outline-none" />
															</div>
															<button type="submit" name="send" id="send" class="send bg-green-500 text-white font-semibold text-base border-2 border-green-500 px-8 py-2 pb-3 rounded-xl shadow hover:bg-white hover:text-green-500 outline-none focus:outline-none ease-linear transition-all duration-150 inline-block sm:w-auto w-full flaot-right">{{__('messages.send')}}</button>
															
														</div>
														
														<div class="w-full inline-block mb-4">
															<ul class="list-unstyled inline-flex flex-wrap">
																<li class="bg-white text-green-500 font-medium text-sm lg:text-base px-2 py-1 pb-2 lg:px-6 xl:px-8 lg:py-2 lg:py-3 cursor-pointer mx-1 my-2 lg:m-2 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-3xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500 price-active" data-defcurncy="<?php echo $currency_symbol[0]; ?>" data-defmsg="<?php echo floor($post_price); ?>"><?php echo $currency_symbol[0] . floor($post_price); ?></li>
																<?php
																$perc = 0;
																$offer = "";
																for ($j = 1; $j <= 4; $j++) {
																if ($post_price > 100) {
																$perc = $perc + 5;
																$offer = $post_price - round(($post_price * $perc) / 100);
																$lastPrice = ($j==4) ? $offer : "";
																} else {
																$offer = $post_price - $j;
																$lastPrice = ($j==4) ? $offer : "";
																}
																?>
																<li class="bg-white text-green-500 font-medium text-sm lg:text-base px-2 py-1 pb-2 lg:px-6 xl:px-8 lg:py-2 lg:py-3 cursor-pointer mx-1 my-2 lg:m-2 border-2 border-dashed border-green-500 rounded-lg lg:rounded-xl xl:rounded-3xl shadow-lg hover:text-white hover:bg-green-500 transition-all ease-linear duration-500" data-defcurncy="<?php echo $currency_symbol[0]; ?>" data-defmsg="<?php echo $offer; ?>"><?php echo $currency_symbol[0] . $offer; ?></li>
																<?php } ?>
															</ul>
														</div>
														
														<div class="w-full inline-block">
															<div class="w-full lg:w-3/4 xl:w-3/5 inline-block pl-4 pr-2 lg:px-4">
																<div class="bg-green-500 p-3 rounded-xl rounded-tl-none relative">
																	<div class="cnt-design absolute right-full top-0"></div>
																	<div class="w-full inline-block mb-4 sm:mb-0">
																		<h4 class="text-base lg:text-lg font-medium text-white mb-2">{{__('messages.very good offer')}}</h4>
																		<p class="mt-1 mb-2 font-medium text-xs lg:text-sm text-white">{{__('messages.high chances of sellers reply')}}</p>
																	</div>
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
									<div class="w-full bg-white pb-4 float-left rounded-t-lg relative">
										<form method="post" id="chat_form" name="chatform" onsubmit="return validateForm()" class="w-full" enctype="multipart/form-data">
										<input type="hidden" name="current_user_id" class="current_user" value="{{ auth()->user()->id }}" />
										<input type="hidden" name="post_id" class="post_id" value="{{ request()->p }}" />
										<input type="hidden" name="to" class="to" value="{{ request()->to }}" />
										<input type="hidden" class="receiver_id" value="{{ $receiver_detail }}" />
											<div class="bg-gray-100 p-5 rounded-lg chat-box relative">
											<input class="msg w-3/5 md:w-3/4 bg-gray-100 outline-none" autocomplete="off" type="text" id="chat_message" name="chat_message" placeholder="{{__('messages.type message')}}">
												<div class="w-2/11 md:w-2/6 absolute right-5 top-3">
													<input type="hidden" id="file" name="filename">
													<span class="text-xs bg-green-500  text-center  w-8 h-8  ml-2 leading-7  md:w-10 md:h-10 md:ml-3 md:leading-9 text-white float-right inline-block rounded-full"><button type="submit" name="send" id="send" class="send"><img class="inline-block" src="{{ URL::to('images/frontend/send.png') }}" alt="send.png"></button></span>

													<span class="text-xs bg-green-500  text-center  w-8 h-8  ml-2 leading-9  md:w-10 md:h-10 md:ml-3 md:leading-9 text-white float-right inline-block rounded-full">
														<button class="inline-block w-full text-xl md:mt-1 chat_share_btn" id="share_loc_btn"><i class="fa fa-map-marker text-2xl" aria-hidden="true"></i></button>
													</span>

													<span class="text-xs bg-green-500  text-center w-8 h-8  ml-2 leading-6  md:w-10 md:h-10 md:ml-3 md:leading-9 text-white float-right inline-block rounded-full ">
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

										</div>
									</div>
								</div>
								@endif
							</div>
						
					<!--right-chatend-->
					
					<!-- share location start -->
					<div class="fixed z-50 inset-0 overflow-y-auto" id="share_loc_popup" style="display:none">
						<div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
							<div class="fixed inset-0 transition-opacity" aria-hidden="true">
								<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
							</div>
							<!-- This element is to trick the browser into centering the modal contents. -->
							<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
							<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
								<div class="bg-white px-6 py-4 sm:px-12 sm:py-8">
									<div class="mb-6">
										<div class="text-left">
											<h3 class="block text-xl text-black font-semibold mb-2 sm:mb-4" id="modal-headline">
											{{__('messages.share location')}}
											</h3>
											<div class="mt-2">
												<div class="py-2">
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
									
									<div class="sm:flex sm:flex-row-reverse">
										<button type="button" class="chat_share_send_btn w-full inline-flex justify-center rounded-md border-2 border-transparent shadow-sm px-4 py-2 pb-3 bg-green-500 text-base font-semibold text-white hover:bg-white hover:border-green-500 hover:text-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-all ease-in-out duration-500" id="submit_shared_loc">{{__('messages.share')}}</button>
										
										<button type="button" id="share_loc_cancel" class="chat_share_close mt-3 w-full inline-flex justify-center rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 pb-3 bg-white text-base font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all ease-in-out duration-500">{{__('messages.cancel')}}</button>
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
						@endif
					</div>
					
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
						var denied_offer = "";
						if (data.make_offer != "") {
							if (data.from == "Me") {
								offer_msg = "Your offer " + data.currency;
								edit_offer = '<div class="edit_offer_btn"><p class="cursor-pointer bg-green-500 text-white float-right hover:bg-white hover:text-black hover:border-green-500 shadow-lg border px-2 py-2 text-xs edit-offer mt-3 rounded-lg transition-all ease-linear duration-500" data-defcurency="' + data.currency + '" data-youroffer="' + data.msg + '">Edit Offer</p></div>';
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
									$(".denied-offer").hide();
									$('.edit_offer_btn').hide();
									if ("<?php echo auth()->user()->id; ?>" == data.post_user_id) {
										var show_offer_msg = "Buyer Offer " + data.currency + data.msg + "<span class='text-green-500 float-right font-bold'> Accepted </span>";
										var meet_msg = "Let's move towards the final deal";
									} else {
										var show_offer_msg = "Buyer Offer " + data.currency + data.msg + "<span class='text-green-500 float-right font-bold'> Accepted </span>";
										var meet_msg = "I accpet your offer, Let's move towards the final deal";
									}
									html_data = '<div class="w-full float-right"><div class="bg-gray-100 chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-br-3xl rounded-tr-none float-right"><p class="text-base text-gray-800"><p class="text-base text-gray-800">' + show_offer_msg + '</p><p class="text-base text-gray-800"> ' + meet_msg + '</p><time class="text-xs text-gray-500 float-right block text-right w-36 ">' + time + '<span>' + tick + '</span></time></div>';
								} 
								else if(data.denied_offer != "")
								{   
									// denied offer
									$(".accept-offer").hide();
									$(".denied-offer").hide();
									$('.edit_offer_btn').hide();
									if ("<?php echo auth()->user()->id; ?>" == data.post_user_id) {
										var show_offer_msg = "Buyer Offer " + data.currency + data.msg + "<span class='text-green-500 float-right font-bold'> Denied </span>";
										var meet_msg = "Denied offer";
									} else {
										var show_offer_msg = "Buyer Offer " + data.currency + data.msg + "<span class='text-green-500 float-right font-bold'> Denied </span>";
										var meet_msg = "Denied offer";
									}
									html_data = '<div class="w-full float-right"><div class="bg-gray-100 chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-br-3xl rounded-tr-none float-right"><p class="text-base text-gray-800"><p class="text-base text-gray-800">' + show_offer_msg + '</p><p class="text-base text-gray-800"> ' + meet_msg + '</p><time class="text-xs text-gray-500 float-right block text-right w-36 ">' + time + '<span>' + tick + '</span></time></div>';
									// denied offer

								}else {
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
								} 
								else if(data.denied_offer != ""){
									// denied
									if ("<?php echo auth()->user()->id; ?>" == data.post_user_id) {
										var show_offer_msg = "Buyer Offer " + data.currency + data.msg + "<span class='text-green-500 float-right font-bold'> Denied </span>";
										var meet_msg = "Denied offer";
									} else {
										var show_offer_msg = "Buyer Offer " + data.currency + data.msg + "<span class='text-green-500 float-right font-bold'> Denied </span>";
										var meet_msg = "Denied offer";
									}
									html_data = '<div class="w-full float-left"><div class="bg-gray-100 chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-br-3xl rounded-tr-none float-right"><p class="text-base text-gray-800"><p class="text-base text-gray-800">' + show_offer_msg + '</p><p class="text-base text-gray-800"> ' + meet_msg + '</p><time class="text-xs text-gray-500 float-right block text-right w-36 ">' + time + '<span>' + tick + '</span></time></div>';  
							   // denied
								}else {
									html_data = '<div class="w-full message float-left"><div class="bg-green-500 chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-tr-3xl rounded-br-none"><p class="text-base text-white">' + offer_msg + data.msg + '</p><time class="text-xs text-white float-right block text-right w-36 ">' + time + '</time></div></div>';
								}
							}
							if (data.image != "") {
								html_data = '<div class="w-full message float-left"><div class="bg-green-500 chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-tr-3xl rounded-br-none"><img class="w-24 h-24 m-auto object-none base64-img cursor-pointer" src="' + data.image + '"/><time class="text-xs text-white float-right block text-right w-36 ">' + time + '</time></div></div>';
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

			// last seen process
				//scroll to bottom
				$(window).on('load', function() { CallScrollToBottom(); });

				function CallScrollToBottom()
				{
					console.log("scrolled to bottom");
					var elem = document.getElementById('messages_area');
					if(elem!=null){ elem.scrollTop = elem.scrollHeight; getLastSeen(); }    
				}

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
						url:"{{ URL::to('send_chat_update_last_seen') }}",
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
					var _uri = "{{ URL::to('send_chat_fetch_last_seen') }}?id={{request()->to}}";
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

			// last seen process

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
							denied_offer: "",
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
							denied_offer: "",
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
					var denied_offer = "";
					var data = {
						from: current_user,
						to: to,
						msg: message,
						post_id: post_id,
						receiveid: reciv_id,
						make_offer: "",
						accept_offer: "",
						denied_offer: "",
						post_user_id: "",
						currency: ""
					};
					send_default_chat(message, to, post_id, make_offer, accept_offer, denied_offer,'0');
					conn.send(JSON.stringify(data));
				});

				//offer input 
				$(".offer_input").on("keyup", function(e) {
					var inputVal = $(this).val();
					var price = $(this).attr('price');
					var min = $(this).attr('min');
					var max = $(this).attr('max');
					var get_currency = $(this).attr('currency');
					
					if(inputVal == "" ) {
						//alert(get_currency + price);
						$(".offer-price").text(get_currency + price);
						$(".offer-price").val(price);
					}
					else{
						$(".make-offer-design .offer-price").text(get_currency + inputVal); // curency symbol need to change
					$(".make-offer-design .offer-price").val(inputVal);
					}

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
					var denied_offer = "";

					var msg = parseInt($(".offer-price").val());
					var min = parseInt($(".offer_input").attr("min"));
					var max = parseInt($(".offer_input").attr("max"));

					if(msg >= min && msg <= max)
					{

					var data = {
						from: current_user,
						to: to,
						msg: message,
						post_id: post_id,
						receiveid: reciv_id,
						make_offer: "yes",
						accept_offer: "",
						denied_offer: "",
						post_user_id: "",
						currency: currency
					};
					send_default_chat(message, to, post_id, make_offer, accept_offer, denied_offer,'0');
					conn.send(JSON.stringify(data));
				}else{
					toastr.success("Enter price between "+ min + "-" + max);
				}

				});
				$("body").delegate(".accept-offer", 'click', function(e) {
					e.preventDefault();
					var pri = $(this).attr('data-youroffer');
					var currency = $(this).attr('data-defcurency');
					var to = $(".to").val();
					var post_id = $(".post_id").val();
					var reciv_id = $(".receiver_id").val();
					var current_user = $(".current_user").val();
					var accept_offer = "yes";
					var denied_offer = "";
					var post_user_id = $(this).attr('p_uid');
					var row_id = $(this).attr('p-id');
					// var accept_state =  $(this).attr('a-state');
					var message = "Offer "+currency+pri+" Accepted. \n Let's move towards the final deal.";
						
					var data = {
						from: current_user,
						to: to,
						msg: message,
						post_id: post_id,
						receiveid: reciv_id,
						make_offer: "",
						accept_offer: "yes",
						denied_offer: "",
						post_user_id: post_user_id,
						currency: currency
					};
					send_default_chat(message, to, post_id, "", accept_offer, denied_offer,row_id);
					conn.send(JSON.stringify(data));

					//remove buttons
					$('.accept_offer_btn').attr('a-state','1');
					$('div.accept_offer_btn > p').html("Accepted");
					$('#messages_area').find(".accept_offer_btn").removeClass('accept_offer_btn');
					$('.denied_offer_btn').remove(); 
				
				});

				$("body").delegate(".denied-offer", 'click', function(e) {
					e.preventDefault();
					var pri = $(this).attr('data-youroffer');
					var currency = $(this).attr('data-defcurency');
					var to = $(".to").val();
					var post_id = $(".post_id").val();
					var reciv_id = $(".receiver_id").val();
					var current_user = $(".current_user").val();
					var accept_offer = "";
					var denied_offer = "yes";
					var post_user_id = $(this).attr('p_uid');
					var row_id = $(this).attr('p-id');
					// var accept_state =  $(this).attr('a-state');
					var message = "Offer "+currency+pri+" Denied.";
						
					var data = {
						from: current_user,
						to: to,
						msg: message,
						post_id: post_id,
						receiveid: reciv_id,
						make_offer: "",
						accept_offer: "",
						denied_offer: "yes",
						post_user_id: post_user_id,
						currency: currency
					};
					send_default_chat(message, to, post_id, "", accept_offer, denied_offer,row_id);
					conn.send(JSON.stringify(data));
					
					//remove buttons
						$('.denied_offer_btn').attr('a-state','1');
						$('div.denied_offer_btn > p').html("Denied");
						$('#messages_area').find(".denied_offer_btn").removeClass('denied_offer_btn');
						$('.edit_offer_btn').remove();
				
				});

			});

			function send_default_chat(message, to, post_id, make_offer, accept_offer, denied_offer,row_id) {
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
						make_offer: make_offer,
						denied_offer: denied_offer,
						row_id:row_id
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
		
		<script>
		jQuery(document).ready(function(){
		$(".user_chat_button button").click(function() {
			$(".user_chat_block").toggleClass("hidden")
		});
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
		});
		
		
		$("body").delegate("#chat_conv_image","click",function(){
			var imgSrc = $(this).attr("data-img");
			document.querySelector("#chat_conv_image_popup").style.display = "block";
			document.querySelector("#image_pop_up").src = imgSrc;
		});
		

		$('#chat_conv_image_close').on('click', function(e) { closeConvimagePopUp(); });
		function closeConvimagePopUp(){ document.querySelector("#chat_conv_image_popup").style.display = "none"; }
		
		
		</script>
		
		<script src="{{ URL::to('js/chat.js') }}"></script>
	</div>