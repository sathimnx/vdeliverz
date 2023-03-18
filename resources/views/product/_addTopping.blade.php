

<!-- Modal -->
<div class="modal fade" id="addNewToppingModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Add New Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('toppings.store')}}" method="post">
                    @method('POST')
                    @csrf
                    <input type="hidden" class="form-control" name="product_id" id="topping_modal_product_id">
                    <div class="row">
                        <div class="col-md-12">
                            <section id="form-control-repeater" class="mt-2">
                                <!-- phone repeater -->
                                <div class="card box-shadow-0">
                                    <div class="card-header">
                                        <h4 class="card-title">Add Product Toppings</h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="contact-repeater">
                                                <div data-repeater-list="toppings">
                                                    <div class="row justify-content-between border mt-2" data-repeater-item>
                                                        <div class="col-md-4 col-12 form-group">
                                                            <label for="">Title</label>
                                                            <input type="text" value="{{old('variant')}}" class="form-control" name="name" placeholder="Title" required>
                                                        </div>
                                                        <div class="col-md-4 col-12 form-group">
                                                            <label for="">Category</label>
                                                            <select class="form-control" id="unit" name="title_id"  autocomplete="new-password" data-placeholder="Select Category..." required>

                                                                @forelse ($titles as $item)
                                                                    <option value="{{$item->id}}" {{$val === $item->id ? 'selected' : '' }}>
                                                                        {{$item->name}}</option>
                                                                @empty

                                                                @endforelse
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 col-12 form-group">
                                                            <label for="">Veg/Non-veg</label>
                                                            <select class="form-control" id="size" name="variety"  autocomplete="new-password" data-placeholder="Select Size...">

                                                                <option value="veg" {{$val === 'veg' ? 'selected' : '' }}>Veg</option>
                                                                <option value="non-veg" {{$val === 'non-veg' ? 'selected' : '' }}>Non-Veg</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 col-12 form-group">
                                                            <label for="">Price (₹)</label>
                                                            <input type="number" step=".0000000000000001" class="form-control" value="{{old('actual_price')}}" name="price" placeholder="Price" required>
                                                        </div>
                                                        <div class="col-md-4 col-12 form-group">
                                                            <label for="">Available Count</label>
                                                            <input type="number" class="form-control" value="{{old('available')}}" name="available" placeholder="Available" required>
                                                        </div>
                                                        <div class="col-md-2 col-12 form-group">
                                                            <button class="btn btn-icon btn-danger rounded-circle mt-2" type="button" data-repeater-delete>
                                                                <i class="bx bx-x" ></i>
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
                <p>Are you sure you want to delete this Topping? </p>
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
