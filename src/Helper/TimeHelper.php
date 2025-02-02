<?php

declare(strict_types = 1);

namespace Weiran\Framework\Helper;

use Carbon\Carbon;
use DateTime as PhpDateTime;
use Exception;

/**
 * 时间相关操作
 */
class TimeHelper
{
    /**
     * 标准的UNIX时间戳
     * 获得当前格林威治时间的时间戳
     * @return int
     */
    public static function gmTime(): int
    {
        return time() - date('Z');
    }

    /**
     * 检测是否是标准时间
     * @param string $date date
     * @param string $sep  sep
     * @return bool
     */
    public static function isDate(string $date, string $sep = '-'): bool
    {
        // 时间为空
        if (empty($date)) {
            return false;
        }
        // 长度大于 10
        if (strlen($date) > 10) {
            return false;
        }
        [$year, $month, $day] = explode($sep, $date);

        $year  = (int) $year;
        $month = (int) $month;
        $day   = (int) $day;

        return checkdate($month, $day, $year);
    }

    /**
     * 是否是日期范围
     * @param $range
     * @return bool
     */
    public static function isDateRange($range): bool
    {
        if (preg_match('/^(\d{4}-\d{2}-\d{2}) - (\d{4}-\d{2}-\d{2})$/', trim($range), $matches)) {
            return Carbon::parse($matches[2])->gte(Carbon::parse($matches[1]));
        }
        return false;
    }

    /**
     * 格式化时间
     * @param int|string $time   time
     * @param string     $format format
     * @return bool|string
     */
    public static function datetime($time = 0, string $format = '3-3')
    {
        if (!empty($time)) {
            if (!is_numeric($time)) {
                // strotime强制将代入进来的时间格式都转成Unix时间戳
                $time = strtotime((string) $time);
            }
        }
        else {
            $time = EnvHelper::time();
        }
        switch ($format) {
            case '3-2':
                $df = 'Y-m-d H:i';
                break;
            case '2-2':
                $df = 'm-d H:i';
                break;
            case '2-3':
                $df = 'm-d H:i:s';
                break;
            case '3-0':
                $df = 'Y-m-d';
                break;
            case '3-3':
            default:
                $df = 'Y-m-d H:i:s';
                break;
        }

        return date($df, $time);
    }

    /**
     * 自定义函数：time2string($second) 输入秒数换算成多少天/多少小时/多少分/多少秒的字符串
     * @param mixed $second second
     * @param bool  $more   是否返回分/秒
     * @return string
     */
    public static function time2string($second, bool $more = false): string
    {
        $day    = floor($second / (3600 * 24));
        $second %= (3600 * 24);//除去整天之后剩余的时间
        $hour   = floor($second / 3600);
        $second %= 3600;//除去整小时之后剩余的时间
        $minute = floor($second / 60);
        $second %= 60;//除去整分钟之后剩余的时间
        //返回字符串
        if ($more) {
            return $day . '天' . $hour . '小时' . $minute . '分' . $second . '秒';
        }
        return $day . '天' . $hour . '小时';
    }

    /**
     * 转换字符串形式的时间表达式为GMT时间戳
     * @param string $str str
     * @return bool|int|string
     */
    public static function gmStr2Time(string $str)
    {
        $time = strtotime($str);

        if ($time > 0) {
            $time -= date('Z');
        }

        return $time;
    }

    /**
     * 获得服务器的时区
     * @return float|string
     */
    public static function serverTimezone()
    {
        if (function_exists('date_default_timezone_get')) {
            return date_default_timezone_get();
        }

        return date('Z') / 3600;
    }

    /**
     * 一天的开始, 未传值代表今天
     * @param string $date date
     * @return string
     */
    public static function dayStart(string $date = ''): string
    {
        $date = trim($date);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return Carbon::createFromFormat('Y-m-d', $date)->startOfDay()->toDateTimeString();
        }
        return Carbon::now()->startOfDay()->toDateTimeString();
    }

    /**
     * 一天的结束, 未传值代表今天
     * @param string $date date
     * @return string
     */
    public static function dayEnd(string $date = ''): string
    {
        $date = trim($date);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return Carbon::createFromFormat('Y-m-d', $date)->endOfDay()->toDateTimeString();
        }
        return Carbon::now()->endOfDay()->toDateTimeString();
    }

    /**
     * 格式化日期
     * @param int|string $time   time
     * @param string     $format format
     * @return string
     */
    public static function format($time = 0, string $format = 'Y-m-d H:i'): string
    {
        if ($time === 0) {
            return Carbon::now()->format($format);
        }
        if (is_numeric($time)) {
            return Carbon::createFromTimestamp($time)->format($format);
        }
        return Carbon::parse($time)->format($format);
    }

    /**
     * 空日期检测
     * @param string $date date
     * @return bool
     */
    public static function isEmpty(string $date): bool
    {
        return empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00';
    }

    /**
     * datetime to timestamp
     * @param string $datetime datetime
     * @return int
     */
    public static function datetimeToTimestamp(string $datetime): int
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $datetime)->timestamp;
    }

    /**
     * 时间戳转换成 datetime 类型
     * @param string $timestamp timestamp
     * @return bool|string
     */
    public static function timestampToDatetime(string $timestamp): string
    {
        return self::format($timestamp, 'Y-m-d H:i:s');
    }

    /**
     * 精确时间间隔函数
     * $time 发布时间 如 1356973323
     * $str 输出格式 如 Y-m-d H:i:s
     * 半年的秒数为15552000，1年为31104000，此处用半年的时间
     * @param mixed $time time
     * @return false|string
     */
    public static function tranTime($time)
    {
        $rtime = date('m-d H:i', $time);
        $htime = date('H:i', $time);

        $time = time() - $time;

        if ($time < 60) {
            $str = '刚刚';
        }
        elseif ($time < 60 * 60) {
            $min = floor($time / 60);
            $str = $min . '分钟前';
        }
        elseif ($time < 60 * 60 * 24) {
            $h   = floor($time / (60 * 60));
            $str = $h . '小时前 ' . $htime;
        }
        elseif ($time < 60 * 60 * 24 * 3) {
            $d = floor($time / (60 * 60 * 24));
            if ((int) $d === 1) {
                $str = '昨天 ' . $rtime;
            }
            else {
                $str = '前天 ' . $rtime;
            }
        }
        else {
            $str = $rtime;
        }

        return $str;
    }

    /**
     * Returns a human readable time difference from the value to the
     * current time. Eg: **10 minutes ago**
     * @param string|int $datetime datetime
     * @return string
     */
    public static function timeSince($datetime)
    {
        return self::makeCarbon($datetime)->diffForHumans();
    }

    /**
     * Returns 24-hour time and the day using the grammatical tense
     * of the current time. Eg: Today at 12:49, Yesterday at 4:00
     * or 18 Sep 2015 at 14:33.
     * @param mixed $datetime datetime
     * @return string
     */
    public static function timeTense($datetime)
    {
        $datetime = self::makeCarbon($datetime);
        $time     = $datetime->format('H:i');
        $date     = $datetime->format('j M Y');

        if ($datetime->isToday()) {
            $date = 'Today';
        }
        elseif ($datetime->isYesterday()) {
            $date = 'Yesterday';
        }
        elseif ($datetime->isTomorrow()) {
            $date = 'Tomorrow';
        }

        return $date . ' at ' . $time;
    }

    /**
     * Converts mixed inputs to a Carbon object.
     * @param Carbon|PhpDateTime|string|int $value value
     * @return Carbon|null
     */
    public static function makeCarbon($value): ?Carbon
    {
        $item = $value;
        if ($value instanceof PhpDateTime) {
            $item = Carbon::instance($value);
        }

        if (is_numeric($value)) {
            $item = Carbon::createFromTimestamp($value);
        }

        if (is_string($value)) {
            if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value)) {
                $item = Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
            }
        }

        try {
            return Carbon::parse($item);
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * Converts a PHP date format to "Moment.js" format.
     * @param string $format format
     * @return string
     */
    public static function momentFormat(string $format)
    {
        $replacements = [
            'd' => 'DD',
            'D' => 'ddd',
            'j' => 'D',
            'l' => 'dddd',
            'N' => 'E',
            'S' => 'o',
            'w' => 'e',
            'z' => 'DDD',
            'W' => 'W',
            'F' => 'MMMM',
            'm' => 'MM',
            'M' => 'MMM',
            'n' => 'M',
            't' => '', // no equivalent
            'L' => '', // no equivalent
            'o' => 'YYYY',
            'Y' => 'YYYY',
            'y' => 'YY',
            'a' => 'a',
            'A' => 'A',
            'B' => '', // no equivalent
            'g' => 'h',
            'G' => 'H',
            'h' => 'hh',
            'H' => 'HH',
            'i' => 'mm',
            's' => 'ss',
            'u' => 'SSS',
            'e' => 'zz', // deprecated since version 1.6.0 of moment.js
            'I' => '',   // no equivalent
            'O' => '',   // no equivalent
            'P' => '',   // no equivalent
            'T' => '',   // no equivalent
            'Z' => '',   // no equivalent
            'c' => '',   // no equivalent
            'r' => '',   // no equivalent
            'U' => 'X',
        ];

        foreach ($replacements as $from => $to) {
            $replacements['\\' . $from] = '[' . $from . ']';
        }

        return strtr($format, $replacements);
    }

    /**
     * 返回微秒数值
     * @return string
     */
    public static function micro()
    {
        [$micro] = explode(' ', microtime());

        return sprintf("%'.03d", $micro * 1000);
    }

    /**
     * 通过 Carbon 对象来获取格式化的时间
     * @param Carbon|null|string $carbon carbon
     * @param string             $format format
     * @return string
     */
    public static function fetchFormat($carbon, $format = 'Y-m-d H:i:s'): string
    {
        if ($carbon instanceof Carbon) {
            return $carbon->format($format);
        }

        if (is_string($carbon)) {
            return $carbon;
        }

        return '';
    }

    /**
     * 根据日期返回每年的周数
     * 例如 2020-01-01 会返回 [2019,52] 周, 用于统计部分
     * @param string $date  日期
     * @param int    $start 默认以那一天作为第一天的开始
     * @return array
     */
    public static function week(string $date, int $start = Carbon::MONDAY): array
    {
        /** @var Carbon $carbon */
        $carbon = Carbon::createFromFormat('Y-m-d', $date)->startOfWeek($start);

        $startWeek = (clone $carbon)->subDays($carbon->dayOfWeek - 1);
        $endWeek   = (clone $carbon)->addDays((7 - $carbon->dayOfWeek) % 7);

        if ($endWeek->format('W') === '01') {
            return [$endWeek->year, $endWeek->format('W')];
        }
        else {
            return [$startWeek->year, $startWeek->format('W')];
        }
    }
}