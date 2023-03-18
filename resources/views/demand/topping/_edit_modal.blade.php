 <!-- Modal -->
 <div class="modal fade" id="productCategoryModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel"> Categories</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        <form action="{{route('titles.store')}}" method="post" enctype="multipart/form-data">
            @method('POST')
            @csrf
            <section id="form-control-repeater" class="mt-2">
                <!-- phone repeater -->
                  <div class="">
                    <div class="card-header py-0">
                      <h4 class="card-title">Add Topping Category</h4>
                    </div>
                    <div class="card-content">
                      <div class="card-body">
                              <div class="row justify-content-between" >
                                  @role('admin')
                                  <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Select Shop</label>
                                        <select class="form-control" id="shop_id" name="shop_id"  data-placeholder="Select Shop..." >
                                            <option value="">Select Shop...</option>
                                             @forelse ($shops as $shop)
                                            <option value="{{$shop->id}}">{{$shop->name}}</option>
                                            @empty

                                            @endforelse
                                        </select>
                                    </div>
                                  </div>
                                  @else
                                      <input type="hidden" name="shop_id" value="{{auth()->user()->shop->id}}">
                                  @endrole
                                  <div class="col-md-12 form-group">
                                    <label for="">Name</label>
                                  <input type="text" value="" class="form-control" name="name" placeholder="Category Name" required>
                                  </div>
{{--                                  <div class="col-md-12">--}}
{{--                                      <img src="" alt="" srcset="">--}}
{{--                                    <fieldset class="form-group">--}}
{{--                                        <label for="storePANImage">Upload Category Image </label>--}}
{{--                                        <div class="input-group" >--}}
{{--                                        <div class="input-group-prepend">--}}
{{--                                            <span class="input-group-text" id="storePANImage">Category Icon Image</span>--}}
{{--                                        </div>--}}
{{--                                        <div class="custom-file">--}}
{{--                                        <input type="file"  class="custom-file-input"  name="image" id="storePANImageUpload" aria-describedby="storePANImage">--}}
{{--                                        <label class="custom-file-label" for="storePANImage">Choose file</label>--}}
{{--                                        </div>--}}
{{--                                        </div>--}}
{{--                                    </fieldset>--}}
{{--                                </div>--}}
                              </div>
                      </div>
                    </div>
                  </div>

                <!-- /phone repeater -->

          </section>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Add</button>
        </div>
    </form>
      </div>
    </div>
  </div>
