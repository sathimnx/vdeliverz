<table id="users-list-datatable" class="table zero-configuration">
    <thead>
        <tr>
            <th>S.no</th>
            <th>Shop Name</th>
            <th>Payment Status</th>
            <th>Orders Count</th>
            <th>Earnings</th>
            <th>Actions</th>

        </tr>
    </thead>
    <tbody>
        @if (isset($payments) && !empty($payments))
            @foreach ($payments as $key => $item)
                <tr>
                    <td>{{ $payments->perPage() * ($payments->currentPage() - 1) + $key + 1 }}
                    </td>
                    <td>{{ $item->shop->name }}</td>
                    <td>{{ $item->pay_state }}</td>
                    <td>
                        {{ $item->count }}
                    </td>
                    <td>
                        {{ $item->total }}
                    </td>

                    <td>
                        <a href="{{ route('payments.view', $item->id) }}" class="mr-1">
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
<div class="mx-auto" style="width: fit-content">{{ $payments->links() }}</div>
