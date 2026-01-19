<?php

declare(strict_types = 1);

namespace Weiran\Framework\Tests\Classes;

use Throwable;
use Weiran\Framework\Application\TestCase;
use Weiran\Framework\Classes\Number;

class NumberTest extends TestCase
{
    public function testDivide(): void
    {
        $numberB = new Number(5);
        try {
            $result = (new Number(5))->divide($numberB);
            $this->assertEquals('1.00', $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testAbs(): void
    {
        try {
            $result = (new Number(-10.2))->abs();
            $this->assertEquals('10.20', $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testAdd(): void
    {
        $number100 = new Number(100);
        try {
            $result = (new Number(-100))->add($number100);
            $this->assertEquals('0.00', $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testCeil(): void
    {
        try {
            $result = (new Number(1.1))->ceil();
            $this->assertEquals('2', $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testCompareTo(): void
    {
        $number = new Number(10);
        try {
            $result = (new Number(10))->compareTo($number);
            $this->assertEquals('0.00', $result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testConvertToBase(): void
    {
        try {
            $result = (new Number(10))->convertToBase(2);
            $this->assertEquals('1010', $result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testDecrement(): void
    {
        try {
            $result = (new Number(1))->decrement();
            $this->assertEquals('0.00', $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testFloor(): void
    {
        try {
            $result = (new Number(2.9))->floor();
            $this->assertEquals('2', $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testGetScale(): void
    {
        try {
            $result = (new Number(2.9))->getScale();
            $this->assertEquals('2', $result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testGetValue(): void
    {
        try {
            $result = (new Number(2.9))->getValue();
            $this->assertEquals('2.90', $result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testIncrement(): void
    {
        try {
            $result = (new Number(2.9))->increment();
            $this->assertEquals('3.90', $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testIsEqualTo(): void
    {
        $number = new Number(3);
        try {
            $result = (new Number(3))->isEqualTo($number);
            $this->assertTrue($result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testIsGreaterThan(): void
    {
        $number = new Number(3);
        try {
            $result = (new Number(4))->isGreaterThan($number);
            $this->assertTrue($result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testIsGreaterThanOrEqualTo(): void
    {
        $number = new Number(3);
        try {
            $result = (new Number(3))->isGreaterThanOrEqualTo($number);
            $this->assertTrue($result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testIsLessThan(): void
    {
        $number = new Number(3);
        try {
            $result = (new Number(2))->isLessThan($number);
            $this->assertTrue($result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testIsLessThanOrEqualTo(): void
    {
        $number = new Number(3);
        try {
            $result = (new Number(3))->isLessThanOrEqualTo($number);
            $this->assertTrue($result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testIsNegative(): void
    {
        try {
            $result = (new Number(-3))->isNegative();
            $this->assertTrue($result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testIsPositive(): void
    {
        try {
            $result = (new Number(2))->isPositive();
            $this->assertTrue($result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testMod(): void
    {
        $number = new Number(2.9);
        try {
            $result = (new Number(4))->mod($number);
            $this->assertEquals('1', $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testMultiply(): void
    {
        $number = new Number(2.1);
        try {
            $result = (new Number(2))->multiply($number);
            $this->assertEquals('4.20', $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testNegate(): void
    {
        try {
            $result = (new Number(2))->negate();
            $this->assertEquals('-2.00', $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testPow(): void
    {
        try {
            $result = (new Number(5.2))->pow(3);
            $this->assertEquals('140.60', $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testPowMod(): void
    {
        try {
            // 数值需是整数 否则异常
            $result = (new Number(2))->powMod(2, 2);
            $this->assertEquals('0.00', $result->getValue());
        }
        catch (Throwable $e) {
            dump($e);
            $this->fail($e->getMessage());
        }
    }

    public function testRound(): void
    {
        try {
            // 这里输入三位小数 实际执行是2位
            $result = (new Number(5.499))->round(2);
            $this->assertEquals(5.49, $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testSetScale(): void
    {
        try {
            // todo scale全等于null 才设置位数 所以这里设置无效
            $result = (new Number(6))->setScale(1);
            $this->assertEquals('6.00', $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testSetValue(): void
    {
        try {
            $result = (new Number(6))->setValue(3);
            $this->assertEquals('3.00', $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testShiftLeft(): void
    {
        try {
            $result = (new Number(6))->shiftLeft(3);
            $this->assertEquals(48, $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testShiftRight(): void
    {
        try {
            $result = (new Number(8))->shiftRight(3);
            $this->assertEquals(1, $result->getValue());
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testSigNum(): void
    {
        try {
            $result = (new Number(1))->signum();
            $this->assertEquals(1, $result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testSqrt(): void
    {
        try {
            $result = (new Number(8))->sqrt();
            $this->assertEquals('2.82', $result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testSubtract(): void
    {
        $number = new Number(4);
        try {
            $result = (new Number(2))->subtract($number);
            $this->assertEquals('-2.00', $result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testBaseConvert(): void
    {
        try {
            $result = (new Number(10))::baseConvert(10, 2);
            $this->assertEquals(2, $result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testConvertFromBase10t(): void
    {
        try {
            $result = (new Number(10))::convertFromBase10('8', 2);
            $this->assertEquals(1000, $result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testConvertToBase10(): void
    {
        try {
            $result = (new Number(10))::convertToBase10('10', 8);
            $this->assertEquals(8, $result);
        }
        catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }
}
