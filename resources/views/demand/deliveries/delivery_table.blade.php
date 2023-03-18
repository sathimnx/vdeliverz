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
            <th>Status</th>
            <th>Order Type</th>
            @can('view_orders')
            <th>Actions</th>
            @endcan

        </tr>
    </thead>
    <tbody>

        @if (isset($orders) && !empty($orders))
            @foreach ($orders as $k => $item)
            <tr>
            <td>{{($orders->perPage() * ($orders->currentPage() - 1)) + $k + 1}}</td>
            @role('admin')
            <td>{{$item->cart->shop->name}}</td>
            @endrole
            <td>{{$item->prefix.$item->id}}</td>
            <td>{{$item->user->mobile}}</td>
            <td>{{$item->amount}} ₹</td>
            <td>{{$item->order_state}}</td>
            <td>{{$item->type == 0 ? 'COD' : "Online"}}</td>
            <td>
                <a href="{{route('orders.show', $item->id)}}" class="mr-1">
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
</div>
