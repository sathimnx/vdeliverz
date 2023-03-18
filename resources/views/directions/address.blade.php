@extends('layouts.main')

@section('content')
    <section class="users-list-wrapper">

        <div class="users-list-table">
            <div class="card">
                <div class="card-header">
                    <div class="card-text">
                        {{-- <h5 class="d-block card-text text-center mb-3">Total Addresses - {{ $addresses->total() }}</h5> --}}

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
                                        <th>Customer Name</th>
                                        <th>Customer Address</th>
                                        <th>Shop Address</th>
                                        <th>KMS</th>
                                        <th>Show in Map</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($addresses) && !empty($addresses))
                                        @foreach ($addresses as $key => $item)
                                            <tr>
                                                <td>{{ $key + $addresses->firstItem() }}</td>
                                                <td><a
                                                        href="{{ route('users.show', $item->address->user->id) }}">{{ $item->address->user->name }}</a>
                                                </td>
                                                <td>{{ $item->address->address ?? null }}</td>
                                                <td><strong>{{ $item->shop->name }}</strong> -
                                                    {{ $item->shop->address ?? null }}
                                                </td>
                                                <td>{{ $item->kms }}</td>
                                                <td>
                                                    <a href="https://www.google.com/maps/dir/?api=1&origin={{ $item->address->latitude }},{{ $item->address->longitude }}&destination={{ $item->shop->latitude }},{{ $item->shop->longitude }}&travelmode=driving"
                                                        target="_blank">
                                                        <button class="btn-outline-info"> <i
                                                                class="bx bx-compass"></i></button>
                                                    </a>
                                                </td>

                                            </tr>
                                        @endforeach
                                    @endif


                                </tbody>
                            </table>
                            <div class="mx-auto" style="width: fit-content">{{ $addresses->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
