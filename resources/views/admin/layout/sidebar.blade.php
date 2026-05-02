@php
    $global['assigned_module'] =auth('admin')->user()->modules()->pluck('id')->all();
@endphp
<style>
    .nav-link.active .svg {
        fill: #ffffff !important;
        width: 1.5rem !important;
    }
</style>
<aside class="main-sidebar elevation-2 sidebar-light-pink">
    <!-- Brand Logo -->
    <a href="#" class="brand-link navbar-pink text-center">
        <!-- <img src="{{ asset('images/user2-160x160.jpg') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
        style="opacity: .8">-->
        <span class="brand-text font-weight-light text-white">247DRANK</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar p-0">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                @if (auth('admin')->user()->admin_type == 'superadmin')
                    <li class="nav-item">
                        <a href="{{ url('admin/admin/list') }}" class="nav-link"
                            data-relation="admin/admin/list,admin/admin/add,admin/admin/edit">
                            <img class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;"
                                src="{{ asset('images/side_menu/admin management.svg') }}" />
                            {{-- <img src="{{ asset('images/side_menu/Category Management.svg') }}" class="nav-icon nav-svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat"> --}}
                            <p>
                                Admin Management
                            </p>
                        </a>
                    </li>
                @endif
                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('15', $global['assigned_module']))
                    <li class="nav-item">
                        <a href="{{ url('admin/allergen/list') }}" class="nav-link"
                            data-relation="admin/allergen/list,admin/allergen/add,admin/allergen/edit">
                            <img src="{{ asset('images/side_menu/allergen.svg') }}" class="nav-icon nav-svg svg"
                                style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                            <p>
                                Allergen
                            </p>
                        </a>
                    </li>
                @endif
                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('1', $global['assigned_module']))
                    <li class="nav-item">
                        <a href="{{ url('admin/category/list') }}" class="nav-link"
                            data-relation="admin/category/list,admin/category/add,admin/category/edit,admin/category/subcategorylist,admin/category/assignproduct">
                            <img class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;"
                                src="{{ asset('images/side_menu/Category Management.svg') }}" />
                            {{-- <img src="{{ asset('images/side_menu/Category Management.svg') }}" class="nav-icon nav-svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat"> --}}
                            <p>
                                Category Management
                            </p>
                        </a>
                    </li>
                @endif
                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('2', $global['assigned_module']))
                    <li class="nav-item">
                        <a href="{{ url('admin/product/list') }}" class="nav-link"
                            data-relation="admin/product/list,admin/product/add,admin/product/edit">
                            <img src="{{ asset('images/side_menu/Product Management.svg') }}"
                                class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                            <p>
                                Product Management
                            </p>
                        </a>
                    </li>
                @endif
                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('3', $global['assigned_module']))
                    <li class="nav-item">
                        <a href="{{ url('admin/extraproduct/list') }}" class="nav-link"
                            data-relation="admin/extraproduct/list,admin/extraproduct/add,admin/extraproduct/edit">
                            <img src="{{ asset('images/side_menu/extra item.svg') }}" class="nav-icon nav-svg svg"
                                style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                            <p>
                                Extra Product
                            </p>
                        </a>
                    </li>
                @endif
                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('3', $global['assigned_module']))
                    <li
                        class="nav-item   {{ Request::segment(2) == 'warehouse' || Request::segment(2) == 'warehouseproduct' || Request::segment(2) == 'warehousestock' ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link"
                            data-relation="admin/warehouse,admin/warehouseproduct,admin/warehousestock">
                            <img src="{{ asset('images/side_menu/Stock Order.svg') }}" class="nav-icon nav-svg svg"
                                style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                            <p>
                                Warehouse
                            </p>
                            <i class="fas fa-angle-left right"></i>
                        </a>
                        <ul class="nav nav-treeview"
                            style="display:{{ Request::segment(2) == 'warehouse' || Request::segment(2) == 'warehouseproduct' || Request::segment(2) == 'warehousestock' || Request::segment(2) == 'warehousestockorder' ? 'block' : 'none' }}">

                            <li class="nav-item">
                                <a href="{{ url('admin/warehouse/list') }}" class="nav-link"
                                    data-relation="admin/warehouse/">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Warehouse</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/warehouseproduct/list') }}" class="nav-link"
                                    data-relation="admin/warehouseproduct/">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Warehouse Product</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/warehousestock/list') }}" class="nav-link"
                                    data-relation="admin/warehousestock/">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Warehouse Stock</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/warehousestockorder/list') }}" class="nav-link"
                                    data-relation="admin/warehousestockorder/list,admin/warehousestockorder/view">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>
                                        Warehouse Stock Order
                                    </p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('4', $global['assigned_module']))
                    <li class="nav-item">
                        <a href="{{ url('admin/pool/list') }}" class="nav-link"
                            data-relation="admin/pool/list,admin/pool/add,admin/pool/edit">
                            <img src="{{ asset('images/side_menu/Pool Management.svg') }}" class="nav-icon nav-svg svg"
                                style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                            <p>
                                Pool Management
                            </p>
                        </a>
                    </li>
                @endif
                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('5', $global['assigned_module']))
                    <li class="nav-item">
                        <a href="{{ url('admin/customerservice/list') }}" class="nav-link"
                            data-relation="admin/customerservice/list,admin/customerservice/add,admin/customerservice/edit,admin/customerservice/hours/list/">
                            <img src="{{ asset('images/side_menu/Customer Services.svg') }}"
                                class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                            <p>
                                Customer Services
                            </p>
                        </a>
                    </li>
                @endif
                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('6', $global['assigned_module']))
                    <li class="nav-item">
                        <a href="{{ url('admin/franchise/list') }}" class="nav-link"
                            data-relation="admin/franchise/list,admin/franchise/add,admin/franchise/edit">
                            <img src="{{ asset('images/side_menu/Franchise Management.svg') }}"
                                class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;"
                                alt="Chat">
                            <p>
                                Franchise Management
                            </p>
                        </a>
                    </li>
                @endif
                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('7', $global['assigned_module']))
                    <li class="nav-item">
                        <a href="{{ url('admin/stock/list') }}" class="nav-link"
                            data-relation="admin/stock/list,admin/stock/add,admin/stock/edit">
                            <img src="{{ asset('images/side_menu/Stock Management.svg') }}"
                                class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;"
                                alt="Chat">
                            <p>
                                Stock Management
                            </p>
                        </a>
                    </li>
                @endif
                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('8', $global['assigned_module']))
                    <li class="nav-item">
                        <a href="{{ url('admin/customer/list') }}" class="nav-link"
                            data-relation="admin/customer/list,admin/customer/add,admin/customer/edit">
                            <img src="{{ asset('images/side_menu/Customer Management.svg') }}"
                                class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;"
                                alt="Chat">
                            <p>
                                Customer Management
                            </p>
                        </a>
                    </li>
                @endif
                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('9', $global['assigned_module']))
                    <li class="nav-item">
                        <a href="{{ url('admin/deliveryperson/list') }}" class="nav-link"
                            data-relation="admin/deliveryperson/list,admin/deliveryperson/add,admin/deliveryperson/edit">
                            <img src="{{ asset('images/side_menu/Delivery Person.svg') }}"
                                class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;"
                                alt="Chat">
                            <p>
                                Delivery Person
                            </p>
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a href="{{ url('admin/uber/storelist') }}" class="nav-link"
                        data-relation="admin/uber/storelist,admin/uber/storemenu,admin/uber/storeview">
                        <img src="{{ asset('images/side_menu/Delivery Person.svg') }}" class="nav-icon nav-svg svg"
                            style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                        <p>
                            Uber Store
                        </p>
                    </a>
                </li>

                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('10', $global['assigned_module']))
                    <li
                        class="nav-item {{ (Request::segment(2) == 'order' && Request::segment(3) == 'list') || (Request::segment(2) == 'order' && Request::segment(3) == 'invoice-pdf') || (Request::segment(2) == 'order' && Request::segment(3) == 'all-invoice') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link"
                            data-relation="admin/order/list,admin/order/view,admin/order/invoice-pdf">
                            <img src="{{ asset('images/side_menu/order.svg') }}" class="nav-icon nav-svg svg"
                                style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                            <p>
                                Order
                            </p><i class="fas fa-angle-left right"></i>
                        </a>
                        <ul class="nav nav-treeview"
                            style="display:{{ (Request::segment(2) == 'order' && Request::segment(3) == 'invoice-pdf') || (Request::segment(2) == 'order' && Request::segment(3) == 'all-invoice') || (Request::segment(2) == 'order' && Request::segment(3) == 'list') ? 'block' : 'none' }}">
                            <li class="nav-item">
                                <a href="{{ url('admin/order/list') }}" class="nav-link"
                                    data-relation="admin/order/list">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Order</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/order/invoice-pdf') }}" class="nav-link"
                                    data-relation="admin/order/invoice-pdf">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Generate Invoice PDF</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/order/all-invoice') }}" class="nav-link"
                                    data-relation="admin/order/all-invoice">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Franchise Invoices</p>
                                </a>
                            </li>
                        </ul>

                    </li>
                @endif

                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('11', $global['assigned_module']))
                    <li class="nav-item">
                        <a href="{{ url('admin/stockorder/list') }}" class="nav-link"
                            data-relation="admin/stockorder/list,admin/stockorder/view">
                            <img src="{{ asset('images/side_menu/Stock Order.svg') }}" class="nav-icon nav-svg svg"
                                style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                            <p>
                                Stock Order
                            </p>
                        </a>
                    </li>
                @endif
                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('12', $global['assigned_module']))
                    <li class="nav-item">
                        <a href="{{ url('admin/deliveryperson/schedule') }}" class="nav-link"
                            data-relation="admin/deliveryperson/schedule">
                            <img src="{{ asset('images/side_menu/Delivery Schedule.svg') }}"
                                class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;"
                                alt="Chat">
                            <p>
                                Delivery Schedule
                            </p>
                        </a>
                    </li>
                @endif
                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('13', $global['assigned_module']))
                    <li class="nav-item">
                        <a href="{{ url('admin/promocode/list') }}" class="nav-link"
                            data-relation="admin/promocode/list,admin/promocode/add">
                            <img src="{{ asset('images/side_menu/Promo.svg') }}" class="nav-icon nav-svg svg"
                                style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                            <p>
                                Promo Code
                            </p>
                        </a>
                    </li>
                @endif
                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('14', $global['assigned_module']))
                    <li class="nav-item">
                        <a href="{{ url('admin/message/list') }}" class="nav-link"
                            data-relation="admin/message/list,admin/message/add,admin/message/edit">
                            <img src="{{ asset('images/side_menu/message.svg') }}" class="nav-icon nav-svg svg"
                                style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                            <p>
                                Broadcast message
                            </p>
                        </a>
                    </li>
                @endif

                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('15', $global['assigned_module']))
                    <!-- <li class="nav-item">
               <a href="{{ url('admin/paymentmethod') }}" class="nav-link" data-relation="admin/paymentmethod">
                  <img src="{{ asset('img/payment-method.png') }}" class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                  <p>
                     Payment Methods
                  </p>
               </a>
            </li> -->
                    <li class="nav-item {{ Request::segment(2) == 'paymentmethod' ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link" data-relation="admin/paymentmethod">
                            <img src="{{ asset('img/payment-method.png') }}" class="nav-icon nav-svg svg"
                                style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                            <p>
                                Payment
                            </p>
                            <i class="fas fa-angle-left right"></i>
                        </a>
                        <ul class="nav nav-treeview"
                            style="display:{{ Request::segment(2) == 'paymentmethod' ? 'block' : 'none' }}">
                            <li class="nav-item">
                                <a href="{{ url('admin/paymentmethod') }}" class="nav-link"
                                    data-relation="admin/paymentmethod">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Payment Methods</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="https://admin.pay.nl/dashboard" class="nav-link" target="_blank">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pay Provider</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                {{--
           <li class="nav-item">
              <a href="{{url('admin/settings')}}" class="nav-link" data-relation="admin/settings">
            <img src="{{ asset('images/side_menu/setting.svg') }}" class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
            <p>
               Settings
            </p>
            </a>
            </li>
            --}}
                @if (auth('admin')->user()->admin_type == 'superadmin' || in_array('16', $global['assigned_module']))
                    <li
                        class="nav-item   {{ Request::segment(2) == 'settings' || Request::segment(2) == 'contactus' || Request::segment(2) == 'help' || Request::segment(2) == 'banner' || Request::segment(2) == 'privacy_policy' || Request::segment(2) == 'cookie_statement' || Request::segment(2) == 'terms_condition' || Request::segment(2) == 'colophone' || Request::segment(2) == 'technology' || Request::segment(2) == 'alcohol_law' ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link"
                            data-relation="admin/settings,admin/banner,admin/privacy_policy,admin/terms_condition,admin/colophone,admin/cookie_statement,admin/contactus/list,admin/contactus/add,admin/help/list,admin/help/add,admin/technology,admin/alcohol_law">
                            <img src="{{ asset('images/side_menu/Setting.svg') }}" class="nav-icon nav-svg svg"
                                style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                            <p>
                                Settings
                            </p>
                            <i class="fas fa-angle-left right"></i>
                        </a>
                        <ul class="nav nav-treeview"
                            style="display:{{ Request::segment(2) == 'settings' || Request::segment(2) == 'contactus' || Request::segment(2) == 'help' || Request::segment(2) == 'banner' || Request::segment(3) == 'privacy_policy' || Request::segment(3) == 'terms_condition' || Request::segment(3) == 'colophone' || Request::segment(3) == 'cookie_statement' || Request::segment(3) == 'alcohol_law' ? 'block' : 'none' }}">

                            <li class="nav-item">
                                <a href="{{ url('admin/settings') }}" class="nav-link"
                                    data-relation="admin/settings">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Get In Touch</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/contactus/list') }}" class="nav-link"
                                    data-relation="admin/contactus/list,admin/contactus/add">
                                    {{-- <img src="{{ asset('images/side_menu/Contatc us.svg') }}" class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat"> --}}
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>
                                        Contact Us
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/help/list') }}" class="nav-link"
                                    data-relation="admin/help/list,admin/help/add">
                                    {{-- <img src="{{ asset('images/side_menu/help.svg') }}" class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat"> --}}
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>
                                        Help
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/settings/banner') }}" class="nav-link"
                                    data-relation="admin/settings/banner">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Banner</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/cms/privacy_policy') }}" class="nav-link"
                                    data-relation="admin/cms/privacy_policy">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Privacy Policy</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/cms/terms_condition') }}" class="nav-link"
                                    data-relation="admin/cms/terms_condition">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Terms and Conditions</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/cms/colophone') }}" class="nav-link"
                                    data-relation="admin/cms/colophone">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Colophone</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/cms/cookie_statement') }}" class="nav-link"
                                    data-relation="admin/cms/cookie_statement">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Cookie statement</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/cms/alcohol_law') }}" class="nav-link"
                                    data-relation="admin/cms/alcohol_law">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Guaranteed Working Method Alcohol Law</p>
                                </a>
                            </li>
                            {{-- <li class="nav-item">
                  <a href="{{url('admin/technology')}}" class="nav-link" data-relation="admin/technology">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Technology</p>
                  </a>
            </li> --}}
                        </ul>
                @endif
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
