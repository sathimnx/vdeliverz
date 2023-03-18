@extends('layouts.main')

@section('content')
    @push('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/app-invoice.min.css') }}">
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
                                    {{-- <span class="invoice-number mr-50">Order referel :</span>
                  <span>{{$order->prefix.$order->id}}</span> --}}
                                </div>
                                <div class="col-xl-8 col-md-12">
                                    <div class="d-flex align-items-center justify-content-xl-end flex-wrap">
                                        <div class="mr-3">
                                            <small class="text-muted">Created at:</small>
                                            <span>{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y H:i') }}</span>
                                        </div>
                                        <div>
                                            {{-- <small class="text-muted">Delivery Date:</small>
                      <span>{{\Carbon\Carbon::parse($order->cart->sheduled_at)->format('d-m-Y')}}</span>
                      <span>( {{$order->cart->from}} - </span>
                      <span>{{$order->cart->to}} )</span> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- logo and title -->
                            <div class="row my-3">
                                <div class="col-4">
                                    <h4 class="text-primary">{{ $user->name }} <span
                                            class="{{ $order ? 'text-danger' : 'text-success' }}">(
                                            {{ $order ? 'Not Available' : 'Available' }} )</span>
                                    </h4>
                                    <span>{{ $user->email }}</span><br>
                                    <span>{{ $user->mobile }}</span>
                                    <span class="mt-2 d-block text-success"
                                        style="font-size: 1.5rem">{{ $user->rating_avg }} <i class="bx bxs-star"
                                            style="font-size: 1rem"></i></span>
                                    <span class="d-block">{{ $user->rating_count }} reviews</span>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-dark text-center">Ongoing Order <br><br>
                                        @if ($order)
                                            <a href="{{ route('orders.show', $order->id) }}"><span
                                                    class="text-success">{{ $order->search }}</span></a>
                                        @else
                                            <span class="text-success">NILL</span>
                                        @endif
                                    </h4>
                                </div>
                                <div class="col-4 d-flex justify-content-end">
                                    <img src="{{ $user->image }}" alt="" style="height: auto" width="164">
                                </div>
                            </div>
                            <hr>

                            <hr>
                        </div>
                        <!-- product details table-->
                        <div class="invoice-product-details table-responsive mx-md-25 card-body">
                            <table class="table table-borderless mb-0 text-center">
                                <thead>
                                    <tr class="border-0">
                                        <th scope="col" class="font-weight-bolder">Order Id</th>
                                        <th scope="col" class="font-weight-bolder">Accepted At</th>
                                        <th scope="col" class="font-weight-bolder">Status</th>
                                        <th scope="col" class="font-weight-bolder">Distance</th>
                                        <th scope="col" class="font-weight-bolder">Rating</th>
                                        <th scope="col" class="font-weight-bolder">Comment</th>
                                        {{-- <th scope="col" class="text-right font-weight-bolder">Price</th> --}}
                                    </tr>
                                </thead>
                                <tbody id="salesDetailTable">
                                    @forelse ($deliveries as $item)
                                        <tr>
                                            <td><a
                                                    href="{{ route('orders.show', $item->order_id) }}">{{ $item->order->search }}</a>
                                            </td>
                                            <td>{{ $item->accepted_at }}</td>
                                            <td class="{{ $item->status == 3 ? 'text-success' : 'text-danger' }}">
                                                {{ $item->delivery_status }}</td>
                                            <td>{{ $item->order->kms }} Kms</td>
                                            <td>{{ $item->order->customer_rating }}</td>
                                            <td>{{ $item->order->customer_comment }}</td>
                                            {{-- <td class="text-primary text-right font-weight-bold">{{$item->amount}}</td> --}}
                                        </tr>
                                    @empty

                                    @endforelse


                                </tbody>
                            </table>
                        </div>
                        <div class="ajax-load text-center" style="display:none">
                            <p><img src="">Loading More Orders</p>x
                        </div>

                        <!-- invoice subtotal -->
                        <div class="card-body pt-0 mx-25">
                            <hr>
                            <div class="row">
                                <div class="col-4 col-sm-4 d-flex justify-content-end  mt-75">
                                    <div class="invoice-subtotal">
                                        <div class="invoice-calc d-flex justify-content-between mb-1">
                                            <span class="invoice-title">Orders Accepted</span>
                                            <span class="invoice-value">{{ $user->deliveries->count() }}</span>
                                        </div>
                                        <div class="invoice-calc d-flex justify-content-between mb-1">
                                            <span class="invoice-title">Orders Delivered</span>
                                            <span class="invoice-value">{{ $user->delivered_count }}</span>
                                        </div>
                                        <div class="invoice-calc d-flex justify-content-between mb-1 text-danger">
                                            <span class="invoice-title">Orders Canceled</span>
                                            <span class="invoice-value text-danger">{{ $user->canceled_count }}</span>
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
                        alert('No more Orders');
                    });
            }
        </script>
        <script src="{{ asset('app-assets/js/scripts/pages/app-invoice.min.js') }}"></script>
    @endpush
@endsection
