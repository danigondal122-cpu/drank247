@extends('layouts.user')

@push('extraStyle')
    <style>
        .card {
            border-radius: .25rem;
        }
    </style>
@endpush

<x-plugins vendors="star-rating" :js="[asset('js/page/customer_profile.js'), asset('js/page/image_upload.js')]" />

@section('content')
    <x-user.content>
        <x-slot:breadcrumbs col="col-md-10" :title="__('messages.profile')">
            <li class="breadcrumb-item active">{{ __('messages.userprofile') }}</li>
        </x-slot>

        <div class="row justify-content-center">
            <div class="col-lg-3">
                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle" style="width: 120px; height: 120px;"
                                src="{{ $customer->profile->url() }}" alt="User profile picture">
                        </div>
                        <h3 class="profile-username text-center">{{ $customer->customer_name }}</h3>
                        <p class="text-muted text-center"></p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>{{ __('messages.order') }}</b> <a class="float-right">{{ $order->count() }}</a>
                            </li>
                        </ul>

                        <a href="{{ route('customer.logout') }}" class="btn btn-primary btn-block">
                            <b>{{ __('messages.logout') }}</b>
                        </a>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header p-0 profile-card-header horizontalTab productlistdiv">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link tabselection" href="#profile" id="profile_tab"
                                    data-toggle="tab">{{ __('messages.profile') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link tabselection" href="#changepassword" id="changepassword_tab"
                                    data-toggle="tab">{{ __('messages.chnagepassoword') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link tabselection" href="#addresslist" id="addresslist_tab"
                                    data-toggle="tab">Address</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link tabselection" href="#orderhistory" id="orderhistory_tab"
                                    data-toggle="tab">Order</a>
                            </li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body productlistdiv">
                        <div class="tab-content">
                            <div class="tab-pane" id="profile">
                                <form class="form-horizontal" id="profileForm">
                                    @csrf
                                    <div class="form-group row">
                                        <label for="inputName"
                                            class="col-sm-2 col-form-label">{{ __('messages.name') }}</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="name" name="name"
                                                placeholder="Name" value="{{ $customer->customer_name }}">
                                            <span id="name_error" class="d-block text-danger mt-1"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputEmail"
                                            class="col-sm-2 col-form-label">{{ __('messages.email') }}</label>
                                        <div class="col-sm-10">
                                            <input type="email" class="form-control" placeholder="Email"
                                                value="{{ $customer->customer_email }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputName2"
                                            class="col-sm-2 col-form-label">{{ __('messages.contactno') }}</label>
                                        <div class="col-sm-10">
                                            <div class="d-flex">
                                                <select class="form-control" id="country_code" name="country_code"
                                                    style="width: 25%;">
                                                    @foreach ($countries as $country)
                                                        <option value="+{{ $country->phonecode }}"
                                                            @selected($customer->country_code == $country->phonecode || (!$customer->country_code && $country->phonecode == 31))>{{ $country->iso }}
                                                            +{{ $country->phonecode }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text" class="form-control" id="contact_no" name="contact_no"
                                                    placeholder="Contact No" value="{{ $customer->contact }}">
                                            </div>
                                            <span id="contact_no_error" class="d-block text-danger mt-1"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputName2" class="col-sm-2 col-form-label">Type</label>
                                        <div class="col-sm-10">
                                            <div class="d-inline-block">
                                                <input type="radio" id="type0" name="type" value="0"
                                                    @checked($customer->customer_type == '0')>
                                                <label for="type0" class="ml-1">Personal</label>
                                            </div>
                                            <div class="d-inline-block ml-4">
                                                <input type="radio" id="type1" name="type" value="1"
                                                    @checked($customer->customer_type == '1')>
                                                <label for="type1" class="ml-1">Business</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="exampleInputEmail1"
                                            class="col-sm-2 col-form-label">{{ __('messages.profile') }}</label>
                                        <div class="col-sm-10">
                                            @if ($customer->profile->fileName)
                                                <div id="postedImages">
                                                    <div class="card elevation-1 mb-3 " style="width: 120px;">
                                                        <div class="d-flex align-self-center align-items-center px-2"
                                                            style="height: 120px;">
                                                            <img src="{{ $customer->profile->url() }}"
                                                                class="card-img-top cart-item-img"
                                                                style="max-height: 120px; margin-left: auto; margin-right: auto;"
                                                                alt="http://localhost/drank247_adminuploads/category/1619264594_1613236125_20210213_180821.jpg">
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm previewImage"
                                                            data-toggle="tooltip" data-placement="bottom"
                                                            title="View full image"
                                                            style="position: absolute; left: 2%; bottom: 2%;">
                                                            <i class="fas fa-search-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm deleteImage"
                                                            data-toggle="tooltip" data-placement="bottom" title="Remove"
                                                            style="position: absolute; right: 2%; bottom: 2%;"
                                                            data-id="">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <div id="postedImages"></div>
                                            @endif

                                            <input type="file" name="image_file" id="image_file" class="d-none"
                                                accept="image/*">
                                            <input type="hidden" name="old_cat_pic" id="old_cat_pic"
                                                value="{{ $customer->profile->fileName }}">
                                            <div class="dropHere float-left">
                                                @if (!$customer->profile->fileName)
                                                    <button class="btn btn-outline-primary" type="button"
                                                        onclick="$('#image_file').click()"
                                                        title="click here to add images">
                                                        <i class="fas fa-plus fa-5x"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="offset-sm-2 col-sm-10">
                                            <button type="submit"
                                                class="btn btn-secondary">{{ __('messages.update') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="orderhistory">
                                <div class="card-body wrapper productlistdiv" style="background-color: #f4f6f9">
                                    @forelse ($order as $item)
                                        <div class="callout col-md-12" style="border-left-color: {{ $item->orderStatus?->os_color ?? '#e9ecef' }};">
                                            <div class="row">

                                                <div class="col-sm-6 col-xs-4 col-md-6 col-lg-8">
                                                    <h6>{{ __('messages.orderno') }}: {{ $item->id }}</h6>
                                                </div>
                                                <div class="col-sm-6 col-xs-4 col-md-6 col-lg-4 order_status">
                                                    @if ($item->orderStatus)
                                                        <span class="badge p-2"
                                                            style="background-color: {{ $item->orderStatus->os_color }}; color: #fff; font-size: 10px;">
                                                            {{ $item->orderStatus->os_name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p>{{ __('messages.price') }}: &euro;
                                                        {{ $item->order_final_with_discount }}</p>
                                                </div>
                                            </div>
                                            @if ($item->payment_method)
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <p>Payment Method: {{ $item->payment_method }}</p>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="row">
                                                <div class="col-xs-6 col-lg-8">
                                                    {{ __('messages.address') }}: {{ $item->address->address }}
                                                </div>
                                                <div class="col-xs-6 col-lg-4 order_status">
                                                    <button
                                                        class="btn btn-small btn-primary py-0 px-2"
                                                        onclick="getorderdetail({{ $item->id }})"
                                                        style="cursor: pointer; font-size: 14px; font-weight: bold;"
                                                    >
                                                        {{ __('messages.orderdetail') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div>Order Not Found!</div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="tab-pane" id="changepassword">
                                <form name="change_password_frm" id="change_password_frm">
                                    @csrf
                                    <div class="card-body p-0">
                                        <div class="message_div">
                                            @if ($customer->login_type != 'NORMAL')
                                                You cannot change password because your account is associated with your
                                                social account.
                                            @endif
                                        </div>
                                        <div class="form-group row">
                                            <label for="current_password"
                                                class="col-sm-3 col-form-label">{{ __('messages.currentpassword') }}</label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control" id="current_password"
                                                    name="current_password" placeholder="Current password">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="new_password"
                                                class="col-sm-3 col-form-label">{{ __('messages.newpassword') }}</label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control" id="new_password"
                                                    name="new_password" placeholder="New password">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="confirm_password"
                                                class="col-sm-3 col-form-label">{{ __('messages.confirmpassword') }}</label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control" id="confirm_password"
                                                    name="new_password_confirmation" placeholder="Confirm password">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-secondary" @disabled($customer->login_type != 'NORMAL')>
                                            {{ __('messages.update') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="addresslist">
                                <div class="col-12 col-sm-12 col-md-12 d-flex align-items-stretch">
                                    <button class="btn btn-secondary mb-3 float-right" data-toggle="modal"
                                        data-target="#addUpdateAddress">
                                        {{ __('messages.addaddress') }}
                                    </button>
                                </div>
                                <div id="addressList"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-user.content>

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
                            <label for="postcode" class="col-form-label col-md-3">PostCode</label>
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
        $(document).ready(function() {
            $('.tabselection').on('click', function(e) {
                e.preventDefault();
                $('.tab-pane').removeClass('active');
                $($(this).attr('href')).addClass('active');
                $('.tab-active').removeClass('tab-active');
                $(this).parent().addClass('tab-active');
            });
        });

        function getorderdetail(id) {
            $.ajax({
                url: SITE_URL + 'order/getorderdetail',
                type: 'POST',
                data: {
                    id: id,
                    _token: $('meta[name=csrf-token]').attr('content'),
                },
                success: function(obj) {
                    $('#orderhistory').html(obj);
                }
            });
        }

        (function($) {
            $(window).on('load', function() {
                var seltab = sessionStorage.getItem('selectedtab') != 'undefined' ?
                    sessionStorage.getItem('selectedtab') :
                    'profile_tab';
                console.log(seltab);
                $('#' + seltab).trigger('click');

                if (seltab == 'addresslist_tab') getAddressList();

                $('.tabselection').click(function(e) {
                    e.preventDefault();
                    var tabno = $(this).attr('id');

                    if (tabno == 'addresslist_tab') getAddressList();

                    sessionStorage.setItem('selectedtab', tabno);

                    $(this).tab('show');
                });
            });
        })(jQuery);
    </script>
@endpush
