
<div style="display: inline-flex">
    <a href="{{route($entity.'.show', $id)}}" class="mr-1">
        <button type="submit" class="btn-outline-info" data-icon="warning-alt">
            <i class="bx bx-show"></i>
        </button>
    </a>
@can('edit_'.$entity)
<a href="{{route($entity.'.edit', $id)}}">
    <button type="submit" class="btn-outline-info" data-icon="warning-alt">
        <i class="bx bx-edit-alt"></i>
    </button>
    </a>
@endcan

@can('delete_'.$entity)
    <form action="{{route($entity.'.destroy', $id)}}" class="ml-1"  onsubmit="return confirm('Are you sure wanted to delete this {{str_singular($entity)}}?')" method="post">
        {{method_field('DELETE')}}
        @csrf
        <button type="submit" class="btn-outline-danger">
            <i class="bx bx-trash-alt"></i>
        </button>

    </form>
@endcan
</div>
