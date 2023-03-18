<table id="users-list-datatable" class="table zero-configuration">
    <thead>
        <tr>
            <th>S.no</th>
            <th>Customer</th>
            <th>Mobile</th>
            <th>Address</th>
            <th>Landmark</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($addresses) && !empty($addresses))
            @foreach ($addresses as $key => $item)
            <tr>
            <td>{{$key + 1}}</td>
            <td>{{ucfirst($item->user->name)}}</td>
            <td>{{$item->user->mobile}}</td>
            <td>{{$item->address}}</td>
            <td>{{$item->landmark}}</td>
            <td class="text-center">
                <form action="{{route('addresses.destroy', $item->id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this Address ?')" method="post">
                    {{method_field('DELETE')}}
                    @csrf
                    <button type="submit" class="btn-outline-danger">
                        <i class="bx bx-trash-alt"></i>
                    </button>

                </form>
            </td>



            </tr>
            @endforeach
        @endif


    </tbody>
</table>
<div class="mx-auto" style="width: fit-content">{{ $addresses->links() }}</div>
</div>

