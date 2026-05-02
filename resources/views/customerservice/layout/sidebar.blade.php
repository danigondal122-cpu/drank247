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
                {{-- <li class="nav-item">
                    <a href="{{url('customer_service/franchise/list')}}" class="nav-link" data-relation="customerservice/franchise/list,customerservice/franchise/view">
                        <img src="{{ asset('images/side_menu/Franchise Management.svg') }}" class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                        <p>
                        Franchise Management
                        </p>
                    </a>
                </li> --}}
                <li class="nav-item">
                    <a href="{{ url('customer_service/deliveryperson/list') }}" class="nav-link"
                        data-relation="customerservice/deliveryperson/list,customerservice/deliveryperson/add,customerservice/deliveryperson/edit">
                        <img src="{{ asset('images/side_menu/Delivery Person.svg') }}" class="nav-icon nav-svg svg"
                            style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                        <p>
                            Delivery Person
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('customer_service/order/list') }}" class="nav-link"
                        data-relation="customerservice/order/list,customerservice/order/view">
                        <img src="{{ asset('images/side_menu/order.svg') }}" class="nav-icon nav-svg svg"
                            style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                        <p>
                            Order
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('customer_service/contactus/list') }}" class="nav-link"
                        data-relation="customerservice/contactus/list,customerservice/contactus/view">
                        <img src="{{ asset('images/side_menu/Contatc us.svg') }}" class="nav-icon nav-svg svg"
                            style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                        <p>
                            Contact Us
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('customer_service/help/list') }}" class="nav-link"
                        data-relation="customerservice/help/list,customerservice/help/view">
                        <img src="{{ asset('images/side_menu/help.svg') }}" class="nav-icon nav-svg svg"
                            style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                        <p>
                            Help
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('customer_service/hours/list') }}" class="nav-link"
                        data-relation="customerservice/hours/list,customerservice/hours/view">
                        <img src="{{ asset('images/side_menu/working hours1.svg') }}" class="nav-icon nav-svg svg"
                            style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
                        <p>
                            Working Hours
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
