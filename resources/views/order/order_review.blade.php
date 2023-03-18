<!-- Modal -->
<div class="modal fade" id="reviewOrder" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Review Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('review-order.index')}}" method="post">
                    @method('POST')
                    @csrf
                    <input type="hidden" class="form-control" name="order_id" id="modal_order_id">
                    <div class="card-header py-0">
                        <h4 class="card-title">Review Order</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="contact-repeater" id="dynamicReviewOrder">

                            </div>
                        </div>
                    </div>
            <div class="modal-footer">
{{--                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Close</button>--}}
                <button type="submit" class="btn btn-primary">Confirm</button>
            </div>
            </form>
        </div>
    </div>
</div>
