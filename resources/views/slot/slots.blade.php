@extends('layouts.main')

@section('content')
<section class="users-list-wrapper">


    <div class="users-list-table">
        <div class="card">
            <div class="card-header">
                <div class="card-text">
                        <div class="row">

                            <div class="col-sm-6">
                                @can('create_slots')
                                <a href="{{route('slots.create')}}" style="cursor: pointer;" class="btn btn-warning float-left text-white">Create Slot</a>
                                @endcan

                            </div>
                            <div class="col-sm-6">
                                @role('admin')
                                <label for="">Select Shop</label>
                                <select name="" id="filterByType" onchange="filterSlot('{{env('APP_URL')}}')" class="select2 form-control">
                                   <option value="all">All</option>
                                    @forelse ($shops as $item)
                                        <option value="{{$item->id}}" {{isset(request()->id) ? request()->id == $item->id ? 'selected' : '' : ''}}>{{$item->name}}</option>
                                   @empty

                                   @endforelse
                                </select>
                                </div>
                                @push('scripts')
                                    <script>
                                        function filterSlot(url){
                                            console.log(url);
                                            var id = $('#filterByType').val();
                                            window.location.href = url+'slots/'+id+'/filter';
                                        }
                                    </script>
                                @endpush
                                @else
                                    <input type="hidden" name="shop_id" value="{{$shop->id}}">
                                @endrole
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
                                    <th>Weekdays</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>status</th>
                                    @canany(['edit_slots', 'delete_slots'])
                                    <th>Actions</th>
                                    @endcanany

                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($slots) && !empty($slots))
                                    @foreach ($slots as $key => $item)
                                    <tr>
                                    <td>{{($slots->perPage() * ($slots->currentPage() - 1)) + $key + 1}}</td>
                                        <td><a href="{{route('shops.show', $item->shop->id)}}" class="mr-1">{{ucfirst($item->shop->name)}}</a></td>
                                    <td>{{$item->weekdays}}</td>
                                    <td>{{$item->from_time}}</td>
                                    <td>{{$item->to_time}}</td>
                                    <td>
                                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" {{$item->active == 1 ? 'checked' : ''}}
                                              onchange="change_status('{{$item->id}}', 'slots', '#customSwitchGlow{{$key}}', 'active');" id="customSwitchGlow{{$key}}">
                                            <label class="custom-control-label" for="customSwitchGlow{{$key}}">
                                            </label>
                                        </div>
                                    </td>

                                        @canany(['edit_slots', 'delete_slots'])
                                        <td>
                                            <div style="display: inline-flex">
                                                @can('edit_slots')
                                                <a href="{{route('slots.edit', $item->id)}}">
                                                    <button   class="btn-outline-info" data-icon="warning-alt">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </button>
                                                </a>
                                                @endcan
                                                @can('delete_slots')
                                                    <form action="{{route('slots.destroy', $item->id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this Slot?')" method="post">
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
                        <div class="mx-auto" style="width: fit-content">{{ $slots->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
    </div>
</section>
{{-- @include('slot._edit_modal')
@push('scripts')
    <script>
        function showProductCategory(modalId, id = null, from = null, to = null, shop = null){
            if(id != null){
                console.log(shop);
                $(modalId+' form').attr('action', "{{ url('/slots') }}" + "/" + id);
                $(modalId+' input[name="_method"]').val("PUT");
                $(modalId+' input[name="from"]').val(from);
                $(modalId+' input[name="to"]').val(to);
                $(modalId+' select[name="shop_id"]').val(shop);
                $(modalId+' option[value='+shop+']').attr('selected', true);
                $(modalId+' button[type="submit"]').text('Update');
                $(modalId+' h4').text('Edit Slot');
                $(modalId).modal('show');
            }else{
                $(modalId+' form').attr('action', "{{ url('/slots') }}");
                $(modalId+' input[name="_method"]').val("POST");
                $(modalId+' input[name="from"]').val(from);
                $(modalId+' input[name="to"]').val(to);
                $(modalId+' select[name="shop_id"]').val(shop);
                $(modalId+' select option[value='+shop+']').attr('selected', true);
                $(modalId+' button[type="submit"]').text('Create');
                $(modalId+' h4').text('Create Slot');
                $(modalId).modal('show');
            }
        }
    </script>
@endpush --}}
@endsection
