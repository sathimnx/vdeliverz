@forelse ($orders as $item)
    <tr>
        <td><a href="{{ route('orders.show', $item->id) }}"
                class="mr-1">{{ $item->prefix . $item->id }}</a></td>
        <td>{{ isset($item->user->mobile) ? $item->user->mobile : $item->user->email }}
        </td>
        <td class="text-center">{{ $item->cart->products_count }}
        </td>
        <td class="text-center">{{ $item->cart->total_amount }} ₹</td>
        <td class="text-center">{{ $item->comission }} ₹</td>
        <td class="text-center">{{ $item->cart->delivery_charge }} ₹</td>
        <td class="text-center">{{ $item->cart->coupon_amount }} ₹</td>
        <td class="text-center">{{ $item->shop_earned }} ₹</td>
    </tr>
@empty

@endforelse
