<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\UserRequest;
use Auth;
use Hash;

class ProfileController extends Controller
{
    /**
     * Folder to be save the images
     *
     * @var string
     */
    private $folder = 'app/public/profile';

    /**
     * Show the user profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $google2fa_url = "";
        if ($user->passwordSecurity()->exists()) {
            $google2fa = app('pragmarx.google2fa');
            $google2fa->setAllowInsecureCallToGoogleApis(true);
            $google2fa_url = $google2fa->getQRCodeGoogleUrl(
                'HydraDEX',
                $user->email,
                $user->passwordSecurity->google2fa_secret
            );
        }

        $data = [
            'user' => $user,
            'google2fa_url' => $google2fa_url
        ];

        return view('app.profile')->with('data', $data);
    }

    /**
     * update the user profile
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request)
    {
        try {
            if ($request->has('name')) {
                auth()->user()->name = $request->get('name');
            }

            if ($request->has('email')) {
                auth()->user()->email = $request->get('email');
            }

            if ($request->get('password')) {
                auth()->user()->password = bcrypt($request->get('password'));
            }

            auth()->user()->save();

            flash(trans('messages.success'), 'success');
        } catch (\Exception $e) {
            flash(trans('messages.exception'), 'danger');
        }

        return redirect(route('app.profile'));
    }
}
