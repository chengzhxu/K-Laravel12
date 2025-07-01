<?php

/**
 * 下划线命名转驼峰
 * @pararm string
 *
 * @return string
 */

use Illuminate\Support\Facades\Redis;

if ( !function_exists('toCamelCase')) {
    function toCamelCase($str)
    {
        $array  = explode('_', $str);
        $result = $array[0];
        $len    = count($array);
        if ($len > 1) {
            for ($i = 1; $i < $len; $i++) {
                $result .= ucfirst($array[$i]);
            }
        }

        return $result;
    }
}

/**
 * 输出调试log
 *
 * @return string
 */
if ( !function_exists('writeDebugLog')) {
    function writeDebugLog($data, $filename): void
    {
        $log  = [
            'time' => date('Y-m-d H:i:s'),
            'data' => $data,
        ];
        $uri  = request()->path();
        $text = "[" . date('Y-m-d H:i:s') . "][{$uri}]： 位置是{$filename}" . json_encode($data);
        file_put_contents(__DIR__ . '/debug.log', $text . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
