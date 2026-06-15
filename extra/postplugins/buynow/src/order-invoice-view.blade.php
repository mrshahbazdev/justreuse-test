<x-admin-layout>
    <style>
        .invoice-box {
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        tr.top.shipping-tracking h6 {
            margin: 0px;
            font-size: 21px;
            margin-top: 15px;
        }

        tr.top.shipping-tracking p {
            margin: 0px;
            font-size: 14px;
            line-height: 29px;
        }


        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .rtl {
            .invoice-box.rtl {
                direction: rtl;
                font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            }

            .rtl table {
                .invoice-box.rtl table {
                    text-align: right;
                }

                .rtl table tr td:nth-child(2) {
                    .invoice-box.rtl table tr td:nth-child(2) {
                        text-align: left;
                    }
    </style>
 <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded my-3"><a href="{{URL::to('back')}}">Back</a></button>

    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title" style="width:70%">
                                <img src="<?php echo URL::to('/storage/' . $logo['logo']); ?>" style="width: 100px;height: 66px;" />
                            </td>
                            <td style="text-align:right;">
                                Invoice #: <?php echo $orderDetail->orderId; ?><br />
                                Created: <?php echo date('d M Y', strtotime($orderDetail->created_at)); ?><br />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td style="width:50%;">
                                <b>Seller Info :</b> <br />
                                <?php echo $seller_info->name; ?>.<br />
                                <?php echo $seller_info->email; ?><br />
                            </td>
                            <td style="width:50%;text-align:right;">
                                <b>User Info :</b> <br />
                                <?php echo $user_info->name; ?>.<br />
                                <?php echo $user_info->email; ?><br />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>Payment Method</td>
                <td>Stripe</td>
            </tr>
            <tr class="details">
                <td>Transaction Id</td>
                <td><?php echo $orderDetail->payment_id; ?></td>
            </tr>
            <tr class="heading">
                <td>Item</td>
                <td>Price</td>
            </tr>
            <tr class="item">
                <td><?php echo $post_info->title; ?></td>
                <td><?php echo $currency_symbol->currency_hex; ?><?php echo $orderDetail->price; ?></td>
            </tr>
            <tr class="item">
                <td>Shipping Fee</td>
                <td><?php echo $currency_symbol->currency_hex; ?><?php echo $orderDetail->shipping_fee; ?></td>
            </tr>
            <tr class="total">
                <td></td>
                <td>Total: <?php echo $currency_symbol->currency_hex; ?><?php echo $orderDetail->total; ?></td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0">
            <tr class="top shipping-tracking">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title" style="width:50%">
                                <h6>Shipping Address : </h3>
                                    <p><b>Name :</b> <?php echo $orderDetail->shipping_add_name; ?></p>
                                    <p><b>Phone :</b> <?php echo $orderDetail->shipping_add_phone_number; ?></p>
                                    <p><b>Address :</b> <?php echo $orderDetail->shipping_add_address1; ?> <?php echo !empty($orderDetail->shipping_add_address2) ? "," . $orderDetail->shipping_add_address1 : ""; ?></p>
                                    <p><b>City :</b> <?php echo $orderDetail->shipping_add_city; ?></p>
                                    <p><b>State :</b> <?php echo $orderDetail->shipping_add_state; ?></p>
                                    <p><b>country :</b> <?php echo $orderDetail->shipping_add_country; ?></p>
                                    <p><b>Zipcode :</b> <?php echo $orderDetail->shipping_add_zipcode; ?></p>

                            </td>
                            <td class="title" style="text-align:right;">
                                <?php if (!empty($tracking_info)) { ?>
                                    <div class="tracking-address">
                                        <h6>Tracking details : </h6>
                                        <p><b>Shipment Date :</b> <?php echo date('d M Y', strtotime($tracking_info->shipping_date)); ?></p>
                                        <p><b>Shipment Method :</b> <?php echo $tracking_info->courier_name; ?></p>
                                        <p><b>Shipment Service :</b> <?php echo $tracking_info->courier_service; ?></p>
                                        <p><b>Tracking Id :</b> <?php echo $tracking_info->tracking_id; ?></p>
                                    </div>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </div>
    </x-admin-layout>