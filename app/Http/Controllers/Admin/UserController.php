<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Authorizable;
use \Spatie\Permission\Models\Role;
use \Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Validator, Auth, DB, Session;
use Hash;

class UserController extends Controller
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
            $data["users"] = User::with('roles')->latest()->paginate(10);
            if(Auth::user()->hasAnyRole('vendor')){
                return redirect()->route('dashboard.index');
            }
            if (request()->ajax()) {
                if(isset(request()->search) && !empty(request()->search)){
                    $data['users'] = User::with('roles')->where('name', 'Like', '%'.request()->search.'%')->orWhere('email', 'Like','%'.request()->search.'%')->orWhere('mobile', 'Like','%'.request()->search.'%')
                    ->orWhereHas('roles', function($query){
                        $query->where('name', 'Like', '%'.request()->search.'%');
                    })->latest()->paginate(10);
                }
                Session::put(['prev_page_no' => $data['users']->currentPage()]);
                return view('user.user_table', array('users' => $data['users']))->render();
            }
            return view("user.users", $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    public function usersFilter($id){
        try {
            $data["users"] = User::role($id)->orderBy('id', 'desc')->get();
            $data["centres"] = \App\Centre::all();
            $data['roles'] = Role::all();
            return view("user.users", $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
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
            $data["roles"] = Role::all();
            return view("user.user_create", $data ?? NULL);
        } catch (\Throwable $th) {
            //throw $th;
            return catchResponse();
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
        try {
            $mobile = str_replace('+91', '', $request->mobile);
            $request->merge(['mobile' => '+91'.$mobile]);
            $validator = Validator::make($request->all(), [
                'name' => 'bail|required|min:2',
                'email' => 'bail|required|email|unique:users',
                'mobile' => 'bail|required|unique:users',
                'password' => 'bail|required|min:6'
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return redirect()->back()->withInput();
            }
            DB::beginTransaction();
            $user = new User;
            if($request->hasFile('profile_image')){
                if($request->file('profile_image')->isValid())
                {
                    $extension = $request->profile_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->email)).time()."user." .$extension;
                    $request->profile_image->move(config('constants.user_profile_img'), $file_path);
                    $user->image = $file_path;
                }
            }
            $user->password =  Hash::make($request->get('password'));
            $user->active = $request->status == null ? 0 : 1;
            $user->name = $request->name;
            $user->email = $request->email;
            if($request->roles[0] == 4){
                $user->delivery_type = $request->delivery_type;
                $user->driving_preference = implode(',', $request->input('driving_preference'));
            }
            $user->mobile = $request->mobile;
            $user->created_by = Auth::user()->id;
            if($user->save()){
                $this->syncPermissions($request, $user);
                // if($request->roles[0] == config('constants.delivery_boy_role_id')){
                //     flash('Delivery Boy has been Updated.');
                //     return redirect()->route('delivery-boys.index');
                // }
                DB::commit();
                flash()->success('User has been created Successfully!');
                return redirect()->route('users.index');
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return catchResponse();

            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        try {
            if(Auth::user()->hasAnyRole('vendor') && Auth::user()->id != $user->id){
                    abort(404);
                }
                $data['user'] = $user;
                $data["roles"] = Role::all();
            return view('user.user_edit', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        try {
            $data["user"] = User::findOrFail($user->id);
            if(auth()->user()->hasAnyRole('vendor')){
                if(Auth::user()->id != $user->id){
                    abort(404);
                }
                $data["user"] = User::findOrFail(auth()->user()->id);
            }
            $data["roles"] = Role::all();
            return view('user.user_edit', $data ?? NULL);
        } catch (\Throwable $th) {
            //throw $th;
            return catchResponse();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        try {
           //dd($request->driving_preference);
            $mobile = str_replace('+91', '', $request->mobile);
            $request->merge(['mobile' => '+91'.$mobile]);
            $validator = Validator::make($request->all(), [
                'name' => 'bail|required|min:2',
                'email' => 'required|email|unique:users,email,'.$user->id,
                'mobile' => 'required|unique:users,mobile,'.$user->id,
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return redirect()->back()->withInput();
            }
            // dd($request);
            DB::beginTransaction();
            if($request->hasFile('profile_image')){
                // dd(1);
                if($request->file('profile_image')->isValid())
                {

                    $extension = $request->profile_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($user->email)).time()."user." .$extension;
                    $request->profile_image->move(config('constants.user_profile_img'), $file_path);
                    $user->image = $file_path;
                }
            }

            if($user->hasAnyRole('admin') && !auth()->user()->hasAnyRole('admin')){
                flash()->error('Sorry! Cannot edit Super Admin.');
                return redirect()->route('users.index');
            }
            if(isset($request->password)){
            $user->password =  Hash::make($request->get('password'));
            }

            $user->active = $request->status == null ? 0 : 1;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->created_by = Auth::user()->id;
            if($request->roles[0] == 4){
                $user->delivery_type = $request->delivery_type;
                $user->driving_preference = implode(',', $request->input('driving_preference'));
            }
            if($user->save()){
                if(!$user->hasAnyRole('admin')){
                    $this->syncPermissions($request, $user);
                }
                DB::commit();
                // if($request->roles[0] == config('constants.delivery_boy_role_id')){
                //     flash('Delivery Boy has been Updated.');
                //     return redirect()->route('delivery-boys.index');
                // }
                flash('User has been Updated.');
                $url = route('users.index').'?page='.Session::get('prev_page_no');
                return redirect($url);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return catchResponse();

            dd($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ( Auth::user()->id == $user->id ) {
            flash()->warning('Deletion of currently logged in user is not allowed :(')->important();
            return redirect()->back();
        }
        if($user->hasAnyRole('admin')){
                flash()->error('Sorry! Cannot Delete Super Admin.');
                return redirect()->route('users.index');
        }

        if(User::findOrFail($user->id)->delete()) {
            flash()->info('User has been deleted');
        } else {
            flash()->info('User not deleted');
        }

        return redirect()->back();
    }

    public function deliveryBoys(){
        try {
            $data['delivery_boys'] = User::role('delivery-boy')->with('centre')->get();
            if (Auth::user()->hasAnyRole('hub-user')) {
            $centre_id = Auth::user()->centre->centre_id;
            $data['delivery_boys'] = User::role('delivery-boy')->where('centre_id', $centre_id)->with('centre')->get();
            }
            return view('delivery.delivery', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

        /**
     * Sync roles and permissions
     *
     * @param Request $request
     * @param $user
     * @return string
     */
     private function syncPermissions(Request $request, $user)
    {
        // dd($request);
        // Get the submitted roles
        $roles = $request->get('roles', []);
        $permissions = $request->get('permissions', []);

        // Get the roles
        $roles = Role::find($roles);

        // check for current role changes
        if( ! $user->hasAllRoles( $roles ) ) {
            // reset all direct permissions for user
            $user->permissions()->sync([]);
        } else {
            // handle permissions
            $user->syncPermissions($permissions);
        }

        $user->syncRoles($roles);

        return $user;
    }
}
