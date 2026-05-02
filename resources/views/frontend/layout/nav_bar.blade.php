<style>
    .dropdown-item a {
        color: #000;
    }

    .my_categories .show .custom_nav {
        position: absolute;
        /* margin: 50px; */
        width: 250px;
        display: block !important;
    }

    @media screen and (max-width:991px) {
        .my_categories .show .custom_nav {
            position: fixed;
            top: 0;
            left: 0;
            opacity: 1;
            width: 100%;
            transition: 0.5s;
            display: block !important;
            border-radius: 0 16px 16px 0;
        }
    }

    @media screen and (max-width: 400px) {
        .my_categories .show .custom_nav {
            width: 145%;
        }
    }

    .custom_nav ul {
        list-style: none;
        margin: 0;
        background: white;
        height: 100%;
        padding: 0;
    }

    .custom_nav ul li {
        /* Sub Menu */
    }

    .custom_nav ul li a {
        display: block;
        background: white;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        -webkit-transition: 0.2s linear;
        -moz-transition: 0.2s linear;
        -ms-transition: 0.2s linear;
        -o-transition: 0.2s linear;
        transition: 0.2s linear;
    }

    .custom_nav ul li :hover {
        background: #f8f8f8;
        color: #515151;
    }

    .custom_nav ul li a .fa {
        width: 16px;
        text-align: center;
        margin-right: 5px;
        float: right;
    }

    .custom_nav ul ul {
        background-color: #ebebeb;
    }

    .custom_nav ul li ul li a {
        background: #f8f8f8;
        border-left: 4px solid transparent;
        padding: 10px 20px;
    }

    .custom_nav ul li ul li a:hover {
        background: #ebebeb;
        border-left: 4px solid #3498db;
    }

    .has-search .form-control {
        padding-left: 2.375rem;
    }

    .has-search .form-control-feedback {
        width: 2.375rem;
        height: 2.375rem;
        color: #aaa;
        display: flex;
        align-items: center;
        justify-content: center;
        /* position: absolute;
    z-index: 2;
    display: block; */
        /* line-height: 2.375rem;
    text-align: center;
    pointer-events: none; */
    }

    .header_search {
        margin-right: 5px;
        background: white;
        border-radius: 0.25rem;
        margin-bottom: 0;
    }

    .header_search_input {
        height: 100% !important;
        border: 0;
        padding-left: 5px;
    }

    .header_search_input:focus {
        border: 0;
    }

    /* .dropdown, .dropleft, .dropright, .dropup {
    position: absolute;
} */
    .ui-autocomplete .ui-menu-item {
        /* font-style:italic; */
        color: #333;
        background: #ebebeb;
    }

    .ui-menu {
        margin-top: 50px;
        z-index: 9999999;
    }

    @media screen and (min-width: 100px) and (max-width: 500px) {
        .main_header_search {
            width: 67%;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
    }

    @media screen and (min-width:1001px) and (max-width:1600px) {
        .saperate_headers .main_header_div {
            padding: 0 20px !important;
        }

        .nav_padding_0 {
            padding-left: 0;
        }

        .saperate_headers .nav_padding_0 li a {
            padding-right: 0;
            font-size: 15px;
        }

        .saperate_headers .navbar-brand img {
            width: 100px;
        }

        .saperate_headers .header_search {
            width: 50% !important;
        }

        .saperate_headers .main_header_search {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .header_profile-user-img {
            width: 30px;
        }
    }

    @media screen and (min-width:1001px) and (max-width:1350px) {
        .saperate_headers .nav_padding_0 li a {
            font-size: 13px;
            padding-right: 0 !important;
        }
    }
</style>

<nav class="main-header navbar navbar-expand-lg navbar-light  elevation-1 {{ Request::segment(1) != '' ? 'saperate_headers' : '' }}"
    style="height:60px;">
    <div class="container-fluid main_header_div px-5">
        @if (Request::segment(1) != '')
            <a href="{{ url('/') }}" class="navbar-brand">
                <img src="{{ asset('img/white_logo.svg') }}" alt="247Drank" class="brand-image">
            </a>
        @endif

        <div class="main_nav_div">

            <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                <!-- Left navbar links -->
                <ul class="navbar-nav px-10 text-white nav_padding_0">
                    <li class="nav-item">
                        <a href="{{ url('/') }}"
                            class="{{ Request::segment(1) == '' ? 'nav-link extra active' : 'nav-link extra' }}">Home</a>
                    </li>

                    {{-- <li class="custom_nav nav-item dropdown show">
          <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" class="{{ Request::segment(1)=='products' ? 'nav-link dropdown-toggle extra active' : 'nav-link dropdown-toggle extra' }}">{{ __('messages.categories') }}</a>
          <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu  border-0 shadow" style="left: 0px; right: inherit;height:100vh;;
          overflow-y:auto;">
        {!! $global['html'] !!}
          </ul>
        </li> --}}
                    <div class="my_categories">
                        <li class="nav-item custom_navigation">
                            <a href="#" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                aria-expanded="false"
                                class="{{ Request::segment(1) == 'products' ? 'nav-link extra active' : 'nav-link extra' }}">{{ __('messages.categories') }}</a>
                            <ul aria-labelledby="dropdownMenuButton">
                                <nav class='custom_nav animated bounceInDown wrapper categories_popup'>
                                    <ul>
                                        {!! $global['html'] !!}
                                    </ul>
                                </nav>
                            </ul>
                        </li>
                    </div>

                    {{-- <li class="nav-item dropdown show">
          <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" class="{{ Request::segment(1)=='products' ? 'nav-link dropdown-toggle extra active' : 'nav-link dropdown-toggle extra' }}">{{ __('messages.categories') }}</a>
          <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu  border-0 shadow" style="left: 0px; right: inherit;height:200px;
          overflow-y:auto;">
            {!! $global['html'] !!}
          </ul>
        </li> --}}

                    <li class="nav-item">
                        <a href="{{ url('/contact_us') }}"
                            class="{{ Request::segment(1) == 'contact_us' ? 'nav-link extra active' : 'nav-link extra' }}">{{ __('messages.contactus') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/cart') }}"
                            class="{{ Request::segment(1) == 'cart' ? 'nav-link extra active' : 'nav-link extra' }}">
                            <i class="fas fa-cart-arrow-down"></i> {{ __('messages.cart') }} (<span
                                id="cart_total_item">@auth{{ $global['cart_item_total'] }}@else{{ Cart::content()->count() }}@endauth
                            </span>
                            )</a>
                    </li>
                    @if (auth()->check())
                        <li class="nav-item">
                            <a href="{{ url('/favourite') }}"
                                class="{{ Request::segment(1) == 'favourite' ? 'nav-link extra active' : 'nav-link extra' }}">
                                <i class="far fa-heart"></i> {{ __('messages.favourite') }} (<span
                                    id="favourite_item_total">@auth{{ $global['favourite_item_total'] }}@endauth
                                </span>)</a>
                        </li>
                    @endif
                    @if (Session::get('locale') == 'nl')
                        <li class="nav-item">
                            <a href="{{ url('https://www.nix18.nl/') }}" class="nav-link extra" target="_blank">
                                {{ __('messages.nix18') }}</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('https://www.idin.nl/') }}" class="nav-link extra" target="_blank">
                                {{ __('messages.idin') }}</a>
                        </li>
                    @endif
                </ul>
                <div class="navbar_effect">
                    <div class="main_line_effect"></div>
                </div>
            </div>

        </div>
        <div class="black_bg"></div>

        <!-- Right navbar links -->
        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto main_header_search">
            {{-- <form class="form-horizontal" method="post"  id="productsearch">
        @csrf --}}
            <div class="form-group has-search header_search"
                style="width: 100%;display:flex;justify-content: space-between;padding:0 !important;align-items: center;">
                <span class="fa fa-search form-control-feedback"></span>
                <input style="width: 95%;padding:0 !important" type="text" class="form-control header_search_input"
                    id="product_search" name="product_search">
            </div>
            {{-- <div class="input-group input-group-sm mt-2">
          <input type="text" class="form-control" id="product_search" name="product_search" placeholder="Search">
          <span class="input-group-append" >
            <input type="hidden" class="form-control" id="product_search_or_not" name="product_search_or_not" placeholder="Search">
          
             <button type="button" id="productsearchsubmit" class="btn btn-primary btn-flat">  <i class="fa fa-search"></i></button>
          </span>
        </div> --}}
            {{-- </form> --}}
            <li class="nav-item dropdown">
                <a class="nav-link text-white py-1 d-flex align-items-center header_profile_div " data-toggle="dropdown"
                    href="#" style="padding:2px;">
                    <button
                        class="locale-flag {{ Session::get('locale') == 'nl' ? 'locale-flag-dutch' : 'locale-flag-eng' }}"
                        style="z-index:-1"></button>
                </a>
                <div class="dropdown-menu dropdown-menu-right static_remove">
                    <a href="{{ url('locale/en') }}" class="dropdown-item">
                        <div class="row">
                            <div class="col-4"> <span class="locale-flag locale-flag-eng"></span></div>
                            <div class="col-8">English</div>
                        </div>
                    </a>
                    <a href="{{ url('locale/nl') }}" class="dropdown-item">
                        <div class="row">
                            <div class="col-4"><span class="locale-flag locale-flag-dutch"></span></div>
                            <div class="col-8">Dutch</div>
                        </div>
                    </a>
                </div>
            </li>

            <!-- Messages Dropdown Menu -->
            <li class="nav-item dropdown">
                @if (auth()->check())
                    <a class="nav-link text-white py-1 d-flex align-items-center header_profile_div"
                        data-toggle="dropdown" href="#" style="padding:0px;">
                        <img class="header_profile-user-img img-fluid img-circle" src="{{ auth()->user()->profile }}"
                            alt="User profile picture" style="z-index:-1">
                        <span class="visible-sm-and-lg">&nbsp;&nbsp;{{ auth()->user()->customer_name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right static_remove">
                        <a onclick="getprofiletab()" href="{{ url('/profile') }}" class="dropdown-item"> <i
                                class="fas fa-user"></i> &nbsp;&nbsp;{{ __('messages.myprofile') }}</a>
                        <a onclick="getchangepasswortab()" class="dropdown-item"><i class="fas fa-unlock-alt"></i>
                            &nbsp;&nbsp;{{ __('messages.chnagepassoword') }}</a>
                        <a href="{{ url('customer/logout') }}" class="dropdown-item"><i
                                class="fas fa-sign-out-alt"></i> &nbsp;&nbsp;{{ __('messages.logout') }}</a>
                    </div>
                @else
                    <button type="button" class="btn btn-default page-link" data-toggle="modal"
                        data-target="#loginModule">Login</button>
                @endif
            </li>
            <!-- =========== burger menu =========== -->
            <button class="navbar-toggler border-0" type="button" data-toggle="collapse"
                data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                aria-label="Toggle navigation">
                <!-- <span class="navbar-toggler-icon"></span> -->
                <div id="nav-icon1">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>
        </ul>
    </div>
    {{-- <div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
     Language
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
      <a class="dropdown-item" href="{{ url('locale/en') }}">English</a>
      <a class="dropdown-item" href="{{ url('locale/nl') }}">Dutch</a>
    </div>
  </div> --}}
</nav>
<script>
    function getchangepasswortab() {
        sessionStorage.setItem('selectedtab', 'changepassword_tab');
        window.location.href = SITE_URL + 'profile';
    }

    function getprofiletab() {
        sessionStorage.setItem('selectedtab', 'profile_tab');
        window.location.href = SITE_URL + 'profile';
    }
    $('.sub-menu ul').hide();
    $(".sub-menu div").click(function() {
        $(this).parent(".sub-menu").children("ul").slideToggle("100");
        $(this).find(".right").toggleClass("fa-caret-up fa-caret-down");
    });

    // $('.custom_navigation').click(function(){
    //   $('.custom_nav').css('display','block')
    // })
    // $(document).click(function(event) {
    //         $target = $(event.target);

    //         if (!$target.closest('.custom_navigation').length){
    //           $('.custom_nav').css('display','none')
    //         }
    //       });

    $(".sub-menu").hover(function() {
        // $(this).css('background','#f8f8f8');
    });
</script>
<script>
    $(document).ready(function() {

        $("#product_search").autocomplete({
            source: function(request, response) {
                // Fetch data
                $.ajax({
                    url: SITE_URL + 'autocomplete',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        _token: $('meta[name=csrf-token]').attr('content'),
                        search: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                // Set selection
                console.log(ui.item);
                $('#product_search').val(ui.item.label); // display the selected text
                $('#productsearchsubmit').val(ui.item.value);
                $('#product_search_or_not').val(1); // save selected id to input

                window.location = "/products/" + ui.item.value
                return false;
            }
        });

    });
    // $("#productsearchsubmit").click(function () {
    // var search_or_not=$('#product_search_or_not').val();

    //   if(search_or_not==1)
    //   {
    //     if(this.value!='' ){
    //       window.location = "/products/" + this.value
    //     }
    //   }
    //  });
</script>


<script>
    $(document).ready(function() {
        $('#nav-icon1,#nav-icon2,#nav-icon3,#nav-icon4').click(function() {
            $(this).toggleClass('open');
        });
    });
</script>
