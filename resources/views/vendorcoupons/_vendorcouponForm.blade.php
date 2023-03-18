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
<div class="form-group mx-auto" >
  <div class="form-group">
  <div class="controls">
    <label for="">Select Category</label>
 <select name="category" class="form-control input-lg dynamic" id="category" data-dependent="couponCategory" onchange="getshops()" >
       <option value="">Select Category</option>
      @forelse ($Categories as $Category)
      <option  value="{{$Category->id}}" {{isset($coupon) ? $coupon->category_id == $Category->id ? 'selected' : '' : ''}}>
        {{$Category->name}}</option>
      @empty

      @endforelse
  </select>
     
    <label for="">Select Shop</label>
    <?php $shopId = [];
        if (isset($coupon->shop_id)) {
            $shopId = explode(',', $coupon->shop_id);
        } ?>
        
        @if(isset($shopId[0]))
        {{ $shopId[0] }}
        @endif
        
     <select name="couponShop[]" id="couponShop" data-placeholder="Select Shop..." data-dependent="couponCategory"  onchange="getSubcategories()"  data-select2-id="multiple-select2-icons3"
     multiple="" class="select2-icons form-control select2-hidden-accessible">
      <?php  if($Type == 'Create'){ ?>

       <option value="0">All Shops</option>
       
      <?php } else {?>
      @if($shopId != null && $shopId[0] == '0')
            <option value="0"  selected>All Shops</option>
            @endif
          @forelse ($shops as $shop)
          <option  value="{{$shop->id}}"  {{ in_array($shop->id,$shopId) ? 'selected' : '' }}>
            {{$shop->name}}</option>
          @empty
    
          @endforelse
          
       <?php }?>
       
      </select>
  
  <div class="controls">
<label>select Product Category</label>
    <?php $subCategoryId = [];
        if (isset($coupon->sub_category_id)) {
            $subCategoryId = explode(',', $coupon->sub_category_id);
        } ?>
     <select data-placeholder="Select Product category..." name="product_category[]" onchange="getproducts()"
                                                autocomplete="new-password"
                                                class="select2-icons form-control select2-hidden-accessible"
                                                id="product_category" multiple="" data-dependent="products"
                                                data-select2-id="multiple-select2-icons1" tabindex="-1" aria-hidden="true">
         
    
                                            
    <?php  if($Type == 'Create'){ ?>

       <option value="0">All Product Category</option>
       
      <?php } else {?>
        @if(isset($subCategoryId[0]))
      @if($subCategoryId != null && $subCategoryId[0] == '0')
            <option value="0"  selected>All Product Category</option>
            @endif @endif
          @forelse ($subCategories as $subCategory)
          <option  value="{{$subCategory->id}}"  {{ in_array($subCategory->id,$subCategoryId) ? 'selected' : '' }}>
            {{$subCategory->name}}</option>
          @empty
    
          @endforelse
          
       <?php }?>
       
    </select>
    
    
    
  <label>select Product</label>
  <?php $Product_Id = [];
        if (isset($coupon->product_id)) {
            $Product_Id = explode(',', $coupon->product_id);
            
        } ?>
      <select data-placeholder="Select Products..." name="product_dtl[]" 
                                                autocomplete="new-password"
                                                class="select2-icons form-control select2-hidden-accessible"
                                                id="product_dtl" multiple="" 
                                                data-select2-id="multiple-select2-icons2" tabindex="-1" aria-hidden="true">
          
      
       <?php  if($Type == 'Create'){ ?>
     
       <option value="0">All Products</option>
       
      <?php } else {?>
      @if(isset($Product_Id[0]))
         @if(isset($Product_Id[0]) == '0')
            <option value="0"  selected>All Products</option>
            @endif @endif
          @forelse ($products as $product)
          <option  value="{{$product->id}}" {{ in_array($product->id,$Product_Id) ? 'selected' : '' }}>
            {{$product->name}}</option>
          @empty
    
          @endforelse
          
       <?php }?>
    </select>

</div>
</div>


 
 
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
  <textarea class="form-control" name="coupon_description" rows="4" placeholder="Descripton">{{isset($coupon) ? $coupon->coupon_description : null}}</textarea>
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
        <label>Maximum Discount Amount</label>
        <input type="text" class="form-control" placeholder="Max Discount Amount"
            value="{{isset($coupon) ? $coupon->max_order_amount : ''}}" name="max_order_amount" required
            data-validation-required-message="This field is required">
    </div>
    </div>
    <div class="form-group">
      <div class="controls">
          <label>Minimum Order value</label>
          <input type="text" class="form-control" placeholder="Minimum Order value"
              value="{{isset($coupon) ? $coupon->min_order_amt : ''}}" name="min_order_amt" required
              data-validation-required-message="This field is required">
      </div>
      </div>
    <div class="form-group">
      <div class="controls">
          <label>Enter : No. of times users can use this coupon</label>
          <input type="text" class="form-control" placeholder="Number of times users can use this coupon"
              value="{{isset($coupon) ? $coupon->Discount_use_amt : ''}}" name="Discount_use_amt" required
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
     
     <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
 <script>
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

function getshops()
        {
            let categoryId= $('#category').val();
                 $.ajax({
                    type:'POST',
                    url:"{{ route('getshopdtls') }}",
                    data: {
                    "_token": "{{ csrf_token() }}","category":categoryId
                    },
                    success: function(data) {
                        $('#couponShop').html(data.html);
                    }
                 });
        }
        function getSubcategories()
        {
            let shopId= $('#couponShop').val();
            let categoryId= $('#category').val();
                 $.ajax({
                    type:'POST',
                    url:"{{ route('getProductCategory') }}",
                    data: {
                    "_token": "{{ csrf_token() }}","shop_id":shopId,"category_id":categoryId
                    },
                    success: function(data) {
                        $('#product_category').html(data.html);
                    }
                 });
        }
        
        function getproducts() {
            let product_category= $('#product_category').val();
            let shopId= $('#couponShop').val();
            let categoryId= $('#category').val();
                 $.ajax({
                    type:'POST',
                    url:"{{ route('getProducts') }}",
                    data: {
                    "_token": "{{ csrf_token() }}","sub_category_id":product_category,"category_id":categoryId,"shop_id":shopId
                    },
                    success: function(data) {
                        $('#product_dtl').html(data.html);
                    }
                 });
        }
        </script>
        