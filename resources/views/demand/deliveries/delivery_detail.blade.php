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
                      <span>{{\Carbon\Carbon::parse($user->created_at)->format('d-m-Y H:i')}}</span>
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
                <div class="col-6">
                  <h4 class="text-primary">{{$user->name}}</h4>
                  <span>{{$user->email}}</span><br>
                  <span>{{$user->mobile}}</span>
                </div>
                <div class="col-6 d-flex justify-content-end">
                  <img src="{{$user->image}}" alt="" height="46" width="164">
                </div>
              </div>
              <hr>
              <!-- invoice address and contact -->
              {{-- <div class="row invoice-info">
                <div class="col-6 mt-1">
                  <h6 class="invoice-from">Order From (Shop)</h6>
                  <div class="">
                    <span>{{$order->cart->shop->name}}</span>
                  </div>
                  <div class="">
                    <span>{{$order->cart->shop->street}},</span>
                    <span>{{$order->cart->shop->area}}</span><br>
                    <span>{{$order->cart->shop->city}}</span><br>
                  </div>
                  <div class="">
                    <span>{{$order->cart->centre->user->email}}</span>
                  </div>
                  <div class="">
                    <span>{{$order->cart->centre->user->mobile}}</span>
                  </div>
                </div>
                <div class="col-6 mt-1">
                  <h6 class="invoice-to">Delivered to</h6>

                  <div class="">
                    <span>{{$address->address}}</span>
                  </div>
                  <div class="">
                    <span>{{$order->address->landmark}},</span>
                  </div>
                </div>
              </div> --}}
              <hr>
            </div>
            <!-- product details table-->
            <div class="invoice-product-details table-responsive mx-md-25 card-body">
              <table class="table table-borderless mb-0 text-center">
                <thead>
                  <tr class="border-0">
                    <th scope="col" class="font-weight-bolder">Accepted Orders</th>
                    <th scope="col" class="font-weight-bolder">Delivered Orders</th>
                    <th scope="col" class="font-weight-bolder">Canceled Orders</th>
                    <th scope="col" class="font-weight-bolder">Points Earned</th>
                    <th scope="col" class="font-weight-bolder">Amount Earned</th>
                    {{-- <th scope="col" class="text-right font-weight-bolder">Price</th> --}}
                  </tr>
                </thead>
                <tbody>
                   <tr>
                        <td>{{$user->deliveries->count()}}</td>
                        <td>{{$user->delivered_count}}</td>
                        <td>{{$user->canceled_count}}</td>
                        <td>{{$user->amount_earned}}</td>
                        <td>{{$user->points_earned}}</td>
                        {{-- <td class="text-primary text-right font-weight-bold">{{$item->amount}}</td> --}}
                      </tr>

                </tbody>
              </table>
            </div>

            <!-- invoice subtotal -->
            <div class="card-body pt-0 mx-25">

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
