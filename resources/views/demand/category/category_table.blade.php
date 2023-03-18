<table id="users-list-datatable" class="table zero-configuration">
    <thead>
        <tr>
            <th>S.no</th>
            <th>Service Name</th>
            {{-- <th>Category Name</th> --}}
            <th>Icon</th>
            <th>Order</th>
            <th>status</th>
            @canany(['edit_categories', 'delete_categories'])
            <th>Actions</th>
            @endcanany

        </tr>
    </thead>
    <tbody>
        @if (isset($services) && !empty($services))
            @foreach ($services as $key => $item)
            <tr>
            <td>{{($services->perPage() * ($services->currentPage() - 1)) + $key + 1}}</td>
            {{-- <td>Demand and Services</td> --}}
            <td>{{$item->name}}</td>
            <td><img src="{{$item->image}}" alt="" srcset="" width="15%"></td>
            <td><input type="number" value="{{ $item->order }}" onchange="changeOrder('{{ $item->id }}', this.value)" id="categoryOrder{{ $item->id }}" maxlength="1" class="form-control w-50" ></td>
            <td>
                <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                    <input type="checkbox" class="custom-control-input" {{$item->active == 1 ? 'checked' : ''}}
                      onchange="change_status('{{$item->id}}', 'services', '#customSwitchGlow{{$item->id}}', 'active');" id="customSwitchGlow{{$item->id}}">
                    <label class="custom-control-label" for="customSwitchGlow{{$item->id}}">
                    </label>
                </div>
            </td>
                {{-- @if ($item->id != 1) --}}
                @canany(['edit_services', 'delete_services'])
                <td>
                    <div style="display: inline-flex">
                        @can('edit_services')
                            <button type="button" onclick="showProductCategory('#productCategoryModal', '{{$item->id}}', '{{$item->name}}', '{{$item->image}}', null)" class="btn-outline-info" data-icon="warning-alt">
                                <i class="bx bx-edit-alt"></i>
                            </button>
                        @endcan
                        @can('delete_slots')
                            <form action="{{route('demand.services.destroy', $item->id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this Service?')" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-outline-danger">
                                    <i class="bx bx-trash-alt"></i>
                                </button>
                            </form>
                        @endcan
                        </div>
                </td>
                @endcanany
                {{-- @endif --}}

            </tr>
            @endforeach
        @endif


    </tbody>
</table>
<div class="mx-auto" style="width: fit-content">{{ $services->links() }}</div>
