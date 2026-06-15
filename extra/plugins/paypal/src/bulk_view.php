<!--begin bulk view - paypal payment-->
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

    $("#pay_proceed").click(function() {
        var payment_type = $('input[name="payment_type"]:checked').val();
        var fa = $("#final_total_amount").val();
        var ids = [];
        // $("input:checkbox[class=checked_pack]:checked").each(function() {
        //     console.log($(this).val());
        //     ids.push($(this).val());
        // });
        var getVal = $('.checked_pack:checkbox:checked').each(function () {
            // console.log($(this).val());
            ids.push($(this).val());
        });
        var cid = "<?php echo !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""; ?>";
        if (fa != "0" && ids.length != "0" && cid != "") {
            if (payment_type == "paypal") {
                paypal_pay_function(ids);
            }
        } else {
            toastr.warning('something wrong');
        }
    });


    //begin paypal
    function paypal_pay_function(ids) {
        var baseurl = "<?php echo URL::to('/'); ?>";
        var package_id = ids;
        var pack_amt = $("#final_total_amount").val();
        var payment_type = $('input[name="payment_type"]:checked').val();
        var cid = "<?php echo !empty($currency_symbol) ? $currency_symbol['id'] : ""; ?>";
        $("#overlay").css('display', 'block');
        //paypal begin
        var url = baseurl + '/paypal-payment-bulk-package?pack_amt=' + pack_amt + '&cid=' + cid + '&package_id=' + package_id + '&payment_type=' + payment_type;
        location.href = url;
    }
</script>
<!--end - paypal payment-->