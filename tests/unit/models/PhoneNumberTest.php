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
            'phone_id' => '1',
            'processed_number' => '27831234567',
            'validated_number' => '27831234567',
            'fix_type' => null,
            'error_type' => null,
            'validated' => true
        ]);
    }

    public function testValidateCorrectNumberWithNoCountryIndicative()
    {
        $phone_id = '1';
        $phone_number = '831234567';

        $result = PhoneNumber::validateNumber($phone_id, $phone_number);

        $this->assertEquals($result, [
            'phone_id' => '1',
            'processed_number' => '831234567',
            'validated_number' => '27831234567',
            'fix_type' => PhoneNumber::FIX_ADD_COUNTRY_INDICATIVE,
            'error_type' => null,
            'validated' => true
        ]);
    }

    public function testValidateCorrectNumberWithStrangeCharacters()
    {
        $phone_id = '1';
        $phone_number = '278ahsadhjahjkhjk31  2345  67shjadajksdjkh';

        $result = PhoneNumber::validateNumber($phone_id, $phone_number);

        $this->assertEquals($result, [
            'phone_id' => '1',
            'processed_number' => '278ahsadhjahjkhjk31  2345  67shjadajksdjkh',
            'validated_number' => '27831234567',
            'fix_type' => PhoneNumber::FIX_REMOVE_NON_DIGITS,
            'error_type' => null,
            'validated' => true
        ]);
    }

}