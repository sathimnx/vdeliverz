

<!-- Modal -->
<div class="modal fade" id="addNewToppingModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Add Provider Services</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('demand.add-sub-services.store')}}" method="post">
                    @method('POST')
                    @csrf
                    <input type="hidden" class="form-control" name="provider_id" id="topping_modal_product_id">
                    <div class="row">
                        <div class="col-md-12">
                            <section id="form-control-repeater" class="mt-2">
                                <!-- phone repeater -->
                                <div class="card box-shadow-0">
                                    <div class="card-header">
                                        <h4 class="card-title">Add Provider Services</h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="contact-repeater">
                                                <div data-repeater-list="services">
                                                    <div class="row justify-content-between mb-2" data-repeater-item>
                                                        <div class="col-md-4 form-group">
                                                            <label for="">Services</label>
                                                            <select class="form-control" id="unit" name="sub_service_id"  autocomplete="new-password" data-placeholder="Select Service..." required>
                                                                <option value="">Select Service</option>
                                                               @forelse ($services as $item)
                                                               <optgroup label="{{ $item->name }}">
                                                               @foreach ($item->subServices as $sub)
                                                               <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                                               @endforeach
                                                              </optgroup>
                                                               @empty

                                                               @endforelse

                                                            </select>
                                                          </div>

                                                          <div class="col-md-3 form-group">
                                                            <label for="">Per Hour (₹)</label>
                                                          <input type="number" step=".0000000000000001" class="form-control" value="{{old('hour_price')}}" name="hour_price" placeholder="Price" required>
                                                          </div>
                                                          <div class="col-md-3 form-group">
                                                            <label for="">Per Job (₹)</label>
                                                          <input type="number" step=".0000000000000001" class="form-control" value="{{old('job_price')}}" name="job_price" placeholder="Price" required>
                                                          </div>
                                                          <div class="col-md-2 form-group">
                                                          <button class="btn btn-icon btn-danger rounded-circle mt-2 float-left" type="button" data-repeater-delete>
                                                              <i class="bx bx-x" style="vertical-align: middle;"></i>
                                                          </button>
                                                          </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-2">
                                                    <div class="float-left mt-2">
                                                        <button class="btn btn-icon rounded-circle btn-primary" id="addNewStep" type="button" data-repeater-create>
                                                            <i class="bx bx-plus" ></i>
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
                    </div>

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
<div class="modal fade" id="deleteToppingModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="deleteStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteStockModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-danger">
                <p>Are you sure you want to delete this Service? </p>
                <form id="toppingDeleteForm" method="post">
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
