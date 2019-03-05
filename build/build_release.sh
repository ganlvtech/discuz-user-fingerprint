#!/usr/bin/env bash
pushd $(dirname $0)
cd ../

mkdir dist/
mkdir dist/js/

cp    LICENSE                             dist/
cp    README.md                           dist/
cp -r Libraries                           dist/
cp -r Models                              dist/
cp    js/bundle.min.js                    dist/js/
cp    admin_relation.inc.php              dist/
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
