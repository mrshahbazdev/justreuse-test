<div>
@if($message = Session::get('message'))
<?php $result = Session::get('result');
$color = ($result=="1")?"bg-green-700":"bg-yellow-500";

if($result=="1"){
echo "<script>toastr.success('$message');</script>";
}
else{
  echo "<script>toastr.warning('$message');</script>";
}

?>
	
@endif
    <div class=py-24>
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-16">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left">
          <div class="flex flex-col mb-4" id="fullsection">
            <h1 class="text-xl font-medium text-gray-900 mb-2">Update Keys </h1>
         <?php
       
         $keysInfos = json_decode($keys_info[0]->keys_info,true);
         ?>
         <form action="{{ route('admin/key-update') }}" method="POST">
         @csrf
         @foreach($keysInfos as $keysInfo)
         <?php
         if(!empty($keys_info[0]->keys_value)){
          $a = json_decode($keys_info[0]->keys_value,true);
          $get_key_val = "";
          foreach($a as $a){
           $get_key_val = array_intersect_key($a,array_flip(array($keysInfo[0]['name'])));
           if(!empty($get_key_val)){
             break;
           }
          }
          $final_val = array_values($get_key_val);
         }else{
          $final_val="";
         }
           
         ?>
            <div class="mt-6">      
             
              <div>
              <label class="block text-gray-700 text-sm font-bold mb-2">{{$keysInfo[0]['label']}} <span class="required-asterisk" style="display:inline"> *</span></label>
              <input type="text" name="{{$keysInfo[0]['name']}}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter {{$keysInfo[0]['label']}}" required value="<?php echo !empty($final_val) ? $final_val[0] : "";?>">                    
              <?php
              if(isset($keysInfo[0]['notes'])){ ?>
              <span class="text-xs pt-2"><?php echo $keysInfo[0]['notes'];?></span>
              <?php }
              ?>
              </div>
             
           </div>
           @endforeach
           <input type="hidden" value="{{$keys_info[0]->id}}" name="payment_id"/>
           <div class="justify-center">
            <span class="">
              <button type="submit" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                  Save
              </button>
            </span>
          </div>
          </form> 
          </div>
         
        </div>
    </div>
</div>
<style>
span.required-asterisk {
    color: red;
}
</style>