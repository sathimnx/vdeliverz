<table class="table zero-configuration " style="width:100%;">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Coupon Code</th>
            <th>Max Amount</th>
            <th>Coupon Percentage</th>
            <th style="white-space: nowrap">&nbsp;&nbsp;Offer Ends&nbsp;&nbsp;</th>
            <th>Description</th>
            <th>Status</th>
            <th style="white-space: nowrap;">&nbsp;&nbsp;Created at&nbsp;&nbsp;</th>
            @canany(['edit_coupons', 'delete_coupons'])
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
                    <td>{{ $item->coupon_percentage }}%</td>
                    <td>{{ date('d-M-Y H:i:s', strtotime($item->expired_on)) }}</td>
                    <td style="max-width: 100px; cursor: pointer;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;" data-toggle="tooltip" data-placement="top"
                        title="{{ $item->coupon_description }}">
                        {{ $item->coupon_description }}</td>
                    <td>
                        <div class="custom-control custom-switch custom-switch-glow custom-control-inline">
                            <input type="checkbox" class="custom-control-input"
                                {{ $item->active == 1 ? 'checked' : '' }} value=""
                                onchange="change_status('{{ $item->id }}', 'coupons', '#customSwitchGlow{{ $k }}', 'active');"
                                id="customSwitchGlow{{ $k }}">
                            <label class="custom-control-label" for="customSwitchGlow{{ $k }}">
                            </label>
                        </div>
                    </td>
                    <?php $created_at = date('d-M-Y H:i:s', strtotime($item->created_at)); ?>
                    <td>{{ $created_at }}</td>
                    @canany(['edit_coupons', 'delete_coupons'])
                        <td class="text-center">
                            @include('shared._actions', [
                            "entity" => "coupons",
                            "id" => $item->id
                            ])
                        </td>

                    @endcanany


                </tr>
            @endforeach

        @endif
    </tbody>
</table>
<div class="mx-auto" style="width: fit-content">{{ $coupons->links() }}</div>
