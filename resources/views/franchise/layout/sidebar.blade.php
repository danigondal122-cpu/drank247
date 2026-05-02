<style>
  .nav-link.active .svg {
     fill: #ffffff !important;
     width: 1.5rem !important;
  }
</style>
<aside class="main-sidebar elevation-2 sidebar-light-pink">
  <!-- Brand Logo -->
  <a href="#" class="brand-link navbar-pink text-center">
    <!-- <img src="{{asset('images/user2-160x160.jpg')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
      style="opacity: .8">-->
    <span class="brand-text font-weight-light text-white">247DRANK</span>
  </a>
  <!-- Sidebar -->
  <div class="sidebar p-0">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="{{url('franchise/stock/list')}}" class="nav-link" data-relation="franchise/stock/list,franchise/stock/add,franchise/stock/edit">
            <img src="{{ asset('images/side_menu/Stock Management.svg') }}" class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
            <p>
              Stock Management
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{url('franchise/deliveryperson/list')}}" class="nav-link" data-relation="franchise/deliveryperson/list,franchise/deliveryperson/add,franchise/deliveryperson/edit,franchise/deliveryperson/view,franchise/deliveryperson/historydetail">
            <img src="{{ asset('images/side_menu/Delivery Person.svg') }}" class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
            <p>
              Delivery Person
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{url('franchise/order/list')}}" class="nav-link" data-relation="franchise/order/list,franchise/order/view">
            <img src="{{ asset('images/side_menu/order.svg') }}" class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
            <p>
              Order
            </p>
          </a>
        </li>
        <li class="nav-item {{ Request::segment(3) == 'reporting' || Request::segment(3) == 'invoice' ? 'menu-open' : '' }} ">
          <a href="#" class="nav-link" data-relation="franchise/order/reporting,franchise/order/invoice">
            <img src="{{ asset('images/side_menu/order.svg') }}" class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
            <p>
              Reporting
            </p><i class="fas fa-angle-left right"></i>
          </a>
          <ul class="nav nav-treeview" style="display:{{ Request::segment(3) == 'reporting' || Request::segment(3) == 'invoice' ? 'block' : 'none' }}">
            <li class="nav-item">
                    <a href="{{url('franchise/order/reporting')}}" class="nav-link" data-relation="franchise/order/reporting">
                       <i class="far fa-circle nav-icon"></i>
                       <p>Order Archive</p>
                    </a>
            </li>
            <li class="nav-item">
                    <a href="{{url('franchise/order/invoice')}}" class="nav-link" data-relation="franchise/order/invoice">
                       <i class="far fa-circle nav-icon"></i>
                       <p>Invoices</p>
                    </a>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="{{url('franchise/franchisestockorderfrom/list')}}" class="nav-link" data-relation="franchise/franchisestockorderfrom/list,franchise/franchisestockorderfrom/FrStockOrderList,franchise/franchisestockorderfrom/view">
            <img src="{{ asset('images/side_menu/Stock Order.svg') }}" class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
            <p>
              Stock Order
            </p>
          </a>
        </li>
        {{-- <li class="nav-item">
          <a href="{{url('franchise/stockproduct/list')}}" class="nav-link" data-relation="franchise/stockproduct/list,franchise/stockorder/list,franchise/stockorder/view">
            <img src="{{ asset('images/side_menu/Stock Order.svg') }}" class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
            <p>
              Stock Order
            </p>
          </a>
        </li> --}}
        <li class="nav-item">
          <a href="{{url('franchise/schedule/list')}}" class="nav-link" data-relation="franchise/schedule/list,franchise/schedule/add,franchise/schedule/edit">
            <img src="{{ asset('images/side_menu/Delivery Schedule.svg') }}" class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
            <p>
              Schedule Management
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{url('franchise/hours/list')}}" class="nav-link" data-relation="franchise/hours/list,franchise/hours/add,franchise/hours/edit">
            <img src="{{ asset('images/side_menu/working hours1.svg') }}" class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
            <p>
              Working Hours
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="https://live.cartracker.nl/login" class="nav-link" target="_blank">
            <img src="{{ asset('images/side_menu/Car.svg') }}" class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
            <p>
              Car Tracker
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{url('franchise/help/list')}}" class="nav-link" data-relation="franchise/help/list,franchise/help/add,franchise/help/edit">
            <img src="{{ asset('images/side_menu/help.svg') }}" class="nav-icon nav-svg svg" style="width: 1.4rem;margin-right: .5rem;" alt="Chat">
            <p>
             Help
            </p>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
