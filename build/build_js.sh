#!/usr/bin/env bash
pushd $(dirname $0)
cd ../js/

if [[ ! -f fingerprint2.min.js ]]; then
    wget https://cdn.jsdelivr.net/npm/fingerprintjs2@2.0.3/dist/fingerprint2.min.js
fi
cp fingerprint2.min.js bundle.min.js
uglifyjs --compress --mangle --comments "/^!/" -- script.js >> bundle.min.js

popd
