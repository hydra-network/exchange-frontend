<?php

namespace App\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;

class Api
{
    public function map(Registrar $router)
    {
        $router->group([
            'guard' => 'api',
            'prefix' => 'api',
            'middleware' => 'jwt.auth',
        ], function ($router) {
            $router->group([
                'prefix' => 'v1',
            ], function ($router) {
                $router->get('balances/deposit/getAddress/{code}', 'BalancesController@getDepositAddress')->name('deposit.address');
                $router->get('balances/deposit/getList/{code}', 'BalancesController@getDepositList')->name('deposit.list');
                $router->get('balances/withdrawal/getList/{code}', 'BalancesController@getWithdrawalList')->name('withdrawal.list');
                $router->post('balances/withdrawal/order', 'BalancesController@withdrawalOrder');

                $router->post('login', 'AuthController@login');
                $router->post('logout', 'AuthController@logout');
                $router->post('refresh', 'AuthController@refresh');
                $router->post('me', 'AuthController@me');
            });
        });

        $router->group([
            'guard' => 'api',
            'prefix' => 'api',
            'middleware' => 'jwt.verify',
        ], function ($router) {
            $router->group([
                'prefix' => 'v1',
            ], function ($router) {
                $router->get('market/getTicks/{code}/{period}', 'MarketController@getTicks')->name('market.ticker');
                $router->get('market/getBalance/{code}', 'MarketController@getBalances')->name('market.balance'); //tests are exists
                $router->get('market/getSummary/{code}', 'MarketController@getSummaryInfo')->name('market.summary');
                $router->get('market/order/getList/{code}/{type}', 'MarketController@getOrdersList')->name('market.orders'); //tests are exists
                $router->get('market/order/getMyList', 'MarketController@getMyOrdersList')->name('market.orders.my');
                $router->get('market/deals/getList/{code}/{my}/{buy}/{sell}', 'MarketController@getDeals')->name('market.deals'); //tests are exists
                $router->post('market/order/add', 'MarketController@addOrder')->name('market.order.add'); //tests are exists
                $router->post('market/order/remove', 'MarketController@removeOrder')->name('market.order.remove'); //tests are exists
                /*
                $router->post('escrow/generateLink', 'EscrowController@generateLink')->name('market.escrow.link');
                $router->post('escrow/cancel', 'EscrowController@cancel')->name('market.escrow.cancel'); */
            });
        });

        /*
        $router->group([
            'prefix' => 'api',
        ], function ($router) {
            $router->group([
                'prefix' => 'v1',
            ], function ($router) {
                $router->get('escrow/getOrder/{id}', 'EscrowController@getOrder');
                $router->get('escrow/getEscrowOrder/{id}', 'EscrowController@getEscrowOrder');
                $router->post('escrow/activateOrder/{id}', 'EscrowController@activateOrder');
            });
        });
        */
    }
}
