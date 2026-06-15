<?php

	$dir_rtl =  App\Models\Setting::is_dir_rtl();
	$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
	
	$class_dir_float_lr = ($dir_rtl=="true")?'float-right':'float-left';
	$class_dir_float_rl = ($dir_rtl=="true")?'float-left':'float-right';
	$class_dir_text_rl = ($dir_rtl=="true")?'text-left':'text-right';
	$class_dir_mr_lr = ($dir_rtl=="true")?'mr-2':'ml-2';
	$class_dir_mr_rl = ($dir_rtl=="true")?'ml-2':'mr-2';
	
?>
                     
<?php $get_post = App\Models\TblPost::where('id', request()->p)->first(); ?>
<?php $currency_symbol = App\Models\TblPost::get_post_currency($get_post->currency_id); ?>


                     @foreach ($details as $date => $detail)                     
                                
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
					<time class="text-xs text-gray-500 {{$class_dir_float_rl}} block {{$class_dir_text_rl}} w-36"><?php echo $time; ?><?php echo $tick; ?></time>
						<?php if ($detail->make_offer == 1) { ?>
					@if($detail->accept_offer != 1 || $detail->denied_offer !=1)
						<div class="edit_offer_btn" p-id="{{$detail->id}}" o-state="0"  d-state="0" data-defcurency="<?php echo $currency_symbol[0]; ?>" data-youroffer="<?php echo $detail->msg; ?>">
						<p class="bg-green-500 text-white {{$div_flow}} hover:bg-white hover:text-black hover:border-green-500 shadow-lg border px-2 py-2 text-xs edit-offer mt-3 rounded-lg transition-all ease-linear duration-500" >Edit Offer</p>
						</div>
					@endif    
						<?php } ?>

				</div>
            </div>
            <?php } else {         
            $colorclass = "bg-green-500";
            $textcolor = "text-white";
            ?>
            <div class="w-4/5 {{$class_dir_float_lr}} {{$read_state}}"  id="{{$detail->id}}">
				<div class="{{$colorclass}} chat_message inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl rounded-tr-3xl rounded-br-none">
					<div class="block">
						<p class="text-base {{$textcolor}}"><?php echo $make_offer_msg.$detail->msg; ?></p>
						<time class="text-xs text-white {{$class_dir_float_rl}} block {{$class_dir_text_rl}} w-36 "><?php echo $time; ?></time>
					</div>

				<?php if ($detail->make_offer == 1) { ?>
					<div class="inline-block mt-3 ">
					@if($detail->denied_offer!=1)
					<div class="accept_offer_btn float-left mr-2" p-id="{{$detail->id}}" a-state="0" data-defcurency="<?php echo $currency_symbol[0]; ?>" data-youroffer="<?php echo $detail->msg; ?>">
															
						<p class="accept-offer {{$div_flow}} bg-gray-300 hover:bg-transparent hover:text-white shadow-lg border-2 border-gray-300 px-2 py-2 text-xs font-semibold rounded-lg transition-all ease-linear duration-500">Accept Offer</p>
					</div>
					@endif
					@if($detail->accept_offer!=1)    
					<div class="denied_offer_btn float-left" p-id="{{$detail->id}}" a-state="0" data-defcurency="<?php echo $currency_symbol[0]; ?>" data-youroffer="<?php echo $detail->msg; ?>">
					<p class="denied-offer {{$div_flow}} bg-gray-300 hover:bg-transparent hover:text-white shadow-lg border-2 border-gray-300 px-2 py-2 text-xs font-semibold rounded-lg transition-all ease-linear duration-500">Deny Offer</p>
					</div>
					@endif
					</div>
				<?php } ?>

				</div>
            </div>

            <?php } } ?>
            <!--end message area-->

                    <!-- location  -->
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

						<!-- image -->
                        <?php if ($detail->attachment != "") { ?>
                        <div class="mb-2 w-full {{$div_flow}} {{$read_state}}" id="{{$detail->id}}">
                            <div class="inline-block py-2 lg:py-4 px-5 my-4 rounded-3xl {{$mar_r}} {{$corner}} {{$bg}} {{$div_flow}}">
							<?php $image_url = URL::to('storage/' . $detail->attachment); ?>
                            
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

                    @endforeach