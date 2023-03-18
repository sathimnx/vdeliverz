@extends('layouts.main')

@section('content')
    <section class="users-list-wrapper">

        <?php $route = explode('.', Route::currentRouteName()); ?>
        <div class="users-list-table">
            <div class="card">
                <div class="card-header">
                    <div class="card-text">
                        <h5 class="d-block card-text text-center mb-3">Total Orders - {{ $orders_count }}</h5>
                        <div class="row justify-content-between">
                            <div class="col-sm-4">
                                @role('admin')
                                    <label for="">Select Shop</label>
                                    <select name="" id="filterByShop" class="form-control select2"
                                        onchange="filterOrder('{{ env('APP_URL') }}')">
                                        <option value="all" {{ request()->type == 'all' ? 'selected' : '' }}>All</option>
                                        @forelse ($shops as $item)
                                            <option value="{{ $item->id }}"
                                                {{ request()->shop == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                            </option>
                                        @empty

                                        @endforelse
                                    </select>
                                @else
                                    <input type="hidden" name="" id="filterByShop" value="{{ $shop->id }}">
                                @endrole
                            </div>
                            <div class="col-sm-4">
                                {{-- @can('view_orders')
                                    <label for="">Order Status</label>
                                    <select name="" id="filterByType" onchange="filterOrder('{{ env('APP_URL') }}')"
                                        class="form-control select2">
                                        <option value="all" {{ request()->type == null ? 'selected' : '' }}>All</option>
                                        <option value="7" {{ request()->type == 7 ? 'selected' : '' }}>Not Assigned</option>
                                        <option value="5" {{ request()->type == 5 ? 'selected' : '' }}>Accepted</option>
                                        <option value="1" {{ request()->type == 1 ? 'selected' : '' }}>Accepted and Assigned
                                        </option>
                                        <option value="2" {{ request()->type == 2 ? 'selected' : '' }}>Out for Delivery
                                        </option>
                                        <option value="3" {{ request()->type == 3 ? 'selected' : '' }}>Delivered</option>
                                        <option value="0" {{ request()->type == '0' ? 'selected' : '' }}>Canceled by customer
                                        </option>
                                        <option value="6" {{ request()->type == 6 ? 'selected' : '' }}>Rejected by Vendor
                                        </option>
                                    </select>
                                @endcan --}}
                            </div>
                            {{-- <div class="col-sm-4">
                                <div class="form-group">
                                    <fieldset>
                                        <label for="recipePreTimede">Delivery Person Charge</label>
                                      <div class="input-group">
                                        <input type="number" class="form-control" step=".0000000000000001" value="{{ isset($shop->delivery_boy_charge) ? $shop->delivery_boy_charge : old('delivery_boy_charge') }}" name="delivery_charge" id="recipePreTime" placeholder="Delivery Charge" aria-describedby="recipePreTimede"  required>
                                        <div class="input-group-append">
                                        <span class="input-group-text" id="recipePreTimede">Per Order</span>
                                      </div>
                                    </div>
                                    </fieldset>
                                </div>
                            </div> --}}
                            @push('scripts')
                                <script>
                                    function filterOrder(url) {
                                        // var type = $('#filterByType').val();
                                        var shop = $('#filterByShop').val();
                                        window.location.href = url + 'paid-orders/' + shop + '/datas';
                                    }
                                </script>
                            @endpush
                            <div class="col-sm-4">
                                <div class="searchbar">
                                    <form>
                                        <label for=""></label>
                                        <div class="input-group">

                                            <input type="text" id="search" class="form-control" placeholder="Search">
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <hr>
                <div class="card-content">
                    <div class="card-body">
                        <!-- datatable start -->
                        <div class="table-responsive">
                            <table id="users-list-datatable" class="table zero-configuration">
                                <thead>
                                    <tr>
                                        <th>S.no</th>
                                        @role('admin')
                                            <th>Shop Name</th>
                                        @endrole
                                        <th>Order Referel</th>
                                        <th>Customer mobile</th>
                                        <th>Order Amount</th>
                                        <th>Status</th>
                                        <th>Order Type</th>
                                        <th style="white-space: nowrap">Delivered At</th>
                                        @can('view_orders')
                                            <th>Detail</th>
                                        @endcan

                                    </tr>
                                </thead>
                                <tbody>

                                    @if (isset($orders) && !empty($orders))
                                        @foreach ($orders as $k => $item)
                                            <tr>
                                                <td>{{ $orders->perPage() * ($orders->currentPage() - 1) + $k + 1 }}</td>
                                                @role('admin')
                                                    <td><a href="{{ route('shops.show', $item->shop->id) }}"
                                                            class="mr-1">{{ $item->shop->name }}</a></td>
                                                @endrole
                                                <td>{{ $item->prefix . $item->id }}</td>
                                                <td>{{ $item->user->mobile }}</td>
                                                <td>{{ $item->amount }} ₹</td>
                                                <td>{{ $item->order_state }}</td>
                                                <td>{{ $item->type == 0 ? 'COD' : 'Online' }}</td>
                                                <td>{{ $item->delivered_at }}</td>

                                                <td>
                                                    <a href="{{ route('orders.show', $item->id) }}"
                                                        class="mr-1">
                                                        <button type="submit" class="btn-outline-info"
                                                            data-icon="warning-alt">
                                                            <i class="bx bx-show"></i>
                                                        </button>
                                                    </a>
                                                </td>

                                            </tr>
                                        @endforeach
                                    @endif


                                </tbody>
                            </table>
                            <div class="mx-auto" style="width: fit-content">{{ $orders->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
        </div>

    </section>
@endsection
