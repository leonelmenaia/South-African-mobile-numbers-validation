<?php

namespace tests\unit\models;


use app\models\PhoneNumber;
use Codeception\Test\Unit;

class PhoneNumberTest extends Unit
{

    public function testValidateCorrectNumber()
    {
        $phone_number = '27831234567';

        $result = PhoneNumber::validateNumber($phone_number);

        $expected = [
            'identifier' => null,
            'file_id' => null,
            'number' => '27831234567',
            'validated' => true,
        ];

        $this->assertEquals($result, $expected);
    }

    public function testValidateCorrectNumberWithNoCountryIndicative()
    {
        $phone_number = '831234567';

        $result = PhoneNumber::validateNumber($phone_number);

        $expected = [
            'identifier' => null,
            'file_id' => null,
            'number' => '27831234567',
            'validated' => true,
        ];

        $this->assertEquals($result, $expected);
    }

    public function testValidateCorrectNumberWithNonDigits()
    {
        $phone_number = '278ahsadhjahjkhjk31  2345  67shjadajksdjkh';

        $result = PhoneNumber::validateNumber($phone_number);

        $expected = [
            'identifier' => null,
            'file_id' => null,
            'number' => '27831234567',
            'validated' => true,
        ];

        $this->assertEquals($result, $expected);
    }

    public function testValidateCorrectNumberWithNonDigitsAndNoCountryIndicative()
    {
        $phone_number = '8ahsadhjahjkhjk31  2345  67shjadajksdjkh';

        $result = PhoneNumber::validateNumber($phone_number);

        $expected = [
            'identifier' => null,
            'file_id' => null,
            'number' => '27831234567',
            'validated' => true,
        ];

        $this->assertEquals($result, $expected);
    }

}