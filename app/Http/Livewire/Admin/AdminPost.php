<?php
namespace App\Http\Livewire\Admin;

use App\Models\Setting;
use App\Models\TblBlockedPost;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\TblPost;
use App\Models\TblCategory;
use App\Models\TblChat;
use App\Models\User;
use App\Models\TblFieldsDetail;
use App\Models\TblFieldsOption;
use App\Models\TblPostValue;
use App\Models\TblCustomField;
use App\Models\User_profile;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str; 

use Session;

class AdminPost extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search, $deleted_post;
    public $post_id, $title, $description, $price, $images = [];
    public $first_name, $last_name, $phone, $address_line1, $address_line2, $date_of_birth, $gender, $user_id, $email, $name, $is_blocked;
    public $post_updateMode = false;
    public $user_updateMode=false;
    public $cnfopen = 0;
    public $gendArr = array("male"=>"Male","female"=>"Female","other"=>"Other");

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        //  $userid = auth()->user()->id;
        //  dd(Auth::id(),$userid);

        if(!empty($this->deleted_post) && $this->deleted_post == "deleted_post")
        {
            $data = TblPost::join('tbl_cities','tbl_posts.city','=','tbl_cities.id')
            ->join('users','tbl_posts.user_id','=','users.id')
            ->select(['tbl_posts.*','tbl_cities.name as city_name','users.name as user_name', 'users.id as user_id'])
            ->where('title','like', '%'.$this->search.'%')
            ->orderBy('tbl_posts.created_at', 'desc')
            ->onlyTrashed()->paginate(50);
        }else{
            $data = TblPost::join('tbl_cities','tbl_posts.city','=','tbl_cities.id')
            ->join('users','tbl_posts.user_id','=','users.id')
            ->select(['tbl_posts.*','tbl_cities.name as city_name','users.name as user_name', 'users.id as user_id'])
            ->where('title','like', '%'.$this->search.'%')
            ->orderBy('tbl_posts.created_at', 'desc')
            ->paginate(50);
        }

		return view('livewire.admin.admin_post.compo', compact('data'));
    }
   

    // for back button redirect page
    public function back()
    {   
        return redirect()->route('admin/post');
    }


// post edit- admin side
    public function edit($id)
       {
            $post = TblPost::find($id);

                    $this->custJson = $this->getHTML($post->category_id, $id);

        // $this->ancestors = TblCategory::ancestorsAndSelf($post->category_id);

         // dd($this->custJson);
            $this->post_id = $id;
            $this->title = $post->title;
            $this->price = $post->price;
            $this->description = $post->description;
            $this->images = $post->images;

            $this->post_updateMode=true;
            $this->user_updateMode=false;
       }
         public function getHTML($catid, $posted_id)
    {
        $info_posts = TblPost::join('tbl_categories', 'tbl_posts.category_id', '=', 'tbl_categories.id')
        ->where('tbl_posts.id', $posted_id)
        ->get(['tbl_posts.*', 'tbl_categories.title as cattitle']);
        $cfld  = TblCustomField::where('cat_id', $catid)->get();
        $htmltag = "";
        if (!empty($cfld[0])) {
            if ($cfld[0]->field_count > 0) {
                $arrayData = TblFieldsDetail::where('cat_id', $catid)->where('active', '1')->get();
                if ($arrayData->count() > 0) {
                    $loopmain = 0;
                    foreach ($arrayData as $r) {

                        $field_id = $r["id"];
                        $type = $r["type"];
                        $name = $field_id . '_' . $r["form_field_name"];
                        $label = $r['name'];
                        $required = ($r['required'] == "0") ? "" : "required='required'";
                        $requiredLbl = ($r['required'] == "0") ? "" : '&nbsp;<span class="text-red-800">*</span>';

                        $postvalue = "";
                        $postDet = TblPostValue::where('post_id', $posted_id)->where('field_id', $field_id)->get();
                        if ($postDet->count() > 0) {
                            $postvalue = $postDet[0]->value;
                        }

                        $htmltag .= '<div class="w-full block mb-2 sm:mb-6 md:mb-8 lg:mb-10 float-left"><label class="block text-base text-black font-semibold mb-2 sm:mb-4">' . $label . $requiredLbl . '</label>';
                        $comm_class = 'class="w-full h-14 rounded px-6 py-4 text-base text-black border border-gray-200 bg-gray-50 focus:outline-none  placeholder-black"';
                        $select_common_class = 'class="w-full h-14 rounded px-6 py-4 text-base text-black border border-gray-200 bg-gray-50 focus:outline-none  placeholder-black"';

                        //textfield
                        if ($type == "text") {
                            $htmltag .= '<input type="' . $type . '" name="' . $name . '" value="' . $postvalue . '" ' . $comm_class . ' ' . $required . '/>';
                        }
                        //number
                        if ($type == "number") {

                            if ($label == "Budget") {
                                $htmltag .= '<span>';
                                $htmltag .= '<select id="currency_id" class="w-4/4 h-14 mr-4 rounded-lg px-2 py-2 md:px-4 md:py-4 text-sm sm:text-base text-black border-l-2 border-gray-400 bg-gray-100 focus:outline-none  placeholder-black text-center ">';
                                $currency = TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
                                $settings = Setting::get_logos();
                                $default_curr = $settings['default_currency'];
                                foreach ($currency as $currency) {
                                    if ($currency->id == $info_posts[0]->currency_id) {
                                        $bud_selc = "selected";
                                    } else {
                                        $bud_selc = "";
                                    }

                                    $htmltag .= '<option '.$bud_selc.'>' . $currency->short_code . ' (' . $currency->currency_hex . ') </option>';
                                }
                                $htmltag .= '</select>';
                                $htmltag .= '</span>';
                                $htmltag .= '<input type="' . $type . '" name="' . $name . '" min="0" value="' . $postvalue . '" class="w-10/12 h-14 rounded-lg px-6 py-4 text-base text-black border-l-2 border-gray-400 bg-gray-100 focus:outline-none  placeholder-black" ' . $required . '/>';
                            } else {
                                $htmltag .= '<input type="' . $type . '" name="' . $name . '" min="0" value="' . $postvalue . '" ' . $comm_class . ' ' . $required . '/>';
                            }
                        }

                        //date
                        if ($type == "date") {
                            $datevalue = ($postvalue != "") ? date('Y-m-d', strtotime($postvalue)) : "";
                            $htmltag .= '<input type="' . $type . '" name="' . $name . '" value="' . $datevalue . '" ' . $comm_class . ' ' . $required . '/>';
                        }
                        //file
                        if ($type == "file") {
                            $htmltag .= '<input type="' . $type . '" name="' . $name . '" ' . $comm_class . ' ' . $required . '/>';

                            if ($postvalue != "") {
                                $url = url()->to('/');
                                $imageUrl = url()->to('/storage') . '/' . $postvalue;
                                $htmltag .= '<img src="' . $imageUrl . '" alt="image" width="100" height="100"/>';
                            }
                        }

                        //textarea
                        // if ($type == "textarea") {
                        //     $htmltag .= '<textarea type="textarea" ' . $comm_class . ' name="' . $name . '" ' . $required . '></textarea>';
                        // }

                        //select
                        if ($type == "select" || $type == "autocomplete" || $type == "checkbox-group" || $type == "radio-group") {
                            $arrayData = TblFieldsOption::where('cat_id', $catid)->where('form_field_name', $r["form_field_name"])->where('active', '1')->get();


                            if ($type == "checkbox-group") {
                                $checkedvalues = explode(',', $postvalue); //make it as array
                                $htmltag .= '<div class="mt-2">';
                                $i = 0;
                                foreach ($arrayData as $k) {
                                    $checked = (in_array($k['value'], $checkedvalues)) ? "checked" : "";
                                    $htmltag .= '<label class="inline-flex items-center mr-2"><input name="' . $name . '[' . $k["value"] . ']" type="checkbox" class="form-checkbox text-xs font-bold" value=' . $k["value"] . ' ' . $checked . '><span class="ml-1">' . $k["key"] . '</span></label>';
                                    $i++;
                                }
                                $htmltag .= '</div>';
                            } else if ($type == "radio-group") {
                                $i = 0;
                                $htmltag .= '<div class="mt-2">';
                                foreach ($arrayData as $k) {
                                    $checked = ($postvalue == $k['value']) ? "checked" : "";
                                    $htmltag .= '<label class="inline-flex items-center mr-4 text-lg"><input name="' . $name . '" type="radio" class="form-radio h-6 w-6 text-indigo-500" value=' . $k["value"] . ' ' . $checked . '><span class="ml-2" >' . $k["key"] . '</span></label>';
                                    $i++;
                                }
                                $htmltag .= '</div>';
                            } else {
                                if ($r['form_field_name'] == "brandwithmodel") {
                                    // brand select box

                                    $brandClass = 'class="w-full h-14 rounded px-6 py-4 text-base text-black border border-gray-200 bg-gray-50 focus:outline-none  placeholder-black brands-select"';
                                    $htmltag .= '<select name="' . $name . '" ' . $brandClass . ' ' . $required . '>';
                                    foreach ($arrayData as $k) {
                                        $brand_id = !empty($postvalue) ? explode(',', $postvalue)[0] : "";
                                        $selected_brand = ($brand_id == $k["id"]) ? "selected" : "";
                                        $htmltag .= '<option value=' . $k["id"] . ' ' . $selected_brand . '>' . $k["key"] . '</option>';
                                    }
                                    $htmltag .= '</select>';

                                    // models select box
                                    $htmltag .= '<div class="">';
                                    // $htmltag .= '<label class="block text-base text-black font-semibold mb-2 sm:mb-4 mt-4">Models ' . $requiredLbl . '</label>';
                                    $modelsClass = 'class="w-full h-14 rounded px-6 py-4 text-base text-black border border-gray-200 bg-gray-50 focus:outline-none  placeholder-black models-select"';

                                    $htmltag .= '<select name="' . $field_id . '_brandswithmodels' . '" ' . $modelsClass . ' ' . $required . '>';
                                    if ($postvalue != "") {
                                        $brand_id = explode(',', $postvalue)[0];
                                        $model_id = explode(',', $postvalue)[1];
                                        $get_brand_val = TblFieldsOption::where('id', $brand_id)->where('active', '1')->pluck('value')->toArray();
                                        if (!empty($get_brand_val[0])) {
                                            $exploded_brand_vals = explode(',', $get_brand_val[0]);
                                            foreach ($exploded_brand_vals as $exploded_brand_val) {
                                                $modelVal = Str::slug($exploded_brand_val, "-");
                                                $selected_model = "";
                                                if (strcasecmp(Str::title(str_replace('-', ' ', $model_id)), $exploded_brand_val) == 0) {
                                                    $selected_model = "selected";
                                                }
                                                $htmltag .= '<option value=' . $modelVal . ' ' . $selected_model . '>' . $exploded_brand_val . '</option>';
                                            }
                                        } else {
                                            $first_models = explode(',', $arrayData[0]['value']);
                                            foreach ($first_models as $first_model) {
                                                $modelVal = Str::slug($first_model, "-");
                                                $htmltag .= '<option value=' . $modelVal . '>' . $first_model . '</option>';
                                            }
                                        }
                                    } else {
                                        $first_models = explode(',', $arrayData[0]['value']);
                                        foreach ($first_models as $first_model) {
                                            $modelVal = Str::slug($first_model, "-");
                                            $htmltag .= '<option value=' . $modelVal . '>' . $first_model . '</option>';
                                        }
                                    }
                                    $htmltag .= '</select>';
                                    $htmltag .= '</div>';
                                } else {
                                    $htmltag .= '<select name="' . $name . '" ' . $comm_class . ' ' . $required . '>';
                                    foreach ($arrayData as $k) {
                                        $selected = ($postvalue == $k['value']) ? 'selected="selected"' : '';
                                        $htmltag .= '<option value=' . $k["value"] . ' ' . $selected . '>' . $k["key"] . '</option>';
                                    }
                                    $htmltag .= '</select>';
                                }
                            }
                        }

                        $htmltag .= '</div>';
                        $loopmain++;
                    }
                }
            }
        }
        return $htmltag;
    }


       //edit user profile - Admin side

       public function editUser($id)
       {
           
         $user = User::find($id);
         $user_pro = User::find($id)->user_profile;
         $this->user_id = $id;
         $this->email = $user->email;
         $this->name = $user->name;
         $this->is_blocked = $user->is_blocked;
         $this->first_name = $user_pro->first_name;
         $this->last_name = $user_pro->last_name;
         $this->phone = $user_pro->phone;
         $this->address_line1 = $user_pro->address_line1;
         $this->address_line2 = $user_pro->address_line2;
         $this->date_of_birth = $user_pro->date_of_birth;
         $this->gender = $user_pro->gender;

             $this->post_updateMode=false;
            $this->user_updateMode=true;
       }


    //    user update - admin side
public function userUpdate()
{

    
     $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            $this->insertMode = false;
            $this->updateMode=false;
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'error'); 
            return redirect()->route('admin/post');
            exit;
        }


            $id = $this->user_id;
            $user_pro = User::find($id)->user_profile;
            $user_pro_update = User_profile::findOrFail($user_pro->id);

            $user_pro_update->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'address_line1' => $this->address_line1,
                'address_line2' => $this->address_line2,
                'date_of_birth' => $this->date_of_birth,
                'gender' => $this->gender,
                'phone' => $this->phone,

            ]);

                $user = User::find($id);
                $user->update([
                    'email' => $this->email,
                    'name' => $this->name,
                ]);

    return redirect()->route('admin/post');

}

//  post block or unblock
public function block_post($id)
{



     $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            $this->insertMode = false;
            $this->updateMode=false;
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'error'); 
            return redirect()->route('admin/post');
            exit;
        }

    
    // $isDemoUser = User::isDemoUser();
    // if($isDemoUser["result"]==true)
    // {
        
    //     session()->flash('message', $isDemoUser["message"]);
    //     Session()->flash('class', 'filed');
    //     return redirect()->route('admin/post');
    // }


    // active = 1 means post blocked... active = 0 means post unblocked
    $post_id = $id;
    $currDate = date('Y-m-d H:i:s');
    $auth_user = auth()->id();

    $settings = Setting::get_logos();
    $site_name = $settings['name'];

    $getblockedPost = TblBlockedPost::where('post_id', $post_id)->get();

    if(count($getblockedPost) == 0)
    {
        // block
        TblBlockedPost::create([
            "post_id" => $post_id,
            "blocked_by" => $auth_user,
            "blocked_date" => $currDate,
        ]);

        // send notification
        
        $get_post_info = TblPost::where('id', $post_id)->first();
        $user_id = $get_post_info->user_id;
        $get_user_info = User::where('id', $user_id)->first();
        $slug = url('/post');
        $get_admin = User::role('superadmin')->get();
        $admin_id = $get_admin[0]->id;

        $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
        $message = array("notifydata" => array('to_id' => $user_id, 'from_id' => $admin_id, 'message' => "Your post has been blocked due to some reason. post name - ".$get_post_info->title, 'notify_from' => 'block_post', 'notify_title' => "Post Blocked In ".$site_name." !..", 'post_id' => $post_id, 'slug' => $slug));
        TblPost::send_push_notification($fcmid, $message);

        $mail_data = array("send_maildata" => array('to_id' => $user_id, 'message' => "Your post has been blocked due to some reason. post name - ".$get_post_info->title .". Contact us if you want to un block your post.", 'subject' => "Post Blocked In ".$site_name." !..",'ad_url' => $slug));
        $mail_key = "block_post";
        Setting::notification_mail($mail_data, $mail_key);

        // end notification

        TblPost::find($post_id)->delete();

        session()->flash('message', 'post blocked successfully!');
        Session()->flash('class', 'success');
        return redirect()->route('admin/post');
    }else{
        if($getblockedPost[0]->active == 1)
        {
            // unblock
            $recId = $getblockedPost[0]->id;
            $update = TblBlockedPost::find($recId);

            $update->update([
                "unblocked_date" => $currDate,
                "unblocked_by" => $auth_user,
                "active" => 0,
            ]);

            TblPost::withTrashed()->find($post_id)->restore();

        // send notification
        
            $get_post_info = TblPost::where('id', $post_id)->first();
            $user_id = $get_post_info->user_id;
            $get_user_info = User::where('id', $user_id)->first();
            $slug = url('/post');
            $get_admin = User::role('superadmin')->get();
            $admin_id = $get_admin[0]->id;

            $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
            $message = array("notifydata" => array('to_id' => $user_id, 'from_id' => $admin_id, 'message' => "Your post has been unblocked. post name - ".$get_post_info->title, 'notify_from' => 'unblock_post', 'notify_title' => "Post UnBlocked In ".$site_name." !..", 'post_id' => $post_id, 'slug' => $slug));
            TblPost::send_push_notification($fcmid, $message);

            $mail_data = array("send_maildata" => array('to_id' => $user_id, 'message' => "Your post has been unblocked. post name - ".$get_post_info->title, 'subject' => "Post UnBlocked In ".$site_name." !..",'ad_url' => $slug));
            $mail_key = "unblock_post";
            Setting::notification_mail($mail_data, $mail_key);

         // end notification

            session()->flash('message', 'post unblocked successfully!');
            Session()->flash('class', 'success');
            return redirect()->route('admin/post');

        }else if($getblockedPost[0]->active == 0){
        // block
        $recId = $getblockedPost[0]->id;

         $update = TblBlockedPost::find($recId);

            $update->update([
                "blocked_by" => $auth_user,
                "blocked_date" => $currDate,
                "active" => 1,
            ]);
        
        // send notification
        
        $get_post_info = TblPost::where('id', $post_id)->first();
        $user_id = $get_post_info->user_id;
        $get_user_info = User::where('id', $user_id)->first();
        $slug = url('/post');
        $get_admin = User::role('superadmin')->get();
        $admin_id = $get_admin[0]->id;

        $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
        $message = array("notifydata" => array('to_id' => $user_id, 'from_id' => $admin_id, 'message' => "Your post has been blocked due to some reason. post name - ".$get_post_info->title, 'notify_from' => 'block_post', 'notify_title' => "Post Blocked In ".$site_name." !..", 'post_id' => $post_id, 'slug' => $slug));
        TblPost::send_push_notification($fcmid, $message);

        $mail_data = array("send_maildata" => array('to_id' => $user_id, 'message' => "Your post has been blocked due to some reason. post name - ".$get_post_info->title .". Contact us if you want to un block your post.", 'subject' => "Post Blocked In ".$site_name." !..",'ad_url' => $slug));
        $mail_key = "block_post";
        Setting::notification_mail($mail_data, $mail_key);

        // end notification

        TblPost::find($post_id)->delete();

        session()->flash('message', 'post blocked successfully!');
        Session()->flash('class', 'success');
        return redirect()->route('admin/post');

        }

    }

}
	
}