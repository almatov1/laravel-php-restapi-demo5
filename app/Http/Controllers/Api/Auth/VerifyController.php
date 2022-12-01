<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Models\Models\VerifyModel;
use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class VerifyController extends Controller
{
    public function verify(Request $req) {
        $rules = [
            'code' => 'required|min:6',
        ];
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $userId = auth('api')->user()->id;

        $result = User::where('email_code', $req['code'])->where('id', $userId)->get();

        if($result->isEmpty())
            return response()->json(['error' => true, 'message' => 'Not found'], 400);
        
        User::where('id', $userId)->update(array(
            'email_verified_at'=>now(),
        ));
        return response()->json(['error' => false, 'message' => 'Successfully activated'], 200);
    }

    public function sendMail() {
        $code = random_int(100000, 999999);

        User::where('id', auth('api')->user()->id)->update(array(
            'email_code'=>$code,
        ));

        Mail::raw($code, function ($message) {
            $message->to(auth('api')->user()->email)->subject('Email activation code');
        });
          

        return response()->json(['error' => false, 'message' => 'Activation code sended'], 201);
    }
    
}
