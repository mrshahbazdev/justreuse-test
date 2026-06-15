<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\TblBuynowOrder;
use Livewire\WithPagination;
use App\Models\TblPostMethod;
use App\Models\TblPost;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\URL;

class BuynowOrdersComponent extends Component
{

    use WithPagination;
    public $search, $data;
    public $buynoworder_view_mode = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function check_method_is_active()
    {
        $resut = 0;
        $post_methods = TblPostMethod::get_active_post_methods();
        if (!empty($post_methods)) {
            $check_post_methods = $post_methods->pluck('name')->toArray();
            if (in_array("buynow", $check_post_methods)) {
                $resut = 1;
            }
        }
        return $resut;
    }

    public function render()
    {
        $res = $this->check_method_is_active();
        if ($res == 1) {
            $BuynowOrders = TblBuynowOrder::whereNull('tbl_buynow_orders.deleted_at')
                ->join('users', 'tbl_buynow_orders.user_id', '=', 'users.id')
                ->join('tbl_posts', 'tbl_buynow_orders.post_id', '=', 'tbl_posts.id')
                ->join('tbl_default_currencies', 'tbl_buynow_orders.currency_id', '=', 'tbl_default_currencies.id')
                ->select(['tbl_buynow_orders.*', 'tbl_default_currencies.currency_hex as currency_hex', 'tbl_posts.title as post_title', 'users.name as user_name', 'users.id as user_id', 'users.email as user_email'])
                ->where(function ($q) {
                    $q->where('users.name', 'like', '%' . $this->search . '%')->orWhere('users.email', 'like', '%' . $this->search . '%');
                })->orderBy('tbl_buynow_orders.created_at', 'desc')->paginate(10);
            return view('livewire.admin.buynow_orders.compo', compact('BuynowOrders'));
        } else {
            abort(404);
        }
    }

    // for back button redirect page
    public function back()
    {
        return redirect()->route('admin/buynow-orders');
    }

    public function view($id)
    {
        $buynoworder = TblBuynowOrder::where('tbl_buynow_orders.id', $id)->join('users', 'tbl_buynow_orders.user_id', '=', 'users.id')->select(['tbl_buynow_orders.*', 'users.name as user_name', 'users.id as user_id', 'users.email as user_email'])->first();
        $this->data = $buynoworder;
        $this->buynoworder_view_mode = true;
    }
}
