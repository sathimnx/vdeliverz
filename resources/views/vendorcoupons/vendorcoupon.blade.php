@extends('layouts.main')

@section('content')


    <!-- Scroll - horizontal and vertical table -->
    {{-- <h5><b>Store</b></h5> <br /> --}}
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <p class="card-text">
                        <h5 class="d-block card-text text-center mb-3">Total Coupons - {{ $coupons->total() }}</h5>
                        <div class="row">

                            <div class="col-sm-6">
                               
                                    <a href="{{ route('vendorcoupons.create') }}" class="btn btn-warning float-left"
                                        class="btn btn-primary">Create Coupon</a>
                             

                            </div>
                            <div class="col-sm-6">
                                <div class="searchbar">
                                    <form>
                                        <div class="input-group">
                                            <input type="text" id="search" class="form-control" placeholder="Search">
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                        </p>
                    </div>
                    <hr>
                    <div class="card-content">
                        <div class="card-body card-dashboard">

                            <div class="table-responsive">
                                <table class="table zero-configuration" style="width:100%;">
                                    <thead>
                                        <tr>
                                            {{-- <th><input type="checkbox" id="master"></th> --}}
                                            <th>S.No</th>
                                            <th>Coupon Code</th>
                                             <th>Max Amount</th>
                                            <th>Min Amount</th>
                                            <th>No.of times users can use coupon</th>
                                            <th>Shop</th>
                                            <th>Category</th>
                                            <th>Sub Category</th>
                                            <th>Product</th>
                                            <th>Coupon Percentage</th>
                                            <th style="white-space: nowrap">&nbsp;&nbsp;Offer Ends&nbsp;&nbsp;</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th style="white-space: nowrap;">&nbsp;&nbsp;Created at&nbsp;&nbsp;</th>
                                              
                                                <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($coupons) && !empty($coupons))
                                            @foreach ($coupons as $k => $item)
                                           
                                                <tr>
                                                    {{-- <td><input type="checkbox" id="master"></td> --}}
                                                    <td>{{ $k + $coupons->firstItem() }}</td>
                                                    <td>{{ $item->coupon_code }}</td>
                                                   <td>{{ $item->max_order_amount }}</td>
                                                    <td>{{ $item->min_order_amt }}</td>
                                                    <td>{{ $item->Discount_use_amt }}</td>
                                                    <td>  @if (isset($item->shop_name)) 
                                                            {{ $item->shop_name }}
                                                          @endif
                                                        </td>
                                                    <td>  @if (isset($item->Category->name)) 
                                                        {{ $item->Category->name }}
                                                      @endif
                                                    </td>   
                                                    <td>  @if (isset($item->sub_category_name)) 
                                                            {{ $item->sub_category_name }}
                                                          @endif
                                                        </td>
                                                    <td>  @if (isset($item->product_name)) 

                                                            {{ $item->product_name }}
                                                          @endif
                                                        </td>
                                                   
                                                    <td>{{ $item->coupon_percentage }}%</td>
                                                    <td>{{ date('d-M-Y H:i:s', strtotime($item->expired_on)) }}</td>
                                                    <td style="max-width: 100px; cursor: pointer;
                                                    overflow: hidden;
                                                    text-overflow: ellipsis;
                                                    white-space: nowrap;" data-toggle="tooltip" data-placement="top"
                                                        title="{{ $item->coupon_description }}">
                                                        {{ $item->coupon_description }}</td>
                                                    <td>
                                                        <div
                                                            class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input"
                                                                {{ $item->active == 1 ? 'checked' : '' }} value=""
                                                                onchange="change_status('{{ $item->id }}', 'vendor_coupons', '#customSwitchGlow{{ $k }}', 'active');"
                                                                id="customSwitchGlow{{ $k }}">
                                                            <label class="custom-control-label"
                                                                for="customSwitchGlow{{ $k }}">
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <?php $created_at = date('d-M-Y H:i:s', strtotime($item->created_at)); ?>
                                                    <td>{{ $created_at }}</td>
                                                   <td>
                                                    <a href="{{route('vendorcoupons.edit', $item->id)}}">
                                                                    <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                                                <i class="bx bx-edit-alt"></i>
                                                            </button>
                                                            </a>
                                                              <button type="submit" class="btn-outline-info" onclick="destroyvendorCoupons({{$item->id}})"
                                                            data-icon="warning-alt">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                       </td>


                                                </tr>
                                            @endforeach

                                        @endif
                                    </tbody>
                                </table>
                                <div class="mx-auto" style="width: fit-content">{{ $coupons->links() }}</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Scroll - horizontal and vertical table -->

    <!-- // Basic Floating Label Form section start -->
    <!-- Button trigger modal -->


    @push('scripts')

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
 <script>
 function destroyvendorCoupons(id)
 { 
      var result = confirm("Are you sure want to delete this coupon?");
    if (result) {
      $.ajax({
        type:'POST',
        url:"{{ route('deletevendorCoupons') }}",
        data: {
           
        "_token": "{{ csrf_token() }}","coupon_id":id
        },
        success: function(data) {
            alert('Coupon details removed successfully');
            window.location.reload();
        }
     });
    }
 }
 </script>
    @endpush

@endsection
