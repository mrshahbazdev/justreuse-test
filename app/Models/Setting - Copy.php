<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\EmailTemplate;
use App\Models\User;
use Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Setting extends Model
{

	use HasFactory;

	protected $fillable = [
		'name', 'key', 'value', 'description', 'field', 'parent_id', 'lft', 'rgt', 'depth', 'active', 'default_currency',
	];

	public static function get_logos()
	{
		$settings = Setting::where('key', 'app')->pluck('value');
		$values = json_decode($settings[0], true);
		return [
			'fav_icon' => $values['favicon'],
			'logo' => $values['app_logo'],
			'admin_logo' => $values['admin_logo'],
			'watermark' => $values['app_watermark'],
			'default_currency' => $values['default_currency'],
			'name' => $values['site_title'],
			'meta_title' => $values['meta_title'],
			'meta_desc' => $values['meta_desc'],
			'meta_keywords' => $values['meta_keywords'],
		];
	}

	public static function get_image_size_settings()
	{
		$settings = Setting::where('key', 'image_size_settings')->pluck('value');
		$values = json_decode($settings[0], true);
		return ['list' => $values['list_page'], 'detail' => $values['detail_page'], 'max_image_limit' => $values['max_image_limit']];
	}

	public static function get_admin_default_currency()
	{
		// $settings = Setting::where('key', 'app')->pluck('value');
		// $values = json_decode($settings[0], true);
		// $default_currency = TblCurrency::where('id', $values['default_currency'])->first();
		// return $default_currency;


		$settings = Setting::where('key', 'app')->pluck('value');
		$values = json_decode($settings[0], true);
		$default_currency = TblCurrency::where('id', $values['default_currency'])->first();

		//get current country
		$current_ip = $_SERVER["REMOTE_ADDR"];
		//$url= "http://ipinfo.io/".$current_ip;
		$url = "http://www.geoplugin.net/json.gp?ip=" . $current_ip;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpcode == 200) {
			$j  = json_decode($output);
			$country_name = $j->geoplugin_countryName;
			if ($country_name != "" || $country_name != null) {
				$get_new =  TblAdminCountry::where('name', $country_name)->first();
				if ($get_new!=null && $get_new->currency_code != "") {
					$default_currency = TblCurrency::where('short_code', $get_new->currency_code)->first();
				}
			}
		}

		//get current country
		return $default_currency;
	}


	public static function notification_mail_old($mail_data, $mail_key)
	{
		return "success";
	}

	public static function notification_mail($mail_data, $mail_key)
	{

		if ($mail_key == "new_registration" || $mail_key == "app_forgot_password" || $mail_key == "success_mail" || $mail_key == "post_comment" || $mail_key == "post_chat" || $mail_key == "new_follower" || $mail_key == "post_exchange_request" || $mail_key == "post_exchange_status" || $mail_key == "post_exchange_success" || $mail_key == "post_buy_now_request" || $mail_key == "post_buy_now_success" || $mail_key == "make_offer" || $mail_key == "banner_ad_approve" || $mail_key == "banner_ad_refund" || $mail_key == "post_buy_now_cancel" || $mail_key == "account_deleted" || $mail_key == "admin_account_deleted" || $mail_key == "admin_account_activated" || $mail_key == "post_expire_today" || $mail_key == "post_expire_yesterday" || $mail_key == "block_post" || $mail_key == "unblock_post") {

			$to_user_id = $mail_data['send_maildata']['to_id'];
			$eT = EmailTemplate::where('key', $mail_key)->get()->first();
			$content = $eT->html_content;
			$settings = Setting::get_logos();
			$get_to_user = User::where('id', $to_user_id)->first();


			$mail_body_logo = asset('storage/' . $settings['logo']);
			$site_name = $settings['name'];
			$to_user_name = $get_to_user->name;                          //dynamic  1
			$message_con = !empty($mail_data['send_maildata']['message']) ? $mail_data['send_maildata']['message'] : "";      //dynamic 1 - 
			$dynamic_mail_subject = !empty($mail_data['send_maildata']['subject']) ? " - " . $mail_data['send_maildata']['subject'] : "";
			if ($mail_key == "new_registration" || $mail_key == "app_forgot_password") {
				$subject = $eT->subject_title;
			} else {
				$subject = $eT->subject_title . $dynamic_mail_subject;
			}                   //dynamic  1 -
			$ad_url = $mail_data['send_maildata']['ad_url'];                //dynamic 1 -


			$content = str_replace('[[site_logo]]', $mail_body_logo, $content);
			$content = str_replace('[[site_name]]', $site_name, $content);
			$content = str_replace('[[to_user_name]]', $to_user_name, $content);
			$content = str_replace('[[message_content]]', $message_con, $content);
			$content = str_replace('[[subject_title]]', $subject, $content);
			if(!empty($ad_url)){
				$content = str_replace('[[ad_url]]', $ad_url, $content);
			}
			

			$result = Mail::send(
				'livewire.admin.email-template.smtp',
				array('content' => $content),
				function ($message) use ($get_to_user, $subject) {
					$settings = Setting::where('key', 'app')->get()[0];
					$admin_mail = json_decode($settings->value);
					//$subject = "new post created";   //dynamic
					$message->from($admin_mail->smtp_mail_username);
					$message->to($get_to_user->email)->subject($subject);
				}
			);

			return $result;
		} else if ($mail_key == "contact_us") {

			$eT = EmailTemplate::where('key', $mail_key)->get()->first();
			$content = $eT->html_content;
			$settings = Setting::get_logos();


			$mail_body_logo = asset('storage/' . $settings['logo']);
			$site_name = $settings['name'];
			$user_name = $mail_data['send_maildata']['name'];
			$email = $mail_data['send_maildata']['email'];
			$phone = $mail_data['send_maildata']['phone'];
			$ad_link = $mail_data['send_maildata']['ad_link'];
			$description = $mail_data['send_maildata']['description'];
			$subject = $eT->subject_title . " - Your Site Contect Form";
			$ad_url = URL::to('admin/contact-us');



			$content = str_replace('[[site_logo]]', $mail_body_logo, $content);
			$content = str_replace('[[site_name]]', $site_name, $content);
			$content = str_replace('[[user_name]]', $user_name, $content);
			$content = str_replace('[[email]]', $email, $content);
			$content = str_replace('[[phone]]', $phone, $content);
			$content = str_replace('[[ad_link]]', $ad_link, $content);
			$content = str_replace('[[description]]', $description, $content);
			$content = str_replace('[[subject_title]]', $subject, $content);
			$content = str_replace('[[ad_url]]', $ad_url, $content);

			$result = Mail::send(
				'livewire.admin.email-template.smtp',
				array('content' => $content),
				function ($message) use ($subject) {
					$settings = Setting::where('key', 'app')->get()[0];
					$admin_mail = json_decode($settings->value);

					$message->from($admin_mail->smtp_mail_username, 'Admin');
					$message->to($admin_mail->email)->subject($subject);
				}
			);

			return $result;
		} else if ($mail_key == "invite_friend") {

			$eT = EmailTemplate::where('key', $mail_key)->get()->first();
			$content = $eT->html_content;
			$settings = Setting::get_logos();


			$mail_body_logo = asset('storage/' . $settings['logo']);
			$site_name = $settings['name'];
			$description = $mail_data['send_maildata']['message'];
			$subject = $eT->subject_title . " - " . $mail_data['send_maildata']['subject'];
			$ad_url = $mail_data['send_maildata']['ad_url'];
			$to_email = $mail_data['send_maildata']['to_id'];



			$content = str_replace('[[site_logo]]', $mail_body_logo, $content);
			$content = str_replace('[[site_name]]', $site_name, $content);
			$content = str_replace('[[message_content]]', $description, $content);
			$content = str_replace('[[subject_title]]', $subject, $content);
			$content = str_replace('[[ad_url]]', $ad_url, $content);

			$result = Mail::send(
				'livewire.admin.email-template.smtp',
				array('content' => $content),
				function ($message) use ($subject, $to_email) {
					$settings = Setting::where('key', 'app')->get()[0];
					$admin_mail = json_decode($settings->value);

					$message->from($admin_mail->smtp_mail_username, 'Admin');
					$message->to($to_email)->subject($subject);
				}
			);

			return $result;
		} else {
			return $result = "failed";
		}
	}




	//for uuid working
	public $incrementing = false;
	protected $keyType = 'string';

	public static function boot()
	{
		parent::boot();

		static::creating(function ($model) {
			$model->{$model->getKeyName()} = (string) Str::uuid();
		});
	}
	
	
	public static function getImage($post_id,$type)
    {
		$default_img = URL::to('storage/noimage150.png');
		
        $images = TblPost::where('id', $post_id)->pluck('images')->first();
        if (!empty($images)) {
            $imgURLs = explode(',', $images)[0];
            $exp_imgURLs = explode('/', $imgURLs);
            $imgName = end($exp_imgURLs);

			if($type=="normal"){
				$checkIt = public_path('/storage/adpost/predefined/normal/'.$imgName);
				$imgUrl = URL::to('storage/adpost/predefined/normal/' . $imgName);
			}
			else if($type="medium")
			{
				$checkIt = public_path('/storage/adpost/predefined/'.$imgName);
				$imgUrl = URL::to('storage/adpost/predefined/' . $imgName);
			}
			else if($type="list")
			{
				$checkIt = public_path('/storage/adpost/predefined/list/'.$imgName);
				$imgUrl = URL::to('storage/adpost/predefined/list/' . $imgName);
			}
			else{
				$checkIt = public_path('/storage/adpost/predefined/list/'.$imgName);
				$imgUrl = URL::to('storage/adpost/predefined/list/' . $imgName);
			}
			

			if(file_exists($checkIt)) {                                            
				$imgUrlfinal = $imgUrl;
			}
			else{
				$imgUrlfinal = $default_img;
				
			}

        } else {
            $imgUrlfinal = $default_img;
        }

        return $imgUrlfinal;
    }



	public static function htmlAdBlock($post_id)
	{

		$dir = static::is_dir_rtl();

		$post_det = TblPost::where('id',$post_id)->get(); 
		$city_info = TblCity::where('id',$post_det[0]->city)->get();
		
		$imgUrlfinal = TblChat::getPostImg($post_id);
		$posted_on = date('d M Y', strtotime($post_det[0]->created_at));
		$slug = URL::to('/'.$post_det[0]->slug);
		$ad_type = TblPost::getAddtype($post_id);
		$ad_type = ($ad_type=="")?"":str_replace('_', ' ', strtoupper($ad_type->ad_type));


		$fav_style = TblSavedPosts::check_fav($post_id);
		$hearty = ($fav_style==true)?'fa-heart':'fa-heart-o';
		$final_city_name = !empty($post_det[0]->locality) ? $post_det[0]->locality : $city_info[0]->name; // get locality & city
		$get_categoryname = TblCategory::getCategoryName($post_det[0]->category_id);
		
		/* show the curreny symbol */
		$settings = Setting::get_logos();
		$slected_currency = !empty($post_det[0]->currency_id) ? $post_det[0]->currency_id : $settings['default_currency'];
		$currency_symbol = TblPost::get_post_currency($slected_currency);
		$currency = $currency_symbol[0];


		$post_title = $post_det[0]->title;
		$post_title_short = mb_strimwidth($post_det[0]->title, 0, 25, "...");
		$post_price = $post_det[0]->price;

		$get_categoryname_short = mb_strimwidth($get_categoryname, 0, 15, "..");
		//$location_icon = URL::to(''). 'images/frontend/Group111.png';
		$location_icon = URL::to('images/frontend/Group111.png');
		$city_name_short =  mb_strimwidth($final_city_name, 0, 25,"...");


		$logged_user_id = Auth::id();
		$post_user_id = $post_det[0]->user_id;

		$html='';


		$html .='		
		<div class="w-full float-left relative px-1 md:px-2">
			<div class="w-full float-left hover:shadow-lg ease-linear transition-all duration-500 border border-gray-200">
				<div class="w-full float-left overflow-hidden relative">
					<div class="items-center">
						<div class="absolute left-0 top-0 z-10">
							<h3 class="text-xs text-white font-semibold relative">';

								if($ad_type!=""){
								$html .='<span class="bg-yellow-500 px-2 py-1 rounded-tr-lg rounded-br-lg uppercase inline-block">'.$ad_type.'</span>';
								}
							$html .='</h3>
						</div>
						<div class="absolute right-0 top-0 z-10">
							<button type="button" id="favourate_post_id_'.$post_id.'" data-fav-post-id="'.$post_id.'" value="'.$post_title.'" class="bg-green-500 text-white w-8 h-8 save_favourate rounded-tl-lg rounded-bl-lg  text-center focus:outline-none border-0 save_favourate">
							<i class="fa '.$hearty.'" aria-hidden="true"></i>
							</button>
						</div>
					</div>
					<a class="block" href="'.$slug.'">
						<div class="flex items-center justify-center h-48 md:h-60">
						<img alt="'.$post_title.'" src="'.$imgUrlfinal.'" class="transform hover:scale-125 transition duration-500 ease-in-out m-auto max-h-full object-contain h-screen w-screen">
						<!--img alt="'.$post_title.'" src="'.$imgUrlfinal.'" class="w-full transform hover:scale-125 transition duration-500 ease-in-out h-80 w-80 object-cover object-center"-->
						</div>
					</a>
				</div>
				<div class="w-full float-left p-2 md:px-2 lg:px-3 xl:px-4">
				<a href="'.$slug.'">
					<h4 class="font-sans text-gray-600  hover:text-green-500 transition duration-500 ease-in-out truncate h-7 text-base md:text-base capitalize ">'.$post_title_short.'</h4>';
				
				if($dir=="false"){
				$html.='<p class="mb-2 sm:mt-0.5 sm:my-2 text-base w-full inline-block flex justify-between"><span class="text-black font-bold block lg:inline">'.$currency.$post_price.'</span> <span class="lg:float-right lg:text-right lg:w-2/5 xl:w-1/2 text-gray-600 block h-6 truncate">'.$get_categoryname_short.'</span></p>
				<p class="text-gray-600 bg-left bg-contain bg-no-repeat pl-5 truncate h-4 text-xs" style="background-image: url('.$location_icon.')">'.$city_name_short.'</p>';
				}
				else{
					$html.='<p class="mb-2 mt-1 sm:my-2 text-base w-full inline-block"><span class="text-black font-bold block lg:inline">'.$currency.$post_price.'</span> <span class="lg:float-left lg:text-left lg:w-2/5 xl:w-1/2 text-gray-600 block h-6 truncate">'.$get_categoryname_short.'</span></p>
					<p class="text-gray-600 bg-right bg-contain bg-no-repeat pr-5 truncate h-4 text-xs" style="background-image: url('.$location_icon.')">'.$city_name_short.'</p>';
				}
							
				$html.='</a>
				</div>
			</div>
		</div>
		';


		return $html;
	}

	public static function mapview($post_id,$i)
	{
       
		$dir = static::is_dir_rtl();

		$post_det = TblPost::where('id',$post_id)->get(); 
		$city_info = TblCity::where('id',$post_det[0]->city)->get();
		
		$imgUrlfinal = TblChat::getPostImg($post_id);
		$posted_on = date('d M Y', strtotime($post_det[0]->created_at));
		$slug = URL::to('/'.$post_det[0]->slug);
		$ad_type = TblPost::getAddtype($post_id);
		$ad_type = ($ad_type=="")?"":str_replace('_', ' ', strtoupper($ad_type->ad_type));


		$fav_style = TblSavedPosts::check_fav($post_id);
		$hearty = ($fav_style==true)?'fa-heart':'fa-heart-o';

		$final_city_name = !empty($post_det[0]->locality) ? $post_det[0]->locality : $city_info[0]->name; // get locality & city
		$get_categoryname = TblCategory::getCategoryName($post_det[0]->category_id);

		$info_location = TblCity::join('tbl_countries', 'tbl_cities.country_id', '=', 'tbl_countries.id')
		->join('tbl_states', 'tbl_cities.state_id', '=', 'tbl_states.id')
		->where('tbl_cities.id', $post_det[0]->city)
		->get(['tbl_cities.*', 'tbl_countries.code as country_short', 'tbl_countries.name as country_long', 'tbl_states.code as state_short', 'tbl_states.name as state_long']);
		
		/* show the curreny symbol */
		$settings = Setting::get_logos();
		$slected_currency = !empty($post_det[0]->currency_id) ? $post_det[0]->currency_id : $settings['default_currency'];
		$currency_symbol = TblPost::get_post_currency($slected_currency);
		$currency = $currency_symbol[0];


		$post_title = $post_det[0]->title;
		$post_title_short = mb_strimwidth($post_det[0]->title, 0, 25, "...");
		$post_price = $post_det[0]->price;

		$get_categoryname_short = mb_strimwidth($get_categoryname, 0, 15, "..");
		//$location_icon = URL::to(''). 'images/frontend/Group111.png';
		$location_icon = URL::to('images/frontend/Group111.png');
		$city_name_short =  mb_strimwidth($final_city_name, 0, 25,"...");


		$logged_user_id = Auth::id();
		$post_user_id = $post_det[0]->user_id;

		

		$html='';


		$html .='		
		<div class="w-full float-left relative px-1 md:px-2">
			<div class="w-full float-left hover:shadow-lg ease-linear transition-all duration-500 border border-gray-200">
			<ul class="flex flex-col divide-y w-full">
			<li class="flex-row">
				<div class="w-full float-left overflow-hidden relative">
					<div class="items-center">
						<div class="absolute left-0 top-0 z-10">
							<h3 class="text-xs text-white font-semibold relative">';

								if($ad_type!=""){
								$html .='<span class="bg-yellow-500 px-2 py-1 rounded-tr-lg rounded-br-lg uppercase inline-block">'.$ad_type.'</span>';
								}
							$html .='</h3>
						</div>
						<div class="absolute right-0 top-0 z-10">
							<button type="button" id="favourate_post_id_'.$post_id.'" data-fav-post-id="'.$post_id.'" value="'.$post_title.'" class="bg-green-500 text-white w-8 h-8 save_favourate rounded-tl-lg rounded-bl-lg  text-center focus:outline-none border-0 save_favourate">
							<i class="fa '.$hearty.'" aria-hidden="true"></i>
							</button>
						</div>
					</div>
					<a class="block" href="'.$slug.'"  id="services_'.$i.'" onmouseover="triggerClick('.$i.')" onmouseout="triggerOut('.$i.')">
						<div class="flex items-center justify-center h-44" id="map_det" data-title="'.$post_det[0]->title.'" data-lat="'.$info_location[0]['latitude'].'" data-lon="'.$info_location[0]['logitude'].'">
						
					
						<img alt="'.$post_title.'" src="'.$imgUrlfinal.'" class="transform hover:scale-125 transition duration-500 ease-in-out m-auto max-h-full object-cover h-screen w-screen">
						<!--img alt="'.$post_title.'" src="'.$imgUrlfinal.'" class="w-full transform hover:scale-125 transition duration-500 ease-in-out h-80 w-80 object-cover object-center"-->
						</div>
					</a>
				</div>
				<input type="hidden" id="lat" value='.$info_location[0]['latitude'].' />
				<input type="hidden" id="log" value='.$info_location[0]['logitude'].' />
				<div class="w-full float-left p-2 md:px-2 lg:px-3 xl:px-4">
				<a href="'.$slug.'">
					<h4 class="font-sans text-gray-600 font-semibold hover:text-green-500 transition duration-500 ease-in-out truncate h-7 text-base md:text-lg">'.$post_title_short.'</h4>';
				
				if($dir=="false"){
				$html.='<p class="mb-2 mt-1 sm:my-2 text-base w-full inline-block"><span class="text-black font-bold block lg:inline">'.$currency.$post_price.'</span> <span class="lg:float-right lg:text-right lg:w-2/5 xl:w-1/2 text-gray-600 block h-6 truncate">'.$get_categoryname_short.'</span></p>
				<p class="text-gray-600 bg-left bg-contain bg-no-repeat pl-5 truncate h-4 text-xs" style="background-image: url('.$location_icon.')">'.$city_name_short.'</p>';
				}
				else{
					$html.='<p class="mb-2 mt-1 sm:my-2 text-base w-full inline-block"><span class="text-black font-bold block lg:inline">'.$currency.$post_price.'</span> <span class="lg:float-left lg:text-left lg:w-2/5 xl:w-1/2 text-gray-600 block h-6 truncate">'.$get_categoryname_short.'</span></p>
					<p class="text-gray-600 bg-right bg-contain bg-no-repeat pr-5 truncate h-4 text-xs" style="background-image: url('.$location_icon.')">'.$city_name_short.'</p>';
				}
							
				$html.='</a>
				</div>
				<li>
				</ul>
			</div>
		</div>
		';


		return $html;
	}


	public static function viewblock($post_id)
	{

		$dir = static::is_dir_rtl();

		$post_det = TblPost::where('id',$post_id)->get(); 
		$city_info = TblCity::where('id',$post_det[0]->city)->get();
		
		$imgUrlfinal = TblChat::getPostImg($post_id);
		$posted_on = date('d M Y', strtotime($post_det[0]->created_at));
		$slug = URL::to('/'.$post_det[0]->slug);
		$ad_type = TblPost::getAddtype($post_id);
		$ad_type = ($ad_type=="")?"":str_replace('_', ' ', strtoupper($ad_type->ad_type));


		$fav_style = TblSavedPosts::check_fav($post_id);
		$hearty = ($fav_style==true)?'fa-heart':'fa-heart-o';
		$final_city_name = !empty($post_det[0]->locality) ? $post_det[0]->locality : $city_info[0]->name; // get locality & city
		$get_categoryname = TblCategory::getCategoryName($post_det[0]->category_id);
		
		/* show the curreny symbol */
		$settings = Setting::get_logos();
		$slected_currency = !empty($post_det[0]->currency_id) ? $post_det[0]->currency_id : $settings['default_currency'];
		$currency_symbol = TblPost::get_post_currency($slected_currency);
		$currency = $currency_symbol[0];


		$post_title = $post_det[0]->title;
		$post_title_short = mb_strimwidth($post_det[0]->title, 0, 25, "...");
		$post_price = $post_det[0]->price;

		$get_categoryname_short = mb_strimwidth($get_categoryname, 0, 15, "..");
		//$location_icon = URL::to(''). 'images/frontend/Group111.png';
		$location_icon = URL::to('images/frontend/Group111.png');
		$city_name_short =  mb_strimwidth($final_city_name, 0, 25,"...");


		$logged_user_id = Auth::id();
		$post_user_id = $post_det[0]->user_id;

		$html='';


		$html .='
		
		<div class="flex flex-col container  mx-auto w-full items-center justify-center bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
		<ul class="flex flex-col divide-y w-full">
		  <li class="flex flex-row">
			<div class="select-none cursor-pointer hover:bg-gray-50 flex flex-1 items-center p-4">
			  <div class="flex flex-col w-60 h-48 md:h-60  justify-center items-center mr-4 overflow-hidden">
				<a href="#" class="block relative">
				  <img alt="'.$post_title.'" src="'.$imgUrlfinal.'" class="transform hover:scale-125 transition duration-500 ease-in-out m-auto max-h-full object-cover h-60"/>
				</a>
			  </div>
			  <div class="flex-1 pl-1">
				<div class="font-medium dark:text-white">'.$post_title.'</div>
				<div class="text-gray-600 dark:text-gray-200 text-sm">'.$post_title_short.'</div>
			  </div>
			
			</div>
		  </li>
		</ul>
	  </div>';


		return $html;
	}

	public static function get_admin_default_country()
	{
		$country_name ="";
		//get current country
		$current_ip = $_SERVER["REMOTE_ADDR"];
		//$url= "http://ipinfo.io/".$current_ip;
		$url = "http://www.geoplugin.net/json.gp?ip=" . $current_ip;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpcode == 200) {
			$j  = json_decode($output);
			$country_name = $j->geoplugin_countryName;
		}
		//get current country
		return $country_name;
	}
	
	public static function check_active_chat_system($currentURL,$chat_seg)
	{
		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
		$settings = TblAdminChatMethods::where('active', '1')->get('name')->first();
		$c_type = (is_null($settings))?"":$settings->name;

			if(!is_null($chat_seg))
			{
				
				if (str_contains($chat_seg, 'chat')) {		
					if($c_type=="wstype" && $chat_seg!="chat")
					{
						$update = preg_replace('/\bchatting\b/u', 'chat', $currentURL);
						$newUrl = $actual_link.$update;
						//echo "WS Type: ".$c_type." Curr Url: ".$newUrl;
						echo '<script>window.location.href="'.$newUrl.'";</script>';
					}
					
					if($c_type=="ajaxtype" && $chat_seg!="chatting")
					{
						$update = preg_replace('/\bchat\b/u', 'chatting', $currentURL);
						$newUrl = $actual_link.$update;
						//echo "AJType: ".$c_type." Curr Url: ".$newUrl;
						echo '<script>window.location.href="'.$newUrl.'";</script>';
					}
				}
			}
	
			//return chat url
			if(strlen($c_type)>0)
			{
				$type = ($c_type=="ajaxtype")?"chatting":"chat";
				$c_url = URL::to('')."/".$type;
			}
			else{
				$c_url = URL::to('')."/#";
			}
		
		return $c_url;
	}


	public static function check_active_chat_system_old($currentURL,$chat_seg)
	{
		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

		$settings = Setting::where('key', 'user_chatting_type')->pluck('value');
		$chattype = '';
		if($settings!="" || $settings!=null)
		{
			$values = json_decode($settings[0], true);
			$chattype = $values['chat_system'];
		}

		$c_type = $chattype; 

		if(!is_null($chat_seg))
		{
			
			if (str_contains($chat_seg, 'chat')) {		
				if($c_type=="ws_type" && $chat_seg!="chat")
				{
					$update = preg_replace('/\bchatting\b/u', 'chat', $currentURL);
					$newUrl = $actual_link.$update;
					//echo "WS Type: ".$c_type." Curr Url: ".$newUrl;
					echo '<script>window.location.href="'.$newUrl.'";</script>';
				}
				
				if($c_type=="ajax_type" && $chat_seg!="chatting")
				{
					$update = preg_replace('/\bchat\b/u', 'chatting', $currentURL);
					$newUrl = $actual_link.$update;
					//echo "AJType: ".$c_type." Curr Url: ".$newUrl;
					echo '<script>window.location.href="'.$newUrl.'";</script>';
				}
			}
		}

		//return chat url
		if(strlen($c_type)>0)
		{
			$type = ($c_type=="ajax_type")?"chatting":"chat";
			$c_url = URL::to('')."/".$type;
		}
		return $c_url;
	}
	
	public static function is_dir_rtl()
	{
		$dir_rtl = "ltr"; 
		if(Session::has("locale")) 
		{
			$tbl = TblLanguage::where('locale', Session::get("locale"))->get('direction');
			if(count($tbl)>0){
				$dir_rtl = $tbl[0]->direction;
			}
		}
		$dir_rtl = ($dir_rtl=="rtl")?"true":"false";
		return $dir_rtl;
	}
	public static function grid_setup_landing()
	{
		$settings = Setting::where('key', 'app')->pluck('value');
		$values = json_decode($settings[0], true);
		return $values['grid_count'];


	}
	public static function set_last_visited_url()
	{
		$prev = URL::previous();
		session()->put('prev_visited_url', $prev);
	}

}
