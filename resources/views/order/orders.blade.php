@extends('layouts.main')

@section('content')
    <section class="users-list-wrapper">
<meta name="csrf-token" content="{{ csrf_token() }}" />
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
                                @can('view_orders')
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
                                @endcan
                            </div>
                        
                            @push('scripts')
                                <script>
                                    function filterOrder(url) {
                                        var type = $('#filterByType').val();
                                        var shop = $('#filterByShop').val();
                                        window.location.href = url + 'orders_filter/'+ shop + '/' + type ;
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
                                        <th>Name</th>
                                        <th>Contact Number</th>
                                        <th>Order Amount</th>
                                        <th style="white-space: nowrap">Delivery Boy</th>
                                        <th>Status</th>
                                        <th>Order Type</th>
                                        <th style="white-space: nowrap">Delivered At</th>
                                        <th>Actions</th>
                                     {{--   @can('view_orders')
                                            <th>Detail</th>
                                        @endcan  --}}

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
                                                <td>{{ $item->user->name }}</td>
                                                <td>{{ $item->user->mobile }}</td>
                                                <td>{{ $item->amount  }} ₹</td>
                                                <td>{{ $item->deliveredBy->name ?? '' }}</td>
                                                <td>{{ $item->order_state }}</td>
                                                <td>{{ $item->type == 0 ? 'COD' : 'Online' }}</td>
                                                <td>{{ $item->delivered_at }}</td>
                                                 {{-- <td>
                                                  @if ($item->order_status == 7 || $item->order_status == 5)
                                                        <a href="#"
                                                            onclick="showReviewOrder('{{ $item->prefix }}', '{{ $item->id }}')">
                                                            <button type="submit" class="btn-outline-info"
                                                                data-icon="warning-alt">
                                                                Review
                                                            </button>
                                                        </a>
                                                    @endif
                                                </td> --}}
                                                <td  style="width: 8%;">
                                                    <a href="{{ route('orders.show', $item->id) }}"
                                                        class="mr-1">
                                                        <button type="submit" class="btn-outline-info"
                                                            data-icon="warning-alt">
                                                            <i class="bx bx-show"></i>
                                                        </button>
                                                    </a>
                                                        <a >
                                                     <button type="submit" class="btn-outline-info" onclick="show_change_Statusmodalpopup({{ $item->id}},{{ $item->order_status }})" data-icon="warning-alt">
                                                        <i class="bx bx-edit"></i>
                                                    </button>
                                                    </a>
                                                    
                                                      <a >
                                                     <button type="submit" class="btn-outline-danger" onclick="delete_orders({{ $item->id}})">
                                                            <i class="bx bx-trash-alt"></i>
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
            <div class="modal" tabindex="-1" role="dialog" id="dvChangeOrderStatus">
                              <div class="modal-dialog" role="document">
                                 <div class="modal-content">
                                    <div class="alert alert-danger" style="display:none"></div>
                                    <div class="modal-header">
                                       <h5 class="modal-title">
                                           Change Order Status</h5>
                                       <button type="button" class="close" id="close_addVehicleModal" name="close_addVehicleModal" data-dismiss="modal" aria-label="frame_Close">
                                       <span aria-hidden="true">&times;</span>
                                       </button>
                                    </div>
                                    <div class="modal-body">
                                       <div class="row">
                                          <div class="form-group col-md-12">
                                              <input type="hidden" class="form-control" name="orderId" id="orderId">
                                            <label for="Vehicle_Type">Change Order Status:</label>
                                            
                                                 <select class="form-control" id="order_status" name="Orderstatus" data-placeholder="Select Status...">
                                                    <option value="">Choose Status</option>                                                                           
                                                    <option value="3">Delivered</option>
                                                    <option value="0">Cancelled</option>
                                                    <option value="1">Accepted and Assigned</option>
                                                    <option value="2">Out for delivery</option>
                                                    <option value="4">Yet to Pick</option>
                                                    <option value="5">Accepted</option>
                                                    <option value="6">Rejected by Vendor</option>
                                                    <option value="7">Not Assigned</option>

                                                </select>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="modal-footer">
                                       <button  class="btn btn-success" id="ajaxSubmit" onclick="changeStatus()">Save changes</button>
                                    </div>
                                 </div>
                              </div>
                           </div>
        </div>
        </div>
        @include('order.order_review')

    </section>
    <script>
         function show_change_Statusmodalpopup(orderId,OrderStatus){
             if(OrderStatus != 0 && OrderStatus != 3)
             {
                $('#dvChangeOrderStatus').modal('show'); 
                $('#orderId').val(orderId);
             }
            
         }
         function changeStatus()
         {
             var changed_status = $('#order_status').val();
             var orderId = $('#orderId').val();
             $.ajax({
               type:'POST',
               url:"{{ route('changeOrderStatus') }}",
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
               data:{order_id:orderId,order_status:changed_status},
               success: function(data) {
                 alert('Status Changed');
                 $('#dvChangeOrderStatus').modal('hide'); 
                 window.location.reload();
               }
		     });
         }
         function delete_orders(orderId)
         {
             alert(orderId);
             var result = confirm("Are you sure want to delete this order?");
            if (result) {
                 $.ajax({
                   type:'POST',
                   url:"{{ route('delete_orders') }}",
                   headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                   data:{order_id:orderId},
                   success: function(data) {
                      alert('Deleted Successfully');
                      window.location.reload();
                   }
    		     });
            }
         }
    </script>
@endsection
