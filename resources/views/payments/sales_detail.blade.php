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
                                    <div class="___class_+?23___">
                                        <span>{{ $shop->name }}</span>
                                    </div>
                                    <div class="___class_+?24___">
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
                                <div class="col-6 mt-1">
                                    <h6 class="invoice-to card-title">Shop Vendor Details</h6>
                                    <div class="___class_+?27___">
                                        <span>Name : {{ $shop->user->name }}</span>
                                    </div>
                                    <div class="___class_+?28___">
                                        <span>Email : {{ $shop->user->email }}</span>
                                    </div>
                                    <div class="___class_+?29___">
                                        <span>Mobile : {{ $shop->user->mobile }}</span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <!-- product details table-->
                        <div class="invoice-product-details table-responsive mx-md-25 card-body" id="orderDetailsScroll">
                            <table class="table table-borderless mb-0">
                                <thead>
                                    <tr class="border-0 text-center">
                                        <th scope="col" class="card-title">Order ID</th>
                                        <th scope="col" class="card-title">Order Status</th>
                                        <th scope="col" class="card-title">Payment Type</th>
                                        <th scope="col" class="card-title">Total</th>
                                        <th scope="col" class="card-title">Delivery Charge</th>
                                        <th scope="col" class="card-title">Shop Earnings</th>
                                        <th scope="col" class="card-title">Commission</th>
                                    </tr>
                                </thead>
                                <tbody id="salesDetailTable">
                                    @forelse ($orders as $item)
                                        <tr class="text-center">
                                            <td><a
                                                    href="{{ route('orders.show', $item->id) }}">{{ $item->prefix . $item->id }}</a>
                                            </td>
                                            <td class="@if ($item->order_state == 'Canceled') text-danger @endif">{{ $item->order_state }}</td>
                                            <td>{{ $item->type == 1 ? 'Online Payment' : 'COD' }}</td>
                                            <td>{{ $item->amount }} ₹</td>
                                            <td>{{ $item->cart->delivery_charge }} ₹</td>
                                            <td>{{ str_replace(',', '', $item->cart->total_amount) - str_replace(',', '', $item->comission) }}
                                                ₹</td>
                                            <td>{{ $item->comission }} ₹</td>

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
                            <div class="row">

                                <div class="col-md-3 col-xl-3 col-sm-4 d-flex justify-content-start mt-75">
                                    <div class="invoice-subtotal">
                                        <div class="invoice-calc d-flex justify-content-between mb-2">
                                            <span class="invoice-title">Total Orders</span>
                                            <span class="invoice-value">{{ $total_orders }}</span>
                                        </div>
                                        {{-- <div class="invoice-calc d-flex justify-content-between">
                      <span class="invoice-title">Total Earnings</span>
                      <span class="invoice-value text-success">{{$paymnet->tot_amt}} ₹</span>
                    </div> --}}
                                        <div class="invoice-calc d-flex justify-content-between mb-2">
                                            <span class="invoice-title">Shop Earnings</span>
                                            <span class="invoice-value text-success">{{ $shop_total }} ₹</span>
                                        </div>
                                        <div class="invoice-calc d-flex justify-content-between">
                                            <span class="invoice-title">{{ config('app.name') }} Commission</span>
                                            <span class="invoice-value text-success">{{ $commission }} ₹</span>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-md-9 col-sm-8 mt-75">
                                    @if ($orders->count() <= 0)
                                        <h4 class="text-danger">No Pending Payments</h4>
                                    @endif
                                    @if (request('status') !== 0 &&
        $orders->count() > 0 &&
        auth()->user()->hasAnyRole('admin'))

                                        <form action="{{ route('payments.update', request('shop')) }}" method="post">
                                            @method('POST')
                                            @csrf
                                            <input type="hidden" class="form-control" name="order_id" id="modal_order_id">

                                            <div class="row">
                                                <div class="col-md-6 col-12 form-group">
                                                    <div class="mb-3">
                                                        <button class="btn btn-icon rounded-circle btn-primary"
                                                            data-target="#addnewBank" data-toggle="modal" type="button">
                                                            <i class="bx bx-plus" style="vertical-align: 0;"></i>
                                                        </button>
                                                        <span class="ml-1 font-weight-bold text-primary">ADD NEW BANK</span>
                                                    </div>
                                                    <h4>Choose bank </h4>
                                                    <div style="height:200px; overflow:auto">
                                                        @forelse ($banks as $bank)
                                                            <fieldset class="mb-2">
                                                                <div class="radio radio-info radio-glow">
                                                                    <input type="radio" id="action{{ $bank->id }}"
                                                                        value="{{ $bank->id }}" name="bank"
                                                                        @if ($loop->first)
                                                                    checked
                                                        @endif
                                                        >
                                                        <label for="action{{ $bank->id }}">{{ $bank->bank_name }}
                                                            <br> {{ $bank->acc_no }}</label>
                                                    </div>
                                                    </fieldset>
                                                @empty
                                                    <h6 class="text-danger">Please add bank account to proceed</h6>
                                    @endforelse
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="contact-repeater" id="dynamicReviewOrder">
                                            <div class="row justify-content-between">
                                                <div class="col-md-6 col-12 form-group">
                                                    <fieldset>
                                                        <div class="radio radio-info radio-glow">
                                                            <input type="radio" id="action1" value="2" name="action"
                                                                checked>
                                                            <label for="action1">Accept Payment</label>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-6 col-12 form-group">
                                                    <fieldset>
                                                        <div class="radio radio-info radio-glow">
                                                            <input type="radio" id="action2" value="3" name="action">
                                                            <label for="action2">Complete Payment</label>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="">Add Instructions</label>
                                                    <textarea name="instruction" id="" cols="30" rows="3"
                                                        class="form-control"></textarea>
                                                </div>
                                                <button type="submit"
                                                    class="btn btn-primary mt-2 mx-auto d-block">Submit</button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        </form>
                        @endif
                        @if (request('status') == 0 &&
        $orders->count() > 0 &&
        auth()->user()->hasAnyRole('vendor'))

                            <form action="{{ route('payments.update', request('shop')) }}" method="post">
                                @method('POST')
                                @csrf
                                <div class="row">

                                    <div class="col-md-6 col-12 form-group">
                                        <div class="mb-3">
                                            <button class="btn btn-icon rounded-circle btn-primary"
                                                data-target="#addnewBank" data-toggle="modal" type="button">
                                                <i class="bx bx-plus" style="vertical-align: 0;"></i>
                                            </button>
                                            <span class="ml-1 font-weight-bold text-primary">ADD NEW BANK</span>
                                        </div>
                                        <h4>Choose bank </h4>
                                        <div style="height:200px; overflow:auto">
                                            @forelse ($banks as $bank)
                                                <fieldset class="mb-2">
                                                    <div class="radio radio-info radio-glow">
                                                        <input type="radio" id="action{{ $bank->id }}"
                                                            value="{{ $bank->id }}" name="bank" @if ($loop->first)
                                                        checked
                                            @endif
                                            >
                                            <label for="action{{ $bank->id }}">{{ $bank->bank_name }}
                                                <br> {{ $bank->acc_no }}</label>
                                        </div>
                                        </fieldset>
                                    @empty
                                        <h6 class="text-danger">Please add bank account to proceed</h6>
                        @endforelse
                    </div>

                </div>
                <div class="col-md-6">
                    <label for="">Add Instructions</label>
                    <textarea name="instruction" id="" cols="30" rows="5" class="form-control"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-2 mx-auto d-block">Request
                Payment</button>
            </form>
            @endif
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
    @include('payments.addnewBank')

    @push('scripts')
        <script src="{{ asset('app-assets/js/scripts/pages/app-invoice.min.js') }}"></script>
    @endpush
@endsection
