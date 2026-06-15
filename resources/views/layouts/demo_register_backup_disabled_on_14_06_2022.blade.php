<?php


use Illuminate\Support\Facades\Cookie;
use App\Models\PhoneCodes;
$dir_rtl =  App\Models\Setting::is_dir_rtl();
$class_dir = ($dir_rtl=="true")?'dir=rtl':""; 
$class_dir_slider = ($dir_rtl=="true")?'ar':"";
$class_dir_text_lr = ($dir_rtl == "true") ? 'text-right' : 'text-left';
$class_dir_popup_btn = ($dir_rtl == "true") ? '' : 'sm:space-x-reverse';

$phone_codes = PhoneCodes::orderBy('phonecode','asc')->get()->toArray();

$temp = array_unique(array_column($phone_codes, 'phonecode'));
$phone_codes = array_intersect_key($phone_codes, $temp);

?>


<?php
if(!Cookie::has('demo_ip') && strlen(Cookie::get("demo_ip"))==0){
?>
<!-- report start -->
<div class="fixed z-50 inset-0 overflow-y-auto" style="backdrop-filter: blur(8px);">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" {{$class_dir}}>
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <!-- This element is to trick the browser into centering the modal contents. -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <div class="bg-white px-6 py-4 sm:px-12 sm:py-8">
                        
                   <!--form begin -->

<!--loader begin-->
<div id="loading_start" class="fixed grid justify-items-center items-center top-0 left-0 mx-auto right-0 text-center h-full w-full z-50 hidden">
<div class="absolute top-0 lef-0 h-full w-full"></div>
<div style="border-top-color:transparent" class="w-32 h-32 border-8 border-green-500 border-solid rounded-full animate-spin"></div>
</div>
<!--loader end-->
                    <div class="form-group form-check text-center mb-6">
        <label class="form-check-label inline-block text-gray-800 text-xl text-black font-semibold" for="exampleCheck25">Register to Continue the demo</label>
      </div>
      <p class="signup_error text-red-500 mb-1"></p>

      <div class="grid grid-cols-2 gap-2">
      <div class="form-group mb-6">
          <input type="text" id="d_your_name" class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700
            bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out
            m-0 focus:text-gray-700 focus:bg-white focus:border-green-500 focus:outline-none" placeholder="First Name *">
        </div>

        <div class="form-group mb-6">
          <input type="text" id="d_your_last_name" class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700
            bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out
            m-0 focus:text-gray-700 focus:bg-white focus:border-green-500 focus:outline-none" placeholder="Last Name">
        </div>
</div>


      <div class="grid grid-cols-4 gap-4">
        <div class="form-group mb-6">
          <select id="d_mobile_code" class="px-3 py-1.5 text-base font-normal text-gray-700
            bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out
            m-0 focus:text-gray-700 focus:bg-white focus:border-green-500 focus:outline-none py-0 pl-2 pr-4">
            @foreach($phone_codes as $s)   
            <option value="+{{$s['phonecode']}}" <?php echo($s['phonecode']=="+1")?"selected":""; ?>>+{{$s['phonecode']}}</option>
            @endforeach
          </select>
        </div>
        
        <div class="form-group mb-6 col-span-3">
          <input type="number" id="d_mobile" class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700
            bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0
            focus:text-gray-700 focus:bg-white focus:border-green-500 focus:outline-none"  placeholder="Mobile Number *">
        </div>
      </div>
      
      <div class="form-group mb-6">
        <input type="email"  id="d_email" class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700
          bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0
          focus:text-gray-700 focus:bg-white focus:border-green-500 focus:outline-none" placeholder="Email Address *">
      </div>

      <div class="form-group mb-4" wire:ignore>
          <div class="g-recaptcha w-full" data-tabindex=0 data-sitekey="6LcKXh4eAAAAAOCe-9VwtyKDLbCk-K5czSF-OZEp"></div>
      </div>

      <button id="d_reg" class="
      w-full inline-flex justify-center rounded-md border-2 border-transparent shadow-sm px-4 py-2 pb-3 bg-green-500 text-base font-semibold text-white hover:bg-white hover:text-green-500 hover:border-green-500 focus:outline-none transition-all ease-linear duration-500">Submit</button>
    <!--form end -->
                    </div>
                </div>
            </div>
        </div>
        <!-- report end -->

<script src="https://www.google.com/recaptcha/api.js" async defer="defer"></script>


<script type="text/javascript">
window.onload = function() {
        var $recaptcha = document.querySelector('#g-recaptcha-response');
        if ($recaptcha) {
            $recaptcha.setAttribute("required", "required");
        }
    };
</script>

<script type="text/javascript">
//jQuery.support.cors = true;

$(document).ready(function(){
  $('#d_your_last_name, #d_your_name, #d_mobile, #d_email').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    $('#d_reg').click();
    return false;  
  }
});   

$("#d_reg").click(function(){

        var _your_name = $("#d_your_name").val();
        var _your_last_name = $("#d_your_last_name").val();
        var _your_mobile = $("#d_mobile").val();
        var _your_mobile_code = $("#d_mobile_code").val();
        var _your_email = $("#d_email").val();


        $(".signup_error").html("");

        if (_your_name.length == 0) {
          $(".signup_error").html("Enter First Name");
          $("#d_your_name").focus();
          return false;
        }


        if (_your_mobile.length == 0) {
          $(".signup_error").html("Enter Mobile Number");
          $("#d_mobile").focus();
          return false;
        }

        if (_your_mobile.length < 10 || _your_mobile.length > 12) {
          $(".signup_error").html("Enter Valid Mobile Number");
          $("#d_mobile").focus();
          return false;
        }


        if (_your_email.length == 0) {
          $(".signup_error").html("Enter Email");
          $("#d_email").focus();
          return false;
        }

        if (_your_email.length > 0) {
          if (isEmail(_your_email) == false) {
            $(".signup_error").html("Enter Valid Email");
            $("#d_email").focus();
            return false;
          }
        }

		
		
        //captcha validation
        var response = grecaptcha.getResponse();
        if(response.length == 0) {
          $(".signup_error").html("Captcha field is required. Check it.");
          return false;
        }
		
		    delete $.ajaxSettings.headers["X-CSRF-TOKEN"];//import for cross domain

        addLoader();
        var _data = { "your_first_name": _your_name,"your_last_name": _your_last_name,"your_phonenumber": _your_mobile_code+" "+_your_mobile,
            "your_email": _your_email, "your_ip":'<?php echo $_SERVER["REMOTE_ADDR"];?>' }
        $.ajax({
          type: "post",
          url: "https://www.appcodemonster.com/wp-json/contact-form-7/v1/contact-forms/2206/feedback",
          data: _data,
          success: function(data){
            toastr.success(data.message);
            if(data.message.length>0)
              {
                setDemo();
              }            
            setTimeout(function(){
                resetForm();
                window.location.reload(true);
                removeLoader();
            },(2*1000));
          },
          error: function(jqXHR, textStatus, ex) {
            console.log(textStatus + "," + ex + "," + jqXHR.responseText);
            removeLoader();
          }
        });
        $(".signup_error").html("");
      
      });
  });
  
  function setDemo(){ 
    
    $.ajaxSetup({
      headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    var _url = "{{ url('/save_demo_cookie') }}";
    $.ajax({
      type: 'POST',
      url: _url,
      data: { dum: "-" },
      success: function(data) {
        console.log("its all set");
      }
    });


  }

function resetForm()
{
  $("#d_cmp_name").val("");
  $("#d_your_name").val("");
  $("#d_your_last_name").val("");
  $("#d_mobile").val("");
  $("#d_email").val("");
}
  function addLoader()
  {
  $("#loading_start").removeClass("hidden");
  }
  function removeLoader()
  {
  setTimeout(function(){ $("#loading_start").addClass("hidden"); },2*1000);
  }


  function isEmail(email) {
    // var EmailRegex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    // return EmailRegex.test(email);

    $.ajaxSetup({
      headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    var valid = false;
    var _url = "{{ url('/email_validate') }}";
      $.ajax({
        type: 'POST',
        url: _url,
        async: false,
        data: { email: email },
        success: function(data) {
          var res = JSON.parse(data);
          console.log(res.message);
          valid = res.result;
        }
      });
      return valid;
  }




</script>

<?php } ?>