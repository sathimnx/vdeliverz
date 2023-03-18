@extends('layouts.main')

@section('content')
<?php $route = explode('.', Route::currentRouteName());     ?>
<section class="input-validation">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h2 class="">@if ($route[1] == 'create')
                Create
            @else
                Edit
            @endif Slot</h2>
          </div>
          <div class="card-content">
            <div class="card-body">
                @if ($route[1] == 'create')
                <form action="{{route('slots.store')}}" method="post">
                    @method('POST')
                @endif
                @if ($route[1] == 'edit')
                <form action="{{route('slots.update', $slot->id)}}" method="post">
                    @method('PUT')
                @endif

                    @csrf
                    <section id="form-control-repeater" class="mt-2">
                        <!-- phone repeater -->
                          <div class="">
                            <div class="card-header py-0">
                              <h4 class="card-title">Add Slot</h4>
                            </div>
                            <div class="card-content">
                              <div class="card-body">
                                      <div class="row justify-content-between" >
                                          @role('admin')
                                          <div class="col-md-12">
                                              <?php $shop_id = null; if(isset($slot)){$shop_id = $slot->shop->id;}  ?>
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Select Shop</label>
                                                <select class="select2 form-control" id="shop_id" name="shop_id"  data-placeholder="Select Shop..." data-validation-required-message="Select Shop" required>
                                                    <option value="">Select Shop</option>
                                                    @forelse ($shops as $shop)
                                                    <option value="{{$shop->id}}" {{$shop_id == $shop->id ? 'selected' : ''}}>{{$shop->name}}</option>
                                                    @empty

                                                    @endforelse

                                                </select>
                                                </div>
                                            </div>
                                          </div>

                                          @else
                                          <input type="hidden" name="shop_id" value="{{$shop->id}}">
                                          @endrole
                                          <div class="col-12">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Weekdays</label>
                                                <?php $weekdays = []; if(isset($slot->weekdays)){
                                                    $weekdays = explode(',', $slot->weekdays);
                                                }  ?>
                                                <select data-placeholder="Select days..." name="weekdays[]" autocomplete="new-password" class="select2-icons form-control select2-hidden-accessible" id="multiple-select2-icons" multiple="" data-select2-id="multiple-select2-icons" tabindex="-1" aria-hidden="true" data-validation-required-message="Select weekdays" required>
                                                   <option value="Monday" {{in_array('Monday', $weekdays) ? 'selected' : ''}}>Monday</option>
                                                   <option value="Tuesday" {{in_array('Tuesday', $weekdays) ? 'selected' : ''}}>Tuesday</option>
                                                   <option value="Wednesday" {{in_array('Wednesday', $weekdays) ? 'selected' : ''}}>Wednesday</option>
                                                   <option value="Thursday" {{in_array('Thursday', $weekdays) ? 'selected' : ''}}>Thursday</option>
                                                   <option value="Friday" {{in_array('Friday', $weekdays) ? 'selected' : ''}}>Friday</option>
                                                   <option value="Saturday" {{in_array('Saturday', $weekdays) ? 'selected' : ''}}>Saturday</option>
                                                   <option value="Sunday" {{in_array('Sunday', $weekdays) ? 'selected' : ''}}>Sunday</option>
                                                </select>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($route[1] == 'create')
                                        <div class="col-md-12 form-group">
                                            <section id="form-control-repeater">
                                                <!-- phone repeater -->
                                                  <div class="card">
                                                    <div class="card-header">
                                                      {{-- <h4 class="card-title">Add Product Stocks</h4> --}}
                                                    </div>
                                                    <div class="card-content">
                                                      <div class="card-body">
                                                        <div class="contact-repeater">
                                                          <div data-repeater-list="stocks">
                                                              <div class="row" data-repeater-item>
                                                                  <div class="col-md-5 col-12 form-group">
                                                                    <label for="">From</label>
                                                                    <input type="time" value="{{isset($slot->from) ? $slot->from : old('from')}}" class="form-control" name="from" placeholder="From" required>
                                                                  </div>
                                                                  <div class="col-md-5 col-12 form-group">
                                                                    <label for="">To</label>
                                                                    <input type="time" value="{{isset($slot->to) ? $slot->to : old('to')}}" class="form-control" name="to" placeholder="To" required>
                                                                  </div>
                                                                  <div class="col-md-2 col-12 form-group">
                                                                    <button class="btn btn-icon btn-danger rounded-circle mt-2" type="button" data-repeater-delete>
                                                                        <i class="bx bx-x" style="vertical-align: 0;"></i>
                                                                    </button>
                                                                    </div>
                                                              </div>
                                                          </div>
                                                          <div class="col-12 mb-2">
                                                            <div class="float-left">
                                                                <button class="btn btn-icon rounded-circle btn-primary" id="addNewStep" type="button" data-repeater-create>
                                                                    <i class="bx bx-plus" style="vertical-align: 0;"></i>
                                                                  </button>
                                                                  <span class="ml-1 font-weight-bold text-primary">ADD NEW</span>
                                                            </div>
                                                          </div>
                                                        </div>

                                                      </div>
                                                    </div>
                                                  </div>

                                                <!-- /phone repeater -->

                                          </section>
                                        </div>

                                        @else
                                        <div class="col-md-6 form-group">
                                            <div class="controls">
                                                <label for="">From</label>
                                              <input type="time" value="{{isset($slot->from) ? $slot->from : old('from')}}" class="form-control" name="from" placeholder="From" required>

                                            </div>
                                            </div>
                                              <div class="col-md-6 form-group">
                                            <div class="controls">
                                                <label for="">To</label>
                                              <input type="time" value="{{isset($slot->to) ? $slot->to : old('to')}}" class="form-control" name="to" placeholder="To" required>

                                            </div>
                                            </div>
                                        @endif


                                      </div>
                              </div>
                            </div>
                          </div>

                        <!-- /phone repeater -->

                  </section>

                </div>
                <div class="modal-footer">
                    @include('shared._submit', [
                        'entity' => 'slots',
                        'button' => $route[1] == 'create' ? 'Create' : 'Update',
                      ])
                </div>
            </form>
            </div>
         </div>
        </div>
    </div>
</div>


</section>
@endsection




