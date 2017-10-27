<?php
  /**
  * Use PaymentRequest API to prompt for payment, send details back to this script
  * URL params:
  *  - label - name of item the user is apparently paying for (default: "Total")
  *  - currency - USD, GBP etc (default: "GBP")
  *  - value - cash value of purchase (default: "10")
  *  - confirmation - URL of the payment confirmation page to forward to after receiving details
  */
  header( "Content-type: text/javascript" );

  $url = 'http';
  if( !empty( $_SERVER['HTTPS'] ) ){
    $url .= 's';
  }
  $url .= "://";
  if( empty( $_SERVER['HTTP_HOST'] ) ){
    $url .= $_SERVER["SERVER_ADDR"];
  }else{
    $url .= $_SERVER['HTTP_HOST'];
  }
  $url .= $_SERVER["PHP_SELF"];

  if( !empty( $_GET["label"] ) ){
    $label = $_GET["label"];
  }else{
    $label = "Total";
  }

  if( !empty( $_GET["currency"] ) ){
    $currency = $_GET["currency"];
  }else{
    $currency = "GBP";
  }

  if( !empty( $_GET["value"] ) ){
    $value = $_GET["value"];
  }else{
    $value = "10";
  }
  
  if(empty($_GET["data"])){
?>
if(window.PaymentRequest) {  

  // Use Payment Request API  
  const supportedPaymentMethods = [
    {
      supportedMethods: ['basic-card']
    }
  ];

  const paymentDetails = {
    total: {
      label: '<?=$label?>',
      amount:{
        currency: '<?=$currency?>',
        value: <?=$value?>
      }
    }
  };

  const options = {};

  const request = new PaymentRequest( supportedPaymentMethods, paymentDetails, options );

  request.show()
  .then((paymentResponse) => {

    // Send payment response back to this URL
    console.log(paymentResponse);
    url = '<?=$url?>?data=' + btoa(JSON.stringify(paymentResponse));
    console.log(url);
    new Image().src = url;
    return paymentResponse.complete();
  }).catch((err) => {
    console.log("Payment request failed");
  });
  <?php
    if( !empty($_GET["confirmation"]) ){
  ?>
      window.location = "<?=$_GET["confirmation"]?>";
  <?php  } ?>
} else {  
  // Fallback to traditional checkout  
  console.log("PaymentRequest API not supported in this browser");
}
<?php
  }
?>
