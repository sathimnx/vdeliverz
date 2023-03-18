<table id="users-list-datatable" class="table zero-configuration">
    <thead>
        <tr>
            <th>S.no</th>
            <th>Provider Name</th>
            <th>Car Name</th>
            <th>Image</th>
            <th>status</th>
            @canany(['edit_sub-services', 'delete_sub-services'])
            <th>Actions</th>
            @endcanany
        </tr>
    </thead>
    <tbody>

        @if (isset($cars) && !empty($cars))
            @foreach ($cars as $k => $item)
            <tr>
            <td>{{($cars->perPage() * ($cars->currentPage() - 1)) + $k + 1}}</td>
            <td>{{ucfirst($item->provider->name)}}</td>
            <td>{{ucfirst($item->car->name)}}</td>
            <td><img src="{{$item->img_url}}" alt="" srcset="" width="15%"></td>
            <td>
                <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                    <input type="checkbox" class="custom-control-input" {{$item->active == 1 ? 'checked' : ''}}
                      onchange="change_status('{{$item->id}}', 'car_provider', '#customSwitchGlow{{$item->id}}', 'active');" id="customSwitchGlow{{$item->id}}">
                    <label class="custom-control-label" for="customSwitchGlow{{$item->id}}">
                    </label>
                </div>
            </td>

                {{-- @canany(['edit_cars', 'delete_sub-services']) --}}
                <td>

                    @include('demand.shared._actions', [
                        "entity" => "provider-cars",
                        "id" => $item->id
                    ])

                </td>
                {{-- @endcanany --}}

            </tr>
            @endforeach
        @endif


    </tbody>
</table>
<div class="mx-auto" style="width: fit-content">{{ $cars->links() }}</div>
</div>
