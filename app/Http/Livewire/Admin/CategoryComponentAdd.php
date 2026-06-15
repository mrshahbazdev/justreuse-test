<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\TblCategory;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Session;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Null_;
use Livewire\WithFileUploads; //for file upload
use Illuminate\Support\Facades\Request;
use Image;
use Storage;

class CategoryComponentAdd extends Component
{

    use WithFileUploads; //for file upload

    public $ancestors, $current_parent, $image, $title, $parent_id, $html, $app_image, $banner, $paid_banner_price, $product_condition, $meta_title, $meta_key, $meta_description;

    public function render()
    {
        $id = request()->id;
        if ($id) {
            $uuid = $id;
            $id = TblCategory::where('uuid', $uuid)->get();
            $id = $id[0]->id;
            $this->current_parent = $id;
            $this->parent_id = $id;
            $this->ancestors = TblCategory::ancestorsAndSelf($id);
        }
        $categories = TblCategory::with('ancestors')->get()->toTree();
        return view('livewire.admin.category.add', compact('categories'));
    }

    public function store()
    {
        $parentid = $this->parent_id;
        $redirect_url = URL::to('/admin/category/');
        if ($parentid != 0) {
            $uuid = TblCategory::where('id', $parentid)->get()[0]['uuid'];
            $redirect_url = $redirect_url . '/' . $uuid . '/subcategories';
        }
        //start check demo user
            $isDemoUser = User::isDemoUser();
            if($isDemoUser["result"]==true)
            {
                session()->flash('message', $isDemoUser["message"]);
                Session()->flash('result', '0');
                return redirect($redirect_url); 
            }
        //end check demo user

        // dd($this->meta_title, $this->meta_key, $this->meta_description);
        
        $title = $this->title;
        $html = $this->html;
        $image = $this->image;
        $banner = $this->banner;
        $meta_title = $this->meta_title;
        $meta_key = $this->meta_key;
        $meta_description = $this->meta_description;
        $slug = Str::slug($title, "-");
        $product_condition = $this->product_condition;
        $paid_banner_price1 = 0;
        if ($this->paid_banner_price != null || $this->paid_banner_price != "") {
            $paid_banner_price1 = $this->paid_banner_price;
        }
        $app_image = $this->app_image;
        $isExist = TblCategory::where('title', $title)->whereNull('deleted_at')->get();

        if ($isExist->count() == 0) {
            if (!empty($image)) {
                $path = $image->hashName('public/categories');
                $save_img = $image->hashName('categories');
                $image = Image::make($image)->resize(300, 300);
                Storage::put($path, (string) $image->encode());
            } else {
                $save_img = "";
            }
            $save_app_image = "";
            if (!empty($app_image)) {
                $path = $app_image->hashName('public/categories');
                $save_app_image = $app_image->hashName('categories');
                $app_image = Image::make($app_image)->resize(150, 150);
                Storage::put($path, (string) $app_image->encode());
            }
            $save_banner_img = "";
            if (!empty($banner)) {
                $path = $banner->hashName('public/categories/banner');
                $save_banner_img = $banner->hashName('banner');
                $banner = Image::make($banner)->resize(1200, 400);
                Storage::put($path, (string) $banner->encode());
            }
            if ($product_condition == 1) {
                $pc = 1;
            } else {
                $pc = 0;
            }

            $category = TblCategory::create([
                'title' => $title,
                'slug' => $slug,
                'uuid' => (string) Str::uuid(),
                'html' => $html,
                'image' => $save_img,
                'banner' => !empty($save_banner_img) ? $save_banner_img : "",
                'paid_banner_price' => $paid_banner_price1,
                'product_condition' => $pc,
                'app_image' => $save_app_image,
                'meta_title' => $meta_title,
                'meta_key' => $meta_key,
                'meta_description' => $meta_description,
            ]);

            if (($parentid != 0) || ($parentid != null) || ($parentid != "")) {
                $node = TblCategory::find($parentid);
                $node->appendNode($category);
            }
            Session::flash('result', '1');
            Session::flash('message', 'Inserted Successfully');
        } else {
            Session::flash('result', '0');
            Session::flash('message', 'Same Title Already Exist');
        }
        // $redirect_url = URL::to('/admin/category/');
        // if ($parentid != 0) {
        //     $uuid = TblCategory::where('id', $parentid)->get()[0]['uuid'];
        //     $redirect_url = $redirect_url . '/' . $uuid . '/subcategories';
        // }
        return redirect($redirect_url);
    }
}
