@can('edit_users')


<div class="col-12">
<div class="accordion" id="accordionExample">
    <div class="card">
      <div class="card-header" id="headingOne">
        <h2 class="mb-0">
          <button class="btn btn-link btn-block text-center" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            Override Permissions &nbsp;&nbsp;<span class="text-warning">{{isset($user) ?  $role->getDirectPermissions()->count(): null}}</span>
          </button>
        </h2>
      </div>

      <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mt-1">
                    <thead>
                        <tr>
                            <th>Module Permission</th>
                            <th class="text-center">Create</th>
                            <th class="text-center">View</th>
                            <th class="text-center">Update</th>
                            <th class="text-danger text-center">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  $permissions = Config('constants.permissions');?>
                        @if (isset($permissions) && !empty($permissions))
                            @foreach ($permissions as $key => $item)

                        <tr>
                        <td>{{ucfirst($item)}}</td>
                            <td class="text-center">
                                <?php $create = NULL; if(isset($role)){ $create = $role->hasPermissionTo('create_'.$item) ? 'checked' : '';} ?>
                                <div class="checkbox"><input type="checkbox" name="permissions[]"
                                id="create_{{$item}}" value="create_{{$item}}" class="checkbox-input" {{$create}}>
                                    <label for="create_{{$item}}"></label>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php $view = null; if(isset($role)){ $view = $role->hasPermissionTo('view_'.$item) ? 'checked' : '';} ?>
                                <div class="checkbox"><input type="checkbox" name="permissions[]"
                                id="view_{{$item}}" value="view_{{$item}}" class="checkbox-input" {{$view}}><label
                                        for="view_{{$item}}"></label>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php $edit = null; if(isset($role)){ $edit = $role->hasPermissionTo('edit_'.$item) ? 'checked' : '';} ?>
                                <div class="checkbox"><input type="checkbox" name="permissions[]"
                                id="edit_{{$item}}" value="edit_{{$item}}" class="checkbox-input" {{$edit}}><label
                                        for="edit_{{$item}}"></label>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php $delete = null; if(isset($role)){ $delete = $role->hasPermissionTo('delete_'.$item) ? 'checked' : '';} ?>
                                <div class="checkbox"><input type="checkbox" name="permissions[]"
                                id="delete_{{$item}}" value="delete_{{$item}}" class="checkbox-input text-danger" {{$delete}}>
                                    <label for="delete_{{$item}}"></label>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>

</div>



</div>
@endcan
