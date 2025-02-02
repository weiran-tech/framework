<?php

declare(strict_types = 1);

namespace Weiran\Framework\Helper;

/**
 * Cookie Helper file
 */
class CookieHelper
{
    /**
     * 判断Cookie是否存在
     * @param string $name name
     * @return bool
     */
    public static function has(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * 获取某个Cookie值
     * @param string $name name
     * @return string
     */
    public static function get(string $name): string
    {
        return $_COOKIE[$name] ?? '';
    }

    /**
     * 设置某个 Cookie 的值
     * @param string      $name   cookie name
     * @param string|null $value  cookie value
     * @param int         $expire expired time
     * @param string      $path   path
     * @param string      $domain domain
     * @return bool
     */
    public static function set(string $name, $value, $expire = 0, $path = '', $domain = ''): bool
    {
        if (empty($path)) {
            $path = '/';
        }
        if (empty($domain)) {
            $domain = EnvHelper::domain();
        }

        $expire = !empty($expire) ? time() + $expire : 0;

        return setcookie($name, $value, $expire, $path, $domain);
    }

    /**
     * 删除某个Cookie值
     * @param string $name name
     */
    public static function remove(string $name): void
    {
        self::set($name, '', time() - 3600);
        unset($_COOKIE[$name]);
    }

    /**
     * 清空所有Cookie值
     */
    public static function clear(): void
    {
        unset($_COOKIE);
    }
}