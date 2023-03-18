 <!-- Modal -->
 <div class="modal fade" id="productCategoryModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Product Category</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        <form action="{{route('product-categories.store')}}" method="post">
            @method('POST')
            @csrf
            <section id="form-control-repeater" class="mt-2">
                <!-- phone repeater -->
                  <div class="">
                    <div class="card-header py-0">
                      <h4 class="card-title">Add Product Category</h4>
                    </div>
                    <div class="card-content">
                      <div class="card-body">
                              <div class="row justify-content-between" >
                                  <div class="col-md-12 col-12 form-group">
                                    <label for="">Name</label>
                                  <input type="text" value="" class="form-control" name="name" placeholder="Name" required>
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
