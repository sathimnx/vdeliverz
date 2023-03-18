@extends('demand.layouts.main')

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
                  <span class="invoice-number mr-50">Booking referel :</span>
                  <span>{{$booking->referral}}</span>
                </div>
                <div class="col-xl-8 col-md-12">
                  <div class="d-flex align-items-center justify-content-xl-end flex-wrap">
                    <div class="mr-3">
                      <small class="text-muted">Confirmed Date:</small>
                      <span>{{\Carbon\Carbon::parse($booking->confirmed_at)->format('d-m-Y H:i')}}</span>
                    </div>
                    <div>
                      <small class="text-muted">Booked Date:</small>
                      <span>{{\Carbon\Carbon::parse($booking->pick_up)->format('d-M-Y (H:i)')}} - {{ \Carbon\Carbon::parse($booking->drop_off)->format('d-M-Y (H:i)') }}</span>
                      {{-- <span>( {{$booking->car_price}}  )</span> --}}
                    </div>
                  </div>
                </div>
              </div>
              <!-- logo and title -->
              <div class="row my-3">
                <div class="col-md-4">
                  <h4 class="text-primary">{{$booking->user->name}}</h4>
                  <span>{{$booking->user->email}}</span><br>
                  <span>{{$booking->user->mobile}}</span>
                </div>
                  <div class="col-md-4 text-center">
                      @if($booking->status == 1)
                          <a href="#" onclick="showReviewBook('{{$booking->prefix}}', '{{$booking->id}}')">
                              <button type="submit" class="btn btn-info" data-icon="warning-alt">
                                  Review
                              </button>
                          </a>
                      @endif
                </div>
                <div class="col-md-4 d-flex justify-content-end">
                  <img src="{{$booking->user->image}}" alt="" height="46" width="164">
                </div>
              </div>
              <hr>
              <!-- invoice address and contact -->
              <div class="row invoice-info justify-content-center">
                <div class="col-4">
                  <h4 class="invoice-to mb-2">Provider Address</h4>
                  <div class="">
                    <span>{{$booking->provider->name}}</span>
                  </div>
                  <div class="">
                    <span class="d-block mb-1">{{$booking->provider->street}},</span>
                    <span class="d-block mb-1">{{$booking->provider->area}}</span>
                    <span class="d-block mb-1">{{$booking->provider->city}}</span>
                  </div>

                </div>
                <div class="col-4">
                    <h4 class="invoice-to mb-2">Car Details</h4>
                  <div class="">
                    <span class="d-block mb-1"><strong>Car Name :</strong> {{ $booking->car_name }}</span>
                    <span class="d-block mb-1"><strong>Car Fair :</strong> {{ $booking->charge }} ₹</span>
                    <span class="d-block mb-1"><strong>Price Type :</strong> {{ $booking->days.$booking->car_price }}</span>
                    <span class="d-block mb-1"><strong>Car Total Amount :</strong> {{ $booking->total_amount }} ₹</span>
                    <span class="d-block mb-1"><strong>Car deposit Amount :</strong> {{ $booking->amount_paid }} ₹</span>
                  </div>
                  </div>
                <div class="col-4">
                  <h4 class="invoice-to mb-2">Customer Address</h4>
                  <?php $address = json_decode($booking->address_details) ?>
                  <div class="">
                    <span>{{ $address->address }}</span>
                  </div>
                  {{-- <div class="">
                    <span>{{$booking->address->landmark}},</span>
                  </div> --}}
                </div>
                <div class="col-md-5 mt-2">
                    <h6 class="invoice-from text-center">Booking Instructions from Customer</h6>
                    <div class="">
{{--                          <span>{{$order->cart->instructions}}</span>--}}
                        <textarea class="form-control" name="decription" id="productescription" rows="5" placeholder="Descipton" disabled>{{ isset($booking->instructions) ? $booking->instructions : 'No Instructions Provided' }}</textarea>
                    </div>
                </div>
              </div>
            </div>
            <!-- product details table-->


            <!-- invoice subtotal -->
            <div class="card-body pt-0 mx-25">
              <hr>
              <div class="row justify-content-between">
                <div class="col-4 col-sm-4 mt-75">
                    <div class="invoice-subtotal">
                        <div class="invoice-calc d-flex justify-content-between mb-1">
                            <span class="invoice-title">Service status : </span>

                            <span class="invoice-value text-primary">{{$booking->book_state}}</span>
                          </div>
                        <div class="invoice-calc d-flex justify-content-between mb-1">
                          <span class="invoice-title">Payment status : </span>
                          <?php $class = $booking->paid == 0 ? 'danger' : 'success';
                            $name = $booking->paid == 0 ? 'Pending' : 'Paid';
                            ?>
                          <span class="invoice-value text-{{$class}}">{{$name}}</span>
                        </div>
                        <div class="invoice-calc d-flex justify-content-between mb-1">
                            <span class="invoice-title">Payment Type :</span>
                            <span class="invoice-value">{{$booking->type == 1 ? 'Online Payment' : 'Cash on Delivery' }}</span>
                        </div>

                      </div>
                </div>

                <div class="col-4 col-sm-4 text-center">
                    @if($booking->book_state == 'Ongoing')
                        <button type="submit"  onclick="showReviewBook('{{$booking->prefix}}', '{{$booking->id}}')" class="btn btn-info" data-icon="warning-alt">
                            Review
                        </button>
                        @endif
                     @if ($booking->canceled_at != null)
                    <button type="button" class="btn btn-outline-none text-danger">Order Canceled at</button>
                        <span>{{\Carbon\Carbon::parse($booking->canceled_at)->format('d-m-Y')}}</span>
                    <fieldset class="form-group">
                        <label for="productDescription">Cancel Reason</label>
                        <textarea class="form-control" name="description" id="productDescription" rows="2" placeholder="Descripton" disabled>{{ isset($booking->cancel_reason) ? $booking->cancel_reason : 'No reason Provided' }}</textarea>
                    </fieldset>
                    @endif
                    @if ($booking->status == 4)
                        <button type="button" class="btn btn-outline-none text-danger">Rejected by Vendor</button>
                        <span>{{\Carbon\Carbon::parse($booking->rejected_at)->format('d-m-Y')}}</span>

                    @endif
                </div>
                <div class="col-4 col-sm-4 d-flex justify-content-end  mt-75">
                  <div class="invoice-subtotal">
                    <div class="invoice-calc d-flex justify-content-between mb-1">
                      <span class="invoice-title">Sub-total</span>
                      <span class="invoice-value">{{$booking->total_amount}} ₹</span>
                    </div>

                    <div class="invoice-calc d-flex justify-content-between mb-1">
                      <span class="invoice-title">Delivery Charge</span>
                      <span class="invoice-value">{{$booking->travel_charge}} ₹</span>
                    </div>
                    <hr>
                    <div class="invoice-calc d-flex justify-content-between mb-1">
                      <span class="invoice-title">Payable Total</span>
                      <span class="invoice-value font-weight-bolder text-success">{{$booking->payable_amount}} ₹</span>
                    </div>
                     <div class="invoice-calc d-flex justify-content-between mb-1">
                      <span class="invoice-title">Shop Earnings</span>
                      <span class="invoice-value">{{$booking->payable_amount - $booking->comission}} ₹</span>
                    </div>
                    <div class="invoice-calc d-flex justify-content-between mb-1">
                      <span class="invoice-title">{{ config('app.name') }} commission</span>
                      <span class="invoice-value">{{$booking->comission ?? 0}} ₹</span>
                    </div>
                  </div>
                </div>
                  <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                      <a href="{{ url()->previous() }}" class="mr-auto btn btn-light">Back</a>
{{--                      <button type="submit" class="btn btn-warning glow mb-1 mb-sm-0 mr-0 mr-sm-1">Next--}}
{{--                      </button>--}}
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
{{-- @include('booking.order_review') --}}
@include('demand.booking.booking_review')
@push('scripts')
<script src="{{ asset('app-assets/js/scripts/pages/app-invoice.min.js') }}"></script>
@endpush
@endsection
