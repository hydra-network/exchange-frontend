<template>
    <div class="content trade-app">
        <div class="row">
            <div class="col-lg-9">
                <div class="info-block">
                    <table title="24 hours"  width="100%">
                        <tbody class="table-no-bordered">
                            <tr>
                                <td>High: {{h24}} {{ primary_asset }}</td>
                                <td>Low: {{l24}} {{ primary_asset }}</td>
                                <td><strong>Last: {{ last_price }} {{ primary_asset }}</strong></td>
                                <td>Volume: {{ daily_volume }} {{ primary_asset }}</td>
                                <td>
                                    <div class="pull-right periods">
                                        <small>
                                            <a href="javascript:" v-for="(item, key, index) in timePeriods" @click="chart_period=key" v-bind:class="{ active: (key==chart_period) }"> {{item}}</a>
                                        </small>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="clear: both;"></div>

                <section id="chart" class="chart" ref="chartdiv">

                </section>

                <div style="clear: both;"></div>

                <div class="row">
                    <div class="col-md-12" v-if="bid_size_percent > 0 && ask_size_percent > 0">
                        <div class="volume-line">
                            <div class="bid-volume" v-bind:style="'width: ' + bid_size_percent + '%'">{{ bid_size }} {{ secondary_asset }}</div>
                            <div class="ask-volume" v-bind:style="'width: ' + ask_size_percent + '%'">{{ ask_size }} {{ secondary_asset }}</div>
                        </div>
                        <br />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-5 volume-items left-volume">
                        <h3>
                            <div class="pull-left">BID <small><small>offers to buy</small></small></div>
                            <div class="pull-right"><small>{{ primary_asset_volume }} {{ primary_asset }}</small></div>
                        </h3>
                        <div style="clear: both;"></div>
                        <div class="trade-table-container">
                            <table class="table table-hover trade-table bid-table table-striped trade-table dataTable">
                                <thead>
                                    <tr>
                                        <th>Cost {{ primary_asset }}</th>
                                        <th>Size {{ secondary_asset }}</th>
                                        <th>Price</th>
                                        <th>Sell</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="!bid_list.data | !bid_list.data.length">
                                        <td colspan="4">Empty</td>
                                    </tr>

                                    <tr v-for="item in bid_list.data"  :title="'Orders count: ' + item.count">
                                        <td>{{ item.cost_remain }} </td>
                                        <td>{{ item.quantity_remain }} </td>
                                        <td>
                                            <strong>{{ item.price }}</strong>
                                            <i v-if="item.my" class="my-order glyphicon glyphicon-user"></i>
                                        </td>
                                        <td><a href="javascript:" @click="setPrice(bid_list.data, item.price)"> <i class="glyphicon glyphicon-ok"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="orders-pagination">
                                <pagination :limit="2" :data="Object.assign({}, bid_list)" @pagination-change-page="getBuyOrders"></pagination>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 add-order">
                        <h3>Order <small><small>your new offer</small></small></h3>
                        <div class="">
                            <label style="width: 100%;">
                                {{ secondary_asset }} quantity <small v-if="secondary_asset_data && secondary_asset_data.min_trade_amount"> (min {{ secondary_asset_data.min_trade_amount }})</small>
                                <input type="text" :title="'Balance: ' + primary_asset_balance" v-model="order_quantity" class="form-control" @blur="calculate" />
                            </label>
                            <label style="width: 100%;">
                                {{ primary_asset }} price  <small v-if="secondary_asset_data && secondary_asset_data.min_price"> (min {{ secondary_asset_data.min_price }})</small>
                                <input type="text" v-model="order_price" class="form-control" @blur="calculate" />
                            </label>
                            <label style="width: 100%;">
                                {{ primary_asset }} cost
                                <input type="text"  :title="'Balance: ' + secondary_asset_balance" v-model="primary_asset_cost" disabled class="form-control" />
                            </label>
                            <div style="clear: both"></div>

                            <button class="btn btn-success pull-left" @click="buy"><i class="glyphicon glyphicon-chevron-left"></i> Buy</button>
                            <button class="btn btn-danger pull-right" @click="sell">Sell <i class="glyphicon glyphicon-chevron-right"></i></button>

                        </div>
                    </div>
                    <div class="col-md-5 volume-items right-volume">
                        <h3>
                            <div class="pull-right">ASK <small><small>offers to sell</small></small></div>
                            <div class="pull-left"><small>{{ secondary_asset_volume }} {{ secondary_asset }}</small></div>
                        </h3>
                        <div style="clear: both;"></div>
                        <div class="trade-table-container">
                            <table class="table table-hover trade-table ask table-striped trade-table dataTable">
                                <thead>
                                    <tr>
                                        <th>Buy</th>
                                        <th>Price</th>
                                        <th>Size {{ secondary_asset }}</th>
                                        <th>Cost {{ primary_asset }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="!ask_list.data | !ask_list.data.length">
                                        <td colspan="4">Empty</td>
                                    </tr>
                                    <tr v-for="item in ask_list.data" :title="'Orders count: ' + item.count">
                                        <td><a href="javascript:" @click="setPrice(ask_list.data, item.price)"><i class="glyphicon glyphicon-ok"></i></a></td>
                                        <td>
                                            <strong>{{ item.price }}</strong>
                                            <i v-if="item.my" class="my-order glyphicon glyphicon-user"></i>
                                        </td>
                                        <td>{{ item.quantity_remain }} </td>
                                        <td>{{ item.cost_remain }} </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="orders-pagination">
                            <pagination :limit="2" :data="Object.assign({}, ask_list)" @pagination-change-page="getSellOrders"></pagination>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="trade-statistics">
                    <h3>My active orders</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <h4>BID <small><small>offers to buy</small></small></h4>
                            <table class="table table-hover trade-table ask-table stat-table">
                                <thead>
                                <tr>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Remain</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="!my_buy_orders_list | !my_buy_orders_list.length">
                                    <td colspan="6">Empty</td>
                                </tr>
                                <tr v-for="item in my_buy_orders_list">
                                    <td :title="item.id"><strong>{{ item.price }}</strong></td>
                                    <td>{{ item.quantity }}</td>
                                    <td>{{ item.quantity_remain }}</td>
                                    <td>{{ item.status }}</td>
                                    <td>{{ item.created_at }}</td>
                                    <td><a href="javascript:" style="color: red;" @click="removeOrder(item.id)"><i class="glyphicon glyphicon-remove"></i></a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>ASK <small><small>offers to sell</small></small></h4>
                            <table class="table table-hover trade-table ask-table stat-table">
                                <thead>
                                <tr>
                                    <th>Quantity</th>
                                    <th>Remain</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Price</th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="!my_sell_orders_list | !my_sell_orders_list.length">
                                    <td colspan="6">Empty</td>
                                </tr>
                                <tr v-for="item in my_sell_orders_list">
                                    <td>{{ item.quantity }}</td>
                                    <td>{{ item.quantity_remain }}</td>
                                    <td>{{ item.status }}</td>
                                    <td>{{ item.created_at }}</td>
                                    <td><strong>{{ item.price }}</strong></td>
                                    <td><a href="javascript:" style="color: red;" @click="removeOrder(item.id)"><i class="glyphicon glyphicon-remove"></i></a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="left-block">
                    <div class="">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item active">
                                <a class="nav-link active" data-toggle="tab" href="#primary-asset">{{ primary_asset }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#secondary-asset">{{ secondary_asset }}</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="primary-asset">
                                <table class="table table-stripped trade-table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Balance</th>
                                        <th title="Unconfirmed balance">Unc. balance</th>
                                        <th>In orders</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td width="40%">{{ primary_asset_balance }}</td>
                                        <td>{{ primary_asset_unc_balance }}</td>
                                        <td>{{ primary_asset_io_balance }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div style="clear: both;"></div>
                                <div class="balance-buttons text-center" v-if="primary_asset_data">
                                    <a :href="route('app.balances.deposit', {code: primary_asset_data.code})" class="btn btn-success" title="deposit" target="_blank">Deposit <i class="glyphicon glyphicon-log-in"></i></a>
                                    <a :href="route('app.balances.withdrawal', {code: primary_asset_data.code})" class="btn" title="withdrawal" target="_blank">Withdrawal <i class="glyphicon glyphicon-log-out"></i></a>
                                </div>
                            </div>
                            <div class="tab-pane" id="secondary-asset">
                                <table class="table table-stripped trade-table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Amount</th>
                                        <th title="Unconfirmed balance">Unc. balance</th>
                                        <th>In orders</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td width="40%">{{ secondary_asset_balance }}</td>
                                        <td>{{ secondary_asset_unc_balance }}</td>
                                        <td>{{ secondary_asset_io_balance }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div style="clear: both;"></div>
                                <div class="balance-buttons text-center" v-if="secondary_asset_data">
                                    <a :href="route('app.balances.deposit', {code: secondary_asset_data.code})" class="btn btn-success" title="deposit" target="_blank">Deposit <i class="glyphicon glyphicon-log-in"></i></a>
                                    <a :href="route('app.balances.withdrawal', {code: secondary_asset_data.code})" class="btn" title="withdrawal" target="_blank">Withdrawal <i class="glyphicon glyphicon-log-out"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <h4 align="center">Deals history</h4>
                    <div class="deals-table-container">
                        <div class="deals-control">
                            <div class="pull-right">
                                Show:
                                <label>
                                    Mine
                                    <input type="checkbox" v-model="deals_show_my_history" />
                                </label>
                                <label>
                                    Buy
                                    <input type="checkbox" v-model="deals_show_buy_history" />
                                </label>
                                <label>
                                    Sell
                                    <input type="checkbox" v-model="deals_show_sell_history" />
                                </label>
                            </div>
                        </div>
                        <table class="table table-striped trade-table table-hover trade-table deals-table stat-table">
                            <tr>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Date</th>
                            </tr>
                            <tr v-if="!deals_list.data | !deals_list.data.length">
                                <td colspan="4">Empty</td>
                            </tr>
                            <tr v-for="item in deals_list.data" :title="'ID is ' + item.id">
                                <td>
                                    <span v-if="item.type == 'sell'" class="dot_sell">&nbsp;</span>
                                    <span  v-if="item.type == 'buy'" class="dot_buy">&nbsp;</span>
                                    {{ item.quantity }}
                                </td>
                                <td><strong>{{ item.price }}</strong></td>
                                <td>{{ item.created_at }}</td>
                            </tr>
                        </table>

                        <pagination :limit="2" :data="Object.assign({}, deals_list)" @pagination-change-page="getDeals"></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue'
    import axios from 'axios';
    import AWN from "awesome-notifications"
    import zingchart from "zingchart";
    import pagination from 'laravel-vue-pagination';

    let notifier = new AWN({})

    export default {
        components: {
            pagination
        },
        data() {
            return {
                timePeriods: {
                    1: "1 min",
                    5: "5 min",
                    10: "10 min",
                    30: "30 min",
                    60: "1h",
                    120: "2h",
                    360: "6h",
                    720: "12h",
                    1440: "1d"
                },
                loaded: false,
                marketName: '...',
                chart_period: null,
                pair: null,
                primary_asset: null,
                secondary_asset: null,
                primary_asset_balance: null,
                secondary_asset_balance: null,
                primary_asset_unc_balance: null,
                secondary_asset_unc_balance: null,
                primary_asset_io_balance: null,
                secondary_asset_io_balance: null,
                primary_asset_data: null,
                secondary_asset_data: null,
                primary_asset_volume: null,
                secondary_asset_volume: null,
                bid_size_percent: 50,
                ask_size_percent: 50,
                bid_size: 0,
                ask_size: 0,
                last_price: null,
                ask_list: {data: []},
                bid_list: {data: []},
                my_sell_orders_list: [],
                my_buy_orders_list: [],
                deals_list: {data: []},
                primary_asset_cost: 0,
                daily_volume: null,
                h24: null,
                l24: null,
                order_quantity: 0,
                order_price: 0,
                user_id: null,
                deals_show_buy_history: true,
                deals_show_sell_history: true,
                deals_show_my_history: false,
                currentPages: {
                    deals: 1,
                    buyOrders: 1,
                    sellOrders: 1
                }
            }
        },
        computed: {

        },
        watch: {
            chart_period: function(new_val) {
                localStorage.setItem('chart_period', new_val);

                this.updateChart();
            },
            deals_show_my_history: function() {
                this.getDeals();
            },
            deals_show_buy_history: function() {
                this.getDeals();
            },
            deals_show_sell_history: function() {
                this.getDeals();
            },
        },
        methods: {
            updateChart: function() {
                var that = this;

                axios.get(route("market.ticker", {code: that.pair, period: parseInt(that.chart_period)}))
                    .then((response) => {
                        var myTheme = {
                            palette:{
                                line:[
                                    ['#FBFCFE', '#00BAF2', '#00BAF2', '#00a7d9'], /* light blue */
                                    ['#FBFCFE', '#E80C60', '#E80C60', '#d00a56'], /* light pink */
                                    ['#FBFCFE', '#9B26AF', '#9B26AF', '#8b229d'], /* light purple */
                                    ['#FBFCFE', '#E2D51A', '#E2D51A', '#E2D51A'], /* med yellow */
                                    ['#FBFCFE', '#FB301E', '#FB301E', '#e12b1b'], /* med red */
                                    ['#FBFCFE', '#00AE4D', '#00AE4D', '#00AE4D'], /* med green */
                                ]
                            }
                        };

                        zingchart.THEME = myTheme;

                        let dateFormat = "%H:%i";
                        if (that.chart_period >= 120) {
                            dateFormat = "%d.%m.%Y<br>%H:%i:%s";
                        }

                        let max_price = parseFloat(response.data.max_price);
                        let min_price = parseFloat(response.data.min_price);

                        let min_chart_price = ((min_price)-(min_price*0.2));
                        let max_chart_price = ((max_price)+(max_price*0.1));

                        let myConfig = {
                            "type": "mixed",
                            "title": {
                                "text": ""
                            },
                            "utc": true,
                            "timezone": 0,
                            "scale-x": {
                                "min-value": parseInt(response.data.time_start) * 1000,
                                "step": parseInt(that.chart_period) * 60 * 1000,
                                "transform": {
                                    "type":"date",
                                    "all":dateFormat
                                },
                                "item":{
                                    "font-size":9
                                }
                            },
                            "scale-y": { //for Stock Chart
                                "offset-start": "35%", //to adjust scale offsets.
                                "format": "%v",
                                //"values": "12000:5000",
                                "values": max_chart_price + ':' + min_chart_price,
                                "label": {
                                    "text": "Prices"
                                }
                            },
                            "scale-y-2": { //for Volume Chart
                                "placement": "default", //to move scale to default (left) side.
                                "blended": true, //to bind the scale to "scale-y".
                                "offset-end": "75%", //to adjust scale offsets.
                                //"values": "0:" + response.data.max_volume + ":" + response.data.max_volume,
                                "format": "%v",
                                "label": {
                                    "text": "Volume"
                                }
                            },
                            "series": [
                                {
                                    "type": "stock", //Stock Chart
                                    "scales": "scale-x,scale-y", //to set applicable scales.
                                    "values": response.data.ticker.ohlc
                                },
                                {
                                    "type": "bar", //Volume Chart
                                    "background-color": "#f4f4f4",
                                    "scales": "scale-x,scale-y-2", //to set applicable scales.
                                    "values": response.data.ticker.volume
                                }
                            ]
                        };

                        $('#chart').html("");
console.log(myConfig);
                        zingchart.render({
                            id : 'chart',
                            data : myConfig,
                        });
                    });
            },
            reset: function () {
                this.primary_asset_cost = 0;
                this.order_quantity = 0;

                if (this.secondary_asset_data.min_trade_amount) {
                    this.order_quantity = this.secondary_asset_data.min_trade_amount;
                }

                this.order_price = 0;
                if (this.secondary_asset_data.min_price) {
                    this.order_price = this.secondary_asset_data.min_price;
                }
            },
            updateSummary: function() {
                var that = this;

                axios.get(route("market.summary", {code: that.pair}))
                    .then((response) => {
                        that.primary_asset_volume = response.data.primary_asset_volume;
                        that.secondary_asset_volume = response.data.secondary_asset_volume;
                        that.bid_size = response.data.bid_size;
                        that.ask_size = response.data.ask_size;
                        that.last_price = response.data.last_price;
                        that.daily_volume = response.data.daily_volume;
                        that.l24 = response.data.l24;
                        that.h24 = response.data.h24;

                        if (response.data.alerts) {
                            $(response.data.alerts).each((i, el) => {
                                if (el.class == 'success') {
                                    notifier.success(el.message);
                                }
                            });
                        }

                        that.updateVolumeChart();
                    });
            },
            updateBalances: function() {
                var that = this;

                axios.get(route("market.balance", {code: that.pair}))
                    .then((response) => {
                    that.primary_asset_balance = response.data.primary_asset;
                    that.secondary_asset_balance = response.data.secondary_asset;
                    that.primary_asset_unc_balance = response.data.primary_asset_unc_balance;
                    that.secondary_asset_unc_balance = response.data.secondary_asset_unc_balance;
                    that.primary_asset_io_balance = response.data.primary_asset_io_balance;
                    that.secondary_asset_io_balance = response.data.secondary_asset_io_balance;
                });
            },
            updateOrders: function() {
                if (this.currentPages.buyOrders == 1) this.getBuyOrders();
                if (this.currentPages.sellOrders == 1) this.getSellOrders();
            },
            getBuyOrders: function(page = 1) {
                this.currentPages.buyOrders = page;

                var that = this;

                axios.get(route("market.orders", {code: that.pair, type: "buy", page: page}))
                    .then((response) => {
                        that.bid_list = response.data.list;
                        that.my_buy_orders_list = response.data.my_list;
                    });
            },
            getSellOrders: function(page = 1) {
                this.currentPages.sellOrders = page;

                var that = this;

                axios.get(route("market.orders", {code: that.pair, type: "sell", page: page}))
                    .then((response) => {
                        that.ask_list = response.data.list;
                        that.my_sell_orders_list = response.data.my_list;
                    });
            },
            updateDeals: function() {
                if (this.currentPages.deals == 1) this.getDeals();
            },
            getDeals: function(page = 1) {
                this.currentPages.deals = page;

                var that = this;

                axios.get(route("market.deals", {code: this.pair, page: page, my: (this.deals_show_my_history) ? 1 : 0, buy: (this.deals_show_buy_history) ? 1 : 0, sell: (this.deals_show_sell_history) ? 1 : 0}))
                    .then((response) => {
                        that.deals_list = response.data;
                    });
            },
            updateDashboard: function() {
                this.updateDeals();
                this.updateOrders();
                this.updateBalances();
            },
            addOrder: function(type) {
                var that = this;

                if (!confirm ("Really want to " + type + " " + that.order_quantity + " with price " + that.order_price + "?")) {
                    return false;
                }

                let quantity = that.order_quantity;
                let price = that.order_price;

                axios.post(route("market.order.add"), {pair: this.pair, type: type, quantity: quantity.replace(' ', ''), price: price.replace(' ', '')})
                    .then((response) => {
                    that.reset();
                    that.updateDashboard();
                });
            },
            route: function(id, params) {
                return route(id, params);
            },
            sell: function() {
                this.addOrder('sell');
            },
            buy: function() {
                this.addOrder('buy');
            },
            showErrors: function(errors) {
                Object.keys(errors).forEach(function(element, key) {
                    alert(errors[element]);
                });
            },
            calculate: function() {
                this.primary_asset_cost = Number.parseFloat(Number.parseFloat(this.order_quantity) * Number.parseFloat(this.order_price)).toFixed(this.primary_asset_data.round);
            },
            removeOrder: function(id) {
                var that = this;
                axios.post(route("market.order.remove"), {id: id})
                    .then((response) => {
                        that.updateDashboard();
                    });
            },
            setPrice: function(list, price) {
                this.order_price = price;
                let orderQuantity = 0;

                $(list).each(function() {
                    if (this.price <= price) {
                        orderQuantity += parseFloat(this.quantity_remain);
                    }
                });

                this.order_quantity = Number.parseFloat(orderQuantity).toFixed(this.secondary_asset_data.round);
                this.calculate();
            },
            updateVolumeChart: function() {
                let bid_size = Number.parseFloat(this.bid_size);
                let ask_size = Number.parseFloat(this.ask_size);
                var sum = bid_size + ask_size;

                this.bid_size_percent = bid_size*100/sum;
                this.ask_size_percent = ask_size*100/sum;

                if (this.bid_size_percent < 9) {
                    this.bid_size_percent = 9;
                    this.ask_size_percent = 91;
                }

                if (this.ask_size_percent < 9) {
                    this.ask_size_percent = 9;
                    this.bid_size_percent = 91;
                }
            }
        },
        mounted() {
            let hydra = window.hydra;

            if (hydra && hydra.market) {
                this.pair = hydra.market.pair.code;
                this.primary_asset = hydra.market.primary_asset.code;
                this.secondary_asset = hydra.market.secondary_asset.code;
                this.primary_asset_balance = hydra.market.primary_asset_balance;
                this.secondary_asset_balance = hydra.market.secondary_asset_balance;
                this.primary_asset_unc_balance = hydra.market.primary_asset_unc_balance;
                this.secondary_asset_unc_balance = hydra.market.secondary_asset_unc_balance;
                this.primary_asset_io_balance = hydra.market.primary_asset_io_balance;
                this.secondary_asset_io_balance = hydra.market.secondary_asset_io_balance;
                this.secondary_asset_data = hydra.market.secondary_asset;
                this.primary_asset_data = hydra.market.primary_asset;

                if (hydra.auth_token) {
                    axios.defaults.headers.common['Authorization'] = 'Bearer ' + hydra.auth_token;
                }
            }

            let chart_period;
            if (chart_period = localStorage.getItem('chart_period')) {
                this.chart_period = chart_period;
            } else {
                this.chart_period = 5;
            }

            this.marketName = this.pair;

            this.reset();
            this.updateSummary();
            this.updateDashboard();
            this.updateBalances();

            setInterval(function () {
                this.updateDashboard();
                this.updateChart();
            }.bind(this), 20000);

            setInterval(function () {
                this.updateBalances();
                this.updateSummary();
            }.bind(this), 70000);

            this.loaded = true;
        }
    }
</script>

<style>
    .dot_sell {
        width: 5px;
        height: 5px;
        background-color: red;
    }
    .dot_buy {
        color: green;
        width: 5px;
        height: 5px;
        background-color: green;
    }

    .deals-control label {
        padding-left: 14px;
    }

    .my-order {
        color: #ccc;
    }

    .left-block {
        width: 401px;
        padding: 8px;
        position: fixed;
        top: 1px;
        background-color: #f9f9f9;
        border-left: 1px solid #ccc;
        height: 100vh;
        z-index: 10;
        font-size: 12px;
    }

    .deals-table-container {
        width: 100%;
        height: 500px;
        overflow-y: scroll;
    }

    .periods a {
        float: left;
        display: block;
        padding: 4px;
    }

    .periods .active {
        font-weight: bold;
    }

    #chart-license-text {
        display: none;
    }

    #chart {
        min-height: 485px;
    }

    .volume-line {
        width: 100%;
        height: 20px;
    }

    .volume-line .bid-volume {
        color: white;
        padding: 3px;
        font-size: 12px;
        text-align: right;
        background-color: #8cd6ba;
        width: 50%;
        float: left;
    }

    .volume-line .ask-volume {
        color: white;
        padding: 3px;
        font-size: 12px;
        text-align: left;
        background-color: #ecb5ba;
        width: 50%;
        float: right;
    }

    .trade-table td, .trade-table th {
        font-family: 'Roboto';
    }

    .dataTable td {
        padding: 1px !important;
        font-size: 12px;
    }

    .trade-app tr:hover td {
        background-color: #e4e4e4;
    }

    .nav-tabs {
        margin-left: -9px;
        margin-top: -9px;
    }

    .pagination > li > a, .pagination > li > span {
        padding: 4px 9px;
    }

    .table-no-bordered tr:hover td {
        background-color: #fff;
    }
</style>
