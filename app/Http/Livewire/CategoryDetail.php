<?php

namespace App\Http\Livewire;

use App\Models\TblCategory;
use App\Models\TblCity;
use App\Models\TblFieldsDetail;
use App\Models\TblFieldsOption;
use App\Models\TblPost;
use App\Models\TblPostValue;
use App\Models\TblReview;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Http\Livewire\DB;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;

class CategoryDetail extends Component
{
    public $post_detail="", $cat_detail="", $left_tree="";
    use WithPagination;
    
    public function render()
    {
        
        $slug = request()->segment(2);
        $this->cat_detail = TblCategory::where('slug',$slug)->get();
        if($this->cat_detail->count()>0)
        {
            //order by - filter condition begin
            $orderby = request()->order_by;
            $orderthis = "";
            $field = ""; $order="";
            if($orderby==null){ $field = "created_at"; $order="desc"; }
            else{
                if($orderby=="date"){ $field = "created_at"; $order="asc"; }
                if($orderby=="priceAsc"){ $field = "price"; $order="asc"; }
                if($orderby=="priceDesc"){ $field = "price"; $order="desc"; }
            }
            //order by - filter condition end

            //day filter - begin
            $days = request()->days_last;
            $currdate = date("Y-m-d");
            $fromdate="";
            if($days!=null){
                $fromdate = date("Y-m-d",strtotime('-'.$days.' day',strtotime($currdate)));
            }
            //day filter - end

            //price range filter - begin
            $min_price = (request()->min_price==null)?"0":request()->min_price;
            $max_price = (request()->max_price==null)?"":request()->max_price;
            //price range filter - end
           

            $catid = $this->cat_detail[0]['id'];
            //left list begin
            $result1 = TblCategory::ancestorsOf($catid)->toTree()->first();
            $left_parent = ($result1==null)?$catid:$result1->id;
            $this->left_tree = TblCategory::whereDescendantOf($left_parent)->get();
            //left list end          

            $result = TblCategory::descendantsAndSelf($catid);
            $arry = [];
            foreach($result as $s){ $arry[] = $s->id; }        
            

            $this->post_detail = "";
            $postDetail = TblPost::join('tbl_cities','tbl_posts.city','=','tbl_cities.id')
            ->whereIn('tbl_posts.category_id',$arry)
            ->whereBetween('tbl_posts.created_at',[$fromdate, $currdate])
            ->where('tbl_posts.price','>=',$min_price)
            ->whereNull('tbl_posts.deleted_at')
            ->orderBy('tbl_posts.'.$field,$order);

            if($max_price!="")
            {
                $postDetail  = $postDetail->where('tbl_posts.price','<=',$max_price); 
            }

            $postDetail  = $postDetail->get(['tbl_posts.*','tbl_cities.name as city_name','tbl_cities.latitude','tbl_cities.logitude']);            
            if($postDetail->count()>0)
            {
                $this->post_detail = $postDetail;
            }
        }      
        
        return view('livewire.category-detail');  
    }    


}
