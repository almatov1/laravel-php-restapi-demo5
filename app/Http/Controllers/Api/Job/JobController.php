<?php

namespace App\Http\Controllers\Api\Job;
use App\Models\Job;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Validator;

class JobController extends Controller
{
    public function upload(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|min:0|unique:job',
            'info' => 'required|string|between:5,255',
        ]);

        if($validator->fails()){
            return response()->json(['error' => true, $validator->errors()], 400);
        }

        if(!$this->checkUserId($request->user_id)) {
            return response()->json(['error' => true, 'message' => 'User dont found'], 400);
        }

        $job = Job::create(array_merge(
            $validator->validated(),
        ));

        $this->sendMail($request->user_id);

        return response()->json([
            'error' => false,
            'message' => 'Job successfully uploaded and mail sended to user',
            'job' => $job
        ], 201);
    }

    public function checkUserId($userId) {
        $result = User::Where('id', $userId)->first();

        if (!empty($result)) {
            return true;
        }

        return false;
    }

    public function sendMail($userId) {
        $email = User::where('id', $userId)->first()->email;
        
        Mail::raw('Then you are author', function ($message) use ($email) {
            $message->to($email)->subject('Job test');
        });
    }
}
