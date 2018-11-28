<?php
if (php_sapi_name() !== 'cli') {
    exit('Access Denied');
}
define('FINGERPRINT2_MIN_JS_URL', 'https://cdn.jsdelivr.net/npm/fingerprintjs2@2.0.3/dist/fingerprint2.min.js');
define('FINGERPRINT2_MIN_JS_PATH', dirname(__DIR__) . '/js/fingerprint2.min.js');
define('SCRIPT_JS_PATH', dirname(__DIR__) . '/js/script.js');
define('OUTPUT_JS_PATH', dirname(__DIR__) . '/js/bundle.min.js');

if (!file_exists(FINGERPRINT2_MIN_JS_PATH)) {
    $fingerprint2_js = file_get_contents(FINGERPRINT2_MIN_JS_URL);
    file_put_contents(FINGERPRINT2_MIN_JS_PATH, $fingerprint2_js);
} else {
    $fingerprint2_js = file_get_contents(FINGERPRINT2_MIN_JS_PATH);
}
$script = file_get_contents(SCRIPT_JS_PATH);
$script = minify_js($script);
$output = $fingerprint2_js . "\n" . $script;
file_put_contents(OUTPUT_JS_PATH, $output);

function minify_js($script)
{
    $vars = [
        'send',
        'murmur',
        'scriptElement',
        'fingerprintReport',
        'components',
        'pair',
    ];
    $replaces = [];
    for ($i = 0; $i < count($vars); ++$i) {
        $replaces[] = chr(ord('a') + $i);
    }
    $script = str_replace($vars, $replaces, $script);

    $credits = '';
    if (1 === preg_match('@/\*!.*?\*/\r?\n@s', $script, $matches)) {
        $credits = $matches[0];
        $script = str_replace($credits, '[CREDITS]', $script);
    }

    $script = preg_replace('/([^A-Za-z0-9])\s+/', '$1', $script);
    $script = preg_replace('/\s+([^A-Za-z0-9])/', '$1', $script);

    $script = str_replace('[CREDITS]', $credits, $script);
    return $script;
}
