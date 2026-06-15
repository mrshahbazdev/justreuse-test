  <div>

  <?php 
    $get_meta = App\Models\TblOtherpage::get_meta('login-with-otp');
    $meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
    $meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
    $meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
  ?>

    <?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
        <?php $__env->startSection('meta_title', $meta_title); ?>
        <?php $__env->startSection('meta_keywords', $meta_keywords); ?>
        <?php $__env->startSection('meta_description', $meta_description); ?>
	  <?php endif; ?>

		<div class="mx-4 sm:mx-12 md:mx-20 xl:mx-28 py-6 sm:py-10 lg:py-16">
			<h6 class="text-center text-2xl capitalize text-white font-normal mb-6">
			  <?php echo e(__('messages.sign in')); ?>

			</h6>
			
			<div class="bg-white w-full rounded-xl mb-2">
				<div class="py-6 sm:py-10 lg:py-16 mx-4 sm:mx-10 flex-auto">
					  <input type="hidden" name="_token" value="">
					  <div class="relative w-full mb-3">
						<input type="text" id="phone" name="phone" value="" class="rounded shadow-sm w-full border bg-gray-200 border-gray-200 placeholder-gray-500 text-gray-500 px-3 py-4 pl-6 sm:pl-10 sm:rounded-xl outline-none focus:outline-none" placeholder="<?php echo e(__('messages.phone')); ?>">
						<input type="hidden" id="is-otp-sent" />
					  </div>
					  <div class="relative w-full mb-3 mt-4 otp-visible">
						<input type="text" autocomplete="off" id="otp" name="otp" class="w-2/4 border bg-gray-200 border-gray-200 px-3 py-3 sm:py-4 pl-6 sm:pl-10  bg-transparent rounded sm:rounded-xl text-gray-500 text-sm outline-none focus:outline-none focus:ring w-full" placeholder="<?php echo e(__('messages.OTP')); ?>">
					  </div>
					  <input type="button" class="cursor-pointer resend-otp text-center text-lg text-gray-500 font-semibold no-underline bg-transparent mt-2" value="<?php echo e(__('messages.resend otp')); ?>" />
					  <div class="text-center mt-4 sm:mt-7">
						<input type="button" value="<?php echo e(__('messages.send otp')); ?>" class="sendotp text-white px-2 py-2 pb-3 sm:py-3 sm:pb-4 bg-green-500 rounded sm:rounded-xl text-sm sm:text-base md:text-lg font-bold focus:outline-none hover:bg-white hover:text-green-500 border-2 border-green-500 w-full cursor-pointer ease-linear transition-all duration-150" />
					  </div>
				</div>
			</div>
		</div>
  <script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    function getIp(callback) {
      fetch('https://ipinfo.io/json', {
          headers: {
            'Accept': 'application/json'
          }
        })
        .then((resp) => resp.json())
        .catch(() => {
          return {
            country: 'in',
          };
        })
        .then((resp) => callback(resp.country));
    }
    const phoneInputField = document.querySelector("#phone");
    const phoneInput = window.intlTelInput(phoneInputField, {
      initialCountry: "auto",
      separateDialCode: true,
      geoIpLookup: getIp,
      utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
    });
    $(".otp-visible").hide();
    $(".resend-otp").hide();
    $(document).ready(function() {

      $('#phone').on('keypress', function(e) {
        var $this = $(this);
        var regex = new RegExp("^[0-9\b]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        // for 10 digit number only
        if ($this.val().length > 9) {
          e.preventDefault();
          return false;
        }
        if (e.charCode < 54 && e.charCode > 47) {
          if ($this.val().length == 0) {
            e.preventDefault();
            return false;
          } else {
            return true;
          }

        }
        if (regex.test(str)) {
          return true;
        }
        e.preventDefault();
        return false;
      });

    });


    $(document).ready(function() {
      $(".sendotp").click(function(e) {
        var phone = $("#phone").val();
        var is_otp_sent = $("#is-otp-sent").val();
        if (is_otp_sent == "") {
          if (phone != "") {
            const phoneNumber = phoneInput.getNumber();
            send_otp(phone, phoneNumber);
          } else {
            toastr.warning("Please enter Phone Number");
          }
        } else {
          var otp = $("#otp").val();
          if (otp == "") {
            toastr.warning("Please enter OTP");
          } else {
            const phoneNumber = phoneInput.getNumber();
            verify_otp(phoneNumber, otp);
          }
        }

      });

      $(document).ready(function() {
        $(".resend-otp").click(function(e) {
          var phone = $("#phone").val();
          if (phone != "") {
            const phoneNumber = phoneInput.getNumber();
            send_otp(phone, phoneNumber);
          } else {
            toastr.warning("Please enter Phone Number");
          }
        });
      });


      function send_otp(phone, phoneNumber) {
        $.ajax({
          type: 'POST',
          dataType: 'json',
          url: "<?php echo e(URL::to('send_otp')); ?>",
          data: {
            phone: phone,
            e164: phoneNumber
          },
          success: function(data) {
            if (data.result == "error") {
              toastr.warning(data.message);
            } else {
              $(".resend-otp").show(1000);
              $(".otp-visible").show(1000);
              $("#is-otp-sent").val(1);
              $(".sendotp").val("Verify OTP");
              toastr.success(data.message);
            }

          }
        });
      }

      function verify_otp(phone, otp) {
        $.ajax({
          type: 'POST',
          dataType: 'json',
          url: "<?php echo e(URL::to('verify_otp')); ?>",
          data: {
            phone: phone,
            otp: otp
          },
          success: function(data) {
            if (data.result == "error") {
              toastr.warning(data.message);
            } else {
              toastr.success(data.message);
              window.location.href = data.return_url;
            }
          }
        });
      }
    });
  </script>


  </div><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/login-with-otp.blade.php ENDPATH**/ ?>