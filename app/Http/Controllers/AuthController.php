<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    // User Registration API Function
    public function register(Request $request)
    {
        $request->validate([
            'name'=> 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        $userData = User::where('email',$request->input('email'))->first();
        if ($userData) {
            return response()->json(['message' => 'User Allready Exist'], 403);
        } else {

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $token = $user->createToken('agdumbagdum')->accessToken;
        $success['name'] = $user->name;
        event(new Registered($user));
        return response()->json(['token' => $token,'message' => 'Registration Successfull!'], 201);
        }
    }


        // User Login API Function
    public function login(Request $request)
    {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $userData = User::where('email',$request->input('email'))->first();
            if ($userData) {
                // if (!$userData->hasVerifiedEmail()) {
                //     return response()->json(['message'=> 'Email Not Verified, Please Verify your email!'], 403);
                // }
                if (Hash::check($request->password, $userData->password)) {
                        $token = $userData->createToken('agdumbagdum')->accessToken;
                        $message = "Login Successfully!";
                        return response()->json(['token'=> $token,'message'=>$message], 200)
                        ->header('Authorization', $token);
                }else{
                    return response()->json(['message'=>'email or password incorrect!'], 403);
                }
            }else{
                return response()->json(['message'=> 'User does not exist, please register'], 404);
            }
        }

    // Passport Logout API
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json(['message' => 'You have been successfully logged out!'], 200);
    }

      // User Data
      public function userData(Request $request)
      {
          $userData = $request->user();
          return response()->json($userData, 200);
      }

      // Update User

      public function UpdateUser(Request $request)
      {
          $validator = $request->validate([
              'id' => 'required',
              'name'=> 'required|string',
          ]);
          $userData = User::findOrFail($request->input('id'));
          if ($userData) {
              $userData->update([
              'name'=> $request->input('name'),
              ]);
              return response()->json(['message' => 'Profile Updated Successfully!'], 200);
          }
      }

      // Change Password
      public function changePassword(Request $request)
      {
          $validator = $request->validate([
              'email' => 'required|email',
              'old_Password' => 'required|min:6',
              'new_Password' => 'required|min:6',
              'confirm_password' => 'required|min:6'
          ]);

          $userData = User::where('email',$request->input('email'))->first();
          if ($userData) {
              if (Hash::check($request->old_Password, $userData->password)) {
                  if (Hash::check($request->new_Password, $userData->password)) {
                      return response()->json(['message' => 'New Password is Same as Old Password'], 409);
                  }else{
                      User::where('email',$request->input('email'))->update(['password' => Hash::make($request->new_Password)]);
                      return response()->json(['message' => 'Password change successfully!'], 200);
                  }
              }else{
                  return response()->json(['message' => 'Passeword not match!'], 404);
              }
          }else{
              return response()->json(['message' => 'User not exist!'], 404);
          }
      }
}
