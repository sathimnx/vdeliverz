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
                                <a href="{{ route('demand.provider-cars.create') }}" class="btn btn-warning float-left" class="btn btn-primary">Create Provider Cars</a>
                                @endif

                            </div>
                            <div class="col-sm-4">
                                @role('admin')
                                <label for="">Select Provider</label>
                                    <select name="" id="filterByShop" class="form-control select2" onchange="filterOrder('{{env('APP_URL')}}')">
                                        <option value="all" {{request()->type == 'all'  ? 'selected' : ''}}>All</option>
                                        @forelse ($providers as $item)
                                        <option value="{{$item->id}}" {{request()->provider == $item->id  ? 'selected' : ''}}>{{$item->name}}</option>
                                        @empty

                                        @endforelse
                                    </select>
                                    @else
                                    <input type="hidden" name="" id="filterByShop" value="{{auth()->user()->provider->id}}">
                                @endrole
                                </div>
                            <div class="col-sm-4">
                                <div class="searchbar">
                                    <form>
                                            <label for="">Search car</label>
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
                            var provider = $('#filterByShop').val();
                            window.location.href = url+'demand/provider-cars/'+provider+'/datas';
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
                                    <th>Provider Name</th>
                                    <th>Car Name</th>
                                    <th>Image</th>
                                    <th>status</th>
                                    <th>Actions</th>
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

                                        {{-- @canany(['edit_cars', 'delete_car-provider']) --}}
                                        <td>

                                            <div style="display: inline-flex">

                                            <a href="{{route('demand.provider-cars.edit', $item->id)}}">
                                                <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                                    <i class="bx bx-edit-alt"></i>
                                                </button>
                                                </a>

                                                <form action="{{route('demand.provider-cars.destroy', $item->id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this Car?')" method="post">
                                                    {{method_field('DELETE')}}
                                                    @csrf
                                                    <button type="submit" class="btn-outline-danger">
                                                        <i class="bx bx-trash-alt"></i>
                                                    </button>

                                                </form>
                                            </div>

                                        </td>
                                        {{-- @endcanany --}}

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
