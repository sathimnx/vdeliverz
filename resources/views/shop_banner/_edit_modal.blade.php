 <!-- Modal -->
 <div class="modal fade" id="productCategoryModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="staticBackdropLabel"> Banners</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                 <form action="{{ route('shop-banners.store') }}" method="post" enctype="multipart/form-data">
                     @method('POST')
                     @csrf
                     <section id="form-control-repeater" class="mt-2">
                         <!-- phone repeater -->
                         <div class="">
                             <div class="card-header py-0">
                                 <h4 class="card-title">Add Banner</h4>
                             </div>
                             <div class="card-content">
                                 <div class="card-body">
                                     <div class="row justify-content-between">
                                         @role('admin')
                                         <div class="col-md-12">
                                             <div class="form-group">
                                                 <div class="controls">
                                                     <label>Select Shop <span class="text-danger">*</span></label>
                                                     <select class="form-control" id="shop_id" name="shop_id"
                                                         data-placeholder="Select Shop..." required>
                                                         <option value="">Select Shop</option>
                                                         @forelse ($shops as $shop)
                                                             <option value="{{ $shop->id }}">{{ $shop->name }}
                                                             </option>
                                                         @empty

                                                         @endforelse

                                                     </select>
                                                 </div>
                                             </div>
                                         </div>
                                     @else
                                         <input type="hidden" name="shop_id" value="{{ auth()->user()->shop->id }}">
                                         @endrole
                                         {{-- <div class="col-md-12 form-group"> --}}
                                         {{-- <label for="">Name</label> --}}
                                         {{-- <input type="text" value="" class="form-control" name="name" placeholder="Category Name" required> --}}
                                         {{-- </div> --}}
                                         {{-- <div class="col-md-12 form-group"> --}}
                                         {{-- <fieldset class="form-group"> --}}
                                         {{-- <label for="categoryDescription">Description</label> --}}
                                         {{-- <textarea class="form-control" name="description" id="categoryDescription" rows="4" placeholder="Descripton" required></textarea> --}}
                                         {{-- </fieldset> --}}
                                         {{-- </div> --}}
                                         <div class="col-md-12">
                                             <img src="" alt="" id="icon_image" srcset="" style="width:inherit">
                                             <fieldset class="form-group">
                                                 <label for="storePANImage">Upload Icon Image </label>
                                                 <div class="input-group">
                                                     <div class="input-group-prepend">
                                                         <span class="input-group-text" id="storePANImage">Icon
                                                             Image</span>
                                                     </div>
                                                     <div class="custom-file">
                                                         <input type="file" class="custom-file-input" name="image"
                                                             id="storePANImageUpload" aria-describedby="storePANImage">
                                                         <label class="custom-file-label" for="storePANImage">Choose
                                                             file</label>
                                                     </div>
                                                 </div>
                                             </fieldset>
                                         </div>
                                         {{-- <div class="col-md-12">
                                    <img src="" alt="" id="banner_image" srcset="" style="width:inherit">
                                  <fieldset class="form-group">
                                      <label for="storePANImage2">Upload Banner Image </label>
                                      <div class="input-group" >
                                      <div class="input-group-prepend">
                                          <span class="input-group-text" id="storePANImage2">Banner Image</span>
                                      </div>
                                      <div class="custom-file">
                                      <input type="file"  class="custom-file-input"  name="banner" id="storePANImageUpload" aria-describedby="storePANImage2">
                                      <label class="custom-file-label" for="storePANImage2">Choose file</label>
                                      </div>
                                      </div>
                                  </fieldset>
                              </div> --}}
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
