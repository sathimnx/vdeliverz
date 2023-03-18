<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Spatie\Permission\Models\Role;
use \Spatie\Permission\Models\Permission;
use Validator;
use App\User;
use App\Authorizable;

class RoleController extends Controller
{
    use Authorizable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data["roles"] = Role::all();
            $data["permission"] = Permission::all();
            return view('role.role', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            //throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            return view('role.role_create');
        } catch (\Throwable $th) {
            return catchResponse();
            //throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles'
        ]);
        if($validator->fails()){
            flash()->error($validator->messages()->first());
            return redirect()->back();
        }
        $role = Role::create($request->only('name'));
        if($role){
            $permissions = $request->get('permissions', []);
            $role->syncPermissions($permissions);
            flash()->success('Role Created !');
            return redirect()->route('roles.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data["role"] = Role::findOrFail($id);
        return view('role.role_edit', $data ?? NULL);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' .$id
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return redirect()->back();
            }
            if($role = Role::findOrFail($id)) {
            // admin role has everything
            if(strtolower($role->name) === 'admin') {
                $role->syncPermissions(Permission::all());
                flash()->error('Sorry! Cannot edit Super Admin.');
                return redirect()->route('roles.index');
            }

            $permissions = $request->get('permissions', []);
            $role->syncPermissions($permissions);
            $role->update(['name' => $request->name]);
            flash( $role->name . ' permissions has been updated.');
            } else {
                flash()->error( 'Role with id '. $id .' not found.');
            }

            return redirect()->route('roles.index');
        } catch (\Throwable $th) {
            // dd($th);
            return catchResponse();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if(Role::findOrFail($id)->delete()){
                flash("Role Deleted Successfully!");
                return redirect()->back();
            }
            flash()->error("Could Not Delete Role!")->important();
            return redirect()->back();
        } catch (\Throwable $th) {
            //throw $th;
            return catchResponse();
        }
    }
}
