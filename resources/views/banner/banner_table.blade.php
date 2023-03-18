<table id="users-list-datatable" class="table zero-configuration">
    <thead>
        <tr>
            <th>S.no</th>
{{--                                    <th>Service Name</th>--}}
            {{-- <th>Shop Name</th> --}}
{{--                                    <th>Description</th>--}}

            <th>Image</th>
            <th>status</th>
            @canany(['edit_banners', 'delete_banners'])
            <th>Actions</th>
            @endcanany

        </tr>
    </thead>
    <tbody>
        @if (isset($banners) && !empty($banners))
            @foreach ($banners as $key => $item)
            <tr>
            <td>{{($banners->perPage() * ($banners->currentPage() - 1)) + $key + 1}}</td>
{{--                                    <td>{{ucfirst($item->type->name)}}</td>--}}
            {{-- <td>{{$item->shop->name}}</td> --}}
{{--                                    <td>{{$item->description}}</td>--}}
            <td><img src="{{$item->image}}" alt="" srcset="" width="20%"></td>
            <td>
                <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                    <input type="checkbox" class="custom-control-input" {{$item->active == 1 ? 'checked' : ''}}
                      onchange="change_status('{{$item->id}}', 'banners', '#customSwitchGlow{{$item->id}}', 'active');" id="customSwitchGlow{{$item->id}}">
                    <label class="custom-control-label" for="customSwitchGlow{{$item->id}}">
                    </label>
                </div>
            </td>

                @canany(['edit_banners', 'delete_banners'])
                <td>
                    <div style="display: inline-flex">
                        @can('edit_banners')
                            <button type="button" onclick='showProductCategory("#productCategoryModal", "{{$item->id}}")' class="btn-outline-info" data-icon="warning-alt">
                                <i class="bx bx-edit-alt"></i>
                            </button>
                        @endcan
                        @can('delete_banners')
                        <form action="{{route('banners.destroy', $item->id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this Banner?')" method="post">
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
<div class="mx-auto" style="width: fit-content">{{ $banners->links() }}</div>
