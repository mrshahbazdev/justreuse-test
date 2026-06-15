<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TblSellerReviews;
use App\Models\User;

class SellerReviewController extends Controller
{
   
    public $search;
    public $cnfopen = 0;

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function render()
    {
//dd('nse');
       
        $seller_review_list = TblSellerReviews::join('users', 'tbl_seller_reviews.user_id', '=', 'users.id')
            ->join('users as sel', 'tbl_seller_reviews.seller_id', '=', 'sel.id')
            ->select(["tbl_seller_reviews.id", "tbl_seller_reviews.comment", "tbl_seller_reviews.ratings", "tbl_seller_reviews.approved", "tbl_seller_reviews.created_at", "users.name","sel.name as  seller_name"])
			->orderBy('created_at', 'desc')
            ->paginate(10);
          

        return view('livewire.admin.seller_reviews.show', compact('seller_review_list'));
    }

    public function deleteReq($id)
    {
        $this->cnfopen = $id;
    }

    public function deleteCan()
    {
        $this->cnfopen = 0;
    }

    public function delete(Request $request)
    {
      //dd($id);
        TblSellerReviews::find($request->id)->delete();
        return back()->with('success','Deleted successfully');
  

        //session()->flash('message', 'Review Deleted Successfully.');
    }

    public function approved(Request $request)
    {
        //dd('mspp');
        
        $isDemoUser = User::isDemoUser();
        //dd($isDemoUser);
        if($isDemoUser["result"]==true)
        {
            return response()->json(['success'=>false,'message' => $isDemoUser["message"]]);
            exit;
        }
        $id = $request->id;
        //dd($id);
        $approve = TblSellerReviews::find($id);
        $approve->update([
            'approved' => 1,
        ]);

        return redirect()->back()->with('success', 'updated successfully');
        // if ($value == "1") {

		// /* Send push notification start*/
        //     $get_user_info = User::where('id', $approve->user_id)->first();
        //     $get_post_info = TblPost::where('id', $approve->post_id)->first();	
        //     $get_seller_info = User::where('id', $get_post_info->user_id)->first();

		// 		$slug = TblPost::get_post_slug($get_post_info->slug);				
			
		// 	$fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";    
		// 	$message = array("notifydata" => array('to_id' => $get_post_info->user_id,'from_id'=>$approve->user_id, 'message' => "New comment posted by ".$get_user_info->name."!. Post Name - " . $get_post_info->title, 'notify_from' => 'new_comment', 'notify_title' => "New Comment Added In Letgo!..",'post_id' => $approve->post_id,'slug' => $get_post_info->slug));
		// 	TblPost::send_push_notification($fcmid, $message);
			
		// 	$mail_data = array("send_maildata" => array('to_id' => $get_post_info->user_id, 'message' => "New comment posted by ".$get_user_info->name."!. Post Name - " . $get_post_info->title, 'subject' => "New Comment Added In Letgo!..",'ad_url' => $slug));
		// 	$mail_key = "post_comment";
		// 	Setting::notification_mail($mail_data, $mail_key);
			
		// /* Send push notification start*/
			
        //     return response()->json(['success'=>true,'message' => "This review Approved.."]);
        // } else {
        //     return response()->json(['success'=>true,'message' => "This review Not Approved.."]);
        // }
    }
    public function delete_review() {

        //dd('mm');
        $ids = request()->id;
        //dd($ids);
        TblSellerReviews::whereIn('id', explode(",", $ids))->delete();
        return response()->json(['message' => "Removed Select Reviews successfully.."]);
    } 
}
