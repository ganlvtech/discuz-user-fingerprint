#!/usr/bin/env bash
pushd $(dirname $0)
cd ../

mkdir dist/
mkdir dist/js/

cp -r Models                              dist/
cp -r "function"                          dist/
cp    js/bundle.min.js                    dist/js/
cp    README.md                           dist/
cp    admin_fingerprint.inc.php           dist/
cp    admin_sid.inc.php                   dist/
cp    admin_user.inc.php                  dist/
cp    discuz_plugin_user_fingerprint.xml  dist/discuz_plugin_user_fingerprint_SC_UTF8.xml
cp    install.php                         dist/
cp    uninstall.php                       dist/
cp    user_fingerprint.class.php          dist/
cp    user_fingerprint.inc.php            dist/

iconv -f utf-8 -t gbk dist/discuz_plugin_user_fingerprint_SC_UTF8.xml > dist/discuz_plugin_user_fingerprint_SC_GBK.xml

mv dist user_fingerprint
zip user_fingerprint.zip -r user_fingerprint
rm -rf user_fingerprint/

popd
