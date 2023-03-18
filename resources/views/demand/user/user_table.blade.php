<table id="users-list-datatable" class="table zero-configuration">
    <thead>
        <tr>
            <th>S.no</th>
            <th>Username</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>role</th>
            <th>status</th>
            @canany(['edit_users', 'delete_users'])
            <th>Actions</th>
            @endcanany

        </tr>
    </thead>
    <tbody>
        @if (isset($users) && !empty($users))
            @foreach ($users as $key => $item)
            <tr>
            <td>{{$key + 1}}</td>
            <td>{{ucfirst($item->name)}}</td>
            <td>{{$item->email}}</td>
            <td>{{$item->mobile}}</td>
            <td>{{ $item->roles->implode('name', ', ') }}</td>
            <td>
                <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                    <input type="checkbox" class="custom-control-input" {{$item->active == 1 ? 'checked' : ''}}
                      onchange="change_status('{{$item->id}}', 'users', '#customSwitchGlow{{$key}}', 'active');" id="customSwitchGlow{{$key}}">
                    <label class="custom-control-label" for="customSwitchGlow{{$key}}">
                    </label>
                </div>
            </td>
            @if (strtolower($item->roles->implode('name', ', ')) === 'admin')
            @canany(['edit_users', 'delete_users'])
            <td>  <button disabled="disabled" class="btn-outline-info">Super Admin</button></td>
            @endcanany
            @else
                @canany(['edit_users', 'delete_users'])
                <td>

                    @include('shared._actions', [
                        "entity" => "users",
                        "id" => $item->id
                    ])

                </td>
                @endcanany
            @endif



            </tr>
            @endforeach
        @endif


    </tbody>
</table>

<div class="mx-auto" style="width: fit-content">{{ $users->links() }}</div>
</div>
