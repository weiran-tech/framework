<?php

declare(strict_types = 1);

namespace Weiran\Framework\Classes;

/**
 * This file is part of the Moontoast\Math library
 *
 * Copyright 2013 Moontoast, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @copyright 2013 Moontoast, Inc.
 * @license   http://alphabase.moontoast.com/licenses/apache-2.0.txt Apache 2.0
 */

use Illuminate\Support\Str;
use InvalidArgumentException;
use Weiran\Framework\Exceptions\ArithmeticException;

/**
 * Represents a number for use with Binary Calculator computations
 *
 * @link http://www.php.net/bcmath
 */
class Number
{
    /**
     * Number value, as a string
     *
     * @var string $numberValue
     */
    protected string $numberValue;

    /**
     * The scale for the current number
     *
     * @var int $numberScale
     */
    protected int $numberScale = 0;

    /**
     * Constructs a BigNumber object from a string, integer, float, or any
     * object that may be cast to a string, resulting in a numeric string value
     *
     * @param float|string|numeric $number May be of any type that can be cast to a string
     *                      representation of a base 10 number
     * @param int                  $scale (optional) Specifies the default number of digits after the decimal
     *                      place to be used in operations for this BigNumber
     */
    public function __construct(mixed $number, int $scale = 2)
    {
        if ($scale) {
            $this->setScale($scale);
        }

        $this->setValue($number);
    }

    /**
     * 进制转换
     * Converts a number between arbitrary bases (from 2 to 36)
     *
     * @param int|string $number The number to convert
     * @param int        $fromBase (optional) The base $number is in; defaults to 10
     * @param int        $toBase (optional) The base to convert $number to; defaults to 16
     * @return string
     */
    public static function baseConvert(int|string $number, int $fromBase = 10, int $toBase = 16): string
    {
        $number = self::convertToBase10($number, $fromBase);

        return self::convertFromBase10($number, $toBase);
    }

    /**
     * 转换 10 进制到其他进制
     * Converts a base-10 number to an arbitrary base (from 2 to 36)
     *
     * @param int|string $number The number to convert
     * @param int        $toBase The base to convert $number to
     * @return string
     * @throws InvalidArgumentException if $toBase is outside the range 2 to 36
     */
    public static function convertFromBase10(int|string $number, int $toBase): string
    {
        if ($toBase < 2 || $toBase > 36) {
            throw new InvalidArgumentException("Invalid `to base' ({$toBase})");
        }

        $bn               = new self($number);
        $number           = $bn->abs()->getValue();
        $digits           = '0123456789abcdefghijklmnopqrstuvwxyz';
        $outNumber        = '';
        $returnDigitCount = 0;

        while (bcdiv($number, bcpow((string) $toBase, (string) $returnDigitCount)) > ($toBase - 1)) {
            $returnDigitCount++;
        }

        for ($i = $returnDigitCount; $i >= 0; $i--) {
            $pow       = bcpow((string) $toBase, (string) $i);
            $c         = bcdiv($number, $pow);
            $number    = bcsub($number, bcmul($c, $pow));
            $outNumber .= $digits[(int) $c];
        }

        return $outNumber;
    }

    /**
     * 转换成 10 进制
     * Converts a number from an arbitrary base (from 2 to 36) to base 10
     *
     * @param int|string $number The number to convert
     * @param int        $fromBase The base $number is in
     * @return string
     * @throws InvalidArgumentException if $fromBase is outside the range 2 to 36
     */
    public static function convertToBase10(int|string $number, int $fromBase): string
    {
        if ($fromBase < 2 || $fromBase > 36) {
            throw new InvalidArgumentException("Invalid `from base' ({$fromBase})");
        }

        $number    = (string) $number;
        $len       = strlen($number);
        $base10Num = '0';

        for ($i = $len; $i > 0; $i--) {
            $c = ord($number[$len - $i]);

            if ($c >= ord('0') && $c <= ord('9')) {
                $c -= ord('0');
            }
            elseif ($c >= ord('A') && $c <= ord('Z')) {
                $c -= ord('A') - 10;
            }
            elseif ($c >= ord('a') && $c <= ord('z')) {
                $c -= ord('a') - 10;
            }
            else {
                continue;
            }

            if ($c >= $fromBase) {
                continue;
            }

            $base10Num = bcadd(bcmul($base10Num, (string) $fromBase), (string) $c);
        }

        return $base10Num;
    }

    /**
     * 设置默认小数位数
     * Changes the default scale used by all Binary Calculator functions
     *
     * @param int $scale scale
     * @return void
     */
    public static function setDefaultScale(int $scale): void
    {
        ini_set('bcmath.scale', (string) $scale);
    }

    /**
     * Returns the string value of this BigNumber
     *
     * @return string String representation of the number in base 10
     */
    public function __toString()
    {
        return $this->getValue();
    }

    /**
     * 将当前数字设置为自身的绝对值
     * Sets the current number to the absolute value of itself
     */
    public function abs(): static
    {
        // Use substr() to find the negative sign at the beginning of the
        // number, rather than using signum() to determine the sign.
        if (str_starts_with($this->numberValue, '-')) {
            $this->numberValue = substr($this->numberValue, 1);
        }

        return $this;
    }

    /**
     * 加法运算
     * Adds the given number to the current number
     *
     * @param float|int|string|static $number May be of any type that can be cast to a string
     *                      representation of a base 10 number
     * @return $this
     * @link http://www.php.net/bcadd
     */
    public function add(mixed $number): static
    {
        $this->numberValue = bcadd(
            $this->numberValue,
            $this->filterNumber($number),
            $this->getScale()
        );

        return $this;
    }

    /**
     * 通过重新排列当前数字来查找下一个最高整型值
     * Finds the next highest integer value by rounding up the current number
     * if necessary
     *
     * @link http://www.php.net/ceil
     */
    public function ceil(): static
    {
        $number = $this->getValue();

        if ($this->isPositive()) {
            // 14 is the magic precision number
            $number = bcadd($number, '0', 14);
            if (!str_ends_with($number, '.00000000000000')) {
                $number = bcadd($number, '1', 0);
            }
        }

        $this->numberValue = bcadd($number, '0', 0);

        return $this;
    }

    /**
     * 比较数据
     * Compares the current number with the given number
     *
     * Returns 0 if the two operands are equal, 1 if the current number is
     * larger than the given number, -1 otherwise.
     *
     * @param float|int|string|static $number May be of any type that can be cast to a string
     *                      representation of a base 10 number
     * @return int
     * @link http://www.php.net/bccomp
     */
    public function compareTo(float|int|string|Number $number): int
    {
        return bccomp(
            $this->numberValue,
            $this->filterNumber($number),
            $this->getScale()
        );
    }

    /**
     * 进制转换, 转换成任意进制
     * Returns the current value converted to an arbitrary base
     *
     * @param int $base The base to convert the current number to
     * @return string String representation of the number in the given base
     */
    public function convertToBase(int $base): string
    {
        return self::convertFromBase10($this->getValue(), $base);
    }

    /**
     * 将当前数字的值减一
     * Decreases the value of the current number by one
     */
    public function decrement(): static
    {
        return $this->subtract(1);
    }

    /**
     * 除法运算
     * Divides the current number by the given number
     *
     * @param float|int|string|static $number May be of any type that can be cast to a string
     *                      representation of a base 10 number
     * @return $this
     * @throws ArithmeticException if $number is zero
     * @link http://www.php.net/bcdiv
     */
    public function divide(mixed $number): static
    {
        $number = $this->filterNumber($number);

        if ($number === '0') {
            throw new ArithmeticException('Division by zero');
        }

        $this->numberValue = bcdiv(
            $this->numberValue,
            $number,
            $this->getScale()
        );

        return $this;
    }

    /**
     * 通过舍入当前数字找到下一个最低整数值
     * Finds the next lowest integer value by rounding down the current number
     * if necessary
     *
     * @link http://www.php.net/floor
     */
    public function floor(): static
    {
        $number = $this->getValue();

        if ($this->isNegative()) {
            // 14 is the magic precision number
            $number = bcadd($number, '0', 14);
            if (!str_ends_with($number, '.00000000000000')) {
                $number = bcsub($number, '1', 0);
            }
        }

        $this->numberValue = bcadd($number, '0', 0);

        return $this;
    }

    /**
     * 返回小数位数
     * Returns the scale used for this BigNumber
     *
     * If no scale was set, this will default to the value of `bcmath.scale`
     * in php.ini.
     *
     * @return int
     */
    public function getScale(): int
    {
        return $this->numberScale;
    }

    /**
     * 获取字串值
     * Returns the current raw value of this BigNumber
     *
     * @return string String representation of the number in base 10
     */
    public function getValue(): string
    {
        return $this->numberValue;
    }

    /**
     * 增加
     * Increases the value of the current number by one
     *
     */
    public function increment(): static
    {
        return $this->add(1);
    }

    /**
     * 检测是否相等
     * Returns true if the current number equals the given number
     *
     * @param float|int|string|static $number May be of any type that can be cast to a string
     *                      representation of a base 10 number
     * @return bool
     */
    public function isEqualTo(float|int|string|Number $number): bool
    {
        return $this->compareTo($number) === 0;
    }

    /**
     * 检测是否大于
     * Returns true if the current number is greater than the given number
     *
     * @param string|int|float|static $number May be of any type that can be cast to a string
     *                      representation of a base 10 number
     * @return bool
     */
    public function isGreaterThan(string|int|float|Number $number): bool
    {
        return $this->compareTo($number) === 1;
    }

    /**
     * 检测大于等于
     * Returns true if the current number is greater than or equal to the given number
     *
     * @param float|int|string|static $number May be of any type that can be cast to a string
     *                      representation of a base 10 number
     * @return bool
     */
    public function isGreaterThanOrEqualTo(float|int|string|Number $number): bool
    {
        return $this->compareTo($number) >= 0;
    }

    /**
     * 检测小与
     * Returns true if the current number is less than the given number
     *
     * @param float|int|string|static $number May be of any type that can be cast to a string
     *                      representation of a base 10 number
     * @return bool
     */
    public function isLessThan(mixed $number): bool
    {
        return $this->compareTo($number) === -1;
    }

    /**
     * 检测小与等于
     * Returns true if the current number is less than or equal to the given number
     *
     * @param float|int|string|static $number May be of any type that can be cast to a string
     *                      representation of a base 10 number
     * @return bool
     */
    public function isLessThanOrEqualTo(mixed $number): bool
    {
        return $this->compareTo($number) <= 0;
    }

    /**
     * 是否是负数
     * Returns true if the current number is a negative number
     *
     * @return bool
     */
    public function isNegative(): bool
    {
        return $this->signum() === -1;
    }

    /**
     * 是否是正数
     * Returns true if the current number is a positive number
     *
     * @return bool
     */
    public function isPositive(): bool
    {
        return $this->signum() === 1;
    }

    /**
     * 取余数
     * Finds the modulus of the current number divided by the given number
     *
     * @param float|int|string|static $number May be of any type that can be cast to a string
     *                      representation of a base 10 number
     * @return $this
     * @throws ArithmeticException if $number is zero
     * @link http://www.php.net/bcmod
     */
    public function mod(mixed $number): static
    {
        $number = $this->filterNumber($number);

        if ($number === '0') {
            throw new ArithmeticException('Division by zero');
        }

        $this->numberValue = bcmod(
            $this->numberValue,
            $number
        );

        return $this;
    }

    /**
     * 乘法
     * Multiplies the current number by the given number
     *
     * @param float|int|string|static $number May be of any type that can be cast to a string
     *                      representation of a base 10 number
     * @return $this
     * @link http://www.php.net/bcmul
     */
    public function multiply(mixed $number): static
    {
        $this->numberValue = bcmul(
            $this->numberValue,
            $this->filterNumber($number),
            $this->getScale()
        );

        return $this;
    }

    /**
     * 返回负数
     * Sets the current number to the negative value of itself
     */
    public function negate(): static
    {
        return $this->multiply(-1);
    }

    /**
     * 幂运算
     * Raises current number to the given number
     *
     * @param float|int|string|static $number May be of any type that can be cast to a string
     *                      representation of a base 10 number
     * @return $this
     * @link http://www.php.net/bcpow
     */
    public function pow(mixed $number): static
    {
        $this->numberValue = bcpow(
            $this->numberValue,
            $this->filterNumber($number),
            $this->getScale()
        );

        return $this;
    }

    /**
     * 指定次数的幂运算
     * Raises the current number to the $pow, then divides by the $mod
     * to find the modulus
     *
     * This is functionally equivalent to the following code:
     *
     * <code>
     *     $n = new BigNumber(1234);
     *     $n->mod($n->pow(32), 2);
     * </code>
     *
     * However, it uses bcpowmod(), so it is faster and can accept larger
     * parameters.
     *
     * @param float|int|string $pow May be of any type that can be cast to a string
     *                   representation of a base 10 number
     * @param float|int|string $mod May be of any type that can be cast to a string
     *                   representation of a base 10 number
     * @return $this
     * @throws ArithmeticException if $number is zero
     * @link http://www.php.net/bcpowmod
     */
    public function powMod(float|int|string $pow, float|int|string $mod): static
    {
        $mod = $this->filterNumber($mod);

        if ($mod === '0') {
            throw new ArithmeticException('Division by zero');
        }

        if (!$this->isEqualTo((int) $this->numberValue)) {
            throw new ArithmeticException('The value is expected to be an integer');
        }

        // fix 2.00 error : bcpowmod(): non-zero scale in base
        $this->numberValue = bcpowmod(
            Str::before($this->numberValue, '.'),
            $this->filterNumber($pow),
            $mod,
            $this->getScale()
        );

        return $this;
    }

    /**
     * 四舍五入
     * Rounds the current number to the nearest integer
     *
     * @param int $precision precision
     * @return Number
     */
    public function round(int $precision = 0): self
    {
        $original = $this->getValue();
        $floored  = $this->floor()->getValue();
        $diff     = (float) bcsub($original, $floored, 20);
        if ($this->isNegative()) {
            $roundedDiff = round($diff, $precision, PHP_ROUND_HALF_DOWN);
        }
        else {
            $roundedDiff = round($diff, $precision);
        }

        $this->numberValue = bcadd(
            $floored,
            (string) $roundedDiff,
            $precision
        );

        return $this;
    }

    /**
     * 设置小数位数
     * Sets the scale of this BigNumber
     *
     * @param int $scale Specifies the default number of digits after the decimal
     *                   place to be used in operations for this BigNumber
     * @return $this
     */
    public function setScale(int $scale): static
    {
        $this->numberScale = $scale;

        return $this;
    }

    /**
     * 设置为一个新值
     * Sets the value of this BigNumber to a new value
     *
     * @param float|int|string|static $number May be of any type that can be cast to a string
     *                      representation of a base 10 number
     * @return $this
     */
    public function setValue(mixed $number): static
    {
        // Set the scale for the number to the scale value passed in
        $number = bcadd(
            $this->filterNumber($number),
            '0',
            $this->getScale()
        );

        $this->numberValue = $number;

        return $this;
    }

    /**
     * 位向左移动
     * Shifts the current number $bits to the left
     *
     * @param int $bits bits
     * @return $this
     */
    public function shiftLeft(int $bits): static
    {
        $this->numberValue = bcmul(
            $this->numberValue,
            bcpow('2', (string) $bits)
        );

        return $this;
    }

    /**
     * 位向右移动
     * Shifts the current number $bits to the right
     *
     * @param int $bits bits
     * @return $this
     */
    public function shiftRight(int $bits): static
    {
        $this->numberValue = bcdiv(
            $this->numberValue,
            bcpow('2', (string) $bits)
        );

        return $this;
    }

    /**
     * 前数字的符号
     * Returns the sign (signum) of the current number
     *
     * @return int -1, 0 or 1 as the value of this BigNumber is negative, zero or positive
     */
    public function signum(): int
    {
        if ($this->isGreaterThan(0)) {
            return 1;
        }
        if ($this->isLessThan(0)) {
            return -1;
        }

        return 0;
    }

    /**
     * 求平方根
     * Finds the square root of the current number
     *
     * @link http://www.php.net/bcsqrt
     */
    public function sqrt(): static
    {
        $this->numberValue = bcsqrt(
            $this->numberValue,
            $this->getScale()
        );

        return $this;
    }

    /**
     * 减法
     * Subtracts the given number from the current number
     *
     * @param float|int|string|static $number May be of any type that can be cast to a string
     *                      representation of a base 10 number
     * @return $this
     * @link http://www.php.net/bcsub
     */
    public function subtract(mixed $number): static
    {
        $this->numberValue = bcsub(
            $this->numberValue,
            $this->filterNumber($number),
            $this->getScale()
        );

        return $this;
    }

    /**
     * 转换成 String 类型
     * Filters a number, converting it to a string value
     *
     * @param float|int|string $number number
     * @return string
     */
    protected function filterNumber(mixed $number): string
    {
        return filter_var(
            $number,
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        );
    }
}