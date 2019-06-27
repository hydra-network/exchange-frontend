<template>
    <div clas="row">
        <div class="col-md-4">
            <h3>New withdrawal</h3>
            <div class="deposit-form">
                <input class="form-control" v-model="address" required v-on:keyup.enter="orderWithdrawal" placeholder="Your address" required>
                <hr />
                <div class="input-group">
                    <input class="form-control" v-on:keyup.enter="orderWithdrawal" v-model="amount" placeholder="Amount" required>
                    <span class="input-group-btn"> <button class="btn btn-danger" type="button" @click="orderWithdrawal">Create withdrawal order</button> </span>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <h3>Withdrawal history <a href="javascript:" @click="updateWithdrawalList" class="btn btn-default"><i class="glyphicon glyphicon-repeat"></i></a></h3>
            <table class="table table-hover">
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>TxId</th>
                    <th>Quantity</th>
                    <th>Confirmations</th>
                    <th>Status</th>
                </tr>
                <tr v-for="item in withdrawalList">
                    <td>{{ item.id }}</td>
                    <td>{{ item.created_at }}</td>
                    <td>{{ item.tx_id }}</td>
                    <td>{{ item.quantity }}</td>
                    <td>{{ item.confirmations }}</td>
                    <td>{{ item.status }}</td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                address: null,
                amount: null,
                currencyCode: null,
                withdrawalList: [],
            }
        },
        methods: {
            orderWithdrawal: function() {
                var that = this;

                $.post({
                    type: "POST",
                    url: "/api/v1/balances/withdrawal/order",
                    data: {address: this.address, amount: this.amount, code: this.currencyCode},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                }).done(function (data) {
                    console.log(data);
                    if (data.error) {
                        alert(data.error);
                    } else {
                        that.address = null;
                        that.amount = null;
                        that.updateWithdrawalList();
                    }
                }).fail(function (data) {
                    alert(data.responseJSON.errors);
                });
            },
            updateWithdrawalList: function() {
                var that = this;

                axios.get("/api/v1/balances/withdrawal/getList/" + this.currencyCode, function(data) {
                    that.withdrawalList = data.list;
                });
            }
        },
        mounted() {
            if(coinmonkey && coinmonkey.deposit) {
                this.currencyCode = coinmonkey.deposit.currency;
            }

            var that = this;

            this.updateWithdrawalList();
        }
    }
</script>
