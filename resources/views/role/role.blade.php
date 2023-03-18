@extends('layouts.main')

@section('content')
<section class="users-list-wrapper">
    {{-- <div class="users-list-filter px-1">
        <form>
            <div class="row border rounded py-2 mb-2">
                <div class="col-12 col-sm-6 col-lg-3">
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
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
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
                </div>
            </div>
        </form>
    </div> --}}
    
    <div class="users-list-table">
        <div class="card">
            <div class="card-header">
                <div class="card-text">  
                        <div class="row">
                        <div class="col-sm-6">  
                            {{-- <h4 class="card-title">List</h4> --}}
                           
                            </div> 
                            <div class="col-sm-6">
                                @can('create_roles')
                                <a href="{{ route('roles.create') }}" class="btn btn-warning float-right" class="btn btn-primary">Create Role</a>
                                @endcan
                            </div>
                            
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
                                    <th>Role Name</th>
                                    @canany(['edit_roles', 'delete_roles'])
                                        <th>Actions</th>
                                    @endcanany
                                    
                                   
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($roles) && !empty($roles))
                                    @foreach ($roles as $key => $item)
                                    <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{ucfirst($item->name)}}</td>
                                        {{-- <td> {{$item->Permissions()->pluck('name')}}   </td> --}}
                                        @if (strtolower($item->name) === "admin")
                                        @can(['edit_roles', 'delete_roles'])
                                        <td>  <button disabled="disabled" class="btn-outline-info">Super Admin</button></td>
                                        @endcan
                                        
                                        @else
                                        @canany(['edit_roles', 'delete_roles'])
                                        <td>
                                            @include('shared._actions', [
                                                "entity" => "roles",
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
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection