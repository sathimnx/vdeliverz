@extends('layouts.main')

@section('content')
    <section class="users-list-wrapper">


        <div class="users-list-table">
            <div class="card">
                <div class="card-header">
                    <div class="card-text">
                        <h5 class="d-block card-text text-center mb-3">Total Product Categories -
                            {{ $subCategories->total() }}</h5>
                        <div class="row">

                            <div class="col-sm-6">
                                {{-- @can('create_product-categories')
                                    <a onclick="showProductCategory('#productCategoryModal', null, null)"
                                        style="cursor: pointer;" class="btn btn-warning float-left text-white">Create Product
                                        Category</a>
                                @endcan --}}
                                @role('admin')
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
                                @endrole

                            </div>
                            @push('scripts')
                                <script>
                                    function filterOrder(url) {
                                        var type = $('#filterByType').val();
                                        var shop = $('#filterByShop').val();
                                        window.location.href = url + 'product-categories/' + shop + '/filter';
                                    }
                                </script>
                            @endpush
                            <div class="col-sm-6">
                                <div class="searchbar">
                                    <form>
                                        <label for="">Search</label>
                                        <div class="input-group">
                                            <input type="text" id="search" class="form-control" placeholder="Search">
                                        </div>
                                    </form>
                                </div>
                            </div>

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
                                        @role('admin')
                                        <th>Shop Name</th>
                                        @endrole
                                        <th>Category Name</th>
                                        <th>Products Count</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        @if (auth()->user()->email === 'vdeliverz@superadmin.com')
                                            <th>Actions</th>
                                        @endif
                                        {{-- @canany(['edit_product-categories', 'delete_product-categories'])
                                    <th>Actions</th>
                                    @endcanany --}}

                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($subCategories) && !empty($subCategories))
                                        @foreach ($subCategories as $k => $item)
                                            <tr>
                                                <td>{{ $subCategories->perPage() * ($subCategories->currentPage() - 1) + $k + 1 }}
                                                </td>
                                                @role('admin')
                                                <td>{{ ucfirst($item->shop->name) }}</td>
                                                @endrole
                                                <td>{{ ucfirst($item->subCategory->name) }}</td>
                                                <td>{{ $item->prod_count }}</td>
                                                <td><input type="number" value="{{ $item->order }}"
                                                        onchange="changeOrder('{{ $item->id }}', '{{ $item->shop->id }}', this.value)"
                                                        id="categoryOrder{{ $item->id }}" class="form-control"></td>
                                                <td>
                                                    <div
                                                        class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                                        <input type="checkbox" class="custom-control-input"
                                                            {{ $item->active == 1 ? 'checked' : '' }}
                                                            onchange="change_status('{{ $item->id }}', 'shop_sub_category', '#customSwitchGlow{{ $item->id }}', 'active');"
                                                            id="customSwitchGlow{{ $item->id }}">
                                                        <label class="custom-control-label"
                                                            for="customSwitchGlow{{ $item->id }}">
                                                        </label>
                                                    </div>
                                                </td>
                                                {{-- @canany(['edit_product-categories', 'delete_product-categories'])
                                        <td>
                                            <div style="display: inline-flex">
                                                @can('edit_product-categories')
                                                    <button type="button" onclick="showProductCategory('#productCategoryModal', '{{$item->id}}', '{{$item->name}}')" class="btn-outline-info" data-icon="warning-alt">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </button>
                                                @endcan
                                                <!--@can('delete_product-categories')-->
                                                <!--    <form action="{{route('product-categories.destroy', $item->id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this Product Category?')" method="post">-->
                                                <!--        @csrf-->
                                                <!--        @method('DELETE')-->
                                                <!--        <button type="submit" class="btn-outline-danger">-->
                                                <!--            <i class="bx bx-trash-alt"></i>-->
                                                <!--        </button>-->

                                                <!--    </form>-->
                                                <!--@endcan-->
                                                </div>
                                        </td>
                                        @endcanany --}}
                                                @if (auth()->user()->email === 'vdeliverz@superadmin.com')
                                                    <td>
                                                        <form
                                                            action="{{ route('product-categories.destroy', $item->subCategory->id) }}"
                                                            class="ml-1"
                                                            onsubmit="return confirm('Are you sure wanted to delete this Product Category?')"
                                                            method="post">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn-outline-danger">
                                                                <i class="bx bx-trash-alt"></i>
                                                            </button>

                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif


                                </tbody>
                            </table>
                            <div class="mx-auto" style="width: fit-content">{{ $subCategories->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
        </div>
    </section>
    @include('product_category._edit_modal')
    @push('scripts')
        <script>
            function showProductCategory(modalId, id = null, name = null) {
                if (id != null) {
                    $(modalId + ' form').attr('action', "{{ url('/product-categories') }}" + "/" + id);
                    $(modalId + ' input[name="_method"]').val("PUT");
                    $(modalId + ' input[name="name"]').val(name);
                    $(modalId + ' button[type="submit"]').text('Update');
                    $(modalId + ' h4').text('Edit Product Category');
                    $(modalId).modal('show');
                } else {
                    $(modalId + ' form').attr('action', "{{ url('/product-categories') }}");
                    $(modalId + ' input[name="_method"]').val("POST");
                    $(modalId + ' input[name="name"]').val(name);
                    $(modalId + ' button[type="submit"]').text('Create');
                    $(modalId + ' h4').text('Add Product Category');
                    $(modalId).modal('show');
                }
            }

            function changeOrder(id, shop, num) {
                $.ajax({
                    url: "/product-categories/" + id + "/change",
                    type: 'GET',
                    data: {
                        "_token": '{{ csrf_token() }}',
                        num: num,
                        shop: shop
                    },
                    success: function(response) {
                        console.log(response);
                        $('#categoryOrder' + response.id).val(response.order);
                        toastr.success("Order Changed!");
                    },
                    error: function(response) {
                        toastr.error("Bad network", "Please Refresh and Try!");
                    }
                })
            }
        </script>
    @endpush
@endsection
