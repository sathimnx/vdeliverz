<table id="users-list-datatable" class="table zero-configuration">
    <thead>
        <tr>
            <th>S.no</th>
            <th>Shop Name</th>
            <th>Opened</th>
            <th>GST</th>
            <th>Vendor Name</th>
            <th>Mobile</th>
            <th>Products</th>
            <th>Order Accept</th>
            @role('admin')
            <th>Priority</th>
            <th>status</th>
            @endrole
            @canany(['edit_shops', 'delete_shops', 'view_shops'])
                <th>Actions</th>
            @endcanany

        </tr>
    </thead>
    <tbody>
        @if (isset($shops) && !empty($shops))
            @foreach ($shops as $key => $item)
                <tr>
                    <td>{{ $shops->perPage() * ($shops->currentPage() - 1) + $key + 1 }}</td>
                    <td>{{ ucfirst($item->name) }}</td>
                    <td>
                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                            <input type="checkbox" class="custom-control-input"
                                {{ $item->opened == 1 ? 'checked' : '' }}
                                onchange="change_status('{{ $item->id }}', 'shops', '#openedSwitchGlow{{ $key }}', 'opened');"
                                id="openedSwitchGlow{{ $key }}">
                            <label class="custom-control-label" for="openedSwitchGlow{{ $key }}">
                            </label>
                        </div>
                    </td>
                     <td>
                        <div
                            class="custom-control custom-switch custom-switch-glow custom-control-inline">
                            <input type="checkbox" class="custom-control-input"
                                {{ $item->gst == 1 ? 'checked' : '' }}
                                onchange="change_status('{{ $item->id }}', 'shops', '#gstSwitchGlow{{ $key }}', 'gst');"
                                id="gstSwitchGlow{{ $key }}">
                            <label class="custom-control-label"
                                for="gstSwitchGlow{{ $key }}">
                            </label>
                        </div>
                    </td>
                    <td>{{ ucfirst($item->user->name) }}</td>
                    <td>{{ ucfirst($item->user->mobile) }}</td>
                    <td>{{ $item->products->count() }}</td>
                    <td>{{ $item->assign ? 'Automatic' : 'Manual' }}</td>

                    @role('admin')
                    <td><input type="number" value="{{ $item->prior }}"
                            onchange="changeOrder('{{ $item->id }}', this.value)"
                            id="categoryOrder{{ $item->id }}" class="form-control"></td>
                    <td>
                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                            <input type="checkbox" class="custom-control-input"
                                {{ $item->active == 1 ? 'checked' : '' }}
                                onchange="change_status('{{ $item->id }}', 'shops', '#customSwitchGlow{{ $key }}', 'active');"
                                id="customSwitchGlow{{ $key }}">
                            <label class="custom-control-label" for="customSwitchGlow{{ $key }}">
                            </label>
                        </div>
                    </td>
                    @endrole
                    @canany(['edit_shops', 'delete_shops', 'view_shops'])
                        <td>

                            <div style="display: inline-flex">
                                <a href="{{ route('shops.show', $item->id) }}" class="mr-1">
                                    <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                        <i class="bx bx-show"></i>
                                    </button>
                                </a>
                                @can('edit_shops')
                                    <a href="{{ route('shops.edit', $item->id) }}">
                                        <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                    </a>
                                @endcan

                                {{-- @can('delete_' . $entity) --}}
                                {{-- <form action="{{route($entity.'.destroy', $id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this {{str_singular($entity)}}?')" method="post"> --}}
                                {{-- {{method_field('DELETE')}} --}}
                                {{-- @csrf --}}
                                {{-- <button type="submit" class="btn-outline-danger"> --}}
                                {{-- <i class="bx bx-trash-alt"></i> --}}
                                {{-- </button> --}}

                                {{-- </form> --}}
                                {{-- @endcan --}}
                            </div>

                        </td>
                    @endcanany

                </tr>
            @endforeach
        @endif


    </tbody>
</table>
<div class="mx-auto" style="width: fit-content">{{ $shops->links() }}</div>
