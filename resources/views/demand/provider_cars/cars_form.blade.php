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
            @endif Provider Cars</h2>
          </div>
          <div class="card-content">
            <div class="card-body">
                @if (Session::has('message'))
                        <div class="alert alert-danger">
                            <p class="m-0">{{ Session::get('message') }} <a href="{{ route('demand.provider-cars.edit', Session::get('car_provider_id')) }}" class="float-right text-white">View</a></p>
                        </div>
                    @endif
                @if ($route[2] == 'create')
                <form class="form-horizontal" action="{{ route('demand.provider-cars.store') }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
                    @method('POST')
                    @else
                    <form class="form-horizontal" action="{{ route('demand.provider-cars.update', request()->provider_car) }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
                        @method('PUT')
                    @endif
                    @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Select Provider</label>
                                    <select class="select2 form-control" id="provider_id" @if($route[2] === 'create') onchange="checkProviderCar()" @endif name="provider_id" data-validation-required-message="This Provider field is required"  data-placeholder="Select Provider..." required @if ($route[2] == 'edit') disabled @endif>
                                        <option value="">Select Provider</option>
                                        @forelse ($providers as $provider)
                                        <option value="{{$provider->id}}" {{isset($carProvider) ? $carProvider->provider_id == $provider->id ? 'selected' : '' : ''}}>{{$provider->name}}</option>
                                        @empty

                                        @endforelse
                                    </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="carField">
                                    <div class="controls">
                                        <label>Select Car</label>
                                    <select class="select2 form-control" id="car_id" @if($route[2] === 'create') onchange="checkProviderCar()" @endif name="car_id" data-validation-required-message="This Car field is required"  data-placeholder="Select Car..." required @if ($route[2] == 'edit') disabled @endif>
                                        <option value="">Select Car</option>
                                        @forelse ($cars as $car)
                                        <option value="{{$car->id}}" {{isset($carProvider) ? $carProvider->car_id == $car->id ? 'selected' : '' : ''}}>{{$car->name}}</option>
                                        @empty

                                        @endforelse
                                    </select>
                                    </div>
                                </div>
                            </div>
                            @push('scripts')
                                <script>
                                    function checkProviderCar(){
                                        var provider_id = $('#provider_id').val();
                                        var car_id = $('#car_id').val();
                                        $.ajax({
                                            url: "{{ route('demand.check-unique-car.index') }}",
                                            type: 'POST',
                                            data: {"_token": '{{csrf_token()}}', provider_id:provider_id, car_id:car_id},
                                            success: function(response){
                                                if(response.status == false){
                                                    toastr.error(response.message);
                                                    $('#carField .unique').remove();
                                                    $('#carField').append('<div class="help-block text-danger unique"><ul role="alert"><li>'+response.message+'</li></ul></div>');
                                                }
                                                if(response.status == true){
                                                    $('#carField .unique').remove();
                                                }
                                            }
                                        })
                                    }
                                </script>
                            @endpush
                            <div class="col-md-3 form-group">
                                <label for="">Per Day (₹)</label>
                              <input type="number" step=".0000000000000001" class="form-control" value="{{ isset($carProvider) ? $carProvider->day : old('day') }}" name="day" placeholder="Price" required>
                              </div>
                              <div class="col-md-3 form-group">
                                <label for="">Per Week (₹)</label>
                              <input type="number" step=".0000000000000001" class="form-control" value="{{ isset($carProvider) ? $carProvider->week : old('week') }}" name="week" placeholder="Price" required>
                              </div>
                              <div class="col-md-3 form-group">
                                <label for="">Per Month (₹)</label>
                              <input type="number" step=".0000000000000001" class="form-control" value="{{ isset($carProvider) ? $carProvider->month : old('month') }}"  name="month" placeholder="Price" required>
                              </div>
                              <div class="col-md-3 form-group">
                                <label for="">Deposit (₹)</label>
                              <input type="number" step=".00000001"  class="form-control" value="{{ isset($carProvider) ? $carProvider->deposit : old('deposit') }}" name="deposit" placeholder="Amount" required>
                              </div>
                              @if ($route[2] === 'edit')
                              <?php $head = ['seats', 'Gear type', 'fuel type']; ?>
                                @forelse ($carProvider->specs as $key => $item)
                                <input type="hidden" name="about[{{ $key }}][id]" value="{{ $item->id }}">
                                <div class="col-md-3 form-group">
                                    <label for="">{{ $head[$key] }}</label>
                                  <input type="text"  class="form-control" value="{{ $item->name }}" name="about[{{ $key }}][name]" placeholder="Seats" required>
                                  </div>
                                @empty

                                @endforelse
                              @else

                              <div class="col-md-3 form-group">
                                <label for="">Seats</label>
                              <input type="text"  class="form-control" value="4 Seater" name="about[0][name]" placeholder="Seats" required>
                              </div>
                              <div class="col-md-3 form-group">
                                <label for="">Gear type</label>
                              <input type="text"  class="form-control" value="Automatic" name="about[1][name]" placeholder="Gear Type" required>
                              </div>
                              <div class="col-md-3 form-group">
                                <label for="">Fuel type</label>
                              <input type="text"  class="form-control" value="Diesel" name="about[2][name]" placeholder="Fuel Type" required>
                              </div>
                              <input type="hidden" name="about[0][icon]" value="seat.png">
                              <input type="hidden" name="about[1][icon]" value="gear.png">
                              <input type="hidden" name="about[2][icon]" value="fuel.png">
                              @endif

                              <div class="col-md-3">

                                  <fieldset id="productimageElement1" class="form-group">
                                    <div class="controls">
                                      <label for="categoryImage">Upload Shop Banner Image</label>
                                      <div class="input-group" >
                                        <div class="input-group-prepend">
                                          <span class="input-group-text" id="productImage1">Image</span>
                                        </div>
                                        <div class="custom-file">
                                        <input type="file"  class="custom-file-input" name="image" id="productImageUpload1" aria-describedby="productImage"  @if ($route[1] == 'create') data-validation-required-message="This Image field is required"
                                        required
                                        @endif >
                                        <label class="custom-file-label" for="productImage1">Choose file</label>
                                        </div>
                                      </div>
                                    </div>
                                  </fieldset>
                              </div>
                              <div class="col-md-3">
                                @if(isset($carProvider->image))
                                <div class="my-1">
                                  <img src="{{$carProvider->img_url}} " width="30%" alt="" srcset="">
                                </div>
                                @endif
                              </div>

                    @include('demand.shared._submit', [
                      'entity' => 'provider-cars',
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
