@extends('layouts.main')

@section('content')
    <section class="users-list-wrapper">
        <div class="users-list-filter px-1">
            <form>
                <div class="row border rounded py-2 mb-2">
                    {{-- <div class="col-12 col-sm-6 col-lg-3">
                    <label for="users-list-verified">Verified</label>
                    <fieldset class="form-group">
                        <select class="form-control" id="users-list-verified">
                            <option value="">Any</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </fieldset>
                </div> --}}
                    {{-- <div class="col-12 col-sm-6 col-lg-3">
                    <label for="users-list-role">Role</label>
                    <fieldset class="form-group">
                        <select class="form-control" id="users-list-role" onchange="getRoleUsers(this.value, '{{env('APP_URL')}}')">
                            <option value="" {{request()->id == null ? 'selected' : null}}>Any</option>
                            @foreach ($roles as $item)
                            <option value="{{$item->id}}" {{request()->id == $item->id ? 'selected' : null}}>{{$item->name}}</option>
                            @endforeach
                        </select>
                    </fieldset>
                </div> --}}
                    @push('scripts')
                        <script>
                            function getRoleUsers(id, url) {
                                if (id) {
                                    window.location.href = url + '/users/role/' + id;
                                } else {
                                    window.location.href = url + '/users/';
                                }
                            }
                        </script>
                    @endpush
                    {{-- <div class="col-12 col-sm-6 col-lg-3">
                    <label for="users-list-status">Status</label>
                    <fieldset class="form-group">
                        <select class="form-control" id="users-list-status">
                            <option value="">Any</option>
                            <option value="Active">Active</option>
                            <option value="Close">Close</option>
                            <option value="Banned">Banned</option>
                        </select>
                    </fieldset>
                </div> --}}
                    {{-- <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-center">
                    <button type="reset" class="btn btn-primary btn-block glow users-list-clear mb-0">Search</button>
                </div> --}}
                </div>
            </form>
        </div>

        <div class="users-list-table">
            <div class="card">
                <div class="card-header">
                    <div class="card-text">
                        <h5 class="d-block card-text text-center mb-3">Total Users - {{ $users->total() }}</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                @can('create_users')
                                    <a href="{{ route('users.create') }}" class="btn btn-warning float-left"
                                        class="btn btn-primary">Create User</a>
                                @endcan
                            </div>
                            <div class="col-sm-6">
                                <div class="searchbar">
                                    <form>
                                        <div class="input-group">
                                            <input type="text" id="search" class="form-control" placeholder="Search">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="card-content">
                    <div class="card-body">
                        <!-- datatable start -->
                        <div class="table-responsive">
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
                                                    <div
                                                        class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                                        <input type="checkbox" class="custom-control-input"
                                                            {{ $item->active == 1 ? 'checked' : '' }}
                                                            onchange="change_status('{{ $item->id }}', 'users', '#customSwitchGlow{{ $key }}', 'active');"
                                                            id="customSwitchGlow{{ $key }}">
                                                        <label class="custom-control-label"
                                                            for="customSwitchGlow{{ $key }}">
                                                        </label>
                                                    </div>
                                                </td>
                                                @if (strtolower($item->roles->implode('name', ', ')) === 'admin')
                                                    @canany(['edit_users', 'delete_users'])
                                                        <td> <button disabled="disabled" class="btn-outline-info">Super
                                                                Admin</button></td>
                                                    @endcanany
                                                @else
                                                    @canany(['edit_users', 'delete_users'])
                                                        <td>

                                                            <div style="display: inline-flex">
                                                                <a href="{{ route('users.show', $item->id) }}" class="mr-1">
                                                                    <button type="submit" class="btn-outline-info"
                                                                        data-icon="warning-alt">
                                                                        <i class="bx bx-show"></i>
                                                                    </button>
                                                                </a>
                                                                @can('edit_users')
                                                                    <a href="{{ route('users.edit', $item->id) }}">
                                                                        <button type="submit" class="btn-outline-info"
                                                                            data-icon="warning-alt">
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
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
