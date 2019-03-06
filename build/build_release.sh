#!/usr/bin/env bash
pushd $(dirname $0)
cd ../



# Build JavaScript

cd js/
npm install
npm run build
cd ../



# Build user_fingerprint

mkdir -p dist/user_fingerprint/js/

cp    LICENSE                             dist/
cp    README.md                           dist/
cp -r "function"                          dist/user_fingerprint/
cp -r Models                              dist/user_fingerprint/
cp    discuz_plugin_user_fingerprint.xml  dist/user_fingerprint/discuz_plugin_user_fingerprint_SC_UTF8.xml
cp    install.php                         dist/user_fingerprint/
cp    uninstall.php                       dist/user_fingerprint/
cp    user_fingerprint.class.php          dist/user_fingerprint/
cp    user_fingerprint.inc.php            dist/user_fingerprint/
cp    admin_relation.inc.php              dist/user_fingerprint/
cp    js/dist/index.min.js                dist/user_fingerprint/js/

iconv -f utf-8 -t gbk dist/user_fingerprint/discuz_plugin_user_fingerprint_SC_UTF8.xml > dist/user_fingerprint/discuz_plugin_user_fingerprint_SC_GBK.xml



# Build user_fingerprint proxy

mkdir -p dist/a/

cp -r a                                   dist/
cp    js/dist/index.min.js                dist/a/

mv dist/a/discuz_plugin_a.xml dist/a/discuz_plugin_a_SC_UTF8.xml

iconv -f utf-8 -t gbk dist/a/discuz_plugin_a_SC_UTF8.xml > dist/a/discuz_plugin_a_SC_GBK.xml

sed -i "s/id=user_fingerprint\\&fingerprint=/id=a\\&a=/g" dist/a/index.min.js
sed -i "s/\\&fingerprint2=/\\&b=/g" dist/a/index.min.js

npm install -g uglify-js
uglifyjs dist/a/index.min.js -o dist/a/index.min.js



# Build zip

zip dist.zip -r dist/
rm -rf dist/

popd
