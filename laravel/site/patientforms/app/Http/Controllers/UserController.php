<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Role;
use App\Program;
use Exception;
use Validator;

class UserController extends Controller
{
    
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $currentUser = Auth::user();
        if ($currentUser->hasRoles('Super-Admin', 'Master-Admin')) {
            $users = User::all()->sortBy('email');
        } else {
            $users = User::active()->get()->sortBy('email');
        }
        return view('admin.users', ['users' => $users]);

    }

    public function create() {
        $programs = Program::all();
        $roles = Role::where('role', '!=', 'Master-Admin')->get();
        return view('admin.create_new_user', ['programs' => $programs, 'roles' => $roles]);
    }

    public function store(Request $request) {

        $program_ids = Program::all()->pluck('id')->toArray();
        $role_ids = Role::all()->pluck('id')->toArray();

        $this->validate($request, [
            'email' => 'required|unique:users,email,NULL,id,deleted_at,NULL|max:255',
            'program_id' => 'required|in:' . implode(',', $program_ids),
            'active' => 'boolean',
            'roles_id.*' => 'in:' . implode(',', $role_ids),
            'password' => 'required|min:6|same:password_confirm|max:255'
        ]);

        $user = new User;

        $user->id = \Webpatser\Uuid\Uuid::generate(4);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->program_id = $request->program_id;

        $user->save();
        if ($request->role !== null) {
            foreach ($request->role as $role) {
                $user->roles()->attach($role);
            }
        }

        $users = User::all();

        return redirect()->route('users.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $currentUser = Auth::user();
        if ($user != $currentUser && ! $currentUser->hasRoles('Admin', 'Super-Admin', 'Master-Admin')) {
            return redirect()->route('access_denied');
        }
        $programs = Program::all();
        $roles = Role::where('role', '!=', 'Master-Admin')->get();
        return view('user.account', ['user' => $user, 'programs' => $programs, 'roles' => $roles]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $currentUser = Auth::user();

        if (! $user->isSameAs($currentUser)) {
            if (! $currentUser->hasRoles('Super-Admin', 'Master-Admin')) {
                return redirect()->route('access_denied');
            }
        }

        $program_ids = Program::all()->pluck('id')->toArray();
        $role_ids = Role::all()->pluck('id')->toArray();

        $this->validate($request, [
            'email' => 'required|max:255',
            'program_id' => 'required|in:' . implode(',', $program_ids),
            'active' => 'boolean',
            'roles_id.*' => 'in:' . implode(',', $role_ids)
        ]);

        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        if ($user->isSameAs($currentUser) || $currentUser->hasRoles('Super-Admin', 'Master-Admin')) {
            if ($request->email !== $user->email) {
                $this->validate($request, [
                    'email' => 'unique:users,email,NULL,id,deleted_at,NULL'
                ]);
            }
            $user->email  = $request->email;
        }
        $user->program_id = $request->program_id;
        $user->active     = !is_null($request->active);

        if ($user->isSameAs($currentUser)) {
            if ($request->password != '') {
                $validator = Validator::make($request->all(), [
                    'password' => 'required|min:6|same:password_confirm|max:255'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator);
                }
                $user->password = bcrypt($request->password);
            }
        }

        $user->save();
        if ($currentUser->hasRoles('Super-Admin', 'Master-Admin')) {
            $user->roles()->detach();
            foreach ($request->roles_id as $id) {
                $user->roles()->attach($id);
            }
        }

        $programs = Program::all();
        $roles = Role::where('role', '!=', 'Master-Admin')->get();

        return view('user.account', ['user' => $user, 'programs'=> $programs, 'message' => 'User successfully updated', 'roles' => $roles]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $currentUser = Auth::user();
        if ($user->isSameAs($currentUser)) {
            throw new Exception("Cannot delete; user is same as current user");
        }

        $user->delete();
        return "user deleted";
    }

    public function deleteModal(User $user)
    {
        return view('user.delete-modal',
            [
                'user' => $user
            ]
        );

    }

    public function activate(Request $request, User $user) {
        $user->active = true;
        $user->save();
    }

    public function deactivate(Request $request, User $user) {
        $user->active = false;
        $user->save();
    }

}
