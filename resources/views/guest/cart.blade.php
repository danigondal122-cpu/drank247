@extends('layouts.user')

@push('extraStyle')
    <style>
        .input-group {
            flex-wrap: inherit !important;
        }

        .wrapper::-webkit-scrollbar-track {
            background-color: #f4f6f9 !important;
        }

        @media screen and (min-device-width: 320px) and (max-device-width: 767px) {
            .productlistdiv {
                padding: 0 !important;
            }
        }
    </style>
@endpush

<x-plugins vendors="bootstrap-touchspin" :js="[asset('js/page/cart.js')]" />

@section('content')
    <x-user.content>
        <x-slot:breadcrumbs col="col-md-9" :title="__('messages.yourcart')">
            <li class="breadcrumb-item active">Cart</li>
        </x-slot>

        <div class="px-5 productlistdiv" id="cartItems">
            @if (cart()->count())
                <div class="row justify-content-center">
                    <div class="wrapper RemoveHeight col-md-6"
                        style="background-color: #f4f6f9 !important; padding-right: 0px; height:auto !important;">
                        <div class="col-lg-12" style="max-height: 600px;">
                            @foreach (cart()->get() as $item)
                                <div class="col-md-12 cartItem" for="{{ $item->id }}">
                                    <div class="card" style="border-radius: 0px !important;">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col-md-4 p-3 text-center">
                                                <img src="{{ $item->options->image }}" class="card-img cart-item-img"
                                                    alt="Image" onclick="showProductDetailpoup('{{ $item->id }}')">
                                            </div>
                                            <div class="col-md-8">
                                                <div class="card-body">
                                                    <h5 class="card-title text-ellipsis">{{ $item->name }}</h5>
                                                    <p class="card-text text-ellipsis">{{ __('messages.category') }} :
                                                        {{ $item->options->category_name ?? 'Extra Category' }}
                                                    </p>
                                                    <div class="row">
                                                        <div class="col-12 col-md-12 col-xl d-flex align-items-center"
                                                            style="padding-right: 0px; margin-bottom: 15px; justify-content: center;">
                                                            <i
                                                                class="fas fa-euro-sign"></i>&nbsp;{{ $item->format('vat_price', 2) }}
                                                        </div>
                                                        <div class="d-flex col-12 col-md-12 col-xl-6"
                                                            style="margin-bottom: 15px">
                                                            <input id="product_qty" type="number" name="qty"
                                                                class="form-control text-center itemTotal"
                                                                value="{{ $item->qty }}" data-id="{{ $item->id }}"
                                                                data-price="{{ $item->options->original_price }}"
                                                                data-vatprice="{{ $item->vat_price }}">
                                                        </div>
                                                        <div class="col-12 col-md-12 col-xl d-flex align-items-center"
                                                            style="padding-left: 0px; margin-bottom: 15px; justify-content: center;">
                                                            <i class="fas fa-euro-sign"></i>&nbsp;<span
                                                                id="{{ (auth('customer')->check() ? 'vattaxamount' : 'cartItemTotal') . $item->id }}">{{ $item->format('total:vat_price', 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="remove-from-cart-btn cursor-pointer" title="Remove from Cart"
                                            data-row-id="{{ $item->id }}">
                                            <img class="img-as-icon" src="{{ asset('images/icon/close.png') }}"
                                                alt="" style="width: 22px !important; margin: 10px;">
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-4 TopMargin" style="padding-left: 15px;">
                        <div class="invoice col-lg-12" style="min-height: 600px;">
                            <div class="row"
                                style="padding: 15px 10px; border-bottom: 1px solid rgba(0,0,0,.125); font-size: 21px; text-align: center;">
                                <div class="col-lg-12"><b>{{ __('messages.ordersummary') }}</b></div>
                            </div>
                            <div class="row" style="padding: 7px 10px; margin-top: 10px;">
                                <div class="col-lg-8"><b>{{ __('messages.total') }}</b></div>
                                <div class="col-lg-4"><i class="fa fa-euro-sign"></i> <span
                                        id="cartTotal">{{ cart()->format('subtotal', 2) }}</span></div>
                            </div>
                            <div class="row" style="padding: 7px 10px;">
                                <div class="col-lg-8"><b>{{ __('messages.deliverycharge') }} (tot € 75,00)</b></div>
                                <div class="col-lg-4"><i class="fa fa-euro-sign"></i> <span
                                        id="DeliveryCharge">{{ cart()->payment('delivery_charge') }}</span></div>
                            </div>
                            <div class="row" style="padding: 7px 10px;" id="FinalAmountDiv">
                                <div class="col-lg-8"><b>{{ __('messages.finalamount') }}</b></div>
                                <div class="col-lg-4"><i class="fa fa-euro-sign"></i> <span
                                        id="FinalAmount">{{ cart()->payment('total') }}</span></div>
                            </div>

                            <div id="DiscountMainDiv" style="display: none;">
                                <div class="row" style="padding: 7px 10px;">
                                    <div class="col-lg-8"><b>Discount</b></div>
                                    <div class="col-lg-4"><i class="fa fa-euro-sign"></i> <span id="Discount"></span></div>
                                    <div class="col-lg-4" style="display: none;"><i class="fa fa-euro-sign"></i> <span
                                            id="Discount_type"></span></div>
                                    <div class="col-lg-4" style="display: none;"><i class="fa fa-euro-sign"></i> <span
                                            id="Discount_inper"></span></div>
                                    <div class="col-lg-4" style="display: none;"><i class="fa fa-euro-sign"></i> <span
                                            id="promo_code"></span></div>
                                </div>
                                <div class="row" style="padding: 7px 10px;">
                                    <div class="col-lg-8"><b>{{ __('messages.finalamount') }}</b></div>
                                    <div class="col-lg-4"><i class="fa fa-euro-sign"></i> <span
                                            id="withDiscount_FinalAmount"></span></div>
                                </div>
                            </div>

                            <div class="row"
                                style="padding: 30px 10px; border-top: 1px solid rgba(0,0,0,.125); background-color: #ececec;">
                                <b>{{ __('messages.haveacode') }}</b>
                                <div class="search_inputdiv" style="width: 100% !important; margin-top: 10px;">
                                    <form class="form-horizontal" method="post" id="promocodeform">
                                        @csrf
                                        <input type="text" class="form-control promocode" id="promocode"
                                            name="promocode" placeholder="{{ __('messages.enterpromocode') }}">
                                        <button type="submit" id="promocodeSubmit"
                                            class="btn btn-primary btn-sm gobutton">Go</button>
                                    </form>
                                </div>
                            </div>

                            @auth('customer')
                                <div class="row"
                                    style="padding: 15px 10px; border-bottom: 1px solid rgba(0,0,0,.125); border-top: 1px solid rgba(0,0,0,.125);">
                                    <div class="col-md-6 col-xs-12 col-sm-12 col-lg-6">
                                        <p class="lead">{{ __('messages.deliveryaddress') }}</p>
                                        <p id="defaultAddressId" style="display: none;">
                                            {{ $address?->id ?? '' }}
                                        </p>

                                        <p class="text-muted well well-sm shadow-none" id="defaultAddress"
                                            style="margin-top: 10px;">
                                            {{ $address?->address ?? '' }}
                                        </p>
                                        <p id="postcode">
                                            {{ $address?->post_code ?? '' }}
                                        </p>
                                    </div>
                                    <div class="col-md-6 col-xs-12 col-sm-12 col-lg-6 text-center">
                                        @if ($address)
                                            <button type="button"
                                                class="btn btn-default btn-sm btn-outline-primary float-right"
                                                onclick="getAddressList()">
                                                {{ __('messages.change') }}
                                            </button>
                                        @else
                                            <div class="col-12 col-sm-12 col-md-12 d-flex align-items-stretch">
                                                <button class="btn btn-secondary mb-3 float-right" data-toggle="modal"
                                                    data-target="#addUpdateAddress">{{ __('messages.addaddress') }}</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="row"
                                    style="padding: 15px 10px; border-bottom: 1px solid rgba(0,0,0,.125); border-top: 1px solid rgba(0,0,0,.125);">
                                    <div class="col-lg-8"><b>{{ __('messages.deliveryaddress') }}</div>
                                    <div class="col-lg-4">-</div>
                                </div>

                                <div class="form-group row guest_checkout_section d-none">
                                    <label for="inputName2" class="col-sm-4 mt-2 col-form-label">Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control customer_name mt-2" id="customer_name"
                                            name="customer_name" placeholder="Name" autocomplete="off">
                                        <span id="customer_name_error"></span>
                                    </div>

                                    <label for="inputName2" class="col-sm-4 mt-2 col-form-label">Email</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control customer_email mt-2" name="customer_email"
                                            id="customer_email" placeholder="Email" autocomplete="off">
                                        <span id="customer_email_error"></span>
                                    </div>

                                    <label for="inputName2" class="col-sm-4 mt-2 col-form-label">House No</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control house_no mt-2" id="house_no"
                                            name="house_no" placeholder="House No" autocomplete="off">
                                        <span id="house_no_error"></span>
                                    </div>

                                    <label for="inputName2" class="col-sm-4 mt-2 col-form-label">PostCode</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control post_code mt-2" name="post_code"
                                            id="post_code" placeholder="PostCode" autocomplete="off">
                                        <span id="post_code_error"></span>
                                    </div>

                                    <label for="inputName2"
                                        class="col-sm-4 mt-2 col-form-label">{{ __('messages.contactno') }}</label>
                                    <div class="col-sm-8 d-flex mt-2">
                                        <select class="form-control country_code" id="country_code" name="country_code"
                                            style="width:60% ;">
                                            @foreach ($countries as $country)
                                                <option value="+{{ $country->phonecode }}" @selected(!$country_code && $country->phonecode == 31)>
                                                    {{ $country->iso }} +{{ $country->phonecode }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" class="form-control contact_no" id="contact_no"
                                            name="contact_no" placeholder="Contact No" autocomplete="off">
                                        <span id="contact_no_error"></span>
                                    </div>

                                    <label for="inputName2"
                                        class="col-sm-4 mt-2 col-form-label">{{ __('messages.Add_note_for_delivery') }}</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control mt-2 note" id="note" name="note"
                                            placeholder="{{ __('messages.Add_note_for_delivery') }}"></textarea>
                                    </div>
                                </div>

                                <div class="row align-self-end" style="padding:30px 10px;">
                                    <button type="button" class="btn btn-primary btn-block float-right guest_checkout">
                                        Guest Checkout
                                    </button>
                                    <button type="button" class="btn btn-primary btn-block float-right user_checkout_button"
                                        data-toggle="modal" data-target="#loginModule">
                                        <i class="fas fa-shopping-cart"></i> {{ __('messages.checkout') }}
                                    </button>
                                    <button type="button"
                                        class="btn btn-primary btn-block float-right guest_checkout_button d-none"
                                        onclick="guestcheckout()">
                                        <i class="fas fa-shopping-cart"></i> {{ __('messages.checkout') }}
                                    </button>

                                    <p class="mt-4">{{ __('messages.orderpaystatement') }}</p>
                                </div>
                            @endauth
                        </div>
                        @auth('customer')
                            <div class="form-group row">
                                <label for="inputName2"
                                    class="col-sm-4 mt-2 col-form-label">{{ __('messages.contactno') }}</label>
                                <div class="col-sm-8 d-flex mt-2">
                                    <select class="form-control" id="country_code" name="country_code" style="width:60% ;">
                                        @foreach ($countries as $country)
                                            <option value="+{{ $country->phonecode }}" @selected((!$country_code && $country->phonecode == 31) || $country_code == $country->phonecode)>
                                                {{ $country->iso }} +{{ $country->phonecode }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control " id="contact_no" name="contact_no"
                                        placeholder="Contact No" autocomplete="off" value="{{ $contact_no }}">
                                </div>
                                <label for="inputName2"
                                    class="col-sm-4 mt-2 col-form-label">{{ __('messages.Add_note_for_delivery') }}</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control mt-2" id="note" name="note"
                                        placeholder="{{ __('messages.Add_note_for_delivery') }}"></textarea>
                                </div>
                            </div>
                            <div class="row align-self-end" style="padding: 30px 10px;">
                                <button type="button" class="btn btn-primary btn-block float-right" onclick="CMplaceorder()"
                                    data-toggle="modal">
                                    <i class="fas fa-shopping-cart"></i> {{ __('messages.checkout') }}
                                </button>
                                <p class="mt-4">{{ __('messages.orderpaystatement') }}</p>
                            </div>
                        @endauth
                    </div>
                </div>
            @else
                <div class="col-lg-12 d-flex justify-content-center">
                    <div class="text-center">
                        <h3 style="padding: 30px;">{{ __('messages.yourcartisempty') }}</h3>
                        <a href="{{ route('homepage') }}"
                            class="btn btn-primary">{{ __('messages.continuetoaddproduct') }}</a>
                    </div>
                </div>
            @endif
        </div>
    </x-user.content>

    <x-common-modal view="user" dialogClass="w-100" />

    <div class="modal fade" id="addressList" tabindex="-1" aria-labelledby="addressList" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <div>
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('messages.changeaddress') }}</h5>
                    </div>
                    <div>
                        <button class="btn btn-secondary" data-toggle="modal" data-target="#addUpdateAddress"
                            style="">{{ __('messages.addaddress') }}</button>
                    </div>
                    <div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body" id="body_content" style="overflow-y: scroll; overflow-x: auto;">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addUpdateAddress" tabindex="-1" aria-labelledby="addUpdateAddress" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="addUpdateAddressForm">
                    @csrf
                    <div class="modal-header py-2">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('messages.addaddress') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="houseno" class="col-form-label col-md-3">{{ __('messages.houseno') }}</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="houseno" name="houseno"
                                    placeholder="House No" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="postcode" class="col-form-label col-md-3">Postcode:</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="postcode" name="postcode"
                                    placeholder="Enter PostCode" value="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="address_id" id="address_id" value="">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-primary"
                            id="addressBtn">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addManualAddress" tabindex="-1" aria-labelledby="addManualAddress" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="addManualAddressForm">
                    @csrf
                    <div class="modal-header py-2">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('messages.addaddress') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="houseno" class="col-form-label col-md-3">{{ __('messages.houseno') }}</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="houseno" name="houseno"
                                    placeholder="House No" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="street" class="col-form-label col-md-3">Street</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="street" name="street"
                                    placeholder="Enter Street" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="city" class="col-form-label col-md-3">City</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="city" name="city"
                                    placeholder="Enter City" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="city" class="col-form-label col-md-3">State</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="state" name="state"
                                    placeholder="Enter State" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="postcode" class="col-form-label col-md-3">PostCode</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="postcode" name="postcode"
                                    placeholder="Enter PostCode" value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="address_id" id="address_id" value="">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-primary"
                            id="addressBtn">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('exstraScript')
    <script>
        function showProductDetailpoup(id) {
            $.ajax({
                url: SITE_URL + 'products/getProductDetail',
                type: 'POST',
                data: 'id=' + id + '&_token=' + $('meta[name=csrf-token]').attr('content'),
                success: function(obj) {
                    $('#commonModalHtml').html(obj);
                    $('#commonModal').modal('show');
                }
            });
        }

        $(document).on('submit', '#promocodeform', function(e) {
            e.preventDefault();
            var data = new FormData(this);
            $('.is-invalid').removeClass('is-invalid');
            $('.text-danger').html('');
            var final_amount = $('#FinalAmount').html();
            var finalamount = final_amount.replace(',', '');
            data.append('finalamount', finalamount);

            $.ajax({
                url: SITE_URL + 'customer/checkPromoCode',
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(obj) {
                    if (!obj.status && obj.type == 'validation') {
                        loader_hide();
                        for (key in obj.errors) {
                            $('#' + key).addClass('is-invalid');
                            $('#promocode').focus();
                        }
                    } else if (!obj.status && obj.type == 'invalidPromoCode') {
                        $.alert(obj.message);
                        $('#Discount').html('0.00');
                        $('#Discount_type').html('');
                        $('#Discount_inper').html('');
                        $('#withDiscount_FinalAmount').html(final_amount);
                        $('#FinalAmount').html(final_amount);
                    }

                    if (obj.status) {
                        $.alert(obj.message);
                        $("#DiscountMainDiv").show();
                        var FinalAmount = $("#FinalAmount").html().replace(',', '');
                        Discount = obj.discount;

                        if (obj.discount_type == 0) {
                            discountamounts = Discount;
                            pay_amount = FinalAmount - Discount;
                        } else {
                            discountamounts = FinalAmount * Discount / 100;
                            pay_amount = FinalAmount - discountamounts;
                        }

                        $('#FinalAmountDiv').hide();
                        $('#withDiscount_FinalAmount').html(accounting.formatMoney(pay_amount, "", 2,
                            ",", "."));
                        $('#Discount').html(accounting.formatMoney(discountamounts, "", 2, ",", "."));
                        $('#Discount_type').html(obj.discount_type);
                        $('#Discount_inper').html(Discount);
                        $('#promo_code').html(obj.promo_code);
                    }
                }
            });
        })
    </script>
@endpush
