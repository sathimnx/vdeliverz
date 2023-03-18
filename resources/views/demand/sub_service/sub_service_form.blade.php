@extends('demand.layouts.main')

@section('content')
<section class="input-validation">
    <?php $route = explode('.', Route::currentRouteName());     ?>
    @if ($route[1] == 'show')
    @push('scripts')
        <script>
            $('input').attr('disabled', true);
            $('select').attr('disabled', true);
            $('textarea').attr('disabled', true);
            $('button').addClass('d-none');
        </script>
    @endpush
@endif
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h2 class="">@if ($route[1] == 'create')
                Create
            @else
                Edit
            @endif Sub-Service</h2>
          </div>
          <div class="card-content">
            <div class="card-body">
                @if ($route[2] == 'create')
                <form class="form-horizontal" action="{{ route('demand.sub-services.store') }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
                    @method('POST')
                    @else
                    <form class="form-horizontal" action="{{ route('demand.sub-services.update', request()->sub_service) }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
                        @method('PUT')
                    @endif
                    @csrf
                    <h4 class="card-title"></h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Select Service Category</label>
                                    <select class="select2 form-control" id="category_id" name="service_id" data-validation-required-message="This Service field is required"  data-placeholder="Select Service Category..." required>
                                        <option value="">Select Service</option>
                                        @forelse ($services as $service)
                                        <option value="{{$service->id}}" {{isset($sub_service) ? $sub_service->service->id == $service->id ? 'selected' : '' : ''}}>{{$service->name}}</option>
                                        @empty

                                        @endforelse
                                    </select>
                                    </div>
                                </div>

                            </div>


                        <div class="row">
                        @if ($route[2] === 'create')
                        <div class="col-md-12">
                        <section id="form-control-repeater" class="mt-2">
                            <!-- phone repeater -->
                            <div class="card">
                                <div class="card-header">
                                <h4 class="card-title">Add Services</h4>
                                </div>
                                <div class="card-content">
                                <div class="card-body">
                                    <div class="contact-repeater">
                                    <div data-repeater-list="stocks">

                                        <div class="row justify-content-between" data-repeater-item>
                                            <div class="col-md-4 col-12 form-group">
                                                <label for="">Name</label>
                                            <input type="text" value="{{old('variant')}}" class="form-control" name="name" placeholder="Service Name">
                                            </div>
                                            <fieldset class="col-md-6 form-group" id="profile_image">
                                                <label for="storePANImage">Upload Image </label>
                                                <div class="input-group" >
                                                {{-- <div class="input-group-prepend">
                                                    <span class="input-group-text" id="storePANImage">Service Image</span>
                                                </div> --}}
                                                <div class="custom-file">
                                                <input type="file"  class=""  name="image" id="storePANImageUpload" aria-describedby="storePANImage">
                                                {{-- <label class="" for="storePANImage">Choose file</label> --}}
                                                </div>
                                                </div>
                                            </fieldset>
                                            <div class="col-md-2 col-12 form-group">
                                            <button class="btn btn-icon btn-danger rounded-circle mt-2 float-right" type="button" data-repeater-delete>
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
                        @if ($route[2] === 'edit')
                        <div class="col-md-12">
                            <section id="form-control-repeater" class="mt-2">
                                <!-- phone repeater -->
                                <div class="card">

                                    <div class="card-content">
                                    <div class="card-body">
                                        <div class="contact-repeater">
                                        <div data-repeater-list="stocks">

                                            <div class="row justify-content-between" data-repeater-item>
                                                <div class="col-md-4 col-12 form-group">
                                                    <label for="">Name</label>
                                                <input type="text" value="{{$sub_service->name}}" class="form-control" name="name" placeholder="Service Name">
                                                </div>
                                                <img src="{{ asset($sub_service->image) }}" width="20%" alt="{{ __('') }}">
                                                <fieldset class="col-md-6 form-group" id="profile_image">
                                                    <label for="storePANImage">Upload Image </label>
                                                    <div class="input-group" >
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="storePANImage">Service Image</span>
                                                    </div>
                                                    <div class="custom-file">
                                                    <input type="file"  class="custom-file-input"  name="image" id="storePANImageUpload" aria-describedby="storePANImage">
                                                    <label class="custom-file-label" for="storePANImage">Choose file</label>
                                                    </div>
                                                    </div>
                                                </fieldset>

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
                        </div>


                    @include('demand.shared._submit', [
                      'entity' => 'sub-serives',
                      'button' => $route[2] == 'create' ? 'Create' : "Update"
                    ])
                </form>
            </div>
         </div>
        </div>
    </div>
</div>



</section>
@endsection
