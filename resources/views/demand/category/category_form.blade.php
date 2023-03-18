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
            @endif Product</h2>
          </div>
          <div class="card-content">
            <div class="card-body">
                @if ($route[1] == 'create')
                <form class="form-horizontal" action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
                    @method('POST')
                    @else
                    <form class="form-horizontal" action="{{ route('products.update', request()->product) }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
                        @method('PUT')
                    @endif
                    @csrf
                    <h4 class="card-title">Shop Details</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Shop Category</label>
                                    <select class="select2 form-control" id="category_id" name="category_id" data-validation-required-message="This Shop Category field is required"  data-placeholder="Select Shop Category..." required>
                                        <option value="">Select Shop Category</option>
                                        @forelse ($categories as $category)
                                        <option value="{{$category->id}}" {{isset($product) ? $product->category->id == $category->id ? 'selected' : '' : ''}}>{{$category->name}}</option>
                                        @empty

                                        @endforelse
                                    </select>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="controls">
                                        <label>Product Name</label>
                                        <input type="text" class="form-control" placeholder="Product Name"
                                    value="{{isset($product) ? $product->name : old('name')}}" name="name"
                                            data-validation-required-message="This Product Name field is required" required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                <div class=" form-group" style="width:30%">
                                    <label for="">Variety</label>
                                    <select class="form-control" id="variety" name="variety"  autocomplete="new-password" data-placeholder="Select Variant..." required>
                                        <?php $val = null; if(isset($product)){ $val = $product->variety; }  ?>
                                        <option value="veg" {{$val === 'veg' ? 'selected' : '' }}>veg</option>
                                       ` <option value="non-veg" {{$val === 'non-veg' ? 'selected' : '' }}>non-veg</option>
                                    </select>
                                  </div>
                                  <div class="form-group" style="width: 65%">
                                    <div class="controls" id="cuisineDropdown" >
                                        <label>Product Cuisine</label>
                                        <select class="select2 form-control" id="cuisine_id" name="cuisine_id" data-validation-required-message="This Product Cuisine field is required" required data-placeholder="Select Product Cuisine...">
                                            <option value="">Select Product Cuisine</option>
                                            @forelse ($cuisines as $cuisine)
                                            <option value="{{$cuisine->id}}" {{isset($product) ? $product->cuisine->id == $cuisine->id ? 'selected' : '' : ''}}>{{$cuisine->name}}</option>
                                            @empty

                                            @endforelse
                                        </select>
                                  </div>
                                  </div>
                            </div>
                                @if(isset($product) && $product->image != null)
                                <div class="my-1">
                                <img src="{{ asset($product->image) }}" width="30%" alt="" srcset="">
                                </div>
                                @endif

                                <fieldset class="form-group" id="profile_image">
                                <div class="controls">
                                    <label for="storePANImage">Upload Product Image </label>
                                <div class="input-group" >
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="storePANImage">Product Image</span>
                                </div>
                                <div class="custom-file">
                                <input type="file"  class="custom-file-input"  name="image" id="storePANImageUpload" aria-describedby="storePANImage" @if($route[1] == 'create') data-validation-required-message="This image field is required" required @endif>
                                <label class="custom-file-label" for="storePANImage">Choose file</label>
                                </div>
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
                                    <div class="controls">
                                        <label>Shop</label>
                                    <select class="select2 form-control" id="shop_id" name="shop_id" data-validation-required-message="This Shop Name field is required"  data-placeholder="Select Shop...">
                                        <option value="">Select Shop</option>
                                        @forelse ($shops as $shop)
                                        <option value="{{$shop->id}}" {{isset($product) ? $product->shop->id == $shop->id ? 'selected' : '' : ''}}>{{$shop->name}}</option>
                                        @empty

                                        @endforelse
                                    </select>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-group" id="catDropdown" style="width: 70%">
                                            <label>Product Category</label>
                                            <select class="select2 form-control" id="sub_category_id" name="sub_category_id"  data-placeholder="Select Product Category...">
                                                <option value="">Select Product Category</option>
                                                @forelse ($subCategories as $subCategory)
                                                <option value="{{$subCategory->id}}" {{isset($product) ? $product->subCategory->id == $subCategory->id ? 'selected' : '' : ''}}>{{$subCategory->name}}</option>
                                                @empty

                                                @endforelse
                                            </select>
                                      </div>
                                      <div class="form-group d-none" id="newCatField" style="width: 70%">
                                        <label>New Product Category</label>
                                        <input type="text" class="form-control" placeholder="Product Category"
                                        value="{{old('new_sub_category')}}" id="new_sub_category" name="new_sub_category"
                                        >
                                  </div>
                                      <div class="form-group  m-0" style="width: 30%">
                                        <label></label>
                                        <button type="button" id="addNewCat" class="btn btn-warning float-right">Add New</button>
                                        <button type="button" id="selectOld" class="btn btn-warning float-right d-none">Select List</button>
                                  </div>
                                </div>
                                @push('scripts')
                                    <script>
                                        $('#addNewCat').on('click', function(){
                                            $('#catDropdown').addClass('d-none');
                                            $('#catDropdown select').attr('required', false);
                                            $(this).addClass('d-none');
                                            $('#newCatField').removeClass('d-none');
                                            $('#newCatField input').attr('required', true);
                                            $('#selectOld').removeClass('d-none');
                                        })
                                        $('#selectOld').on('click', function(){
                                            $('#new_sub_category').val(null);
                                            $('#new_sub_category input').attr('required', false);
                                            $('#catDropdown').removeClass('d-none');
                                            $('#catDropdown select').attr('required', true);
                                            $(this).addClass('d-none');
                                            $('#newCatField').addClass('d-none');
                                            $('#addNewCat').removeClass('d-none');
                                        })
                                    </script>
                                @endpush

                                  <fieldset class="form-group">
                                    <label for="productDescription">Description</label>
                                    <textarea class="form-control" name="description" id="productDescription" rows="4" placeholder="Descripton">{{ isset($product->description) ? $product->description : old('description') }}</textarea>
                                </fieldset>
                            </div>
                        </div>

                        <div class="row">
                            @if ($route[1] === 'create')
        <div class="col-md-12">
          <section id="form-control-repeater" class="mt-2">
            <!-- phone repeater -->
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title">Add Product Stocks</h4>
                </div>
                <div class="card-content">
                  <div class="card-body">
                    <div class="contact-repeater">
                      <div data-repeater-list="stocks">

                          <div class="row justify-content-between" data-repeater-item>
                              <div class="col-md-2 col-12 form-group">
                                <label for="">Variant</label>
                              <input type="text" value="{{old('variant')}}" class="form-control" name="variant" placeholder="Variant">
                              </div>
                              <div class="col-md-2 col-12 form-group">
                                <label for="">Unit</label>
                                <select class="form-control" id="unit" name="unit"  autocomplete="new-password" data-placeholder="Select Variant...">
                                    <?php $val = null; if(isset($mapping)){ $val = $mapping->stock->variant; }  ?>
                                    <option value="gms" {{$val === 'gms' ? 'selected' : '' }}>gms</option>
                                    <option value="litre" {{$val === 'litre' ? 'selected' : '' }}>litre</option>
                                    <option value="ml" {{$val === 'ml' ? 'selected' : '' }}>ml</option>
                                    <option value="kgs" {{$val === 'kgs' ? 'selected' : '' }}>kgs</option>
                                </select>
                              </div>
                              <div class="col-md-2 col-12 form-group">
                                <label for="">Actual Price</label>
                              <input type="text" class="form-control" value="{{old('actual_price')}}" name="actual_price" placeholder="Actual Price" required>
                              </div>
                              <div class="col-md-2 col-12 form-group">
                                <label for="">Selling Price</label>
                              <input type="text" class="form-control" value="{{old('selling_price')}}" name="selling_price" placeholder="Selling Price" required>
                              </div>
                              <div class="col-md-2 col-12 form-group">
                                <label for="">Available Count</label>
                              <input type="text" class="form-control" value="{{old('available')}}" name="available" placeholder="Available" required>
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
          @if (isset($product->stocks) && !empty($product->stocks))

          <div class="col-md-12">
            <section id="form-control-repeater" class="mt-2">
              <!-- phone repeater -->
                <div class="card">
                  <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Product Stocks</h4>


                    <div class="">

                      <button class="btn btn-icon rounded-circle btn-primary" onclick="add_new_stock({{$product->id}});" data-toggle="modal"  type="button">
                          <i class="bx bx-plus" style="vertical-align: 0;"></i>
                        </button>
                        @if ($route[1] !== 'show')
                        <span class="ml-1 font-weight-bold text-primary">ADD NEW Stock</span>
                        @endif
                  </div>

                  </div>
                  <div class="card-content">
                    <div class="card-body">
                      <div class="contact-repeater">
                        <div data-repeater-list="stocks">
                            @foreach ($product->stocks as $key => $item)
                            <div class="row justify-content-between" data-repeater-item>
                                <div class="col-md-2 col-12 form-group">
                                    <input type="hidden" name="stock_id" value="{{$item->id}}">
                                  <label for="">Variant</label>
                                <input type="text" value="{{isset($item) ? $item->variant : old('variant')}}" class="form-control" name="variant" placeholder="Variant" >
                                </div>
                                <div class="col-md-2 col-12 form-group">
                                  <label for="">Unit</label>
                                  <select class="form-control" id="unit" name="unit"  autocomplete="new-password" data-placeholder="Select Variant..." >
                                      <?php $val = null; if(isset($item)){ $val = $item->unit; }  ?>
                                      <option value="gms" {{$val === 'gms' ? 'selected' : '' }}>gms</option>
                                      <option value="litre" {{$val === 'litre' ? 'selected' : '' }}>litre</option>
                                      <option value="ml" {{$val === 'ml' ? 'selected' : '' }}>ml</option>
                                      <option value="kgs" {{$val === 'kgs' ? 'selected' : '' }}>kgs</option>
                                  </select>
                                </div>
                                <div class="col-md-2 col-12 form-group">
                                  <label for="">Actual Price</label>
                                <input type="text" class="form-control" value="{{isset($item) ? $item->actual_price : old('actual_price')}}" name="actual_price" placeholder="Actual Price" required>
                                </div>
                                <div class="col-md-2 col-12 form-group">
                                  <label for="">Selling Price</label>
                                <input type="text" class="form-control" value="{{isset($item) ? $item->price : old('selling_price')}}" name="selling_price" placeholder="Selling Price" required>
                                </div>
                                <div class="col-md-2 col-12 form-group">
                                  <label for="">Available Count</label>
                                <input type="text" class="form-control" value="{{isset($item) ? $item->available : old('available')}}" name="available" placeholder="Available" required>
                                </div>

                                <div class="col-md-2 col-12 form-group  m-auto">
                                <label for=""></label>
                                  <button type="button" onclick="delete_stock({{$item->id}});" data-toggle="modal" class="btn-outline-danger" style="margin-top: 6px;">
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
