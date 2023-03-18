@extends('layouts.main')

@section('content')
<section class="users-list-wrapper">

    <?php $route = explode('.', Route::currentRouteName());     ?>
    <div class="users-list-table">
        <div class="card">
            <div class="card-header">
                <div class="card-text">
                    <form action="{{route('manage-deliveries.store')}}" method="post">
                        @csrf

                        <div class="row ">
                            <div class="col-sm-12">
                                <div class="d-flex justify-content-between w-100">
                                    <div class="form-group" style="width: 35%">
                                        <fieldset >
                                            <label for="recipePreTimede">Delivery Person Charge</label>
                                          <div class="input-group">
                                            <input type="number" class="form-control" step=".0000000000000001" value="{{ isset($delivery_boy_charge) ? $delivery_boy_charge : old('delivery_boy_charge') }}" name="delivery_boy_charge" id="deliveryCharge" placeholder="Delivery Charge" aria-describedby="recipePreTimede" required  disabled>
                                            <div class="input-group-append">
                                            <span class="input-group-text" id="recipePreTimede">Per Order</span>
                                          </div>
                                        </div>
                                        </fieldset>
                                    </div>
                                    <div class="form-group" style="width: 35%">
                                        <fieldset >
                                        <label for="recipePreTimede">Delivery Person Points</label>
                                          <div class="input-group">
                                            <input type="number" class="form-control" step=".0000000000000001" value="{{ isset($points) ? $points : old('points') }}" name="points" id="deliveryPoints" placeholder="Delivery Points" aria-describedby="recipePreTimede" required disabled>
                                            <div class="input-group-append">
                                            <span class="input-group-text" id="recipePreTimede">Per Order</span>
                                          </div>
                                        </div>
                                        </fieldset>

                                    </div>
                                    <div class="form-group m-0" style="width: 20%">
                                        <button type="button" id="chargeEditBtn" class="btn btn-warning glow mt-2">Edit
                                        </button>
                                        <button type="submit" id="chargeUpdateBtn" class="btn btn-warning glow mt-2 d-none">Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    @push('scripts')
                        <script>
                            $('#chargeEditBtn').on('click', function(){
                                $(this).addClass('d-none');
                                $('#deliveryCharge').attr('disabled', false);
                                $('#deliveryPoints').attr('disabled', false);
                                $('#chargeUpdateBtn').removeClass('d-none');
                            })
                        </script>
                    @endpush
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
                                    <th>Delivery Person</th>
                                    <th>Mobile number</th>
                                    <th>Orders Delivered</th>
                                    <th>Work type</th>
                                    @can('view_orders')
                                    <th>Actions</th>
                                    @endcan

                                </tr>
                            </thead>
                            <tbody>

                                @if (isset($users) && !empty($users))
                                    @foreach ($users as $k => $item)
                                    <tr>
                                    <td>{{($users->perPage() * ($users->currentPage() - 1)) + $k + 1}}</td>
                                    <td>{{$item->name}}</td>
                                    <td>{{$item->mobile}}</td>
                                    <td>{{$item->delivered_count}}</td>
                                    <td>{{$item->delivery_type == 1 ? 'Monthly' : 'Part time'}}</td>
                                    <td>
                                        <a href="{{route('manage-deliveries.show', $item->id)}}" class="mr-1">
                                            <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                                <i class="bx bx-show"></i>
                                            </button>
                                        </a>
                                    </td>

                                    </tr>
                                    @endforeach
                                @endif


                            </tbody>
                        </table>
                        <div class="mx-auto" style="width: fit-content">{{ $users->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
