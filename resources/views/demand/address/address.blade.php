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
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="users-list-role">Role</label>
                    <fieldset class="form-group">
                        <select class="form-control" id="users-list-role">
                            <option value="">Any</option>
                            <option value="User">User</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </fieldset>
                </div> --}}
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
                </div>
                <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-center">
                    <button type="reset" class="btn btn-primary btn-block glow users-list-clear mb-0">Clear</button>
                </div> --}}
            </div>
        </form>
    </div>

    <div class="users-list-table">
        <div class="card">
            <div class="card-header">
                <div class="card-text">
                        <div class="row">
                        <div class="col-sm-6">
                            {{-- <h4 class="card-title">List</h4> --}}

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
                            {{-- <div class="col-sm-6">
                                @can('create_users')
                                <a href="{{ route('users.create') }}" class="btn btn-warning float-right" class="btn btn-primary">Create User</a>
                                @endcan

                            </div> --}}

                        </div>
                        </div>
                </div><hr>
            <div class="card-content">
                <div class="card-body">
                    <!-- datatable start -->
                    <div class="table-responsive">
                        <table id="users-list-datatable" class="table zero-configuration">
                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Customer</th>
                                    <th>Mobile</th>
                                    <th>Address</th>
                                    <th>Landmark</th>
                                    <th>Actions</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($addresses) && !empty($addresses))
                                    @foreach ($addresses as $key => $item)
                                    <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{ucfirst($item->user->name)}}</td>
                                    <td>{{$item->user->mobile}}</td>
                                    <td>{{$item->address}}</td>
                                    <td>{{$item->landmark}}</td>
                                    <td class="text-center">
                                        <form action="{{route('addresses.destroy', $item->id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this Address ?')" method="post">
                                            {{method_field('DELETE')}}
                                            @csrf
                                            <button type="submit" class="btn-outline-danger">
                                                <i class="bx bx-trash-alt"></i>
                                            </button>

                                        </form>
                                    </td>



                                    </tr>
                                    @endforeach
                                @endif


                            </tbody>
                        </table>
                        <div class="mx-auto" style="width: fit-content">{{ $addresses->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
