@extends('layouts.main')

@section('content')
<section class="users-list-wrapper">


    <div class="users-list-table">
        <div class="card">
            <div class="card-header">
                <div class="card-text">
                    <h5 class="d-block card-text text-center mb-3">Total Shops - {{$shops->total()}}</h5>
                        <div class="row">
                            <div class="col-sm-6">

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
                                    <th>Shop Name</th>
                                    <th>Delivered Orders</th>
                                    <th>Earnings</th>
                                    <th>Commission</th>
                                    <th>Mobile</th>
                                    <th>Actions</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($shops) && !empty($shops))
                                    @foreach ($shops as $key => $item)
                                    <tr>
                                    <td>{{($shops->perPage() * ($shops->currentPage() - 1)) + $key + 1}}</td>
                                    <td>{{ucfirst($item->name)}}</td>
                                    <td>{{ucfirst($item->total_delivered)}}</td>
                                    <td>{{ucfirst($item->total_earnings)}} ₹</td>
                                    <td>{{ucfirst($item->commission_earnings)}} ₹</td>
                                    <td>{{ucfirst($item->user->mobile)}}</td>
                                   <td>
                                    <a href="{{route('sales.show', $item->id)}}" class="mr-1">
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
                        <div class="mx-auto" style="width: fit-content">{{ $shops->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
