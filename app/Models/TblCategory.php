<?php

namespace App\Models;

use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\TblBannerAdvertisement;
use Illuminate\Support\Facades\URL;

class TblCategory extends Model
{

    use HasFactory;
    use NodeTrait;
    use SoftDeletes;

    protected $fillable = ['app_image', 'uuid', 'title', 'slug', 'html', 'deleted_at', 'list_order', 'image', 'banner', 'paid_banner_price', 'product_condition', 'meta_title', 'meta_key', 'meta_description'];

    public static function get_cat_banners($slug = NULL, $id = NULL)
    {
        
        if (!empty($slug)) {
            $getbanners = TblCategory::where('slug', $slug)->first();
        } else {
            $getbanners = TblCategory::where('id', $id)->first();
        }
        
        $banners = "";
        if(!empty($getbanners->parent_id))
        {
        if ($getbanners->parent_id != "") {
            if (!empty($getbanners->banner)) {
                $banners = $getbanners->banner;
            } else {
                $parent_banners = TblCategory::where('id', $getbanners->parent_id)->first();
                if (!empty($parent_banners->banner)) {
                    $banners = $parent_banners->banner;
                }
            }
            } else if (!empty($getbanners->banner)) {
                $banners = $getbanners->banner;
            }
        }
        $category_banner = array();
        if (!empty($banners)) {
            $category_banner[] = array(
                'url' => "",
                'image' => URL::to('storage/categories/' . $banners),
            );
        }
        return $category_banner;
    }

    public static function get_paid_cat_banners($slug = NULL, $id = NULL, $from = NULL)
    {
        if (!empty($slug)) {
            $cat = TblCategory::where('slug', $slug)->pluck('id')->first();
        } else {
            $cat = $id;
        }
        $curr_date = date('Y-m-d');
        $paid_category_banner = array();
        $get_paid_cat_banners = TblBannerAdvertisement::where('page', 'search')->where('category_id', $cat)->where('active', 1)->where('status', 'approved')->where('end_date', '>=', $curr_date)->whereNull('deleted_at')->orderBy('created_at', 'desc')->get();
        if ($from == "web") {
            if (count($get_paid_cat_banners) > 0) {
                foreach ($get_paid_cat_banners as $get_paid_cat_banner) {
                    $paid_category_banner[] = array(
                        "url" => $get_paid_cat_banner->web_link,
                        'image' => URL::to('storage/' . $get_paid_cat_banner->web_banner),
                    );
                }
            }
        } else {
            if (count($get_paid_cat_banners) > 0) {
                foreach ($get_paid_cat_banners as $get_paid_cat_banner) {
                    $paid_category_banner[] = array(
                        "url" => $get_paid_cat_banner->app_link,
                        'image' => URL::to('storage/' . $get_paid_cat_banner->app_banner),
                    );
                }
            }
        }

        return $paid_category_banner;
    }

    public static function get_all_main_categories()
    {
        $categories = TblCategory::withDepth()->having('depth', '=', 0)->whereNull('deleted_at')->orderBy('list_order', 'asc')->get();
        return $categories;
    }

    public static function getCategoryName($id)
    {
        $category_name = TblCategory::where('id',$id)->pluck('title')->first();
        return $category_name;
    }
  public function subcategories()
    {
        return $this->hasMany(TblCategory::class, 'parent_id')->orderBy('title', 'ASC');
    }

    /**
     * Get the posts for the category.
     */
    public function posts()
    {
        return $this->hasMany(TblPost::class, 'category_id', 'id');
    }
}

