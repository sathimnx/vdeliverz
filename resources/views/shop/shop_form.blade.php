@extends('layouts.main')

@section('content')
    @push('css')
        <link href="https://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.css" rel="stylesheet" />

    @endpush
    <?php $route = explode('.', Route::currentRouteName()); ?>
    <section class="input-validation">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="___class_+?5___">
                            @if ($route[1] == 'create')
                                Create
                            @else
                                Edit
                            @endif Shop
                        </h2>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            @if ($route[1] == 'create')
                                <form class="form-horizontal" action="{{ route('shops.store') }}" method="POST"
                                    enctype="multipart/form-data" novalidate autocomplete="off">
                                    @method('POST')
                                @else

                                    <form class="form-horizontal" @role('admin')
                                    action="{{ route('shops.update', request()->shop) }}" @else
                                        action="{{ route('shop-update', auth()->user()->shop->id) }}" @endrole
                                        method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
                                        @method('PUT')
                            @endif
                            @csrf
                            @role('admin')
                            <h4 class="card-title">Vendor Details</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Vendor Name</label>
                                            <input type="text" class="form-control" placeholder="Vendor Name"
                                                value="{{ isset($shop) ? $shop->user->name : old('username') }}"
                                                name="username"
                                                data-validation-required-message="This Vendor Name field is required"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group" id="vendorEmail">
                                        <div class="controls">
                                            <label>E-mail</label>
                                            <input type="email" @if ($route[1] == 'create')
                                            onkeyup="checkUniqueName('users', 'email', '#vendorEmail', this.value)"
                                            @endif
                                            class="form-control" placeholder="Email"
                                            value="{{ isset($shop) ? $shop->user->email : old('email') }}"
                                            name="email" required
                                            data-validation-required-message="This email field is required">

                                        </div>
                                    </div>
                                    @if (isset($shop) && $shop->user->image != null)
                                        <div class="my-1">
                                            <img src="{{ asset($shop->user->image) }}" width="30%" alt="" srcset="">
                                        </div>
                                    @endif

                                    <fieldset class="form-group" id="profile_image">
                                        <label for="storePANImage">Upload Profile Image </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="storePANImage">Profile Image</span>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="profile_image"
                                                    id="storePANImageUpload" aria-describedby="storePANImage">
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
                                                placeholder="Password" @if ($route[1] == 'create') data-validation-required-message="This Password field is required" @endif>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="ConfirmPassword">Repeat password must match</label>
                                        <div class="controls">
                                            <input type="password" autocomplete="new-password" name="confirm_password"
                                                id="ConfirmPassword" data-validation-match-match="password"
                                                class="form-control" placeholder="Repeat Password"
                                                @if ($route[1] == 'create') data-validation-required-message="This Confirm Password field is required" @endif>
                                        </div>
                                    </div>
                                    <div style="display: flex">
                                        <fieldset class="controls" style="width: 60%">
                                            <label for="MobileNumber">Mobile Number</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="recipePreTimede674">+91</span>
                                                </div>
                                                <input type="text"
                                                    value="{{ isset($shop) ? str_replace('+91', '', $shop->user->mobile) : '' }}"
                                                    @if ($route[1] == 'create') onkeyup="checkUniqueName('users', 'mobile', '#vendorMobile', this.value)" @endif name="mobile" id="vendorMobile"
                                                    class="form-control"
                                                    data-validation-required-message="This mobile field is required"
                                                    placeholder="Enter Your Mobile Number" required>

                                            </div>
                                        </fieldset>

                                    </div>
                                </div>
                            </div>
                            @endrole
                            <h4 class="card-title mt-3">Shop Details</h4>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Weekdays</label>
                                            <?php $weekdays = [];
                                            if (isset($shop->weekdays)) {
                                                $weekdays = explode(',', $shop->weekdays);
                                            } ?>
                                            <select data-placeholder="Select days..." name="weekdays[]"
                                                autocomplete="new-password"
                                                class="select2-icons form-control select2-hidden-accessible"
                                                id="multiple-select2-icons1" multiple=""
                                                data-validation-required-message="Choose weekdays"
                                                data-select2-id="multiple-select2-icons1" tabindex="-1" aria-hidden="true"
                                                required>
                                                <option value="Monday"
                                                    {{ in_array('Monday', $weekdays) ? 'selected' : '' }}>Monday</option>
                                                <option value="Tuesday"
                                                    {{ in_array('Tuesday', $weekdays) ? 'selected' : '' }}>Tuesday
                                                </option>
                                                <option value="Wednesday"
                                                    {{ in_array('Wednesday', $weekdays) ? 'selected' : '' }}>Wednesday
                                                </option>
                                                <option value="Thursday"
                                                    {{ in_array('Thursday', $weekdays) ? 'selected' : '' }}>Thursday
                                                </option>
                                                <option value="Friday"
                                                    {{ in_array('Friday', $weekdays) ? 'selected' : '' }}>Friday</option>
                                                <option value="Saturday"
                                                    {{ in_array('Saturday', $weekdays) ? 'selected' : '' }}>Saturday
                                                </option>
                                                <option value="Sunday"
                                                    {{ in_array('Sunday', $weekdays) ? 'selected' : '' }}>Sunday</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="">Opening Time</label>
                                    <input type="time" onchange="timeForm('.fullTimeForamtOpen', this.value)"
                                        value="{{ isset($shop->opens_at) ? $shop->opens_at->format('H:i') : old('opening_time') }}"
                                        class="form-control fullTime" name="opening_time" placeholder="From" required>

                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="">24 Hours Format</label>
                                    <input class="fullTimeForamtOpen form-control"
                                        value="{{ isset($shop->opens_at) ? $shop->opens_at->format('H:i') : old('opening_time') }}"
                                        disabled>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="">Closing Time</label>
                                    <input type="time" onchange="timeForm('.fullTimeForamtClose', this.value)"
                                        value="{{ isset($shop->closes_at) ? $shop->closes_at->format('H:i') : old('closing_time') }}"
                                        class="form-control fullTime" name="closing_time" placeholder="To" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="">24 Hours Format</label>
                                    <input class="fullTimeForamtClose form-control"
                                        value="{{ isset($shop->closes_at) ? $shop->closes_at->format('H:i') : old('closing_time') }}"
                                        disabled>

                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Cuisines</label>
                                        <select data-placeholder="Select Cuisines..." name="cuisines[]"
                                            autocomplete="new-password"
                                            class="select2-icons form-control select2-hidden-accessible"
                                            id="multiple-select2-icons" multiple="" data-select2-id="multiple-select2-icons"
                                            tabindex="-1" aria-hidden="true">
                                            @forelse ($cuisines as $cuisine)
                                                <option value="{{ $cuisine->id }}"
                                                    {{ isset($shop) ? (in_array($cuisine->id, $select) ? 'selected' : '') : '' }}>
                                                    {{ $cuisine->name }}</option>
                                            @empty

                                            @endforelse

                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    @role('admin')
                                    <div class="form-group">
                                        <label>Service</label>
                                        <select class="select2 form-control" id="type_id" name="type_id"
                                            data-placeholder="Select Service Type..." style="pointer-events: none;">
                                            <option value="1" selected>Order and Delivery</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Shop Street</label>
                                            <input type="text" class="form-control" placeholder="Shop Street"
                                                value="{{ isset($shop) ? $shop->street : old('street') }}" name="street"
                                                data-validation-required-message="This Shop Street field is required"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Shop Area</label>
                                            <input type="text" class="form-control" placeholder="Shop Area"
                                                value="{{ isset($shop) ? $shop->area : old('area') }}" name="area"
                                                data-validation-required-message="This Shop Area field is required"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Shop City</label>
                                            <input type="text" class="form-control" placeholder="Shop City"
                                                value="{{ isset($shop->name) ? $shop->city : old('city') }}" name="city"
                                                data-validation-required-message="This Shop city field is required"
                                                required>
                                        </div>
                                    </div>
                                    @endrole
                                    <fieldset class="form-group">
                                        <label for="productDescription">Description</label>
                                        <textarea class="form-control" name="description" id="productDescription" rows="4"
                                            placeholder="Descripton">{{ isset($shop->description) ? $shop->description : old('description') }}</textarea>
                                    </fieldset>
                                    <div class="d-flex justify-content-between">
                                        <div class="form-group" style="width: 45%">
                                            <fieldset class="controls">
                                                <label for="serve_count">Delivery Radius</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                        value="{{ isset($shop->radius) ? $shop->radius : old('radius') }}"
                                                        name="radius" id="radius" placeholder="Radius"
                                                        aria-describedby="radius"
                                                        data-validation-required-message="This Radius field is required"
                                                        required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="recipePreTimede654">KM</span>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="form-group" style="width: 48%">
                                            <fieldset>
                                                <label for="recipePreTimede">Min Order Amount (₹)</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" step=".0000000000000001"
                                                        value="{{ isset($shop->min_amount) ? $shop->min_amount : old('min_amount') }}"
                                                        name="min_amount" id="recipePreTime654" placeholder="Amount"
                                                        aria-describedby="recipePreTimede" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="recipePreTimede654">Per
                                                            Order</span>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-12 col-sm-6">
                                    @role('admin')
                                    <div class="form-group" id="vendorShopName">
                                        <div class="controls">
                                            <label>Shop Name</label>
                                            <input type="text" class="form-control" placeholder="Shop Name"
                                                value="{{ isset($shop) ? $shop->name : old('shop_name') }}"
                                                name="shop_name" @if ($route[1] == 'create') onkeyup="checkUniqueName('shops', 'name', '#vendorShopName', this.value)" @endif
                                                data-validation-required-message="This Shop Name field is required"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="controls">
                                            <label>Shop E-mail</label>
                                            <input type="email" class="form-control" placeholder="Email"
                                                value="{{ isset($shop->email) ? $shop->email : old('shop_email') }}"
                                                name="shop_email" required
                                                data-validation-required-message="This email field is required">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <fieldset class="controls">
                                            <label>Shop Mobile</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="shopMobile">+91</span>
                                                </div>
                                                <input type="text" class="form-control" placeholder="Mobile"
                                                    id="shopMobile"
                                                    value="{{ isset($shop->mobile) ? str_replace('+91', '', $shop->mobile) : old('shop_mobile') }}"
                                                    name="shop_mobile" required
                                                    data-validation-required-message="This mobile field is required">

                                            </div>
                                        </fieldset>

                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div class="form-group" style="width: 45%">
                                            <fieldset>
                                                <label for="recipePreTime">Latitude</label>
                                                <div class="input-group">

                                                    <input type="number" class="form-control" step=".0000000000000001"
                                                        value="{{ isset($shop->latitude) ? $shop->latitude : old('latitude') }}"
                                                        name="latitude" id="recipePreTime1" placeholder="Latitude"
                                                        aria-describedby="recipePreTime1" required>
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
                                                    <input type="number" class="form-control" step=".0000000000000001"
                                                        value="{{ isset($shop->longitude) ? $shop->longitude : old('longitude') }}"
                                                        name="longitude" id="recipePreTime" placeholder="Longitude"
                                                        aria-describedby="recipePreTime" required>
                                                    {{-- <div class="input-group-append">
                                        <span class="input-group-text" id="recipePreTime">Per KG</span>
                                      </div> --}}
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <div class="form-group" style="width: 45%">
                                            <fieldset class="controls">
                                                <label for="serve_count">Approx price for 2 persons</label>
                                                <div class="input-group">

                                                    <input type="text" class="form-control"
                                                        value="{{ isset($shop->price) ? $shop->price : old('price') }}"
                                                        name="price" id="price" placeholder="Price" aria-describedby="price"
                                                        data-validation-required-message="This Price field is required"
                                                        required>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="form-group" style="width: 48%">
                                            <fieldset>
                                                <label for="recipePreTimede">Customer Delivery Charge (₹)</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" step=".0000000000000001"
                                                        value="{{ isset($shop->delivery_charge) ? $shop->delivery_charge : old('delivery_charge') }}"
                                                        name="delivery_charge" id="recipePreTime"
                                                        placeholder="Delivery Charge" aria-describedby="recipePreTimede"
                                                        required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="recipePreTimede">Per
                                                            Order</span>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div class="form-group" style="width: 45%">
                                            <fieldset>
                                                <label for="recipePreTimede12">{{ config('app.name') }} Commission
                                                    (%)</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" step=".0000000000000001"
                                                        value="{{ isset($shop->comission) ? $shop->comission : old('comission') }}"
                                                        name="comission" id="recipePreTime12" placeholder="Comission"
                                                        aria-describedby="recipePreTimede12" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="recipePreTimede12">Per
                                                            Order</span>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                        @endrole
                                        <div class="form-group" style="width: 48%">
                                            <label for="">Delivery Type</label>
                                            <select class="form-control" id="size" name="assign"
                                                autocomplete="new-password" data-placeholder="Select Size...">
                                                <option value="1"
                                                    {{ isset($shop->assign) ? ($shop->assign == 1 ? 'selected' : old('comission')) : '' }}>
                                                    Automatic</option>
                                                <option value="0"
                                                    {{ isset($shop->assign) ? ($shop->assign == 0 ? 'selected' : old('comission')) : '' }}>
                                                    Manual</option>
                                            </select>
                                        </div>
                                        @role('admin')
                                    </div>
                                    @endrole
                                    @if (isset($shop->image))
                                        <div class="my-1">
                                            <img src="{{ $shop->image }} " width="30%" alt="" srcset="">
                                        </div>
                                    @endif
                                    <fieldset id="productimageElement" class="form-group">
                                        <div class="controls">
                                            <label for="categoryImage">Upload Shop Image</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="productImage">90 x 97</span>
                                                </div>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" name="shop_image"
                                                        id="productImageUpload" aria-describedby="productImage" @if ($route[1] == 'create')
                                                    data-validation-required-message="This Image field is required"
                                                    required @endif>
                                                    <label class="custom-file-label" for="productImage">Choose file</label>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    @if (isset($shop->banner_image))
                                        <div class="my-1">
                                            <img src="{{ $shop->banner_image }} " width="30%" alt="" srcset="">
                                        </div>
                                    @endif
                                    <fieldset id="productimageElement1" class="form-group">
                                        <div class="controls">
                                            <label for="categoryImage">Upload Shop Banner Image</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="productImage1">342 x 97</span>
                                                </div>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" name="banner_image"
                                                        id="productImageUpload1" aria-describedby="productImage"
                                                        @if ($route[1] == 'create')
                                                    data-validation-required-message="This Image field is required"
                                                    required @endif>
                                                    <label class="custom-file-label" for="productImage1">Choose
                                                        file</label>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>

                                </div>

                            </div>
                            @include('shared._submit', [
                            'entity' => 'shops',
                            'button' => $route[1] == 'create' ? 'Create' : "Update"
                            ])
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </section>
@endsection
