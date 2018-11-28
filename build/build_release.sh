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
cp    discuz_plugin_user_fingerprint.xml  dist/
cp    install.php                         dist/
cp    uninstall.php                       dist/
cp    user_fingerprint.class.php          dist/
cp    user_fingerprint.inc.php            dist/

zip dist.zip -r dist
rm -rf dist/

popd
