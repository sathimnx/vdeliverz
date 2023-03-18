<table id="users-list-datatable" class="table zero-configuration">
    <thead>
        <tr>
            <th>S.no</th>
            <th>Customer Name</th>
            <th>Customer Address</th>
            <th>Shop Address</th>
            <th>KMS</th>
            <th>Show in Map</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($addresses) && !empty($addresses))
            @foreach ($addresses as $key => $item)
                <tr>
                    <td>{{ $key + $addresses->firstItem() }}</td>
                    <td><a
                            href="{{ route('users.show', $item->address->user->id) }}">{{ $item->address->user->name }}</a>
                    </td>
                    <td>{{ $item->address->address ?? null }}</td>
                    <td><strong>{{ $item->shop->name }}</strong> -
                        {{ $item->shop->address ?? null }}
                    </td>
                    <td>{{ $item->kms }}</td>
                    <td>
                        <a href="https://www.google.com/maps/dir/?api=1&origin={{ $item->address->latitude }},{{ $item->address->longitude }}&destination={{ $item->shop->latitude }},{{ $item->shop->longitude }}&travelmode=driving"
                            target="_blank">
                            <button class="btn-outline-info"> <i class="bx bx-compass"></i></button>
                        </a>
                    </td>

                </tr>
            @endforeach
        @endif


    </tbody>
</table>
<div class="mx-auto" style="width: fit-content">{{ $addresses->links() }}</div>
