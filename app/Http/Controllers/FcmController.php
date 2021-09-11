<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FcmController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();

        $user_id  = $input['user_id'];
        $fcm_token  = $input['fcm_token'];

        $user = User::findOrFail($user_id);

        $user->fcm_token =  $fcm_token;
        $user->save();

        return response()->json(['success' => true, 'message' => 'FCM token saved successfully']);
    }
}
