<!DOCTYPE html>
<html>

<head></head>

<body>
    {{-- {{dd($franchise)}} --}}
    <table style="font-size:20px;">
        <thead>
            <tr>
                <th colspan="11" style="text-align:center"><b>Order List {{ $franchise ? '-' . $franchise : '' }}</b>
                </th>
            </tr>
            <tr>
                <th colspan="11" style="text-align:center"></th>
            </tr>
            <tr style="font-size:12px;background-color:#f2f2f2;text-align:center;">
                <th><b>Order From</b></th>
                <th><b>Order No.</b></th>
                <th><b>Date</b></th>
                <th><b>Customer</b></th>
                <th><b>Franchisee</b></th>
                <th><b>Delivery Person</b></th>
                <th><b>Order Id</b></th>
                <th><b>Price</b></th>
                @if ($is_accountant == 1)
                    <th><b>Articles 0%</b></th>
                    <th><b>Articles 9%</b></th>
                    <th><b>Articles 21%</b></th>
                    <th><b>Delivery charge</b></th>
                @endif
                <th><b>Order Payment</b></th>
                <th><b>Payment Method</b></th>
                <th><b>Status</b></th>
            </tr>

            @foreach ($orders as $value)
                <tr style="text-align:center;">
                    <td>{{ $value['channel_name'] ? $value['channel_name'] : '247Drank' }}</td>
                    <td style="text-align:center;">{{ $value['order_id'] }}</td>
                    <td>{{ $value['new_order_date'] }}</td>
                    <td>{{ $value['customer_name'] }}</td>
                    <td>{{ $value['franchises_name'] }}</td>
                    <td>{{ $value['dp_name'] }}</td>
                    <td>
                        @if ($value['order_channel_order_id'] != '')
                            {{ $value['order_channel_order_id'] }}
                        @elseif($value['order_uber_display_id'])
                            {{ $value['order_uber_display_id'] }}
                        @else
                            {{ $value['order_takeaway_public_ref'] }}
                        @endif
                    </td>
                    <td>€{{ $value['order_final_with_discount'] }}</td>
                    @if ($is_accountant == 1)
                        <td>€{{ $value['product_price_0'] }}</td>
                        <td>€{{ $value['product_price_9'] }}</td>
                        <td>€{{ $value['product_price_21'] }}</td>
                        <td>€{{ $value['order_deliverycharge'] }}</td>
                    @endif
                    <td style="text-align:center;">{{ $value['order_payment_status_text'] }}</td>
                    <td style="text-align:center;">{{ $value['payment_method'] }}</td>
                    <td style="text-align:center;color:{{ $value['os_color'] }}">{{ $value['os_name'] }}</td>

                </tr>
            @endforeach

        </thead>
        <tbody>

        </tbody>
    </table>
</body>

</html>
