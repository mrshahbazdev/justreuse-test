<!--begin - paypal payment-->
<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    //form submit
    <?php
    //$settings = App\Models\Setting::get_logos();
    $currency_symbol = App\Models\Setting::get_admin_default_currency();
    $currency_id = $currency_symbol['id'];
    ?>
    $('#BannerAdsForm').on('submit', function(event) {
        event.preventDefault();
        var payment_type = $('input[name="payment_type"]:checked').val();
        if (payment_type == "paypal") {
            var baseurl = "<?php echo URL::to('/'); ?>";
            var total_amount = $("#final_total_amount").val();
            var formData = new FormData(this);
            if (total_amount > 0) {
                save_banner_ads(formData);
            } else {
                toastr.warning('Please try again later!');
            }
        }
    });

    function save_banner_ads(formData) {
        var post_to = "<?php echo URL::to('/save_banner_ads'); ?>";
        var baseurl = "<?php echo URL::to('/'); ?>";
        var currency_code = "<?php echo $currency_id; ?>";
        $.ajax({
            type: 'POST',
            url: post_to,
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(data) {
                var item = JSON.parse(data);
                if (item.result == "success") {
                    $("#overlay").css('display', 'block');
                    var url = baseurl + '/bannerad-paypal-payment-process?total_amount=' + item.total_amount + '&id=' + item.last_id + "&cid=" + currency_code;
                    location.href = url;
                } else {
                    toastr.warning('Please try again later!');
                }
            }
        });
    }
</script>
<!--end - paypal payment-->