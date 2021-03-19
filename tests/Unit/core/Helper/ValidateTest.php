<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Helper\Validate;

class Test extends TestCase
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // init validate
        $newValidate = new Validate();
        $this->validate = $newValidate;
    }

    public function testRequiredSuccess()
    {
        $testCase = 'abc';
        $actual = $this->validate->required($testCase);
        $this->assertTrue($actual);
    }

    public function testRequiredFailed()
    {
        $string = 'abc';
        $testCase = [
            '', '         '
        ];
        foreach($testCase as $val) {
            $actual = $this->validate->required($val);
            $this->assertFalse($actual);
        }
    }

    public function testDateSuccess()
    {
        $testCase = '2019-02-12';
        $expected = 1;
        $actual = $this->validate->date($testCase);
        $this->assertEquals($actual, $expected);
    }

    public function testDateFailed()
    {
        $testCase = [
            '12-02-2019',
            'ab-bc-cd',
            'abc',
            '',
            '12312312321',
            '02-17-2019',
            '!@#!#!@#!@'
        ];
        $expected = 0;
        foreach($testCase as $val) {
            $actual = $this->validate->date($val);
            $this->assertEquals($actual, $expected);
        } 
    }
}