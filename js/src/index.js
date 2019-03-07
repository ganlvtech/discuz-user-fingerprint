/*!
 * User Fingerprint Discuz! X plugin <https://github.com/ganlvtech/discuzx-user-fingerprint>
 * Copyright (C) 2018 Ganlv <https://github.com/ganlvtech>
 * License: GPL 3.0
 */

var Fingerprint2 = require('fingerprintjs2');

function send(murmur, murmur2) {
  var scriptElement = document.createElement('script');
  scriptElement.src = '/plugin.php?id=user_fingerprint&fingerprint=' + murmur + '&fingerprint2=' + murmur2;
  document.getElementsByTagName('head')[0].appendChild(scriptElement);
}

function fingerprintReport() {
  Fingerprint2.get(function (components) {
    var murmur = Fingerprint2.x64hash128(components.map(function (pair) {
      return pair.value;
    }).join(), 31);
    var murmur2 = Fingerprint2.x64hash128(components.map(function (pair) {
      if (pair.key === 'canvas' || pair.key === 'webgl' || pair.key === 'webglVendorAndRenderer') {
        return '';
      }
      return pair.value;
    }).join(), 31);
    send(murmur, murmur2);
  });
}

if (typeof discuz_uid !== 'undefined' && discuz_uid > 0) {
  if (window.requestIdleCallback) {
    requestIdleCallback(function () {
      setTimeout(fingerprintReport, 1000);
    });
  } else {
    setTimeout(fingerprintReport, 3000);
  }
}
