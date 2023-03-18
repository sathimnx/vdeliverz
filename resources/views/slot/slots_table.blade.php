
<table id="users-list-datatable" class="table zero-configuration">
    <thead>
        <tr>
            <th>S.no</th>
            <th>Shop Name</th>
            <th>Weekdays</th>
            <th>From</th>
            <th>To</th>
            <th>status</th>
            @canany(['edit_slots', 'delete_slots'])
            <th>Actions</th>
            @endcanany

        </tr>
    </thead>
    <tbody>
        @if (isset($slots) && !empty($slots))
            @foreach ($slots as $key => $item)
            <tr>
                <td>{{($slots->perPage() * ($slots->currentPage() - 1)) + $key + 1}}</td>
                <td><a href="{{route('shops.show', $item->shop->id)}}" class="mr-1">{{ucfirst($item->shop->name)}}</a></td>
                <td>{{$item->weekdays}}</td>
                <td>{{$item->from_time}}</td>
                <td>{{$item->to_time}}</td>
            <td>
                <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                    <input type="checkbox" class="custom-control-input" {{$item->active == 1 ? 'checked' : ''}}
                        onchange="change_status('{{$item->id}}', 'sub_categories', '#customSwitchGlow{{$key}}', 'active');" id="customSwitchGlow{{$key}}">
                    <label class="custom-control-label" for="customSwitchGlow{{$key}}">
                    </label>
                </div>
            </td>

                @canany(['edit_slots', 'delete_slots'])
                <td>
                    <div style="display: inline-flex">
                        @can('edit_slots')
                            <button type="button" onclick="showProductCategory('#productCategoryModal', '{{$item->id}}', '{{$item->from}}', '{{$item->to}}')" class="btn-outline-info" data-icon="warning-alt">
                                <i class="bx bx-edit-alt"></i>
                            </button>
                        @endcan
                        @can('delete_slots')
                            <form action="{{route('slots.destroy', $item->id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this Slot?')" method="post">
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
            </tr>
            @endforeach
        @endif


    </tbody>
</table>
<div class="mx-auto" style="width: fit-content">{{ $slots->links() }}</div>
