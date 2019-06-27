<template>
    <table class="table table-hover">
        <tbody>
            <tr>
                <th>Market</th>
                <th>Deal</th>
                <th>Price</th>
                <th>Cost</th>
                <th>Date</th>
                <th>Status</th>
                <th>Link</th>
            </tr>

            <tr v-if="!ordersList">
                <td colspan="6">Empty</td>
            </tr>

            <tr v-for="order in ordersList">
                <td><a :href="route('app.market.pair', {code: order.pair.code})">{{ order.pair.code }}</a></td>
                <td>{{ order.deal_name }} </td>
                <td>{{ order.price }}</td>
                <td><strong>{{ order.cost_remain }}</strong> {{ order.currency1o.name }}</td>
                <td>{{ order.created_at }}</td>
                <td>{{ order.status }}</td>
                <td style="min-width: 360px; padding: 5px;">
                    <div v-if="['escrow_ws'].includes(order.status)">
                        <div class="row">
                            <div class="col-sm-8">
                                <input type="text" name="link" :value="route('guest.escrow.buy', {id: order.id})" onclick="$(this).select();" class="form-control"/>
                            </div>
                            <div class="col-sm-4"><button @click="cancel(order)" class="btn btn-danger">Cancel</button></div>
                        </div>
                    </div>
                    <div v-if="['new', 'active', 'broken'].includes(order.status)">
                        <button @click="getLink(order)" class="btn btn-success">Get link</button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
    export default {
        data() {
            return {
                ordersList: [],
            }
        },
        methods: {
            updateOrdersList: function(str) {
                var that = this;
                
                axios.get("/api/v1/market/order/getMyList", function(data) {
                    that.ordersList = data.data;
                });
            },
            route: function(name, params) {
                return route(name, params);
            },
            getLink: function(order) {
                var that = this;
                $.post({
                    type: "POST",
                    url: "/api/v1/escrow/generateLink",
                    data: {id: order.id},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function(data) {
                    order = data.data;
                    that.updateOrdersList();
                });
            },
            cancel: function(order) {
                var that = this;
                $.post({
                    type: "POST",
                    url: "/api/v1/escrow/cancel",
                    data: {id: order.id},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function(data) {
                    order = data.data;
                    that.updateOrdersList();
                });
            },
        },
        mounted() {
            this.updateOrdersList();

            setInterval(function () {
                this.updateOrdersList();
            }.bind(this), 20000);
        }
    }
</script>
