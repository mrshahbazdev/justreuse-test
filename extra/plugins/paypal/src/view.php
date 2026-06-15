<!--begin - paypal payment-->
<?php
$currency_symbol = App\Models\Setting::get_admin_default_currency();
?>
<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

   // $("body","#pay_proceed click", function() {
    $("#pay_proceed").click(function() {
        var payment_type = $('input[name="payment_type"]:checked').val();
        if (payment_type == "paypal") {
            paypal_pay_function();
        }
    });

    //begin paypal
    function paypal_pay_function() {

        var baseurl = "<?php echo URL::to('/'); ?>";
        var package_id = $("#selected_pack_id").val();
        var pack_amt = $("#final_total_amount").text();
        var payment_type = $('input[name="payment_type"]:checked').val();
        var live_days = $("#selected_pack_days").val();
        var post_id = "<?php echo request()->post; ?>";
        var coupon_id = $("#coupon_id").val();
        var cid = "<?php echo !empty($currency_symbol) ? $currency_symbol['id'] : ""; ?>";

        if (package_id != 0 && pack_amt != 0 && cid != "") {
            $("#overlay").css('display', 'block');
            //paypal begin
            var url = baseurl + '/paypal-payment-process?pack_amt=' + pack_amt + '&cid=' + cid + '&post_id=' + post_id + '&live_days=' + live_days + '&package_id=' + package_id + '&payment_type=' + payment_type + '&coupon_id=' + coupon_id;
            location.href = url;
        } else {
            toastr.warning('choose any package');
            window.location.reload();
        }

    }
</script>
<!--end - paypal payment-->