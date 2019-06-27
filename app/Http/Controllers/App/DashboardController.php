<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Pair;
use Google2FA;

class DashboardController extends Controller
{
    /**
     * Show the application app.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencies = Asset::orderBy('name')->get();
        $pairs = Pair::where('status', Pair::STATUS_ACTIVE)->orderBy('id', 'ASC')->get();

        return view('app.dashboard', [
            'currencies' => $currencies,
            'pairs' => $pairs,
        ]);
    }
    
    public function mailSender(Request $request)
    {
        $result = Mail::send('emails.contacts_form', ['name' => $request->input('name'), 'contact' => $request->input('contact')], function ($message) use ($request) {
            $message->to($request->input('to'), $request->input('to_name'))->subject($request->input('subject'));
        });
        
        return 1;
    }
}
