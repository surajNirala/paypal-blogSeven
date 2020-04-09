<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PayPal</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  <script src="https://www.paypalobjects.com/api/checkout.js"></script>
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <br>
        <br>
        <h1>Cleint Side</h1>
        <div id="paypal-button"></div>
      </div>
      <div class="col-md-6">
        <br>
        <h1>Server Side</h1>
        <form action="{{ route('create-payment')}}" method="post">
        @csrf
        <div class="form-group">  
          <input type="number" name="amount" placeholder="Enter your Amount" class="form-control" id="amount">
          <br>
          <button type="submit" class="form-control btn btn-primary">Pay Now</button>
        </div>
        </form>
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
        @if(session()->has('list'))
            <div class="alert alert-primary">
                {{ session()->get('list') }}
            </div>
        @endif
      </div>
    </div>
  </div>
</body>
</html>
<script>
  paypal.Button.render({
    // Configure environment
    env: 'sandbox',
    client: {
      sandbox:'AUrbMVLikWi9g_yzC9E5TDOEUl-sEii57--7YXNm8ahQJtxqUp8l6fgHosPTHOpRo5UmnIFIWpsEtu_7', // bussness
      production: 'demo_production_client_id'
    },
    // ************************* PERSONAL **********************************************************
    // ========================================== (1) ===============================================
    // sandbox A/C :- sb-vdold1393632@personal.example.com
    // password    :- 5c5-Z"c9
    // ========================================== (2) ===============================================
    // sandbox A/C :- buyerUSD@business.example.com
    // password    :- suraj@1234

    // ************************* BUSINESS **********************************************************
    // ======================= (1) =================================================
    // secret      :- ENkxEffD638fJPTUL6uJYT4Ewkrn0yRYiwX1XyXH6Z9URWTqKNmzRbmUbHF34ZuRsKWE_fSvErMeS4xT
    // client ID   :- AYX7m0nAIg0S8HulFanYfVBeEa-el4-OoodWx1PFidvxMzJmTfHhLtp2YDr5bkSQs_1cjm12IWw3hDpi
    // sandbox A/C :- sb-yo36h1389592@business.example.com
    // password    :- K'H3iZ1!
    // ==================== (2) ======================================
    // secret      :- EGDYMflQJfVvt1HqN3OAWIDr0Z_x1R1pHMoekp-bi1xt-bCw4JAr_LLUpeXzh-qXPhHse_RJTw8qTh1w
    // client ID   :- AUrbMVLikWi9g_yzC9E5TDOEUl-sEii57--7YXNm8ahQJtxqUp8l6fgHosPTHOpRo5UmnIFIWpsEtu_7
    // sandbox A/C :- sellerUSD@business.example.com
    // password    :- suraj@1234
    locale: 'en_US',
    style: {
      size: 'large',
      color: 'gold',
      shape: 'pill',
    },
    // Enable Pay Now checkout flow (optional)
    commit: true,
    // Set up a payment
    payment: function(data, actions) {
      return actions.payment.create({
        redirect_urls:{
          return_url:"http://localhost:8000/execute-payment"
        },
        transactions: [{
          amount: {
            total: '8',
            currency: 'USD'
          }
        }]
      });
    },
    // Execute the payment
    onAuthorize: function(data, actions) {
      console.log(data)
      return actions.redirect();
      /* return actions.payment.execute().then(function() {
        // Show a confirmation message to the buyer
        window.alert('Thank you for your purchasewwww!');
      }); */
    }
  }, '#paypal-button');

</script>