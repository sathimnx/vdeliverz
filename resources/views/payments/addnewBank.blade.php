

  <!-- Modal -->
  <div class="modal fade" id="addnewBank" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Add New Bank</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        <form action="{{route('banks.store')}}" method="post">
            @method('POST')
            @csrf
            <section id="form-control-repeater" class="mt-2">
                <!-- phone repeater -->
                  <div class="">
                    <div class="card-header py-0">
                      <h4 class="card-title">Add Bank Account</h4>
                    </div>
                    <div class="card-content">
                      <div class="card-body">
                        <div class="contact-repeater">
                          <div>

                              <div class="row justify-content-between" >
                                  <input type="hidden" class="form-control" value="{{ $shop->id }}" name="shop_id" id="modal_product_id">
                                  <div class="col-md-12 col-12 form-group">
                                    <label for="">Account holder's name</label>
                                  <input type="text" value="" class="form-control" name="name" placeholder="Account holder's name" required>
                                  </div>
                                  <div class="col-md-12 col-12 form-group">
                                    <label for="">Bank Name</label>
                                  <input type="text" class="form-control" value="" name="bank" placeholder="Bank Name" required>
                                  </div>
                                  <div class="col-md-12 col-12 form-group">
                                    <label for="">Bank Account number</label>
                                  <input type="text" class="form-control" value="" name="acc_no" placeholder="Bank Account number" required>
                                  </div>
                                  <div class="col-md-12 col-12 form-group">
                                    <label for="">IFSC Code</label>
                                  <input type="text" class="form-control" value="" name="ifsc" placeholder="IFSC Code" required>
                                  </div>
                                  <div class="col-md-6 col-12 form-group">
                                    <label for="">Bank branch name</label>
                                  <input type="text" class="form-control" value="" name="branch" placeholder="Bank branch name" required>
                                  </div>
                                  <div class="col-md-6 col-12 form-group">
                                    <label for="">City</label>
                                  <input type="text" class="form-control" value="" name="city" placeholder="City" required>
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

