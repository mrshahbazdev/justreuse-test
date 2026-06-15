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
    $get_keys = App\Models\TblPaymentsMethod::where('name', "stripe")->pluck('keys_value');
    $keys = json_decode($get_keys[0], true);
    $settings = App\Models\Setting::get_logos();
    //$currency_code = App\Models\TblCurrency::where('id', $settings['default_currency'])->pluck('short_code')->first();

    $currency_symbol = App\Models\Setting::get_admin_default_currency();
    $currency_code = $currency_symbol['short_code'];
    ?>
    $('#BannerAdsForm').on('submit', function(event) {
        event.preventDefault();
        $("#overlay").css('display', 'block');
        var payment_type = $('input[name="payment_type"]:checked').val();
        if (payment_type == "stripe") {
            var post_to = "<?php echo URL::to('/bannerads_stripe_proceed'); ?>";
            var api_publ_key = "<?php echo $keys[0]['STRIPE_PUBLISH_KEY']; ?>";
            var app_name = "<?php echo !empty($settings) ? $settings['name'] : "classified"; ?>";
            var total_amount = $("#final_total_amount").val();
            var formData = new FormData(this);
            var web_banner = document.getElementById("web_banner");
            var web_banner_file = web_banner.files[0];
            var app_banner = document.getElementById("app_banner");
            var app_banner_file = app_banner.files[0];

            if (!web_banner_file || !web_banner_file.type.match(/image.*/) || !app_banner_file || !app_banner_file.type.match(/image.*/)) {
                toastr.warning('Invalid Banners');
                return false;
            } else if (total_amount > 0) {
                //stripe begin
                var handler = StripeCheckout.configure({
                    key: api_publ_key, // your publisher key id
                    locale: 'auto',
                    token: function(token) {
                        // You can access the token ID with `token.id`.
                        // Get the token ID to your server-side code for use.
                        $('#res_token').html(JSON.stringify(token));
                        formData.append("token", token.id);
                        formData.append("test_banner", app_banner_file);
                        $.ajax({
                            url: post_to,
                            method: 'post',
                            data: formData,
                            cache: false,
                            processData: false,
                            contentType: false,
                            success: (response) => {
                                if (response == "success") {
                                    location.href = "<?php echo URL::to('/my-banner-ads') ?>";
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
                    amount: total_amount * 100,
                    currency: '<?php echo $currency_code; ?>',
                    closed: function () {
                        $("#overlay").css('display', 'none');
                    }
                });
                //stripe end
            } else {
                toastr.warning('Invalid Amount!');
                window.location.reload();
            }
        }
    });
</script>
<!--end - stripe payment-->