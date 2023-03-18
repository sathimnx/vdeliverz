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
            <h2 class="">@if ($route[2] == 'create')
                Create
            @else
                Edit
            @endif Car</h2>
          </div>
          <div class="card-content">
            <div class="card-body">
                @if ($route[2] == 'create')
                <form class="form-horizontal" action="{{ route('demand.cars.store') }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
                    @method('POST')
                    @else
                    <form class="form-horizontal" action="{{ route('demand.cars.update', request()->car) }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
                        @method('PUT')
                    @endif
                    @csrf
                    <h4 class="card-title"></h4>
                        <div class="row">
                            {{-- <div class="col-md-6">
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
 --}}

                        <div class="row">
                        @if ($route[2] === 'create')
                        <div class="col-md-12">
                        <section id="form-control-repeater" class="mt-2">
                            <!-- phone repeater -->
                            <div class="card">
                                <div class="card-header">
                                <h4 class="card-title">Add Cars</h4>
                                </div>
                                <div class="card-content">
                                <div class="card-body">
                                    <div class="contact-repeater">
                                    <div >

                                        <div class="row justify-content-between" >
                                            @for ($i = 0; $i < 8; $i++)
                                            <div class="col-md-3 form-group" id="carNameUnique{{ $i }}">
                                                <label for="">Name <span class="text-danger"> *</span></label>
                                            <input type="text" value="{{old('name')}}" class="form-control" name="cars[{{ $i }}][name]" onkeyup="checkUniqueName('cars', 'name', '#carNameUnique{{ $i }}', this.value)"  placeholder="Car Name" @if ($i == 0)
                                                required
                                            @endif
                                            >
                                            </div>
                                            <fieldset class="col-md-3 form-group">
                                                <label for="storePANImage">Upload Image </label>
                                                <div class="input-group" >
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Image</span>
                                                </div>
                                                <div class="custom-file">
                                                <input type="file"  class="custom-file-input"  name="cars[{{ $i }}][image]" aria-describedby="storePANImage">
                                                <label class="custom-file-label" for="storePANImage">Choose file</label>
                                                </div>
                                                </div>
                                            </fieldset>
                                            {{-- <div class="col-md-2 col-12 form-group">
                                            <button class="btn btn-icon btn-danger rounded-circle mt-2 float-right" type="button" data-repeater-delete>
                                                <i class="bx bx-x" style="vertical-align: 0;"></i>
                                            </button>
                                            </div> --}}
                                            @endfor
                                        </div>
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
                                                    <label for="">Name <span class="text-danger"> *</span></label>
                                                <input type="text" value="{{$car->name}}" class="form-control" name="name" id="carName" placeholder="Car Name" required >
                                                </div>
                                                <img src="{{ asset($car->img_url) }}" width="20%" alt="{{ __('') }}">
                                                <fieldset class="col-md-6 form-group" id="profile_image">
                                                    <label for="storePANImage">Upload Image </label>
                                                    <div class="input-group" >
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="storePANImage">Car Image</span>
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
                      'entity' => 'cars',
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
