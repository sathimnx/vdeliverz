@forelse ($deliveries as $item)
    <tr>
        <td><a href="{{ route('orders.show', $item->order_id) }}">{{ $item->order->search }}</a></td>
        <td>{{ $item->accepted_at }}</td>
        <td class="{{ $item->status == 3 ? 'text-success' : 'text-danger' }}">
            {{ $item->delivery_status }}</td>
        <td>{{ $item->order->kms }} Kms</td>
        <td>{{ $item->order->customer_rating }}</td>
        <td>{{ $item->order->customer_comment }}</td>
        {{-- <td class="text-primary text-right font-weight-bold">{{$item->amount}}</td> --}}
    </tr>
@empty

@endforelse
