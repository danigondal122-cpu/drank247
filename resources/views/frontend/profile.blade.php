@extends('frontend.layout.app')
@section('pageCSS')
    <link rel="stylesheet"
        href="{{ asset('plugins/SVG-Based-Star-Rating-Plugin-For-jQuery-star-rating-svg-js/src/css/star-rating-svg.css') }}">
    <style>
        .card {
            border-radius: .25rem;
        }
    </style>
@endsection
@section('header_content')
    <!-- <div class="row justify-content-center">
                                                                                                                                                              <div class="col-md-10 ml-2">
                                                                                                                                                                <h1 class="underline" style="display: inline-block;">{{ __('messages.profile') }}</h1>
                                                                                                                                                                <ol class="breadcrumb float-sm-right">
                                                                                                                                                                  <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                                                                                                                                                                  <li class="breadcrumb-item active">{{ __('messages.userprofile') }}</li>
                                                                                                                                                                </ol>
                                                                                                                                                              </div>
                                                                                                                                                            </div> -->
@endsection
@section('content')
    <div class="row justify-content-center px-5 SmallDivPadding">

        <div class="col-lg-10 ml-2">
            <h1 class="underline" style="display: inline-block;">{{ __('messages.profile') }}</h1>
            <ol class="breadcrumb mb-1 float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ __('messages.userprofile') }}</li>
            </ol>
        </div>

        <div class="col-lg-3">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle" style="width:120px;height:120px;"
                            src="{{ $user_profile->profile }}" alt="User profile picture">
                    </div>
                    <h3 class="profile-username text-center">{{ $user_profile->customer_name }}</h3>
                    <p class="text-muted text-center"></p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>{{ __('messages.order') }}</b> <a class="float-right">{{ $ordercount }}</a>
                        </li>
                    </ul>

                    <a href="{{ url('/customer/logout') }}"
                        class="btn btn-primary btn-block"><b>{{ __('messages.logout') }}</b></a>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header p-0 profile-card-header horizontalTab productlistdiv">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link tabselection" href="#profile" id="profile_tab"
                                data-toggle="tab">{{ __('messages.profile') }}</a></li>
                        <li class="nav-item"><a class="nav-link tabselection" href="#changepassword" id="changepassword_tab"
                                data-toggle="tab">{{ __('messages.chnagepassoword') }}</a></li>
                        <li class="nav-item"><a class="nav-link tabselection" href="#addresslist" id="addresslist_tab"
                                data-toggle="tab">Address</a></li>
                        <li class="nav-item"><a class="nav-link tabselection" href="#orderhistory" id="orderhistory_tab"
                                data-toggle="tab">Order</a></li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body productlistdiv">
                    <div class="tab-content">
                        <div class="tab-pane " id="profile">
                            <form class="form-horizontal" id="profileForm">
                                @csrf
                                <div class="form-group row">
                                    <label for="inputName"
                                        class="col-sm-2 col-form-label">{{ __('messages.name') }}</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Name" value="{{ $user_profile->customer_name }}">
                                        <span id="name_error" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputEmail"
                                        class="col-sm-2 col-form-label">{{ __('messages.email') }}</label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" placeholder="Email"
                                            value="{{ $user_profile->customer_email }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputName2"
                                        class="col-sm-2 col-form-label">{{ __('messages.contactno') }}</label>
                                    <div class="col-sm-10 d-flex">
                                        <select class="form-control" id="country_code" name="country_code"
                                            style="width:25% ;">
                                            @foreach ($country_code as $key => $c)
                                                @if (isset($user_profile->country_code) && $user_profile->country_code == $c->phonecode)
                                                    {{ $class = 'selected' }}
                                                @elseif(!isset($user_profile->country_code) && $c->phonecode == 31)
                                                    {{ $class = 'selected' }}
                                                @else
                                                    {{ $class = '' }}
                                                @endif
                                                <option value="+{{ $c->phonecode }}" {{ $class }}>
                                                    {{ $c->iso }} +{{ $c->phonecode }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" class="form-control" id="contact_no" name="contact_no"
                                            placeholder="Contact No" value="{{ $user_profile->customer_contactno }}">
                                        <span id="contact_no_error" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputName2" class="col-sm-2 col-form-label">Type</label>
                                    <div class="col-sm-10">
                                        <div class="col-sm-4" style="display:inline-block;">
                                            <input type="radio" id="type0" name="type" value="0"
                                                {{ $user_profile->customer_type == '0' ? 'checked' : '' }}>
                                            Personal
                                        </div>
                                        <div class="col-sm-4" style="display:inline-block;">
                                            <input type="radio" id="type1" name="type" value="1"
                                                {{ $user_profile->customer_type == '1' ? 'checked' : '' }}>
                                            Business
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="exampleInputEmail1"
                                        class="col-sm-2 col-form-label">{{ __('messages.profile') }}</label>
                                    <div class="col-sm-10">

                                        @if ($user_profile->profile != '')
                                            <div id="postedImages">
                                                <div class="card elevation-1 mb-3 " style="width:120px;">
                                                    <div class="d-flex align-self-center align-items-center px-2"
                                                        style="height:120px;">
                                                        <img src="{{ $user_profile->profile }}"
                                                            style="max-height:120px;margin-left: auto;margin-right: auto;"
                                                            class="card-img-top cart-item-img"
                                                            alt="http://localhost/drank247_adminuploads/category/1619264594_1613236125_20210213_180821.jpg">
                                                    </div>
                                                    <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                        title="View full image"
                                                        style="position: absolute;left: 2%;bottom: 2%;"
                                                        class="btn btn-primary btn-sm previewImage">
                                                        <i class="fas fa-search-plus"></i>
                                                    </button>
                                                    <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                        title="Remove" style="position: absolute;right: 2%;bottom: 2%;"
                                                        class="btn btn-danger btn-sm deleteImage" data-id="">
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
                                            value="{{ empty($user_profile) == false ? $user_profile->image : '' }}">
                                        <div class="dropHere float-left">
                                            @if ($user_profile->profile == '')
                                                <button class="btn btn-outline-primary" type="button"
                                                    onclick="$('#image_file').click()" title="click here to add images">
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
                                @dd($order)
                                @if (count($order) != '0')
                                    @foreach ($order as $value)
                                        <div class="callout col-md-12" style="border-left-color:{{ $value->os_color }};">
                                            <div class="row">
                                                <div class="col-sm-6 col-xs-4 col-md-6 col-lg-8">
                                                    <h6>{{ __('messages.orderno') }}:{{ $value->order_id }} </h6>
                                                </div>
                                                <div class="col-sm-6 col-xs-4 col-md-6 col-lg-4 order_status"><span
                                                        class="badge"
                                                        style="background-color:{{ $value->os_color }};color:#fff;font-size:10px;">{{ $value->os_name }}</span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p>{{ __('messages.price') }}: &euro;
                                                        {{ $value->order_final_with_discount }}</p>
                                                </div>
                                            </div>
                                            @if ($value->payment_method)
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <p>Payment Method: {{ $value->payment_method }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-6 col-lg-8">

                                                    {{ __('messages.address') }} :
                                                    {{ $value->customerAddress->address ? $value->customerAddress->address : 'not found' }}
                                                    <div class="col-md-6 col-sm-6 col-xs-6 col-lg-4 order_status"><span
                                                            class="badge"
                                                            style="background-color:#adb5bd;color:#fff;font-size:10px;"> <a
                                                                onclick="getorderdetail({{ $value->order_id }})"
                                                                title="" itemprop="url"
                                                                style="cursor: pointer">{{ __('messages.orderdetail') }}</a></span>
                                                    </div>
                                                </div>
                                            </div>
                                    @endforeach
                                @else
                                    <div>Order Not Found!</div>
                                @endif


                            </div>


                        </div>

                        <div class="tab-pane" id="changepassword">
                            <form name="change_password_frm" id="change_password_frm">
                                @csrf
                                <div class="card-body p-0">
                                    <div class="message_div">
                                        {{ $user_profile['login_type'] == 'NORMAL' ? '' : 'You cannot change password because your account is associated with your social account.' }}
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
                                                name="confirm_password" placeholder="Confirm password">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-secondary"
                                        {{ $user_profile['login_type'] == 'NORMAL' ? '' : 'disabled' }}>{{ __('messages.update') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="addresslist">
                            <div class="col-12 col-sm-12 col-md-12 d-flex align-items-stretch">
                                <button class="btn btn-secondary mb-3 float-right" data-toggle="modal"
                                    data-target="#addUpdateAddress">{{ __('messages.addaddress') }}</button>
                            </div>
                            <div class="" id="addressList"></div>
                        </div>
                    </div>
                </div>
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
@section('pageJS')
    <script>
        $(document).ready(function() {
            // $('#horizontalTab').easyResponsiveTabs({
            //     type: 'default', //Types: default, vertical, accordion           
            //     width: 'auto', //auto or any width like 600px
            //     fit: true, // 100% fit in a container
            //     closed: 'accordion', // Start closed if in accordion view
            //     activate: function (event) { // Callback function if tab is switched
            //         var $tab = $(this);
            //         var $info = $('#tabInfo');
            //         var $name = $('span', $info);
            //         $name.text($tab.text());
            //         $info.show();
            //     }
            // });
        });
        $(document).ready(function() {
            // $('#tab_scroll').scrollTabs();
            $('.tabselection').on('click', function(event) {
                $('.tab-pane').removeClass('active');
                $($(this).attr('href')).addClass('active');
                // alert($(this).attr('href'));
                event.preventDefault();
                $('.tab-active').removeClass('tab-active');
                $(this).parent().addClass('tab-active');
                // $('.tabs-stage > div').hide();
                ;
            });
            //$('.sub_tabs a:first').trigger('click');

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
            })
        }
        (function($) {

            $(window).on("load", function() {

                var seltab = (sessionStorage.getItem('selectedtab') != "undefined" ? sessionStorage.getItem(
                    'selectedtab') : 'profile_tab')
                console.log(seltab);
                $('#' + seltab).trigger('click');
                if (seltab == "addresslist_tab") {
                    getAddressList();
                }

                $(".tabselection").click(function(e) {
                    e.preventDefault();
                    var tabno = $(this).attr('id');
                    if (tabno == "addresslist_tab") {
                        getAddressList();
                    }
                    sessionStorage.setItem('selectedtab', tabno);
                    $(this).tab('show');
                });
            });
        })(jQuery);
    </script>
    <script src="{{ asset('js/page/customer_profile.js') }}"></script>
    <script src="{{ asset('js/page/image_upload.js') }}"></script>
    <script
        src="{{ asset('plugins/SVG-Based-Star-Rating-Plugin-For-jQuery-star-rating-svg-js/src/jquery.star-rating-svg.js') }}">
    </script>
@endsection
