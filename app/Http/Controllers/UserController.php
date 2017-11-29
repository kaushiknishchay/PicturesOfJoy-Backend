<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        //
    }

    public function setupNeelForm()
    {
        $allUser = User::all();
        if ($allUser != null && $allUser->count() === 0) {
            return ("<form action='' method='post'>
                         <input type='text' name='username' placeholder='Username'/>
                         <input type='password' name='password' placeholder='Password'/>
                         <input type='submit' value='Signup'/>
                </form>");
        } else {
            return abort(404);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function setupNeel(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);
        $userName = $request->input('username');
        $passWord = $request->input('password');

        $allUser = User::all();
        if ($allUser->count() === 0 && !empty($userName) && !empty($passWord)) {

            $user = new User;
            $user->username = $userName;
            $user->password = Hash::make($passWord);

            if ($user != null) {
                $user->save();
                return response()->json([], 200);
            } else {
                return response()->json([], 401);
            }
        } else {
            return abort(404);
        }
    }

    /**
     * @param $request
     * @return string
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        $userName = $request->json()->get('username');
        $passWord = $request->input('password');

        $user = User::where('username', $userName)->first();

        if ($user != null && Hash::check($passWord, Hash::make($user->password))) {
            $apikey = base64_encode(str_random(40));
            User::where('username', $userName)->update(['api_key' => "$apikey"]);;
            return response()->json(['code' => 200, 'status' => 'success', 'api_key' => $apikey]);
        } else {
            return response()->json(['code' => 401, 'status' => 'fail'], 401);
        }
    }

    public function signout(Request $request)
    {
        User::where('api_key', $request->header("Api-Key"))->update(['api_key' => null]);

        return response()->json(['code' => 200, 'status' => 'success']);
    }
}
