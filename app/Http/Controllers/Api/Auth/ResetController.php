<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class ResetController extends Controller
{
    public function reset(Request $req) {
        $rules = [
            'old_password' => 'required|min:6',
            'new_password' => 'required|confirmed|min:6',
        ];
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if(!Hash::check($req->old_password, auth()->user()->password)){
            return response()->json(['error' => true, 'message' => 'Password doesn`t match'], 404);
        }

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($req->new_password)
        ]);

        return response()->json(['error' => false, 'message' => 'Password changed successfully'], 200);
    }
}
