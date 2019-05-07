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

    Injection
      - Basic polyglot / inline script
      - 0xsobky - Ultimate XSS Polyglot
      - String variable escape
      - img element onerror
      - SVG element
      - Element onclick

    Obfuscation
      - base64 (btoa())
      - reverse
      - String.fromCharCode()

  */
?>
