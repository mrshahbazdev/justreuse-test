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
        var payment_type = $('input[name="payment_type"]:checked').val();
        if (payment_type == "stripe") {
            card_pay_function();
        }
    });


    <?php
    $get_keys = App\Models\TblPaymentsMethod::where('name', "stripe")->pluck('keys_value');
    $keys = json_decode($get_keys[0], true);
    ?>
    //begin card payment
    function card_pay_function() {
        var post_to = "<?php echo URL::to('/stripe_proceed'); ?>";
        var api_publ_key = "<?php echo $keys[0]['STRIPE_PUBLISH_KEY']; ?>";
        var app_name = "<?php echo !empty($settings) ? $settings['name'] : "classified"; ?>";
        var package_id = $("#selected_pack_id").val();
        var pack_amt = $("#final_total_amount").text();
        var payment_type = $('input[name="payment_type"]:checked').val();
        var live_days = $("#selected_pack_days").val();
        var post_id = "<?php echo request()->post; ?>";
        var coupon_id = $("#coupon_id").val();
        var cid = "<?php echo !empty($currency_symbol) ? $currency_symbol['id'] : ""; ?>";

        if (package_id != 0 && pack_amt != 0 && cid != "") {
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
                            tokenId: token.id,
                            pack_amt: pack_amt,
                            post_id: post_id,
                            package_id: package_id,
                            payment_type: payment_type,
                            live_days: live_days,
                            coupon_id: coupon_id,
                            cid: cid
                        },
                        success: (response) => {
                            if (response == "success") {
                                console.log(response);
                                location.href = "<?php echo URL::to('/post'); ?>";
                            } else {
                                console.log(response);
                                location.href = "<?php echo URL::to('/'); ?>";
                            }

                        },
                        error: (error) => {
                            console.log(error);
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