<table id="users-list-datatable" class="table zero-configuration">
    <thead>
        <tr>
            <th>S.no</th>
            @role('admin')
            <th>Shop Name</th>
            @endrole
            <th>Order Referel</th>
            <th>Customer mobile</th>
            <th>Order Amount</th>
            <th style="white-space: nowrap">Expect Delivery</th>
            <th>Status</th>
            <th>Order Type</th>
            <th style="white-space: nowrap">Delivered At</th>
            <th>Actions</th>
            @can('view_orders')
                <th>Detail</th>
            @endcan

        </tr>
    </thead>
    <tbody>

        @if (isset($orders) && !empty($orders))
            @foreach ($orders as $k => $item)
                <tr>
                    <td>{{ $orders->perPage() * ($orders->currentPage() - 1) + $k + 1 }}</td>
                    @role('admin')
                    <td><a href="{{ route('shops.show', $item->shop->id) }}"
                            class="mr-1">{{ $item->shop->name }}</a></td>
                    @endrole
                    <td>{{ $item->prefix . $item->id }}</td>
                    <td>{{ $item->user->mobile }}</td>
                    <td>{{ $item->amount }} ₹</td>
                    <td>{{ $item->expected_time->addMinutes(30) }}</td>
                    <td>{{ $item->order_state }}</td>
                    <td>{{ $item->type == 0 ? 'COD' : 'Online' }}</td>
                    <td>{{ $item->delivered_at }}</td>
                    <td>
                        @if ($item->order_status == 7 || $item->order_status == 5)
                            <a href="#" onclick="showReviewOrder('{{ $item->prefix }}', '{{ $item->id }}')">
                                <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                    Review
                                </button>
                            </a>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('orders.show', $item->id) }}" class="mr-1">
                            <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                                <i class="bx bx-show"></i>
                            </button>
                        </a>
                    </td>

                </tr>
            @endforeach
        @endif


    </tbody>
</table>
<div class="mx-auto" style="width: fit-content">{{ $orders->links() }}</div>
