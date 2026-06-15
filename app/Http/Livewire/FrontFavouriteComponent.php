<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\TblPost;
use App\Models\TblCity;
use App\Models\TblSavedPosts;
use Livewire\WithPagination;
use Illuminate\Support\Collection;

class FrontFavouriteComponent extends Component
{

    use WithPagination;

    public $post_tbl_id;
    public $search = "";
    public $userid = "";

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $userid = auth()->user()->id;
        $savedposts = TblSavedPosts::where('user_id', $userid)->get();
        $res = [];
        foreach ($savedposts as $row) {
            $get_post_ids = TblPost::check_payment_pack_expired($row->post_id);
            if(count($get_post_ids) > 0)
            {
                $res[] = $get_post_ids[0];
            }

        }

        /*$data = TblPost::select("tbl_posts.*", "tbl_cities.name as city_name", "tbl_saved_posts.id as fav_id")
            ->whereIn('tbl_posts.id', $res)
            ->where('tbl_posts.active', 1)
            ->where('tbl_posts.sold_status', 0)
            ->whereNull('tbl_posts.deleted_at')
            ->where('tbl_posts.title', 'like', '%' . $this->search . '%')
            ->join("tbl_cities", "tbl_cities.id", "=", "tbl_posts.city")
            ->join("tbl_saved_posts", function ($join) {
                $join->on("tbl_saved_posts.post_id", "=", "tbl_posts.id")
                    ->where("tbl_saved_posts.user_id", "=", auth()->user()->id);
            })->paginate(10);*/

            $data = TblPost::select("tbl_posts.*", "tbl_cities.name as city_name", "tbl_saved_posts.id as fav_id")
            ->whereIn('tbl_posts.id', $res)
            ->where('tbl_posts.active', 1)
            ->where('tbl_posts.sold_status', 0)
            ->whereNull('tbl_posts.deleted_at')
            ->where('tbl_posts.title', 'like', '%' . $this->search . '%')
            ->join("tbl_cities", "tbl_cities.id", "=", "tbl_posts.city")
            ->join("tbl_saved_posts", function ($join) {
                $join->on("tbl_saved_posts.post_id", "=", "tbl_posts.id")
                    ->where("tbl_saved_posts.user_id", "=", auth()->user()->id);
            })
            ->join("users", function ($join) {
                $join->on("users.id", "=", "tbl_posts.user_id")
                    ->where("users.is_blocked", "=", "0");
            })->paginate(10);


        return view('livewire.front-favourite', ['data' => $data]);
    }

    public function destroy($id)
    {
        // remove favourite
        if ($id) {
            $del_post = TblSavedPosts::where('post_id', $id)->get()[0]->id;
            TblSavedPosts::find($del_post)->delete();
        }
    }
}
