@extends('demand.layouts.main')

@section('content')
<section class="users-list-wrapper">


    <div class="users-list-table">
        <div class="card">
            <div class="card-header">
                <div class="card-text">
                    <h5 class="d-block card-text text-center mb-3">Total Sub Services - {{$cars->total()}}</h5>
                        <div class="row">

                            <div class="col-sm-4">
                                @if (isset(auth()->user()->provider->id) && auth()->user()->provider->c_car == 1 || auth()->user()->hasAnyRole('admin'))
                                <a href="{{ route('demand.cars.create') }}" class="btn btn-warning float-left" class="btn btn-primary">Create Cars</a>
                               @endif

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
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
