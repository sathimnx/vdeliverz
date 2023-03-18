@extends('layouts.main')

@section('content')
    <section class="users-list-wrapper">

        <div class="users-list-table">
            <div class="card">
                <div class="card-header">
                    <div class="card-text">
                        <h5 class="d-block card-text text-center mb-3">Total Reviews - {{ $reviews->total() }}</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                {{-- <h4 class="card-title">List</h4> --}}

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
                            {{-- <div class="col-sm-6">
                                @can('create_users')
                                <a href="{{ route('users.create') }}" class="btn btn-warning float-right" class="btn btn-primary">Create User</a>
                                @endcan

                            </div> --}}

                        </div>
                    </div>
                </div>
                <hr>
                <div class="card-content">
                    <div class="card-body">
                        <!-- datatable start -->
                        <div class="table-responsive">
                            <table id="users-list-datatable" class="table zero-configuration">
                                <thead>
                                    <tr>
                                        <th>S.no</th>
                                        <th>Given To</th>
                                        <th>Given by</th>
                                        <th>Rating</th>
                                        <th>Comment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($reviews) && !empty($reviews))
                                        @foreach ($reviews as $key => $item)
                                            <tr>
                                                <td>{{ $key + $reviews->firstItem() }}</td>
                                                @if ($item->shop_id)
                                                    <td>{{ $item->shop->name }} ( Shop )</td>
                                                @else
                                                    <td>{{ $item->deliveryBoy->name }} ( Delivery Boy )</td>
                                                @endif
                                                @if ($item->user->hasAnyRole('vendor'))
                                                    <td><a href="{{ route('users.show', $item->user->id) }}">{{ $item->user->name }}
                                                            ( Vendor )</a></td>
                                                @else
                                                    <td> <a href="{{ route('users.show', $item->user->id) }}">
                                                            {{ $item->user->name }} ( Customer )</a></td>
                                                @endif
                                                <td>{{ $item->rating ?? null }}</td>
                                                <td>{{ $item->comment }}</td>

                                            </tr>
                                        @endforeach
                                    @endif


                                </tbody>
                            </table>
                            <div class="mx-auto" style="width: fit-content">{{ $reviews->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
