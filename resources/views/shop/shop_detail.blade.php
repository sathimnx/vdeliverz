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
                  <span>#5876432{{$shop->id}}</span>
                </div>
                <div class="col-xl-8 col-md-12">
                  <div class="d-flex align-items-center justify-content-xl-end flex-wrap">
                    <div class="mr-3">
                      <small class="text-muted">Created Date:</small>
                      <span>{{\Carbon\Carbon::parse($shop->created_at)->format('d-m-Y H:i')}}</span>
                    </div>
                    <div>
                      <small class="text-muted">Status:</small>
                      <span class="{{$shop->active == 1 ? 'text-success' : 'text-danger'}}">( {{$shop->active == 1 ? 'Active' : 'Inactive'}} - </span>
                      <span class="{{$shop->opened == 1 ? 'text-success' : 'text-danger'}}">{{$shop->opened == 1 ? 'Opened' : "Closed"}} )</span>
                    </div>
                  </div>
                </div>
              </div>
              <!-- logo and title -->
              <div class="row my-3">
                <div class="col-6">
                  <h4 class="text-primary">{{$shop->name}}</h4>
                  <span>{{$shop->email}}</span><br>
                  <span>{{$shop->mobile}}</span>
                </div>
                <div class="col-6 d-flex justify-content-end">
                  <img src="{{$shop->image}}" alt="" width="30%">
                </div>
              </div>
              <hr>
              <!-- invoice address and contact -->
              <div class="row invoice-info">
                <div class="col-md-4 mt-1">
                  <h6 class="invoice-from card-title">Shop Address</h6>
                  <div class="">
                    <span>{{$shop->name}}</span>
                  </div>
                  <div class="">
                    <span>{{$shop->street}},</span>
                    <span>{{$shop->area}},</span><br>
                    <span>{{$shop->city}}</span><br>
                  </div>
                  {{-- <div class="">
                    <span>{{$order->cart->centre->user->email}}</span>
                  </div>
                  <div class="">
                    <span>{{$order->cart->centre->user->mobile}}</span>
                  </div> --}}
                </div>
                  <div class="col-md-4 mt-1">
                      <h6 class="invoice-to card-title">Shop Timings</h6>
                      <div class="">
                          <span><b>Weekdays :</b> {{$shop->weekdays}}</span>
                      </div>
                      <div class="mt-1">
                          <span><b>Time :</b> {{$shop->open_time}} - {{$shop->close_time}}</span>
                      </div>
{{--                      <div class="">--}}
{{--                          <span>Mobile : {{$shop->user->mobile}}</span>--}}
{{--                      </div>--}}
                  </div>
                <div class="col-md-4 mt-1">
                  <h6 class="invoice-to card-title">Shop Vendor Details</h6>
                  <div class="">
                      <span><b>Name :</b> {{$shop->user->name}}</span>
                  </div>
                  <div class="">
                      <span><b>Email :</b> {{$shop->user->email}}</span>
                  </div>
                  <div class="">
                      <span><b>Mobile :</b> {{$shop->user->mobile}}</span>
                  </div>
                </div>
              </div>
              <hr>
            </div>
            <!-- product details table-->
            <div class="invoice-product-details table-responsive mx-md-25 card-body">
                <h4 class="text-center">Shop Products</h4>
              <table class="table table-borderless mb-0">
                <thead>
                  <tr class="border-0">
                      <th scope="col" class="card-title">Category</th>
                    <th scope="col" class="card-title">Product Name</th>
                    <th scope="col" class="card-title">Product Category</th>
                    <th scope="col" class="card-title">Variants Count</th>
                    <th scope="col" class="card-title">Status</th>
                    <th scope="col" class="card-title text-center">View</th>
                  </tr>
                </thead>
                <tbody>
                    @forelse ($products as $item)
                    <tr>
                        <td>{{$item->category->name}}</td>
                        <td>{{$item->name}}</td>
                        <td>{{$item->subCategory->name}}</td>
                        <td class="text-center">{{$item->stocks->count()}}</td>
                        <td>
                            <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                <input type="checkbox" class="custom-control-input" {{$item->active == 1 ? 'checked' : ''}}
                                  onchange="change_status('{{$item->id}}', 'products', '#customSwitchGlow{{$item->id}}', 'active');" id="customSwitchGlow{{$item->id}}">
                                <label class="custom-control-label" for="customSwitchGlow{{$item->id}}">
                                </label>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="{{route('products.show', $item->id)}}" class="mr-1">
                                <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                    <i class="bx bx-show"></i>
                                </button>
                            </a>
                            <a href="{{route('products.edit', $item->id)}}" class="mr-1">
                                <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                    <i class="bx bx-pencil"></i>
                                </button>
                            </a>
                        </td>
                      </tr>
                    @empty
                      <tr>
                          <td>No Items</td>
                      </tr>
                    @endforelse

                </tbody>
              </table>
            </div>

 <div class="invoice-product-details table-responsive mx-md-25 card-body">
     <h4 class="text-center">Shop Slots</h4>
                <table class="table table-borderless mb-0">
                  <thead>
                    <tr class="border-0 text-center">
                      <th scope="col" class="font-weight-bolder">Monday</th>
                      <th scope="col" class="font-weight-bolder">Tuesday</th>
                      <th scope="col" class="font-weight-bolder">Wednesday</th>
                      <th scope="col" class="font-weight-bolder">Thursday</th>
                      <th scope="col" class="font-weight-bolder">Friday</th>
                      <th scope="col" class="font-weight-bolder">Saturday</th>
                      <th scope="col" class="font-weight-bolder">Sunday</th>
                    </tr>
                  </thead>
                  <tbody>
                      <tr style="white-space: nowrap;">
                          <td >
                            @forelse ($monday as $item)
                            {{$item->from_time}} - {{$item->to_time}} <br>
                          @empty

                          @endforelse
                        </td>
                          <td>
                            @forelse ($tuesday as $item)
                            {{$item->from_time}} - {{$item->to_time}} <br>
                          @empty

                          @endforelse
                          </td>
                          <td>
                            @forelse ($wednesday as $item)
                            {{$item->from_time}} - {{$item->to_time}} <br>
                          @empty

                          @endforelse
                          </td>
                          <td class="text-center">
                            @forelse ($thursday as $item)
                            {{$item->from_time}} - {{$item->to_time}} <br>
                          @empty

                          @endforelse
                          </td>
                          <td class="text-center">
                            @forelse ($friday as $item)
                            {{$item->from_time}} - {{$item->to_time}} <br>
                          @empty

                          @endforelse
                          </td>
                          <td>
                            @forelse ($saturday as $item)
                            {{$item->from_time}} - {{$item->to_time}} <br>
                          @empty

                          @endforelse
                          </td>
                          <td>
                            @forelse ($sunday as $item)
                            {{$item->from_time}} - {{$item->to_time}} <br>
                          @empty

                          @endforelse
                          </td>
                        </tr>
                  </tbody>
                </table>
              </div>

            <!-- invoice subtotal -->
            <!-- invoice subtotal -->
            <div class="card-body pt-0 mx-25">
              <hr>
              <div class="row">
                <div class="col-4 col-xl-6 col-sm-4 mt-75">
                    <div class="invoice-subtotal">
                        <div class="invoice-calc d-flex justify-content-between">
                          <span class="invoice-title">Total Products : {{$shop->products_count}}</span>
                          {{-- <span class="invoice-value text-success">{{$shop->products_count}}</span> --}}
                        </div>
                        <a href="{{ url()->previous() }}" class="mr-auto mt-4 btn btn-light">Back</a>

                        {{-- <div class="invoice-calc d-flex justify-content-between">
                            <span class="invoice-title">Picked at :</span>
                            <span class="invoice-value">{{isset($order->picked_at) ? $order->picked_at : 'Not yet Picked!'}}</span>
                        </div> --}}
                        {{-- <div class="invoice-calc d-flex justify-content-between">
                            <span class="invoice-title">Delivered at :</span>
                            <span class="invoice-value">{{isset($order->delivered_at) ? $order->delivered_at : 'Not yet Delivered!'}}</span>
                        </div> --}}
                        {{-- <div class="invoice-calc d-flex justify-content-between">
                          <span class="invoice-title">Delivered boy :</span>
                          @if ($order->order_status == 3 && $order->delivered_by == null)
                          <span class="invoice-value">Manual Delivery</span>
                          @else
                          <span class="invoice-value">{{isset($order->deliveredBy->name) ? $order->delivery->deliveredBy->name : 'Not yet Assigned!'}}</span>
                          @endif
                        </div> --}}
                      </div>
                </div>
                <div class="col-4 col-xl-6 col-sm-4 d-flex justify-content-end mt-75">
                  <div class="invoice-subtotal">
                    <div class="invoice-calc d-flex justify-content-between">
                      <span class="invoice-title">Total Orders Delivered</span>
                      <span class="invoice-value">{{$shop->delivered_count}}</span>
                    </div>
                    <div class="invoice-calc d-flex justify-content-between">
                      <span class="invoice-title">Total Earnings</span>
                      <span class="invoice-value text-success">{{$shop->earnings}} ₹</span>
                    </div>
                    <div class="invoice-calc d-flex justify-content-between">
                      <span class="invoice-title">Total Canceled Orders</span>
                      <span class="invoice-value text-danger">{{$shop->canceled_count}}</span>
                    </div>
                    <div class="invoice-calc d-flex justify-content-between">
                      <span class="invoice-title">Shop Ratings</span>
                      <span class="invoice-value">{{$shop->rating_avg}}</span>
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
