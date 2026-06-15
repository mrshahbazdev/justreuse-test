<?php

namespace App\Http\Livewire\Admin;
use App\Models\EmailTemplate;
use Livewire\Component;
use App\Models\Setting;
use App\Models\User;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Artisan;
use Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Svg\Tag\Rect;

class EmailTemplateComponent extends Component {

    use WithFileUploads;
    use WithPagination;

    public $to_user_name,$to_email,$to_message;
    
    public $updateMode=false;
    public $insertMode=false;
    public $edit_info;
    public $edited_id,$subject_title,$html_content,$key,$content;

    public function mount()
    {
        
    }

    public function render() {
        
        return view('livewire.admin.email-template.compo', [
            'settings' => EmailTemplate::where('active',1)->paginate(50),
        ]);

    }

    public function create()
    {
        $this->insertMode=true;
    }
    // public function insert(){
    //     //start check demo user
    //         $isDemoUser = User::isDemoUser();
    //         if($isDemoUser["result"]==true)
    //         {
    //             session()->flash('message', $isDemoUser["message"]);
    //             Session()->flash('class', 'error');
    //             return redirect('admin/email-template');
    //         }
    //     //end check demo user

    //     $validatedDate = $this->validate([
    //         'subject_title' => 'required',
    //         'html_content' => 'required',
    //         'key'=>'required',
    //         'content'=>'required'
    //     ]);

    //     $data = EmailTemplate::insert([
    //         'subject_title' => $this->subject_title,
    //         'html_content' => $this->html_content,
    //         'key'=>$this->key,
    //         'content' => $this->content
    //         ]);

	// 		session()->flash('message', 'Template created Successfully.');
    //         return redirect('admin/email-template');
    // }

    public function edit($id) 
    {
        $this->edited_id = $id;
        $template_data = EmailTemplate::find($id);
        $this->edit_info = $template_data;
        $this->updateMode = true;
        $this->subject_title = $template_data->subject_title;
        $this->html_content = $template_data->html_content;
        $this->key = $template_data->key;
        $this->content = $template_data->content;
      
    }
    public function update()
    {
      
        //start check demo user
            $isDemoUser = User::isDemoUser();
            if($isDemoUser["result"]==true)
            {
                session()->flash('message', $isDemoUser["message"]);
                Session()->flash('class', 'error');
                return redirect('admin/email-template');
            }
        //end check demo user

        //$this->edited_id
       
        $eT = EmailTemplate::find($this->edited_id);

        $eT->update([
                'subject_title' => $this->subject_title,
                'content' => $this->content
        ]);

		session()->flash('message', 'Template updated Successfully.');
        return redirect('admin/email-template');

    }


    //testing begin
    // public function submit_mail(){

    //     $t_username = $this->to_user_name;
    //     $t_email = $this->to_email;
    //     $t_message = $this->to_message;
    //     $t_subject = "CRM - Email Template";
    //     $company_email = "crm-servicea@gmail.com";

    //     $validatedDate = $this->validate([
    //         'to_email' => 'required|email',
    //         'to_user_name' => 'required',
    //         'to_message'=>'required'
    //     ]);
    //     $settings = Setting::get_logos();
    //     $logo = asset('storage/'.$settings['logo']); 
    //     //$logo = public_path('storage/'.$settings['logo']);


    //     $content =  file_get_contents(base_path('resources/views/livewire/admin/email-template/template1_message.blade.php'), true);

    //     $content=str_replace('[[company_logo]]', $logo, $content);
    //     $content=str_replace('[[to_user_name]]', $t_username, $content);
    //     $content=str_replace('[[to_message]]', $t_message, $content);
        


    //     //dd($content);
    //     $this->sentEmail($t_username,$content,$t_email,$t_subject,$company_email);

    //     //dd($t_username,$t_email,$t_message);


    // }


    // public function sentEmail($t_username,$content,$t_email,$t_subject,$company_email)
    // {
    //     Mail::send('livewire.admin.email-template.smtp',array('content'=>$content),
    //     function($message) use ($t_username, $t_email,$t_subject,$company_email) {
    //         $message->to($t_email, $t_username)->subject($t_subject);
    //         $message->from($company_email,$company_email);
    //     });

    //     if (Mail::failures()) {
    //         // return response showing failed emails
    //         dd("failed to sent");
    //     }
    //     else{
    //         $this->clearall();
    //     }

    // }


    // public function template1_message()
    // {
    //     return view('livewire.admin.email-template.template1_message');
    // }
    
    // public function clearall()
    // {
    //     $this->to_user_name="";
    //     $this->to_email="";
    //     $this->to_message="";
    // }
    //testing end

    // for back button redirect page


    public function email_tem(Request $request){

        $sub = $request->input('subject_title');
        $key = $request->input('key');
        $content = $request->input('content');

        // $validatedDate = $this->validate([
        //     'subject_title' => 'required',
        //     'key'=>'required',
        //     'content'=>'required'
        // ]);
        // dd($content);
        $ckedit = EmailTemplate::create([
            'subject_title' => $sub,
            'key' => $key,
            'content' => $content,
        ]);
        return redirect('admin/email-template');
    }

    // public function edit($id)
    // {

    //     $this->edit_info = EmailTemplate::find($id);
    //     $subject_title = $this->edit_info->subject_title;
    //     $key = $this->edit_info->key;
    //     $content = $this->edit_info->content;
    //     $this->updateMode=true; 
    // }

    // public function update(Request $request)
    // {
    //     $id = $request->id;
    //     $eT = EmailTemplate::find($id);
    //     $eT->update([
    //         'subject_title' => $request->subject_title,
    //         'content' => $request->content,
    //     ]);

    //     return redirect('admin/email-template');
    // }
}
