#!/usr/bin/env bash
pushd $(dirname $0)
cd ../js/

if [[ ! -f fingerprint2.min.js ]]; then
    wget https://cdn.jsdelivr.net/npm/fingerprintjs2@2.0.3/dist/fingerprint2.min.js
fi
uglifyjs --compress --mangle --comments "/^!/" -- script.js > script.min.js
cat fingerprint2.min.js script.min.js > bundle.min.js

if [[ ! -f echarts.min.js ]]; then
    wget https://cdn.jsdelivr.net/npm/echarts@4.2.0-rc.2/dist/echarts.min.js
fi
if [[ ! -f axios.min.js ]]; then
    wget https://cdn.jsdelivr.net/npm/axios@0.18.0/dist/axios.min.js
fi
uglifyjs --compress --mangle --comments "/^!/" -- admin_chart.js > admin_chart.min.js
cat axios.min.js echarts.min.js admin_chart.min.js > admin.min.js

popd
