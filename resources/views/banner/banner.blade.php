@extends('layouts.main')

@section('content')
<section class="users-list-wrapper">


    <div class="users-list-table">
        <div class="card">
            <div class="card-header">
                <div class="card-text">
                        <div class="row">

                            <div class="col-sm-6">
                                {{-- @can('create_banners')
                                <a onclick="showProductCategory('#productCategoryModal', null, null, null, null, null)" style="cursor: pointer;" class="btn btn-warning float-left text-white">Create Banner</a>
                                @endcan --}}

                            </div>
                            <div class="col-sm-6">
                                {{-- <div class="searchbar">
                                    <form>
                                        <div class="input-group">
                                          <input type="text" id="search" class="form-control" placeholder="Search">
                                        </div>
                                    </form>
                                </div> --}}
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
{{--                                    <th>Service Name</th>--}}
                                    {{-- <th>Shop Name</th> --}}
{{--                                    <th>Description</th>--}}

                                    <th>Image</th>
                                    <th>status</th>
                                    @canany(['edit_banners', 'delete_banners'])
                                    <th>Actions</th>
                                    @endcanany

                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($banners) && !empty($banners))
                                    @foreach ($banners as $key => $item)
                                    <tr>
                                    <td>{{1}}</td>
{{--                                    <td>{{ucfirst($item->type->name)}}</td>--}}
                                    {{-- <td>{{$item->shop->name}}</td> --}}
{{--                                    <td>{{$item->description}}</td>--}}
                                    <td><img src="{{$item->image}}" alt="" srcset="" width="20%"></td>
                                    <td>
                                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" {{$item->active == 1 ? 'checked' : ''}}
                                              onchange="change_status('{{$item->id}}', 'banners', '#customSwitchGlow{{$item->id}}', 'active');" id="customSwitchGlow{{$item->id}}">
                                            <label class="custom-control-label" for="customSwitchGlow{{$item->id}}">
                                            </label>
                                        </div>
                                    </td>

                                        @canany(['edit_banners', 'delete_banners'])
                                        <td>
                                            <div style="display: inline-flex">
                                                @can('edit_banners')
                                                    <button type="button" onclick='showProductCategory("#productCategoryModal", "{{$item->id}}")' class="btn-outline-info" data-icon="warning-alt">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </button>
                                                @endcan
                                                 @can('delete_banners')
                                                    <form action="{{route('banners.destroy', $item->id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this Banner?')" method="post">
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
                                    </tr>
                                    @endforeach
                                @endif


                            </tbody>
                        </table>
                        {{-- <div class="mx-auto" style="width: fit-content">{{ $banners->links() }}</div> --}}
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
    </div>
</section>
@include('banner._edit_modal')
@push('scripts')
    <script>
        function showProductCategory(modalId, id = null){
            if(id != null){
                $.ajax({
                    url: "banners/"+id+"/edit",
                    type: 'GET',
                    data: {"_token": '{{csrf_token()}}'},
                    success: function(response){
                        // console.log(response);
                        $(modalId+' form').attr('action', "{{ url('/banners') }}" + "/" + id);
                        $(modalId+' input[name="_method"]').val("PUT");
                        // $(modalId+' select option[value="'+response.shop_id+'"]').attr('selected', true);
                        // $(modalId+' textarea[name="description"]').val(response.description);
                        $(modalId+' img').attr('src', response.image);
                        $(modalId+' button[type="submit"]').text('Update');
                        $(modalId+' h4').text('Change Banner');
                        $(modalId).modal('show');
                    },
                    error:function(response){
                        toastr.error("Bad network","Please Refresh and Try!");
                    }
                })
            }else{
                $(modalId+' form').attr('action', "{{ url('/banners') }}");
                $(modalId+' input[name="_method"]').val("POST");
                // $(modalId+' input[name="name"]').val(null);
                // $(modalId+' textarea[name="description"]').val(null);
                // $(modalId+' select option[value=""]').attr('selected', true);
                $(modalId+' img').attr('src', '');
                $(modalId+' button[type="submit"]').text('Create');
                $(modalId+' h4').text('Create Banner');
                $(modalId).modal('show');
            }
        }
    </script>
@endpush
@endsection
