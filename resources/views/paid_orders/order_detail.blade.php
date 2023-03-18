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
                                    <span class="invoice-number mr-50">Order referel :</span>
                                    <span>{{ $order->prefix . $order->id }}</span>
                                </div>
                                <div class="col-xl-8 col-md-12">
                                    <div class="d-flex align-items-center justify-content-xl-end flex-wrap">
                                        <div class="mr-3">
                                            <small class="text-muted">Ordered Date:</small>
                                            <span>{{ $order->confirmed_at->format('d-m-Y H:i') }}</span>
                                        </div>
                                        <div>
                                            <small class="text-muted">Delivery Date:</small>
                                            <span>{{ \Carbon\Carbon::parse($order->cart->scheduled_at)->format('d-m-Y') }}</span>
                                            <span>( {{ $order->cart->from }} - </span>
                                            <span>{{ $order->cart->to }} )</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- logo and title -->
                            <div class="row my-3">
                                <div class="col-md-4">
                                    <h4 class="text-primary">{{ $order->user->name }}</h4>
                                    <span>{{ $order->user->email }}</span><br>
                                    <span>{{ $order->user->mobile }}</span>
                                </div>
                                <div class="col-md-4 text-center">
                                    @if ($order->order_status == 7 || $order->order_status == 5)
                                        <a href="#"
                                            onclick="showReviewOrder('{{ $order->prefix }}', '{{ $order->id }}')">
                                            <button type="submit" class="btn btn-info" data-icon="warning-alt">
                                                Review
                                            </button>
                                        </a>
                                    @endif
                                </div>
                                <div class="col-md-4 d-flex justify-content-end">
                                    <img src="{{ $order->user->image }}" alt="" height="46" width="164">
                                </div>
                            </div>
                            <hr>
                            <!-- invoice address and contact -->
                            <div class="row invoice-info">
                                <div class="col-md-3 mt-1">
                                    <h6 class="invoice-from">Order From (Shop)</h6>
                                    <div class="___class_+?23___">
                                        <span>{{ $order->cart->shop->name }}</span>
                                    </div>
                                    <div class="___class_+?24___">
                                        <span>{{ $order->cart->shop->street }},</span>
                                        <span>{{ $order->cart->shop->area }}</span><br>
                                        <span>{{ $order->cart->shop->city }}</span><br>
                                    </div>
                                    {{-- <div class="">
                    <span>{{$order->cart->centre->user->email}}</span>
                  </div>
                  <div class="">
                    <span>{{$order->cart->centre->user->mobile}}</span>
                  </div> --}}
                                </div>
                                <div class="col-md-5 mt-1">
                                    <h6 class="invoice-from text-center">Order Instructions from Customer</h6>
                                    <div class="___class_+?27___">
                                        {{-- <span>{{$order->cart->instructions}}</span> --}}
                                        <textarea class="form-control" name="decription" id="productescription" rows="5"
                                            placeholder="Descipton"
                                            disabled>{{ isset($order->cart->instructions) ? $order->cart->instructions : 'No Instructions Provided' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-4 mt-1">
                                    <h6 class="invoice-to">Delivered to</h6>
                                    <?php $address = json_decode($order->address); ?>
                                    <div class="___class_+?31___">
                                        <span>{{ $address->address }}</span>
                                    </div>
                                    {{-- <div class="">
                    <span>{{$order->address->landmark}},</span>
                  </div> --}}
                                </div>
                            </div>
                            <hr>
                        </div>
                        <!-- product details table-->
                        <div class="invoice-product-details table-responsive mx-md-25 card-body">
                            <table class="table table-borderless mb-0 text-center">
                                <thead>
                                    <tr class="border-0">
                                        <th scope="col" class="font-weight-bolder">Item</th>
                                        <th scope="col" class="font-weight-bolder">Toppings</th>
                                        <th scope="col" class="font-weight-bolder">Cost</th>
                                        <th scope="col" class="font-weight-bolder">Qty</th>
                                        <th scope="col" class="text-right font-weight-bolder">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($order->cart->cartProduct as $item)
                                        <?php $product = json_decode($item->product_details);
                                        $stock = json_decode($item->stock_details); ?>
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>
                                                @if (isset($item->toppings) && $item->toppings !== 'null')
                                                    <?php $found = json_decode($item->toppings, true);

                                                    $arr = array_map(function ($entry) {
                                                        return $entry;
                                                    }, $found); ?>
                                                    @forelse ($arr as $a)
                                                        {{ $a['name'] . ' - ' . $a['price'] . ' ₹' ?? null }} <br>
                                                    @empty
                                                        NILL
                                                    @endforelse
                                                @else
                                                    NILL
                                                @endif
                                            </td>
                                            <td>{{ $stock->price }}</td>
                                            <td>{{ $item->count }}</td>
                                            <td class="text-primary text-right font-weight-bold">{{ $item->amount }}</td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td>No Items</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- invoice subtotal -->
                            <div class="card-body pt-0 mx-25">
                                <hr>
                                <div class="row justify-content-between">
                                    <div class="col-4 col-sm-4 mt-75">
                                        <div class="invoice-subtotal">
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">Payment status : </span>
                                                <?php $class = $order->paid == 0 ? 'danger' : 'success';
                                                $name = $order->paid == 0 ? 'Pending' : 'Paid';
                                                ?>
                                                <span
                                                    class="invoice-value text-{{ $class }}">{{ $name }}</span>
                                            </div>
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">Payment Type :</span>
                                                <span
                                                    class="invoice-value">{{ $order->type == 1 ? 'Online Payment' : 'Cash on Delivery' }}</span>
                                            </div>
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">Ven Accepted at :</span>
                                                <span
                                                    class="invoice-value">{{ $order->accepted_at == null ? (in_array($order->order_status, [1, 6, 0, 3]) ? 'Auto ' . \Carbon\Carbon::parse($order->confirmed_at)->format('(d-m-Y H:i)') : 'Not yet accepted') : $order->accepted_at }}</span>
                                            </div>
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">Ven Assigned at :</span>
                                                <span
                                                    class="invoice-value">{{ $order->assigned_at == null ? (in_array($order->order_status, [1, 6, 0, 3]) ? 'Auto ' . \Carbon\Carbon::parse($order->confirmed_at)->format('(d-m-Y H:i)') : 'Not yet assigned') : $order->assigned_at }}</span>
                                            </div>
                                            <br>
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">Delivered boy :</span>
                                                @if ($order->order_status == 3 && $order->delivered_by == null)
                                                    <span class="invoice-value">Manual Delivery</span>
                                                @else
                                                    <span
                                                        class="invoice-value">{{ isset($order->deliveredBy->name) ? $order->deliveredBy->name : 'Not yet Accepted!' }}</span>
                                                @endif
                                            </div>
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">D-Boy Accepted at :</span>
                                                <span
                                                    class="invoice-value">{{ isset($order->deliveredBy->id)
    ? \App\Delivery::where('order_id', $order->id)->where('user_id', $order->deliveredBy->id)->pluck('accepted_at')->first()
    : 'NILL' }}</span>
                                            </div>
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">D-Boy Picked at :</span>
                                                <span
                                                    class="invoice-value">{{ isset($order->picked_at) ? $order->picked_at : 'NILL' }}</span>
                                            </div>
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">Delivered at :</span>
                                                <span
                                                    class="invoice-value">{{ isset($order->delivered_at) ? $order->delivered_at : 'Not yet Delivered!' }}</span>
                                            </div>
                                            <h5 class="invoice-title mt-3">Delivery boy ratings</h5>
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">Customer Rating :</span>
                                                <span class="invoice-value text-success">{{ $order->customer_rating }}</span>
                                            </div>

                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <textarea class="form-control" name="description" id="productDescription"
                                                    rows="4" placeholder="No Comments"
                                                    disabled>{{ $order->customer_comment }}</textarea>
                                            </div>
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">Vendor Rating :</span>
                                                <span class="invoice-value text-success">{{ $order->vendor_rating }}</span>
                                            </div>

                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <textarea class="form-control" name="description" id="productDescription"
                                                    rows="4" placeholder="No Comments"
                                                    disabled>{{ $order->vendor_comment }}</textarea>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-4 col-sm-4 text-center">
                                        @if ($order->delivered_at == null && $order->canceled_at == null && $order->picked_at == null)
                                            <button class="btn btn-primary" id="manualDelivery">Delivery Manualy</button>
                                            <input type="hidden" name="" value="{{ $order->id }}" id="manualId">
                                        @endif
                                        @if ($order->canceled_at != null)
                                            <button type="button" class="btn btn-outline-none text-danger">Order Canceled
                                                at</button>
                                            <span>{{ \Carbon\Carbon::parse($order->canceled_at)->format('d-m-Y') }}</span>
                                            <fieldset class="form-group">
                                                <label for="productDescription">Cancel Reason</label>
                                                <textarea class="form-control" name="description" id="productDescription"
                                                    rows="2" placeholder="Descripton"
                                                    disabled>{{ isset($order->cancel_reason) ? $order->cancel_reason : 'No reason Provided' }}</textarea>
                                            </fieldset>
                                        @endif
                                        @if ($order->order_status == 6)
                                            <button type="button" class="btn btn-outline-none text-danger">Rejected by
                                                Vendor</button>
                                            <span>{{ \Carbon\Carbon::parse($order->rejected_at)->format('d-m-Y') }}</span>

                                        @endif
                                    </div>
                                    <div class="col-4 col-sm-4 d-flex justify-content-end  mt-75">
                                        <div class="invoice-subtotal">
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">Subtotal</span>
                                                <span class="invoice-value">{{ $order->cart->total_amount }} ₹</span>
                                            </div>
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">Coupon / Discount</span>
                                                <span class="invoice-value">{{ $order->cart->coupon_amount }} ₹</span>
                                            </div>
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">Delivery Charge</span>
                                                <span class="invoice-value">{{ $order->cart->delivery_charge }} ₹</span>
                                            </div>
                                            <hr>
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">Payable Total</span>
                                                <span
                                                    class="invoice-value font-weight-bolder text-success">{{ $order->amount }}
                                                    ₹</span>
                                            </div>
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">Shop Earnings</span>
                                                <?php $ta = str_replace(',', '', $order->cart->total_amount);
                                                $tc = str_replace(',', '', $order->comission); ?>
                                                <span class="invoice-value">{{ $ta - $tc }}
                                                    ₹</span>
                                            </div>
                                            <div class="invoice-calc d-flex justify-content-between mb-1">
                                                <span class="invoice-title">{{ config('app.name') }} commission</span>
                                                <span class="invoice-value">{{ $order->comission }} ₹</span>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <a href="{{ url()->previous() }}" class="mr-auto btn btn-light">Back</a>
                                        {{-- <button type="submit" class="btn btn-warning glow mb-1 mb-sm-0 mr-0 mr-sm-1">Next --}}
                                        {{-- </button> --}}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                @push('scripts')
                    <script>
                        $('#manualDelivery').on('click', function() {
                            var id = $('#manualId').val();
                            if (confirm('Make sure you got the payment. Are you sure to delivery this order manually?')) {
                                $.ajax({
                                    type: 'POST',
                                    url: "{{ route('manual.index') }}",
                                    data: {
                                        '_token': '{{ csrf_token() }}',
                                        id: id
                                    },
                                    success: function(response) {
                                        window.location.reload();
                                    }
                                })
                            }
                        })
                    </script>
                @endpush
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
        @include('order.order_review')
        @push('scripts')
            <script src="{{ asset('app-assets/js/scripts/pages/app-invoice.min.js') }}"></script>
        @endpush
    @endsection
