@csrf

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
<div class="row" >

<div class="col-sm-6">

  <div class="form-group">
  <div class="controls">
      <label>Coupon Code</label>
      <input type="text" class="form-control" placeholder="Coupon Code"
          value="{{isset($coupon) ? $coupon->coupon_code : ''}}" name="coupon_code" required
          data-validation-required-message="This Coupon Code field is required">
  </div>
  </div>
  <?php $date = isset($coupon->expired_on) ? date('Y-m-d', strtotime($coupon->expired_on)) : null ?>
  <?php $time = isset($coupon->expired_on) ? date('H:i', strtotime($coupon->expired_on)) : null ?>
  <div class="d-flex">
  <div style="width: 60%">
    <fieldset class="form-group ">
        <div class="conrols">
            <label for="">End Date</label>
    <input type="date" class="form-control" value="{{isset($date) ? $date : null}}" name="coupon_date" placeholder="Select Date"  data-validation-required-message="This End date field is required" required>
  </div>

  </fieldset>
  </div>
  <div class="form-group mx-auto" style="width: 30%">
  <div class="controls">
    <label>Time</label>
    <input type="time" class="form-control" placeholder="End Time"
        value="{{isset($time) ? $time : ''}}" name="coupon_time"  data-validation-required-message="This End time field is required" required>
  </div>
  </div>

  </div>
  <fieldset class="form-group">
  <label for="offerDescription">Description</label>
  <textarea class="form-control" name="coupon_description" id="offerDescription" rows="4" placeholder="Descripton">{{isset($coupon) ? $coupon->coupon_description : null}}</textarea>
  </fieldset>

  </div>
  <div class="col-md-6">

  <fieldset class="form-group">
  <label for="">Coupon percentage</label>
  <div class="input-group">

  <input type="text" class="form-control" name="coupon_percentage" value="{{isset($coupon) ? $coupon->coupon_percentage : null}}" placeholder="Percentage" aria-describedby="basic-addon1" required>
    <div class="input-group-append">
      <span class="input-group-text" id="basic-addon1">%</span>
    </div>
  </div>
  </fieldset>
  <div class="form-group">
    <div class="controls">
        <label>Max Amount</label>
        <input type="text" class="form-control" placeholder="Max Amount"
            value="{{isset($coupon) ? $coupon->max_order_amount : ''}}" name="max_order_amount" required
            data-validation-required-message="This field is required">
    </div>
    </div>
  {{-- <fieldset id="offerimageElement" class="form-group">
  <label for="offerImage">Upload Offer Image</label>
  <div class="input-group" >
  <div class="input-group-prepend">
    <span class="input-group-text" id="offerImage">Image</span>
  </div>
  <div class="custom-file">
  <input type="file"  class="custom-file-input" onchange="imageValidate('#productImageUpload', '#productimageElement');" name="offer_image" id="offerImageUpload" aria-describedby="productImage" required>
  <label class="custom-file-label" for="offerImage">Choose file</label>
  </div>
  </div>
  <div class="invalid-feedback">
  <i class="bx bx-radio-circle"></i>
  Image should be jpg, jpeg Format
  </div>
  </fieldset> --}}
  </div>


        @push('scripts')



        @endpush


    </div>
