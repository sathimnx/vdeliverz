@extends('demand.layouts.main')

@section('content')
<section class="users-list-wrapper">


    <div class="users-list-table">
        <div class="card">
            <div class="card-header">
                <div class="card-text">
                    <h5 class="d-block card-text text-center mb-3">Total Providers - {{$providers->total()}}</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                @can('create_providers')
                                <a href="{{ route('demand.providers.create') }}" class="btn btn-warning float-left" class="btn btn-primary">Create Provider</a>
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
                </div><hr>
            <div class="card-content">
                <div class="card-body">
                    <!-- datatable start -->
                    <div class="table-responsive">
                        <table id="users-list-datatable" class="table zero-configuration">
                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Shop Name</th>
                                    <th>Opened</th>
                                    <th>Vendor Name</th>
                                    <th>Mobile</th>
                                    @role('admin')
                                    <th>status</th>
                                    <th>Create Cars</th>
                                    <th>Create Services</th>
                                    @endrole
                                    @canany(['edit_providers', 'delete_providers', 'view_providers'])
                                    <th>Actions</th>
                                    @endcanany

                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($providers) && !empty($providers))
                                    @foreach ($providers as $key => $item)
                                    <tr>
                                    <td>{{($providers->perPage() * ($providers->currentPage() - 1)) + $key + 1}}</td>
                                    <td>{{ucfirst($item->name)}}</td>
                                    <td>
                                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" {{$item->opened == 1 ? 'checked' : ''}}
                                              onchange="change_status('{{$item->id}}', 'providers', '#openedSwitchGlow{{$key}}', 'opened');" id="openedSwitchGlow{{$key}}">
                                            <label class="custom-control-label" for="openedSwitchGlow{{$key}}">
                                            </label>
                                        </div>
                                    </td>
                                    <td>{{ucfirst($item->user->name)}}</td>
                                    <td>{{ucfirst($item->user->mobile)}}</td>
                                    @role('admin')
                                    <td>
                                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" {{$item->active == 1 ? 'checked' : ''}}
                                              onchange="change_status('{{$item->id}}', 'providers', '#customSwitchGlow{{$key}}', 'active');" id="customSwitchGlow{{$key}}">
                                            <label class="custom-control-label" for="customSwitchGlow{{$key}}">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" {{$item->c_car == 1 ? 'checked' : ''}}
                                              onchange="change_status('{{$item->id}}', 'providers', '#customSwitchGlow1{{$key}}', 'c_car');" id="customSwitchGlow1{{$key}}">
                                            <label class="custom-control-label" for="customSwitchGlow1{{$key}}">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" {{$item->c_ser == 1 ? 'checked' : ''}}
                                              onchange="change_status('{{$item->id}}', 'providers', '#customSwitchGlow2{{$key}}', 'c_ser');" id="customSwitchGlow2{{$key}}">
                                            <label class="custom-control-label" for="customSwitchGlow2{{$key}}">
                                            </label>
                                        </div>
                                    </td>
                                    @endrole
                                        @canany(['edit_providers', 'delete_providers', 'view_providers'])
                                        <td>

                                            <div style="display: inline-flex">
                                                <a href="{{route('demand.providers.show', $item->id)}}" class="mr-1">
                                                    <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                                        <i class="bx bx-show"></i>
                                                    </button>
                                                </a>
                                                @can('edit_providers')
                                                    <a href="{{route('demand.providers.edit', $item->id)}}">
                                                        <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                                            <i class="bx bx-edit-alt"></i>
                                                        </button>
                                                    </a>
                                                @endcan

{{--                                                @can('delete_'.$entity)--}}
{{--                                                    <form action="{{route($entity.'.destroy', $id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this {{str_singular($entity)}}?')" method="post">--}}
{{--                                                        {{method_field('DELETE')}}--}}
{{--                                                        @csrf--}}
{{--                                                        <button type="submit" class="btn-outline-danger">--}}
{{--                                                            <i class="bx bx-trash-alt"></i>--}}
{{--                                                        </button>--}}

{{--                                                    </form>--}}
{{--                                                @endcan--}}
                                            </div>

                                        </td>
                                        @endcanany

                                    </tr>
                                    @endforeach
                                @endif


                            </tbody>
                        </table>
                        <div class="mx-auto" style="width: fit-content">{{ $providers->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
