@extends('layouts.main')

@section('content')
    @push('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/app-invoice.min.css') }}">
        <style>
            td {
                white-space: nowrap;
            }

        </style>
    @endpush
    <section class="invoice-view-wrapper">
        <div class="row">
            <!-- invoice view page -->
            <div class="col-xl-12 col-md-12 col-12">
                <div class="card invoice-print-area">
                    <div class="card-content">
                        <div class="card-body pb-0 mx-25">
                            <!-- header section -->
                            <div class="row">
                                <div class="col-xl-4 col-md-12">
                                    <span class="invoice-number mr-50">Shop referel no :</span>
                                    <span>#5876432{{ $shop->id }}</span>
                                </div>
                                <div class="col-xl-8 col-md-12">
                                    <div class="d-flex align-items-center justify-content-xl-end flex-wrap">
                                        <div class="mr-3">
                                            <small class="text-muted">Created Date:</small>
                                            <span>{{ \Carbon\Carbon::parse($shop->created_at)->format('d-m-Y H:i') }}</span>
                                        </div>
                                        <div>
                                            <small class="text-muted">Status:</small>
                                            <span class="{{ $shop->active == 1 ? 'text-success' : 'text-danger' }}">(
                                                {{ $shop->active == 1 ? 'Active' : 'Inactive' }} - </span>
                                            <span
                                                class="{{ $shop->opened == 1 ? 'text-success' : 'text-danger' }}">{{ $shop->opened == 1 ? 'Opened' : 'Closed' }}
                                                )</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- logo and title -->
                            <div class="row my-3">
                                <div class="col-6">
                                    <h4 class="text-primary">{{ $shop->name }}</h4>
                                    <span>{{ $shop->email }}</span><br>
                                    <span>{{ $shop->mobile }}</span>
                                </div>
                                <div class="col-6 d-flex justify-content-end">
                                    <img src="{{ $shop->image }}" alt="" width="30%">
                                </div>
                            </div>
                            <hr>
                            <!-- invoice address and contact -->
                            <div class="row invoice-info">
                                <div class="col-6 mt-1">
                                    <h6 class="invoice-from card-title">Shop Address</h6>
                                    <div class="">
                                        <span>{{ $shop->name }}</span>
                                    </div>
                                    <div class="">
                                        <span>{{ $shop->street }},</span>
                                        <span>{{ $shop->area }},</span><br>
                                        <span>{{ $shop->city }}</span><br>
                                    </div>
                                    {{-- <div class="">
                    <span>{{$order->cart->centre->user->email}}</span>
                  </div>
                  <div class="">
                    <span>{{$order->cart->centre->user->mobile}}</span>
                  </div> --}}
                                </div>
                                <div class="          col-6 mt-1">
                                    <h6 class="invoice-to card-title">Shop Vendor Details</h6>
                                    <div class="">
                                        <span>Name : {{ $shop->user->name }}</span>
                                    </div>
                                    <div class="">
                                        <span>Email : {{ $shop->user->email }}</span>
                                    </div>
                                    <div class="          ">
                                        <span>Mobile : {{ $shop->user->mobile }}</span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <!-- product details table-->
                        <div class="invoice-product-details
                                            table-responsive mx-md-25 card-body"
                            id="orderDetailsScroll">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="text-success">Paid Orders List</h4>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('sales.show', $shop) }}" class="btn btn-danger float-right">Show
                                        Pending
                                        Orders</a>
                                </div>
                            </div>
                            <table class="table table-borderless mb-0">
                                <thead>
                                    <tr class="border-0">
                                        <th scope="col" class="card-title">Order ID</th>
                                        {{-- <th scope="col" class="card-title">Order Status</th> --}}
                                        <th scope="col" class="card-title">Customer</th>
                                        <th scope="col" class="card-title">Prod Count</th>
                                        <th scope="col" class="card-title">Amount</th>
                                        <th scope="col" class="card-title">Commission</th>
                                        <th scope="col" class="card-title">Del Charge</th>
                                        <th scope="col" class="card-title">Discount</th>
                                        <th scope="col" class="card-title">Shop Earnings</th>
                                        {{-- <th scope="col" class="card-title">Paid</th> --}}
                                    </tr>
                                </thead>
                                <tbody id="salesDetailTable">
                                    @forelse ($orders as $item)
                                        <tr>
                                            <td><a href="{{ route('orders.show', $item->id) }}"
                                                    class="mr-1">{{ $item->prefix . $item->id }}</a></td>
                                            {{-- <td class="@if ($item->order_state == 'Canceled') text-danger @endif">{{ $item->order_state }}
                                            </td> --}}
                                            <td>{{ isset($item->user->mobile) ? $item->user->mobile : $item->user->email }}
                                            </td>
                                            <td class="text-center">{{ $item->cart->products_count }}
                                            </td>
                                            <td class="text-center">{{ $item->cart->total_amount }} ₹</td>
                                            <td class="text-center">{{ $item->comission }} ₹</td>
                                            <td class="text-center">{{ $item->cart->delivery_charge }} ₹</td>
                                            <td class="text-center">{{ $item->cart->coupon_amount }} ₹</td>
                                            <td class="text-center">{{ $item->shop_earned }} ₹</td>
                                            {{-- <td
                                                class="text-center {{ $item->pay_status ? 'text-success' : 'text-danger' }}">
                                                {{ $item->pay_status ? 'Paid' : 'Pending' }} </td> --}}

                                        </tr>
                                    @empty

                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                        <div class="ajax-load text-center" style="display:none">
                            <p><img src="">Loading More Orders</p>x
                        </div>

                        @push('scripts')
                            <script>
                                var page = 1;
                                $(window).scroll(function() {
                                    if ($(window).scrollTop() + $(window).height() >= $(document).height() - 250) {
                                        page++;
                                        loadMoreData(page);
                                    }
                                });

                                function loadMoreData(page) {
                                    $.ajax({
                                            url: '?page=' + page,
                                            type: "get",
                                            beforeSend: function() {
                                                $('.ajax-load').show();
                                            }
                                        })
                                        .done(function(data) {
                                            if (data.html == "") {
                                                $('.ajax-load').html("No more Orders found");
                                                return;
                                            }
                                            $('.ajax-load').hide();
                                            $("#salesDetailTable").append(data.html);
                                        })
                                        .fail(function(jqXHR, ajaxOptions, thrownError) {
                                            // alert('server not responding...');
                                        });
                                }
                            </script>
                        @endpush
                        <!-- invoice subtotal -->
                        <div class="card-body pt-0 mx-25">
                            <hr>
                            <div class="row">
                                <div class="col-4 col-xl-4 col-sm-4 mt-75">
                                    <div class="invoice-subtotal">
                                        <div class="invoice-calc d-flex justify-content-between">
                                            <span class="invoice-title">Total Products :
                                                {{ $shop->products_count }}</span>
                                            {{-- <span class="invoice-value text-success">{{$shop->products_count}}</span> --}}
                                        </div>

                                    </div>
                                </div>
                                <div class="col-4 col-xl-4 col-sm-4 d-flex justify-content-between mt-75">
                                    <div class="invoice-subtotal w-75">
                                        <div class="invoice-calc d-flex justify-content-between ">
                                            <span class="invoice-title">Total Orders Delivered</span>
                                            <span class="invoice-value">{{ $shop->delivered_count }}</span>
                                        </div>
                                        <hr>
                                        <div class="invoice-calc d-flex justify-content-between">
                                            <span class="invoice-title">Total Earnings</span>
                                            <span class="invoice-value text-success">{{ round($shop->earnings, 2) }}
                                                ₹</span>
                                        </div>
                                        <hr>

                                        <div class="invoice-calc d-flex justify-content-between">
                                            <span class="invoice-title">Shop Earnings</span>
                                            <span class="invoice-value text-success">{{ round($shop->shop_earnings, 2) }}
                                                ₹</span>
                                        </div>
                                        <hr>

                                        <div class="invoice-calc d-flex justify-content-between">
                                            <span class="invoice-title">{{ config('app.name') }}
                                                Commission</span>
                                            <span
                                                class="invoice-value text-success">{{ round($shop->commission_earnings, 2) }}
                                                ₹</span>
                                        </div>
                                        <hr>
                                        <div class="invoice-calc d-flex justify-content-between">
                                            <span class="invoice-title">{{ config('app.name') }}
                                                Delivery Charges</span>
                                            <span class="invoice-value text-success">{{ $shop->delivery_charges }}
                                                ₹</span>
                                        </div>
                                        <hr>
                                        <div class="invoice-calc d-flex justify-content-between">
                                            <span class="invoice-title">{{ config('app.name') }}
                                                Discounts</span>
                                            <span class="invoice-value text-success">{{ $shop->total_discount }}
                                                ₹</span>
                                        </div>
                                        <hr>
                                        <div class="invoice-calc d-flex justify-content-between">
                                            <span class="invoice-title">
                                                Amount Paid to Shop</span>
                                            <span class="invoice-value text-success">{{ $shop->paid_earnings }}
                                                ₹</span>
                                        </div>
                                        <hr>
                                        <div class="invoice-calc d-flex justify-content-between">
                                            <span class="invoice-title text-danger">
                                                Pending Amount</span>
                                            <span class="invoice-value text-danger">{{ $shop->pending_earnings }}
                                                ₹</span>
                                        </div>
                                        <hr>
                                        <div class="invoice-calc d-flex justify-content-between">
                                            <span class="invoice-title">Total Canceled Orders</span>
                                            <span class="invoice-value text-danger">{{ $shop->canceled_count }}</span>
                                        </div>
                                        <hr>
                                        <div class="invoice-calc d-flex justify-content-between">
                                            <span class="invoice-title">Shop Ratings</span>
                                            <span class="invoice-value">{{ $shop->rating_avg }}</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- invoice action  -->
            <div class="col-xl-3 col-md-4 col-12 justify-content-center">
                <div class="card invoice-action-wrapper shadow-none border">
                    <div class="card-body">
                        {{-- <div class="invoice-action-btn">
              <button class="btn btn-primary btn-block invoice-send-btn">
                <i class="bx bx-send"></i>
                <span>Send Invoice</span>
              </button>
            </div> --}}
                        <div class="invoice-action-btn">
                            <button class="btn btn-light-primary btn-block invoice-print">
                                <span>print</span>
                            </button>
                        </div>
                        {{-- <div class="invoice-action-btn">
              <a href="app-invoice-edit.html" class="btn btn-light-primary btn-block">
                <span>Edit Invoice</span>
              </a>
            </div> --}}
                        {{-- <div class="invoice-action-btn">
              <button class="btn btn-success btn-block">
                <i class='bx bx-dollar'></i>
                <span>Add Payment</span>
              </button>
            </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="{{ asset('app-assets/js/scripts/pages/app-invoice.min.js') }}"></script>
    @endpush
@endsection
