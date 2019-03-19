<?php

namespace tests\unit\models;


use app\models\PhoneNumber;
use Codeception\Test\Unit;
use InvalidArgumentException;

class PhoneNumberTest extends Unit
{

    public function testValidateEmptyNumber(){
        $this->expectException(InvalidArgumentException::class);
        $result = PhoneNumber::validateNumber('');
    }

    public function testValidateCorrectNumber()
    {
        $phone_number = '27831234567';

        $result = PhoneNumber::validateNumber($phone_number);

        $expected = new PhoneNumber();
        $expected->id = $result->id;
        $expected->identifier = null;
        $expected->file_id = null;
        $expected->number = '27831234567';
        $expected->validated = true;
        $expected->created_at = $result->created_at;

        $this->assertEquals($expected->getAttributes(), $result->getAttributes());
    }

    public function testValidateCorrectNumberWithNoCountryIndicative()
    {
        $phone_number = '831234567';

        $result = PhoneNumber::validateNumber($phone_number);

        $expected = new PhoneNumber();
        $expected->id = $result->id;
        $expected->identifier = null;
        $expected->file_id = null;
        $expected->number = '27831234567';
        $expected->validated = true;
        $expected->created_at = $result->created_at;

        $this->assertEquals($expected->getAttributes(), $result->getAttributes());
    }

    public function testValidateCorrectNumberWithNonDigits()
    {
        $phone_number = '278ahsadhjahjkhjk31  2345  67shjadajksdjkh';

        $result = PhoneNumber::validateNumber($phone_number);

        $expected = new PhoneNumber();
        $expected->id = $result->id;
        $expected->identifier = null;
        $expected->file_id = null;
        $expected->number = '27831234567';
        $expected->validated = true;
        $expected->created_at = $result->created_at;

        $this->assertEquals($expected->getAttributes(), $result->getAttributes());
    }

    public function testValidateCorrectNumberWithNonDigitsAndNoCountryIndicative()
    {
        $phone_number = '8ahsadhjahjkhjk31  2345  67shjadajksdjkh';

        $result = PhoneNumber::validateNumber($phone_number);

        $expected = new PhoneNumber();
        $expected->id = $result->id;
        $expected->identifier = null;
        $expected->file_id = null;
        $expected->number = '27831234567';
        $expected->validated = true;
        $expected->created_at = $result->created_at;

        $this->assertEquals($expected->getAttributes(), $result->getAttributes());
    }

}