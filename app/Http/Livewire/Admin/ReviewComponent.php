<?php

namespace App\Http\Livewire\Admin;

use App\Models\TblReportThisAd;
use Livewire\Component;
use App\Models\TblReview;
use App\Models\User;
use App\Models\TblPost;
use App\Models\Setting;
use Livewire\WithPagination;
use DB;

class ReviewComponent extends Component
{

    use WithPagination;
    public $search;
    public $cnfopen = 0;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {

        $review_list = TblReview::join('users', 'tbl_reviews.user_id', '=', 'users.id')
            ->join('tbl_posts', 'tbl_reviews.post_id', '=', 'tbl_posts.id')
            ->select(["tbl_reviews.id", "tbl_reviews.view", "tbl_reviews.comment", "tbl_posts.id as post_id", "tbl_reviews.ratings", "tbl_reviews.approved", "tbl_reviews.created_at", "users.name", "tbl_posts.title", "tbl_posts.slug"])
            ->where('tbl_posts.title', 'like', '%' . $this->search . '%')
            ->orwhere('users.name', 'like', '%' . $this->search . '%')
			->orderBy('created_at', 'desc')
            ->paginate(10);



        return view('livewire.admin.reviews.show', compact('review_list'));
    }


    public function approved()
    {
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            return response()->json(['success'=>false,'message' => $isDemoUser["message"]]);
            exit;
        }
        $id = request()->id;
        $value = request()->value;
        $approve = TblReview::find($id);
        $approve->update([
            'approved' => $value,
        ]);
        if ($value == "1") {

		/* Send push notification start*/
            $get_user_info = User::where('id', $approve->user_id)->first();
            $get_post_info = TblPost::where('id', $approve->post_id)->first();	
            $get_seller_info = User::where('id', $get_post_info->user_id)->first();

				$slug = TblPost::get_post_slug($get_post_info->slug);				
			
			$fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";    
			$message = array("notifydata" => array('to_id' => $get_post_info->user_id,'from_id'=>$approve->user_id, 'message' => "New comment posted by ".$get_user_info->name."!. Post Name - " . $get_post_info->title, 'notify_from' => 'new_comment', 'notify_title' => "New Comment Added In Letgo!..",'post_id' => $approve->post_id,'slug' => $get_post_info->slug));
			TblPost::send_push_notification($fcmid, $message);
			
			$mail_data = array("send_maildata" => array('to_id' => $get_post_info->user_id, 'message' => "New comment posted by ".$get_user_info->name."!. Post Name - " . $get_post_info->title, 'subject' => "New Comment Added In Letgo!..",'ad_url' => $slug));
			$mail_key = "post_comment";
			Setting::notification_mail($mail_data, $mail_key);
			
		/* Send push notification start*/
			
            return response()->json(['success'=>true,'message' => "This review Approved.."]);
        } else {
            return response()->json(['success'=>true,'message' => "This review Not Approved.."]);
        }
    }


    public function review_comment()
    {
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            return response()->json(['success'=>false,'message' => $isDemoUser["message"]]);
            exit;
        }

        $id = request()->id;
        $comment = TblReview::find($id)->comment;
        $view = TblReview::find($id);
        $view->update([
            'view' => "1",
        ]);
        return response()->json(['success'=>false,'message' => $comment]);
    }

    public function deleteReq($id)
    {
        $this->cnfopen = $id;
    }

    public function deleteCan()
    {
        $this->cnfopen = 0;
    }

    public function delete($id)
    {
        $this->cnfopen = 0;
        TblReview::find($id)->delete();
        session()->flash('message', 'Review Deleted Successfully.');
    }
}
