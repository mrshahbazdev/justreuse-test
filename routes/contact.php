<?php 				
	$imgstr = $pageinfo[0]["background_image"];	
	if($imgstr=="" || $imgstr=="no-image.png"){$imageUrl = base_url()."images/no-image.png";}	
	else{$imageUrl = base_url()."images/page/background/".$imgstr;}		
?>
<div class="col-sm-12 banner">
	<div class="row">
		<div class="col-sm-12 innerpage">
			<div class="innerpage_banner" style="background-image:url('<?php echo $imageUrl;?>');"></div>
			<div class="inner_banner_content">
				<div class="inner_detail">
					<h1><?php echo $pageinfo[0]["title"];?></h1>
				</div>
			</div>
			<div class="bottom_shape_inner">
				<img src="<?php echo base_url('assets/css/images/blog/treepage.png');?>" alt="treepage.png" style="width:100%;" /> 
			</div>
		</div>
	</div>
</div>
<section>		
<!-----------sesmas-tree-service----------->
<?php 
	/* $contact_blockimg=$pageinfo[0]['images'];			
	if(($contact_blockimg == "")||($contact_blockimg =="no-image.png")){
		$contact_blockimage=base_url().'images/no-image.png';
	}else{
		$contact_blockimage=base_url().'images/page/image/'.$contact_blockimg;
	} */
?>	
<div class="col-sm-12 contact_tree_service">
	<div class="tree_ser_bg" style="background-image:url('<?php //echo $contact_blockimage;?>');">
	</div>
	<div class="container">
		<div class="col-sm-12 tree_ser_form">
		<?php 
		/* $phone_number= $this->cms_model->getadminsettingsbykey('phone_number', 'setting_value');
		$address= $this->cms_model->getadminsettingsbykey('address', 'setting_value');
		$fax= $this->cms_model->getadminsettingsbykey('fax', 'setting_value'); */
		
		?>	
			<!--<div class="col-sm-5 tree_service_detail">
				<h5>Sesmas Tree Service</h5>
				<div class="tree_serdetail_hole">
					<div class="service_address">
						<h4>Our Location</h4>
						<p><?php //echo $address;?></p>
					</div>
					<div class="service_address service_phone">
						<h4>Phone</h4>
						<a href="tel:<?php //echo $phone_number;?>"><?php //echo $phone_number;?></a>
					</div>
					<div class="service_address service_Fax">
						<h4>Fax</h4>
						<p><?php //echo $fax;?></p>
					</div>
				</div>
			</div>-->
			<?php $site=$this->cms_model->get_adminsettings('recaptcha-site-key');	
				$recaptcha_site_key=$site[0]['setting_value']; 
			?>
			<div data-aos="zoom-in" class="col-sm-7 estimate_form">
				<?php echo $pageinfo[0]["description"];?>
					<?php
						if(($this->session->flashdata('item'))) {
						$message = $this->session->flashdata('item');
					  ?>
						<div class="<?php echo $message['class'];?>"><?php echo $message['message']; ?></div>
					<?php } ?>	
				<form method="post" action="<?php echo base_url('/contact/contactform')?>">	
					<div class="col-sm-12 form-group">
						<label>Full Name</label>
						<input type="text" class="form-control" id="name" placeholder="Enter your full name" name="name"required />
						<span class="required">*</span>
						<div class="error-form" style="color:red">
							<?php echo form_error('name','*'); ?>
						</div>
					</div>
					<div class="col-sm-12 form-group">
						<label>Email</label>
						<input type="email" class="form-control" id="email" placeholder="Enter your Email" name="email" required />
						<span class="required">*</span>
						<div class="error-form" style="color:red">
							<?php echo form_error('email','*'); ?>
						</div>
					</div>
					<div class="col-sm-12 form-group">
						<label>Phone Number</label>
						<input type="number" class="form-control" id="phonenumber" placeholder="Enter your Phone number" name="phonenumber"  required />
						<span class="required">*</span>
						<div class="error-form" style="color:red">
							<?php echo form_error('phone_number','*'); ?>
						</div>
					</div>
					<div class="col-sm-12 form-group">
						<label>Address</label>
						<input type="text" class="form-control" id="address" placeholder="Enter your Address" name="address"  />
						<span class="required">*</span>
						<div class="error-form" style="color:red">
							<?php echo form_error('address','*'); ?>
						</div>
					</div>
					<div class="col-sm-12 custom-select">
						<label>Services Interested in ?</label>
						<select name="service" required >
							<?php 
							
							//print_r($services); exit;
							
							$service_names = array(); 
							
							foreach ($services as $serdata) {
								$service_names[] = $serdata->servicename; //any object field
							}

							//array_multisort($names, SORT_ASC, $my_array);

							asort($service_names);
							
							
							foreach($service_names as $serdata){ ?>
								<option value="<?php echo $serdata; ?>"><?php echo $serdata; ?></option>
							<?php }?>							
						</select> 
						<span class="input_icon"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
					</div>					<div class="col-sm-12 form-group text_message">						<label>Message</label>						<textarea class="form-control" name="message" placeholder="Your message..." rows="5" cols="50" id="comment" required /></textarea>											</div>
					<div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_site_key;?>"></div>
					<div class="col-sm-12 sumbit_btn">
						<button type="submit" class="square_btn blue-button"><span>Send Message</span></button>
					</div>	
				</form>
			</div>
		</div>
	</div>
</div>
</section>