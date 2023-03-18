@extends('demand.layouts.main')

@section('content')
<section class="users-list-wrapper">

    <?php $route = explode('.', Route::currentRouteName());     ?>
    <div class="users-list-table">
        <div class="card">
            <div class="card-header">
                <div class="card-text">
                    <h5 class="d-block card-text text-center mb-3">Total Bookings - {{ $bookings->total() }}</h5>
                        <div class="row justify-content-between">
                            <div class="col-sm-3">
                                <label for="">Select Service</label>
                                    <select name="" id="filterByBook" class="form-control" onchange="filterBooking('{{env('APP_URL')}}')">
                                        <option value="all" {{request()->book == null  ? 'selected' : ''}}>All</option>
                                        <option value="services" {{request()->book == 'services'  ? 'selected' : ''}}>Service Bookings</option>
                                        <option value="cars" {{request()->book == 'cars'  ? 'selected' : ''}}>Car Bookings</option>
                                    </select>
                            </div>
                            @role('admin')
                            <div class="col-sm-3">
                                <label for="">Select Provider</label>
                                    <select name="" id="filterByShop" class="form-control select2" onchange="filterOrder('{{env('APP_URL')}}')">
                                        <option value="all" {{request()->type == 'all'  ? 'selected' : ''}}>All</option>
                                        @forelse ($shops as $item)
                                        <option value="{{$item->id}}" {{request()->shop == $item->id  ? 'selected' : ''}}>{{$item->name}}</option>
                                        @empty

                                        @endforelse
                                    </select>
                                </div>
                                    @else
                                    <input type="hidden" name="" id="filterByShop" value="{{auth()->user()->provider->id}}">
                                @endrole
                            <div class="col-sm-3">
                                @can('view_bookings')
                                    <label for="">Booking Status</label>
                                    <select name="" id="filterByType" onchange="filterOrder('{{env('APP_URL')}}')" class="form-control select2">
                                        <option value="all" {{request()->type == null  ? 'selected' : ''}}>All</option>
                                        <option value="1" {{request()->type == 1 ? 'selected' : ''}}>Pending</option>
                                        <option value="2" {{request()->type == 2 ? 'selected' : ''}}>Accepted</option>
                                        <option value="6" {{request()->type == 6 ? 'selected' : ''}}>Completed</option>
                                        <option value="5" {{request()->type == 5 ? 'selected' : ''}}>Canceled by customer</option>
                                        <option value="4" {{request()->type == 4 ? 'selected' : ''}}>Rejected by Vendor</option>
                                    </select>
                                @endcan
                            </div>

                            @push('scripts')
                                <script>
                                    function filterOrder(url){
                                        var type = $('#filterByType').val();
                                        var shop = $('#filterByShop').val();
                                        var book = $('#filterByBook').val();
                                        window.location.href = url+'demand/bookings/'+book+'/'+shop+'/'+type+'/datas';
                                    }
                                    function filterBooking(url){
                                        var type = $('#filterByType').val();
                                        var shop = $('#filterByShop').val();
                                        var book = $('#filterByBook').val();
                                        window.location.href = url+'demand/bookings/'+book+'/'+shop+'/'+type+'/datas';
                                    }
                                </script>
                            @endpush
                            <div class="col-sm-3">
                                <div class="searchbar">
                                    <form>
                                        <label for="">Search Booking-ID</label>
                                        <div class="input-group">
                                          <input type="text" id="search" class="form-control" placeholder="Search">
                                        </div>
                                    </form>
                                </div>
                                </div>

                        </div>
                        </div>
                </div><hr>
            <div class="card-content">
                <div class="card-body">
                    <!-- datatable start -->
                    <div class="table-responsive">
                        <table id="users-list-datatable" class="table zero-configuration">
                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    @role('admin')
                                    <th>Provider Name</th>
                                    @endrole
                                    <th>Booking Referel</th>
                                    <th>Customer mobile</th>
                                    <th>Booked Amount</th>
                                    {{-- <th style="white-space: nowrap">Expect Delivery</th> --}}
                                    <th>Status</th>
                                    <th>Payment Type</th>
                                    <th>Actions</th>
                                    @can('view_bookings')
                                    <th>Detail</th>
                                    @endcan

                                </tr>
                            </thead>
                            <tbody>

                                @if (isset($bookings) && !empty($bookings))
                                    @foreach ($bookings as $k => $item)
                                    <tr>
                                        <td>{{($bookings->perPage() * ($bookings->currentPage() - 1)) + $k + 1}}</td>
                                    @role('admin')
                                    <td><a href="{{route('demand.providers.show', $item->provider->id)}}" class="mr-1">{{$item->provider->name}}</a></td>
                                    @endrole
                                    <td>{{$item->referral}}</td>
                                    <td>{{$item->user->mobile ?? NULL}}</td>
                                    <td>{{$item->payable_amount}} ₹</td>
                                    {{-- <td>{{$item->expected_time}}</td> --}}
                                    <td>{{$item->book_state}}</td>
                                    <td>{{$item->payment == 0 ? 'COD' : "Online"}}</td>
                                    <td>
                                        @if ($item->status == 1)
                                        <a href="#" onclick="showReviewBook('{{$item->prefix}}', '{{$item->id}}')">
                                            <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                                Review
                                            </button>
                                        </a>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route('demand.bookings.show', $item->id)}}" class="mr-1">
                                            <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                                <i class="bx bx-show"></i>
                                            </button>
                                        </a>
                                    </td>

                                    </tr>
                                    @endforeach
                                @endif


                            </tbody>
                        </table>
                        <div class="mx-auto" style="width: fit-content">{{ $bookings->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
    </div>
@include('demand.booking.booking_review')

</section>
@endsection
