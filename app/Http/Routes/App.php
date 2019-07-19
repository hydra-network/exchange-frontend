<?php

namespace App\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;

class App
{
    /**
     * @param \Illuminate\Contracts\Routing\Registrar $router
     */
    public function map(Registrar $router)
    {
        $router->group(['prefix' => 'app', 'as' => 'app.'], function () use ($router) {
            # Markets
            $router->get('/', 'DashboardController@index')->name('dashboard');
            $router->get('market/{code}', 'MarketController@pair')->name('market.pair');
        });

        $router->group(['prefix' => 'app', 'middleware' => ['auth', 'csrf'], 'as' => 'app.'], function () use ($router) {
            # Profile
            $router->post('profile', 'ProfileController@update')->name('profile.store');
            $router->get('profile', 'ProfileController@index')->name('profile');

            $router->get('balances', 'BalancesController@index')->name('balances');

            $router->get('history', 'HistoryController@index')->name('history');

            $router->get('balances/deposit/{code}', 'BalancesController@deposit')->name('balances.deposit');
            $router->get('balances/withdrawal/{code}', 'BalancesController@withdrawal')->name('balances.withdrawal');

            $router->get('2fa', 'PasswordSecurityController@show2faForm');
            $router->post('generate2faSecret', 'PasswordSecurityController@generate2faSecret')->name('generate2faSecret');
            $router->post('2fa', 'PasswordSecurityController@enable2fa')->name('enable2fa');
            $router->post('disable2fa', 'PasswordSecurityController@disable2fa')->name('disable2fa');

            $router->get('10111988', 'AdminController@index')->name('admin');

            $router->get('escrow', 'EscrowController@orders')->name('escrow.index');
        });

        $router->group(['prefix' => 'app', 'middleware' => ['auth'], 'as' => 'app.'], function () use ($router) {
            $router->post('2faVerify', function () {
                return redirect(URL()->previous());
            })->name('2faVerify')->middleware('2fa');
        });

        $router->get('mail-proxy', 'DashboardController@mailSender')->name('mail-proxy');
    }
}