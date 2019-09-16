# Hydra EX

For now here is an ordinal centralized exchange (EX), ready to 80%. But the primarily goal is to create
the first decentralized exchange (DEX). The DEX is 15% ready.

The basic market advantage of Hydra is fully open source and decentralization. No license
to code or idea. No one human is the owner or author of code or project. It means that nobody able
to close the project or stop it by scam or concessions to government. 

This is why the name of project is Hydra. There will not be the main head, every private person can become
an another one head of project, just installing the node to server or committing some code. Or even by
creation of independent fork (all forks are welcome). 

![hydra](https://user-images.githubusercontent.com/8104605/60806324-5cbe1d00-a18b-11e9-8412-e3af1bd91229.png)

_Zmey Gorynych (on picture) is a fork of Hydra character in Russian folklore._ 

This repository includes:

 * PHP API (Laravel)
 * Trader Interface for operate with API (VueJS)
 
## Install

Via Laradock (change mysql version to 5.7 in .env):

```
docker-compose up -d nginx php-fpm mysql
docker exec -it laradock_workspace_1 bash
chmod -R 0777 storage/logs
chmod -R 0777 storage/framework
php artisan key:generate
cp .env.example .env
```

Config editing
```
nano .env #for config editor
```

Installing of dependencies and migrations

```
composer install
php artisan migrate
php artisan db:seed
php artisan jwt:secret
```

Demo data:

```
php artisan db:seed --class=DatabaseSeeder
```

Launching of queue consumer:

``
php artisan queue:work --queue=matching
``

For now there is backend and frontend, a bit later I'll separate this repository for 2 independent components.
 
## EX functionality:

 * ~~Registration and authorization, reset password (with 2FA and confirmation of email)~~;
 * ~~Markets page~~;
 * ~~Market page with a chart and control panels of user~~;
 * ~~Adding of orders to order book, cancel of orders~~;
 * ~~Orders matcher (assets switcher by changing of balance of both sides of deal)~~;
 * ~~Orders history~~;
 * ~~Market restrictions by owners of assets (daily volume limit, price limit, etc)~~;
 * Simple admin panel for administrator;
 * Deposit and withdrawal of cryptocurrencies through a crypto-driver (all other assets are compatible);
 * Simple admin panel for assets owners;
 * Fees system;
 * Referral system.
 
# Hydra DEX
 
## Roadmap 

The roadmap for short term period is:
 
 * ~~Feature tests of API; - 28.06.2019~~
 * Withdrawal and deposit systems; - 01.07.2019
 * ~~Design for page of list of markets; - 30.06.2019~~
 * ~~Demo instance for demonstration of basic advantages of EX; - 05.07.2019~~
 * Landing page and video presentation of EX; - 05.07.2019
 * ~~Docker container, instructions for simple installation; - 07.07.2019~~
 * ~~Creation of temporary ERC20 token (HDR) for period while the DEX is in development; - 10.07.2019~~
 * Launching of token trading on EX (HYST/BTC); - 15.07.2019
 * EX documentation; - 25.07.2019
 * Fees system; - 30.07.2019
 * Referral program; - 05.08.2019
 * Simple interface for non-professional traders (possible to buy and sell of any assets without
 chart and all other complex interfaces for professional traders); - 15.08.2019
 * Trading View chart; - 30.08.2019
 
## DEX functionality

Long term plans (not sure I'll be strong enough):

 * Hydra Dex Protocol. This paper will show my thoughts about architect of DEX (consensus, roles, workflow of order and deal);
 * Own blockchain with security token on board;
 * Node server for security tokens owners;
 
 .. coming soon


