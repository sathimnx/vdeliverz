@extends('layouts.main')

@section('content')
    <section class="users-list-wrapper">


        <div class="users-list-table">
            <div class="card">
                <div class="card-header">
                    <div class="card-text">
                        <h5 class="d-block card-text text-center mb-3">Payments History</h5>
                        <div class="row">
                            @role('vendor')
                                <div class="col-sm-4">
                                    <a href="{{ route('payments.show', [auth()->user()->shop->id, 0]) }}">
                                        <button type="submit" class="btn btn-info" data-icon="warning-alt">
                                            Request Payment
                                        </button>
                                    </a>
                                </div>
                            @endrole
                            @role('admin')
                                <div class="col-sm-4">
                                    {{-- <a href="{{ route('payments.list') }}" class="btn btn-info">
                                        Direct Payment
                                    </a> --}}
                                </div>
                                <div class="col-sm-4">
                                    <label for="">Select Shop</label>
                                    <select name="" id="filterByShop" class="form-control select2"
                                        onchange="filterOrder('{{ env('APP_URL') }}')">
                                        <option value="all" {{ request()->type == 'all' ? 'selected' : '' }}>All</option>
                                        @forelse ($shops as $item)
                                            <option value="{{ $item->id }}"
                                                {{ request()->shop == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                            </option>
                                        @empty

                                        @endforelse
                                    </select>

                                </div>
                            @else
                                <input type="hidden" name="" id="filterByShop" value="{{ auth()->user()->shop->id }}">
                            @endrole
                            <div class="col-sm-4">
                                <label for="">Payment Status</label>
                                <select name="" id="filterByType" onchange="filterOrder('{{ env('APP_URL') }}')"
                                    class="form-control select2">
                                    <option value="all" {{ request()->status === 'all' ? 'selected' : '' }}>All</option>
                                    <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Requested</option>
                                    <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Accepted</option>
                                    <option value="3" {{ request()->status == 3 ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                            {{-- <div class="col-sm-4">
                                <div class="searchbar">
                                    <form>
                                        <div class="input-group">
                                          <input type="text" id="search" class="form-control" placeholder="Search">
                                        </div>
                                    </form>
                                </div>
                                </div> --}}
                            @push('scripts')
                                <script>
                                    function filterOrder(url) {
                                        var status = $('#filterByType').val();
                                        var shop = $('#filterByShop').val();
                                        window.location.href = url + 'payments-history/' + shop + '/' + status;
                                    }
                                </script>
                            @endpush
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
                                        <th>Shop Name</th>
                                        <th>Payment Status</th>
                                        <th>Orders Count</th>
                                        <th>Earnings</th>
                                        <th>Actions</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($payments) && !empty($payments))
                                        @foreach ($payments as $key => $item)
                                            <tr>
                                                <td>{{ $payments->perPage() * ($payments->currentPage() - 1) + $key + 1 }}
                                                </td>
                                                <td>{{ $item->shop->name }}</td>
                                                <td>{{ $item->pay_state }}</td>
                                                <td>
                                                    {{ $item->count }}
                                                </td>
                                                <td>
                                                    {{ $item->total }}
                                                </td>

                                                <td>
                                                    <a href="{{ route('payments.view', $item->id) }}"
                                                        class="mr-1">
                                                        <button type="submit" class="btn-outline-info"
                                                            data-icon="warning-alt">
                                                            <i class="bx bx-show"></i>
                                                        </button>
                                                    </a>
                                                </td>

                                            </tr>
                                        @endforeach
                                    @endif


                                </tbody>
                            </table>
                            <div class="mx-auto" style="width: fit-content">{{ $payments->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
