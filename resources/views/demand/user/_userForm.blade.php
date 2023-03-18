@csrf
<?php $route = explode('.', Route::currentRouteName());     ?>
@if ($route[1] == 'show')
    @push('scripts')
        <script>
            $('input').attr('disabled', true);
            $('select').attr('disabled', true);
            $('textarea').attr('disabled', true);
            $('button').addClass('d-none');
        </script>
    @endpush
@endif
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <div class="controls">
                    <label>Username</label>
                    <input type="text" class="form-control" placeholder="Username"
                value="{{isset($user) ? $user->name : old('name')}}" name="name" required
                        data-validation-required-message="This username field is required">
                </div>
            </div>

            <div class="form-group" id="userEmail">
                <div class="controls">
                    <label>E-mail</label>
                    <input type="email" class="form-control" placeholder="Email"  onkeyup="checkUniqueName('users', 'email', '#userEmail', this.value)"
                        value="{{isset($user) ? $user->email : ''}}" name="email" required
                        data-validation-required-message="This email field is required">
                </div>
            </div>
            @if (isset($user))
            <div class="form-group">
                <label for="storePassword">Password</label>
                <div class="controls">
                  <input type="password" name="password" id="storePassword" class="form-control"
                    placeholder="Password">
                </div>
              </div>
            @else
            <div class="form-group">
                <label for="storePassword">Password<span class="text-danger"> *</span></label>
                <div class="controls">
                  <input type="password" name="password" id="storePassword" class="form-control"
                    data-validation-required-message="This field is required" placeholder="Password">
                </div>
              </div>
            @endif


        </div>
        <div class="col-12 col-sm-6">

            <div class="form-group @role('admin') d-block @else d-none @endrole">
                <div class="controls">
                    <label>Role</label>
                    <select class="select2 form-control" id="roles" name="roles[]" autocomplete="new-password" data-placeholder="Select Role..." data-validation-required-message="Select user role" required>
                        <option value="">Select Role</option>
                        @if (isset($roles) && !empty($roles))
                            @foreach ($roles as $key => $item)
                                <?php $val = NULL; if(isset($user)){$val = $user->roles->pluck('id')->first();}  ?>
                                <option value="{{$item->id}}" {{$val == $item->id ? 'selected' : ''}}>{{$item->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            @push('scripts')
                <script>
                    $('#roles').on('change', function(){
                        if($(this).val() == 4){
                            $('#deliveryBoyCentre').removeClass('d-none');
                        }else{
                            $('#deliveryBoyCentre').addClass('d-none');
                        }
                    })
                </script>
            @endpush
            <div class="form-group @if( isset($user) && $user->hasAnyRole('delivery-boy')) d-block @else d-none @endif" id="deliveryBoyCentre">
                <label>Delivery boy Type</label>
                <?php $val = null; if(isset($user->delivery_type) && $user->delivery_type != null){$val = $user->delivery_type;} ?>
                <select class="select2 form-control" id="centre" name="delivery_type" autocomplete="new-password" data-placeholder="Select Type...">
                            <option value="1" {{$val == 1 ? 'selected' : ''}}>Monthly</option>
                            <option value="0" {{$val == 0 ? 'selected' : ''}}>Part time</option>

                </select>
            </div>
            <div style="display: flex">
                <div class="form-group" style="width: 60%" id="userMobile">
                    <label for="MobileNumber">Mobile Number with Country code</label>
                    <div class="controls">
                      <input type="text" value="{{isset($user) ? $user->mobile : ''}}"  onkeyup="checkUniqueName('users', 'mobile', '#userMobile', this.value)" name="mobile" class="form-control"
                        data-validation-required-message="Enter Mobile number"
                        placeholder="Enter Your Mobile Number" required>
                    </div>
                  </div>
                  @role('admin')
                <div class="form-group text-center m-auto" style="width: 40%">
                    <label for="storeActive">Status</label>
                <div class="custom-control custom-switch custom-switch-glow custom-control-inline float-right">
                    <input type="checkbox" class="custom-control-input" name="status" <?php if(isset($user->status) && $user->status == 0) { echo '';}else{  echo 'checked';} ?> id="storeActive">
                    <label class="custom-control-label" for="storeActive">
                    </label>
                  </div>
                </div>
                @endrole
            </div>
            @if (isset($user))
            <div class="form-group">
                <label for="ConfirmPassword">Repeat password must match</label>
                <div class="controls">
                  <input type="password" autocomplete="new-password" name="confirm_password" id="ConfirmPassword" data-validation-match-match="password"
                    class="form-control"
                    placeholder="Repeat Password">
                </div>
              </div>
            @else
            <div class="form-group">
                <label for="ConfirmPassword">Repeat password must match</label>
                <div class="controls">
                  <input type="password" autocomplete="new-password" name="confirm_password" id="ConfirmPassword" data-validation-match-match="password"
                    class="form-control" data-validation-required-message="Repeat password must match"
                    placeholder="Repeat Password">
                </div>
              </div>
            @endif

            @if(isset($user->image) && $user->image != null)
                <div class="my-1">
                <img src="{{ asset($user->image) }}" width="30%" alt="" srcset="">
                </div>
                @endif

                <fieldset class="form-group" id="profile_image">
                <label for="storePANImage">Upload Profile Image </label>
                <div class="input-group" >
                <div class="input-group-prepend">
                    <span class="input-group-text" id="storePANImage">Profile Image</span>
                </div>
                <div class="custom-file">
                <input type="file"  class="custom-file-input"  name="profile_image" id="storePANImageUpload" aria-describedby="storePANImage">
                <label class="custom-file-label" for="storePANImage">Choose file</label>
                </div>
                </div>
                <div class="invalid-feedback">
                <i class="bx bx-radio-circle"></i>
                Image should be jpg, jpeg Format
                </div>
            </fieldset>

        </div>
        {{-- @role('admin')
        @include('shared._permissions', [
            'role' => isset($user) ? $user : null,
            'user' => isset($user) ? 1 : null,
        ])
        @endrole --}}
        {{-- <div class="col-12">
            <div class="table-responsive">
                <table class="table mt-1">
                    <thead>
                        <tr>
                            <th>Module Permission</th>
                            <th>Read</th>
                            <th>Write</th>
                            <th>Create</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Users</td>
                            <td>
                                <div class="checkbox"><input type="checkbox"
                                        id="users-checkbox1" class="checkbox-input" checked>
                                    <label for="users-checkbox1"></label>
                                </div>
                            </td>
                            <td>
                                <div class="checkbox"><input type="checkbox"
                                        id="users-checkbox2" class="checkbox-input"><label
                                        for="users-checkbox2"></label>
                                </div>
                            </td>
                            <td>
                                <div class="checkbox"><input type="checkbox"
                                        id="users-checkbox3" class="checkbox-input"><label
                                        for="users-checkbox3"></label>
                                </div>
                            </td>
                            <td>
                                <div class="checkbox"><input type="checkbox"
                                        id="users-checkbox4" class="checkbox-input" checked>
                                    <label for="users-checkbox4"></label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Articles</td>
                            <td>
                                <div class="checkbox"><input type="checkbox"
                                        id="users-checkbox5" class="checkbox-input"><label
                                        for="users-checkbox5"></label>
                                </div>
                            </td>
                            <td>
                                <div class="checkbox"><input type="checkbox"
                                        id="users-checkbox6" class="checkbox-input" checked>
                                    <label for="users-checkbox6"></label>
                                </div>
                            </td>
                            <td>
                                <div class="checkbox"><input type="checkbox"
                                        id="users-checkbox7" class="checkbox-input"><label
                                        for="users-checkbox7"></label>
                                </div>
                            </td>
                            <td>
                                <div class="checkbox"><input type="checkbox"
                                        id="users-checkbox8" class="checkbox-input" checked>
                                    <label for="users-checkbox8"></label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Staff</td>
                            <td>
                                <div class="checkbox"><input type="checkbox"
                                        id="users-checkbox9" class="checkbox-input" checked>
                                    <label for="users-checkbox9"></label>
                                </div>
                            </td>
                            <td>
                                <div class="checkbox"><input type="checkbox"
                                        id="users-checkbox10" class="checkbox-input" checked>
                                    <label for="users-checkbox10"></label>
                                </div>
                            </td>
                            <td>
                                <div class="checkbox"><input type="checkbox"
                                        id="users-checkbox11" class="checkbox-input"><label
                                        for="users-checkbox11"></label>
                                </div>
                            </td>
                            <td>
                                <div class="checkbox"><input type="checkbox"
                                        id="users-checkbox12" class="checkbox-input"><label
                                        for="users-checkbox12"></label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div> --}}

    </div>
