@extends('demand.layouts.main')

@section('content')
<section class="users-list-wrapper">


    <div class="users-list-table">
        <div class="card">
            <div class="card-header">
                <div class="card-text">
                    <h5 class="d-block card-text text-center mb-3">Total Sub Services - {{$sub_services->total()}}</h5>
                        <div class="row">

                            <div class="col-sm-4">
                                @can('create_sub-services')
                                <a href="{{ route('demand.sub-services.create') }}" class="btn btn-warning float-left" class="btn btn-primary">Create Service</a>
                                @endcan

                            </div>
                            <div class="col-sm-4">
{{--                                @role('admin')--}}
{{--                                <label for="">Select Shop</label>--}}
{{--                                <select name="" id="filterByShop" class="form-control select2" onchange="filterOrder('{{env('APP_URL')}}')">--}}
{{--                                    <option value="all" {{request()->type == 'all'  ? 'selected' : ''}}>All</option>--}}
{{--                                    @forelse ($shops as $item)--}}
{{--                                        <option value="{{$item->id}}" {{request()->shop == $item->id  ? 'selected' : ''}}>{{$item->name}}</option>--}}
{{--                                    @empty--}}

{{--                                    @endforelse--}}
{{--                                </select>--}}
{{--                                @else--}}
{{--                                    <input type="hidden" name="" id="filterByShop" value="{{auth()->user()->shop->id}}">--}}
{{--                                    @endrole--}}
                                </div>
                            <div class="col-sm-4">
                                <div class="searchbar">
                                    <form>
                                            <label for="">Search</label>
                                        <div class="input-group">
                                            <input type="text" id="search" class="form-control" placeholder="Search">
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                        </div>
                @push('scripts')
                    <script>
                        function filterOrder(url){
                            var type = $('#filterByType').val();
                            var shop = $('#filterByShop').val();
                            window.location.href = url+'products/'+shop+'/datas';
                        }
                    </script>
                @endpush
                </div><hr>
            <div class="card-content">
                <div class="card-body">
                    <!-- datatable start -->
                    <div class="table-responsive">
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
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
