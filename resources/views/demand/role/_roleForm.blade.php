@csrf


    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <div class="controls">
                    <label>Role Name</label>
                    <input type="text" class="form-control" placeholder="Role Name"
                value="{{isset($role) ? $role->name : ''}}" name="name" required
                        data-validation-required-message="This Role name field is required">
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">

        </div>


        @include('shared._permissions', [
            'role' => isset($role) ? $role : null
        ])

    </div>
