<template>
    <div clas="row">
        <div class="col-md-4">
            <h3>New deposit address</h3>
            <div class="deposit-form">
                <input class="form-control" v-model="address" placeholder="" onclick="$(this).select();">
            </div>
        </div>
        <div class="col-md-8">
            <h3>Deposit history <a href="javascript:" @click="updateDepositList" class="btn btn-default"><i class="glyphicon glyphicon-repeat"></i></a></h3>
            <table class="table table-hover">
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>TxId</th>
                    <th>Quantity</th>
                    <th>Confirmations</th>
                </tr>
                <tr v-for="item in depositList">
                    <td>{{ item.id }}</td>
                    <td>{{ item.created_at }}</td>
                    <td>{{ item.tx_id }}</td>
                    <td>{{ item.quantity }}</td>
                    <td>{{ item.confirmations }}</td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                address: "...",
                currencyCode: null,
                depositList: [],
            }
        },
        methods: {
            updateDepositList: function(str) {
                var that = this;
                
                axios.get("/api/v1/balances/deposit/getList/" + this.currencyCode, function(data) {
                    that.depositList = data.list;
                });
            },
        },
        mounted() {
            if(coinmonkey && coinmonkey.deposit) {
                this.currencyCode = coinmonkey.deposit.currency;
            }

            var that = this;

            axios.get("/api/v1/balances/deposit/getAddress/" + this.currencyCode, function(data) {
                that.address = data.address;
            });
            
            this.updateDepositList();

            setInterval(function () {
                this.updateDepositList();
            }.bind(this), 20000);
        }
    }
</script>
