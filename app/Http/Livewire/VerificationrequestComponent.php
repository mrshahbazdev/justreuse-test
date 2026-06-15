<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Verificationrequest;
use App\Models\VerificationAttachment;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Notifications\Verification_request_Successful;
use Illuminate\Support\Facades\Notification;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use App\Models\User;

class VerificationrequestComponent extends Component
{ 
    use WithPagination;
    public $name,$user_id,$document,$fileName,$file_path,$email,$companycertificate,$iscompany,$validatedData;

    public function store(Request $request)
    {


        // $request->validate([
        //     'document' => 'required|file|mimetypes:image/jpeg,image/png,image/jpg,image/gif,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:2048',
        //     'address_proof' => 'required|file|mimetypes:image/jpeg,image/png,image/jpg,image/gif,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:2048',
        //     'certificate' => 'required|file|mimetypes:image/jpeg,image/png,image/jpg,image/gif,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:2048'
        // ], [
        //     'document.required' => 'Please upload a document for government proof.',
        //     'document.mimetypes' => 'The government proof document must be an image or a supported document format (e.g., DOC, DOCX, XLS, XLSX).',
        //     'document.max' => 'The government proof document must not exceed 2MB in size.',
        //     'address_proof.required' => 'Please upload an address proof document.',
        //     'address_proof.mimetypes' => 'The address proof document must be an image or a supported document format (e.g., DOC, DOCX, XLS, XLSX).',
        //     'address_proof.max' => 'The address proof document must not exceed 2MB in size.',
        //     'certificate.required' => 'Please upload a certificate document.',
        //     'certificate.mimetypes' => 'The certificate document must be an image or a supported document format (e.g., DOC, DOCX, XLS, XLSX).',
        //     'certificate.max' => 'The certificate document must not exceed 2MB in size.',
        // ]);
        
    // If the validation passes, proceed with storing the file.
    
    // Your code to store the file here...

        $user=auth()->user();
        $user_id=$user->id;
        $email=$user->email;
        $name = request()->name;
        $governmentproof = request()->document;
        $certificate = request()->certificate;
        $address_proof = request()->address_proof;
        $iscompany = request()->iscompany;
       
$destinationPath='/verification_request';
if (Verificationrequest::where('user_id', '=', $user_id)->where('is_approved',1)->exists())
    {
        
        return back()
        ->with('success','You have already verified'); 
    }
    else{

$data=Verificationrequest::create([
    'name'=>$name,
    'user_id'=>$user_id,
    'email'=>$email,
    'is_company'=>$iscompany,

]);

$document_certificate=[$governmentproof,$address_proof];
$document=[$governmentproof,$certificate,$address_proof];
if($document[1]==null){
    foreach ($document_certificate as $key => $file) {
        $fileName = $file->getClientOriginalName();
        $extension = time().'.'.$file->getClientOriginalExtension(); 
        $filepath=$file->storeAs($destinationPath, $fileName);
        $fileUpload=new VerificationAttachment();
        $fileUpload->verify_id=$data->id;
        $fileUpload->attachments= $fileName;
        $fileUpload->save();
    //dd('hhdsse');
    }

}else{
        foreach ($document as $key => $file) {
                $fileName = $file->getClientOriginalName();
                $extension = time().'.'.$file->getClientOriginalExtension(); 
                $filepath=$file->storeAs($destinationPath, $fileName);
                $fileUpload=new VerificationAttachment();
                $fileUpload->verify_id=$data->id;
                $fileUpload->attachments= $fileName;
                $fileUpload->save();
        }
    }
        return back()
        ->with('success','Your Request sent to the admin Successfully.Please Wait for admin Approval');
   }
    }

    public function show()
    {
        $details=Verificationrequest::paginate(10); 
        return view('livewire.admin.verification_request.show', compact('details'));
    }

    public function downloads()
    {
      $id=request()->id;
      $path = VerificationAttachment::where('id', $id)->value('attachments');
      return storage::download(('verification_request/'.$path));

      }

    public function approve()
    {
        $id=request()->id;
        $req = Verificationrequest::where("id", $id)->value("user_id");
        $user=User::find($req);
        if(isset($id)){
          //$data=Verificationrequest::where('id' ,$id )->where('is_approved','=', 0)->get();
          $data=Verificationrequest::where('id' ,$id )->value('is_approved');
            if($data == '1'){
             return redirect()->route('admin/verification-request');
            }
           else{
           $value= Verificationrequest::where('id', $id )->update(['is_approved' => 1,'decline_reason' => null]);
            Notification::send($user,new Verification_request_Successful($user,$value,''));
            return redirect()->route('admin/verification-request');
            }
        }
        
    }

    public function decline()
    {
        $id=request()->id;
        return view('livewire.admin.verification_request.decline',compact('id'));
    }
    
    public function decline_approve()
    {
        $id=request()->id;
        $reason=request()->reason;
        $req = Verificationrequest::where("id", $id)->value("user_id");
        $user=User::find($req);
        if(isset($id)){
            $data=Verificationrequest::where('id' ,$id )->value('is_approved');
            if($data== '0'){
                return redirect()->route('admin/verification-request');
            }
            else{
                $file=VerificationAttachment::where('verify_id' ,$id )->get();

                foreach($file as $doc){
                    storage::delete(('public/verification_request'.'/'.$doc->attachments));
            }
            //Verificationrequest::where('id' ,$id )->delete();
                $data=[
                    'reason'=>$reason
                ];
                $data = json_encode($reason);
            Verificationrequest::where('id', $id )->update(['is_approved' => 0,
            'decline_reason'=>$data,
        ]);
            $value=Verificationrequest::where('id' ,$id )->value('is_approved');
            Notification::send($user,new Verification_request_Successful($user,$value,$reason));
            return redirect()->route('admin/verification-request');
            }

    }
}

  public function delete($request_id){

        $req = Verificationrequest::find($request_id);
        $shop = BusinessProfile::where('verifcation_id',$request_id)->first();
        $shop->delete();
        $req->delete();
        $verificationRequestAttachments = VerificationAttachment::where('verify_id', $request_id)->get();

        foreach ($verificationRequestAttachments as $attachment) {
            
            \Storage::delete('public/verification_request/' . $attachment->attachments);
            $attachment->delete();
        }

        return response()->json("Verification and Shop Deleted Successfully");
  }

public function attachments(){
    $id=request()->id;
    $attachments = VerificationAttachment::where('verify_id', $id)->get();
    return response()->json($attachments);
}


}

