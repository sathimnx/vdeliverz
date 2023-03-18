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
                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                            <input type="checkbox" class="custom-control-input"
                                {{ $item->active == 1 ? 'checked' : '' }}
                                onchange="change_status('{{ $item->id }}', 'shop_sub_category', '#customSwitchGlow{{ $item->id }}', 'active');"
                                id="customSwitchGlow{{ $item->id }}">
                            <label class="custom-control-label" for="customSwitchGlow{{ $item->id }}">
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
                            <form action="{{ route('product-categories.destroy', $item->subCategory->id) }}"
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
