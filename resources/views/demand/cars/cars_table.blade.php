<table id="users-list-datatable" class="table zero-configuration">
    <thead>
        <tr>
            <th>S.no</th>
            <th>Car Name</th>
            <th>Image</th>
            @role('admin')
            <th>status</th>
            @endrole
            @canany(['edit_cars', 'delete_cars'])
            <th>Actions</th>
            @endcanany
        </tr>
    </thead>
    <tbody>

        @if (isset($cars) && !empty($cars))
            @foreach ($cars as $k => $item)
            <tr>
            <td>{{($cars->perPage() * ($cars->currentPage() - 1)) + $k + 1}}</td>
            <td>{{ucfirst($item->name)}}</td>
            <td><img src="{{$item->img_url}}" alt="" srcset="" width="15%"></td>
            @role('admin')
            <td>
                <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                    <input type="checkbox" class="custom-control-input" {{$item->active == 1 ? 'checked' : ''}}
                      onchange="change_status('{{$item->id}}', 'cars', '#customSwitchGlow{{$item->id}}', 'active');" id="customSwitchGlow{{$item->id}}">
                    <label class="custom-control-label" for="customSwitchGlow{{$item->id}}">
                    </label>
                </div>
            </td>
            @endrole
                @canany(['edit_cars', 'delete_cars'])
                <td>


                    <div style="display: inline-flex">
                    @can('edit_cars')
                    <a href="{{route('demand.cars.edit', $item->id)}}">
                        <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                            <i class="bx bx-edit-alt"></i>
                        </button>
                        </a>
                    @endcan

                    @can('delete_cars')
                        <form action="{{route('demand.cars.destroy', $item->id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this Car?')" method="post">
                            {{method_field('DELETE')}}
                            @csrf
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
<div class="mx-auto" style="width: fit-content">{{ $cars->links() }}</div>
