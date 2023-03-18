<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern semi-dark-layout 2-columns  navbar-sticky footer-static  "
    data-open="click" data-menu="vertical-menu-modern" data-col="2-columns" data-layout="semi-dark-layout">
    <?php $route = explode('.', Route::currentRouteName()); ?>
    <!-- BEGIN: Header-->
    <div class="header-navbar-shadow"></div>
    <nav class="header-navbar main-header-navbar navbar-expand-lg navbar navbar-with-menu fixed-top ">
        <div class="navbar-wrapper">
            <div class="navbar-container content">
                <div class="navbar-collapse" id="navbar-mobile">
                    <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                        <ul class="nav navbar-nav">
                            <li class="nav-item mobile-menu d-xl-none mr-auto"><a
                                    class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i
                                        class="ficon bx bx-menu"></i></a></li>
                        </ul>
                        <ul class="nav navbar-nav bookmark-icons">
                            {{-- <li class="nav-item d-none d-lg-block"><a class="nav-link" href="{{ route('seller_product') }}" data-toggle="tooltip" data-placement="top" title="Seller Product price"><i class="ficon bx bx-dollar-circle"></i></a></li>
                <li class="nav-item d-none d-lg-block"><a class="nav-link" href="{{ route('wishlist') }}" data-toggle="tooltip" data-placement="top" title="Wishlist"><i class="ficon bx bx-add-to-queue"></i></a></li>
                <li class="nav-item d-none d-lg-block"><a class="nav-link" href="{{ route('shopping_cart') }}" data-toggle="tooltip" data-placement="top" title="Shopping cart"><i class="ficon bx bx-cart-alt"></i></a></li> --}}
                        </ul>
                    </div>
                    <ul class="nav navbar-nav float-right">
                        {{-- @role('admin')
              <li class="nav-item d-none d-lg-block m-auto text-center mr-3"><a href="{{route('analytics.index')}}" target="_blank" class="nav-link btn btn-outline-primary mr-3">Show Analytics</a></li>
              <li class="nav-item d-none d-lg-block m-auto text-center"><a href="{{route('demand.dashboard.index')}}" class="nav-link btn btn-light-primary">Go to Demand and Services</a></li>
              @endrole --}}
                        <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i
                                    class="ficon bx bx-fullscreen"></i></a></li>

                        {{-- <li class="dropdown dropdown-notification nav-item"><a class="nav-link nav-link-label" href="#" data-toggle="dropdown"><i class="ficon bx bx-bell bx-tada bx-flip-horizontal"></i><span class="badge badge-pill badge-danger badge-up">5</span></a> --}}
                        {{-- <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right"> --}}
                        {{-- <li class="dropdown-menu-header"> --}}
                        {{-- <div class="dropdown-header px-1 py-75 d-flex justify-content-between"><span class="notification-title">7 new Notification</span><span class="text-bold-400 cursor-pointer">Mark all as read</span></div> --}}
                        {{-- </li> --}}
                        {{-- <li class="dropdown-menu-footer"><a class="dropdown-item p-50 text-primary justify-content-center" href="javascript:void(0)">Read all notifications</a></li> --}}
                        {{-- </ul> --}}
                        {{-- </li> --}}
                        {{-- <div id="loadNotifications"> --}}
                        <li class="dropdown dropdown-notification nav-item">
                            <a class="nav-link nav-link-label" href="#" data-toggle="dropdown"><i
                                    class="ficon bx bx-bell bx-tada bx-flip-horizontal"></i><span
                                    class="badge badge-pill badge-danger badge-up" id="notifyCount">.</span></a>
                            <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                <li class="dropdown-menu-header">
                                    <div class="dropdown-header px-1 py-75 d-flex justify-content-between">
                                        {{-- <span class="notification-title">{{auth()->user()->unreadNotifications->count()}} new Notification</span><span class="text-bold-400 cursor-pointer"> </span> --}}
                                        <a class="cursor-pointer" id="checkNotificationPerm">Check Push Notification</a>
                                        <a class="cursor-pointer" id="readAllNotifications">Mark all as read</a>
                                    </div>
                                </li>
                                @include('layouts.notifications')
                                <li class="dropdown-menu-footer">
                                    <a class="dropdown-item p-50 text-primary justify-content-center"
                                        href="{{ route('orders.index') }}">See all Orders</a>
                                </li>
                            </ul>
                        </li>
                        {{-- </div> --}}
                        <li class="dropdown dropdown-user nav-item"><a
                                class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                <div class="user-nav d-sm-flex d-none"><span
                                        class="user-name">{{ Auth::user()->name }}</span><span
                                        class="user-status text-muted">Available</span></div><span><img
                                        class="round" src="{{ asset(Auth::user()->image) }}" alt="avatar"
                                        height="40" width="40"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right pb-0">
                                @role('admin')
                                    <a class="dropdown-item" href="{{ route('users.edit', Auth::user()) }}"><i
                                            class="bx bx-user mr-50"></i> Profile</a>
                                @else
                                    <a class="dropdown-item" href="{{ route('users.edit', Auth::user()->id) }}"><i
                                            class="bx bx-user mr-50"></i> Profile</a>
                                @endrole
                                {{-- <a class="dropdown-item" href="app-todo.html"><i class="bx bx-check-square mr-50"></i> Change password</a> --}}
                                <div class="dropdown-divider mb-0"></div>
                                <form method="POST" class="dropdown-item" action="{{ route('logout') }}">
                                    @csrf<button type="submit" class="bg-transparent border-0"><i
                                            class="bx bx-power-off mr-50"></i>Logout</button> </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto"><a class="navbar-brand" href="">
                        <div class="">
                            {{-- <img class="logo" src="{{ asset('app-assets/images/ico/logo.png') }}" /> --}}
                        </div>
                        <h2 class="
                            brand-text">{{ config('app.name') }}</h2>
                    </a></li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i
                            class="bx bx-x d-block d-xl-none font-medium-4 primary"></i><i
                            class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block primary"
                            data-ticon="bx-disc"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation"
                data-icon-style="lines">
                <li class=" nav-item {{ $route[0] == 'dashboard' ? 'active' : '' }}"><a
                        href="{{ route('dashboard.index') }}"><i class="bx bx-home" data-icon="desktop"></i><span
                            class="menu-title" data-i18n="Dashboard">Dashboard</span></a>
                </li>
                {{-- @can('view_roles')
            <li class=" nav-item {{ $route[0] == 'roles' ? 'active' : ''}}"><a href="{{route('roles.index')}}"><i class="bx bx-key" data-icon="desktop"></i><span class="menu-title" data-i18n="Dashboard">Roles</span></a>
            </li>
          @endcan --}}
                @role('admin')
                    <li class=" nav-item {{ $route[0] == 'users' ? 'active' : '' }}"><a
                            href="{{ route('users.index') }}"><i class="bx bxs-group" data-icon="users"></i><span
                                class="menu-title" data-i18n="Dashboard">Users</span></a>
                    </li>
                @endrole
                @can('view_categories')
                    <li class=" navigation-header"><span>Categories</span>
                    </li>
                    <li class=" nav-item {{ $route[0] == 'categories' ? ' active' : '' }}"><a
                            href="{{ route('categories.index') }}"><i class="bx bx-task"
                                data-icon="settings"></i><span class="menu-title"
                                data-i18n="Form Layout">Categories</span></a>
                    </li>
                    <li class=" nav-item {{ $route[0] == 'banners' ? ' active' : '' }}"><a
                            href="{{ route('banners.index') }}"><i class="bx bx-news" data-icon="settings"></i><span
                                class="menu-title" data-i18n="Form Layout">Banners</span></a>
                    </li>
                    <li class=" nav-item {{ $route[0] == 'shop-banners' ? ' active' : '' }}"><a
                            href="{{ route('shop-banners.index') }}"><i class="bx bx-news"
                                data-icon="settings"></i><span class="menu-title" data-i18n="Form Layout">Shop
                                Banners</span></a>
                    </li>

                @endcan


                @can('view_shops')
                    <li class=" navigation-header"><span>Shops</span>
                    </li>
                    @php $active = ($route[0] == 'shops') ? 'active' : ''; @endphp
                    <li class=" nav-item {{ $active }}"><a href="{{ route('shops.index') }}"><i
                                class="bx bx-sitemap" data-icon="envelope-pull"></i><span class="menu-title"
                                data-i18n="Email">Shops</span></a>
                    </li>
                @endcan
                @can('view_products')
                    <li class=" navigation-header"><span>Product</span>
                    </li>
                @endcan

                @can('view_products')
                    @php $active = ($route[0] == 'products') ? 'active' : ''; @endphp
                    <li class=" nav-item {{ $active }}"><a href="{{ route('products.index') }}"><i
                                class="bx bxs-categories" data-icon="settings"></i><span class="menu-title"
                                data-i18n="Form Layout">Products</span></a>
                    </li>
                @endcan
                <li class=" nav-item {{ $route[0] == 'titles' ? ' active' : '' }}"><a
                        href="{{ route('titles.index') }}"><i class="bx bx-spreadsheet"
                            data-icon="settings"></i><span class="menu-title" data-i18n="Form Layout">Topping
                            Categories</span></a>
                </li>
                @can('view_product-categories')
                    @php $active = ($route[0] == 'product-categories') ? 'active' : ''; @endphp
                    <li class=" nav-item {{ $active }}"><a href="{{ route('product-categories.index') }}"><i
                                class="bx bx-basket" data-icon="settings"></i><span class="menu-title"
                                data-i18n="Form Layout">Product Categories</span></a>
                    </li>
                @endcan
                @can('view_cuisines')
                    @php $active = ($route[0] == 'cuisines') ? 'active' : ''; @endphp
                    <li class=" nav-item {{ $active }}"><a href="{{ route('cuisines.index') }}"><i
                                class="bx bxs-package" data-icon="priority-low"></i><span class="menu-title"
                                data-i18n="Form Wizard">Cuisines</span></a>
                    </li>
                @endcan
                   @can('view_coupons')
                    <li class=" navigation-header"><span>Coupons</span>
                    </li>
                @endcan
                @can('view_coupons')
                    @php $active = ($route[0] == 'coupons') ? 'active' : ''; @endphp
                
                     <li class=" nav-item {{ $active }}"><a href="{{ route('vendorcoupons.index') }}"><i
                                class="bx bx-credit-card-alt" data-icon="morph-preview"></i><span class="menu-title"
                                data-i18n="Disabled Menu">Coupons</span></a>
                    </li>
                @endcan
                @role('vendor')
                    @php $active = ($route[0] == 'vendorcoupons') ? 'active' : ''; @endphp
                    <li class=" nav-item {{ $active }}"><a href="{{ route('vendorcoupons.index') }}"><i
                                class="bx bx-credit-card-alt" data-icon="morph-preview"></i><span class="menu-title"
                                data-i18n="Disabled Menu">VendorCoupons</span></a>
                    </li>
                @endrole
                @can('view_addresses')
                    <li class=" navigation-header"><span>Address</span>
                    </li>
                    <li class=" nav-item {{ $route[0] == 'addresses' ? ' active' : '' }}"><a
                            href="{{ route('addresses.index') }}"><i class="bx bx-map"
                                data-icon="settings"></i><span class="menu-title"
                                data-i18n="Form Layout">Address</span></a>
                    </li>

                @endcan
                @can('view_slots')
                    <li class=" nav-item {{ $route[0] == 'slots' ? ' active' : '' }}"><a
                            href="{{ route('slots.index') }}"><i class="bx bx-time" data-icon="settings"></i><span
                                class="menu-title" data-i18n="Form Layout">Slots</span></a>
                    </li>
                @endcan
                {{-- @can('view_products')
              @php $active = ($route[0] == 'products') ? 'active' : ''; @endphp
              <li class=" nav-item {{$active}}"><a href="{{route('products.index')}}"><i class="bx bx-basket" data-icon="priority-low"></i><span class="menu-title" data-i18n="Form Wizard">Products</span></a>
            </li>
           @endcan
           @can('view_stocks')
                @php $active = ($route[0] == 'stocks') ? 'active' : ''; @endphp
          <li class=" nav-item {{$active}}"><a href="{{route('stocks.index')}}"><i class="bx bxs-package" data-icon="priority-low"></i><span class="menu-title" data-i18n="Form Wizard">Stocks</span></a>
              </li>
           @endcan --}}
                {{-- @can('view_stocks')
                @php $active = ($route[0] == 'sales') ? 'active' : ''; @endphp
          <li class=" nav-item {{$active}}"><a href="{{route('stocks.index')}}"><i class="bx bx-task" data-icon="priority-low"></i><span class="menu-title" data-i18n="Form Wizard">Manage Sales</span></a>
              </li>
           @endcan --}}



                {{-- @php $active = ($route[0] == 'subcategory') ? 'active' : ''; @endphp
          <li class=" nav-item {{ $active }}"><a href=""><i class="bx bx-sitemap" data-icon="comments"></i><span class="menu-title" data-i18n="Chat">Hubs</span></a>
          </li> --}}
                {{-- @can('view_offers')
          <li class=" navigation-header"><span>Offers</span>
          </li>
        <li class=" nav-item {{ $route[0] == 'offers' ? ' active' : '' }}"><a href="{{route('offers.index')}}"><i class="bx bx-time-five" data-icon="settings"></i><span class="menu-title" data-i18n="Form Layout">Offers</span></a>
          </li>
          @endcan

          @can('view_combos')
              @php $active = ($route[0] == 'combos') ? 'active' : ''; @endphp
              <li class=" nav-item {{$active}}"><a href="{{route('combos.index')}}"><i class="bx bxs-grid" data-icon="priority-low"></i><span class="menu-title" data-i18n="Form Wizard">Combos</span></a>
            </li>
        @endcan
        @can('view_coupons')
          @php $active = ($route[0] == 'coupons') ? 'active' : ''; @endphp
          <li class=" nav-item {{$active}}"><a href="{{route('coupons.index')}}"><i class="bx bx-credit-card-alt" data-icon="morph-preview"></i><span class="menu-title" data-i18n="Disabled Menu">Coupons</span></a>
          </li>
        @endcan


         <li class=" nav-item {{ $route[0] == 'delivery-boys' ? ' active' : '' }}"><a href="{{route('delivery-boys.index')}}"><i class="bx bx-truck" data-icon="settings"></i><span class="menu-title" data-i18n="Form Layout">Delivery Boy</span></a>
           </li> --}}


                {{-- <li class=" nav-item {{ $route[0] == 'category-offer' ? ' active' : '' }}"><a href=""><i class="bx bx-gift" data-icon="priority-low"></i><span class="menu-title" data-i18n="Form Wizard">Category offers</span></a>
          </li> --}}
                @can('view_orders')
                    <li class=" navigation-header"><span>Order and Delivery</span>
                        @php $active = ($route[0] == 'orders') ? 'active' : ''; @endphp
                    <li class=" nav-item {{ $active }}"><a href="{{ route('orders.index') }}"><i
                                class="bx bx-link" data-icon="warning-alt"></i><span class="menu-title"
                                data-i18n="Sweet Alert">Orders</span></a>
                    </li>
                    @php $active = ($route[0] == 'paid-orders') ? 'active' : ''; @endphp
                    <li class=" nav-item {{ $active }}"><a href="{{ route('paid-orders.index') }}"><i
                                class="bx bx-money" data-icon="warning-alt"></i><span class="menu-title"
                                data-i18n="Sweet Alert">Paid Orders</span></a>
                    </li>
                @endcan
                @can('view_delivery_boy_charge')
                    @php $active = ($route[0] == 'manage-deliveries') ? 'active' : ''; @endphp
                    <li class=" nav-item {{ $active }}"><a href="{{ route('manage-deliveries.index') }}"><i
                                class="bx bx-map-pin" data-icon="warning-alt"></i><span class="menu-title"
                                data-i18n="Sweet Alert">Delivery</span></a>
                    </li>
                    @php $active = ($route[0] == 'sales') ? 'active' : ''; @endphp
                    <li class=" nav-item {{ $active }}"><a href="{{ route('sales.index') }}"><i
                                class="bx bx-receipt" data-icon="warning-alt"></i><span class="menu-title"
                                data-i18n="Sweet Alert">Sales</span></a>
                    </li>
                    </li>

                @endcan
                 <li class=" navigation-header"><span>Door To Door Delivery</span>
                </li>
                {{-- <li class=" nav-item {{ $route[0] == 'payments' ? ' active' : '' }}"><a href="{{route('payments.index')}}"><i class="bx bx-task" data-icon="settings"></i><span class="menu-title" data-i18n="Form Layout">Payments</span></a>
          </li> --}}
               <li class=" nav-item {{ $route[0] == 'door-To-Door-Delivery'? ' active' : '' }}"><a href="{{ route('door-To-Door-Delivery.index') }}"><i
                                class="bx bx-briefcase-alt" data-icon="warning-alt"></i><span class="menu-title"
                                data-i18n="Sweet Alert">Charges</span></a>
                    </li>
             <li class=" nav-item {{ $route[0] == 'door-To-Door-Delivery-Order'? ' active' : '' }}"><a href="{{ route('door-To-Door-Delivery-Order.index') }}"><i
                                class="bx bx-receipt" data-icon="warning-alt"></i><span class="menu-title"
                                data-i18n="Sweet Alert">Orders</span></a>
                    </li>
                <li class=" navigation-header"><span>Payments</span>
                </li>
                {{-- <li class=" nav-item {{ $route[0] == 'payments' ? ' active' : '' }}"><a href="{{route('payments.index')}}"><i class="bx bx-task" data-icon="settings"></i><span class="menu-title" data-i18n="Form Layout">Payments</span></a>
          </li> --}}
                {{-- @role('admin')
                <li class=" nav-item {{ $route == 'paymentslist' ? ' active' : '' }}"><a
                        href="{{ route('payments.list') }}"><i class="bx bx-task" data-icon="settings"></i><span
                            class="menu-title" data-i18n="Form Layout">Pending Payments</span></a>
                </li>
                @endrole --}}

                <li class=" nav-item {{ $route[0] == 'payments' ? ' active' : '' }}"><a
                        href="{{ route('payments.index') }}"><i class="bx bx-task"
                            data-icon="settings"></i><span class="menu-title"
                            data-i18n="Form Layout">Payments</span></a>
                </li>
                @role('admin')
                    <li class=" nav-item {{ $route[0] == 'reviews' ? ' active' : '' }}"><a
                            href="{{ route('reviews.index') }}"><i class="bx bx-star" data-icon="settings"></i><span
                                class="menu-title" data-i18n="Form Layout">Reviews</span></a>
                    </li>

                    <li class=" navigation-header"><span>Global Information</span>
                    </li>
                    <li class=" nav-item {{ request()->is('contacts*') ? 'active' : '' }}"><a
                            href="{{ route('contacts.index') }}"><i class="bx bxs-contact"
                                data-icon="warning-alt"></i><span class="menu-title"
                                data-i18n="Sweet Alert">Settings</span></a>

                    <li class=" nav-item {{ request()->is('directions*') ? 'active' : '' }}"><a
                            href="{{ route('directions.index') }}"><i class="bx bxs-pin"
                                data-icon="warning-alt"></i><span class="menu-title"
                                data-i18n="Sweet Alert">Directions</span></a>
                    </li>
                @endrole
                {{-- @php $active = (Route::currentRouteName() == 'cancel_orders') ? 'active' : ''; @endphp
            <li class=" nav-item {{$active}}"><a href=""><i class="bx bx-receipt" data-icon="warning-alt"></i><span class="menu-title" data-i18n="Sweet Alert">Canceled Orders</span></a>
          </li>
            @php $active = (Route::currentRouteName() == 'bulkorder') ? 'active' : ''; @endphp
            <li class=" nav-item {{$active}}"><a href=""><i class="bx bx-briefcase-alt" data-icon="morph-map"></i><span class="menu-title" data-i18n="Toastr">Bulk order</span></a>
          </li>
            @php $active = (Route::currentRouteName() == 'bulkorderusers') ? 'active' : ''; @endphp
            <li class=" nav-item {{$active}}"><a href=""><i class="bx bx-briefcase-alt" data-icon="morph-map"></i><span class="menu-title" data-i18n="Toastr">Bulk order users</span></a>
          </li> --}}
                {{-- <li class=" navigation-header"><span>Receipe</span>
          </li> --}}
                {{-- <li class=" nav-item {{ $route[0] == 'recipe-master' ? ' active' : '' }}"><a href=""><i class="bx bx-news" data-icon="warning-alt"></i><span class="menu-title" data-i18n="Sweet Alert">Receipe master</span></a>
          </li>
          <li class=" nav-item {{ $route[0] == 'recipe-category' ? ' active' : '' }}"><a href=""><i class="bx bxs-dock-top" data-icon="morph-map"></i><span class="menu-title" data-i18n="Toastr">Receipe category</span></a>
          </li>
          <li class=" nav-item {{ $route[0] == 'recipe-sub-category' ? ' active' : '' }}"><a href=""><i class="bx bx-grid-alt" data-icon="morph-map"></i><span class="menu-title" data-i18n="Toastr">Receipe subcategory</span></a>
          </li>
          <li class=" navigation-header"><span>Components</span>
          </li> --}}
                {{-- @php $active = (Route::currentRouteName() == 'sliders') ? 'active' : ''; @endphp
            <li class="nav-item {{$active}}"><a href=""><i class="bx bx-revision" data-icon="morph-preview"></i><span class="menu-title" data-i18n="Disabled Menu">Slider</span></a>
          </li>
            @php $active = (Route::currentRouteName() == 'paymenttypes') ? 'active' : ''; @endphp
            <li class=" nav-item {{$active}}"><a href=""><i class="bx bx-money" data-icon="morph-preview"></i><span class="menu-title" data-i18n="Disabled Menu">User payment type</span></a>
          </li>
            @php $active = (Route::currentRouteName() == 'smstemplates') ? 'active' : ''; @endphp
            <li class=" nav-item {{$active}}"><a href=""><i class="bx bx-message" data-icon="morph-preview"></i><span class="menu-title" data-i18n="Disabled Menu">SMS template list</span></a>
          </li>
          <li class=" navigation-header"><span>Other masters</span>
          </li>
        <li class=" nav-item {{ $route[0] == 'event-master' ? ' active' : '' }}"><a href=""><i class="bx bx-calendar-event" data-icon="user"></i><span class="menu-title" data-i18n="User Profile">Event master</span></a>
          </li> --}}
                {{-- <li class=" nav-item {{ $route[0] == 'store' ? ' active' : '' }}"><a href=""><i class="bx bx-store" data-icon="info-alt"></i><span class="menu-title" data-i18n="Knowledge Base">Store</span></a>
          </li>
          <li class=" nav-item {{ $route[0] == 'membership' ? ' active' : '' }}"><a href=""><i class="bx bx-id-card" data-icon="wrench"></i><span class="menu-title" data-i18n="Account Settings">Membership</span></a>
          </li> --}}

                {{-- <li class=" nav-item {{ $route[0] == 'customer' ? ' active' : '' }}"><a href=""><i class="bx bx-user-check" data-icon="wrench"></i><span class="menu-title" data-i18n="Account Settings">Display customer list</span></a>
          </li>
            @php $active = (Route::currentRouteName() == 'weights') ? 'active' : ''; @endphp
            <li class=" nav-item {{$active}}"><a href=""><i class="bx bx-compass" data-icon="wrench"></i><span class="menu-title" data-i18n="Account Settings">Weight</span></a>
          </li>
            @php $active = (Route::currentRouteName() == 'notifications') ? 'active' : ''; @endphp
            <li class=" nav-item {{$active}}"><a href=""><i class="bx bx-bell" data-icon="wrench"></i><span class="menu-title" data-i18n="Account Settings">Notification</span></a>
          </li>
            <li class=" navigation-header"><span>Pincodes</span>
            </li>
            @php $active = (Route::currentRouteName() == 'sdpincodes') ? 'active' : ''; @endphp
            <li class=" nav-item {{ $active }}"><a href=""><i class="bx bx-cog" data-icon="warning-alt"></i><span class="menu-title" data-i18n="Sweet Alert">Standard Pincode</span></a>
            </li> --}}
                {{-- @php $active = (Route::currentRouteName() == 'edpincodes') ? 'active' : ''; @endphp
            <li class=" nav-item {{ $active}}"><a href=""><i class="bx bx-wrench" data-icon="morph-map"></i><span class="menu-title" data-i18n="Toastr">Extended Pincode</span></a>
            </li>
          <li class=" navigation-header"><span>Settings</span>
          </li>
        <li class=" nav-item {{ $route[0] == 'setting' ? ' active' : '' }}"><a href=""><i class="bx bx-cog" data-icon="warning-alt"></i><span class="menu-title" data-i18n="Sweet Alert">Settings</span></a>
          </li>
          <li class=" nav-item {{ $route[0] == 'footer' ? ' active' : '' }}"><a href=""><i class="bx bx-wrench" data-icon="morph-map"></i><span class="menu-title" data-i18n="Toastr">Footer settings</span></a>
          </li>
          <li class=" navigation-header"><span>Reports</span>
          </li> --}}
                {{-- @php $active = ($route[0] == 'selling-report') ? 'active' : ''; @endphp
          <li class=" nav-item {{ $active }}"><a href=""><i class="bx bx-spreadsheet" data-icon="warning-alt"></i><span class="menu-title" data-i18n="Sweet Alert">Seller selling report</span></a>
          </li>
          @php $active = ($route[0] == 'selling-invoice') ? 'active' : ''; @endphp
          <li class=" nav-item {{ $active }}"><a href=""><i class="bx bx-server" data-icon="morph-map"></i><span class="menu-title" data-i18n="Toastr">Selling invoice report</span></a>
          </li>
          @php $active = ($route[0] == 'product-price') ? 'active' : ''; @endphp
          <li class=" nav-item {{ $active }}"><a href=""><i class="bx bx-copy-alt" data-icon="morph-map"></i><span class="menu-title" data-i18n="Toastr">Product price report</span></a>
          </li> --}}
            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2 mt-1">
                    @include('layouts.breadcrump')
                </div>
            </div>
            <div class="content-body">
                <!-- Chartist  -->
                <div id="flash-msg">
                    @include('flash::message')
                </div>
                @yield('content')

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>
