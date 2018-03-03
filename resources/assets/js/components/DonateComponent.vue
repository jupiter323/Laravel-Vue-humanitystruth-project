<template>
<div class="w3-container">
    <div class="w3-row">
        <h3>Donation Frequency</h3>
        <div class="w3-row">
            <div class="w3-col s6 m3 l2 w3-padding">
                <button class="w3-button w3-block w3-border w3-round-large" v-bind:class="[monthlyMethod ? 'w3-white' : 'w3-blue', '']" v-on:click="onceClick">Once</button>
            </div>
            <div class="w3-col s6 m3 l2 w3-padding">
                <button class="w3-button w3-block w3-border w3-round-large" v-bind:class="[monthlyMethod ? 'w3-blue' : 'w3-white', '']" v-on:click="monthlyClick">Monthly</button>
            </div>
        </div>
    </div>
    <div class="w3-row">
        <h3>How much would you like to donate?</h3>
        <div class="w3-half w3-padding">
            <div class="w3-bar">
                <button class="w3-button  w3-border w3-round-large w3-margin-top" v-bind:class="[moneyUnit==1 ? 'w3-blue' : 'w3-white', '']" v-on:click="priceClick(1)">$1,000</button>
                <button class="w3-button  w3-border w3-round-large w3-margin-top" v-bind:class="[moneyUnit==2 ? 'w3-blue' : 'w3-white', '']" v-on:click="priceClick(2)">$500</button>
                <button class="w3-button  w3-border w3-round-large w3-margin-top" v-bind:class="[moneyUnit==3 ? 'w3-blue' : 'w3-white', '']" v-on:click="priceClick(3)">$250</button>
                <button class="w3-button  w3-border w3-round-large w3-margin-top" v-bind:class="[moneyUnit==4 ? 'w3-blue' : 'w3-white', '']" v-on:click="priceClick(4)">$100</button>
                <button class="w3-button  w3-border w3-round-large w3-margin-top" v-bind:class="[moneyUnit==5 ? 'w3-blue' : 'w3-white', '']" v-on:click="priceClick(5)">$50</button>
                <button class="w3-button  w3-border w3-round-large w3-margin-top" v-bind:class="[moneyUnit==6 ? 'w3-blue' : 'w3-white', '']" v-on:click="priceClick(6)">$25</button>
                <button class="w3-button  w3-border w3-round-large w3-margin-top" v-bind:class="[moneyUnit==7 ? 'w3-blue' : 'w3-white', '']" v-on:click="priceClick(7)">$10</button>
            </div>
        </div>
        <div class="w3-half w3-padding">
            <div class="w3-padding w3-light-gray w3-left w3-panel">Custom Amount: </div>
            <input class="w3-margin w3-input w3-light-gray w3-border" style="width:200px;" type="number" v-model="price">
        </div>

    </div>

    <div class="w3-row w3-padding">
        <div><label><input type="checkbox" class="w3-check w3-margin-right">Keep me informed about breaking news in the fight for a free press!</label></div>
    </div>

    <div class="w3-row w3-padding w3-margin">
        <button class="w3-button w3-red w3-border w3-round-large" v-on:click="onPaymentModal" >Donate Now</button>
    </div>
    


    <div id="cardorcoinId" class="w3-modal">
        <div class="w3-container w3-card-4 w3-round-large w3-margin w3-white">
            <div class="w3-container">
                <h2 class="w3-center w3-black">
                    <div class="w3-panel w3-center">
                        <span onclick="document.getElementById('cardorcoinId').style.display='none'" class="w3-button w3-display-topright">&times;</span>
                        <strong>Donation Method</strong>
                    </div>
                </h2>
            </div>
            <div class="w3-container">
                <div class="w3-hide">
                    <form method="POST" action="https://www.paypal.com/cgi-bin/webscr" role="form" id="paypal_form">
                        <div class="form-group">
                            <input type="hidden" name="cmd" value="_donations">
                            <input type="hidden" name="business" value="admin@humanitystruth.com">
                            <input type="hidden" name="amount" v-model="price" class="pure-number">
                            <input type="hidden" name="return" value="https://humanitystruth.com/donate">
                            <input type="hidden" name="cancel_return" value="https://humanitystruth.com/donate">
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-info" value="Proceed to PayPal">
                        </div>
                    </form>
                </div>
                <div class="w3-panel">
                    <button class="w3-button w3-blue w3-border w3-round-large" v-on:click="onPaypal">Donate with Paypal Now</button>
                </div>
            </div>
            <div class="w3-container">
                <div class="w3-col s12 m8 l9 w3-padding w3-panel" style="min-height: 160px;">
                    <p title="Bitcoin is a decentralized and anonymous digital currency. Bitcoins are not subject to central regulations and are still gaining value. To learn more about Bitcoin, visit https://bitcoin.org"><b>Bitcoin</b></p>
                    <p class="color-red" style="overflow-y: auto;">1HB5XMLmzFVj8ALj6mfBsbifRoD4miY36v</p>
                </div>
                <div class="w3-col s12 m4 l3 w3-padding w3-panel">
                    <img id='barcode_bitcoin' src="https://api.qrserver.com/v1/create-qr-code/?data=1HB5XMLmzFVj8ALj6mfBsbifRoD4miY36v&amp;size=150x150" alt="" title="Bitcoin" width="150" height="150" />
                </div>
                
            </div>
            <div class="w3-container">
                <div class="w3-panel w3-section w3-pale-green">
                        <p class="section-title">Monero</p>
                </div>
                <div>
                    <div class="w3-col s12 m8 l9 w3-padding w3-panel"  style="min-height: 160px;">
                        <p><b>Moreno</b> is an open-source cryptocurrency that focuses on privacy, decentralisation and scalability. Unlike many cryptocurrencies that are derivatives of Bitcoin, Monero is based on the CryptoNote protocol and possesses significant algorithmic differences relating to blockchain obfuscation.</p>
                        <p><b>Monero users may choose to donate to us at:</b></p>
                        <p class="color-red" style="overflow-y: auto;">47BnvD18P456f4KJUBKPS3Rqa97LrTaeqJ5NFYmjQM6nVoz6TBv4rJ24GZk883BNo22fAKbr8BSuTjhQC6K7DsSJFa8SHDs</p>
                    </div>   
                    <div class="w3-col s12 m4 l3 w3-padding w3-panel">
                        <img id='barcode_monero' src="https://api.qrserver.com/v1/create-qr-code/?data=47BnvD18P456f4KJUBKPS3Rqa97LrTaeqJ5NFYmjQM6nVoz6TBv4rJ24GZk883BNo22fAKbr8BSuTjhQC6K7DsSJFa8SHDs&amp;size=150x150" alt="" title="Bitcoin" width="150" height="150" />
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
    


    
 
</template>

<script>
    var prices = [0,1000,500,250,100,50,25,10];
    var pay_types = ["once", "Monthly"];
    var data = {
        price: 0,
        monthlyMethod: 0,
        moneyUnit: 0,
        pay_type: 'oncetime'
        
    };
    export default {

        data: function () {
            return data;
           
        },
        mounted() {
            this.priceClick(6);
        },
        methods: {
            onceClick: function (event) {
                this.monthlyMethod = 0;
            },
            monthlyClick: function (event) {
                this.monthlyMethod = 1;
            },
            priceClick: function (val) {
                this.moneyUnit = val;
                this.price = prices[this.moneyUnit];
            },
            onPaymentModal: function () {
                if(this.price > 0) {
                    document.getElementById('cardorcoinId').style.display='block';
                }
                
            },
            onPaypal: function() {
                this.pay_type = pay_types[this.monthlyMethod];
                $("#paypal_form").submit();
            }
        }
    }
</script>
