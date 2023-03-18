<table id="users-list-datatable" class="table zero-configuration">
    <thead>
        <tr class="text-center">
            <th>S.no</th>
            <th>Shop Name</th>
            <th>Pending Payments</th>
            <th>Requested Payments</th>
            <th>Accepted Payments</th>
            <th>Completed Payments</th>

        </tr>
    </thead>
    <tbody>
        @if (isset($shops) && !empty($shops))
            @foreach ($shops as $key => $item)
                <tr class="text-center">
                    <td>{{ $shops->perPage() * ($shops->currentPage() - 1) + $key + 1 }}</td>
                    <td>{{ ucfirst($item->name) }}</td>
                    <td>
                        <a href="{{ route('payments.show', [$item->id, 0]) }}" class="mr-1">
                            <button type="submit" class="btn-outline-danger" data-icon="warning-alt">
                                Pending
                            </button>
                        </a>
                    </td>

                    <td>
                        <a href="{{ route('payments.history', [$item->id, 1]) }}" class="mr-1">
                            <button type="submit" class="btn-outline-primary" data-icon="warning-alt">
                                Requested
                            </button>
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('payments.history', [$item->id, 2]) }}" class="mr-1">
                            <button type="submit" class="btn-outline-primary" data-icon="warning-alt">
                                Accepted
                            </button>
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('payments.history', [$item->id, 3]) }}" class="mr-1">
                            <button type="submit" class="btn-outline-success" data-icon="warning-alt">
                                Completed
                            </button>
                        </a>
                    </td>


                </tr>
            @endforeach
        @endif


    </tbody>
</table>
<div class="mx-auto" style="width: fit-content">{{ $shops->links() }}</div>
