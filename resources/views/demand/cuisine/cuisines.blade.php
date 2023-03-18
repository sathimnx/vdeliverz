@extends('layouts.main')

@section('content')
<section class="users-list-wrapper">


    <div class="users-list-table">
        <div class="card">
            <div class="card-header">
                <div class="card-text">
                    <h5 class="d-block card-text text-center mb-3">Total Cuisines - {{$cuisines->total()}}</h5>
                        <div class="row">

                            <div class="col-sm-6">
                                @can('create_product-categories')
                                <a onclick="showProductCategory('#productCategoryModal', null, null)" style="cursor: pointer;" class="btn btn-warning float-left text-white">Create Cuisine</a>
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
                                    <th>Name</th>
                                    <th>status</th>
                                    {{-- @canany(['edit_cuisines', 'delete_cuisines'])
                                    <th>Actions</th>
                                    @endcanany --}}

                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($cuisines) && !empty($cuisines))
                                    @foreach ($cuisines as $key => $item)
                                    <tr>
                                    <td>{{($cuisines->perPage() * ($cuisines->currentPage() - 1)) + $key + 1}}</td>
                                    <td>{{ucfirst($item->name)}}</td>
                                    <td>
                                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" {{$item->active == 1 ? 'checked' : ''}}
                                              onchange="change_status('{{$item->id}}', 'cuisines', '#customSwitchGlow{{$key}}', 'active');" id="customSwitchGlow{{$key}}">
                                            <label class="custom-control-label" for="customSwitchGlow{{$key}}">
                                            </label>
                                        </div>
                                    </td>

                                        {{-- @canany(['edit_cuisines', 'delete_cuisines'])
                                        <td>
                                            <div style="display: inline-flex"> --}}
                                                {{-- @can('edit_cuisines')
                                                    <button type="button" onclick="showProductCategory('#productCategoryModal', '{{$item->id}}', '{{$item->name}}')" class="btn-outline-info" data-icon="warning-alt">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </button>
                                                @endcan --}}
                                                {{-- @can('delete_cuisines')
                                                    <form action="{{route('cuisines.destroy', $item->id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this Product Category?')" method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-outline-danger">
                                                            <i class="bx bx-trash-alt"></i>
                                                        </button>

                                                    </form>
                                                @endcan --}}
                                                {{-- </div>
                                        </td>
                                        @endcanany --}}
                                    </tr>
                                    @endforeach
                                @endif


                            </tbody>
                        </table>
                        <div class="mx-auto" style="width: fit-content">{{ $cuisines->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
    </div>
</section>
@include('cuisine._edit_modal')
@push('scripts')
    <script>
        function showProductCategory(modalId, id = null, name = null){
            if(id != null){
                $(modalId+' form').attr('action', "{{ url('/cuisines') }}" + "/" + id);
                $(modalId+' input[name="_method"]').val("PUT");
                $(modalId+' input[name="name"]').val(name);
                $(modalId+' button[type="submit"]').text('Update');
                $(modalId+' h4').text('Edit Cuisine');
                $(modalId).modal('show');
            }else{
                $(modalId+' form').attr('action', "{{ url('/cuisines') }}");
                $(modalId+' input[name="_method"]').val("POST");
                $(modalId+' input[name="name"]').val(name);
                $(modalId+' button[type="submit"]').text('Create');
                $(modalId+' h4').text('Add Cuisine');
                $(modalId).modal('show');
            }
        }
    </script>
@endpush
@endsection
