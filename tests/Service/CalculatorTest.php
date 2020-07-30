<?php

namespace App\Tests;


use App\Service\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function testSomething()
    {
        $calculator = new Calculator();
        $result = $calculator->add(1, 9);
        $this->assertSame(10, $result);
    }
}
