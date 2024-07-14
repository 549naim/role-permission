<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use DataTables;
use DB;

class AdminController extends Controller
{

     function __construct()
    {
         $this->middleware('permission:user_list|user_create|user_edit', ['only' => ['user_list']]);
         $this->middleware('permission:user_create', ['only' => ['user_create','user_store']]);
         $this->middleware('permission:user_edit', ['only' => ['user_edit,user_update']]);
         $this->middleware('permission:user_delete', ['only' => ['deleteUser']]);
        
    }

    public function admin_login()
    {
        return view('admin.login');
    }

    public function post_admin_login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            if(Auth::user()->hasRole('SuperAdmin')){
                return redirect('/index');
            }else{
                return redirect('/user-list');
            }
        }else{
            return back()->with('error', 'Invalid login details');
        }
    }

    //  public function index(){
    //     return view ('admin.index');
    //  }

    public function index(Request $request)
    {
        // dd(Auth::user());
        $permission = Permission::get();
        $rolePermissions = [];
        $roles = Role::orderBy('id', 'DESC')->get();

        $permissions = Permission::orderBy('id', 'DESC')->get();
        return view('admin.index', compact('roles', 'permissions', 'rolePermissions', 'permission'));
    }


    public function logout(Request $request)
    {

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin-login');
    }

    public function user_create()
    {
        $roles = Role::orderBy('id', 'DESC')->get();
        return view('admin.usercreate', compact('roles'));
    }

    public function user_store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'email|required|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'roles' => 'required'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->email_verified_at = now();
        $user->save();
        $user->assignRole($request->input('roles'));
        return response()->json(["message" => "User Created Successfuly !"]);
    }

    public function user_list(Request $request)
    {

        $user = User::orderBy('id', 'DESC')->get();

        if ($request->ajax()) {
            return DataTables::of($user)
                ->addIndexColumn()
                ->addColumn('role', function ($row) {
                    $role = '';
                    foreach ($row->getRoleNames() as $roleName) {
                        $role = '<span class="badge bg-success">' . $roleName . '</span>';
                    }
                    return $role;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" title="Edit Content" data-toggle="tooltip" data-id="' . $row->id . '" class="edit" > <button class="btn btn-outline-primary btn-sm">
                    <i class="fa-regular fa-pen-to-square"></i>
                    </button> </a>';
                    $btn = $btn . '<a href="javascript:void(0)" title="Delete Content" data-toggle="tooltip" data-id="' . $row->id . '" class="delete" > <button class="btn btn-outline-danger btn-sm">
                    <i class="fa-solid fa-trash"></i>
                    </button> </a>';
                    return $btn;
                })->rawColumns(['action', 'role'])
                ->make(true);
        }
        return view('admin.userlist');
    }


    public function user_edit(string $id)
    {
        $user = User::find($id);
        $roles = Role::where('name', '!=', 'SuperAdmin')->orderBy('id', 'DESC')->get();
        return view('admin.useredit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function user_update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'roles' => 'required'
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));
        return response()->json(["message" => "User Updated Successfuly !"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteUser(string $id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->back();
    }





}