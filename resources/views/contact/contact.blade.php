@extends('layouts.main')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
    <section class="input-validation">
        <?php $route = explode('.', Route::currentRouteName()); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="___class_+?5___">
                            Contact Details</h2>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form-horizontal" action="{{ route('contacts.store') }}" method="POST" novalidate
                                autocomplete="off">
                                @method('post')
                                @csrf
                                <h4 class="card-title"></h4>
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-md-4 form-group">
                                        <label for="">Email</label>
                                        <input type="text" class="form-control" name="email" placeholder="Email"
                                            value="{{ $contact->email ?? '' }}" required>
                                        @error('email') <span class="error text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <fieldset class="controls">
                                            <label for="MobileNumber">Mobile Number</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="recipePreTimede674">+91</span>
                                                </div>

                                                <input type="text" class="form-control" name="mobile"
                                                    value="{{ str_replace('+91', '', $contact->mobile) ?? '' }}"
                                                    placeholder="Mobile" required>
                                                @error('mobile') <span class=" text-danger">{{ $message }}</span>
                                                @enderror

                                            </div>
                                        </fieldset>

                                    </div>
                                    <div class="col-md-4 form-group">
                                        <fieldset class="controls">
                                            <label for="">Whatsapp</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="recipePreTimede674">+91</span>
                                                </div>

                                                <input type="text" class="form-control" name="wp"
                                                    value="{{ str_replace('+91', '', $contact->wp) ?? '' }}"
                                                    placeholder="Whatsapp" required>
                                                @error('wp') <span class=" text-danger">{{ $message }}</span>
                                                @enderror

                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for=""></label>
                                        <button class="btn btn-warning float-left mt-2" type="submit"
                                            class="btn btn-primary">Update</button>
                                    </div>
                                    <div class="col-md-3">
                                        @if (session()->has('message'))
                                            <div class="alert alert-success mb-0">
                                                {{ session('message') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="___class_+?5___">
                            Delivery Charge Details</h2>

                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form-horizontal" action="{{ route('charges.update') }}" method="POST" novalidate
                                autocomplete="off">
                                @method('post')
                                @csrf
                                <h4 class="card-title"></h4>
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-md-3 form-group">
                                        <fieldset class="controls">
                                            <label for="Basic_charge">Basic Charge</label>
                                            <div class="input-group">
                                                <input type="number" step="0.0001" class="form-control" name="basic_charge"
                                                    value="{{ $charge->basic_charge ?? '' }}" placeholder="Basic Charge"
                                                    required>
                                                    
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="recipesPreTimede674">Per Basic
                                                            KMS</span>
                                                    </div>
                                              
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <fieldset class="controls">
                                            <label for="MobileNumber">Basic KM</label>
                                            <div class="input-group">
                                                <input type="number" step="0.0001" class="form-control" name="basic_km"
                                                    value="{{ $charge->basic_km ?? '' }}" placeholder="Basic KM"
                                                    required>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="recipePreTimede674">KM</span>
                                                </div>
                                                @error('basic_km') <span class=" text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <fieldset class="controls">
                                            <label for="">Extra Charges</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"
                                                        id="recipePreTimede674">{{ config('constants.currency') }}</span>
                                                </div>
                                                <input type="number" step="0.001" class="form-control" name="extra_charge"
                                                    value="{{ $charge->extra_charge ?? '' }}" placeholder="Extra Charges"
                                                    required>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="recipePreTimede674">Per Extra
                                                        KM</span>
                                                </div>
                                                @error('extra_charge') <span
                                                        class=" text-danger">{{ $message }}</span>
                                                @enderror

                                            </div>
                                        </fieldset>
                                    </div>
                                     <div class="col-md-3 form-group">
                                    <fieldset class="controls">
                                        <label for="">GST Charges</label>
                                        <div class="input-group">
                                            
                                            <input type="number" step="0.001" class="form-control" name="gst_charge"
                                                value="{{ $charge->gst_charge ?? '' }}" placeholder="Gst Charges"
                                                required>
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="recipePreTimede674">Percentage</span>
                                            </div>
                                            @error('gst_charge') <span
                                                    class=" text-danger">{{ $message }}</span>
                                            @enderror

                                        </div>
                                    </fieldset>
                                </div>
                                
                                
                                <br/>
                                <br/>
                        <h2 class="___class_+?5___" style="margin-left:1%;">
                            Door To Door Delivery Charge Details</h2>
<div class="col-md-12">
                                <h4 class="card-title"></h4>
                                
                                <div class="col-md-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body row">
                            <div class="col-md-4">
                                <label for="dtd">Door To Door Delivery Status</label>
                                <div id="dtd" class="custom-control custom-switch custom-switch-glow custom-control-inline">
                                        <input type="checkbox" class="custom-control-input"
                                                            {{ $contact->dTd == 1 ? 'checked' : '' }}
                                                            onchange="change_status('{{ $contact->id }}', 'contacts', '#gstSwitchGlow{{ $contact->dTd }}', 'dTd');"
                                                            id="gstSwitchGlow{{ $contact->dTd }}">
                                                        <label class="custom-control-label"
                                                            for="gstSwitchGlow{{ $contact->dTd }}">
                                </div>
                                
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-md-3 form-group">
                                        <fieldset class="controls">
                                            <label for="">Two Wheeler Basic Charge</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"
                                                        id="recipePreTismede674">{{ config('constants.currency') }}</span>
                                                </div>
                                                <input type="number" step="0.00001" class="form-control"
                                                    name="dTd_twoWheeler_basicCharge" placeholder="Basic Charge"
                                                    value="{{ $charge->dTd_twoWheeler_basicCharge ?? '' }}" required>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="recipesPreTimede674">Per Basic
                                                        KMS</span>
                                                </div>
                                                @error('basic_charge') <span
                                                        class="error text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </fieldset>
                                    </div>
                                        <div class="col-md-3 form-group">
                                        <fieldset class="controls">
                                            <label for="">Two Wheeler Basic KM</label>
                                            <div class="input-group">
                                                
                                                <input type="number" step="0.00001" class="form-control"
                                                    name="dTd_twoWheeler_basicKM" placeholder="Basic Charge"
                                                    value="{{ $charge->dTd_twoWheeler_basicKM ?? '' }}" required>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="recipePreTimede674"> 
                                                        KM</span>
                                                </div>
                                                @error('basic_charge') <span
                                                        class="error text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </fieldset>
                                    </div>
                                        <div class="col-md-3 form-group">
                                        <fieldset class="controls">
                                            <label for="">Two Wheeler Extra Charge</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"
                                                        id="recipePreTismede674">{{ config('constants.currency') }}</span>
                                                </div>
                                                <input type="number" step="0.00001" class="form-control"
                                                    name="dTd_twoWheeler_extraCharge" placeholder="Basic Charge"
                                                    value="{{ $charge->dTd_twoWheeler_extraCharge ?? '' }}" required>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="recipePreTimede674">Per Extra KM</span>
                                                </div>
                                                @error('basic_charge') <span
                                                        class="error text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </fieldset>
                                    </div>
                                  
                           
                                </div>
                                
                                <div class="row align-items-center justify-content-between">
                                  <div class="col-md-3 form-group">
                                        <fieldset class="controls">
                                            <label for="MobileNumber">Three Wheeler Basic Charge</label>
                                            <div class="input-group">
                                                 <div class="input-group-prepend">
                                                    <span class="input-group-text"
                                                        id="recipePreTismede674">{{ config('constants.currency') }}</span>
                                                </div>
                                                <input type="number" step="0.0001" class="form-control" name="dTd_threeWheeler_basicCharge"
                                                    value="{{ $charge->dTd_threeWheeler_basicCharge ?? '' }}" placeholder="Basic KM"
                                                    required>
                                               <div class="input-group-prepend">
                                                    <span class="input-group-text" id="recipesPreTimede674">Per Basic
                                                        KMS</span>
                                                </div>
                                                @error('basic_km') <span class=" text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <fieldset class="controls">
                                            <label for="MobileNumber">Three Wheeler Basic KM</label>
                                            <div class="input-group">
                                                <input type="number" step="0.0001" class="form-control" name="dTd_threeWheeler_basicKM"
                                                    value="{{ $charge->dTd_threeWheeler_basicKM ?? '' }}" placeholder="Basic KM"
                                                    required>
                                                <div class="input-group-prepend">
                                                  <span class="input-group-text" id="recipePreTimede674"> 
                                                        KM</span>
                                                </div>
                                                @error('basic_km') <span class=" text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <fieldset class="controls">
                                            <label for="MobileNumber">Three Wheeler Extra Charge</label>
                                            <div class="input-group">
                                                 <div class="input-group-prepend">
                                                    <span class="input-group-text"
                                                        id="recipePreTismede674">{{ config('constants.currency') }}</span>
                                                </div>
                                                <input type="number" step="0.0001" class="form-control" name="dTd_threeWheeler_extraCharge"
                                                    value="{{ $charge->dTd_threeWheeler_extraCharge ?? '' }}" placeholder="Basic KM"
                                                    required>
                                                <div class="input-group-prepend">
                                                  <span class="input-group-text" id="recipePreTimede674">Per Extra KM</span>
                                                </div>
                                                @error('basic_km') <span class=" text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </fieldset>
                                    </div>
                                     </div>
                                    
                                    <div class="row align-items-center justify-content-between">
                                     <div class="col-md-3 form-group">
                                        <fieldset class="controls">
                                            <label for="">Four Wheeler Type</label>
                                            <div class="input-group">
                                                <select name="fourWheelerTypeId" id="fourWheelerType" onchange="getFourWheelercharges()"
                                                        class="form-control ">
                                                        <option value="all" {{ request()->type == null ? 'selected' : '' }}>Select Four Wheeler Type</option>
                                                        @forelse($dTd_charges as $dTd_charge)
                                                            <option value="{{ $dTd_charge->id }}" {{ request()->category == $dTd_charge->id ? 'selected' : '' }}>
                                                                {{ $dTd_charge->vehicle_type }}</option>
                                                        @empty
                                                        @endforelse
                                                    </select>
                                                @error('extra_charge') <span
                                                        class=" text-danger">{{ $message }}</span>
                                                @enderror

                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <fieldset class="controls">
                                            <label for="MobileNumber">Four Wheeler Basic Charge</label>
                                         
                                            <div class="input-group">
                                                    <div class="input-group-prepend">
                                                    <span class="input-group-text"
                                                        id="recipePreTismede674">{{ config('constants.currency') }}</span>
                                                </div>
                                                <input type="number" step="0.0001" class="form-control" name="dtd_fourWheeler_basic_charge" id="dtd_fourWheeler_basic_charge"
                                                    value="0" placeholder="Basic KM"
                                                    required>
                                               <div class="input-group-prepend">
                                                    <span class="input-group-text" id="recipesPreTimede674">Per Basic
                                                        KMS</span>
                                                </div>
                                                @error('basic_km') <span class=" text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <fieldset class="controls">
                                            <label for="MobileNumber">Four Wheeler Basic KM</label>
                                            <div class="input-group">
                                                <input type="number" step="0.0001" class="form-control" name="dtd_fourWheeler_basic_km" id="dtd_fourWheeler_basic_km"
                                                    value="0" placeholder="Basic KM"
                                                    required>
                                                <div class="input-group-prepend">
                                                  <span class="input-group-text" id="recipePreTimede674"> 
                                                        KM</span>
                                                </div>
                                                @error('basic_km') <span class=" text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <fieldset class="controls">
                                            <label for="">Four Wheeler Extra Charge</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"
                                                        id="recipePreTimede674">{{ config('constants.currency') }}</span>
                                                </div>
                                                <input type="number" step="0.001" class="form-control" name="dTd_fourWheeler_extracharge" id="dTd_fourWheeler_extracharge"
                                                    value="0" placeholder="Four Wheeler Charges"
                                                    required>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="recipePreTimede674">Per Extra KM</span>
                                                </div>
                                                @error('extra_charge') <span
                                                        class=" text-danger">{{ $message }}</span>
                                                @enderror

                                            </div>
                                        </fieldset>
                                    </div>
                                    </div>
                                    
                                </div>
                                  <div class="col-md-3 form-group">
                                    <label for=""></label>
                                    <button class="btn btn-warning float-left mt-2" type="submit"
                                        class="btn btn-primary">Update</button>
                                </div>
                            </form>
                      
                </div>
            </div>
           </div>
           {{-- <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="___class_+?5___">
                            GST Charge Details</h2>

                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form-horizontal" action="{{ route('charges.update') }}" method="POST" novalidate
                                autocomplete="off">
                                @method('post')
                                @csrf
                                <h4 class="card-title"></h4>                                
                                <div class="col-md-5 form-group">
                                    <fieldset class="controls">
                                        <label for="">GST Charges</label>
                                        <div class="input-group">
                                            
                                            <input type="number" step="0.001" class="form-control" name="gst_charge"
                                                value="{{ $charge->gst_charge ?? '' }}" placeholder="Gst Charges"
                                                required>
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="recipePreTimede674">Percentage</span>
                                            </div>
                                            @error('gst_charge') <span
                                                    class=" text-danger">{{ $message }}</span>
                                            @enderror

                                        </div>
                                    </fieldset>
                                </div>

                              
                            </br>

                            </div>
                        </form>
                    </div>
                </div>
            </div> --}}
     <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="___class_+?5___">
                             Notification Broadcast Details</h2>

                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form-horizontal"  action="{{ route('notificationAdd') }}" id="upload-image-form" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
                                @method('post')
                                @csrf
                                <h4 class="card-title"></h4>
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-md-3 form-group">
                                        <fieldset class="controls">
                                             <label for="">Title</label>
         
                                            <div class="input-group">                                                
                                                <input type="text" class="form-control" name="title"
                                                    id="title" placeholder="Title" required> 
                                            </div>
                                        </fieldset>
                                    </div>
                                      <div class="col-md-3 form-group">
                                        <fieldset class="controls">
                                            <label for="">Message</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="Msg"
                                                    id="Msg" placeholder="Message" required> 
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3 form-group">
                                                 <fieldset class="form-group" id="profile_image">
                                        <label for="storePANImage">Upload Notification Image </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="storePANImage">Image</span>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="image"
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
                                    </div>
                                    <div class="col-md-3 form-group">
                                    <label for=""></label>
                                   <button class="btn btn-warning float-left mt-2" 
                                        class="btn btn-primary">Add</button>
                                </div>
                    
                                   
                                    </form>
                                    </div>
                                     
            </div>
            </div>
           <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
            <script>
                function saveNotification()
                { 
                    $.ajax({
                        type:'POST',
                        url:"{{ route('notificationAdd') }}",
                        data:{title:$('#title').val(),Msg:$('#Msg').val(),Img:$('#Img').val()},
                        success: function(result) {
                        }
                    });
                }
            </script>
            </div>
          
   <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

     <script type="text/javascript">

        function getFourWheelercharges()
        { 
             $.ajax({
               type:'POST',
               url:"{{ route('get_fourWheelerCharges') }}",
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
               data:{fourWheelerTypeId:$('#fourWheelerType').val()},
               success: function(data) {
                    $('#dtd_fourWheeler_basic_km').val(data.basic_km);
                    $('#dtd_fourWheeler_basic_charge').val(data.basic_charge);
                    $('#dTd_fourWheeler_extracharge').val(data.extra_charge);
               }
           });
        }
           </script>
     </section>
@endsection
