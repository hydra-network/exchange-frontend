<template>
    <div v-if="order">
        <ul>
            <li v-bind:class="{inactive: (step>1)}">
                1. Enter your {{ order.currency2.code }} address<br />
                <span v-if="step==1"><input v-model="currency1_address" class="form-control" /> <button @click="activate()">Next</button></span>
                <span v-if="step>1">✅</span>
            </li>
            <li v-bind:class="{inactive: (step<=1 | step>2)}">
                2. Send {{ order.amount_for_buyer }} {{ order.currency1.code }} <span v-if="!escrowOrder">to the coinmonkey address</span> <span v-if="step>2">✅</span>
                <span v-if="escrowOrder">
                    <br />
                    <br />
                    Address: <input type="text" :value="escrowOrder.deposit_address" onclick="this.select();" style="width: 286px;" />
                </span>
                <table  v-if="escrowOrder" class="table" border="1" width="100%" cellspan="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th width="300">Transaction ID</th>
                            <th width="20">Confirmations</th>
                        </tr>
                        <tr>
                            <td>
                                <img v-if="!escrowOrder.deposit_date" src="http://coinmonkey.io/images/loading.gif" width="20" />
                                <span v-if="escrowOrder.deposit_tx_id">{{escrowOrder.deposit_date}}</span>
                            </td>
                            <td>
                                {{escrowOrder.deposit_amount}}
                            </td>
                            <td>
                                <img v-if="!escrowOrder.deposit_tx_id" src="http://coinmonkey.io/images/loading.gif" width="20"  />
                                <small v-if="escrowOrder.deposit_tx_id">{{escrowOrder.deposit_tx_id}}</small>
                            </td>
                            <td>
                                <span v-if="escrowOrder.deposit_confirmations_count">{{escrowOrder.deposit_confirmations_count}}</span><img v-if="!escrowOrder.deposit_confirmations_count" src="http://coinmonkey.io/images/loading.gif" width="20" />/1
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="alert alert-dangerous" v-if="escrowOrder">
                    You have {{timer}} minutes for send {{ order.amount_for_buyer }} {{ order.currency1.code }}. All inaccurate amounts will be ignored. After {{timer}} minutes your booking will be canceled.
                </div>
            </li>
            <li v-bind:class="{inactive: (step<3)}">3. Check your balance at <span v-if="!currency1_address">{{ order.currency2.code }} your address</span><span v-if="currency1_address"><strong>{{currency1_address}}</strong></span></li>
        </ul>

        <p>Faced with a trouble? <a href="mailto:support@coinmonkey.io">support@coinmonkey.io</a></p>
    </div>
</template>

<script>
    export default {
        props: {
            order_id: String,
            escrow_order_id: String,
        },
        data() {
            return {
                step: 1,
                order: null,
                escrowOrder: null,
                currency1_address: null,
                currency2_address: null,
                timer: 45,
            }
        },
        methods: {
            getOrder: function() {
                var that = this;
                axios.get("/api/v1/escrow/getOrder/" + this.order_id, function(data) {
                    that.order = data.data;
                });
            },
            getEscrowOrder: function() {
                var that = this;
                axios.get("/api/v1/escrow/getEscrowOrder/" + this.escrow_order_id, function(data) {
                    that.escrowOrder = data.data;
                    that.currency1_address = data.data.user_address;

                    if (that.escrowOrder.status == 'done') {
                        that.step = 3;
                    }
                });
            },
            activate: function() {
                var that = this;
                $.post({
                    type: "POST",
                    url: "/api/v1/escrow/activateOrder/" + this.order_id,
                    data: {address: this.currency1_address},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function(data) {
                    document.location = '/e/view/' + data.data.hash;
                }).fail(function(data) {
                    alert( data.responseJSON.error );
                })
            }
        },
        mounted() {
            this.getOrder();

            if (this.escrow_order_id) {
                this.step = 2;
                this.getEscrowOrder();

                setInterval(function () {
                    this.getEscrowOrder();
                }.bind(this), 20000);
            }
        }
    }
</script>
<style>
    .content {
        width: 500px;
    }
    ul {
        text-align: left;
        padding: 0px;
        margin: 1px;
    }
    ul li {
        list-style: none;
        margin-top: 15px;
    }
    .alert {
        font-size: 12px;
        padding: 6px;
        background-color: #f3f378;
        color: black;
    }
    table {
        margin-top: 10px;
    }
    .inactive {
        opacity: 0.3;
    }
</style>