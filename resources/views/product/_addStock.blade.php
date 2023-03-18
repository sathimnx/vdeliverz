

  <!-- Modal -->
  <div class="modal fade" id="addNewStockModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Add New Stock</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        <form action="{{route('stocks.index')}}" method="post">
            @method('POST')
            @csrf
            <section id="form-control-repeater" class="mt-2">
                <!-- phone repeater -->
                  <div class="">
                    <div class="card-header py-0">
                      <h4 class="card-title">Add Product Stocks</h4>
                    </div>
                    <div class="card-content">
                      <div class="card-body">
                        <div class="contact-repeater">
                          <div>

                              <div class="row justify-content-between" >
                                  <input type="hidden" class="form-control" name="product_id" id="modal_product_id">
                                  <div class="col-md-4 col-12 form-group">
                                    <label for="">Variant</label>
                                  <input type="text" value="" class="form-control" name="variant" placeholder="Variant">
                                  </div>
                                  <div class="col-md-4 col-12 form-group">
                                    <label for="">Unit</label>
                                    <select class="form-control" id="unit" name="unit"  autocomplete="new-password" data-placeholder="Select Variant..." >

                                        <option value="gms" >gms</option>
                                        <option value="litre">litre</option>
                                        <option value="ml" >ml</option>
                                        <option value="kgs" >kgs</option>
                                    </select>
                                  </div>
                                  <div class="col-md-4 col-12 form-group">
                                    <label for="">Size</label>
                                  <input type="text" value="" class="form-control" name="size" placeholder="Select Size...">
                                  </div>
                                  {{-- <div class="col-md-4 col-12 form-group">
                                      <label for="">Size</label>
                                      <select class="form-control" id="size" name="size"  autocomplete="new-password" data-placeholder="Select Size...">
                                          <option value="">Choose Size</option>
                                          <option value="small" {{$val === 'small' ? 'selected' : '' }}>Small</option>
                                          <option value="medium" {{$val === 'medium' ? 'selected' : '' }}>Medium</option>
                                          <option value="large" {{$val === 'large' ? 'selected' : '' }}>Large</option>

                                      </select>
                                  </div> --}}
                                  <div class="col-md-4 col-12 form-group">
                                    <label for="">Actual Price</label>
                                  <input type="text" class="form-control" value="" name="actual_price" placeholder="Actual Price" required>
                                  </div>
                                  <div class="col-md-4 col-12 form-group">
                                    <label for="">Selling Price</label>
                                  <input type="text" class="form-control" value="" name="selling_price" placeholder="Selling Price" required>
                                  </div>
                                  <div class="col-md-4 col-12 form-group">
                                    <label for="">Available Count</label>
                                  <input type="text" class="form-control" value="" name="available" placeholder="Available" required>
                                  </div>

                              </div>
                          </div>

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


  {{-- Delete Stocks --}}



  <!-- Modal -->
  <div class="modal fade" id="deleteStockModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="deleteStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteStockModalLabel"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body text-danger">
          <p>Are you sure you want to delete this stock? </p>
        <form id="stockDeleteForm" method="post">
            @method('DELETE')
            @csrf


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
    </form>
      </div>
    </div>
  </div>
