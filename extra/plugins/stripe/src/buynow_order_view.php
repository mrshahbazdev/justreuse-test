<!--begin - stripe payment-->
<script src="https://checkout.stripe.com/checkout.js"> </script>
<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    <?php
    // $get_keys = App\Models\TblPaymentsMethod::where('name', "stripe")->pluck('keys_value');
    // $keys = json_decode($get_keys[0], true);
    //$keys[0]['STRIPE_PUBLISH_KEY'];
    $settings = App\Models\Setting::get_logos();
    ?>
    $('.pay-procceed').on('click', function(event) {
        event.preventDefault();
        $("#overlay").css('display', 'block');
        var post_to = "<?php echo URL::to('/buynow_stripe_orders'); ?>";
        var api_publ_key = $(".seller_pk").val();
        console.log("Api key ",api_publ_key);
        var app_name = "<?php echo !empty($settings) ? $settings['name'] : "classified"; ?>";
        var total_amount = $("#buynow_total_order").val();
        var post_id = $(".selected_post_id").val();
        var address_id = $(".shipping_address_id").val();
        var post_currency = $(".post_currency").val();
        //stripe begin
        var handler = StripeCheckout.configure({
            key: api_publ_key, // your publisher key id
            locale: 'auto',
            token: function(token) {
                // You can access the token ID with `token.id`.
                $.ajax({
                    url: post_to,
                    method: 'post',
                    data: {
                        tokenId: token.id,
                        post_id: post_id,
                        total_amount: total_amount,
                        address_id: address_id
                    },
                    success: (response) => {
                        if (response == "success") {
                            console.log(response);
                            location.href = "<?php echo URL::to('/'); ?>";
                        } else {
                            console.log(response);
                            location.href = "<?php echo URL::to('/'); ?>";
                        }
                    },
                    error: (error) => {
                        console.log(error);
                        toastr.warning('Oops! Something went wrong')
                        // window.location.reload();
                    }
                })
            }
        });
        handler.open({
            name: app_name,
            description: 'Payment',
            amount: total_amount * 100,
            currency: post_currency,
            closed: function () {
                $("#overlay").css('display', 'none');
            }
        });
        //stripe end
    });
</script>
<!--end - stripe payment-->