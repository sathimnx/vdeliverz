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
                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                            <input type="checkbox" class="custom-control-input" {{ $item->rec == 1 ? 'checked' : '' }}
                                onchange="change_status('{{ $item->id }}', 'products', '#openedSwitchGlowq{{ $k }}', 'rec');"
                                id="openedSwitchGlowq{{ $k }}">
                            <label class="custom-control-label" for="openedSwitchGlowq{{ $k }}">
                            </label>
                        </div>
                    </td>
                    <td><a href="{{ route('shops.show', $item->shop->id) }}"
                            class="mr-1">{{ ucfirst($item->shop->name) }}</a></td>
                    <td>{{ ucfirst($item->category->name) }}</td>

                    <td>
                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                            <input type="checkbox" class="custom-control-input"
                                {{ $item->active == 1 ? 'checked' : '' }}
                                onchange="change_status('{{ $item->id }}', 'products', '#customSwitchGlow{{ $item->id }}', 'active');"
                                id="customSwitchGlow{{ $item->id }}">
                            <label class="custom-control-label" for="customSwitchGlow{{ $item->id }}">
                            </label>
                        </div>
                    </td>

                    @canany(['edit_products', 'delete_products'])
                        <td>

                            <div style="display: inline-flex">
                                <a href="{{ route('products.show', $item->id) }}" class="mr-1">
                                    <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                        <i class="bx bx-show"></i>
                                    </button>
                                </a>
                                @can('edit_products')
                                    <a href="{{ route('products.edit', $item->id) }}">
                                        <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                    </a>
                                @endcan

                                @if (auth()->user()->email === 'vdeliverz@superadmin.com')
                                    <form action="{{ route('products.destroy', $item->id) }}" class="ml-1"
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
