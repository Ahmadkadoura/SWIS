<?php

namespace App\Http\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class AuthRepository
{
    public function register($request):array
    {
        $User=User::create([
            'name'=>$request['name'],
            'email'=>$request['email'],
            'password'=>Hash::make($request['password']),
        ]);
        $roles=Role::where('name','clientDDD');
        $User->assignRole($roles);

        $permissions=$roles->permissions()->pluck()->toArray();
        $roles->givePermissionTo($permissions);


        $User->load('roles','permissions');

        $User=User::find($User['id']);
        $User= $this->RolesAndPermissions($User);
        $User['token']=$User->createToken("token")->plainTextToken;

        $message='User created successfully.';
        return['User'=>$User,'message'=>$message];

    }
//
    public function login($request): array
    {
        $user = User::where('email', $request['email'])->first();

        if (!is_null($user)) {
            if (!Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {
                $message = 'User email & password do not match with our records.';
                $code = 401;
                $user = null;
            } else {
                if ($user->first_login) {
                    $message = 'First time login. Please complete your profile.';
                    $code = 200; // Or another code indicating the need for profile completion
                    $user['redirect_to'] = route('api.complete-profile'); // Define this route accordingly
                    $user['access_token'] = $user->createToken("token")->plainTextToken;
                } else {
                    $user = $this->RolesAndPermissions($user);
                    $user['access_token'] = $user->createToken("token")->plainTextToken;
                    $message = 'User logged in successfully.';
                    $code = 200;
                }
            }
        } else {
            $message = 'User not found.';
            $code = 404;
        }

        return ['User' => $user, 'message' => $message, 'code' => $code];
    }
    public function logout():array
    {
        $user=Auth::user();
        if(!is_null($user))
        {
            $user->currentAccessToken()->delete();
            $message='User logged out successfully.';
            $code=200;
        }else{
            $message='invalid token.';
            $code=404;
        }
        return['User'=>$user,'message'=>$message,'code'=>$code];
    }

    public function RolesAndPermissions($user)
    {
        $roles=[];
        foreach ($user->roles as $role)
        {
            $roles[]=$role['name'];
        }
        unset($user['roles']);
        $user['roles']=$roles;

        $permissions=[];
        foreach ($user->permissions as $permission)
        {
            $permissions[]=$permission['name'];
        }
        unset($user['permissions']);
        $user['permissions']=$permissions;

        return $user;
    }
    public function completeProfile($request): array
    {
        $user = Auth::user();

        if ($user->first_login) {
            $user->update([
                'name' => $request['name'],
                'password' => Hash::make($request['password']),
                'first_login' => false,
            ]);

            $user = $this->RolesAndPermissions($user);
            $user['access_token'] = $user->createToken("token")->plainTextToken;
            $message = 'Profile completed successfully.';
            $code = 200;
        } else {
            $message = 'Profile already completed.';
            $code = 400;
            $user = null;
        }

        return ['User' => $user, 'message' => $message, 'code' => $code];
    }
}
