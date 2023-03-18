@extends('layouts.main')

@section('content')
<section class="users-list-wrapper">


    <div class="users-list-table">
        <div class="card">
            <div class="card-header">
                <div class="card-text">
                        <div class="row">

                            <div class="col-sm-6">
{{--                                @can('create_categories')--}}
                                <a onclick="showProductCategory('#productCategoryModal', null, null, null, null)" style="cursor: pointer;" class="btn btn-warning float-left text-white">Create Topping Category</a>
{{--                                @endcan--}}

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
                                    <th>Category Name</th>
{{--                                    <th>Icon</th>--}}
                                    <th>status</th>
{{--                                    @canany(['edit_categories', 'delete_categories'])--}}
                                    <th>Actions</th>
{{--                                    @endcanany--}}

                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($titles) && !empty($titles))
                                    @foreach ($titles as $key => $item)
                                    <tr>
                                    <td>{{($titles->perPage() * ($titles->currentPage() - 1)) + $key + 1}}</td>
                                    <td>{{ucfirst($item->shop->name)}}</td>
                                    <td>{{$item->name}}</td>
{{--                                    <td><img src="{{$item->image}}" alt="" srcset="" width="40%"></td>--}}
                                    <td>
                                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" {{$item->active == 1 ? 'checked' : ''}}
                                              onchange="change_status('{{$item->id}}', 'titles', '#customSwitchGlow{{$item->id}}', 'active');" id="customSwitchGlow{{$item->id}}">
                                            <label class="custom-control-label" for="customSwitchGlow{{$item->id}}">
                                            </label>
                                        </div>
                                    </td>

{{--                                        @canany(['edit_categories', 'delete_categories'])--}}
                                        <td>
                                            <div style="display: inline-flex">
{{--                                                @can('edit_categories')--}}
                                                    <button type="button" onclick="showProductCategory('#productCategoryModal', '{{$item->id}}', '{{$item->name}}', '{{$item->image}}', '{{$item->shop->id}}')" class="btn-outline-info" data-icon="warning-alt">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </button>
{{--                                                @endcan--}}
{{--                                                 @can('delete_slots')--}}
                                                    <form action="{{route('titles.destroy', $item->id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this Topping Category?')" method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-outline-danger">
                                                            <i class="bx bx-trash-alt"></i>
                                                        </button>

                                                    </form>
{{--                                                @endcan--}}
                                                </div>
                                        </td>
{{--                                        @endcanany--}}
                                    </tr>
                                    @endforeach
                                @endif


                            </tbody>
                        </table>
                        <div class="mx-auto" style="width: fit-content">{{ $titles->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
    </div>
</section>
@include('topping._edit_modal')
@push('scripts')
    <script>
        function showProductCategory(modalId, id = null, name = null, image = null, shop = null){
            if(id != null){
                $.ajax({
                    url: "titles/"+id+"/edit",
                    type: 'GET',
                    data: {"_token": '{{csrf_token()}}'},
                    success: function(response){
                        // console.log(response);
                        $(modalId+' form').attr('action', "{{ url('/titles') }}" + "/" + id);
                        $(modalId+' input[name="_method"]').val("PUT");
                        $(modalId+' input[name="name"]').val(response.name);
                        $(modalId+' select option[value="'+response.shop_id+'"]').attr('selected', true);
                        // $(modalId+' img').attr('src', response.image);
                        $(modalId+' button[type="submit"]').text('Update');
                        $(modalId+' h4').text('Edit Category');
                        $(modalId).modal('show');
                    },
                    error:function(response){
                        toastr.error("Bad network","Please Refresh and Try!");
                    }
                })
            }else{
                $(modalId+' form').attr('action', "{{ url('/titles') }}");
                $(modalId+' input[name="_method"]').val("POST");
                $(modalId+' input[name="name"]').val(name);
                $(modalId+' select option').attr('selected', false);
                // $(modalId+' img').attr('src', image);
                $(modalId+' button[type="submit"]').text('Create');
                $(modalId+' h4').text('Create Category');
                $(modalId).modal('show');
            }
        }
    </script>
@endpush
@endsection
