<table id="users-list-datatable" class="table zero-configuration">
    <thead>
        <tr>
            <th>S.no</th>
            <th>Service</th>
            <th>Sub-Service name</th>
            <th>Image</th>
            <th>status</th>
            @canany(['edit_sub-services', 'delete_sub-services'])
            <th>Actions</th>
            @endcanany
        </tr>
    </thead>
    <tbody>

        @if (isset($sub_services) && !empty($sub_services))
            @foreach ($sub_services as $k => $item)
            <tr>
            <td>{{($sub_services->perPage() * ($sub_services->currentPage() - 1)) + $k + 1}}</td>
            <td>{{ucfirst($item->service->name)}}</td>
            <td>{{ucfirst($item->name)}}</td>
            <td><img src="{{ asset($item->image) }}" width="20%" alt="{{ __('') }}"></td>
            <td>
                <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                    <input type="checkbox" class="custom-control-input" {{$item->active == 1 ? 'checked' : ''}}
                      onchange="change_status('{{$item->id}}', 'sub_services', '#customSwitchGlow{{$item->id}}', 'active');" id="customSwitchGlow{{$item->id}}">
                    <label class="custom-control-label" for="customSwitchGlow{{$item->id}}">
                    </label>
                </div>
            </td>

                @canany(['edit_sub-services', 'delete_sub-services'])
                <td>

                    @include('demand.shared._actions', [
                        "entity" => "sub-services",
                        "id" => $item->id
                    ])

                </td>
                @endcanany

            </tr>
            @endforeach
        @endif


    </tbody>
</table>
<div class="mx-auto" style="width: fit-content">{{ $sub_services->links() }}</div>
</div>
