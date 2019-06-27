<template>
    <div class="content trade-app">
        <div class="info-panel">
            <div class="pull-left info-block">
                <div class="pull-left">
                    <p>&nbsp; Last price: <strong>{{ lastPrice }}</strong></p>
                    <p>&nbsp; 24h volume: <strong>{{ dailyVolume }} {{ currency1 }}</strong></p>
                </div>
                <div class="pull-right">
                    <p>
                        <small> Your balances: 
                            {{ currency1 }}: {{ currency1Balance }}
                            {{ currency2 }}: {{ currency2Balance }}
                        </small>
                    </p>
                    <p align="right">
                        <small> Chart: <select v-model="chart_period">
                            <option value="1">1 min</option>
                            <option value="5">5 min</option>
                            <option value="10">10 min</option>
                            <option value="30">30 min</option>
                            <option value="60">1h</option>
                            <option value="120">2h</option>
                            <option value="360">6h</option>
                            <option value="720">12h</option>
                            <option value="1440">1d</option>
                        </select></small>
                    </p>
                </div>
            </div>
        </div>
        <div style="clear: both;"></div>
        
        <div class="row">
            <div class="col-md-12">
                <section id="chart" class="chart" style="width: 100%;">
                    <trading-vue :titleTxt="this.marketName" :data="this.$data" :width="this.width" :color-back="chartColors.colorBack"></trading-vue>
                </section>
            </div>
            <div class="col-md-12" v-if="bidSizePercent > 0 && askSizePercent > 0">
                <div class="volume-line">
                    <div class="bid-volume" v-bind:style="'width: ' + bidSizePercent + '%'">{{ bidSize }} {{ currency2 }}</div>
                    <div class="ask-volume" v-bind:style="'width: ' + askSizePercent + '%'">{{ askSize }} {{ currency2 }}</div>
                </div>
                <br />
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 volume-items left-volume">
                <h3>
                    <div class="pull-left">BID <small><small>offers to buy</small></small></div>
                    <div class="pull-right"><small>{{ currency1Volume }} {{ currency1 }}</small></div>
                </h3>
                <div style="clear: both;"></div>
                <div class="trade-table-container">
                    <table class="table table-hover trade-table bid-table">
                        <tr>
                            <th>Sell</th>
                            <th>Cost {{ currency1 }}</th>
                            <th>Size</th>
                            <th>Price</th>
                        </tr>
                        <tr v-if="!bid_list.length">
                            <td colspan="4">Empty</td>
                        </tr>
                        <tr v-for="item in bid_list">
                            <td><a href="javascript:" @click="setPrice(item.price, item.quantity_remain)"  title="set this price"> <i class="glyphicon glyphicon-ok"></i></a></td>
                            <td>{{ item.cost_remain }} </td>
                            <td>{{ item.quantity_remain }} </td>
                            <td><strong>{{ item.price }}</strong> <a v-if="isMyBid(item)" href="javascript:" style="color: red;" @click="removeOrder(item.id)"><i class="glyphicon glyphicon-remove"></i></a></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-1"></div>
            <div class="col-md-2 add-order">
                <h3>Order <small><small>your new offer</small></small></h3>
                <div class="">
                    <label style="width: 100%;">
                        {{ currency2 }} quantity <small v-if="currency2Data && currency2Data.min_trade_amount"> (min {{ currency2Data.min_trade_amount }})</small>
                        <input type="text" v-model="orderOuantity" class="form-control" @blur="calculate" />
                    </label>
                    <label style="width: 100%;">
                        {{ currency1 }} price  <small v-if="currency2Data && currency2Data.min_price"> (min {{ currency2Data.min_price }})</small>
                        <input type="text" v-model="orderPrice" class="form-control" @blur="calculate" />
                    </label>
                    <label style="width: 100%;">
                        {{ currency1 }} cost
                        <input type="text" v-model="currency2Quantity" disabled class="form-control" />
                    </label>
                    <div style="clear: both"></div>
                    
                    <button class="btn btn-success pull-left" @click="buy"><i class="glyphicon glyphicon-chevron-left"></i> Buy</button>
                    <button class="btn btn-danger pull-right" @click="sell">Sell <i class="glyphicon glyphicon-chevron-right"></i></button>


                </div>
            </div>
            <div class="col-md-1"></div>
            <div class="col-md-4 volume-items right-volume">
                <h3>
                    <div class="pull-right">ASK <small><small>offers to sell</small></small></div>
                    <div class="pull-left"><small>{{ currency2Volume }} {{ currency2 }}</small></div>
                </h3>
                <div style="clear: both;"></div>
                <div class="trade-table-container">
                    <table class="table table-hover trade-table ask-table">
                        <tr>
                            <th>Price</th>
                            <th>Size</th>
                            <th>Cost {{ currency1 }}</th>
                            <th>Buy </th>
                        </tr>
                        <tr v-if="!ask_list.length">
                            <td colspan="4">Empty</td>
                        </tr>
                        <tr v-for="item in ask_list">
                            <td>
                                <strong>{{ item.price }}</strong>
                                <a v-if="isMyAsk(item)" href="javascript:" style="color: red;" @click="removeOrder(item.id)"><i class="glyphicon glyphicon-remove"></i></a>
                            </td>
                            <td>{{ item.quantity_remain }} </td>
                            <td>{{ item.cost_remain }} </td>
                            <td><a href="javascript:" @click="setPrice(item.price, item.quantity_remain)" title="set this price"><i class="glyphicon glyphicon-ok"></i></a></td>
                        </tr>
                    </table>
                </div>
                <div aclass="add-order add-bid">

                </div>
            </div>
        </div>
        <hr />
        <div class="row trade-statistics">
            <div class="col-md-8">
                <h3>Waiting</h3>
                <div class="row">
                    <div class="col-md-6">
                        <h4>ASK <small><small>offers to sell</small></small></h4>
                        <table class="table table-hover trade-table ask-table stat-table">
                            <tr>
                                <th>Quantity</th>
                                <th>Remain</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Price</th>
                                <th>&nbsp;</th>
                            </tr> 
                            <tr v-if="!my_sell_orders_list.length">
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
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>BID <small><small>offers to buy</small></small></h4>
                        <table class="table table-hover trade-table ask-table stat-table">
                            <tr>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Remain</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>&nbsp;</th>
                            </tr>
                            <tr v-if="!my_buy_orders_list.length">
                                <td colspan="6">Empty</td>
                            </tr>
                            <tr v-for="item in my_buy_orders_list">
                                <td><strong>{{ item.price }}</strong></td>
                                <td>{{ item.quantity }}</td>
                                <td>{{ item.quantity_remain }}</td>
                                <td>{{ item.status }}</td>
                                <td>{{ item.created_at }}</td>
                                <td><a href="javascript:" style="color: red;" @click="removeOrder(item.id)"><i class="glyphicon glyphicon-remove"></i></a></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <h3>Your deals</h3>
                <div class="deals-table-container">
                    <table class="table table-hover trade-table deals-table stat-table">
                        <tr>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Type</th>
                            <th>Date</th>
                        </tr>
                        <tr v-if="!deals_list.length">
                            <td colspan="4">Empty</td>
                        </tr>
                        <tr v-for="item in deals_list">
                            <td>{{ item.quantity }}</td>
                            <td><strong>{{ item.price }}</strong></td>
                            <td>{{ (item.buyer_user_id == user_id) ? 'buy' : 'sell' }}</td>
                            <td>{{ item.created_at }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import TradingVue from 'trading-vue-js'

    const d3 = require('d3');
    export default {
        components: {TradingVue},
        data() {
            return {
                marketName: '...',
                chartColors: {
                    colorBack: '#333',
                    colorGrid: '#eee',
                    colorText: '#ccc',
                },
                width: $('#chart').width(),
                height: $('#chart').height(),
                chart_period: null,
                pair: null,
                currency1: null,
                currency2: null,
                currency1Balance: null,
                currency2Balance: null,
                currency1Data: null,
                currency2Data: null,
                currency1Volume: null,
                currency2Volume: null,
                bidSizePercent: 50,
                askSizePercent: 50,
                bidSize: 0,
                askSize: 0,
                lastPrice: null,
                ask_list: [],
                bid_list: [],
                my_sell_orders_list: [],
                my_buy_orders_list: [],
                deals_list: [],
                currency2Quantity: 0,
                dailyVolume: null,
                orderOuantity: 0,
                orderPrice: 0,
                user_id: null,
                ohlcv: [
                    [ 1551128400000, 33,  37.1, 14,  14,  196 ],
                    [ 1551132000000, 13.7, 30, 6.6,  30,  206 ],
                    [ 1551135600000, 29.9, 33, 21.3, 21.8, 74 ],
                    [ 1551139200000, 21.7, 25.9, 18, 24,  140 ],
                    [ 1551142800000, 24.1, 24.1, 24, 24.1, 29 ],
                ],
                onchart: [ // Displayed ON the chart
                    {
                        "name": coinmonkey.market.name,
                        "type": "EMA",
                        "data": [
                            [ 1551128400000, 33,  37.1, 14,  14,  196 ],
                            [ 1551132000000, 13.7, 30, 6.6,  30,  206 ],
                            [ 1551135600000, 29.9, 33, 21.3, 21.8, 74 ],
                            [ 1551139200000, 21.7, 25.9, 18, 24,  140 ],
                            [ 1551142800000, 24.1, 24.1, 24, 24.1, 29 ],
                        ],
                        "settings": {
                            "color": "#fff"
                        }
                    }
                ],
                offchart: [ // Displayed ON the chart
                    {
                        "name": coinmonkey.market.name,
                        "type": "EMA",
                        "data": [
                            [ 1551128400000, 33,  37.1, 14,  14,  196 ],
                            [ 1551132000000, 13.7, 30, 6.6,  30,  206 ],
                            [ 1551135600000, 29.9, 33, 21.3, 21.8, 74 ],
                            [ 1551139200000, 21.7, 25.9, 18, 24,  140 ],
                            [ 1551142800000, 24.1, 24.1, 24, 24.1, 29 ],
                        ],
                        "settings": {
                            "color": "#42b28a",
                            //"width": "100%"
                        }
                    }
                ]
            }
        },
        computed: {

        },
        watch: {
            chart_period: function(newVal) {
                localStorage.setItem('chart_period', newVal);
            }
        },
        methods: {
            reset: function () {
                this.currency2Quantity = 0;
                this.orderOuantity = 0;

                if (this.currency2Data.min_trade_amount) {
                    this.orderOuantity = this.currency2Data.min_trade_amount;
                }

                this.orderPrice = 0;
                if (this.currency2Data.min_price) {
                    this.orderPrice = this.currency2Data.min_price;
                }
            },
            updateSummary: function() {
                var that = this;
                
                axios.get("/api/v1/market/getSummary/" + this.pair, function(data) {
                    console.log('getSummary');
                    that.currency1Volume = parseFloat(data.currency1Volume);
                    that.currency2Volume = parseFloat(data.currency2Volume);
                    that.bidSize = parseFloat(data.bidSize);
                    that.askSize = parseFloat(data.askSize);
                    that.lastPrice = data.lastPrice;
                    that.dailyVolume = data.dailyVolume;
                });
            },
            updateBalances: function() {
                var that = this;
                
                axios.get("/api/v1/market/getBalance/" + this.pair, function(data) {
                    that.currency1Balance = data.currency1;
                    that.currency2Balance = data.currency2;
                });
            },
            updateDashboard: function() {
                var that = this;
                
                axios.get("/api/v1/market/order/getList/" + this.pair, function(data) {
                    that.ask_list = data.ask_list;
                    that.bid_list = data.bid_list;
                    that.my_sell_orders_list = data.my_sell_orders_list;
                    that.my_buy_orders_list = data.my_buy_orders_list;
                    that.deals_list = data.deals_list;
                    
                    if (!that.orderPrice && that.bid_list[0]) {
                        that.bid_list.forEach(function(i, val) {
                            that.orderPrice = that.bid_list[0].price;
                        });
                    }
                    
                    that.updateBalances();
                    that.updateSummary();
                    that.updateVolumeChart();
                });
            },
            addOrder: function(type) {
                var that = this;

                if (!confirm ("Really want to " + type + " " + that.orderOuantity + " with price " + that.orderPrice + "?")) {
                    return false;
                }

                $('.add-order').css('opacity', '0.3');

                $.post({
                    type: "POST",
                    url: "/api/v1/market/order/add",
                    data: {pair: this.pair, type: type, quantity: that.orderOuantity, price: that.orderPrice},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function (data) {
                    //condole.log(data);
                    $('.add-order').css('opacity', '1');

                    if (data.result != 'success') {
                        that.showErrors(data.errors);
                    }

                    that.reset();
                    that.updateDashboard();
                }).fail(function (data) {
                    that.showErrors(data.responseJSON.errors);
                    $('.add-order').css('opacity', '1');
                });
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
                this.currency2Quantity = Number.parseFloat(this.orderOuantity * this.orderPrice).toFixed(8);
            },
            removeOrder: function(id) {
                var that = this;
                
                $.post({
                    type: "POST",
                    url: "/api/v1/market/order/remove",
                    data: {id: id},
                    success: function (data) {
                        that.updateDashboard();
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).fail(function (data) {
                    that.showErrors(data.responseJSON.errors);
                });
            },
            isMyAsk: function(order) {
                var answer = false;
                
                this.my_sell_orders_list.forEach(function(el, i) {
                    if (el.id == order.id) {
                        answer = true;
                        return;
                    }
                });

                return answer;
            },
            isMyBid: function(order) {
                var answer = false;
                
                this.my_buy_orders_list.forEach(function(el, i) {
                    if (el.id == order.id) {
                        answer = true;
                    }
                });
                
                return answer;
            },
            setPrice: function(price, quantity) {
                this.orderPrice = price;
                this.orderOuantity = quantity;
            },
            updateVolumeChart: function() {
                var sum = this.bidSize + this.askSize;
                this.bidSizePercent = (this.bidSize*100)/sum;
                this.askSizePercent = (this.askSize*100)/sum;

                if (this.bidSizePercent < 9) {
                    this.bidSizePercent = 9;
                    this.askSizePercent = 91;
                }
                
                if (this.askSizePercent < 9) {
                    this.askSizePercent = 9;
                    this.bidSizePercent = 91;
                }
            }
        },
        mounted() {
            if (coinmonkey && coinmonkey.market) {
                this.pair = coinmonkey.market.pair.code;
                this.currency1 = coinmonkey.market.currency1.code;
                this.currency2 = coinmonkey.market.currency2.code;
                this.currency1Balance = coinmonkey.market.currency1_balance;
                this.currency2Balance = coinmonkey.market.currency2_balance;
                this.currency2Data = coinmonkey.market.currency2;
                this.currency1Data = coinmonkey.market.currency1;
            }

            let chart_period;
            if (chart_period = localStorage.getItem('chart_period')) {
                this.chart_period = chart_period;
            }

            this.width = $('body').width();
            this.marketName = this.pair;

            //this.reset();
            this.updateDashboard();
            
            setInterval(function () {
                this.updateDashboard();
            }.bind(this), 20000);
        }
    }
</script>
