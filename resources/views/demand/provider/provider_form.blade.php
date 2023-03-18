@extends('demand.layouts.main')

@section('content')
<?php $route = explode('.', Route::currentRouteName());     ?>
<section class="input-validation">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h2 class="">@if ($route[2] == 'create')
                Create
            @else
                Edit
            @endif Provider</h2>
          </div>
          <div class="card-content">
            <div class="card-body">
                @if ($route[2] == 'create')
                <form class="form-horizontal" action="{{ route('demand.providers.store') }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
                    @method('POST')
                    @else
                    <form class="form-horizontal" @role('admin') action="{{ route('demand.providers.update', request()->provider) }}" @else action="{{ route('demand.provider-update', auth()->user()->provider->id) }}" @endrole method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
                        @method('PUT')
                    @endif
                    @csrf
                    @role('admin')
                    <h4 class="card-title">Provider Details</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Provider Name</label>
                                        <input type="text" class="form-control" placeholder="Vendor Name"
                                    value="{{isset($provider) ? $provider->user->name : old('username')}}" name="username"
                                            data-validation-required-message="This Vendor Name field is required" required>
                                    </div>
                                </div>
                                <div class="form-group" id="vendorEmail">
                                    <div class="controls">
                                        <label>E-mail</label>
                                        <input type="email" onkeyup="checkUniqueName('users', 'email', '#vendorEmail', this.value)" class="form-control" placeholder="Email"
                                             value="{{isset($provider) ? $provider->user->email : old('email')}}" name="email" required
                                            data-validation-required-message="This email field is required">

                                    </div>
                                </div>
                                @if(isset($provider) && $provider->user->image != null)
                                <div class="my-1">
                                <img src="{{ asset($provider->user->image) }}" width="30%" alt="" srcset="">
                                </div>
                                @endif

                                <fieldset class="form-group" id="profile_image">
                                <label for="storePANImage">Upload Profile Image </label>
                                <div class="input-group" >
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="storePANImage">Profile Image</span>
                                </div>
                                <div class="custom-file">
                                <input type="file"  class="custom-file-input"  name="profile_image" id="storePANImageUpload" aria-describedby="storePANImage">
                                <label class="custom-file-label" for="storePANImage">Choose file</label>
                                </div>
                                </div>
                                <div class="invalid-feedback">
                                <i class="bx bx-radio-circle"></i>
                                Image should be jpg, jpeg Format
                                </div>
                            </fieldset>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="storePassword">Password</label>
                                    <div class="controls">
                                      <input type="password" name="password" id="storePassword" class="form-control"
                                        placeholder="Password" @if ($route[1] == 'create')
                                        data-validation-required-message="This Password field is required"
                                        @endif>
                                    </div>
                                  </div>
                                  <div class="form-group">
                                    <label for="ConfirmPassword">Repeat password must match</label>
                                    <div class="controls">
                                      <input type="password" autocomplete="new-password" name="confirm_password" id="ConfirmPassword" data-validation-match-match="password"
                                        class="form-control"
                                        placeholder="Repeat Password" @if ($route[1] == 'create')
                                        data-validation-required-message="This Confirm Password field is required"
                                        @endif>
                                    </div>
                                  </div>
                                <div style="display: flex">
                                    <div class="form-group" style="width: 60%" id="vendorMobile">
                                        <label for="MobileNumber">Mobile Number with Country code</label>
                                        <div class="controls">
                                          <input type="text" value="{{isset($provider) ? $provider->user->mobile : old('mobile')}}" onkeyup="checkUniqueName('users', 'mobile', '#vendorMobile', this.value)" name="mobile"  class="form-control"
                                          data-validation-required-message="This mobile field is required"
                                            placeholder="Enter Your Mobile Number" required>
                                        </div>
                                      </div>

                                </div>
                            </div>
                        </div>
                        @endrole
                        <h4 class="card-title mt-3">Provider Shop Details</h4>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <div class="controls">
                                    <label>Weekdays</label>
                                <?php $weekdays = []; if(isset($provider->weekdays)){
                                    $weekdays = explode(',', $provider->weekdays);
                                }  ?>
                                <select data-placeholder="Select days..." name="weekdays[]" autocomplete="new-password" class="select2-icons form-control select2-hidden-accessible" id="multiple-select2-icons1" multiple="" data-validation-required-message="Choose weekdays" data-select2-id="multiple-select2-icons1" tabindex="-1" aria-hidden="true" required>
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
                          <div class="col-md-6 form-group">
                            <label for="">Opening Time</label>
                          <input type="time" value="{{isset($provider->opens_at) ? $provider->opens_at : old('opening_time')}}" class="form-control" name="opening_time" placeholder="From" required>
                          </div>
                          <div class="col-md-6 form-group">
                            <label for="">Closing Time</label>
                          <input type="time" value="{{isset($provider->closes_at) ? $provider->closes_at : old('closing_time')}}" class="form-control" name="closing_time" placeholder="To" required>
                          </div>
                        {{-- <div class="col-12">
                            <div class="form-group">
                                <label>Sub Services</label>
                                <select data-placeholder="Select Services..." name="services[]" autocomplete="new-password" class="select2-icons form-control select2-hidden-accessible" id="multiple-select2-icons" multiple="" data-select2-id="multiple-select2-icons" tabindex="-1" aria-hidden="true">
                                    @forelse ($sub_services as $sub_service)
                                    <option value="{{$sub_service->id}}" {{isset($provider) ? in_array($sub_service->id, $select) ? 'selected' : '' : ''}}>{{$sub_service->name}}</option>
                                    @empty

                                    @endforelse

                                  </select>
                            </div>
                        </div> --}}
                        <div class="col-12 col-sm-6">
                            @role('admin')
                            <div class="form-group">
                                <label>Service</label>
                                <select class="select2 form-control" id="type_id" name="type_id"  data-placeholder="Select Service Type..." style="pointer-events: none;">
                                        <option value="2" selected>Demand and Sevices</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <div class="controls">
                                    <label>Shop Street</label>
                                    <input type="text" class="form-control" placeholder="Shop Street"
                                value="{{isset($provider) ? $provider->street : old('street')}}" name="street"
                                        data-validation-required-message="This Shop Street field is required" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="controls">
                                    <label>Shop Area</label>
                                    <input type="text" class="form-control" placeholder="Shop Area"
                                value="{{isset($provider) ? $provider->area : old('area')}}" name="area"
                                        data-validation-required-message="This Shop Area field is required" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="controls">
                                    <label>Shop City</label>
                                    <input type="text" class="form-control" placeholder="Shop City"
                                value="{{isset($provider->name) ? $provider->city : old('city')}}" name="city"
                                        data-validation-required-message="This Shop city field is required" required>
                                </div>
                            </div>
                            @endrole
                            <fieldset class="form-group">
                                <label for="productDescription">Description</label>
                                <textarea class="form-control" name="description" id="productDescription" rows="4" placeholder="Descripton">{{ isset($provider->description) ? $provider->description : old('description') }}</textarea>
                            </fieldset>
                            <div class="d-flex justify-content-between">
                                <div class="form-group" style="width: 45%">
                                    <fieldset class="controls">
                                        <label for="serve_count">Delivery Radius</label>
                                      <div class="input-group">
                                        <input type="text" class="form-control"   value="{{ isset($provider->radius) ? $provider->radius : old('radius') }}" name="radius" id="radius" placeholder="Radius" aria-describedby="radius" data-validation-required-message="This Radius field is required" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="recipePreTimede654">KM</span>
                                          </div>
                                    </div>
                                    </fieldset>
                                  </div>
                                  @role('admin')
                                <div class="form-group" style="width: 48%">
                                    <fieldset>
                                        <label for="recipePreTimede">Delivery Charge (₹)</label>
                                      <div class="input-group">
                                        <input type="number" class="form-control" step=".0000000000000001" value="{{ isset($provider->delivery_charge) ? $provider->delivery_charge : old('delivery_charge') }}" name="delivery_charge" id="recipePreTime654" placeholder="Amount" aria-describedby="recipePreTimede"  required>
                                        <div class="input-group-append">
                                        <span class="input-group-text" id="recipePreTimede654">Per Booking</span>
                                      </div>
                                    </div>
                                    </fieldset>
                                </div>
                                @endrole
                            </div>

                        </div>
                        <div class="col-12 col-sm-6">
                            @role('admin')
                            <div class="form-group" id="vendorShopName">
                                <div class="controls">
                                    <label>Shop Name</label>
                                    <input type="text" class="form-control" placeholder="Shop Name"
                                value="{{isset($provider) ? $provider->name : old('shop_name')}}" name="shop_name" onkeyup="checkUniqueName('providers', 'name', '#vendorShopName', this.value)"
                                         data-validation-required-message="This Shop Name field is required" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="controls">
                                    <label>Shop E-mail</label>
                                    <input type="email" class="form-control" placeholder="Email"
                                        value="{{isset($provider->email) ? $provider->email : old('shop_email')}}" name="shop_email" required
                                        data-validation-required-message="This email field is required">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                              <div class="form-group" style="width: 45%">
                                <fieldset>
                                    <label for="recipePreTime">Latitude</label>
                                  <div class="input-group">

                                    <input type="number" class="form-control" step=".0000000000000001" value="{{ isset($provider->latitude) ? $provider->latitude : old('latitude') }}" name="latitude"  id="recipePreTime1" placeholder="Latitude" aria-describedby="recipePreTime"  required>
                                    {{-- <div class="input-group-append">
                                        <span class="input-group-text" id="recipePreTime1">Per KG</span>
                                      </div> --}}
                                </div>
                                </fieldset>
                              </div>
                              <div class="form-group" style="width: 45%">
                                <fieldset>
                                    <label for="recipePreTime">Longitude</label>
                                  <div class="input-group">
                                    <input type="number" class="form-control" step=".0000000000000001" value="{{ isset($provider->longitude) ? $provider->longitude : old('longitude') }}" name="longitude" id="recipePreTime" placeholder="Longitude" aria-describedby="recipePreTime"  required>
                                    {{-- <div class="input-group-append">
                                        <span class="input-group-text" id="recipePreTime">Per KG</span>
                                      </div> --}}
                                </div>
                                </fieldset>
                              </div>
                            </div>

                            {{-- <div class="d-flex justify-content-between"> --}}
                                {{-- <div class="form-group" style="width: 45%">
                                    <fieldset class="controls">
                                        <label for="serve_count">Rate Per Hour (₹)</label>
                                      <div class="input-group">

                                        <input type="text" class="form-control"   value="{{ isset($provider->hour_price) ? $provider->hour_price : old('hour_price') }}" name="hour_price" id="price" placeholder="Price" aria-describedby="price" data-validation-required-message="This Price field is required" required>
                                    </div>
                                    </fieldset>
                                  </div>
                                  <div class="form-group" style="width: 48%">
                                    <fieldset class="controls">
                                        <label for="serve_count">Rate per Job (₹)</label>
                                      <div class="input-group">

                                        <input type="text" class="form-control"   value="{{ isset($provider->job_price) ? $provider->job_price : old('job_price') }}" name="job_price" id="price" placeholder="Price" aria-describedby="price" data-validation-required-message="This Price field is required" required>
                                    </div>
                                    </fieldset>
                                  </div> --}}
                                {{-- <div class="form-group" style="width: 48%">
                                    <fieldset>
                                        <label for="recipePreTimede">Rate per Job</label>
                                      <div class="input-group">
                                        <input type="number" class="form-control" step=".0000000000000001" value="{{ isset($provider->delivery_charge) ? $provider->delivery_charge : old('delivery_charge') }}" name="delivery_charge" id="recipePreTime" placeholder="Delivery Charge" aria-describedby="recipePreTimede"  required>
                                        <div class="input-group-append">
                                        <span class="input-group-text" id="recipePreTimede">Per Order</span>
                                      </div>
                                    </div>
                                    </fieldset>
                                </div> --}}
                            {{-- </div> --}}
                            <div class="d-flex justify-content-between">

                                <div class="form-group" style="width: 45%">
                                    <fieldset>
                                        <label for="recipePreTimede12">{{ config('app.name') }} Comission (₹)</label>
                                      <div class="input-group">
                                        <input type="number" class="form-control" step=".0000000000000001" value="{{ isset($provider->comission) ? $provider->comission : old('comission') }}" name="comission" id="recipePreTime12" placeholder="Comission" aria-describedby="recipePreTimede12"  required>
                                        <div class="input-group-append">
                                        <span class="input-group-text" id="recipePreTimede12">Per Service</span>
                                      </div>
                                    </div>
                                    </fieldset>
                                </div>
                                {{-- <div class="form-group" style="width: 48%">
                                    <label for="">Service Type</label>
                                    <select class="form-control" id="size" name="assign"  autocomplete="new-password" data-placeholder="Select Size...">
                                        <option value="1" {{ isset($provider->assign) ? $provider->assign == 1 ? 'selected' : old('comission') : '' }}>Automatic</option>
                                        <option value="0" {{ isset($provider->assign) ? $provider->assign == 0 ? 'selected'  : old('comission') : '' }}>Manual</option>
                                    </select>
                                </div> --}}
                            </div>
                            @endrole

                            @if(isset($provider->image))
                          <div class="my-1">
                            <img src="{{$provider->image}} " width="30%" alt="" srcset="">
                          </div>
                          @endif
                            <fieldset id="productimageElement" class="form-group">
                                <div class="controls">
                                    <label for="categoryImage">Upload Shop Image</label>
                              <div class="input-group" >
                                <div class="input-group-prepend">
                                  <span class="input-group-text" id="productImage">Image</span>
                                </div>
                                <div class="custom-file">
                                <input type="file"  class="custom-file-input" name="shop_image" id="productImageUpload" aria-describedby="productImage" @if ($route[1] == 'create') data-validation-required-message="This Image field is required"
                                required
                                @endif >
                                <label class="custom-file-label" for="productImage">Choose file</label>
                                </div>
                              </div>
                                </div>
                            </fieldset>
                            @if(isset($provider->banner_image))
                          <div class="my-1">
                            <img src="{{$provider->banner_image}} " width="30%" alt="" srcset="">
                          </div>
                          @endif
                            <fieldset id="productimageElement1" class="form-group">
                              <div class="controls">
                                <label for="categoryImage">Upload Shop Banner Image</label>
                                <div class="input-group" >
                                  <div class="input-group-prepend">
                                    <span class="input-group-text" id="productImage1">Image</span>
                                  </div>
                                  <div class="custom-file">
                                  <input type="file"  class="custom-file-input" name="banner_image" id="productImageUpload1" aria-describedby="productImage"  @if ($route[1] == 'create') data-validation-required-message="This Image field is required"
                                  required
                                  @endif >
                                  <label class="custom-file-label" for="productImage1">Choose file</label>
                                  </div>
                                </div>
                              </div>
                            </fieldset>

                        </div>
                        @if ($route[2] == 'create')
                        <div class="col-md-12">
                            <section id="form-control-repeater" class="mt-2">
                              <!-- phone repeater -->
                                <div class="card">
                                  <div class="card-header">
                                    <h4 class="card-title">Select Services</h4>
                                  </div>
                                  <div class="card-content">
                                    <div class="card-body">
                                      <div class="contact-repeater">
                                        <div data-repeater-list="services">
                                            <div class="row justify-content-between" data-repeater-item>

                                                <div class="col-md-4 form-group">
                                                  <label for="">Services</label>
                                                  <select class="form-control" id="unit" name="sub_service_id"  autocomplete="new-password" data-placeholder="Select Variant...">
                                                      <option value="">Choose Service</option>
                                                     @forelse ($services as $item)
                                                     <optgroup label="{{ $item->name }}">
                                                     @foreach ($item->subServices as $sub)
                                                     <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                                     @endforeach
                                                    </optgroup>
                                                     @empty

                                                     @endforelse

                                                  </select>
                                                </div>

                                                <div class="col-md-3 form-group">
                                                  <label for="">Per Hour (₹)</label>
                                                <input type="number" step=".0000000000000001" class="form-control" value="{{old('hour_price')}}" name="hour_price" placeholder="Price" required>
                                                </div>
                                                <div class="col-md-3 form-group">
                                                  <label for="">Per Job (₹)</label>
                                                <input type="number" step=".0000000000000001" class="form-control" value="{{old('job_price')}}" name="job_price" placeholder="Price" required>
                                                </div>
                                                <div class="col-md-2 form-group">
                                                <button class="btn btn-icon btn-danger rounded-circle mt-2 float-left" type="button" data-repeater-delete>
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
                        @endif

                        @if (isset($selected) && !empty($selected))
                        @if (isset(auth()->user()->provider->id) && auth()->user()->provider->c_ser == 1 || auth()->user()->hasAnyRole('admin'))

                        <div class="col-md-12">
                          <section id="form-control-repeater" class="mt-2">
                            <!-- phone repeater -->
                              <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                  <h4 class="card-title">Services</h4>


                                  <div class="">

                                    <button class="btn btn-icon rounded-circle btn-primary" onclick="add_new_topping({{$provider->id}});" data-toggle="modal"  type="button">
                                        <i class="bx bx-plus" style="vertical-align: 0;"></i>
                                      </button>
                                      @if ($route[1] !== 'show')
                                      <span class="ml-1 font-weight-bold text-primary">ADD NEW Service</span>
                                      @endif
                                </div>

                                </div>
                                <div class="card-content">
                                  <div class="card-body">
                                    <div class="contact-repeater">
                                      <div data-repeater-list="services">
                                          @foreach ($selected as $key => $sel)
                                          <div class="row justify-content-between" data-repeater-item>
                                            <div class="col-md-4 form-group">
                                                <input type="hidden" name="provider_sub_id" value="{{ $sel->pivot->id }}">
                                              <label for="">Services</label>
                                              <select class="form-control" id="unit" name="sub_service_id"  autocomplete="new-password" data-placeholder="Select Service..." required>
                                                      {{-- <option value="">Choose Service</option> --}}
                                                     @forelse ($services as $item)
                                                     <optgroup label="{{ $item->name }}">
                                                     @foreach ($item->subServices as $sub)
                                                     <option value="{{ $sub->id }}" {{ $sel->pivot->sub_service_id == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                                     @endforeach
                                                    </optgroup>
                                                     @empty

                                                     @endforelse

                                                  </select>
                                            </div>

                                            <div class="col-md-3 form-group">
                                              <label for="">Per Hour (₹)</label>
                                            <input type="number" step=".0000000000000001" class="form-control" value="{{$sel->pivot->hour}}" name="hour_price" placeholder="Price" required>
                                            </div>
                                            <div class="col-md-3 form-group">
                                              <label for="">Per Job (₹)</label>
                                            <input type="number" step=".0000000000000001" class="form-control" value="{{$sel->pivot->job}}" name="job_price" placeholder="Price" required>
                                            </div>
                                            <div class="col-md-2 form-group m-auto">
                                                <label for=""></label>
                                                <button type="button" onclick="delete_topping({{$sel->pivot->id}});" data-toggle="modal" class="btn-outline-danger" style="margin-top: .3rem;">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>

                                          @endforeach

                                      </div>
                                      {{-- <div class="col-12 mb-2">
                                        <div class="float-left">
                                            <button class="btn btn-icon rounded-circle btn-primary" id="addNewStep" type="button" data-repeater-create>
                                                <i class="bx bx-plus" style="vertical-align: 0;"></i>
                                              </button>
                                              <span class="ml-1 font-weight-bold text-primary">ADD NEW</span>
                                        </div>
                                      </div> --}}
                                    </div>

                                  </div>
                                </div>
                              </div>

                            <!-- /phone repeater -->

                      </section>
                         </div>
                        @endif
                        @endif


                    </div>
                    @include('demand.shared._submit', [
                      'entity' => 'providers',
                      'button' => $route[2] == 'create' ? 'Create' : "Update"
                    ])
                </form>
            </div>
         </div>
        </div>
    </div>
</div>

@push('scripts')
            <script>

              function delete_topping(stock_id){

                  $('#toppingDeleteForm').attr('action', "{{ url('/demand/delete-sub-service') }}" + "/" + stock_id)
                  $('#deleteToppingModal').modal('show');
              }
              function add_new_topping(product_id){

                  $('#topping_modal_product_id').val(product_id);
                  $('#addNewToppingModal').modal('show');
              }

            </script>
        @endpush
@include('demand.provider.add_service')
{{-- @include('product._addTopping') --}}
</section>
@endsection
