<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <!-- <img src="{{url('img/English_TM.svg')}}" width="50"> -->

</head>

<body>
    <style>
         /* @font-face {
        font-family: 'Lato-Italic';
        src: url('http://fonts.googleapis.com/css?family=Lato:400,700') format('truetype');
        font-weight: normal;
        font-style: normal;
        } */

        body {
            font-family: 'Lato', sans-serif;
        }

        header {
            position: fixed;
            top: 20px;
            /* left: 0px; */
            /* right: 0px; */
            height: 50px;
            float: right;

            /** Extra personal styles **/

            color: black;
            /* text-align: center; */
            line-height: 35px;
        }

        footer {
            position: fixed;
            bottom: 10px;
            /* height: 30px; */

            /** Extra personal styles **/
            color: black;
            line-height: 35px;
            font-size: 14px;
            text-align: center;
        }

        table tr td {
            padding-bottom: 5px;
        }

        .footer-section p {
            margin: 0 !important;
        }

        table tr td span{
            padding-left: 20px ;
        }
    </style>
    <!-- <header>
        <img src="{{url('img/247-Drank-Logo.png')}}" width="50%" height="50%"/>
    </header> -->
    <main>

        <table id="example2" width="50%" cellspacing="" style="width:100%;">
            <tr>

                <td style="font-size:30px;vertical-align:bottom;text-align:left;margin-top:10px;">
                    <h3>Factuur</h3>
                </td>
                <td align="right">
                    <img src="{{$image_url}}" width="400" />
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td>Franchisenummmer : {{ $franchise_address['franchises_no'] }}</td>
            </tr>
            <!-- <tr>
            <td>247 DRANK</td>
        </tr> -->
            <tr>
                <td>{{ $franchise_address['franchise_name'] }} t.h.o.d.n 247DRANK®</td>
            </tr>
            <tr>
                <td>{{ $franchise_address['residence'] }} {{ $franchise_address['house_no_street'] }} {{ $franchise_address['block_no'] }}</td>
            </tr>
            <tr>
                <td>{{ $franchise_address['post_code'] }} {{ $franchise_address['landmark'] }}</td>
            </tr>
            <tr>
                <td>{{ $franchise_address['country'] }}</td>
            </tr>
        </table>

        <table style="padding-top: 30px;">

            <tr>
                <td style="padding-right:100px;">Datum : {{ date('d-m-Y') }}</td>
                <td>Factuurnummer : {{ $factuur_no }}</td>
            </tr>
        </table>
        <table style="padding-top: 20px;">
            <tr>
                <td>De volgende diensten brengen wij u in rekening:</td>
            </tr>
        </table>
        <hr>
        <table id="example" cellspacing="4" style="width: 100%;">
            <tr>
                <td align="left">247DRANK® ({{ isset($start_from_date) ?  date('d-m-Y',strtotime($start_from_date)):'' }} t/m {{ isset($end_to_date) ? date('d-m-Y',strtotime($end_to_date)):'' }}): {{$total}} bestelling t.w.v.</td>
                <!-- <td align="right">€ {{str_replace('.',',', number_format($total_order_amount, 2))}}</td> -->
            </tr>
        </table>
        <table style="width: 100%;">
            <tr>
                <td align="left">Commissie: {{ str_replace('.',',', number_format($franchise_address['royalty'],2)) }} % van € {{str_replace('.',',', number_format($total_order_amount,2))}}</td>
                <td align="right">€  @php $amountTax =  ($total_order_amount * $franchise_address['royalty'])/100 @endphp
                  {{ str_replace('.',',', number_format($amountTax,2)) }}</td>
            </tr>
        </table>
        <table id="example" style="padding-top: 10px;width: 100%;" cellspacing="4">
            <tr>
                <td align="left">247DRANK® Regio {{ $franchise_address['franchises_no'] }} ({{ isset($start_from_date) ? date('d-m-Y',strtotime($start_from_date)):'' }} t/m {{ isset($end_to_date) ? date('d-m-Y',strtotime($end_to_date)):'' }}): 7 dagen t.w.v.</td>
                <!-- <td align="right"> € {{str_replace('.',',', number_format($franchise_address['per_day_charges'],2))}}</td> -->
            </tr>

        </table>

        <table style="width: 100%;">
            <tr>
                <td align="left" style="width:79%;">Daghuur: € {{ str_replace('.',',',number_format($franchise_address['per_day_charges'],2)) }} x 7</td>
                <td align="right" style="border-bottom: 1px solid #000;">€  @php $amountTax2 = number_format(7 * $franchise_address['per_day_charges'],2) @endphp
              {{ str_replace('.',',', number_format($amountTax2,2))  }}</td>
            </tr>
        </table>
        <table style="width: 100%;padding-top: 10px;">
            <tr>
                <td align="left">Subtotaal</td>
                <td align="right">€  @php $totalTaxamount = $amountTax + $amountTax2 @endphp
                {{ str_replace('.',',', number_format($totalTaxamount,2))  }}</td>
            </tr>
            <tr>
                <td align="left">BTW (21% over € {{ str_replace('.',',', number_format($totalTaxamount,2)) }})</td>
                <td align="right" style="border-bottom: 1px solid #000;">€ @php $TaxperTotalamount = number_format($totalTaxamount * 21 /100,2) @endphp
                {{ str_replace('.',',', number_format($TaxperTotalamount,2)) }}</td>
            </tr>
            <tr>
                <td align="left">Totaal bedrag van deze factuur</td>
                <td align="right">€  @php $finalamount = $totalTaxamount + $TaxperTotalamount @endphp
                {{ str_replace('.',',', number_format($finalamount,2)) }}</td>
            </tr>
            <tr>
                <td align="left">Verrekend met ontvangen online betalingen</td>
                <td align="right" style="border-bottom: 1px solid #000;">€ {{ str_replace('.',',', number_format($totalTaxamount + $TaxperTotalamount,2)) }}</td>
            </tr>
            <tr>
                <td align="left"><b>Openstaand factuurbedrag</b></td>
                <td align="right"><b>€ 0,00</b></td>
            </tr>
        </table>

        <hr>
        <table style="padding-top: 10px;margin-bottom:10px;width:100%;">
            <tr>
                <td align="left">Uw omzet over de periode {{ isset($start_from_date) ? date('d-m-Y',strtotime($start_from_date)):'' }} t/m {{ isset($end_to_date) ? date('d-m-Y',strtotime($end_to_date)):'' }}: € {{str_replace('.',',', number_format($total_order_amount,2))}}</td>

            </tr>

        </table>
        <hr>
        <p style="font-size:14px;">Bent u een foutieve vermelding tegengekomen of heeft u vragen email ons <a href="#">Invoice@247drank.nl.</a></p>
        <p style="font-size:14px;">247DRANK® B.V. is een geregistreerd handelsnaam waarvan de uitvoering is uitbesteed aan 247DRANK® International B.V.</p>
        <p style="font-size:14px;">247DRANK® International B.V. is geregistreerd bij de Nederlandse Kamer van Koophandel onder nummer 83270035. Kantoorhoudende aan de Keizersgracht 520 H, 1017 EK te Amsterdam.</p>
        <p style="font-size:14px;">247DRANK® International B.V. valt onder het Nederlandse belastingrecht met BTW-nummer NL862803494B01.</p>
        <p style="font-size:14px;">IBAN: NL78 RABO 0369 8068, BIC:RABONL2U t.n.v. 247DRANK® International BV.</p>
        <br>
        <table id="example2" width="50%" cellspacing="" style="width:100%;margin-top:10px;">
            <tr>

                <td style="font-size:30px;vertical-align:bottom;text-align:left;">
                    <h3>Specificaties</h3>
                </td>
                <td align="right">
                    <img src="{{$image_url}}" width="400" />
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td>Franchise : </td>
                <td>{{ $franchise_address['franchise_name'] }} t.h.o.d.n. 247DRANK®</td>
            </tr>
            <tr>
                <td>Periode : </td>
                <td>{{ isset($start_from_date) ? date('d-m-Y',strtotime($start_from_date)):'' }} t/m {{ isset($end_to_date) ? date('d-m-Y',strtotime($end_to_date)):'' }}</td>
            </tr>
        </table>

        <table style="padding-top:20px;width: 100%;">
            <tr>
                <td style="width: 30%;">Totaal : </td>
                <td>{{ $total }} bestellingen t.w.v. </td>
                <td align="right">€ {{str_replace('.',',', number_format($total_order_amount,2))}}</td>
            </tr>
            @if($ondelivery_total >0)
            <tr>
                <td >Betaald bij bezorging : </td>
                <td>{{ $ondelivery_total }} bestellingen  t.w.v. </td>
                <td align="right">€ {{str_replace('.',',', number_format($ondelivery_amount,2))}}</td>
            </tr>
            @endif
            <tr>
                <td>Online betaald *</td>
                <td>{{ $online_total_order }} bestellingen t.w.v.</td>
                <td align="right"> € {{str_replace('.',',', number_format($online_pay_total,2))}}</td>
            </tr>
        </table>
        <hr>

        <table style="width: 100%;">
            <tr>
                <td align="left">Uitstaand saldo online betalingen op {{ isset($end_to_date) ? date('d-m-Y',strtotime($end_to_date . "+1 days")):'' }} ****</td>
                <td align="right" style="border-bottom: 1px solid #000;">€ {{ str_replace('.',',', number_format($online_pay_total,2))}}</td>
            </tr>
            <tr>
                <td align="left">Verrekening van factuur {{ $factuur_no }}</td>
                <td align="right" style="border-bottom: 1px solid #000;">€ {{ str_replace('.',',', number_format($finalamount,2)) }}</td>
            </tr>
            <tr>
                <td align="left">Uitbetaling naar rekening {{$bank_account}} t.n.v. {{ $franchise_address['franchise_name'] }}</td>
                <td align="right">€ {{ str_replace('.',',',number_format($online_pay_total - $finalamount,2)) }}</td>
            </tr>
        </table>

        @if($total > 0 )
        <table style="margin-top:30px;">
            <tr>
                <td></td>
                <td>Datum</td>
                <td style="padding-left: 30px;">#</td>
                <td style="padding-left: 30px;">€</td>
                <td style="padding-left: 25px;">Payment Method</td>
                <td style="padding-left: 25px;">Delivery Person</td>
            </tr>
            @foreach($order as $row)
            <tr>
                <td><img width="30" height="30" src="{{ $row->channel_image ? url('images/channel/'.$row->channel_image) : url('images/channel/drank.png') }}"></img></td>
                <td>{{ date('d-m-Y H:i:s',strtotime($row->new_order_date)) }}</td>
                <td style="padding-left: 25px;">{{ $row->order_channel_order_id }}</td>
                <td style="padding-left: 25px;">{{ str_replace('.',',',number_format($row->order_final_with_discount,2)) }}</td>
                <td style="padding-left: 25px;">{{ $row->order_payment_status ? 'ONLINE ':'CASH/PIN' }}</td>
                <td style="padding-left: 25px;">{{ $row->dp_name }}</td>
            </tr>
            @endforeach
        </table>
        @endif

        <!-- <hr> -->

    </main><br><br><br><br>
    <div class="footer">
        <hr>
        ** Het uitstaande saldo bestaat uit alle ontvangen online betalingen, minus terugboekingen, verrekende facturen, en uitbetalingen

    </div>
</body>
</html>
<?php //exit();
?>
