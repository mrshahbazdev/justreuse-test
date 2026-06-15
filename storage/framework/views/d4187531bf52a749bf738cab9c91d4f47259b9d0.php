<section class="relative w-full h-full py-2 min-h-screen">
    <div class="container">
        <!-- get user info -->
        <?php
        $uid = $_GET['uid'];
        $user_info = App\Models\User::where('id', $uid)->first();
        ?>
        <?php if ($_GET['status'] == "success") { ?>
            <h1 class="text-center title">THANKYOU</h1>
            <img style="width: 30%;" src="<?php echo URL::to('public/images/tick.png'); ?>" />
            <p class="content">Payment paid successfully!.</p>

        <?php } else if ($_GET['status'] == "failed") { ?>
            <img style="width: 50%;" src="<?php echo URL::to('public/images/failed.png'); ?>" />
            <h1 class="text-center title">SORRY</h1>
            <p class="content">Sorry, your payment was failed, please try again.</p>
        <?php } ?>

        <?php if ($_GET['paid_for'] == "package") { ?>
            <?php
            $currency_symbol = App\Models\TblCurrency::where('id', $_GET['cid'])->pluck('currency_hex')->first();
            ?>
            <div class="pakage_info">
                <p>Hi, <b><?php echo $user_info->name; ?></b></p>
                <p>Your package Infos are given below,</p>
                <?php if ($_GET['type'] == "single") { ?>
                    <?php
                    $pack_id = $_GET['pack_id'];
                    $pack_info = App\Models\Package::where('id', $pack_id)->first();
                    ?>
                    <p><b>Package Name </b>: <span><?php echo $pack_info->name; ?></span></p>
                    <p><b>Package Duration </b>: <span><?php echo $pack_info->duration; ?> days</span></p>
                    <p><b>Price </b> : <span><?php echo $currency_symbol; ?> <?php echo $_GET['amount']; ?></span></p>
                <?php } else { ?>
                    <?php
                    $pack_ids = $_GET['pack_id'];
                    $pack_ids = explode(',', $pack_ids);
                    foreach ($pack_ids as $pack_id) {
                        $pack_info = App\Models\Package::where('id', $pack_id)->first();
                        $pack_info_name[] = $pack_info->name;
                    }
                    ?>
                    <p><b>Package Name </b>: <span><?php echo implode(',', $pack_info_name); ?></span></p>
                    <p><b>Package Duration </b>: <span><?php echo $pack_info->duration; ?> days</span></p>
                    <p><b>Price </b> : <span><?php echo $currency_symbol; ?> <?php echo $_GET['amount']; ?></span></p>
                <?php } ?>
            </div>
            <input type="hidden" id="from_type_val" value="<?php echo !empty($_GET['from_type']) ? "add-post" : ""; ?>" />
        <?php } else if ($_GET['paid_for'] == "buynow") { ?>
            <div class="pakage_info">
                <?php
                $lid = $_GET['lid'];
                if ($_GET['status'] == "success") {
                    $get_checkout = App\Models\TblPostCheckout::onlyTrashed()->where('id', $lid)->whereNotNull('deleted_at')->first();
                } else {
                    $get_checkout = App\Models\TblPostCheckout::where('id', $lid)->first();
                }
                $currency_symbol = App\Models\TblCurrency::where('id', $_GET['cid'])->pluck('currency_hex')->first();
                ?>
                <p>Hi, <b><?php echo $user_info->name; ?></b></p>
                <p>Your Order info are given below,</p>
                <p><b>Price </b>: <span><?php echo  $currency_symbol . $get_checkout->price; ?></span></p>
                <p><b>Shipping Rate </b> : <span><?php echo $currency_symbol . $get_checkout->shipping_fee; ?></span></p>
                <p><b>Paid Amount </b> : <span><?php echo $currency_symbol . $_GET['amount']; ?></span></p>
            </div>
            <input type="hidden" id="from_type_val" value="buynow_<?php echo $_GET['status']; ?>" />
        <?php } else { ?>
            <div class="pakage_info">
                <?php
                $lid = $_GET['lid'];
                $banner_info = App\Models\TblBannerAdvertisement::where('id', $lid)->first();
                $currency_symbol = App\Models\TblCurrency::where('id', $_GET['cid'])->pluck('currency_hex')->first();
                ?>

                <p>Hi, <b><?php echo $user_info->name; ?></b></p>
                <p>Your Banner Ad Infos are given below,</p>
                <p><b>Live days </b>: <span><?php echo $banner_info->live_days; ?> days</span></p>
                <p><b>Requested for start date </b>: <span><?php echo date('d M Y', strtotime($banner_info->start_date)); ?></span></p>
                <p><b>Requested for end date </b> : <span><?php echo date('d M Y', strtotime($banner_info->end_date)); ?></span></p>
                <p><b>Paid Amount </b> : <span><?php echo $currency_symbol; ?> <?php echo $_GET['amount']; ?></span></p>
            </div>
            <input type="hidden" id="from_type_val" value="" />
        <?php } ?>
    </div>

</section>
<style>
    .container {
        width: 80%;
        margin: auto;
        text-align: center;
        margin-top: 150px;
    }

    h1.title {
        font-size: 80px;
    }

    p.content {
        font-size: 60px;
        line-height: 74px;
    }

    .pakage_info {
        text-align: left;
        padding-left: 51px;
        font-size: 40px;
        margin-top: 74px;
    }
</style>
<script>
    $(document).ready(function() {
        var from_type = $("#from_type_val").val();
        setTimeout(function() {
            if (from_type == "") {
                window.location.replace("<?php echo URL::to('/'); ?>");
            } else if (from_type == "buynow_success") {
                window.location.replace("<?php echo URL::to('contact-us'); ?>");
            } else if (from_type == "buynow_failed") {
                window.location.replace("<?php echo URL::to('/'); ?>");
            } else {
                window.location.replace("<?php echo URL::to('packages'); ?>");
            }
        }, 5000);
    });
</script><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/thankyou_webview/thankyou-component.blade.php ENDPATH**/ ?>