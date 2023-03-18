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
                    <td>{{ $key + 1 }}</td>
                    <td>{{ ucfirst($item->name) }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->mobile }}</td>
                    <td>{{ $item->roles->implode('name', ', ') }}</td>
                    <td>
                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                            <input type="checkbox" class="custom-control-input"
                                {{ $item->active == 1 ? 'checked' : '' }}
                                onchange="change_status('{{ $item->id }}', 'users', '#customSwitchGlow{{ $key }}', 'active');"
                                id="customSwitchGlow{{ $key }}">
                            <label class="custom-control-label" for="customSwitchGlow{{ $key }}">
                            </label>
                        </div>
                    </td>
                    @if (strtolower($item->roles->implode('name', ', ')) === 'admin')
                        @canany(['edit_users', 'delete_users'])
                            <td> <button disabled="disabled" class="btn-outline-info">Super Admin</button></td>
                        @endcanany
                    @else
                        @canany(['edit_users', 'delete_users'])
                            <td>

                                <div style="display: inline-flex">
                                    <a href="{{ route('users.show', $item->id) }}" class="mr-1">
                                        <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                            <i class="bx bx-show"></i>
                                        </button>
                                    </a>
                                    @can('edit_users')
                                        <a href="{{ route('users.edit', $item->id) }}">
                                            <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                                <i class="bx bx-edit-alt"></i>
                                            </button>
                                        </a>
                                    @endcan

                                    {{-- @can('delete_users')
                    <form action="{{route('users.destroy', $id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this User?')" method="post">
                        {{method_field('DELETE')}}
                        @csrf
                        <button type="submit" class="btn-outline-danger">
                            <i class="bx bx-trash-alt"></i>
                        </button>

                    </form>
                @endcan --}}
                                </div>

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
