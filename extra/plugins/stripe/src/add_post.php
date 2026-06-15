<!--begin - stripe payment-->
<?php
$currency_symbol = App\Models\Setting::get_admin_default_currency();
$settings = App\Models\Setting::get_logos();
?>
<script src="https://checkout.stripe.com/checkout.js"> </script>
<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });


    $("#pay_proceed").click(function() {

// check all fields are filed or not

        // var cat = $('#selected_category option').filter(':selected').val();
        // // var curr = $('#currency_id option').filter(':selected').val();
        // var p_price = $("#number-price-sst").val();
        // var title = $("#text-title-sst").val(); 
        // var city = $("#cName").val();
        // var desc = $("#textarea-desc-sst").val();

        // if(cat.length > 0 && p_price.length > 0 && title.length > 0 && city.length > 0 && desc.length > 0)
        // {
        //     var payment_type = $('input[name="payment_type"]:checked').val();
        //     if (payment_type == "stripe") {
        //         card_pay_function();
        //     }
        // }else{
        //     toastr.warning('Fill all required fields.');

        // }

        // testing

        // $('form').find('input, textarea, select').each(function(){
        //     if(!$(this).prop('required')){
        //         // console.log("not required");
        //         // console.log($(this).attr('name'));
        //     } else {
        //         console.log("required");
        //         console.log($(this).attr('name'));
        //         var inpval = $(this).val(); 
        //         if(inpval != '' || inpval != null || inpval != false)
        //         {
        //             console.log("given-" +inpval);    
        //         }else{
        //             console.log("no val");
        //         }


                
        //     }
        // });
		
		    if(validateForm()==true)
            {
                var payment_type = $('input[name="payment_type"]:checked').val();
                if (payment_type == "stripe") {
                    card_pay_function();
                }
            }
        

    });


    <?php
    $get_keys = App\Models\TblPaymentsMethod::where('name', "stripe")->pluck('keys_value');
    $keys = json_decode($get_keys[0], true);
    ?>
    //begin card payment
    function card_pay_function() {
        var post_to = "<?php echo URL::to('/stripe_proceed_add_post'); ?>";
        var api_publ_key = "<?php echo $keys[0]['STRIPE_PUBLISH_KEY']; ?>";
        var app_name = "<?php echo !empty($settings) ? $settings['name'] : "classified"; ?>";
        var pack_amt = $("#final_total_amount").val();
        var cid = "<?php echo !empty($currency_symbol) ? $currency_symbol['id'] : ""; ?>";
        if (pack_amt != 0 && cid != "") {
            $("#overlay").css('display', 'block');
            //stripe begin
            var handler = StripeCheckout.configure({
                key: api_publ_key, // your publisher key id
                locale: 'auto',
                token: function(token) {
                    // You can access the token ID with `token.id`.
                    // Get the token ID to your server-side code for use.
                    $.ajax({
                        url: post_to,
                        method: 'post',
                        data: {
                            token: token.id,
                            total_amount: pack_amt
                        },
                        dataType: 'JSON',
                        success: (response) => {
                            if (response.result == "success") {
                                $("#paid_id").val(response.stripe_id);   
                                $("#post_json_insert").trigger("click");
                            } else {
                                toastr.warning('Oops! Something went wrong');
                            }
                        },
                        error: (error) => {
                            toastr.warning('Oops! Something went wrong');
                            window.location.reload();
                        }
                    })
                }
            });
            handler.open({
                name: app_name,
                description: 'Payment',
                amount: pack_amt * 100,
                currency: '<?php echo $currency_symbol['short_code']; ?>',
                closed: function () {
                    $("#overlay").css('display', 'none');
                }
            });
            //stripe end
        } else {
            toastr.warning('choose any package');
            window.location.reload();
        }
    }
</script>
<!--end - stripe payment-->