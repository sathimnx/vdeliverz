<table class="table zero-configuration " style="width:100%;">
    <thead>
        <tr>
                <th>S.No</th>
                <th>Coupon Code</th>
                <th>Max Amount</th>
                <th>Min Amount</th>
                <th>No.of times users can use coupon</th>
                <th>Shop</th>
                <th>Sub Category</th>
                <th>Product</th>
                <th>Coupon Percentage</th>
                <th style="white-space: nowrap">&nbsp;&nbsp;Offer Ends&nbsp;&nbsp;</th>
                <th>Description</th>
                <th>Status</th>
                <th style="white-space: nowrap;">&nbsp;&nbsp;Created at&nbsp;&nbsp;</th>
                
                @canany(['edit_vendorcoupons', 'delete_vendorcoupons'])
                 <th>Actions</th>
                @endcanany

        </tr>
    </thead>
    <tbody>
        @if (isset($coupons) && !empty($coupons))
            @foreach ($coupons as $k => $item)
                <tr>
                        <td>{{ $k + $coupons->firstItem() }}</td>
                        <td>{{ $item->coupon_code }}</td>
                        <td>{{ $item->max_order_amount }}</td>
                        <td>{{ $item->min_order_amt }}</td>
                        <td>{{ $item->Discount_use_amt }}</td>
                        <td>  @if (isset($item->shop)) 
                            {{ $item->shop->name }}
                          @endif
                        </td>
                        <td>  @if (isset($item->sub_category_name)) 
                            {{ $item->sub_category_name }}
                          @endif
                        </td>
                        <td>  @if (isset($item->product_name)) 
                        
                            {{ $item->product_name }}
                          @endif
                        </td>
                        
                        <td>{{ $item->coupon_percentage }}%</td>
                        <td>{{ date('d-M-Y H:i:s', strtotime($item->expired_on)) }}</td>
                        <td style="max-width: 100px; cursor: pointer;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;" data-toggle="tooltip" data-placement="top"
                        title="{{ $item->coupon_description }}">
                        {{ $item->coupon_description }}</td>
                        <td>
                        <div
                            class="custom-control custom-switch custom-switch-glow custom-control-inline">
                            <input type="checkbox" class="custom-control-input"
                                {{ $item->active == 1 ? 'checked' : '' }} value=""
                                onchange="change_status('{{ $item->id }}', 'coupons', '#customSwitchGlow{{ $k }}', 'active');"
                                id="customSwitchGlow{{ $k }}">
                            <label class="custom-control-label"
                                for="customSwitchGlow{{ $k }}">
                            </label>
                        </div>
                        </td>
                        <?php $created_at = date('d-M-Y H:i:s', strtotime($item->created_at)); ?>
                        <td>{{ $created_at }}</td>
                         <td>
                    <a href="{{route('vendorcoupons.edit', $item->id)}}">
                        <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                            <i class="bx bx-edit-alt"></i>
                        </button>
                        </a>
                          <button type="submit" class="btn-outline-info" onclick="destroyvendorCoupons({{$item->id}})"
                                                            data-icon="warning-alt">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                   </td>
                        

                                                </tr>
         
            @endforeach

        @endif
    </tbody>
</table>
<div class="mx-auto" style="width: fit-content">{{ $coupons->links() }}</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
 <script>
 function destroyvendorCoupons(id)
 { 
    var result = confirm("Are you sure want to delete this coupon?");
    if (result) {
      $.ajax({
        type:'POST',
        url:"{{ route('deletevendorCoupons') }}",
        data: {
           
        "_token": "{{ csrf_token() }}","coupon_id":id
        },
        success: function(data) {
            alert('Coupon details removed successfully');
        }
     });
    }
 }
 </script>
