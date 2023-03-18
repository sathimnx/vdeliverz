<table id="users-list-datatable" class="table zero-configuration">
    <thead>
        <tr>
            <th>S.no</th>
            <th>Given To</th>
            <th>Given by</th>
            <th>Rating</th>
            <th>Comment</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($reviews) && !empty($reviews))
            @foreach ($reviews as $key => $item)
                <tr>
                    <td>{{ $key + $reviews->firstItem() }}</td>
                    @if ($item->shop_id)
                        <td>{{ $item->shop->name }} ( Shop )</td>
                    @else
                        <td>{{ $item->deliveryBoy->name }} ( Delivery Boy )</td>
                    @endif
                    @if ($item->user->hasAnyRole('vendor'))
                        <td><a href="{{ route('users.show', $item->user->id) }}">{{ $item->user->name }}
                                ( Vendor )</a></td>
                    @else
                        <td> <a href="{{ route('users.show', $item->user->id) }}">
                                {{ $item->user->name }} ( Customer )</a></td>
                    @endif
                    <td>{{ $item->rating ?? null }}</td>
                    <td>{{ $item->comment }}</td>

                </tr>
            @endforeach
        @endif


    </tbody>
</table>
<div class="mx-auto" style="width: fit-content">{{ $reviews->links() }}</div>
