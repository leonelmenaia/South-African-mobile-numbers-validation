<?php

namespace tests\unit\models;


use app\models\PhoneNumber;
use Codeception\Test\Unit;

class PhoneNumberTest extends Unit
{

    public function testValidateCorrectNumber()
    {
        $phone_id = '1';
        $phone_number = '27831234567';

        $result = PhoneNumber::validateNumber($phone_id, $phone_number);

        $this->assertEquals($result, [
            'phone_identifier' => '1',
            'file_id' => null,
            'number' => '27831234567',
            'validated' => true
        ]);
    }

    public function testValidateCorrectNumberWithNoCountryIndicative()
    {
        $phone_id = '1';
        $phone_number = '831234567';

        $result = PhoneNumber::validateNumber($phone_id, $phone_number);

        $this->assertEquals([
            'phone_identifier' => '1',
            'file_id' => null,
            'number' => '27831234567',
            'validated' => true
        ], $result);
    }

    public function testValidateCorrectNumberWithNonDigits()
    {
        $phone_id = '1';
        $phone_number = '278ahsadhjahjkhjk31  2345  67shjadajksdjkh';

        $result = PhoneNumber::validateNumber($phone_id, $phone_number);

        $this->assertEquals([
            'phone_identifier' => '1',
            'file_id' => null,
            'number' => '27831234567',
            'validated' => true
        ], $result);
    }

    public function testValidateCorrectNumberWithNonDigitsAndNoCountryIndicative()
    {
        $phone_identifier = '1';
        $phone_number = '8ahsadhjahjkhjk31  2345  67shjadajksdjkh';

        $result = PhoneNumber::validateNumber($phone_identifier, $phone_number);

        $this->assertEquals([
            'phone_identifier' => '1',
            'file_id' => null,
            'number' => '27831234567',
            'validated' => true
        ], $result);
    }

}