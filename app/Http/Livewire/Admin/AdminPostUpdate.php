<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\TblPost;
use App\Models\User;
use App\Models\TblPostValue;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Setting;
use Illuminate\Support\Str; //for slug
use Session;
use Image;
Use Storage;

class AdminPostUpdate extends Component {

    use WithPagination;
    use WithFileUploads;

    public $post_updateMode = false;
    public $user_updateMode = false;

    public function render() {
       
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $isDemoUser = User::isDemoUser();
            if($isDemoUser["result"]==true)
            {
                redirect('admin/posts');
            }
            $formdata = request()->all();
            $post_id = $formdata["post-id"];
            $title = $formdata["title"];
            $price = $formdata["price"];
            $description = $formdata["description"];
            $slug = Str::slug($title, "-");
            $old_images = !empty($formdata['old_images']) ? $formdata['old_images'] : "";

            $new_imgs = "";
            $check_old_imgs = array();
            $check_new_imgs = array();
            
            /* remove the deleted image from the storage folder start */
            if (!empty($old_images)) {
                $post_imgs = TblPost::where('id', $post_id)->pluck('images')->toArray();
                foreach ($post_imgs as $post_img) {
                    $imgs = explode(',', $post_img);
                    foreach ($imgs as $img) {
                        $db[] = $img;
                    }
                }
                $matched_imgs = array_intersect($db, $old_images);
                $unmatched_imgs = array_diff($db, $old_images);
                foreach ($unmatched_imgs as $unmatched_img) {
                    $unmatched_img_name = str_replace("adpost/predefined/", '', $unmatched_img);
                    /* remove web image file */
                    if (is_file(public_path('/storage/adpost/predefined/' . $unmatched_img_name))) {
                        $path = public_path('/storage/adpost/predefined/' . $unmatched_img_name);
                        unlink($path);
                    }
                    /* remove image file from app list folder */
                    if (is_file(public_path('/storage/adpost/applist/' . $unmatched_img_name))) {
                        $app_list = public_path('/storage/adpost/applist/' . $unmatched_img_name);
                        unlink($app_list);
                    }
                    /* remove image file from app detail folder */
                    if (is_file(public_path('/storage/adpost/appdetail/' . $unmatched_img_name))) {
                        $app_detail = public_path('/storage/adpost/appdetail/' . $unmatched_img_name);
                        unlink($app_detail);
                    }
                }
            }
            if (array_key_exists('images_', $formdata)) {
               
                $images = $formdata['images_'];
                $imagenamesArr = [];
                foreach ($images as $key => $j) {
                    $img_index = explode(',', $formdata['selected-img-index']);
                    if (in_array($key, $img_index)) {
                        $path = $j->hashName('public/adpost/predefined');
                        $save_img = $j->hashName('adpost/predefined');
                        /* image for web */
                        $image = Image::make($j)->resize(500, 350);
                        /* Get watermark image */
                        $settings = Setting::get_logos();                                
                        $image->insert(public_path('storage/'.$settings['watermark']), 'bottom-right', 10, 10);
                        Storage::put($path, (string) $image->encode());
                        /* image for app - list size : 160*160 */
                        $path_app_list = $j->hashName('public/adpost/applist');
                        $app_list = Image::make($j)->resize(160, 160);
                        $app_list->insert(public_path('storage/'.$settings['watermark']), 'bottom-right', 10, 10);
                        Storage::put($path_app_list, (string) $app_list->encode());

                        /* image for app - detail size : 230*230  */
                        $path_app_detail = $j->hashName('public/adpost/appdetail');
                        $app_detail = Image::make($j)->resize(230, 230);
                        $app_detail->insert(public_path('storage/'.$settings['watermark']), 'bottom-right', 10, 10);
                        Storage::put($path_app_detail, (string) $app_detail->encode());

                        array_push($imagenamesArr, $save_img);
                    }
                }
                $new_imgs = $imagenamesArr;
            }
            $check_old_imgs = !empty($old_images) ? $old_images : [];
            $check_new_imgs = !empty($new_imgs) ? $new_imgs : [];
            $allimgs = array_merge($check_old_imgs, $check_new_imgs);
           
            $predefined_imgs = implode(',', $allimgs);                        
            //predefined image upload
            $node = TblPost::find($post_id);
            //new slug update
            $pre_slug = explode('-', $node->slug);
            $pre_slug_id = end($pre_slug);
            $alias_val = $slug . '-' . $pre_slug_id;
            $node->update([
                'title' => $title,
                'slug' => $alias_val,
                'description' => $description,
                'price' => $price,
                'images' => $predefined_imgs
            ]);
            $data = TblPost::join('tbl_cities', 'tbl_posts.city', '=', 'tbl_cities.id')
                ->join('users', 'tbl_posts.user_id', '=', 'users.id')
                ->select(['tbl_posts.*', 'tbl_cities.name as city_name', 'users.name as user_name'])
                ->paginate(10);

            return view('livewire.admin.admin_post.compo', compact('data'));
        }
        $data = TblPost::join('tbl_cities', 'tbl_posts.city', '=', 'tbl_cities.id')
                ->join('users', 'tbl_posts.user_id', '=', 'users.id')
                ->select(['tbl_posts.*', 'tbl_cities.name as city_name', 'users.name as user_name'])
                ->paginate(10);

        return view('livewire.admin.admin_post.compo', compact('data'));
    }

}
