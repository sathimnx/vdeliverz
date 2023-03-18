<div class="row justify-content-between" >
    <div class="col-md-6 col-12 form-group pb-2">
        @if($order->order_status == 7)
        <fieldset>
            <div class="radio radio-info radio-glow">
                <input type="radio" id="action1" value="1" name="action" checked>
                <label for="action1">Accept & Assign</label>
            </div>
        </fieldset>
            @endif
    </div>
    <div class="col-md-6 col-12 form-group">
        @if($order->order_status == 7)
        <fieldset>
            <div class="radio radio-info radio-glow">
                <input type="radio" id="action2" value="5" name="action">
                <label for="action2">Accept</label>
            </div>
        </fieldset>
        @endif
    </div>
    <div class="col-md-6 col-12 form-group">
        @if($order->order_status == 5)
        <fieldset>
            <div class="radio radio-info radio-glow">
                <input type="radio" value="8" id="action3" name="action">
                <label for="action3">Assign</label>
            </div>
        </fieldset>
        @endif
    </div>
    <div class="col-md-6 col-12 form-group">
        @if($order->order_status == 7 || $order->order_status == 5)
        <fieldset>
            <div class="radio radio-danger radio-glow">
                <input type="radio" id="action4" value="6" name="action">
                <label for="action4" class="text-danger">Reject</label>
            </div>
        </fieldset>
            @endif
    </div>
</div>
