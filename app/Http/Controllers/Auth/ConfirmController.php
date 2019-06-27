<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ConfirmController extends Controller
{
    public function confirm($token, Request $request)
    {
        $user = User::where('confirm_token', $token)->firstOrFail();

        $user->activated_at = date('Y-m-d H:i:s');
        $user->confirm_token = '';
        $user->save();

        flash('Account has activated!');
        
        return redirect('/app?activated');
    }
}
