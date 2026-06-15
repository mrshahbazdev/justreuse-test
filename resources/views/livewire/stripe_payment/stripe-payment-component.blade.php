<section class="relative w-full h-full py-2 min-h-screen">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-2 mb-2">
                <h3 class="text-center"></h3>
                <hr>
            </div>
            <div class="col-md-12 mt-2 mb-2">
                <!-- <pre id="res_token"></pre> -->
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 offset-md-4">
                <div class="form-group">
                </div>
                <button type="button" class="pay-btn" style="opacity: 0;"></button>
            </div>
        </div>
    </div>
    <?php
    $get_keys = App\Models\TblPaymentsMethod::where('name', "stripe")->pluck('keys_value');
    $keys = json_decode($get_keys[0], true);
    $settings = App\Models\Setting::get_logos();
    $app_name = !empty($settings) ? $settings['name'] : "classified";
    $currency_symbol = App\Models\Setting::get_admin_default_currency();
    $currency_id = $currency_symbol['id'];
    $paid_for = $_GET['paid_for'];
    if ($paid_for == "package") { ?>
        <!---------------------------------- payment start here for single post self fast and package ------------------------------->
        <?php $cid = $_GET['cid']; ?>
        <input type="hidden" class="amount" value="<?php echo $_GET['pack_amt']; ?>" />
        <input type="hidden" class="ids" value="<?php echo $_GET['package_id']; ?>" />
        <input type="hidden" class="pk" value="<?php echo $keys[0]['STRIPE_PUBLISH_KEY']; ?>" />
        <input type="hidden" class="cc" value="<?php echo !empty($_GET['coupon_id']) ? $_GET['coupon_id'] : ""; ?>" />
        <input type="hidden" class="pid" value="<?php echo !empty($_GET['post_id']) ? $_GET['post_id'] : ""; ?>" />
        <input type="hidden" class="ldays" value="<?php echo !empty($_GET['live_days']) ? $_GET['live_days'] : ""; ?>" />
        <input type="hidden" class="uid" value="<?php echo $_GET['uid']; ?>" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script src="https://checkout.stripe.com/checkout.js"> </script>
        <script type="text/javascript">
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            });
            $("document").ready(function() {
                $(".pay-btn").trigger("click");
            });
            $(".pay-btn").click(function() {
                var payment_type = "stripe";
                var fa = $(".amount").val();
                var ids = $(".ids").val();
                var cid = "<?php echo $cid; ?>";
                if (fa != "0" && ids.length != "0" && cid != "") {
                    if (payment_type == "stripe") {
                        card_pay_function(ids);
                    }
                } else {
                    toastr.warning('something wrong');
                }
            });

            function card_pay_function(ids) {
                var api_publ_key = $(".pk").val();
                var app_name = "<?php echo $app_name; ?>";
                var package_id = ids;
                var post_id = $(".pid").val();
                var coupon_code = $(".cc").val();
                var live_days = $(".ldays").val();
                var pack_amt = $(".amount").val();
                var payment_type = "stripe";
                var uid = $(".uid").val();
                var cid = "<?php echo $cid; ?>";

                if (post_id != "" && pack_amt != 0) {
                    //stripe begin
                    var handler = StripeCheckout.configure({
                        key: api_publ_key, // your publisher key id
                        locale: 'auto',
                        token: function(token) { // You can access the token ID with `token.id`. Get the token ID to your server-side code for use.
                            //$('#res_token').html(JSON.stringify(token));
                            $.ajax({
                                url: "<?php echo URL::to('/app_stripe_single_pack'); ?>",
                                method: 'post',
                                data: {
                                    tokenId: token.id,
                                    pack_amt: pack_amt,
                                    package_id: package_id,
                                    payment_type: payment_type,
                                    uid: uid,
                                    live_days: live_days,
                                    post_id: post_id,
                                    coupon_id: coupon_code,
                                    cid: cid
                                },
                                success: (response) => {
                                    if (response == "success") {
                                        var path = "<?php echo URL::to("/"); ?>";
                                        var result = path + "/thankyou?status=success&type=single&pack_id=" + package_id + '&cid=' + cid + '&uid=' + uid + '&amount=' + pack_amt + '&paid_for=package';
                                        location.href = result;
                                    } else {
                                        var path = "<?php echo URL::to("/"); ?>";
                                        var result = path + "/thankyou?status=failed&type=single&pack_id=" + package_id + '&cid=' + cid + '&uid=' + uid + '&amount=' + pack_amt + '&paid_for=package';
                                        location.href = result;
                                    }
                                },
                                error: (error) => {
                                    console.log(error);
                                    toastr.warning('Oops! Something went wrong');
                                }
                            })
                        }
                    });
                    handler.open({
                        name: app_name,
                        description: 'Payment Bulk Pack',
                        amount: pack_amt * 100,
                        currency: "<?php echo $currency_symbol['short_code']; ?>"
                    });
                    //stripe end
                } else if (package_id != 0 && pack_amt != 0 && cid != "") {
                    //stripe begin
                    var handler = StripeCheckout.configure({
                        key: api_publ_key, // your publisher key id
                        locale: 'auto',
                        token: function(token) {
                            // You can access the token ID with `token.id`.
                            // Get the token ID to your server-side code for use.
                            $.ajax({
                                url: "<?php echo URL::to('/app_stripe_bulk_pack'); ?>",
                                method: 'post',
                                data: {
                                    tokenId: token.id,
                                    pack_amt: pack_amt,
                                    package_id: package_id,
                                    payment_type: payment_type,
                                    uid: uid,
                                    cid: "<?php echo $cid; ?>"
                                },
                                success: (response) => {
                                    if (response == "success") {
                                        var path = "<?php echo URL::to("/"); ?>";
                                        var result = path + "/thankyou?status=success&type=bulk&pack_id=" + package_id + '&cid=' + cid + '&uid=' + uid + '&amount=' + pack_amt + '&paid_for=package';
                                        location.href = result;
                                    } else {
                                        var path = "<?php echo URL::to("/"); ?>";
                                        var result = path + "/thankyou?status=failed&type=bulk&pack_id=" + package_id + '&cid=' + cid + '&uid=' + uid + '&amount=' + pack_amt + '&paid_for=package';
                                        location.href = result;
                                    }
                                },
                                error: (error) => {
                                    console.log(error);
                                    var path = "<?php echo URL::to("/"); ?>";
                                    var result = path + "/thankyou?status=failed&type=bulk&pack_id=" + package_id + '&cid=' + cid + '&uid=' + uid + '&amount=' + pack_amt + '&paid_for=package';
                                    location.href = result;
                                }
                            })
                        }
                    });
                    handler.open({
                        name: app_name,
                        description: 'Payment Bulk Pack',
                        amount: pack_amt * 100,
                        currency: "<?php echo $currency_symbol['short_code']; ?>"
                    });
                    //stripe end
                } else {
                    var path = "<?php echo URL::to("/"); ?>";
                    var result = path + "/thankyou?status=failed&type=bulk&pack_id=" + package_id + '&cid=' + cid + '&uid=' + uid + '&amount=' + pack_amt + '&paid_for=package';
                    location.href = result;
                }
            }
        </script>
        <!------------------------------ payment end here for single post self fast and bulck package ------------------------->
    <?php } else if ($paid_for == "buynow") { ?>
        <!------------------------------ payment start here for buynow ------------------------->
        <?php
        $seller_publishkey = App\Models\User_profile::where('user_id', $_GET['sid'])->pluck('stripe_public_key')->first();
        $currency = App\Models\TblCurrency::where('id', $_GET['cid'])->pluck('short_code')->first();
        ?>
        <input type="hidden" class="amount" value="<?php echo $_GET['pack_amt']; ?>" />
        <input type="hidden" class="pk" value="<?php echo $seller_publishkey; ?>" />
        <input type="hidden" class="uid" value="<?php echo $_GET['uid']; ?>" />
        <input type="hidden" class="lid" value="<?php echo $_GET['lid']; ?>" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script src="https://checkout.stripe.com/checkout.js"> </script>
        <script type="text/javascript">
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            });
            $("document").ready(function() {
                $(".pay-btn").trigger("click");
            });
            $(".pay-btn").click(function() {
                var payment_type = "stripe";
                var fa = $(".amount").val();
                var lid = $(".lid").val();
                if (fa != "0" && lid.length != "0") {
                    var api_publ_key = $(".pk").val();
                    var app_name = "<?php echo $app_name; ?>";
                    var pack_amt = $(".amount").val();
                    var payment_type = "stripe";
                    var uid = $(".uid").val();
                    var lid = $(".lid").val();
                    var cid = "<?php echo $_GET['cid']; ?>";
                    if (lid != "" && pack_amt != 0) {
                        //stripe begin
                        var handler = StripeCheckout.configure({
                            key: api_publ_key, // your publisher key id
                            locale: 'auto',
                            token: function(token) { // You can access the token ID with `token.id`.Get the token ID to your server-side code for use.
                                $.ajax({
                                    url: "<?php echo URL::to('/app_stripe_buynow'); ?>",
                                    method: 'post',
                                    data: {
                                        tokenId: token.id,
                                        pack_amt: pack_amt,
                                        lid: lid,
                                        uid: uid,
                                        cid: cid,
                                    },
                                    success: (response) => {
                                        if (response == "success") {
                                            var path = "<?php echo URL::to("/"); ?>";
                                            var result = path + "/thankyou?status=success&type=buynow&uid=" + uid + '&cid=' + cid + '&amount=' + pack_amt + "&lid=" + lid + '&paid_for=buynow';
                                            location.href = result;
                                        } else {
                                            var path = "<?php echo URL::to("/"); ?>";
                                            var result = path + "/thankyou?status=failed&type=buynow&uid=" + uid + '&cid=' + cid + '&lid=' + lid + '&amount=' + pack_amt + '&paid_for=buynow';
                                            location.href = result;
                                        }
                                    },
                                    error: (error) => {
                                        console.log(error);
                                        var path = "<?php echo URL::to("/"); ?>";
                                        var result = path + "/thankyou?status=failed&type=buynow&uid=" + uid + '&cid=' + cid + '&lid=' + lid + '&amount=' + pack_amt + '&paid_for=buynow';
                                       location.href = result;
                                    }
                                })
                            }
                        });
                        handler.open({
                            name: app_name,
                            description: 'Payment buynow products',
                            amount: pack_amt * 100,
                            currency: '<?php echo $currency; ?>'
                        });
                        //stripe end
                    } else {
                        var path = "<?php echo URL::to("/"); ?>";
                        var result = path + "/thankyou?status=failed&type=buynow&uid=" + uid + '&cid=' + cid + '&lid=' + lid + '&amount=' + pack_amt + '&paid_for=buynow';
                        location.href = result;
                    }
                } else {
                    var path = "<?php echo URL::to("/"); ?>";
                    var result = path + "/thankyou?status=failed&type=buynow&uid=" + uid + '&cid=' + cid + '&lid=' + lid + '&amount=' + pack_amt + '&paid_for=buynow';
                   location.href = result;
                }
            });
        </script>
        <!------------------------------ payment end here for banner advertisement ------------------------->
    <?php } else { ?>
        <!------------------------------ payment start here for banner advertisement ------------------------->
        <?php
        $currency = App\Models\TblCurrency::where('id', $_GET['cid'])->pluck('short_code')->first();
        ?>
        <input type="hidden" class="amount" value="<?php echo $_GET['pack_amt']; ?>" />
        <input type="hidden" class="pk" value="<?php echo $keys[0]['STRIPE_PUBLISH_KEY']; ?>" />
        <input type="hidden" class="uid" value="<?php echo $_GET['uid']; ?>" />
        <input type="hidden" class="lid" value="<?php echo $_GET['lid']; ?>" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script src="https://checkout.stripe.com/checkout.js"> </script>
        <script type="text/javascript">
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            });
            $("document").ready(function() {
                $(".pay-btn").trigger("click");
            });
            $(".pay-btn").click(function() {
                var payment_type = "stripe";
                var fa = $(".amount").val();
                var lid = $(".lid").val();
                if (fa != "0" && lid.length != "0") {
                    var api_publ_key = $(".pk").val();
                    var app_name = "<?php echo $app_name; ?>";
                    var pack_amt = $(".amount").val();
                    var payment_type = "stripe";
                    var uid = $(".uid").val();
                    var lid = $(".lid").val();
                    var cid = "<?php echo $_GET['cid']; ?>";
                    if (lid != "" && pack_amt != 0) {
                        //stripe begin
                        var handler = StripeCheckout.configure({
                            key: api_publ_key, // your publisher key id
                            locale: 'auto',
                            token: function(token) { // You can access the token ID with `token.id`.Get the token ID to your server-side code for use.
                                //$('#res_token').html(JSON.stringify(token));
                                $.ajax({
                                    url: "<?php echo URL::to('/app_stripe_banner_ads'); ?>",
                                    method: 'post',
                                    data: {
                                        tokenId: token.id,
                                        pack_amt: pack_amt,
                                        lid: lid,
                                        uid: uid,
                                        cid: cid
                                    },
                                    success: (response) => {
                                        if (response == "success") {
                                            var path = "<?php echo URL::to("/"); ?>";
                                            var result = path + "/thankyou?status=success&type=bannerads&uid=" + uid + '&cid=' + cid + '&amount=' + pack_amt + "&lid=" + lid + '&paid_for=bannerads';
                                            location.href = result;
                                        } else {
                                            var path = "<?php echo URL::to("/"); ?>";
                                            var result = path + "/thankyou?status=failed&type=bannerads&uid=" + uid + '&cid=' + cid + '&lid=' + lid + '&amount=' + pack_amt + '&paid_for=bannerads';
                                            location.href = result;
                                        }
                                    },
                                    error: (error) => {
                                        console.log(error);
                                        var path = "<?php echo URL::to("/"); ?>";
                                        var result = path + "/thankyou?status=failed&type=bannerads&uid=" + uid + '&cid=' + cid + '&lid=' + lid + '&amount=' + pack_amt + '&paid_for=banneradss';
                                        location.href = result;
                                    }
                                })
                            }
                        });
                        handler.open({
                            name: app_name,
                            description: 'Payment for banner advertisement',
                            amount: pack_amt * 100,
                            currency: '<?php echo $currency; ?>'
                        });
                        //stripe end
                    } else {
                        var path = "<?php echo URL::to("/"); ?>";
                        var result = path + "/thankyou?status=failed&type=bannerads&uid=" + uid + '&cid=' + cid + '&lid=' + lid + '&amount=' + pack_amt + '&paid_for=bannerads';
                        location.href = result;
                    }
                } else {
                    var path = "<?php echo URL::to("/"); ?>";
                    var result = path + "/thankyou?status=failed&type=bannerads&uid=" + uid + '&cid=' + cid + '&lid=' + lid + '&amount=' + pack_amt + '&paid_for=bannerads';
                    location.href = result;
                }
            });
        </script>
        <!------------------------------ payment end here for banner advertisement ------------------------->
    <?php } ?>
    <script>
        $("body").delegate(".field-options ol", "load", function() {
            $("iframe.stripe_checkout_app").css("cssText", "width: 100% !important;height: 100% !important;left: 0px !important;");
            $(".appView").css("cssText", "zoom: 200% !important;");
            $(".a.close.right").css("cssText", "display: none !important;");
        });
    </script>
</section>