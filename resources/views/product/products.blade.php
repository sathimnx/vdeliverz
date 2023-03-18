@extends('layouts.main')

@section('content')
    <section class="users-list-wrapper">


        <div class="users-list-table">
            <div class="card">
                <div class="card-header">
                    <div class="card-text">
                        <h5 class="d-block card-text text-center mb-3">Total Products - {{ $products->total() }}</h5>
                        <div class="row">

                            <div class="col-sm-4">
                                @can('create_products')
                                    <a href="{{ route('products.create') }}" class="btn btn-warning float-left"
                                        class="btn btn-primary">Create Product</a>
                                @endcan

                            </div>
                            <div class="col-sm-4">
                                {{-- @role('admin') --}}
                                {{-- <label for="">Select Shop</label> --}}
                                {{-- <select name="" id="filterByShop" class="form-control select2" onchange="filterOrder('{{env('APP_URL')}}')"> --}}
                                {{-- <option value="all" {{request()->type == 'all'  ? 'selected' : ''}}>All</option> --}}
                                {{-- @forelse ($shops as $item) --}}
                                {{-- <option value="{{$item->id}}" {{request()->shop == $item->id  ? 'selected' : ''}}>{{$item->name}}</option> --}}
                                {{-- @empty --}}

                                {{-- @endforelse --}}
                                {{-- </select> --}}
                                {{-- @else --}}
                                {{-- <input type="hidden" name="" id="filterByShop" value="{{auth()->user()->shop->id}}"> --}}
                                {{-- @endrole --}}
                            </div>
                            <div class="col-sm-4">
                                <div class="searchbar">
                                    <form>
                                        <label for="">Search</label>
                                        <div class="input-group">
                                            <input type="text" id="search" class="form-control" onchange="filterOrder('{{env('APP_URL')}}')" placeholder="Search">
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                    @push('scripts')
                        <script>
                            function filterOrder(url) {
                                alert();
                                var type = $('#filterByType').val();
                                var shop = $('#filterByShop').val();
                                window.location.href = url + 'products;
                            }
                        </script>
                    @endpush
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
                                        <th>Product Name</th>
                                        <th>Product Category</th>
                                        <th>Recommend</th>
                                        <th>Shop Name</th>
                                        <th>Main Category</th>
                                        <th>status</th>
                                        @canany(['edit_products', 'delete_products'])
                                            <th>Actions</th>
                                        @endcanany

                                    </tr>
                                </thead>
                                <tbody>

                                    @if (isset($products) && !empty($products))
                                        @foreach ($products as $k => $item)
                                            <tr>
                                                <td>{{ $products->perPage() * ($products->currentPage() - 1) + $k + 1 }}
                                                </td>
                                                <td>{{ ucfirst($item->name) }}</td>
                                                <td>{{ ucfirst($item->subCategory->name) }}</td>
                                                <td>
                                                    <div
                                                        class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                                        <input type="checkbox" class="custom-control-input"
                                                            {{ $item->rec == 1 ? 'checked' : '' }}
                                                            onchange="change_status('{{ $item->id }}', 'products', '#openedSwitchGlowq{{ $k }}', 'rec');"
                                                            id="openedSwitchGlowq{{ $k }}">
                                                        <label class="custom-control-label"
                                                            for="openedSwitchGlowq{{ $k }}">
                                                        </label>
                                                    </div>
                                                </td>
                                                <td><a href="{{ route('shops.show', $item->shop->id) }}"
                                                        class="mr-1">{{ ucfirst($item->shop->name) }}</a></td>
                                                <td>{{ ucfirst($item->category->name) }}</td>

                                                <td>
                                                    <div
                                                        class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                                        <input type="checkbox" class="custom-control-input"
                                                            {{ $item->active == 1 ? 'checked' : '' }}
                                                            onchange="change_status('{{ $item->id }}', 'products', '#customSwitchGlow{{ $item->id }}', 'active');"
                                                            id="customSwitchGlow{{ $item->id }}">
                                                        <label class="custom-control-label"
                                                            for="customSwitchGlow{{ $item->id }}">
                                                        </label>
                                                    </div>
                                                </td>

                                                @canany(['edit_products', 'delete_products'])
                                                    <td>

                                                        <div style="display: inline-flex">
                                                            <a href="{{ route('products.show', $item->id) }}"
                                                                class="mr-1">
                                                                <button type="submit" class="btn-outline-info"
                                                                    data-icon="warning-alt">
                                                                    <i class="bx bx-show"></i>
                                                                </button>
                                                            </a>
                                                            @can('edit_products')
                                                                <a href="{{ route('products.edit', $item->id) }}">
                                                                    <button type="submit" class="btn-outline-info"
                                                                        data-icon="warning-alt">
                                                                        <i class="bx bx-edit-alt"></i>
                                                                    </button>
                                                                </a>
                                                            @endcan

                                                            @if (auth()->user()->email === 'vdeliverz@superadmin.com')
                                                                <form action="{{ route('products.destroy', $item->id) }}"
                                                                    class="ml-1"
                                                                    onsubmit="return confirm('Are you sure wanted to delete this Product?')"
                                                                    method="post">
                                                                    {{ method_field('DELETE') }}
                                                                    @csrf
                                                                    <button type="submit" class="btn-outline-danger">
                                                                        <i class="bx bx-trash-alt"></i>
                                                                    </button>

                                                                </form>
                                                            @endif
                                                        </div>


                                                    </td>
                                                @endcanany

                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>S.no</th>
                                        <th>Product Name</th>
                                        <th>Product Category</th>
                                        <th>Recommend</th>
                                        <th>Shop Name</th>
                                        <th>Main Category</th>
                                        <th>status</th>
                                        @canany(['edit_products', 'delete_products'])
                                            <th>Actions</th>
                                        @endcanany

                                    </tr>
                                </tfoot>
                            </table>
                            <div class="mx-auto" style="width: fit-content">{{ $products->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
