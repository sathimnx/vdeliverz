<table id="users-list-datatable" class="table zero-configuration">
    <thead>
        <tr>
            <th>S.no</th>
            @role('admin')
            <th>Provider Name</th>
            @endrole
            <th>Booking Referel</th>
            <th>Customer mobile</th>
            <th>Booked Amount</th>
            {{-- <th style="white-space: nowrap">Expect Delivery</th> --}}
            <th>Status</th>
            <th>Payment Type</th>
            <th>Actions</th>
            @can('view_orders')
            <th>Detail</th>
            @endcan

        </tr>
    </thead>
    <tbody>

        @if (isset($bookings) && !empty($bookings))
            @foreach ($bookings as $k => $item)
            <tr>
                <td>{{($bookings->perPage() * ($bookings->currentPage() - 1)) + $k + 1}}</td>
            @role('admin')
            <td><a href="{{route('demand.providers.show', $item->provider->id)}}" class="mr-1">{{$item->provider->name}}</a></td>
            @endrole
            <td>{{$item->referral}}</td>
            <td>{{$item->user->mobile ?? NULL}}</td>
            <td>{{$item->payable_amount}} ₹</td>
            {{-- <td>{{$item->expected_time}}</td> --}}
            <td>{{$item->book_state}}</td>
            <td>{{$item->payment == 0 ? 'COD' : "Online"}}</td>
            <td>
                @if ($item->status == 1)
                <a href="#" onclick="showReviewBook('{{$item->prefix}}', '{{$item->id}}')">
                    <button type="submit" class="btn-outline-info" data-icon="warning-alt">
                        Review
                    </button>
                </a>
                @endif
            </td>
            <td>
                <a href="{{route('demand.bookings.show', $item->id)}}" class="mr-1">
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
<div class="mx-auto" style="width: fit-content">{{ $bookings->links() }}</div>
