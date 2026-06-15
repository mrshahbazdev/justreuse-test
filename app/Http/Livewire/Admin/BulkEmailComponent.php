<?php

namespace App\Http\Livewire\Admin;
use App\Models\BulkEmailTemplateMain;
use App\Models\BulkEmailTemplateDetail;
use Livewire\Component;
use App\Models\Setting;
use App\Models\TblPost;
use App\Models\User;
use Livewire\WithPagination;
use Mail;

class BulkEmailComponent extends Component {
    
    use WithPagination;
    
    public $updateMode=false;
    public $insertMode=false;
    public $previewMode=false;
    public $cnfopen = 0;
    public $title, $email_code, $notification_msg, $html_template, $edited_id;

    public function render() {
        
        $list = BulkEmailTemplateMain::paginate(50);

        return view('livewire.admin.bulk-email.compo', [
            'list' => $list
        ]);

    }


    public function create()
    {
        $this->insertMode=true;
    }


    public function store()
    {
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'success'); 
           return redirect()->route('admin/bulk-email');
        }

        $this->validate([
            'title' => 'required',
            'html_template' => 'required',
            'email_code' => 'required|unique:bulk_email_template_mains',
        ]);

        $rand_str = substr(uniqid(), 0, 6);
        $unique_id = date('YmdHis').str_shuffle($rand_str);

        $insert_template = BulkEmailTemplateMain::create([
            'unique_id' => $unique_id,
            'title' => $this->title,
            'notification_msg' => $this->notification_msg,
            'email_code' => $this->email_code,
            'html_template' => $this->html_template,
            ]);

        $last_unique_id = $insert_template->unique_id;
        $last_template_id = $insert_template->id;

        $get_verified_users = User::where('is_blocked', 0)->whereNotNull('email_verified_at')->get();
        
        foreach($get_verified_users as $row)
        {
            $user_id = $row->id;
            $user_email = $row->email;

            BulkEmailTemplateDetail::create([
                'template_id' => $last_template_id,
                'unique_id' => $last_unique_id,
                'user_id' => $user_id,
                'user_email_id' => $user_email,
            ]);

        }   

            session()->flash('message', 'New bulk email template added successfully.');
            Session()->flash('class', 'success'); 
           return redirect()->route('admin/bulk-email');

    }

    public function preview($id)
    {
        $template = BulkEmailTemplateMain::find($id);
        $html_template = $template->html_template;

        $settings = Setting::get_logos();
        $logo = asset('storage/'.$settings['logo']);
        $site_name = $settings['name'];
        $url = url('/');

        // content replace 
        $content = $html_template;
        $content=str_replace('[[site_logo]]', $logo, $content);
        $content=str_replace('[[ad_url]]', $url, $content);
        $content=str_replace('[[site_name]]', $site_name, $content);

        $this->html_template = $content;
        
        $this->previewMode=true;
    }


    public function edit($id) {
        $this->edited_id = $id;
        $template_data = BulkEmailTemplateMain::find($id);
        $this->title = $template_data->title;
        $this->notification_msg = $template_data->notification_msg;
        $this->html_template = $template_data->html_template;
        $this->email_code = $template_data->email_code;

        $this->updateMode = true;
    }


    public function update(){

        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'success'); 
           return redirect()->route('admin/bulk-email');
        }
        $this->validate([
            'title' => 'required',
            'html_template' => 'required',
            'email_code' => 'required'
        ]);

        $id = $this->edited_id;
        $template_data = BulkEmailTemplateMain::find($id);

        $template_data->update([
            'title' => $this->title,
            'email_code' => $this->email_code,
            'notification_msg' => $this->notification_msg,
            'html_template' => $this->html_template,
        ]);

        session()->flash('message', 'Bulk email template updated successfully.');
        Session()->flash('class', 'success'); 
       return redirect()->route('admin/bulk-email');

    }

    public function start_sent_mail($id)
    { 

        // update init send mail column
        $update_mail_send = BulkEmailTemplateMain::find($id);

        $update_mail_send->update([
            "init_send_mail" => 1
        ]);

        session()->flash('message', 'Initiated to send mail successfully.!');
        Session()->flash('class', 'success'); 
        return redirect()->route('admin/bulk-email');

    }
    


// sent bulk email to all users

public function send_bulk_email()
{

    $template_data = BulkEmailTemplateMain::where('mail_complete_status', 0)->where('init_send_mail', 1)->get();

    if(count($template_data) > 0)
    {
        // get admin id and site name
        $settings = Setting::get_logos();
        $site_name = $settings['name'];

        $get_admin = User::role('superadmin')->get();
        $admin_id = $get_admin[0]->id;

        foreach($template_data as $temp_row_data)
        {
        
        $unique_id = $temp_row_data->unique_id;
        $title = $temp_row_data->title; 
        $msg = $temp_row_data->notification_msg;
        $id = $temp_row_data->id; //template id
        $get_all_users = BulkEmailTemplateDetail::where('unique_id', $unique_id)->where('template_id', $id)->where('sent_status', 0)->limit(50)->get();

        $get_all_users_count = BulkEmailTemplateDetail::where('unique_id', $unique_id)->where('template_id', $id)->where('sent_status', 0)->get();

        if(count($get_all_users_count) == 0)
            {
                
                $update_main = BulkEmailTemplateMain::find($id);
                $update_main->update([
                    "mail_complete_status" => 1
                ]);
    
                echo "Mail sent successfully.";

                 continue;
            }

        if(count($get_all_users) > 0)
        {

            foreach($get_all_users as $row)
            {

                $user_id = $row->user_id;

                $get_user_info = User::where('id', $user_id)->first();

                // sent mail & notification start

                $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
                $message = array("notifydata" => array('to_id' => $user_id, 'from_id' => $admin_id, 'message' => $msg, 'notify_from' => 'bulk_email', 'notify_title' => $title." In ".$site_name." !..", 'post_id' => "", 'slug' => ""));

                TblPost::send_push_notification($fcmid, $message);


                $mail_data = array("send_maildata" => array('to_id' => $user_id, 'subject' => $title, 'unique_id' => $unique_id));
               $sentMail =  BulkEmailTemplateMain::notification_mail($mail_data);
                
             // sent mail & notification end

            //  update read status
            if($sentMail == true)
            {
                $read_users = BulkEmailTemplateDetail::where('unique_id', $unique_id)->where('template_id', $id)->where('sent_status', 0)->where('user_id', $user_id)->update(["sent_status" => 1]);
            }else{
                echo "Mail not sent properly.";
            }
                
            }
            echo "Mail sent successfully.";

        }

     }
    }else{

        echo "Mail already sent to all users";
    }

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
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'success'); 
            return;
        }
        
        $record = BulkEmailTemplateMain::where('id', $id);
        $record->delete();
        session()->flash('message', 'Deleted successfully.');
        Session()->flash('class', 'success');
    }


}