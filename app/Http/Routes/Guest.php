<?php

namespace App\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;

class Guest
{
    /**
     * @param \Illuminate\Contracts\Routing\Registrar $router
     */
    public function map(Registrar $router)
    {
        $router->group(['as' => 'guest.'], function () use ($router) {
            $router->get('/', 'HomeController@index')->name('home');
        });

        $router->group(['as' => 'guest.'], function () use ($router) {
            $router->get('e/buy/{id}', 'EscrowController@form')->name('escrow.buy');
            $router->get('e/view/{hash}', 'EscrowController@view')->name('escrow.view');
        });
    }
}