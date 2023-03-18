<table id="users-list-datatable" class="table zero-configuration">
    <thead>
    <tr>
        <th>S.no</th>
        <th>Shop Name</th>
        <th>Category Name</th>
        {{--                                    <th>Icon</th>--}}
        <th>status</th>
        {{--                                    @canany(['edit_categories', 'delete_categories'])--}}
        <th>Actions</th>
        {{--                                    @endcanany--}}

    </tr>
    </thead>
    <tbody>
    @if (isset($titles) && !empty($titles))
        @foreach ($titles as $key => $item)
            <tr>
                <td>{{($titles->perPage() * ($titles->currentPage() - 1)) + $key + 1}}</td>
                <td>{{ucfirst($item->shop->name)}}</td>
                <td>{{$item->name}}</td>
                {{--                                    <td><img src="{{$item->image}}" alt="" srcset="" width="40%"></td>--}}
                <td>
                    <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                        <input type="checkbox" class="custom-control-input" {{$item->active == 1 ? 'checked' : ''}}
                        onchange="change_status('{{$item->id}}', 'titles', '#customSwitchGlow{{$item->id}}', 'active');" id="customSwitchGlow{{$item->id}}">
                        <label class="custom-control-label" for="customSwitchGlow{{$item->id}}">
                        </label>
                    </div>
                </td>

                {{--                                        @canany(['edit_categories', 'delete_categories'])--}}
                <td>
                    <div style="display: inline-flex">
                        {{--                                                @can('edit_categories')--}}
                        <button type="button" onclick="showProductCategory('#productCategoryModal', '{{$item->id}}', '{{$item->name}}', '{{$item->image}}', '{{$item->shop->id}}')" class="btn-outline-info" data-icon="warning-alt">
                            <i class="bx bx-edit-alt"></i>
                        </button>
                        {{--                                                @endcan--}}
                        {{--                                                 @can('delete_slots')--}}
                        <form action="{{route('titles.destroy', $item->id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this Topping Category?')" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-outline-danger">
                                <i class="bx bx-trash-alt"></i>
                            </button>

                        </form>
                        {{--                                                @endcan--}}
                    </div>
                </td>
                {{--                                        @endcanany--}}
            </tr>
        @endforeach
    @endif


    </tbody>
</table>
<div class="mx-auto" style="width: fit-content">{{ $titles->links() }}</div>
