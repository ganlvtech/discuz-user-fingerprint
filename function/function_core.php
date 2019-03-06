<?php

namespace Ganlv\UserFingerprint;

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

function _($str)
{
    return lang('plugin/user_fingerprint', $str);
}

function config($key = null, $default = null)
{
    global $_G;
    if (is_null($key)) {
        return $_G['cache']['plugin']['user_fingerprint'];
    } elseif (!empty($_G['cache']['plugin']['user_fingerprint'][$key])) {
        return $_G['cache']['plugin']['user_fingerprint'][$key];
    } else {
        return $default;
    }
}

/**
 * @todo Remove unused function
 *
 * @return bool
 */
function is_utf_8()
{
    return strtolower(CHARSET) === 'utf-8';
}

/**
 * @todo Remove unused function
 *
 * @param $data
 *
 * @return false|string
 */
function convert_to_utf_8($data)
{
    if (!is_utf_8()) {
        if (is_string($data)) {
            $data = iconv(CHARSET, 'utf-8', $data);
        } elseif (is_array($data)) {
            foreach ($data as &$item) {
                $item = convert_to_utf_8($item);
            }
        }
    }
    return $data;
}

/**
 * @todo Remove unused function
 *
 * @param $data
 *
 * @return false|string
 */
function convert_from_utf_8($data)
{
    if (!is_utf_8()) {
        if (is_string($data)) {
            $data = iconv('utf-8', CHARSET . '//IGNORE', $data);
        } elseif (is_array($data)) {
            foreach ($data as &$item) {
                $item = convert_from_utf_8($item);
            }
        }
    }
    return $data;
}

/**
 * @todo Remove unused function
 *
 * @param $data
 *
 * @return false|string
 */
function json_encode_with_charset($data)
{
    return json_encode(convert_to_utf_8($data));
}

/**
 * @todo Remove unused function
 *
 * @param $data
 *
 * @return false|string
 */
function json_decode_with_charset($data)
{
    return convert_from_utf_8(json_encode($data));
}
