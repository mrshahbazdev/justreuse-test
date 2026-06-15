<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\TblCategory;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Session;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Livewire\WithFileUploads; //for file upload
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class CategoryComponentEdit extends Component
{
    use WithFileUploads;

    public $ancestors;
    public $success = false;
    public $image, $title, $app_image, $parent_id, $html, $cat_id, $banner, $paid_banner_price, $product_condition, $meta_title, $meta_key, $meta_description;

    // To hold existing image paths for deletion
    private $existing_image, $existing_app_image, $existing_banner;

    public function render()
    {
        if ($this->cat_id == null) {
            $uuid = request()->id ?? request()->query('id'); // Handle both route param and query string
            $categoryData = TblCategory::where('uuid', $uuid)->firstOrFail();
            $id = $categoryData->id;
        } else {
            $id = $this->cat_id;
        }

        $data = TblCategory::findOrFail($id);
        $this->ancestors = TblCategory::ancestorsAndSelf($id);
        
        // Load data into properties only if they are not yet set
        if (!$this->title) {
            $this->cat_id = $id;
            $this->title = $data->title;
            $this->parent_id = $data->parent_id;
            $this->html = $data->html;
            $this->paid_banner_price = $data->paid_banner_price;
            $this->product_condition = $data->product_condition;
            $this->meta_title = $data->meta_title;
            $this->meta_key = $data->meta_key;
            $this->meta_description = $data->meta_description;

            // Store existing image paths
            $this->existing_image = $data->image;
            $this->existing_app_image = $data->app_image;
            $this->existing_banner = $data->banner;
        }

        $arr = array($data->_lft, $data->_rgt);
        $categories = TblCategory::whereNotBetween('_lft', $arr)->get()->toTree(); //ignoring the editing node from listing
        
        return view('livewire.admin.category.edit', compact('data', 'categories'));
    }

    public function update()
    {
        $redirect_url = URL::to('/admin/category/');
        if ($this->parent_id != 0) {
            $uuid = TblCategory::where('id', $this->parent_id)->value('uuid');
            $redirect_url .= '/' . $uuid . '/subcategories';
        }

        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"] == true) {
            session()->flash('message', $isDemoUser["message"]);
            return redirect($redirect_url);
        }

        $id = $this->cat_id;
        $title = $this->title;

        $isExist = TblCategory::where('title', $title)->where('id', '!=', $id)->whereNull('deleted_at')->first();
        if ($isExist) {
            session()->flash('message', 'Same Title Already Exists');
            return redirect($redirect_url);
        }

        $record = TblCategory::find($id);
        $updateData = [
            'title' => $title,
            'slug' => Str::slug($title, "-"),
            'html' => $this->html,
            'meta_title' => $this->meta_title,
            'meta_key' => $this->meta_key,
            'meta_description' => $this->meta_description,
            'paid_banner_price' => $this->paid_banner_price ?? 0,
            'product_condition' => $this->product_condition ? 1 : 0,
        ];

        // === UPDATED IMAGE PROCESSING LOGIC ===
        
        // Handle 'image' upload
        if ($this->image) {
            try {
                if ($record->image) Storage::disk('public')->delete($record->image);
                $img = Image::make($this->image->getRealPath())->resize(50, 50);
                $filename = Str::random(40) . '.' . $this->image->getClientOriginalExtension();
                $path = 'categories/' . $filename;
                Storage::disk('public')->put($path, (string) $img->encode());
                $updateData['image'] = $path;
            } catch (\Exception $e) {
                session()->flash('message', 'Error processing main image.'); return redirect($redirect_url);
            }
        }

        // Handle 'app_image' upload
        if ($this->app_image) {
             try {
                if ($record->app_image) Storage::disk('public')->delete($record->app_image);
                $img = Image::make($this->app_image->getRealPath())->resize(150, 150);
                $filename = Str::random(40) . '.' . $this->app_image->getClientOriginalExtension();
                $path = 'categories/' . $filename;
                Storage::disk('public')->put($path, (string) $img->encode());
                $updateData['app_image'] = $path;
            } catch (\Exception $e) {
                session()->flash('message', 'Error processing app image.'); return redirect($redirect_url);
            }
        }

        // Handle 'banner' upload
        if ($this->banner) {
             try {
                if ($record->banner) Storage::disk('public')->delete($record->banner);
                $img = Image::make($this->banner->getRealPath())->resize(1200, 400);
                $filename = Str::random(40) . '.' . $this->banner->getClientOriginalExtension();
                $path = 'categories/banner/' . $filename;
                Storage::disk('public')->put($path, (string) $img->encode());
                $updateData['banner'] = $path;
            } catch (\Exception $e) {
                session()->flash('message', 'Error processing banner.'); return redirect($redirect_url);
            }
        }

        $record->update($updateData);

        // Handle node/tree structure update
        if ($record->parent_id != $this->parent_id) {
            if ($this->parent_id == 0 || $this->parent_id == null) {
                $record->saveAsRoot();
            } else {
                $node = TblCategory::find($this->parent_id);
                $node->appendNode($record);
            }
        }

        session()->flash('message', 'Updated Successfully');
        return redirect($redirect_url);
    }
}

