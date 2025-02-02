<?php

declare(strict_types = 1);

namespace Weiran\Framework\Helper;

use Request;

/**
 * 环境获取
 */
class EnvHelper
{
    /**
     * 返回 IP 信息
     * @return string 返回IP
     */
    public static function ip(): string
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
        $_SERVER['REMOTE_ADDR']          = $_SERVER['REMOTE_ADDR'] ?? '';
        $_SERVER['HTTP_CLIENT_IP']       = $_SERVER['HTTP_CLIENT_IP'] ?? '';

        if ($_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['REMOTE_ADDR']) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (strpos($ip, ',') !== false) {
                $tmp = explode(',', $ip);
                $ip  = trim(reset($tmp));
            }
            if (UtilHelper::isIp($ip)) {
                return $ip;
            }
        }
        if (UtilHelper::isIp($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (UtilHelper::isIp($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return 'unknown';
    }

    /**
     * 当前执行的脚本文件的名称
     * @return string 当前文件的名称
     */
    public static function self(): string
    {
        return $_SERVER['PHP_SELF']
            ?? $_SERVER['SCRIPT_NAME']
            ?? $_SERVER['ORIG_PATH_INFO'];
    }

    /**
     * 来源地址
     * @return string 来源地址
     */
    public static function referer(): string
    {
        return $_SERVER['HTTP_REFERER'] ?? '';
    }

    /**
     * 返回服务器的名称
     * @return string 返回服务器名称
     */
    public static function domain(): string
    {
        return $_SERVER['SERVER_NAME'] ?? '';
    }

    /**
     * @return string 协议名称
     */
    public static function scheme(): string
    {
        if (!isset($_SERVER['SERVER_PORT'])) {
            return 'http://';
        }

        return (string) $_SERVER['SERVER_PORT'] === '443' ? 'https://' : 'http://';
    }

    /**
     * @return string 返回端口号
     */
    public static function port(): string
    {
        if (!isset($_SERVER['SERVER_PORT'])) {
            return '';
        }

        return (int) $_SERVER['SERVER_PORT'] === '80' ? '' : ':' . $_SERVER['SERVER_PORT'];
    }

    /**
     * @return string 完整的地址
     */
    public static function uri(): string
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
        }
        else {
            $uri = $_SERVER['PHP_SELF'];
            if (isset($_SERVER['argv'])) {
                if (isset($_SERVER['argv'][0])) {
                    $uri .= '?' . $_SERVER['argv'][0];
                }
            }
            else {
                $uri .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
        $uri = StrHelper::htmlSpecialChars($uri);

        return self::scheme() . self::host() . (strpos(self::host(), ':') === false ? self::port() : '') . $uri;
    }

    /**
     * 获取主机
     * @return string
     */
    public static function host(): string
    {
        return $_SERVER['HTTP_HOST'] ?? '';
    }

    /**
     * @return string   没有查询的完整的URL地址, 基于当前页面
     */
    public static function nqUrl(): string
    {
        return self::scheme() . self::host() . (strpos(self::host(), ':') === false ? self::port() : '') . self::self();
    }

    /**
     * 请求的unix 时间戳
     * @return int
     */
    public static function time(): int
    {
        return (int) $_SERVER['REQUEST_TIME'];
    }

    /**
     * 浏览器头部
     * @return string
     * @see        Request::userAgent()
     * @deprecated 4.1
     */
    public static function agent(): string
    {
        return Request::userAgent();
    }

    /**
     * 是否是代理
     * @return bool
     */
    public static function isProxy(): bool
    {
        return
            (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ||
            isset($_SERVER['HTTP_VIA']) ||
            isset($_SERVER['HTTP_PROXY_CONNECTION']) ||
            isset($_SERVER['HTTP_USER_AGENT_VIA']) ||
            isset($_SERVER['HTTP_CACHE_INFO']);
    }

    /**
     * 是否win 服务器
     * @return bool
     */
    public static function isWindows(): bool
    {
        if ('DARWIN' === strtoupper(PHP_OS)) {
            return false;
        }

        return stripos(PHP_OS, 'WIN') !== false;
    }

    /**
     * 获取客户端OS
     * @return string
     */
    public static function os(): string
    {
        $agent = Request::userAgent();
        if (false !== stripos($agent, 'win')) {
            $os = 'windows';
        }
        elseif (false !== stripos($agent, 'linux')) {
            $os = 'linux';
        }
        elseif (false !== stripos($agent, 'unix')) {
            $os = 'unix';
        }
        elseif (false !== stripos($agent, 'mac')) {
            $os = 'Macintosh';
        }
        else {
            $os = 'other';
        }

        return $os;
    }

    /**
     * 最大上传的文件大小
     * @param bool $format 是否格式化
     * @return mixed|string
     */
    public static function maxUploadSize(bool $format = true)
    {
        $sizeMax  = UtilHelper::sizeToBytes(ini_get('upload_max_filesize'));
        $sizePost = UtilHelper::sizeToBytes(ini_get('post_max_size'));

        $min = min($sizeMax, $sizePost);
        if ($format) {
            return UtilHelper::formatBytes($min);
        }

        return $min;
    }

    /**
     * IP 是否是内网地址
     * @param string $ip
     * @return bool
     */
    public static function isInternalIp(string $ip): bool
    {
        $ip = ip2long($ip);
        if (!$ip) {
            return false;
        }
        $net_local = ip2long('127.255.255.255') >> 24; //127.x.x.x
        $net_a     = ip2long('10.255.255.255') >> 24;  //A类网预留ip的网络地址
        $net_b     = ip2long('172.31.255.255') >> 20;  //B类网预留ip的网络地址
        $net_c     = ip2long('192.168.255.255') >> 16; //C类网预留ip的网络地址
        return
            $ip >> 24 === $net_local ||
            $ip >> 24 === $net_a ||
            $ip >> 20 === $net_b ||
            $ip >> 16 === $net_c;
    }
}
