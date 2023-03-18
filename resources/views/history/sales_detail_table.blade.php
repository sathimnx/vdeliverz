@forelse ($orders as $item)
<tr>
    <td>{{$item->prefix.$item->id}}</td>
    <td class="@if ($item->order_state == 'Canceled') text-danger @endif">{{$item->order_state}}</td>
    <td>{{isset($item->user->mobile) ? $item->user->mobile : $item->user->email}}</td>
    <td class="text-center">{{$item->cart->products_count}}</td>
    <td class="text-center">{{$item->amount}} ₹</td>
    <td class="text-center">
        <a href="{{route('orders.show', $item->id)}}" class="mr-1">
            <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                <i class="bx bx-show"></i>
            </button>
        </a>
    </td>
  </tr>
@empty

@endforelse
