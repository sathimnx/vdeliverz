@extends('demand.layouts.main')

@section('content')
<section class="users-list-wrapper">


    <div class="users-list-table">
        <div class="card">
            <div class="card-header">
                <div class="card-text">
                        <div class="row">

                            <div class="col-sm-6">
                                @can('create_categories')
                                <a onclick="showProductCategory('#productCategoryModal', null, null, null, null)" style="cursor: pointer;" class="btn btn-warning float-left text-white">Create Service</a>
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
                                    <th>Service Name</th>
                                    {{-- <th>Category Name</th> --}}
                                    <th>Icon</th>
                                    <th>Order</th>
                                    <th>status</th>
                                    @canany(['edit_categories', 'delete_categories'])
                                    <th>Actions</th>
                                    @endcanany

                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($services) && !empty($services))
                                    @foreach ($services as $key => $item)
                                    <tr>
                                    <td>{{($services->perPage() * ($services->currentPage() - 1)) + $key + 1}}</td>
                                    {{-- <td>Demand and Services</td> --}}
                                    <td>{{$item->name}}</td>
                                    <td><img src="{{$item->image}}" alt="" srcset="" width="15%"></td>
                                    <td><input type="number" value="{{ $item->order }}" onchange="changeOrder('{{ $item->id }}', this.value)" id="categoryOrder{{ $item->id }}" maxlength="1" class="form-control w-50" ></td>
                                    <td>
                                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" {{$item->active == 1 ? 'checked' : ''}}
                                              onchange="change_status('{{$item->id}}', 'services', '#customSwitchGlow{{$item->id}}', 'active');" id="customSwitchGlow{{$item->id}}">
                                            <label class="custom-control-label" for="customSwitchGlow{{$item->id}}">
                                            </label>
                                        </div>
                                    </td>
                                        {{-- @if ($item->id != 1) --}}
                                        @canany(['edit_services', 'delete_services'])
                                        <td>
                                            <div style="display: inline-flex">
                                                @can('edit_services')
                                                    <button type="button" onclick="showProductCategory('#productCategoryModal', '{{$item->id}}', '{{$item->name}}', '{{$item->image}}', null)" class="btn-outline-info" data-icon="warning-alt">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </button>
                                                @endcan
                                                @can('delete_slots')
                                                    <form action="{{route('demand.services.destroy', $item->id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this Service?')" method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-outline-danger">
                                                            <i class="bx bx-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                </div>
                                        </td>
                                        @endcanany
                                        {{-- @endif --}}

                                    </tr>
                                    @endforeach
                                @endif


                            </tbody>
                        </table>
                        <div class="mx-auto" style="width: fit-content">{{ $services->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
    </div>
</section>
@include('demand.category._edit_modal')
@push('scripts')
    <script>
        function showProductCategory(modalId, id = null, name = null, image = null, shop = null){
            if(id != null){
                $.ajax({
                    url: "services/"+id+"/edit",
                    type: 'GET',
                    data: {"_token": '{{csrf_token()}}'},
                    success: function(response){
                        // console.log(response);
                        $(modalId+' form').attr('action', "{{ url('/demand/services') }}" + "/" + id);
                        $(modalId+' input[name="_method"]').val("PUT");
                        $(modalId+' input[name="name"]').val(response.name);
                        // $(modalId+' textarea[name="description"]').val(response.description);
                        $(modalId+' img').attr('src', response.image);
                        $(modalId+' button[type="submit"]').text('Update');
                        $(modalId+' h4').text('Edit Service');
                        $(modalId).modal('show');
                    },
                    error:function(response){
                        toastr.error("Bad network","Please Refresh and Try!");
                    }
                })
            }else{
                $(modalId+' form').attr('action', "{{ url('/demand/services') }}");
                $(modalId+' input[name="_method"]').val("POST");
                $(modalId+' input[name="name"]').val(name);
                $(modalId+' img').attr('src', image);
                $(modalId+' button[type="submit"]').text('Create');
                $(modalId+' h4').text('Create Service');
                $(modalId).modal('show');
            }
        }

        function changeOrder(id, num){
            $.ajax({
                    url: "services/"+id+"/order",
                    type: 'GET',
                    data: {"_token": '{{csrf_token()}}', num:num},
                    success: function(response){
                    console.log(response);
                        $('#categoryOrder'+response.id).val(response.order);
                        toastr.success("Order Changed!");
                    },
                    error:function(response){
                        toastr.error("Bad network","Please Refresh and Try!");
                    }
                })
        }
    </script>
@endpush
@endsection
